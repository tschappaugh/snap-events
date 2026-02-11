# Snap Events

A WordPress plugin that displays upcoming events using Gutenberg blocks. Built as a portfolio project to demonstrate modern WordPress development practices.

## Skills Demonstrated

### PHP / WordPress Backend
- **Custom Post Type** registration (`snap_event`) with REST API support
- **Meta field management** via `register_post_meta()` with REST schema definitions
- **Custom REST API endpoint** (`/wp-json/snap-events/v1/events`) with parameter validation, pagination, and date-filtered queries
- **WP_Query** wrapper class with `meta_query` filtering for future-only events and numeric date sorting
- **Dynamic block rendering** with server-side PHP (`render.php`) for both block types
- **Theme-agnostic templating** using `the_content` and `render_block` filters instead of custom template files
- **Plugin architecture** using an OOP class loader pattern with single-responsibility classes

### JavaScript / React (Gutenberg)
- **Two custom Gutenberg blocks** (Events Grid and Events List) with `block.json` metadata, `edit.js` editor components, and `save.js` returning `null` for dynamic rendering
- **Editor sidebar panel** built in React using `@wordpress/plugins` and `@wordpress/edit-post` for managing event meta fields (dates, venue, location)
- **Inspector controls** for per-block styling configuration (colors, spacing, borders, shadows) using `@wordpress/components`
- **ServerSideRender** for live block preview in the editor
- **Frontend interactivity** (`view.js`) with vanilla JavaScript controllers for Load More pagination and Sort Toggle via the REST API
- **Build tooling** with `@wordpress/scripts` (Webpack-based) compiling multiple entry points

### REST API Design
- Custom endpoint shared by both blocks, returning pre-formatted event data
- Pagination support with `has_more` flag for Load More functionality
- Sort direction parameter for ascending/descending date order
- Consistent query logic between server-rendered output and API responses

### CSS / Responsive Design
- **CSS custom properties** for per-instance block styling (card colors, button styles, border settings)
- **CSS Grid** layout for the Events Grid block with configurable columns and gap
- **Flexbox** layout for the Events List block with horizontal rows
- **Responsive breakpoints** at 980px and 767px for column stacking and layout adjustments
- **Modular CSS architecture** split across shared content styles, layout-specific styles, and interactive control styles

### Accessibility
- Single tab stop per card/item using one focusable link (the title heading)
- Decorative images marked with `alt="" role="presentation"`
- Redundant "View Event" links hidden from assistive technology with `aria-hidden="true" tabindex="-1"`
- `aria-live="polite"` status region announces Load More and Sort changes to screen readers
- `:focus-visible` outlines on all interactive elements
- Disabled button states during async operations to prevent double-clicks

### Architecture Decisions
- **Progressive enhancement**: events render server-side first, JavaScript adds interactivity on top
- **`data-config` attribute** on block wrappers instead of `wp_localize_script`, so multiple block instances on one page each get independent configuration
- **Two separate blocks** instead of one block with a layout toggle, keeping each block's code focused and avoiding attribute conflicts
- **No custom template files**: single event pages use WordPress content and block filters for theme compatibility

## Features

- Custom `snap_event` post type with an `/events/` URL slug
- Two Gutenberg blocks: **Events Grid** (card layout) and **Events List** (single-column rows)
- Editor sidebar panel for event dates, venue, and location fields
- Configurable card styles, button styles, column count, and spacing per block instance
- Load More button for paginated event loading
- Sort toggle to switch between soonest-first and furthest-out-first ordering
- Automatic filtering to show only future events
- Single event page meta display with theme block filtering
- Works with any WordPress theme (classic or block-based)

## Requirements

- WordPress 6.0+
- PHP 7.4+
- Node.js (for building from source)

## Installation

1. Download or clone this repository
2. Copy the `app/public/wp-content/plugins/snap-events` directory into your WordPress site's `wp-content/plugins/` folder
3. In your WordPress admin, go to **Plugins** and activate **Snap Events**

### Building from Source

If the `build/` directory is not included or you want to make changes:

```bash
cd wp-content/plugins/snap-events
npm install
npm run build
```

Use `npm run start` for a development watch mode that recompiles on file changes.

## Usage

1. **Create events**: Go to **Events > Add New** in the WordPress admin. Fill in the title, content, and featured image as usual. Use the **Event Details** sidebar panel to set the start date, end date, venue, city, state, and country.

2. **Display events**: Add the **Events Grid** or **Events List** block to any page or post using the block editor. Use the block's Inspector Panel (sidebar) to configure how many events to show, which fields to display, column count (grid only), and all styling options.

3. **Single event pages**: Each event automatically gets its own page at `/events/event-slug/`. The plugin prepends the event date and location details above the post content.

## Theme compatibility

The plugin works with any WordPress block theme. On single event pages, it automatically hides post-meta blocks (author, date, categories, tags) that aren't relevant to events and prepends the event details (date, venue, location) above the content.

Some themes don't include a Featured Image block in their single post template. If event thumbnails aren't showing on single event pages, add the Featured Image block to your theme's single post template:

1. Go to **Appearance > Editor > Templates > Single Posts**
2. Add a **Featured Image** block above or below the title
3. Save the template

This is a one-time change per theme and won't be overwritten by plugin updates.

## File Structure

```
snap-events/
├── snap-events.php              # Main plugin file
├── includes/
│   ├── class-snap-events.php    # Plugin loader
│   ├── class-events-cpt.php     # Custom post type
│   ├── class-events-meta.php    # Meta field registration
│   ├── class-events-query.php   # Query logic and date formatting
│   ├── class-events-block.php   # Block registration and editor assets
│   ├── class-events-rest.php    # REST API endpoint
│   └── class-events-template.php # Single event display filters
├── src/
│   ├── blocks/
│   │   ├── events-grid/         # Grid block (block.json, edit.js, render.php, view.js)
│   │   └── events-list/         # List block (block.json, edit.js, render.php, view.js)
│   └── editor/
│       └── event-sidebar/       # Editor sidebar panel (React)
├── assets/css/                  # Stylesheets (display, interactive, list)
└── build/                       # Compiled output (auto-generated)
```

## License

GPL-2.0+
