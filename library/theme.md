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

## Image block

ACF block **Image** (`acf/image`), implemented in `App\Blocks\Image` with the view
`resources/views/blocks/image.blade.php`. It is separate from the core Image block and
appears in the **Kraftworx Components** category.

### Image fields

**Content**

- `image` (image, required) — attachment URL, alt text, and width/height. The image is
  used as a normal foreground image, not a background.

**Layout**

- `background_color` (select) — primary, secondary, tertiary, black, or white. Defaults
  to white and colors the section around the image.
- `full_height` (true/false) — fills the viewport below the fixed header, centers the
  image, and shows the shared scroll arrow. Defaults to off.

**Rendering**

- The image is centered inside the standard `.container` and uses a responsive width:
  100% by default, then 90% (`sm`), 80% (`md`), 70% (`lg`), 60% (`xl`), and 40% (`2xl`).
  It keeps its natural height via `h-auto`.
- There is no overlay, background-image treatment, cropping, or `object-cover`; the
  background color applies only to the section.
- The section uses `py-16 lg:py-24` for separation from adjacent blocks.
- Width/height attributes reduce layout shift; images use `loading="lazy"` and
  `decoding="async"`.
- SVG images work with the Safe SVG upload support described below.

## Profiles

The **Profiles** block renders the Leadership section on the About page. It uses a private `profile` post type and manually ordered ACF relationships, so profiles are structured content but have no landing pages.

### Profile post type

- Post type key: `profile`; admin label **Profiles**.
- Registered in `App\PostTypes\Profile` and hooked on `init` from `app/setup.php`.
- Private content: `public`, `publicly_queryable`, `query_var`, `rewrite`, and `has_archive` are all `false`; search and nav-menu exposure are disabled.
- Admin UI is enabled with `show_ui => true`; `show_in_rest => false` so profiles use the classic editor.
- Supports `title` and `thumbnail`. The title is the person's name; the featured image is the transparent portrait.
- There is no public archive or single URL. Profiles are only available to queries and the Profiles block.

### Profile fields

**Profile details**

- `role` (text) — role under the name, e.g. Co-Founder.
- `bio` (WYSIWYG) — rich biography using the **Kraftworx** toolbar.

The profile field group lives in `App\Fields\ProfileFields` and is attached to `post_type == profile`.

### Profiles block fields

**Content**

- `title` (text) — section title, default **Leadership**.
- `profiles` (relationship, required) — profiles selected manually. Drag to reorder; the order is preserved.

**Layout**

- `background_color` (select) — primary, secondary, tertiary, black, or white. Defaults to black; text colors adapt to the background.
- `show_brand_mark` (true/false) — shows the large Kraftworx X mark at the bottom left. Defaults on.
- `full_height` (true/false) — fills the viewport and shows the shared scroll arrow. Defaults on.

### Leadership layout

- Desktop uses two independent columns.
  - Left column: title, then profiles at even positions in the relationship order (2nd, 4th, …).
  - Right column: profiles at odd positions (1st, 3rd, …).
  - With Craig, Rand, Judy, Dan selected in that order, Craig and Judy are on the right and Rand and Dan are on the left, matching the design.
- The title sits in the left column at the top; the right column starts at the same top edge.
- Mobile collapses to one column in the relationship order, with the title first. This uses `display: contents` plus order utilities so the DOM order remains intact.
- The portrait is first in the card markup, above the text. At `lg`, the portrait is 50% of the card width and is pulled into the reserved left area with a negative bottom margin. The text starts 40% across the card and halfway down the portrait, so the masked right edge of the portrait clears the text while the left area stays reserved for the image.
- Below `lg`, the card stacks: a 65%-wide portrait, then full-width text. The section title is centered on mobile.
- Profile name is `text-2xl` on mobile and `text-3xl` at `lg`; role and bio are `text-lg` on mobile and `text-xl` at `lg`.

### Portrait mask and gradient

