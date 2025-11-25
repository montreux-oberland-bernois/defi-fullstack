# Train Routing - Full Stack Application

![CI](https://github.com/Uldrys/defi-fullstack/actions/workflows/ci.yml/badge.svg)

Application de calcul d'itinéraires ferroviaires avec statistiques par code analytique.

## Tech Stack

| Layer | Technologies |
|-------|-------------|
| **Frontend** | Vue.js 3, Vuetify 3, TypeScript, Pinia, vue-i18n, Leaflet |
| **Backend** | Laravel 11, PHP 8.4, JWT Auth |
| **Database** | PostgreSQL 16 |
| **Infra** | Docker, Docker Compose, Nginx |

## Features

- **Calcul d'itinéraires** - Algorithme de Dijkstra pour trouver le chemin le plus court
- **Codes analytiques** - FRET, PASS, MAINT, TEST avec validation
- **Carte interactive** - Visualisation du trajet avec Leaflet
- **Dashboard KPIs** - Métriques mensuelles et totales
- **Statistiques** - Graphiques par code analytique avec filtres
- **Filtres avancés** - Par code analytique et plage de dates
- **i18n** - Support FR/EN (frontend et backend)
- **Auth JWT** - Inscription, connexion, refresh token

## Quick Start

```bash
# Cloner et lancer
git clone <repo-url> && cd defi-fullstack
docker compose up -d

# Accès
# Frontend: http://localhost:5173
# API: http://localhost:8000/api/v1
```

## Architecture

```
├── backend/                 # Laravel 11 API
│   ├── app/
│   │   ├── Http/Controllers/Api/V1/
│   │   ├── Services/        # RouteCalculator, Dijkstra, StationGraph
│   │   └── Models/
│   ├── lang/               # Traductions FR/EN
│   └── tests/
├── frontend/               # Vue.js 3 SPA
│   ├── src/
│   │   ├── views/          # Home, Routes, Stats, NewRoute
│   │   ├── components/     # RouteMap
│   │   ├── services/       # API clients
│   │   ├── stores/         # Pinia stores
│   │   └── locales/        # i18n FR/EN
│   └── tests/
└── docker-compose.yml
```

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/auth/register` | Inscription |
| `POST` | `/auth/login` | Connexion |
| `GET` | `/auth/me` | Profil utilisateur |
| `POST` | `/routes` | Calculer un itinéraire |
| `GET` | `/routes` | Liste des trajets (paginée, filtrable) |
| `GET` | `/routes/{id}` | Détail d'un trajet |
| `GET` | `/stations` | Liste des stations |
| `GET` | `/stats/distances` | Stats par code analytique |
| `GET` | `/stats/dashboard` | KPIs dashboard |

## Calcul d'itinéraire

L'algorithme de Dijkstra calcule le chemin le plus court entre deux stations:

```
POST /api/v1/routes
{
  "fromStationId": "MOB",
  "toStationId": "MTB",
  "analyticCode": "PASS"
}
```

Réponse:
```json
{
  "id": "uuid",
  "fromStationId": "MOB",
  "toStationId": "MTB",
  "analyticCode": "PASS",
  "distanceKm": 12.5,
  "path": ["MOB", "ZWS", "SAN", "MTB"]
}
```

## Tests

```bash
# Backend
cd backend && php artisan test

# Frontend
cd frontend && pnpm test
```

## Environment Variables

### Backend (.env)
```env
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_DATABASE=train_routing
JWT_SECRET=<generated>
```

### Frontend (.env)
```env
VITE_API_URL=http://localhost:8000/api/v1
```

## Choix techniques

| Choix | Justification |
|-------|---------------|
| **Laravel** | Écosystème robuste, Eloquent ORM, validation intégrée |
| **Dijkstra** | Algorithme optimal pour graphes pondérés positifs |
| **JWT** | Stateless, scalable, standard industriel |
| **Pinia** | State management moderne pour Vue 3 |
| **Leaflet** | Léger, open-source, personnalisable |
| **vue-i18n** | Standard pour l'internationalisation Vue |

## Structure des données

Les fichiers `stations.json` et `distances.json` définissent le réseau ferroviaire:
- **38 stations** dans la région MOB (Montreux-Oberland Bernois)
- **Graphe connexe** permettant le calcul d'itinéraires

## Sécurité

- JWT avec expiration configurable
- Validation des entrées (FormRequest)
- CORS configuré
- Headers sécurisés
- Pas de secrets dans le code

---

Développé dans le cadre du défi Full Stack MOB.
