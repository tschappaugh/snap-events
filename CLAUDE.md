# CUALS Events Display Plugin - Project Documentation

This document provides context for Claude Code when working on this project or its Gutenberg block successor.

## Project Overview

**CUALS Events Display** is a WordPress plugin that displays upcoming events for credit union associations. It uses a custom post type with native WordPress meta boxes (no ACF dependency) and renders events via a shortcode `[cuals_events]`.

### Key Features
- Custom post type `cuals_event` with slug `/events/`
- Native WordPress meta boxes for event data (dates, venue, location)
- Shortcode-based rendering with CSS Grid layout
- Default thumbnail system (uses `icon-events.svg` when no image set)
- Single event template with sidebar support
- Responsive design: 3 columns → 2 columns (tablet) → 1 column (mobile)

## Architecture Decisions

### Why Native Meta Boxes (Not ACF)
- Reduces plugin dependencies
- Full control over data structure
- Easier to migrate/export
- No licensing concerns
- Meta fields stored directly in `wp_postmeta`

### Why Shortcode (Current Implementation)
- Works with any theme (Divi, classic, etc.)
- Simple to place in page builders
- Server-side rendering, no JavaScript required

### Default Thumbnail System
Instead of requiring featured images, the plugin automatically provides a default icon:
- Uses `get_post_metadata` filter to inject default thumbnail ID
- Creates media library attachment on first use
- Stores attachment ID in `cuals_default_event_thumbnail_id` option
- Marks attachment with `_cuals_default_event_icon` meta for identification

## Data Structure

### Custom Post Type: `cuals_event`
```php
'supports' => ['title', 'editor', 'excerpt']  // No 'thumbnail' - uses default
'rewrite'  => ['slug' => 'events']
'show_in_rest' => true  // Gutenberg compatible
```

### Meta Fields
| Field | Key | Format | Required |
|-------|-----|--------|----------|
| Start Date | `start_date` | `Ymd` (e.g., `20260115`) | Yes |
| End Date | `end_date` | `Ymd` | No |
| Event Name | `event_name` | String | No |
| Venue | `venue` | String | No |
| City | `city` | String | No |
| State | `state` | String | No |
| Country | `country` | String | No |

**Important:** Dates are stored in `Ymd` format (e.g., `20260115`) for proper sorting and comparison. The meta box converts from `YYYY-MM-DD` input format on save.

### Query Logic
Events are queried with:
```php
'meta_key'  => 'start_date',
'orderby'   => 'meta_value',
'order'     => 'ASC',
'meta_query' => [
    'key'     => 'start_date',
    'value'   => date('Ymd'),  // Today
    'compare' => '>=',
    'type'    => 'NUMERIC',
]
```
This returns only future events, sorted by start date ascending.

## File Structure

```
cuals-event-display/
├── cuals-events-display.php      # Main plugin file, CPT registration
├── includes/
│   ├── class-events-meta-box.php # Admin meta box UI and save logic
│   ├── class-events-query.php    # WP_Query wrapper, date formatting
│   ├── class-events-shortcode.php # [cuals_events] shortcode renderer
│   └── class-default-thumbnail.php # Default image filter
├── templates/
│   └── single-cuals_event.php    # Single event page template
├── assets/
│   ├── css/events-display.css    # Frontend styles
│   ├── js/events-display.js      # Placeholder for interactions
│   └── images/icon-events.svg    # Default event icon
└── debug-events.php              # Standalone debug script (deprecated)
```

## CSS Design System

### CSS Custom Properties
Both the plugin and child theme use consistent CSS variables:

```css
:root {
  /* Colors */
  --color-primary-blue: #407ec9;
  --color-primary-blue-hover: #4196db;
  --color-primary-green: #97d700;
  --color-dark-bg: #2e3858;
  --color-darker-bg: #4a5580;
  --color-black: #000;
  --color-white: #fff;
  --color-white-70: rgba(255, 255, 255, 0.7);

  /* Spacing */
  --spacing-40: 40px;
  --spacing-30: 30px;
  --spacing-20: 20px;
  --spacing-10: 10px;

  /* Typography */
  --font-size-44: 44px;
  --font-size-18: 18px;
  --font-size-16: 16px;
  --font-weight-heading: 700;
  --font-weight-link: 700;
  --font-weight-text: 400;
  --line-height-1-3: 1.3em;
}
```

