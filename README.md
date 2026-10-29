# Kraftworx

WordPress marketing site for Kraftworx, built on Bedrock and the Kraftworx Sage
theme.

## Stack

| Component | Version / source |
| --- | --- |
| CMS | WordPress 7.1.3 via Composer |
| Application | Bedrock |
| Theme | `web/app/themes/sage-kraftworx`, based on Sage 11.2.1 |
| Local environment | DDEV (`wp-bedrock`, PHP 8.4, MariaDB 11.8, Node 24) |
| Frontend | Vite 8 + Tailwind CSS 4 |
| ACF | ACF PRO via Composer |
| Object cache | Redis Object Cache (inactive locally); see [Caching](library/caching.md) |
| Page cache / Varnish | Breeze 2.6.3 (inactive locally) |

## Requirements

- DDEV
- Composer access to the ACF PRO repository, through `COMPOSER_AUTH` or a local
  Composer `auth.json`
- SSH access configured for Cloudways if you need to sync remote data

Do not commit credentials or `.env` files.

## Local setup

### 1. Environment

Create the local environment file and fill in the database credentials, salts, and
local URL:

```bash
cp .env.example .env
```

For DDEV, the local site URL is normally:

```dotenv
WP_ENV='development'
WP_HOME='https://kraftworx.ddev.site'
WP_SITEURL="${WP_HOME}/wp"
```

The local Redis settings are present in `.env.example`, but the Redis Object Cache
plugin is inactive locally. `WP_REDIS_DISABLED=true` remains as a safety guard.
See [Caching](library/caching.md).

### 2. Install PHP dependencies

Configure ACF PRO authentication first, either through `COMPOSER_AUTH` or a local
Composer `auth.json`. Then install both Composer projects:

```bash
ddev start
ddev composer install
ddev exec bash -c 'cd web/app/themes/sage-kraftworx && composer install'
```

The root Composer project manages Bedrock, WordPress core, and site plugins. The
theme has a separate `composer.json` and `composer.lock`; install theme
dependencies from the theme directory, not from the project root.

Composer is pinned to **2.8.12** for this project. See
[Tech debt](library/tech-debt.md) before changing that pin.

### 3. Database

Import an existing database:

```bash
ddev import-db --file=_databases/<dump>.sql
```

Or sync from the configured Cloudways development environment:

```bash
ddev sync dev
```

This replaces the local database. The sync command requires the `kraftworxDev`
SSH alias and the configuration in `.ddev/sync/config.json`.

To download a database dump without importing it or replacing the local database:

```bash
ddev db-dump dev
```

Use `test` or `prod` to pull another environment. `prod` uses the `live`
environment configuration until a separate `prod` entry exists. Dumps are written
to `_databases/<timestamp>_kraftworx_<environment>.sql.gz`.

### 4. Frontend dependencies and build

```bash
ddev exec -d /var/www/html/web/app/themes/sage-kraftworx npm install
ddev exec -d /var/www/html/web/app/themes/sage-kraftworx npm run build
```

If Vite fails with a Rolldown binding error, restore the Linux binding before
building:

```bash
ddev exec -d /var/www/html/web/app/themes/sage-kraftworx \
  npm install --no-save --force @rolldown/binding-linux-arm64-gnu@1.2.14
```

### 5. Start the site

```bash
ddev launch
```

## Compilation

Run frontend commands inside the DDEV web container:

```bash
ddev exec -d /var/www/html/web/app/themes/sage-kraftworx npm run dev
ddev exec -d /var/www/html/web/app/themes/sage-kraftworx npm run watch
ddev exec -d /var/www/html/web/app/themes/sage-kraftworx npm run build
```

After Blade or template changes, clear compiled views:

```bash
ddev exec wp acorn view:clear
```

## Maintenance

### Composer updates

Update Bedrock, WordPress, and site plugins from the project root:

```bash
ddev composer update
```

Update theme dependencies from the theme directory:

```bash
ddev exec bash -c 'cd web/app/themes/sage-kraftworx && composer update'
```

Both `composer.lock` files are committed. Composer-installed `vendor/` directories
are ignored.

### Theme upgrades

Sage is a copied starter, not a package dependency. The theme is not upgraded by
running Composer.

- **Sage baseline:** 11.2.1
- **Project theme version:** 0.1.0

To upgrade, compare the current upstream Sage release against the local theme and
manually port relevant changes. Do not overwrite project code. After an upgrade,
update the Sage baseline and version history in this file, then rebuild assets.

### Cache

Redis Object Cache is configured separately from the theme. See
[Caching](library/caching.md) for environment values, activation commands, and
the local development behavior.

Breeze is installed through Composer for Cloudways Varnish integration. It is
intentionally **inactive locally**; activate it only on development/production
servers, with **Auto Purge Varnish** enabled. After deployments, run:

```bash
wp --path=web/wp breeze purge --cache=all
```

The `ddev sync` command deactivates both Breeze and Redis Object Cache locally
after importing the database.

### Deployment

DeployHQ deploys from Git using the rules in `.deployignore`:

- The frontend build runs in DeployHQ’s Build Pipeline.
- Composer dependencies and WordPress core are installed on the server.
- Environment-specific values belong in the DeployHQ config file, not in Git.

### HubSpot form schema

Add the portal ID, form GUID, and a private app token with the `forms` read scope
to `.env`. The contact block fetches the form definition server-side and caches
the normalized schema for 12 hours. If the API is unavailable or no token is
configured, the original contact fields are used as a fallback.

Fetch the raw definition as structured JSON:

```bash
ddev hubspot-form
ddev hubspot-form <form-id>
```

The result is formatted with `jq` and can be redirected to a file. Never commit
`HUBSPOT_ACCESS_TOKEN`. Set `HUBSPOT_SUBMIT=true` to send submissions to HubSpot
from a non-production environment; production defaults to enabled.

To invalidate the cached schema after changing the form:

```bash
ddev exec wp eval 'App\Forms\HubSpotForm::refresh();'
```

If HubSpot marks submissions as **Unregistered Site Domain**, add the exact
submitting domain in HubSpot under **Settings → Tracking & Analytics → Tracking
Code → Advanced Tracking → Additional site domains**. Add the local
`kraftworx.ddev.site` domain when testing locally, and the production domain when
live. Then manually release the blocked submissions from the form's spam view.

### Documentation
### Documentation

- [Theme reference](library/theme.md) — theme decisions, styling, typography,
  breakpoints, header, footer, and related behavior.
- [Block reference](library/blocks.md) — Stats, Image, Profiles, Contact, and
  Editorial Content blocks.
- [Caching](library/caching.md) — Redis and page-cache notes.
- [Tech debt](library/tech-debt.md) — known workarounds and resolution criteria.

## Known cosmetic leftovers

A few scaffold identifiers still use Sage defaults:

- Theme `composer.json` name: `roots/sage`
- Theme text domain: `sage`

These are functional. The `style.css` Theme Name is already
**Kraftworx Sage**. Changing the text domain also requires updating the
translation scripts and files.

## Version history

| Date | Version | Type | Notes |
| --- | --- | --- | --- |
| 2026-10-17 | 11.2.1 | Sage baseline | Initial install; Vite base path set; assets built |
| 2026-10-17 | 0.1.0 | Project theme | Set “Kraftworx Sage” identity in `style.css` |
