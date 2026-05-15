# Nova CMS Deployment Guide

This guide covers the production runtime pieces added to Nova CMS: queues, scheduler, cache behavior, backups, and storage expectations.

## Core checklist

1. Install PHP dependencies with optimized autoloading.
2. Set a production `.env`.
3. Run migrations.
4. Build frontend assets.
5. Start a queue worker.
6. Start the Laravel scheduler.
7. Persist `storage/` and especially `storage/app/backups`.

## Suggested deployment commands

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm ci
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Queue worker

Nova CMS now uses queue-backed operational jobs for backups.

Run a worker in production:

```bash
php artisan queue:work --tries=3 --timeout=120
```

Recommended:

- run the worker under `supervisor`, `systemd`, or your platform process manager
- restart workers on deploy with `php artisan queue:restart`

## Scheduler

Nova CMS registers these scheduled tasks:

- `php artisan nova:backup` at `02:00`
- `php artisan nova:backups:prune` at `02:30`
- `php artisan queue:prune-failed --hours=168` at `03:00`

Use one of these patterns:

```bash
php artisan schedule:work
```

Or classic cron:

```cron
* * * * * cd /path/to/nova-cms && php artisan schedule:run >> /dev/null 2>&1
```

## Cache strategy

Nova CMS uses explicit invalidation for the most important public-facing CMS data:

- public site settings
- header/footer menus
- SEO settings

Those caches are refreshed automatically when admins update settings, menus, SEO, or themes. You can also refresh them manually in `/admin/operations`.

## Backups

Backups are written to:

- `storage/app/backups`

Each backup is a portable CMS snapshot artifact. SQLite environments can also include the raw `database.sqlite` file inside the generated archive.

Operational commands:

```bash
php artisan nova:backup
php artisan nova:backup --sync
php artisan nova:backups:prune
```

Restore workflow:

- open `/admin/operations`
- upload a Nova `.json` or `.zip` snapshot
- confirm replacement
- optionally create a safety backup first

## Storage and permissions

Ensure these are writable by the PHP process:

- `storage/`
- `bootstrap/cache/`

If you serve uploaded media publicly, keep the storage symlink in place:

```bash
php artisan storage:link
```

## Environment recommendations

At minimum, review:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain`
- `QUEUE_CONNECTION=database`
- `CACHE_STORE=file` or your preferred production cache backend
- database credentials
- mail credentials

## Post-deploy sanity checks

```bash
php artisan about
php artisan schedule:list
php artisan queue:failed
php artisan test --filter=OperationsManagementTest
```

From the UI, verify:

- `/admin/operations` loads
- backup queueing works
- completed backups can be downloaded
- CMS caches can be refreshed
