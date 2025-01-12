( function() {
	const { apiFetch } = wp;

	document.addEventListener( 'DOMContentLoaded', function() {
		const ratingButtons = document.querySelectorAll( '.rating-star a' );
		const messageEl = document.querySelector( '.rating-message' );
		const postId = document.querySelector( 'body' ).classList
			.toString()
			.match( /postid-(\d+)/ )?.[1];

		if ( ! postId ) {
			return;
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
