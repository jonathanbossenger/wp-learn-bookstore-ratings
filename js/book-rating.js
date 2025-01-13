( function() {
	const { apiFetch } = wp;

	document.addEventListener( 'DOMContentLoaded', async function() {
		const ratingButtons = document.querySelectorAll( '.rating-buttons .wp-block-button button' );
		const messageEl = document.querySelector( '.rating-message' );
		const averageRatingEl = document.querySelector( '.average-rating' );
		const postId = document.querySelector( 'body' ).classList
			.toString()
			.match( /postid-(\d+)/ )?.[1];

		if ( ! postId ) {
			return;
		}

		// Fetch and display the average rating
		try {
			const response = await apiFetch( {
				path: `/wp-learn-bookstore-ratings/v1/books/${postId}/ratings`,
				method: 'GET'
			} );

			if ( response && response.average_rating ) {
				averageRatingEl.textContent = `Average rating: ${response.average_rating} ★`;
				averageRatingEl.style.display = 'block';
			}
		} catch ( error ) {
			console.error( 'Error fetching average rating:', error );
		}

		ratingButtons.forEach( button => {
			button.addEventListener( 'click', async function( e ) {
				e.preventDefault();
				const ratingSpan = this.querySelector( 'span' );
				const rating = parseInt( ratingSpan.dataset.rating );

				messageEl.textContent = 'Saving your rating...';
				messageEl.style.display = 'block';
				messageEl.classList.add( 'saving' );

				try {
					const response = await apiFetch( {
						path: `/wp-learn-bookstore-ratings/v1/books/${postId}/ratings`,
						method: 'POST',
						data: { rating }
					} );

					if ( response ) {
						// Update the average rating display if it's returned in the response
						if ( response.average_rating ) {
							averageRatingEl.textContent = `Average rating: ${response.average_rating} ★`;
							averageRatingEl.style.display = 'block';
						}

						ratingButtons.forEach( btn => {
							const btnSpan = btn.querySelector( 'span' );
							const btnRating = parseInt( btnSpan.dataset.rating );
							if ( btnRating <= rating ) {
								btn.classList.add( 'rated' );
							} else {
								btn.classList.remove( 'rated' );
							}
						} );

						messageEl.classList.remove( 'saving' );
						messageEl.textContent = `Thanks! You rated this book ${rating} stars.`;
						messageEl.classList.add( 'success' );
					}
				} catch ( error ) {
					console.error( 'Error submitting rating:', error );
					messageEl.classList.remove( 'saving' );
					messageEl.textContent = 'Sorry, there was an error saving your rating. Please try again.';
					messageEl.classList.add( 'error' );
				}
			} );
		} );
	} );
} )(); 
