# 🚆 Train Routing & Analytics Full Stack Demo

## CI/CD Status

**Pipeline:** [![CI/CD](https://img.shields.io/github/actions/workflow/status/Esysc/defi-fullstack/ci-cd.yml?label=pipeline)](https://github.com/Esysc/defi-fullstack/actions/workflows/ci-cd.yml)
**Pre-commit:** [![pre-commit](https://img.shields.io/github/actions/workflow/status/Esysc/defi-fullstack/ci-cd.yml?job=pre-commit&label=pre-commit)](https://github.com/Esysc/defi-fullstack/actions/workflows/ci-cd.yml)
**Backend:** [![backend](https://img.shields.io/github/actions/workflow/status/Esysc/defi-fullstack/ci-cd.yml?job=backend-tests&label=backend)](https://github.com/Esysc/defi-fullstack/actions/workflows/ci-cd.yml)
**Frontend:** [![frontend](https://img.shields.io/github/actions/workflow/status/Esysc/defi-fullstack/ci-cd.yml?job=frontend-tests&label=frontend)](https://github.com/Esysc/defi-fullstack/actions/workflows/ci-cd.yml)

A production-ready full-stack application demonstrating train route calculation and analytics. This project showcases modern web development practices with Symfony backend, Vue 3 frontend, comprehensive testing, and automated CI/CD pipeline.

---

## About this project

This project implements a train-routing system with analytics capabilities:

**Technology Stack:**

- **Backend:** Symfony 7 (PHP 8.4)
- **Frontend:** Vue 3 + TypeScript + Vuetify
- **Database:** PostgreSQL with Doctrine ORM
- **API:** RESTful API with OpenAPI specification (`openapi.yml`)
- **Authentication:** JWT-based authentication
- **Deployment:** Docker Compose with Nginx reverse proxy

**Key Features:**

- Dijkstra algorithm for optimal route calculation
- Persistent route storage and analytics
- Interactive frontend for route planning and data visualization
- Comprehensive test coverage (backend PHPUnit, frontend Vitest)
- CI/CD pipeline with automated testing and quality checks
- E2E tests with Playwright (Chromium, Firefox, Webkit)

---

## JWT Authentication

Protected endpoints (e.g., `POST /api/v1/routes`) require JWT authentication. This repository provides a small authentication flow for demos:

- `POST /api/v1/auth/register` — create a user
- `POST /api/v1/auth/login` — obtain a JWT

Use the token in requests as:

```shell
Authorization: Bearer <token>
```

Security note: this repository does not commit production private keys. Development keys are either generated automatically at container start (backend entrypoint) or via helper scripts (see SECURITY below); CI generates transient keys before running tests. For production, generate a secure key-pair and use a secrets manager to store the private key.

### Frontend demo: register & login UI

The frontend includes a registration and login page accessible via the header link "Login / Register". Upon successful login, the JWT token is stored in `localStorage` and automatically included in subsequent API requests.

---

## Environment variables

**Backend:**

- `APP_ENV` — Environment mode (prod|dev)
- `APP_DEBUG` — Debug mode (0|1)
- `APP_SECRET` — Application secret key
- `DATABASE_URL` — Database connection string (e.g., `postgresql://postgres:postgres@db:5432/train_routing`)
- `JWT_SECRET_KEY` — Path to JWT private key
- `JWT_PUBLIC_KEY` — Path to JWT public key
- `JWT_PASSPHRASE` — JWT key passphrase (optional for dev)

**Frontend:**

- `VITE_API_URL` — Backend API URL (default: `/api/v1`)

---

## Quick start (Docker)

If you have Docker and Docker Compose installed, start the full stack:

```bash
docker-compose up -d
```

Wait a few seconds for services to spin up, then visit:

- Frontend: <http://localhost:3000>
- Backend API: <http://localhost:8000/api/v1>
- Nginx gateway: <https://localhost>
- API docs (Redoc): <http://localhost:3000/docs> (loads `/api/v1/doc.json`)

Use `./start.sh` as a convenience wrapper for docker-compose up.

---

## Local development (non-Docker)

**Backend requirements:**

- PHP 8.4
- Composer
- PostgreSQL

**Frontend requirements:**

- Node.js 20+
- npm

**Backend setup:**

```bash
cd backend
composer install
php bin/console doctrine:database:create
php bin/console doctrine:schema:create
php bin/console app:load-data
php -S 0.0.0.0:8000 -t public
```

**Frontend setup:**

```bash
cd frontend
npm ci
npm run dev
```


---

## CI/CD Pipeline

The project includes a comprehensive CI/CD pipeline configured in `.github/workflows/ci-cd.yml`:

**Pipeline stages:**

- **Build** — Docker images for backend/frontend
- **Quality** — Linting, tests, and coverage checks (fails if thresholds not met)
- **Security** — Static analysis (PHPStan, npm audit)
- **E2E Tests** — Playwright tests across Chromium, Firefox, and Webkit
- **Artifacts** — Coverage reports and test results

**Quality gates:**

- Backend coverage threshold: 70%
- Frontend coverage threshold: 70%
- All linting must pass
- All tests must pass (unit + E2E)

---

## Bonus features implemented

✅ **Dijkstra routing algorithm** — Calculates optimal distance between stations

✅ **Analytics endpoint** — Aggregated statistics by analytical code (`GET /api/v1/stats/distances`)

✅ **Frontend visualization** — Interactive charts and tables for analytics data

---

## Evaluation criteria met

✅ **Coverage** — Reports generated with thresholds enforced

✅ **OpenAPI** — Strict compliance with endpoint specifications

✅ **Docker** — One-command startup with clear documentation

✅ **Frontend** — Clean UX, TypeScript-typed, comprehensive tests

✅ **CI/CD** — Reliable pipeline with security scans

✅ **Security** — HTTPS, JWT auth, secure headers, secret management

✅ **Quality** — Readable code, atomic commits, coherent architecture


## Tests and quality checks

Backend (PHP / PHPUnit):

```bash
cd backend
composer install
vendor/bin/phpunit
vendor/bin/phpunit --coverage-clover=coverage.xml
```

Frontend (Node / Vitest):

```bash
cd frontend
npm ci
npm run test
npm run coverage
```

Pre-commit (local):

```bash
pip install pre-commit
pre-commit install
pre-commit run --all-files
```

---

## Troubleshooting

- Database issues: `docker-compose logs db` or `php bin/console doctrine:schema:create` inside the backend container.
- Backend start errors: make sure `composer install` ran and dependencies are present.
- Frontend issues: check `npm ci` and `VITE_API_URL`.
- Nginx / CORS: `docker-compose logs nginx` and backend CORS headers.

---

## CI / CD

See `.github/workflows/ci-cd.yml` for the configured pipeline. CI runs backend and frontend tests, linting, coverage checks and uploads coverage artifacts.

---

## Security & JWT key generation (no committed private keys)

This project avoids committing private RSA keys.

Behavior:

- At container start (backend entrypoint), a development RSA keypair will be created automatically in `backend/config/jwt/` if it does not exist.
- CI generates transient keys before running backend tests so the pipeline does not require committed keys.

If you want to generate the key pair manually (local dev), run from the project root:

```bash
mkdir -p backend/config/jwt
openssl genpkey -algorithm RSA -out backend/config/jwt/private.pem -pkeyopt rsa_keygen_bits:4096
openssl rsa -in backend/config/jwt/private.pem -pubout -out backend/config/jwt/public.pem
chmod 600 backend/config/jwt/private.pem
```

For HTTPS (self-signed dev certs), run:

```bash
mkdir -p certs
openssl req -x509 -newkey rsa:4096 -nodes -out certs/cert.pem -keyout certs/key.pem -days 365 -subj "/CN=localhost"
```

Important: never commit private keys into the repository. For production generate keys externally and use a secrets manager or environment-appropriate storage mechanism.

---

## Implemented features

- Dijkstra algorithm in backend for routing
- Persisted routes and analytics endpoint (`GET /api/v1/stats/distances`)
- Frontend UI for analytics and a demo auth/register flow
- CI workflows for tests and checks
