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

## Styling and assets

Sage uses Vite + Tailwind CSS 4. There is a single CSS entry per context; Tailwind
handles bundling, nesting, and variables, so no Sass/Less/Stylus preprocessor is used.

Sources:

- `resources/css/app.css` — frontend Tailwind entry (`@import "tailwindcss"`,
  `@source`, `@theme`)
- `resources/css/editor.css` — block-editor styles (Tailwind entry plus the editor
  title field styles)
- `resources/css/theme.css` — global font faces, `--font-sans`/`--font-display`
  tokens, type scale, heading typography, and container width
- `resources/css/acf-editor.css` — ACF accordion styling in the block editor
- `resources/js/*.js` — JS source; compiled by Vite

Commands (run from the theme directory):

```bash
npm install
npm run dev     # Vite HMR
npm run watch   # build + watch
npm run build   # production build
```

- `vite.config.js` inputs the CSS and JS entries.
- The frontend layout (`resources/views/layouts/app.blade.php`) loads `app.css` and
  `app.js` via `@vite`.
- Editor styles are injected/enqueued in `app/setup.php`: `editor.css` in the editor
  iframe, `acf-editor.css` in the block editor, and the `editor.js` entry.
- Compiled output goes to `public/build`, which is gitignored (`public/*`), so the
  compiled CSS/JS are never committed.
- Vite `base` is set to `/app/themes/sage-kraftworx/public/build/`.
- Missing build output produces `Vite manifest not found .../public/build/manifest.json`.
- HMR (`npm run dev`) with DDEV is not configured yet; use `watch` or `build` for now.

## Design tokens

Colors are defined once in `resources/css/theme.css` under `@theme`, so both the
frontend and the editor share the same tokens. These are the only colors exposed to the
editor and used by the component blocks:

| Token | Value | Example utilities |
| --- | --- | --- |
| `--color-primary` | `#00b0ff` | `bg-primary`, `text-primary` |
| `--color-secondary` | `#0f766e` | `bg-secondary`, `text-secondary` |
| `--color-tertiary` | `#d97706` | `bg-tertiary`, `text-tertiary` |
| `--color-black` | `#000000` | `bg-black`, `text-black` |
| `--color-white` | `#ffffff` | `bg-white`, `text-white` |

- `theme.json` defines the matching 5-swatch editor palette.
- `vite.config.js` sets `disableTailwindColors: true`, so the generated
  `public/build/assets/theme.json` no longer includes Tailwind's default 288-color
  palette.
- `--color-primary` is the design blue (`#00b0ff`). The secondary and tertiary values
  are still placeholders; replace them with the brand colors, then run
  `npm run build`.
- Known exception: the starter `resources/views/components/alert.blade.php` still uses
  default Tailwind colors (`green`, `yellow`, `red`, `indigo`). Those remain available
  in CSS because the default palette is not cleared. Update the alert to the tokens if
  the 5-color system should be enforced everywhere.

## Typography and container

### Fonts

- **Display/title font:** **Replica Pro** Regular (weight 400).
  File: `resources/fonts/replica-pro-regular.woff2`.
- **Body/UI font:** **Avenir**, self-hosted from `Avenir.ttc` as four faces:
  | File | Weight | Use |
  | --- | --- | --- |
  | `avenir-light.woff2` | 300 (`font-light`) | Hero intro, nav links, CTA |
  | `avenir-book.woff2` | 400 (body) | Default text, source notes |
  | `avenir-medium.woff2` | 500 (`font-medium`) | Section intros, stat captions |
  | `avenir-black.woff2` | 800 (`font-extrabold`) | Section headings, stats, labels |
- Avenir Black reports `usWeightClass: 800`; use `font-extrabold` (800), not
  `font-black` (900), or the browser will synthesize the weight.
- `@font-face` rules and the `--font-sans` / `--font-display` tokens live in
  `resources/css/theme.css`, imported by both `app.css` and `editor.css`.
- `--font-sans` sets the Tailwind `font-sans` family and the `body` font. `h1` uses
  `--font-display` at weight 400; `h2`/`h3` use Avenir at weight 800. All three set
  `font-synthesis: none`.
- Replica Pro and the two most-used Avenir faces (Light, Book) are preloaded in
  `resources/views/layouts/app.blade.php`. They are declared in the Vite `input` so
  `Vite::asset()` can resolve their hashed manifest entries.
- `theme.json` exposes `sans` as **Avenir** and `display` as **Replica Pro**; its
  `styles.elements.h1` uses the display family at weight 400, while `h2`/`h3` use the
  sans family at weight 800.
