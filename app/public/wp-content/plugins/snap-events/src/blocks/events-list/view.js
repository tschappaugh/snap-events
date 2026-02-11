/**
 * Events List - Frontend Interactivity
 *
 * Handles Load More and Sort Toggle functionality.
 * Each block instance on the page gets its own controller.
 */
( function () {
	'use strict';

	function initEventsLists() {
		const lists = document.querySelectorAll(
			'.snap-events-list[data-config]'
		);

		lists.forEach( ( list ) => {
			new EventsListController( list );
		} );
	}

	class EventsListController {
		constructor( listElement ) {
			this.list = listElement;
			this.config = JSON.parse(
				listElement.getAttribute( 'data-config' )
			);
			this.currentPage = 1;
			this.currentOrder = this.config.defaultSortOrder;
			this.isLoading = false;

			this.controls = this.list.querySelector(
				'.snap-events-controls'
			);
			this.loadMoreBtn = this.list.querySelector(
				'.snap-events-load-more'
			);
			this.sortBtn = this.list.querySelector(
				'.snap-events-sort-toggle'
			);
			this.statusDiv = this.list.querySelector(
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

				this.list
					.querySelectorAll( '.snap-event-list-item' )
					.forEach( ( item ) => item.remove() );

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

			const response = await fetch( url.toString(), {
				method: 'GET',
				headers: {
					'X-WP-Nonce': this.config.restNonce,
				},
			} );

			if ( ! response.ok ) {
				throw new Error( 'HTTP error: ' + response.status );
			}

			return await response.json();
		}

		appendEvents( events ) {
			events.forEach( ( event ) => {
				const itemHtml = this.renderListItem( event );
				const template = document.createElement( 'template' );
				template.innerHTML = itemHtml.trim();

				if ( this.controls ) {
					this.controls.before( template.content.firstChild );
				} else {
					this.list.appendChild(
						template.content.firstChild
					);
				}
			} );
		}

		renderListItem( event ) {
			const c = this.config;
			const itemStyle =
				'border-bottom: ' + c.borderWidth + 'px solid ' +
				c.borderColor + '; padding: ' + c.itemPadding + 'px 0;';

			const locationParts = [
				event.city,
				event.state,
				event.country,
			].filter( Boolean );
			const location = locationParts.join( ', ' );

			let html =
				'<div class="snap-event-list-item" style="' +
				itemStyle + '">';

			if ( c.showImage && event.thumbnail_url ) {
				html +=
					'<div class="snap-event-list-image">' +
					'<img src="' +
					this.escAttr( event.thumbnail_url ) +
					'" alt="" role="presentation">' +
					'</div>';
			}

			html += '<div class="snap-event-list-content">';
			html +=
				'<h3 class="snap-event-title" style="color: var(--list-heading-color, #000000);">' +
				'<a href="' +
				this.escAttr( event.permalink ) +
				'" style="color: var(--list-heading-color, #000000);">' +
				this.escHtml( event.title ) +
				'</a></h3>';

			if ( c.showDate && event.start_date ) {
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

			if ( c.showLocation && event.venue ) {
				html +=
					'<p class="snap-event-venue"><strong>Venue:</strong> ' +
					'<span class="snap-event-venue-name">' +
					this.escHtml( event.venue ) +
					'</span></p>';
			}

			if ( c.showLocation && location ) {
				html +=
					'<p class="snap-event-location"><strong>Location:</strong> ' +
					'<span class="snap-event-location-text">' +
					this.escHtml( location ) +
					'</span></p>';
			}

			if ( c.showExcerpt && event.excerpt ) {
				html +=
					'<div class="snap-event-excerpt">' +
					event.excerpt +
					'</div>';
			}

			html +=
				'<a href="' +
				this.escAttr( event.permalink ) +
				'" class="snap-event-link" ' +
				'style="color: var(--list-link-color, #0073aa);" ' +
				'aria-hidden="true" tabindex="-1">View Event</a>';

			html += '</div></div>';

			return html;
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
		document.addEventListener( 'DOMContentLoaded', initEventsLists );
	} else {
		initEventsLists();
	}
} )();