### Grid Layout Pattern
```css
.cuals-events-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

/* Tablet */
@media (max-width: 980px) {
    grid-template-columns: repeat(2, 1fr);
}

/* Mobile */
@media (max-width: 767px) {
    grid-template-columns: 1fr;
    gap: 20px;
}
```

### Card Styling
- Background: `var(--color-dark-bg)` (#2e3858)
- Padding: 30px
- Icon height: 83.5px (centered)
- Title: 18px, bold, white
- Body text: 16px, 400 weight, white at 70% opacity
- Links: white, underlined, uppercase for "Read More"

## Shortcode Usage

```
[cuals_events]                    // All upcoming events
[cuals_events count="3"]          // Limit to 3 events
[cuals_events state="MO"]         // Filter by state
[cuals_events city="Kansas City"] // Filter by city
```

## Template Integration

The single event template uses Divi's container structure:
```php
get_header();
// #main-content > .container > #content-area > #left-area + sidebar
get_sidebar();
get_footer();
```

Template is loaded via `template_include` filter in main plugin class.

## Lessons Learned

### Divi Blog Module Override (News Posts)
When styling Divi's blog module to match events, we discovered:

1. **Salvattore Masonry Library**: Divi uses Salvattore for blog grids, which creates `.column.size-1of3` wrapper divs
2. **Solution**: Target `.et_pb_salvattore_content` with CSS Grid and use `display: contents` on `.column` wrappers
3. **Specificity Issues**: Divi's `.et_pb_gutters3` rules require `!important` overrides
4. **Key selector**: `.et_pb_blog_grid .et_pb_salvattore_content > .column { display: contents !important; }`

### Date Format Gotcha
- HTML date inputs use `YYYY-MM-DD`
- WordPress meta stores as `Ymd` (no dashes)
- Must convert on save and display
- Use `NUMERIC` type in meta_query for proper comparison

## Gutenberg Block Conversion Notes

When converting to a Gutenberg block for FSE themes:

### Required Changes
1. **Block Registration**: Replace shortcode with `register_block_type()` + `block.json`
2. **Editor UI**: React component with `@wordpress/blocks`, `InspectorControls`
3. **Live Preview**: Use `ServerSideRender` or custom REST API endpoint
4. **FSE Template**: Create `templates/single-cuals_event.html` for block themes

### Reusable Code
- `class-events-query.php` - Query logic can be reused in render callback
- `class-events-meta-box.php` - Works as-is for admin
- `class-default-thumbnail.php` - Works as-is
- CSS variables and card styles - Adapt for block editor

### Suggested Block Structure
```
cuals-events-block/
├── build/                        # Compiled JS/CSS
├── src/
│   ├── block.json               # Block metadata + attributes
│   ├── index.js                 # Block registration
│   ├── edit.js                  # Editor component (React)
│   ├── save.js                  # Usually null for dynamic blocks
│   └── editor.scss              # Editor-specific styles
├── includes/
│   ├── class-events-block.php   # PHP registration + render_callback
│   ├── class-events-query.php   # (reuse from shortcode version)
│   ├── class-events-meta-box.php # (reuse)
│   └── class-default-thumbnail.php # (reuse)
└── assets/
    └── (same as shortcode version)
```

### Block Attributes (Suggested)
```json
{
  "attributes": {
    "count": { "type": "number", "default": -1 },
    "columns": { "type": "number", "default": 3 },
    "showExcerpt": { "type": "boolean", "default": true },
    "city": { "type": "string", "default": "" },
    "state": { "type": "string", "default": "" }
  }
}
```

## Related Files in Child Theme

The Divi child theme (`divi-child`) contains:

### News Default Thumbnail
`/includes/class-news-default-thumbnail.php` - Same pattern as events, uses `icon-news.svg`

### News Grid Styling
`/style.css` lines 294-404 - CSS overrides for Divi blog module to match events styling

### Single Post Styling
`/style.css` lines 161-266 - Styles for single news/event pages, search results, sidebar

## Debug Tools

Admin submenu at **Events → Debug** shows:
- All events with their start dates
- Whether dates are valid (future) or past
- Quick edit links

## Testing Checklist

When making changes, verify:
- [ ] Events display on homepage via shortcode
- [ ] Only future events appear (past events filtered)
- [ ] Events sorted by start date ascending
- [ ] Default icon appears when no featured image
- [ ] Single event page shows all meta fields
- [ ] Responsive breakpoints work (3 → 2 → 1 column)
- [ ] News posts grid matches events styling
- [ ] Sidebar displays on single event pages
