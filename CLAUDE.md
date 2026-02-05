# Snap Events Plugin - Project Documentation

This document provides context for Claude Code when working on the Snap Events WordPress plugin.

## Project Overview

**Snap Events** is a WordPress plugin that displays upcoming events using a Gutenberg block. It uses a custom post type with an editor sidebar panel for event meta fields and renders events via a dynamic block.

### Key Features
- Custom post type `snap_event` with slug `/events/`
- Gutenberg block `snap-events/events-grid` for displaying events
- Editor sidebar panel for event data (dates, venue, location) using React
- Native WordPress featured images (no default thumbnail system)
- Theme-agnostic single event display via `the_content` filter
- Responsive design: configurable columns with breakpoints
- Configurable card styling (colors, padding, borders, shadows)

## Architecture Decisions

### Why Editor Sidebar (Not Classic Meta Boxes)
- Better integration with Gutenberg editor workflow
- Uses WordPress REST API for meta field access
- Modern React-based UI components
- No page refresh needed when saving

### Why Gutenberg Block (Not Shortcode)
- Native integration with block editor
- Live preview via ServerSideRender
- Block supports for alignment, colors, spacing
- Inspector controls for all settings
- Better user experience for non-technical users

### Theme Compatibility Approach
- Uses `the_content` filter to prepend event meta on single event pages
- Uses `render_block` filter to hide irrelevant theme blocks (author, categories, "More posts")
- Works with any theme (classic or block themes like Twenty Twenty-Five)
- No custom template files required

## Data Structure

### Custom Post Type: `snap_event`
```php
'supports'     => ['title', 'editor', 'excerpt', 'thumbnail', 'custom-fields']
'rewrite'      => ['slug' => 'events']
'show_in_rest' => true
'has_archive'  => false
```

### Meta Fields
| Field | Key | Format | REST Enabled |
|-------|-----|--------|--------------|
| Start Date | `start_date` | `Ymd` (e.g., `20260115`) | Yes |
| End Date | `end_date` | `Ymd` | Yes |
| Venue | `venue` | String | Yes |
| City | `city` | String | Yes |
| State | `state` | String | Yes |
| Country | `country` | String | Yes |

**Important:** Dates are stored in `Ymd` format for proper sorting. The editor sidebar handles conversion to/from ISO format for the date picker.

### Query Logic
```php
'meta_key'    => 'start_date',
'orderby'     => 'meta_value_num',
'order'       => 'ASC',
'meta_query'  => [
    'key'     => 'start_date',
    'value'   => current_time('Ymd'),  // Uses WP timezone
    'compare' => '>=',
    'type'    => 'NUMERIC',
]
```
Returns only future events, sorted by start date ascending.

## File Structure

```
snap-events/
├── snap-events.php                    # Main plugin file, constants, activation
├── package.json                       # Build tooling (@wordpress/scripts)
├── includes/
│   ├── class-snap-events.php          # Main plugin class, loads dependencies
│   ├── class-events-cpt.php           # CPT registration
│   ├── class-events-meta.php          # REST API meta field registration
│   ├── class-events-query.php         # WP_Query wrapper, date formatting
│   ├── class-events-block.php         # Block registration + editor assets
│   └── class-events-template.php      # Single event content/block filters
├── src/
│   ├── blocks/
│   │   └── events-grid/
│   │       ├── block.json             # Block metadata + attributes
│   │       ├── index.js               # Block registration
│   │       ├── edit.js                # Editor component (React)
│   │       ├── save.js                # Returns null (dynamic block)
│   │       └── render.php             # Server-side render callback
│   └── editor/
│       ├── index.js                   # Editor entry point
│       └── event-sidebar/
│           ├── index.js               # Plugin registration
│           └── EventDetailsSidebar.js # Meta fields panel (React)
├── build/                             # Compiled JS (auto-generated)
│   ├── blocks/events-grid/
│   │   ├── index.js
│   │   ├── index.asset.php
│   │   ├── block.json
│   │   └── render.php
│   └── editor/
│       ├── index.js
│       └── index.asset.php
└── assets/
    └── css/
        └── events-display.css         # Frontend grid/card styles
```

