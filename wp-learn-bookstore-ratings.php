<?php
/**
 * Plugin Name: Bookstore & Ratings
 * Description: A bookstore management and rating plugin
 * Version: 1.0.0
 * Author: Jonathan Bossenger
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-learn-bookstore-ratings
 * Domain Path: /languages
 *
 * @package WP_Learn_Bookstore
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin constants.
define( 'WP_LEARN_BOOKSTORE_VERSION', '1.0.0' );
define( 'WP_LEARN_BOOKSTORE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_LEARN_BOOKSTORE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Get the contents of a template file.
 *
 * @param string $template The template file name.
 * @return string The template content.
 */
function wp_learn_get_template_content( $template ) {
	ob_start();
	include $template;
	return ob_get_clean();
}

// Plugin activation hook.
register_activation_hook( __FILE__, 'wp_learn_bookstore_activate' );

/**
 * Plugin activation function
 *
 * @return void
 */
function wp_learn_bookstore_activate() {
	// Create a custom table to store book ratings.
	global $wpdb;
	$table_name      = $wpdb->prefix . 'book_ratings';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		book_id bigint(20) NOT NULL,
		user_id bigint(20) NOT NULL,
		rating tinyint(1) NOT NULL,
		created_at datetime DEFAULT CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
		KEY book_id (book_id),
		KEY user_id (user_id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql );
}

add_action( 'wp_enqueue_scripts', 'wp_learn_bookstore_enqueue_scripts' );
/**
 * Enqueue scripts and styles.
 *
 * @return void
 */
function wp_learn_bookstore_enqueue_scripts() {
	if ( is_singular( 'book' ) ) {
		wp_enqueue_script(
			'wp-learn-bookstore-ratings',
			plugins_url( 'js/book-rating.js', __FILE__ ),
			array( 'wp-api-fetch' ),
			WP_LEARN_BOOKSTORE_VERSION,
			true
		);

		wp_enqueue_style(
			'wp-learn-bookstore-ratings',
			plugins_url( 'css/book-rating.css', __FILE__ ),
			array(),
			WP_LEARN_BOOKSTORE_VERSION
		);
	}
}

add_action( 'init', 'wp_learn_bookstore_init' );
/**
 * Initialize plugin
 *
 * @return void
 */
function wp_learn_bookstore_init() {
	// Register the book custom post type.
	register_post_type(
		'book',
		array(
			'labels'       => array(
				'name'          => __( 'Books', 'wp-learn-bookstore-ratings' ),
				'singular_name' => __( 'Book', 'wp-learn-bookstore-ratings' ),
				'add_new'       => __( 'Add New Book', 'wp-learn-bookstore-ratings' ),
				'add_new_item'  => __( 'Add New Book', 'wp-learn-bookstore-ratings' ),
				'edit_item'     => __( 'Edit Book', 'wp-learn-bookstore-ratings' ),
				'view_item'     => __( 'View Book', 'wp-learn-bookstore-ratings' ),
				'all_items'     => __( 'All Books', 'wp-learn-bookstore-ratings' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'books' ),
			'show_in_rest' => true, // Enable Gutenberg editor.
			'supports'     => array(
				'title',
				'editor',
				'thumbnail',
				'excerpt',
				'custom-fields',
				'revisions',
			),
			'menu_icon'    => 'dashicons-book-alt',
		)
	);
}

add_action( 'init', 'wp_learn_bookstore_register_meta' );
/**
 * Register custom meta fields for books.
 *
 * @return void
 */
function wp_learn_bookstore_register_meta() {
	register_post_meta(
		'book',
		'isbn',
		array(
			'type'         => 'string',
			'description'  => __( 'Book ISBN number', 'wp-learn-bookstore-ratings' ),
			'single'       => true,
			'show_in_rest' => true,
		)
	);
}

add_filter( 'postmeta_form_keys', 'wp_learn_bookstore_add_isbn_to_dropdown', 10, 2 );
/**
 * Add ISBN to custom fields dropdown.
 *
 * @param array  $keys List of meta keys.
 * @param object $post Current post object.
 * @return array
 */
function wp_learn_bookstore_add_isbn_to_dropdown( $keys, $post ) {
	if ( 'book' === $post->post_type ) {
		$keys[] = 'isbn';
	}
	return $keys;
}

add_action( 'init', 'wp_learn_bookstore_register_templates' );
/**
 * Register block templates for the book post type
 */
function wp_learn_bookstore_register_templates() {
	$template_file = WP_LEARN_BOOKSTORE_PLUGIN_DIR . 'templates/single-book.php';

	if ( ! file_exists( $template_file ) ) {
		return;
	}

	register_block_template(
		'wp-learn-bookstore-ratings//single-book',
		array(
			'title'       => __( 'Single Book', 'wp-learn-bookstore-ratings' ),
			'description' => __( 'Template for displaying a single book.', 'wp-learn-bookstore-ratings' ),
			'content'     => wp_learn_get_template_content( $template_file ),
		)
	);
}

add_action( 'rest_api_init', 'wp_learn_bookstore_register_rest_routes' );
/**
 * Register REST API routes for book ratings.
 *
 * @return void
 */
function wp_learn_bookstore_register_rest_routes() {
	register_rest_route(
		'wp-learn-bookstore-ratings/v1',
		'/books/(?P<book_id>\d+)/ratings',
		array(
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'wp_learn_bookstore_get_book_ratings',
				'permission_callback' => '__return_true',
			),
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'wp_learn_bookstore_create_book_rating',
				'permission_callback' => 'wp_learn_bookstore_can_create_rating',
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
 * Check if user can create book ratings.
 *
 * @return bool
 */
function wp_learn_bookstore_can_create_rating() {
	return current_user_can( 'edit_posts' );
}

/**
 * Get ratings for a specific book.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response|WP_Error
 */
function wp_learn_bookstore_get_book_ratings( $request ) {
	global $wpdb;
	$book_id = $request['book_id'];

	if ( 'book' !== get_post_type( $book_id ) ) {
		return new WP_Error(
			'invalid_book_id',
			__( 'Invalid book ID.', 'wp-learn-bookstore-ratings' ),
			array( 'status' => 404 )
		);
	}

	$ratings = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}book_ratings WHERE book_id = %d ORDER BY created_at DESC",
			$book_id
		)
	);

	return rest_ensure_response( $ratings );
}

