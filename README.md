# Taxido Laravel

Laravel backend and admin panel for the Taxido ride booking application.

## Local setup

Requirements: PHP 8.2+, Composer, Node.js/npm, and the PHP extensions required by Laravel and the installed modules.

1. Copy `.env.example` to `.env` and configure the database, mail, Firebase, and other services you use.
2. Keep the existing licensed installation's `APP_ID` in the local `.env`. This is the Bladelib project ID; it is different from the Envato purchase code. Do not commit `.env` or license values.
3. Install dependencies with `composer install` and `npm install`.
4. Generate an `APP_KEY` with `php artisan key:generate` if this is a new installation.
5. Build frontend assets with `npm run build`.
6. Run database migrations only for a new or intentionally migrated database: `php artisan migrate`.
7. Start the local app with `php artisan serve`.

## Firebase login

Set `FIREBASE_API_KEY`, `FIREBASE_AUTH_DOMAIN`, `FIREBASE_PROJECT_ID`, `FIREBASE_STORAGE_BUCKET`, `FIREBASE_MESSAGING_SENDER_ID`, and `FIREBASE_APP_ID` for the same Firebase project. Add every production hostname used by the site to Firebase Authentication's authorized domains. Google and phone identity tokens are verified against `FIREBASE_PROJECT_ID` on the server.

## cPanel deployment notes

- Deploy the application code and `composer.lock`; keep `.env`, service account JSON, and the purchase code out of GitHub.
- Configure the production `.env` with `APP_ENV=production`, `APP_DEBUG=false`, the production `APP_URL`, database/mail values, Firebase values, and the matching `APP_ID` from this licensed installation.
- If the domain has not been activated for this installation, use the vendor's license workflow with the Envato purchase code and have the vendor confirm domain activation. Do not use the purchase code as `APP_ID`.
- Ensure `storage` and `bootstrap/cache` are writable by PHP. Run `composer install --no-dev --optimize-autoloader`, `npm ci`, and `npm run build` in the deployment workflow where Node is available.
- Set `LOG_LEVEL=info` (or a more restrictive level) in production. The application logging fallback is now `info`; an explicit `.env` value takes precedence.
- Use Redis for `CACHE_STORE`, `SESSION_DRIVER`, and `QUEUE_CONNECTION` when Redis is available. For database-backed cache and queues, confirm the required tables exist and run a queue worker under a process manager.
- After updating `.env`, run `php artisan optimize:clear` once to remove stale caches, then `php artisan optimize` to cache configuration, routes, events, and views from the production environment. Keep `APP_ID` in the environment because the vendor package also reads it directly.
- Run `php artisan migrate --force` only when the release includes reviewed database migrations. Restart queue workers after deployment so they load the new code.
- Point the domain document root to the Laravel `public` directory. Never expose `.env`, `vendor`, or application source through the web root.

## Checks

Run the PHP test suite with `php artisan test`, and build frontend assets with `npm run build`.
