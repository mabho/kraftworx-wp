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
- Local development is disabled with `WP_REDIS_DISABLED=true` in the local `.env`. The plugin remains active for parity but does not initialize the object cache. Do not set this on remote development or production.
- To use local DDEV Redis later, set `WP_REDIS_DISABLED=false` and local Redis connection values; otherwise keep local Redis disabled.
- After configuring a server environment, activate and enable the object cache:
  ```bash
  wp --path=web/wp plugin activate redis-cache
  wp --path=web/wp redis enable
  wp --path=web/wp redis status
  ```
- The Redis Object Cache drop-in is generated state. Do not edit it manually.
- Breeze is not used for Redis. It is only relevant if Cloudways page caching/Varnish/CDN is intentionally enabled.

## Page caching and Breeze

- Redis is an object cache and does not replace full-page caching.
- Breeze, Cloudways Varnish, and CDN caching can be evaluated separately if page-level caching is needed.
- Exclude dynamic pages such as the Contact page from full-page caching because its nonce is embedded in the HTML.
- Do not enable a second object-cache implementation in Breeze while Redis Object Cache is active.