- The Replica file was converted from `ReplicaTrialTT-Regular.ttf` (a trial). Replace
  `replica-pro-regular.woff2` with the licensed commercial Replica Pro file before
  production; keep the same file name and family so no CSS changes are needed.
- Only the `.woff2` files are referenced by CSS and published by the build. The source
  files (`Avenir.ttc`, `MyriadPro-Regular.otf`, `ReplicaTrialTT-Regular.ttf`) are kept
  in `resources/fonts` for reference but are not copied to `public/build`.

### Type scale from the design

Sizes were read from the PDF font specs (`pdftohtml`) at the design's native scale
(1:1 with CSS px). Tokens are defined in `theme.css`; use the token rather than raw
values.

| Role | Font | Design size | Token / utility |
| --- | --- | --- | --- |
| Hero title | Replica Pro 400 | 144px | `--text-hero` (9rem, leading 0.95); Hero uses a fluid `clamp(2.75rem, 10vw, 9rem)` |
| Hero intro / description | Avenir Light 300 | 30px | `--text-lead` (1.875rem) + `font-light` |
| Navigation (active) | Avenir Medium 500 | 18px | `--text-nav` (1.125rem) + `font-medium` |
| Navigation (links) | Avenir Light 300 | 18px | `--text-nav` + `font-light` |
| Section heading | Avenir Black 800 | 72px | `--text-section` (4.5rem) + `font-extrabold` |
| Section intro | Avenir Medium 500 | 42px | `--text-section-intro` (2.625rem) + `font-medium` |
| Stat number | Avenir Black 800 | 98px | `--text-stat` (6.125rem) |
| Stat caption (2-up) | Avenir Medium 500 | 36px | `--text-stat-label` (2.25rem) |
| Stat caption (3/4-up) | Avenir Medium 500 | 30px | `--text-stat-label-compact` (1.875rem) |
| Uppercase label | Avenir Black 800 | 29px | `--text-label` (1.8125rem) |
| Source note | Avenir Book 400 | 21px | `--text-note` (1.3125rem) |
| CTA label | Avenir Light 300 | 18px (assumed) | `text-lg font-light` |

The CTA is not present as text in the PDF (likely outlined), so 18px Light matches the
navigation instead of an extracted spec. Confirm with the designer if the CTA appears in
another artboard.

The `Stats` block derives its layout from the repeater count (2–4 rows): 2 stats render
in two columns with 36px captions, while 3 and 4 stats render in three/four columns at the
`xl` breakpoint with 30px captions. The grid keeps its normal column/row gaps; separators
are absolutely positioned pseudo-elements on the stat items, so they do not affect column
widths or outer alignment. Vertical dividers sit in the column gaps and extend through
wrapped rows via half-gap offsets; when the grid wraps, a full-width horizontal pseudo-
element spans the row from the first to the last column edge. Dividers appear from `md`
upward. Below `md` the stats stack in one column and no divider is needed. The block reuses
the `FullHeight` partial (default off) and shows the shared scroll arrow when enabled.

### Stats fields

**Content**

- `section_eyebrow` (textarea) — optional label above the left column title.
- `title` (textarea) — left column title; line breaks are preserved.
- `intro_text` (textarea) — left column intro text; line breaks are preserved.

**Stats**

- `right_content` (WYSIWYG) — rich content above the stats, before the black rule.
- `eyebrow` (textarea) — optional stats eyebrow above the grid.
- `stats` (repeater, 2–4 items):
  - `eyebrow` (textarea) — card eyebrow above the value, e.g. BEFORE / AFTER.
  - `value` (text) — the primary card value, e.g. 69%, 40% lower, 1.7x.
  - `label` (textarea) — the card description; line breaks are preserved.
  - `progress` (range, 0–100) — bar value. The bar is a 16px square bar and renders
    when the value is greater than 0.
  - `bar_label` (text) — optional label under the bar, e.g. 100% OF TRACK.
- `source` (text) — optional source note for the stats.

**Layout**

- `background_color` (select) — section background: primary, secondary, tertiary, black,
  or white.
- `full_height` (true/false) — fills the viewport and shows the shared scroll arrow.
  Default is off.

The bar and its label are bottom-aligned within each card column. The WYSIWYG uses the
lean **Kraftworx** toolbar with a styles dropdown for the **Lead** paragraph (same
responsive size as card values, inheriting the surrounding text color) and **Accent**
(primary color), **Eyebrow**, and **Muted** inline `<span>` styles. The classes live in
`theme.css` and are loaded into TinyMCE with `mce_css`.