- The portrait frame is `583 / 336` and uses `resources/images/kraftworx-leadership-mask.svg` as a CSS mask. The mask is stretched to the frame; both diagonal sides share the same angle.
- The frame background is an automatic `linear-gradient(45deg, primary, white)` behind the transparent PNG. The blue-to-white gradient therefore shows through the transparency and is clipped by the mask.
- Upload subject-only portraits with a transparent background (no baked-in backdrop). The supplied design composites with the blue band are not suitable if the CSS gradient should be visible.
- Images use `object-fit: cover` with `object-position: bottom center`. There is no portrait-position field; all portraits are bottom-aligned.
- `mask-image` is provided with `-webkit-mask-image` for Safari; `overflow: hidden` is the fallback. No JavaScript is involved. `clip-path`/`mask` are supported by current Chrome, Firefox, and Safari.
- The mask is applied to the frame, not the `<img>`, so no image edge can extend beyond the mask.

The mask SVG was corrected so the right diagonal is parallel to the left: the polygon is `10.74 0, 25.16 0, 14.42 12.63, 0 12.63` in a `25.16 × 12.63` viewBox.

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

## Contact block

- `App\Blocks\Contact` renders the contact form only; it has no title, intro, section wrapper, container, background, or outer padding.
- It is designed to be placed inside `Section`, `Editorial content`, or another column.
- Fields: `submit_label` and `confirmation_message`.
- Add separate Heading or Paragraph blocks for any title or introductory copy.
- The form text is white, so it should be placed on a black or dark background.
- Multiple Contact blocks are allowed. Each render generates unique field IDs so forms can coexist on one page.
- The Contact page uses the Editorial content block: the page title is the wrapper title, the intro copy is in the left column, and the Contact form is in the right column.
- The form posts to `admin-post.php` with the `kraftworx_contact` action and a nonce. `App\Forms\ContactForm` validates and sends the message to the site admin email, with the sender as `Reply-To`.
- A honeypot field is included. Success and error states are shown via the `contact_status` query parameter.

## Editorial content block

- `App\Blocks\EditorialContent` is a wrapper with an optional title and a fixed, locked two-column structure.
- It encloses one `core/columns` block. The wrapper and the Columns block use `templateLock: "all"`, so editors cannot add, remove, reorder, or move columns, or add blocks outside them.
- Each `core/column` explicitly has `templateLock: false`; this is required so its own InnerBlocks stay editable. Without it, the Columns lock propagates into the column content.
- The wrapper has a single `Title` textarea field. Line breaks are preserved; it is rendered above the columns using the same display type as Hero titles: Replica Pro 400, `clamp(2.75rem, 10vw, var(--text-hero))`, `0.875` line height, and tight tracking.
- When the title is empty, no title element or title spacing is rendered.
- Editors can insert any blocks into either column, including headings, paragraphs, images, and Contact.
- The block uses a 34% left column, 16% gap, and 50% right column at `lg`; below `lg`, the columns stack.
- The widths are CSS-driven. Editors cannot change the column configuration.
- Apart from the title field, content is edited as normal blocks, not ACF WYSIWYG fields. Source paragraphs in the right column are separated by block CSS, not per-paragraph padding.
- The block is applied to the second About columns section; the original Section remains as a reference.

## Mobile layouts

- Below `md`, Hero blocks stack. The first Hero on a page shows its image first, then the title and copy. Subsequent Heroes show the title above the image and the intro/CTA below. White Heroes show the title and copy above the image.
- On mobile, full-height Heroes use a `50svh` image; non-full-height Heroes use a square `100vw` image. The media wrapper clips the parallax image, so it cannot slide over the title. The title has `3rem` of top padding for separation.
- Hero images support an optional `Mobile background image` field. A single image is used at every width; when both are set, the mobile image is selected below `48rem` and the desktop image at or above it. Both image sources use WordPress attachment `srcset` with `sizes="100vw"`.
- Hero scroll arrows are centered on mobile, except the white About Hero and the Profiles block, where they remain at the right.
- Stats use two columns on mobile and tablet; three stats become three columns at `xl`, and four stats become four columns at `2xl`. Mobile source notes and eyebrow labels are smaller.
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
