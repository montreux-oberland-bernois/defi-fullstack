# 🚆 Défi Full stack - Routage de Train & Statistiques

Bienvenue dans notre défi technique !  
Avant même l’envoi de ton CV, nous te proposons de passer par cette étape pratique. Pourquoi ? Parce que nous croyons que **le code parle plus fort que les mots**.

Ce défi est ton ticket d’entrée : il te permet de nous montrer l’étendue de tes capacités à **collaborer, analyser et livrer du code de qualité**. Tu le réalises chez toi, dans ton environnement, avec tes outils, mais l’objectif est de voir comment tu t’adaptes à notre culture technique et à nos pratiques **DevSecOps**.

---

## 🤝 Esprit du défi
Ce défi est autant une **démonstration de tes compétences** qu’une **simulation de collaboration** dans notre environnement.  
Nous ne cherchons pas la perfection : nous voulons voir ta capacité à t’approprier un contexte technique exigeant, à produire du code de qualité et à réfléchir comme un membre de l’équipe.

Tu es invité à démontrer ta capacité à :
- Travailler avec des outils similaires aux nôtres (**Docker, Composer, GitLab, PHPUnit**, etc.)
- Appliquer des pratiques comme **l’analyse statique**, le **TDD**, le **DDD** et l’**intégration/déploiement continus**
- Produire un code **propre, maintenable et réfléchi**, comme si tu faisais déjà partie de l’équipe


> 💡 Conseil : documente tes choix, structure ton code et montre-nous comment tu raisonnes. C’est tout aussi important que le résultat final.

---

## 🧩 Notre environnement
Nous produisons des applications web modernes, sécurisées et performantes, en utilisant principalement :
- **Backend** : PHP 8 (Symfony 7 et CakePHP 5)
- **Frontend** : Vue.js 3 + Vuetify 3 + TypeScript
- **Tests** : PHPUnit, Vitest, Jest
- **Linter** : PHPCS, ESLint, Prettier
- **UI/UX** : Storybook
- **Base de données** : PostgreSQL ou MariaDB
- **Infrastructure** : Docker, Docker Compose, TeamCity (CI/CD), Gitlab (code versioning)
- **Méthodologies** : TDD, DDD, XP

---

# 🧾 Instructions pour réaliser le défi
Tu dois réaliser une solution à minimum deux niveaux. Un backend PHP 8 exposant une API REST conforme à la spécification OpenAPI fournie ainsi qu'un frontend TypeScript consommant cette API.

## Le contexte
Dans le métier de la circulation ferroviaire, les trajets de chaque train sont répertoriés dans un système de gestion du trafic. Un train circule sur une ligne, ces lignes sont parfois connectées, permettant à un train de circuler sur plusieurs lignes.
Chaque trajet est associé à un code analytique, qui permet de catégoriser le type de trajet (ex : fret, passager, maintenance, etc.).
Les données de statistiques générées sont ensuite utilisées pour diverses analyses.

## Le besoin métier
La solution doit permettre à l'utilisateur de calculer une distance entre deux stations de train. La liste des stations ainsi que les distances entre les stations sont fournies dans les fichiers `stations.json` et `distances.json`.

Tu peux choisir de persister les saisies des utilisateurs, cela t'aidera à compléter les points Bonus (voir ci-dessous), mais ce n'est pas obligatoire.

Il se peut que tu aies des questions ou des incertitudes sur la compréhension du besoin, dans ce cas, tu es libre de faire des hypothèses raisonnables et de les documenter.

> 💡 Conseil : Applique le principe fondamental de qualité du craftsmanship.

## Livrables attendus
Lorsque tu as terminé, envoie à n.girardet[at]mob[point]ch, ton dossier de candidature complet ainsi qu'un lien vers le projet contenant :
- Le projet prêt à déployer, au format que tu préfères : un repo GitHub avec un docker-compose, une image publiée dans un registre, un fichier zip dans une release GitHub...
- Les instructions de déploiement claires
- L'accès au repository du code source, y compris l'historique des commits

## Et après ?
Nous procéderons à une revue de ton code et nous te contacterons pour t'informer de la suite.

> 🚫 N'envoie pas de fichiers volumineux (ex : 30 Mo) par e-mail

---

## 🎯 Objectifs

- Implémenter un **backend PHP 8** exposant une API conforme à la spécification **OpenAPI** fournie.
- Développer un **frontend TypeScript** consommant cette API.
- Fournir une **couverture de code** mesurable (tests unitaires et d’intégration).
- Déployer l’application avec un minimum d’opérations via **Docker** ou **Docker Compose**.
- Mettre en place un **pipeline CI/CD complet** (build, tests, coverage, lint, déploiement).
- Utiliser un **versioning de code** clair et structuré.
- Garantir des **communications sécurisées** (HTTPS, gestion des secrets, authentification).

---

## 🏗️ Architecture attendue

- **Backend**  
  - PHP 8.4 obligatoire.
  - Utilisation d'un Framework (Symfony, CakePHP, Slim, Laravel,...) facultatif.  
  - Implémentation stricte de l’API OpenAPI fournie.  
  - Tests avec PHPUnit + rapport de couverture.  

- **Frontend**
  - TypeScript 5 obligatoire.
  - Interface utilisateur pour :  
    - Créer un trajet (station A → station B) + type de trajet.  
    - Consulter les statistiques par code analytique.
  - Tests avec Vitest/Jest + rapport de couverture.

- **Infrastructure** 
  - Docker Engine 25
  - Docker/Docker Compose pour orchestrer backend, frontend, base de données et reverse proxy (si nécessaire).  
  - Déploiement en une commande (`docker compose up -d`).  

> 💡 Conseil : Documente tes choix dans une documentation.

---

## 🔄 CI/CD complet

Voici notre point de vue de la représentation d'un CI/CD complet :
- Build : images backend/frontend
- Qualité : lint + tests + coverage (fail si seuils non atteints)
- Sécurité : SAST/DAST (ex: phpstan, npm audit, Trivy)
- Release : tagging sémantique ou calendaire, changelog
- Delivery : push images vers registry, déploiement automatisé (Compose ou SSH)

## 🎁 Les points Bonus
- Implémenter un algorithme de routage (ex. Dijkstra) pour calculer la distance entre deux stations.
- Exposer un endpoint de statistiques agrégées par code analytique.
- Visualiser ces statistiques dans le frontend (graphique/tableau).

## ✅ Critères d’évaluation
- Couverture : rapports générés et seuils respectés
- OpenAPI : conformité stricte des endpoints et schémas
- Docker : démarrage en une ou deux commandes, documentation claire
- Frontend : UX propre, typé en TypeScript, tests présents
- CI/CD : pipeline fiable, scans basiques de sécurité, images publiées
- Sécurité : HTTPS, auth, headers, gestion des secrets
- Qualité : code lisible, commits structurés, architecture cohérente

---
## 🚀 À toi de jouer !
Nous avons hâte de découvrir ta solution et de voir comment tu abordes ce défi.  
Bonne chance, et surtout amuse-toi en codant !
