<?php
/**
 * Plugin Name: Bookstore & Ratings
 * Description: A bookstore management plugin
 * Version: 1.0.0
 * Author: Jonathan Bossenger
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-learn-bookstore
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
	include __DIR__ . "/templates/{$template}";
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

// Plugin deactivation hook.
register_deactivation_hook( __FILE__, 'wp_learn_bookstore_deactivate' );

/**
 * Plugin deactivation function
 *
 * @return void
 */
function wp_learn_bookstore_deactivate() {
	// Deactivation code here.
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
				'name'          => __( 'Books', 'wp-learn-bookstore' ),
				'singular_name' => __( 'Book', 'wp-learn-bookstore' ),
				'add_new'       => __( 'Add New Book', 'wp-learn-bookstore' ),
				'add_new_item'  => __( 'Add New Book', 'wp-learn-bookstore' ),
				'edit_item'     => __( 'Edit Book', 'wp-learn-bookstore' ),
				'view_item'     => __( 'View Book', 'wp-learn-bookstore' ),
				'all_items'     => __( 'All Books', 'wp-learn-bookstore' ),
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
			'description'  => __( 'Book ISBN number', 'wp-learn-bookstore' ),
			'single'       => true,
			'show_in_rest' => true,
		)
	);
}

add_action( 'init', 'wp_learn_bookstore_register_templates' );

/**
 * Register block templates for the book post type
 */
function wp_learn_bookstore_register_templates() {
	$template_file = WP_LEARN_BOOKSTORE_PLUGIN_DIR . 'templates/single-book.html';

	if ( ! file_exists( $template_file ) ) {
		return;
	}

	register_block_template(
		'wp-learn-bookstore//single-book',
		array(
			'title'       => __( 'Single Book', 'wp-learn-bookstore' ),
			'description' => __( 'Template for displaying a single book.', 'wp-learn-bookstore' ),
			'content'     => wp_learn_get_template_content( $template_file ),
		)
	);
}
