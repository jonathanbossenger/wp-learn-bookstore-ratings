<?php
/**
 * REST API Controller for book ratings.
 *
 * @package WP_Learn_Bookstore_Ratings
 */

/**
 * Class WPLBR_REST_Controller
 */
class WPLBR_REST_Controller extends WP_REST_Controller {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = 'wp-learn-bookstore-ratings/v1';
		$this->rest_base = 'books/(?P<book_id>[\d]+)/ratings';
	}

	/**
	 * Register routes.
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_rating' ),
					'permission_callback' => '__return_true',
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_rating' ),
					'permission_callback' => array( $this, 'create_rating_permissions_check' ),
					'args'                => array(
						'rating' => array(
							'required'          => true,
							'type'              => 'integer',
							'minimum'           => 1,
							'maximum'           => 5,
							'sanitize_callback' => 'absint',
						),
					),
				),
			)
		);
	}

	/**
	 * Get average rating for a book.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public function get_rating( $request ) {
		global $wpdb;

		$book_id    = $request['book_id'];
		$table_name = $wpdb->prefix . 'bookstore_ratings';

		$average = $wpdb->get_var(
			$wpdb->prepare(
				'SELECT ROUND(AVG(rating), 1) FROM ' . $wpdb->prefix . 'bookstore_ratings WHERE book_id = %d',
				$book_id
			)
		);

		return rest_ensure_response( array( 'rating' => (float) $average ) );
	}

	/**
	 * Create or update a rating.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return WP_REST_Response|WP_Error Response object or error.
	 */
	public function create_rating( $request ) {
		global $wpdb;

		$book_id    = $request['book_id'];
		$user_id    = get_current_user_id();
		$rating     = $request['rating'];
		$table_name = $wpdb->prefix . 'bookstore_ratings';

		// Update existing rating or insert new one.
		$result = $wpdb->replace(
			$table_name,
			array(
				'book_id' => $book_id,
				'user_id' => $user_id,
				'rating'  => $rating,
			),
			array( '%d', '%d', '%d' )
		);

		if ( false === $result ) {
			return new WP_Error(
				'rating_error',
				__( 'Could not save rating.', 'wp-learn-bookstore-ratings' ),
				array( 'status' => 500 )
			);
		}

		return rest_ensure_response( array( 'success' => true ) );
	}

	/**
	 * Check if user can create a rating.
	 *
	 * @param WP_REST_Request $request Request object.
	 * @return bool|WP_Error True if user can create, WP_Error otherwise.
	 */
	public function create_rating_permissions_check( $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'rest_forbidden',
				__( 'Sorry, you must be logged in to rate books.', 'wp-learn-bookstore-ratings' ),
				array( 'status' => rest_authorization_required_code() )
			);
		}

		return true;
	}
}
