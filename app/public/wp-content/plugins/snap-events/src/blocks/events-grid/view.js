/**
 * Events Grid - Frontend Interactivity
 *
 * Handles Load More and Sort Toggle functionality.
 * Each block instance on the page gets its own controller.
 */
( function () {
	'use strict';

	function initEventsGrids() {
		const grids = document.querySelectorAll(
			'.snap-events-grid[data-config]'
		);

		grids.forEach( ( grid ) => {
			new EventsGridController( grid );
		} );
	}

	class EventsGridController {
		constructor( gridElement ) {
			this.grid = gridElement;
			this.config = JSON.parse(
				gridElement.getAttribute( 'data-config' )
			);
			this.currentPage = 1;
			this.currentOrder = this.config.defaultSortOrder;
			this.isLoading = false;

			this.controls = this.grid.querySelector(
				'.snap-events-controls'
			);
			this.loadMoreBtn = this.grid.querySelector(
				'.snap-events-load-more'
			);
			this.sortBtn = this.grid.querySelector(
				'.snap-events-sort-toggle'
			);
			this.statusDiv = this.grid.querySelector(
				'.snap-events-status'
			);

			this.init();
		}

		init() {
			if ( this.loadMoreBtn ) {
				this.loadMoreBtn.addEventListener( 'click', () =>
					this.handleLoadMore()
				);
			}

			if ( this.sortBtn ) {
				this.sortBtn.addEventListener( 'click', () =>
					this.handleSortToggle()
				);
			}
		}

		async handleLoadMore() {
			if ( this.isLoading ) {
				return;
			}

			this.setLoading( true );
			this.currentPage++;

			try {
				const response = await this.fetchEvents(
					this.currentPage,
					this.currentOrder
				);

				if ( response.events && response.events.length > 0 ) {
					this.appendEvents( response.events );
					this.updateStatus(
						'Loaded ' +
							response.events.length +
							' more events'
					);
				}

				if ( ! response.has_more ) {
					this.loadMoreBtn.classList.add(
						'snap-events-hidden'
					);
					this.updateStatus( 'All events loaded' );
				}
			} catch ( error ) {
				this.updateStatus(
					'Failed to load more events. Please try again.'
				);
				this.currentPage--;
			} finally {
				this.setLoading( false );
			}
		}

		async handleSortToggle() {
			if ( this.isLoading ) {
				return;
			}

			this.setLoading( true );

			const newOrder =
				this.currentOrder === 'ASC' ? 'DESC' : 'ASC';

			try {
				const response = await this.fetchEvents( 1, newOrder );

				this.currentOrder = newOrder;
				this.currentPage = 1;

				this.grid
					.querySelectorAll( '.snap-event-card' )
					.forEach( ( card ) => card.remove() );

				if ( response.events && response.events.length > 0 ) {
					this.appendEvents( response.events );
				}

				this.updateSortButton();

				if ( this.loadMoreBtn ) {
					if ( response.has_more ) {
						this.loadMoreBtn.classList.remove(
							'snap-events-hidden'
						);
					} else {
						this.loadMoreBtn.classList.add(
							'snap-events-hidden'
						);
					}
				}

				this.updateStatus(
					this.currentOrder === 'ASC'
						? 'Showing soonest events first'
						: 'Showing furthest out events first'
				);
			} catch ( error ) {
				this.updateStatus(
					'Failed to change sort order. Please try again.'
				);
			} finally {
				this.setLoading( false );
			}
		}

		async fetchEvents( page, order ) {
			const url = new URL( this.config.restUrl );
			url.searchParams.set( 'page', page );
			url.searchParams.set( 'per_page', this.config.count );
			url.searchParams.set( 'order', order );

			const response = await fetch( url.toString() );

			if ( ! response.ok ) {
				throw new Error( 'HTTP error: ' + response.status );
			}

			return await response.json();
		}

		appendEvents( events ) {
			events.forEach( ( event ) => {
				const cardHtml = this.renderEventCard( event );
				const template = document.createElement( 'template' );
				template.innerHTML = cardHtml.trim();

				if ( this.controls ) {
					this.controls.before( template.content.firstChild );
				} else {
					this.grid.appendChild(
						template.content.firstChild
					);
				}
			} );
		}

		renderEventCard( event ) {
			const cardStyle = this.buildCardStyle();
			const locationParts = [
				event.city,
				event.state,
				event.country,
			].filter( Boolean );
			const location = locationParts.join( ', ' );

			let html =
				'<article class="snap-event-card" style="' +
				cardStyle +
				'">';

			if ( this.config.showImage && event.thumbnail_url ) {
				html +=
					'<div class="snap-event-image">' +
					'<img src="' +
					this.escAttr( event.thumbnail_url ) +
					'" alt="" role="presentation">' +
					'</div>';
			}

			html += '<div class="snap-event-content">';
			html +=
				'<h3 class="snap-event-title" style="color: var(--card-heading-color, #000000);">' +
				'<a href="' +
				this.escAttr( event.permalink ) +
				'" style="color: var(--card-heading-color, #000000);">' +
				this.escHtml( event.title ) +
				'</a></h3>';

			if ( this.config.showDate && event.start_date ) {
				html +=
					'<p class="snap-event-date"><strong>Date:</strong> ';
				html += this.escHtml( event.start_date );
				if (
					event.end_date &&
					event.end_date !== event.start_date
				) {
					html += ' - ' + this.escHtml( event.end_date );
				}
				html += '</p>';
			}

			if ( this.config.showLocation && event.venue ) {
				html +=
					'<p class="snap-event-venue"><strong>Venue:</strong> ' +
					'<span class="snap-event-venue-name">' +
					this.escHtml( event.venue ) +
					'</span></p>';
			}

			if ( this.config.showLocation && location ) {
				html +=
					'<p class="snap-event-location"><strong>Location:</strong> ' +
					'<span class="snap-event-location-text">' +
					this.escHtml( location ) +
					'</span></p>';
			}

			if ( this.config.showExcerpt && event.excerpt ) {
				html +=
					'<div class="snap-event-excerpt">' +
					this.escHtml( event.excerpt ) +
					'</div>';
			}

			html +=
				'<a href="' +
				this.escAttr( event.permalink ) +
				'" class="snap-event-link" ' +
				'style="color: var(--card-link-color, #0073aa);" ' +
				'aria-hidden="true" tabindex="-1">View Event</a>';

			html += '</div></article>';

			return html;
		}

		buildCardStyle() {
			const c = this.config;
			let style =
				'background-color: ' + c.cardBackgroundColor + ';';
			style += ' color: ' + c.cardTextColor + ';';
			style += ' padding: ' + c.cardPadding + 'px;';

			if ( c.cardBorderRadius > 0 ) {
				style +=
					' border-radius: ' + c.cardBorderRadius + 'px;';
			}
			if ( c.cardBoxShadow ) {
				style +=
					' box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);';
			}
			if ( c.cardBorderWidth > 0 ) {
				style +=
					' border: ' +
					c.cardBorderWidth +
					'px solid ' +
					c.cardBorderColor +
					';';
			}

			style +=
				' --card-heading-color: ' + c.cardHeadingColor + ';';
			style += ' --card-link-color: ' + c.cardLinkColor + ';';

			return style;
		}

		updateSortButton() {
			const label = this.sortBtn.querySelector(
				'.snap-events-sort-label'
			);
			if ( label ) {
				label.textContent =
					this.currentOrder === 'ASC'
						? 'Soonest First'
						: 'Furthest Out First';
			}
			this.sortBtn.setAttribute(
				'data-current-order',
				this.currentOrder
			);
		}

		setLoading( loading ) {
			this.isLoading = loading;

			if ( this.loadMoreBtn ) {
				this.loadMoreBtn.disabled = loading;
				const label = this.loadMoreBtn.querySelector(
					'.snap-events-load-more-label'
				);
				if ( label ) {
					label.textContent = loading
						? 'Loading...'
						: 'Load More Events';
				}
			}

			if ( this.sortBtn ) {
				this.sortBtn.disabled = loading;
			}
		}

		updateStatus( message ) {
			if ( this.statusDiv ) {
				this.statusDiv.textContent = message;
				setTimeout( () => {
					this.statusDiv.textContent = '';
				}, 3000 );
			}
		}

		escHtml( text ) {
			const div = document.createElement( 'div' );
			div.textContent = text;
			return div.innerHTML;
		}

		escAttr( text ) {
			return this.escHtml( text )
				.replace( /"/g, '&quot;' )
				.replace( /'/g, '&#039;' );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initEventsGrids );
	} else {
		initEventsGrids();
	}
} )();
