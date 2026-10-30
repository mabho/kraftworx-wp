# Blocks

Reference for the project ACF blocks in `web/app/themes/sage-kraftworx`.
See [Theme](theme.md) for the shared design tokens, typography, breakpoints, and layout.

## Shared block behavior

CTA links are targeted automatically: external hosts open in a new tab with
`rel="noopener noreferrer"`; internal, relative, `mailto:`, and `tel:` links stay in the
same tab. The ACF link target setting is ignored. The shared scroll arrow is a button that
smooth-scrolls to the next sibling block (respecting reduced motion and the admin bar) and
is hidden when there is no next block.

## Stats

The `Stats` block derives its layout from the repeater count (2–4 items): 2 stats render
in two columns with 36px captions, while 3 and 4 stats render in three/four columns at the
`xl` breakpoint with 30px captions. The grid keeps its normal column/row gaps; separators
are absolutely positioned pseudo-elements on the stat items, so they do not affect column
widths or outer alignment. Vertical dividers sit in the column gaps and extend through
wrapped rows via half-gap offsets; when the grid wraps, a full-width horizontal pseudo-
element spans the row from the first to the last column edge. Below `sm` (`40rem`) stats
stack in one column with horizontal dividers between items; from `sm` upward the grid uses
two columns and the existing vertical/horizontal divider behavior. The block reuses the
`FullHeight` partial (default off) and shows the shared scroll arrow when enabled.

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
- SVG images work with the Safe SVG upload support described in [Theme](theme.md#svg-uploads).

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
- Phone fields use `intl-tel-input` with the US as the initial country. The selector shows flags, country names, and dial codes, and formats/validates against the selected country. Validation is visual only; invalid numbers do not block submission. Valid numbers are normalized to E.164 before submission.

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
