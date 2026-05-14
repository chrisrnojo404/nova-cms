# Nova CMS

Nova CMS is a modular, Laravel-powered content management system foundation aimed at WordPress/Joomla-style site management with a modern developer experience.

## Platform Status

Completed foundation work now includes:

- Laravel 13 application bootstrap
- Breeze authentication flow
- Email verification and session-backed auth
- Sanctum API authentication endpoints
- Spatie roles and permissions
- Protected admin dashboard shell
- Activity log, settings, theme, and plugin registry tables
- Pages, posts, categories, media, menus, themes, plugins, SEO, and API foundations
- Docker scaffolding for PHP, Nginx, MySQL, and Node
- Theme and plugin directory foundations

## Local development

Install PHP dependencies and frontend packages:

```bash
composer install
npm install
```

Prepare the app:

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Open:

- `/` for the public preview
- `/login` for authentication
- `/admin` for the CMS dashboard

Seeded admin account:

- Email: `admin@nova-cms.test`
- Password: `password`

## Docker

An example Docker workflow is included in [docker-compose.yml](./docker-compose.yml) with environment values in [.env.docker.example](./.env.docker.example).

## API

Nova CMS exposes a versioned API at `/api/v1`.

Useful endpoints:

- `GET /api/v1/meta`
- `GET /api/v1/openapi.json`
- `GET /api/v1/settings/public`
- `POST /api/v1/auth/login`
- `GET /api/v1/pages`
- `GET /api/v1/posts`
- `GET /api/v1/menus/location/header`

Full API usage docs live in [docs/API.md](./docs/API.md).

## Verification

The current foundation has been verified with:

```bash
php artisan migrate:fresh --seed
npm run build
php artisan test
```
