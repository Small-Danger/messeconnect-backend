# MesseConnect — Backend

API REST Laravel 12 pour MesseConnect : authentification Sanctum, espaces fidèle, paroisse et administration.

Frontend associé : [messeconnect-frontend](https://github.com/Small-Danger/messeconnect-frontend)

## Prérequis

- PHP 8.2+
- Composer 2
- PostgreSQL (production) ou SQLite (développement local)

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

L’API est disponible sur `http://127.0.0.1:8000` (routes sous `/api`).

## Variables d’environnement

| Variable | Description |
|----------|-------------|
| `APP_KEY` | Clé d’application (`php artisan key:generate`) |
| `APP_URL` | URL publique de l’API |
| `DB_CONNECTION` | `sqlite` (local) ou `pgsql` (production) |
| `DATABASE_URL` | URL PostgreSQL (Railway, etc.) |
| `FRONTEND_URL` | URL du frontend (CORS, liens) |
| `CORS_ALLOWED_ORIGINS` | Origines autorisées (séparées par des virgules) |
| `SANCTUM_STATEFUL_DOMAINS` | Domaines Sanctum pour le SPA |
| `RUN_SEED` | `true` au premier déploiement uniquement, puis `false` |
| `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` | Optionnel — OAuth fidèle |

## Comptes démo (après seed)

Mot de passe : `password`

| Rôle | Email |
|------|-------|
| Admin | `admin@messeconnect.test` |
| Paroisse | `secretaire@paroisse-saint-pierre.test` |
| Fidèle | `fidele@messeconnect.test` |

## Déploiement Railway

- Fichiers : `railway.toml`, `scripts/railway-start.sh`
- Healthcheck : `GET /up`
- PostgreSQL : définir `DATABASE_URL`, `DB_CONNECTION=pgsql`
- Premier déploiement : `RUN_SEED=true`, puis repasser à `false`

## Données Burkina Faso

Le seeder `BurkinaFasoParoissesSeeder` crée 6 diocèses et 13 paroisses validées (avec démos messes / publications via `ParoisseDemoSeeder`).

```bash
php artisan migrate:fresh --seed
```

## Licence

Projet MesseConnect — usage privé / déploiement selon votre politique.