/**
 * Create a new rating for a book.
 *
 * @param WP_REST_Request $request Request object.
 * @return WP_REST_Response|WP_Error
 */
function wp_learn_bookstore_create_book_rating( $request ) {
	global $wpdb;
	$book_id = $request['book_id'];
	$rating  = $request['rating'];

	if ( 'book' !== get_post_type( $book_id ) ) {
		return new WP_Error(
			'invalid_book_id',
			__( 'Invalid book ID.', 'wp-learn-bookstore-ratings' ),
			array( 'status' => 404 )
		);
	}

	$result = $wpdb->insert(
		$wpdb->prefix . 'book_ratings',
		array(
			'book_id'    => $book_id,
			'user_id'    => get_current_user_id(),
			'rating'     => $rating,
			'created_at' => current_time( 'mysql' ),
		),
		array( '%d', '%d', '%d', '%s' )
	);

	if ( false === $result ) {
		return new WP_Error(
			'rating_creation_failed',
			__( 'Failed to create rating.', 'wp-learn-bookstore-ratings' ),
			array( 'status' => 500 )
		);
	}

	$new_rating = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}book_ratings WHERE id = %d",
			$wpdb->insert_id
		)
	);

	return rest_ensure_response( $new_rating );
}

add_action( 'init', 'wp_learn_bookstore_register_block_patterns' );
/**
 * Register block patterns for book ratings.
 *
 * @return void
 */
function wp_learn_bookstore_register_block_patterns() {
	register_block_pattern(
		'wp-learn-bookstore-ratings/book-rating',
		array(
			'title'       => __( 'Book Rating Stars', 'wp-learn-bookstore-ratings' ),
			'description' => __( 'Displays clickable star ratings for books', 'wp-learn-bookstore-ratings' ),
			'content'     => '
				<!-- wp:group {"className":"book-rating-stars","layout":{"type":"flex","flexWrap":"nowrap,"justifyContent":"center"}} -->
				<div class="wp-block-group book-rating-stars">
					<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
					<div class="wp-block-buttons">
						<!-- wp:button {"className":"rating-star"} -->
						<div class="wp-block-button rating-star"><a data-rating="1" class="wp-block-button__link wp-element-button" href="#">★</a></div>
						<!-- /wp:button -->
						<!-- wp:button {"className":"rating-star"} -->
						<div class="wp-block-button rating-star"><a class="wp-block-button__link wp-element-button" href="#">★</a></div>
						<!-- /wp:button -->
						<!-- wp:button {"className":"rating-star"} -->
						<div class="wp-block-button rating-star"><a class="wp-block-button__link wp-element-button" href="#">★</a></div>
						<!-- /wp:button -->
						<!-- wp:button {"className":"rating-star"} -->
						<div class="wp-block-button rating-star"><a class="wp-block-button__link wp-element-button" href="#">★</a></div>
						<!-- /wp:button -->
						<!-- wp:button {"className":"rating-star"} -->
						<div class="wp-block-button rating-star"><a class="wp-block-button__link wp-element-button" href="#">★</a></div>
						<!-- /wp:button -->
					</div>
					<!-- /wp:buttons -->
				</div>
				<!-- /wp:group -->
			',
			'categories'  => array( 'uncategorized' ),
			'keywords'    => array( 'rating', 'stars', 'book' ),
		)
	);
}
