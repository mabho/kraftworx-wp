# Caching

Caching and infrastructure notes for the Kraftworx site. These are not theme
concerns and are kept separate from the theme reference.

## Redis object cache

- The site uses the **Redis Object Cache** plugin (`wp-plugin/redis-cache`, currently 3.0.0), installed through Composer.
- `config/application.php` maps environment values to WordPress Redis constants:
  `WP_REDIS_HOST`, `WP_REDIS_PORT`, `WP_REDIS_PREFIX`, `WP_REDIS_USERNAME`, `WP_REDIS_PASSWORD`, `WP_REDIS_DATABASE`, and `WP_REDIS_CLIENT`.
- Production and remote development credentials come from the environment-specific `.env`; they are never committed.
- Cloudways Redis runs on the application server and listens on `127.0.0.1:6379`. Use `REDIS_HOST=127.0.0.1`, not the public application IP.
- Use a unique prefix per environment, such as `kraftworx_dev_` and `kraftworx_live_`.
- The Redis Object Cache plugin is inactive locally. `ddev sync` deactivates it after importing a remote database. `WP_REDIS_DISABLED=true` remains in the local `.env` as a safety guard; do not set it on remote development or production.
- To use local DDEV Redis later, activate the plugin, set `WP_REDIS_DISABLED=false`, and provide local Redis connection values; otherwise keep the plugin inactive locally.
- After configuring a server environment, activate and enable the object cache:
  ```bash
  wp --path=web/wp plugin activate redis-cache
  wp --path=web/wp redis enable
  wp --path=web/wp redis status
  ```
- The Redis Object Cache drop-in is generated state. Do not edit it manually.
- Breeze is installed for Cloudways Varnish/page-cache integration; it is not an object cache and does not replace Redis.

## Page caching and Breeze

- Redis is an object cache and does not replace full-page caching.
- Breeze 2.6.3 is installed through Composer. It is **inactive locally** and should only be activated on the Cloudways development and production applications.
- In Breeze, keep **Auto Purge Varnish** enabled. Breeze then purges Varnish when posts/pages are created, updated, or deleted, and when plugins/themes are activated or deactivated.
- Breeze's own activation hook initializes the Cloudways defaults: Varnish server `127.0.0.1`, auto-purge enabled, and cache enabled. A custom activation hook is not required.
- DeployHQ must activate the plugin after Composer installs it, then purge:
  ```bash
  wp --path=web/wp plugin activate breeze || true
  wp --path=web/wp breeze purge --cache=all
  ```
- Exclude `/contact/` from Breeze and Cloudways Varnish caching because its nonce is embedded in the cached HTML.
- Do not enable a second object-cache implementation in Breeze while Redis Object Cache is active.
