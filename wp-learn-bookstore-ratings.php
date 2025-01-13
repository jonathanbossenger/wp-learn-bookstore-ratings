<?php
/**
 * Plugin Name: WP Learn Bookstore Ratings
 * Plugin URI: https://example.com/wp-learn-bookstore-ratings
 * Description: A plugin to manage book ratings for the WP Learn Bookstore.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 7.4
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-learn-bookstore-ratings
 * Domain Path: /languages
 *
 * @package WP_Learn_Bookstore_Ratings
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_activation_hook( __FILE__, 'wplbr_activate' );
/**
 * Plugin activation hook.
 *
 * @return void
 */
function wplbr_activate() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . 'bookstore_ratings';

	$sql = "CREATE TABLE $table_name (
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

	// Flush rewrite rules.
	flush_rewrite_rules();
}

register_deactivation_hook( __FILE__, 'wplbr_deactivate' );
/**
 * Plugin deactivation hook.
 *
 * @return void
 */
function wplbr_deactivate() {
	// Flush rewrite rules.
	flush_rewrite_rules();
}

add_action( 'init', 'wplbr_register_book_post_type' );
/**
 * Register the 'book' custom post type.
 *
 * @return void
 */
function wplbr_register_book_post_type() {
	$labels = array(
		'name'          => _x( 'Books', 'post type general name', 'wp-learn-bookstore-ratings' ),
		'singular_name' => _x( 'Book', 'post type singular name', 'wp-learn-bookstore-ratings' ),
	);

	$args = array(
		'labels'          => $labels,
		'public'          => true,
		'show_in_rest'    => true,
		'supports'        => array(
			'title',
			'editor',
			'thumbnail',
			'custom-fields',
		),
	);

	register_post_type( 'book', $args );
}

add_filter( 'postmeta_form_keys', 'wplbr_register_book_custom_field_key', 10, 2 );
/**
 * Add ISBN to the list of available custom fields.
 *
 * @param array $keys    Array of existing custom field keys.
 * @param int   $post_id Post ID.
 * @return array Modified array of custom field keys.
 */
function wplbr_register_book_custom_field_key( $keys, $post_id ) {
	if ( 'book' === get_post_type( $post_id ) ) {
		if ( ! in_array( 'wplb_isbn', $keys, true ) ) {
			$keys[] = 'wplb_isbn';
		}
	}
	return $keys;
}

add_action( 'init', 'wplbr_register_book_meta' );
/**
 * Register book meta fields for REST API access.
 *
 * @return void
 */
function wplbr_register_book_meta() {
	register_post_meta(
		'book',
		'wplb_isbn',
		array(
			'type'              => 'string',
			'description'       => __( 'ISBN number for the book', 'wp-learn-bookstore-ratings' ),
			'single'            => true,
			'sanitize_callback' => 'sanitize_text_field',
			'show_in_rest'      => true,
			'auth_callback'     => 'wplbr_can_edit_books',
		)
	);
}

/**
 * Check if user can edit books.
 *
 * @return bool Whether the current user can edit posts.
 */
function wplbr_can_edit_books() {
	return current_user_can( 'edit_posts' );
}

add_action( 'init', 'wplbr_register_book_template' );
/**
 * Register block template for single book display.
 *
 * @return void
 */
function wplbr_register_book_template() {
	register_block_template(
		'wp-learn-bookstore-ratings//single-book',
		array(
			'title'      => __( 'Single Book', 'wp-learn-bookstore-ratings' ),
			'post_types' => array( 'book' ),
			'content'    => file_get_contents( plugin_dir_path( __FILE__ ) . 'templates/single-book.html' ),
		)
	);
}

add_action( 'rest_api_init', 'wplbr_register_rest_routes' );
/**
 * Register REST API routes.
 *
 * @return void
 */
function wplbr_register_rest_routes() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-wplbr-rest-controller.php';
	$controller = new WPLBR_REST_Controller();
	$controller->register_routes();
}

add_action( 'wp_enqueue_scripts', 'wplbr_enqueue_assets' );
/**
 * Enqueue plugin assets.
 *
 * @return void
 */
function wplbr_enqueue_assets() {
	if ( is_singular( 'book' ) && is_user_logged_in() ) {
		wp_enqueue_style(
			'wplbr-rating',
			plugins_url( 'css/book-rating.css', __FILE__ ),
			array(),
			filemtime( plugin_dir_path( __FILE__ ) . 'css/book-rating.css' )
		);

		wp_enqueue_script(
			'wplbr-rating',
			plugins_url( 'js/book-rating.js', __FILE__ ),
			array( 'wp-api-fetch' ),
			filemtime( plugin_dir_path( __FILE__ ) . 'js/book-rating.js' ),
			true
		);
	}
}

add_action( 'init', 'wplbr_register_pattern_source' );
/**
 * Register pattern source for ratings UI.
 *
 * @return void
 */
function wplbr_register_pattern_source() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-wplbr-pattern-source.php';
	register_block_pattern_source(
		'wp-learn-bookstore-ratings/rating-ui',
		array(
			'label'    => __( 'Rating UI', 'wp-learn-bookstore-ratings' ),
			'callback' => array( 'WPLBR_Pattern_Source', 'get_rating_ui' ),
		)
	);
} 

