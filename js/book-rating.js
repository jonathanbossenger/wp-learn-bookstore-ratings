( function() {
	const { apiFetch } = wp;

	document.addEventListener( 'DOMContentLoaded', function() {
		const ratingStars = document.querySelectorAll( '.book-rating-stars .rating-star a' );
		const postId = document.body.classList
			.toString()
			.match( /postid-(\d+)/ )?.[1];

		if ( ! postId ) {
			return;
		}

		ratingStars.forEach( ( star ) => {
			star.addEventListener( 'click', async function( event ) {
				event.preventDefault();
				const rating = parseInt( this.dataset.rating, 10 );

				try {
					const response = await apiFetch( {
						path: `/wp-learn-bookstore/v1/books/${postId}/ratings`,
						method: 'POST',
						data: { rating },
					} );

					// Highlight stars up to the selected rating
					ratingStars.forEach( ( s ) => {
						const starRating = parseInt( s.dataset.rating, 10 );
						s.classList.toggle( 'rated', starRating <= rating );
					} );

					// Optional: Show success message
					console.log( 'Rating saved:', response );
				} catch ( error ) {
					console.error( 'Failed to save rating:', error );
				}
			} );
		} );
	} );
} )(); 