ACF initializes its WYSIWYG TinyMCE instance manually, so `editor.js` also applies the
lean toolbar and style formats through ACF's `wysiwyg_tinymce_settings` filter.

All ACF textarea fields are treated as plain text: their values are escaped and rendered
with `{!! nl2br(e($value)) !!}`, so line breaks become `<br />` without WYSIWYG controls
or raw HTML. Do not pair this with `whitespace-pre-line`.

`right_content` is the one rich-text field and is rendered with
`{!! wp_kses_post($right_content) !!}`. Blade's `{!! !!}` is unescaped, like Twig's
`|raw`; only use it with sanitized HTML. Plain-text fields use `{{ }}`/`e()`, and
textareas use `nl2br(e())`.

CTA links are targeted automatically: external hosts open in a new tab with
`rel="noopener noreferrer"`; internal, relative, `mailto:`, and `tel:` links stay in the
same tab. The ACF link target setting is ignored. The shared scroll arrow is a button that
smooth-scrolls to the next sibling block (respecting reduced motion and the admin bar) and
is hidden when there is no next block.

### Breakpoints

- Breakpoints are project tokens in the `@theme` block of `resources/css/theme.css`:
  `--breakpoint-sm` 40rem, `md` 48rem, `lg` 64rem, `xl` 80rem, and `2xl` 96rem.
- Use Tailwind variants (`sm:`, `lg:`, `2xl:`) or the `@variant` directive for custom CSS.
  Do not write raw media queries with pixel/rem values:
  ```css
  @variant 2xl {
    .container {
      max-width: 100rem;
    }
  }
  ```
- CSS custom properties cannot be used in media conditions
  (`@media (min-width: var(--breakpoint-2xl))` is invalid). Tailwind resolves `@variant`
  against the tokens at build time.

### Container

- `.container` is capped at **1600px (`100rem`) from the `2xl` breakpoint (96rem)**.
  Below that, Tailwind's responsive container widths remain (1280px at 1440).
- The cap uses `@variant 2xl`, so it follows the project breakpoint token instead of a
  hardcoded `1536px` media query.
- The Hero, Section (contained), and page header use `.container`.
- Intentional narrow widths (`max-w-2xl`, `max-w-md`, `max-w-4xl`) are unchanged.
- The editor's block preview uses the same `theme.css`, so it matches the frontend.

## Header

- Fixed black header, `4rem` tall, with the white `logo-kraftworx.svg` moved to
  `resources/images/logo-kraftworx.svg`. The logo markup is shared via
  `resources/views/partials/site-logo.blade.php`.
- Desktop navigation is centered from `md`; below `md` the menu collapses into a
  hamburger panel toggled in `resources/js/app.js`.
- Navigation comes from the WordPress `primary_navigation` menu. Active items use the
  primary blue; the Home menu item is marked current on the front page.
- The LinkedIn link and inline icon are shared via
  `resources/views/partials/linkedin-link.blade.php`; update its default URL with the
  company URL.
- The header is fixed: `main` has `pt-16`, and `--site-header-offset` accounts for the
  header and the WordPress admin bar. The scroll-arrow offset includes the header.
- Menu items are managed in **Appearance → Menus**.

## Footer

- Black footer with the shared logo on the left, editable center content, and the footer
  menu plus LinkedIn on the right. An up-arrow button sits above the content with a gap.
- Center content comes from the ACF **Footer** options page
  (`App\Options\Footer`, field `footer_center`); line breaks render as `<br />`.
- The right side renders the `footer_navigation` menu (named **Footer Navigation**,
  currently containing Contact) plus the LinkedIn icon. There is no hardcoded Contact link.
- The up arrow is a `data-scroll-top` button using the shared
  `resources/views/partials/arrow-icon.blade.php` icon rotated 180° — the same 40px icon
  used by the scroll-down button. `resources/js/app.js` scrolls to the top and respects
  reduced motion.
- Sticky footer: `#app` is a flex column with
  `min-h-[calc(100svh-var(--admin-bar-height))]` and `main` is `flex-1`, so the footer sits
  at the bottom of the viewport on short pages.
- The LinkedIn link and icon are shared via the same
  `resources/views/partials/linkedin-link.blade.php` partial.

## SVG uploads

- SVG uploads are provided by the **Safe SVG** plugin (`wp-plugin/safe-svg`, active),
  installed through Composer.
- Safe SVG sanitizes uploads, generates SVG dimensions/metadata, and avoids raster image
  processing, so the media library and block editor can use SVG files normally.
- The earlier custom theme SVG class and `enshrined/svg-sanitize` dependency were removed.

## Version tracking and upgrades

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
