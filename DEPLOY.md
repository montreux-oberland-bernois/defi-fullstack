# Train Routing API - Guide de déploiement

## Prérequis

- Docker Engine 25+
- Docker Compose v2.x
- Git

## Déploiement rapide

### 1. Cloner le projet

```bash
git clone <repository-url>
cd defi-fullstack
```

### 2. Configurer l'environnement

```bash
# Copier les fichiers d'environnement
cp .env.example .env
cp backend/.env.example backend/.env

# Générer les clés (optionnel, sera fait automatiquement)
# Modifier les valeurs dans .env si nécessaire
```

### 3. Lancer l'application

```bash
docker compose up -d
```

### 4. Initialiser la base de données

```bash
# Attendre que les conteneurs soient prêts (~30s)
docker compose exec backend php artisan migrate --seed
docker compose exec backend php artisan jwt:secret --force
```

### 5. Accéder à l'application

- **Frontend**: http://localhost:8080
- **API**: http://localhost:8080/api/v1
- **Health Check**: http://localhost:8080/api/health

## Utilisateurs de test

Après le seeding, vous pouvez vous connecter avec:

- **Email**: `test@example.com`
- **Mot de passe**: `password`

Ou créer un nouveau compte via l'interface.

## Commandes utiles

### Logs

```bash
# Tous les services
docker compose logs -f

# Service spécifique
docker compose logs -f backend
docker compose logs -f frontend
```

### Tests

```bash
# Backend (PHPUnit)
docker compose exec backend php artisan test
docker compose exec backend composer test:coverage

# Frontend (Vitest)
docker compose exec frontend pnpm test
docker compose exec frontend pnpm test:coverage
```

### Linting

```bash
# Backend
docker compose exec backend composer lint
docker compose exec backend composer phpstan

# Frontend
docker compose exec frontend pnpm lint
docker compose exec frontend pnpm type-check
```

### Artisan

```bash
docker compose exec backend php artisan <command>
```

### pnpm

```bash
docker compose exec frontend pnpm <command>
```

## Architecture

```
defi-fullstack/
├── backend/                 # Laravel API (PHP 8.4)
│   ├── app/
│   │   ├── Http/Controllers/Api/V1/
│   │   ├── Models/
│   │   └── Services/       # Dijkstra, RouteCalculator
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── tests/
├── frontend/               # Vue.js 3 + Vuetify 3 (TypeScript)
│   ├── src/
│   │   ├── components/
│   │   ├── views/
│   │   ├── stores/        # Pinia stores
│   │   ├── services/      # API services
│   │   └── types/
│   └── tests/
├── docker/
│   └── nginx/             # Configuration Nginx
├── docker-compose.yml
└── DEPLOY.md
```

## Endpoints API

### Authentification

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/auth/login` | Connexion |
| POST | `/api/v1/auth/register` | Inscription |
| POST | `/api/v1/auth/logout` | Déconnexion |
| POST | `/api/v1/auth/refresh` | Rafraîchir le token |
| GET | `/api/v1/auth/me` | Utilisateur courant |

### Routes (protégées)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/v1/routes` | Calculer un trajet |
| GET | `/api/v1/routes` | Liste des trajets |
| GET | `/api/v1/routes/{id}` | Détail d'un trajet |

### Statistiques (protégées)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/stats/distances` | Distances par code analytique |

### Stations (protégées)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/v1/stations` | Liste des stations |
| GET | `/api/v1/stations/{id}` | Détail d'une station |

## Arrêt

```bash
docker compose down

# Avec suppression des volumes (données)
docker compose down -v
```
