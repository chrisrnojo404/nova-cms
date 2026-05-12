# Nova CMS

Nova CMS is a modular, Laravel-powered content management system foundation aimed at WordPress/Joomla-style site management with a modern developer experience.

## Phase 1

Phase 1 delivers the platform foundation:

- Laravel 13 application bootstrap
- Breeze authentication flow
- Email verification and session-backed auth
- Sanctum API authentication endpoints
- Spatie roles and permissions
- Protected admin dashboard shell
- Activity log, settings, theme, and plugin registry tables
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
- `/admin` for the Phase 1 dashboard

Seeded admin account:

- Email: `admin@nova-cms.test`
- Password: `password`

## Docker

An example Docker workflow is included in [docker-compose.yml](./docker-compose.yml) with environment values in [.env.docker.example](./.env.docker.example).

## Verification

Phase 1 was verified with:

```bash
php artisan migrate:fresh --seed
npm run build
php artisan test
```
