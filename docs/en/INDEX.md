# Slide show block for Silverstripe 5

An Elemental block that tells a story as a deck of slides. On phones the slides
stack; from 48rem up they become a swipeable deck with dots, arrows, keyboard
support and a progress bar.

## Install

As a Composer module (from your own repository or a path repository):

```bash
composer require sunnysideup/silverstripe-slideshow-block
vendor/bin/sake dev/build flush=1
```

It needs `dnadesign/silverstripe-elemental` ^5 and
`symbiote/silverstripe-gridfieldextensions` ^4.

If you copy the module into your project instead, update `resource_module` in
`_config/slideshowblock.yml` so the CSS and JS can be found.

## What's in it

| Class | Files | Purpose |
|---|---|---|
| `SlideShowBlock` | `.php` `.ss` `.scss` `.js` | The block: deck shell, controls, slide list |
| `SlideShowBlockSlide` | `.php` `.ss` | Base slide (Versioned). Shared fields and the inline item editor |
| `SlideShowBlockSlideTitle` | `.php` `.ss` `.scss` `.js` | Big title, subtitle, light subtitle, pills |
| `SlideShowBlockSlideCards` | `.php` `.ss` `.scss` `.js` | Card grid. Style: Default / Warning / Plain. 2, 3, 4 or auto columns |
| `SlideShowBlockSlideProof` | `.php` `.ss` `.scss` `.js` | Big number plus reasons. JS counts the number up |
| `SlideShowBlockSlideTimeline` | `.php` `.ss` `.scss` `.js` | Bars scaled to the longest one |
| `SlideShowBlockSlideFlow` | `.php` `.ss` `.scss` `.js` | Boxes joined by arrows plus a bullet list |
| `SlideShowBlockSlidePricing` | `.php` `.ss` `.scss` `.js` | Factor sliders plus price bands |
| `SlideShowBlockSlideSteps` | `.php` `.ss` `.scss` `.js` | Numbered steps plus a button |
| `SlideShowBlockSlideItem` | `.php` | One row of a slide's list (Versioned) |
| `SlideShowBlockIcons` | `.php` | Line icons for cards (add more in YAML) |
| `SlideShowBlockImportTask` | `.php` | Builds a whole block from YAML |

Slide types are layouts, not specific slides, so one type can be used many
times. The Ship Shape deck uses Cards four times: problems, promises, hosting
and audits.

### Lists are inline-editable

Each slide's lists (pills, cards, reasons, bars, steps, price bands…) are
`SlideShowBlockSlideItem` records, edited directly in the slide's form:
add a row, type, drag to reorder. No pop-ups. Each slide type says which
columns it needs (`$items_fields`, `$extra_items_fields`). Items are versioned
and owned by their slide, which is owned by the block, so publishing the page
or block publishes everything.

### Highlighting words in a heading

Wrap words in asterisks: `Ship Shape *Hosting*` renders as
`Ship Shape <mark>Hosting</mark>`.

## Import the Ship Shape deck

```bash
# create a new draft page with the block on it
vendor/bin/sake dev/tasks/slideshow-block-import

# add it to an existing page (ID or URL segment), replace an earlier import, publish
vendor/bin/sake dev/tasks/slideshow-block-import "page=hosting&replace=1&publish=1"

# use your own file
vendor/bin/sake dev/tasks/slideshow-block-import "file=app/_content/my-deck.yml&page=12"
```

The content lives in `_content/ship-shape-hosting.yml`. Keys are field names,
`Type` picks the slide class, and `Items` / `ExtraItems` are the two lists.

## Styling

All colours, radii and the slide height are CSS custom properties with zero
specificity, so a theme can restyle the block without recompiling:

```css
.slideshow {
    --slideshow-accent: var(--theme-sun);
    --slideshow-surface-2: #fdf8ec;
    --slideshow-slide-height: 36rem;
}
```

Each slide type loads only its own CSS, and only when that type is on the page.

## Build

```bash
npm install
npm run build    # client/src -> client/dist (compiled CSS, copied JS)
npm run watch
```

`client/dist` is committed, so a build is only needed after changing the SCSS or JS.

## Adding a slide type

1. `src/Slides/SlideShowBlockSlideXxx.php` extending `SlideShowBlockSlide`; set
   `$table_name`, `$singular_name`, `$layout_key` and `$items_fields`.
2. `templates/Sunnysideup/SlideShowBlock/Slides/SlideShowBlockSlideXxx.ss`.
3. `client/src/scss/slides/SlideShowBlockSlideXxx.scss`, then `npm run build`.
4. Optional: `client/src/js/slides/SlideShowBlockSlideXxx.js` and `$has_js = true`.
   Listen for the `slideshow:activate` / `slideshow:deactivate` events.
5. `dev/build flush=1`.
