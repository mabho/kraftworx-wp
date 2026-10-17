# Theme

Reference notes for the WordPress theme used in this project.

## Decision

- **Theme directory:** `web/app/themes/sage-kraftworx`
- **Theme name:** Kraftworx Sage (project identity in `style.css`)
- **Project theme version:** `0.1.0`
- **Base:** [Sage](https://roots.io/sage/) 11.x
- **Initial Sage baseline:** **11.2.1** (recorded at install)
- **Installed:** 2026-10-17
- **Type:** standalone starter theme (not a child theme, no parent)

Sage was chosen because it is the Roots-native theme for Bedrock and provides:

- Blade templates and Blade components (the closest analogue to Drupal SDC components)
- Acorn (Laravel-style container, service providers, console commands)
- Compatibility with `log1x/acf-composer` for code-first ACF fields and blocks
- Vite + Tailwind CSS for per-component assets and a modern build
- A single consistent stack with Bedrock and DDEV

## How it was installed

```bash
ddev exec bash -c 'cd web/app/themes && composer create-project roots/sage sage-kraftworx --no-interaction'
ddev wp theme activate sage-kraftworx
```

`composer create-project` **copies** the Sage starter into the target directory. There is no
parent `sage` theme, and no `Template:` header. Upstream Sage changes are not pulled
automatically; upgrades are manual (see below).

## Installed stack

| Component | Version |
| --- | --- |
| Theme (project) | 0.1.0 |
| Sage baseline | 11.2.1 |
| Acorn | 6.3.0 (`roots/acorn ^6.0`) |
| Illuminate components | 13.x |
| Vite | 8.3.4 |
| Tailwind CSS | 4.x |
| Node | 24.20.0 |
| npm | 11.19.0 |
| PHP (DDEV) | 8.4 |

Theme requires PHP >= 8.3 and Node `^20.19.0 || >=22.12.0`.

## Composer model

The theme is a **separate Composer project** from Bedrock:

- `web/app/themes/sage-kraftworx/composer.json`
- `web/app/themes/sage-kraftworx/composer.lock`
- `web/app/themes/sage-kraftworx/vendor/` (gitignored)

Run theme dependencies from the theme directory:

```bash
ddev composer --working-dir=web/app/themes/sage-kraftworx require <package>
# or
ddev exec bash -c 'cd web/app/themes/sage-kraftworx && composer require <package>'
```

Use the **project root** Composer only for site-level dependencies (WordPress core and
plugins such as ACF PRO). Do not install theme dependencies from the root: they would land
in Bedrock's `vendor/` and would not be loaded by Acorn.

Both `composer.lock` files should be committed. Both `vendor/` directories are ignored.

## Assets

Sage uses Vite:

```bash
ddev exec bash -c 'cd web/app/themes/sage-kraftworx && npm install'
ddev exec bash -c 'cd web/app/themes/sage-kraftworx && npm run build'
```

- Vite `base` was updated in `vite.config.js` to
  `/app/themes/sage-kraftworx/public/build/` (the default points at `sage`).
- `public/*` is gitignored by the theme (except `public/.gitkeep`), so a fresh
  checkout/deploy must run `npm run build` before the site will render.
- Missing build output produces: `Vite manifest not found .../public/build/manifest.json`.
- Live reloading (`npm run dev` + DDEV) is not configured yet; the current flow is a
  production build.

## Design tokens

Colors are defined once in `resources/css/app.css` under `@theme`. These are the only
colors exposed to the editor and used by the component blocks:

| Token | Value | Example utilities |
| --- | --- | --- |
| `--color-primary` | `#1d4ed8` | `bg-primary`, `text-primary` |
| `--color-secondary` | `#0f766e` | `bg-secondary`, `text-secondary` |
| `--color-tertiary` | `#d97706` | `bg-tertiary`, `text-tertiary` |
| `--color-black` | `#000000` | `bg-black`, `text-black` |
| `--color-white` | `#ffffff` | `bg-white`, `text-white` |

- `theme.json` defines the matching 5-swatch editor palette.
- `vite.config.js` sets `disableTailwindColors: true`, so the generated
  `public/build/assets/theme.json` no longer includes Tailwind's default 288-color
  palette.
- Replace the placeholder hex values with the brand colors, then run `npm run build`.
- Known exception: the starter `resources/views/components/alert.blade.php` still uses
  default Tailwind colors (`green`, `yellow`, `red`, `indigo`). Those remain available
  in CSS because the default palette is not cleared. Update the alert to the tokens if
  the 5-color system should be enforced everywhere.

## Version tracking and upgrades

Sage is a starter, not a package dependency, so record the baseline version here and treat
upgrades as deliberate merges. Two versions exist:

- **Sage baseline:** **11.2.1** — the upstream starter the theme was created from.
- **Project theme version:** `0.1.0` — declared in `style.css`; this is what WordPress
  reports.

`ddev wp theme get sage-kraftworx --field=version` returns the **project theme version**
(`0.1.0`), not the Sage baseline. The baseline is recorded here because WordPress does not
track it once the theme has been customized.

- To upgrade, compare the current upstream Sage release against the local theme and
  manually port relevant changes. Do not blindly overwrite project code.
- After any upgrade, update the Sage baseline in this file and in the version history below.

### Version history

| Date | Version | Type | Notes |
| --- | --- | --- | --- |
| 2026-10-17 | 11.2.1 | Sage baseline | Initial install; Vite base path set; assets built |
| 2026-10-17 | 0.1.0 | Project theme | Set "Kraftworx Sage" identity in `style.css` |

## Known cosmetic leftovers

A few scaffold identifiers still use Sage defaults:

- `composer.json` name: `roots/sage`
- Text domain: `sage`

These are functional as-is. The `style.css` Theme Name (`Kraftworx Sage`) and Description
are already project-specific. Changing the text domain also requires updating the
`translate` scripts and translation files.

## References

- Sage docs: <https://roots.io/sage/docs/>
- Sage installation: <https://roots.io/sage/docs/installation/>
- Sage components: <https://roots.io/sage/docs/components/>
- Acorn: <https://roots.io/acorn/docs/>
- `@roots/vite-plugin`: <https://www.npmjs.com/package/@roots/vite-plugin>
