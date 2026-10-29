# Theme

Reference notes for the WordPress theme used in this project. See [README](../README.md)
for setup, compilation, and maintenance.

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

## Blocks

See [Blocks](blocks.md) for the Stats, Image, Profiles, Contact, and Editorial
Content blocks, including their fields and rendering behavior.

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
  (`App\Options\Footer`, field `footer_center`). Line breaks and `/` separators become individual address parts; they stack on mobile and return to inline, slash-separated parts at `lg`.
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

## Caching

See [Caching](caching.md) for Redis object cache configuration, Cloudways
environment values, and page-cache/Breeze considerations.

## Mobile layouts

- Below `md`, Hero blocks stack. The first Hero on a page shows its image first, then the title and copy. Subsequent Heroes show the title above the image and the intro/CTA below. White Heroes show the title and copy above the image.
- On mobile, full-height Heroes use a `50svh` image; non-full-height Heroes use a square `100vw` image. The media wrapper clips the parallax image, so it cannot slide over the title. The title has `3rem` of top padding for separation.
- Hero images support an optional `Mobile background image` field. A single image is used at every width; when both are set, the mobile image is selected below `48rem` and the desktop image at or above it. Both image sources use WordPress attachment `srcset` with `sizes="100vw"`.
- Hero scroll arrows are centered on mobile, except the white About Hero and the Profiles block, where they remain at the right.
- Stats stack in one column below `sm` (`40rem`), then use two columns at `sm`; three stats become three columns at `xl`, and four stats become four columns at `2xl`. Mobile source notes and eyebrow labels are smaller.
- Profiles use a 65%-wide left-aligned portrait above the text on mobile; the desktop composition remains unchanged.
- The mobile navigation is a full-screen panel with LinkedIn in the header and rule-separated links including Contact.
- The mobile footer stacks as Contact, logo, address, and LinkedIn, with the up arrow centered.
- The Contact page is available at `/contact/` and is included in the primary navigation.

## Favicon

- `web/app/themes/sage-kraftworx/favicon.svg` is the modern SVG favicon. It is linked after `wp_head()` in `layouts/app.blade.php` with a `filemtime()` cache-busting query.
- `web/favicon.ico` is the site-root legacy fallback. It contains 16, 32, and 48px images and is linked as an alternate icon.
- The WordPress **Site Icon** is a 512px PNG with a black background. WordPress supplies the 32, 192, 180 (Apple touch), and 270px (Windows tile) variants.
- There is no favicon plugin or web app manifest. Add a manifest only if the site needs PWA/install support.

## SVG uploads

- SVG uploads are provided by the **Safe SVG** plugin (`wp-plugin/safe-svg`, active),
  installed through Composer.
- Safe SVG sanitizes uploads, generates SVG dimensions/metadata, and avoids raster image
  processing, so the media library and block editor can use SVG files normally.
- The earlier custom theme SVG class and `enshrined/svg-sanitize` dependency were removed.

## References

- Project setup and maintenance: [README](../README.md)
- Block reference: [blocks.md](blocks.md)
- Caching notes: [caching.md](caching.md)
- Sage docs: <https://roots.io/sage/docs/>
- Sage installation: <https://roots.io/sage/docs/installation/>
- Sage components: <https://roots.io/sage/docs/components/>
- Acorn: <https://roots.io/acorn/docs/>
- `@roots/vite-plugin`: <https://www.npmjs.com/package/@roots/vite-plugin>