## Block Configuration

### Block Attributes (block.json)
```json
{
  "anchor": { "type": "string", "default": "" },
  "count": { "type": "number", "default": 6 },
  "columns": { "type": "number", "default": 3 },
  "showExcerpt": { "type": "boolean", "default": true },
  "showImage": { "type": "boolean", "default": true },
  "showDate": { "type": "boolean", "default": true },
  "showLocation": { "type": "boolean", "default": true },
  "cardBackgroundColor": { "type": "string", "default": "#2e3858" },
  "cardTextColor": { "type": "string", "default": "rgba(255, 255, 255, 0.7)" },
  "cardHeadingColor": { "type": "string", "default": "#ffffff" },
  "cardLinkColor": { "type": "string", "default": "#ffffff" },
  "cardPadding": { "type": "number", "default": 30 },
  "cardBorderRadius": { "type": "number", "default": 0 },
  "cardBoxShadow": { "type": "boolean", "default": false },
  "cardBorderWidth": { "type": "number", "default": 0 },
  "cardBorderColor": { "type": "string", "default": "#cccccc" },
  "gridGap": { "type": "number", "default": 30 }
}
```

### Block Supports
- Alignment: `wide`, `full`
- Anchor: enabled
- HTML editing: disabled

## Single Event Page Customization

The `Snap_Events_Template` class modifies single event display:

### Content Filter (`the_content`)
- Prepends event meta (date, venue, location) as styled HTML box
- Only runs on singular `snap_event` pages
- Uses inline styles for theme compatibility

### Block Filter (`render_block`)
Hides these blocks on single event pages:
- `core/post-author`, `core/post-author-name` (no author needed)
- `core/post-terms` (no categories displayed)
- `core/query` (removes "More posts" query block)
- Paragraphs containing only "Written by" or "in"
- Headings containing "More posts"
- Empty group blocks

## Build Commands

```bash
cd app/public/wp-content/plugins/snap-events
npm install          # Install dependencies
npm run build        # Production build
npm run start        # Development watch mode
```

## Date Handling

### Timezone Considerations
- Use `current_time('Ymd')` instead of `date('Ymd')` for WordPress timezone
- Date picker sends ISO format, sidebar converts to Ymd for storage
- End date offset by -1 day was fixed in sidebar component

### Format Conversions
- Storage: `Ymd` (e.g., `20260115`)
- Display: `F j, Y` (e.g., `January 15, 2026`)
- Picker: ISO 8601 (conversion handled in React)

## CSS Design System

### Grid Layout
```css
.snap-events-grid {
    display: grid;
    grid-template-columns: repeat(var(--snap-events-columns, 3), 1fr);
    gap: var(--snap-events-gap, 30px);
}

/* Breakpoints: 980px (2 cols), 767px (1 col) */
```

### Card Styling
Configurable via block attributes:
- Background, text, heading, link colors
- Padding, border-radius, border, box-shadow
- Applied via CSS custom properties

## Testing Checklist

When making changes, verify:
- [ ] Events display via block on any page
- [ ] Only future events appear (past events filtered)
- [ ] Events sorted by start date ascending
- [ ] Featured image displays (optional)
- [ ] Single event page shows meta box
- [ ] "Written by" and "More posts" hidden on single events
- [ ] Responsive breakpoints work
- [ ] Editor sidebar saves all meta fields
- [ ] Date picker values save correctly
- [ ] Block preview updates in editor

## Key Differences from CUALS Plugin

| Feature | CUALS (Old) | Snap Events (Current) |
|---------|-------------|----------------------|
| Post Type | `cuals_event` | `snap_event` |
| Rendering | Shortcode | Gutenberg Block |
| Meta Input | Classic meta boxes | Editor sidebar (React) |
| Thumbnails | Forced default SVG | Native WordPress |
| Single Template | Custom PHP template | Content/block filters |
| Theme Support | Divi-specific | Any theme |
