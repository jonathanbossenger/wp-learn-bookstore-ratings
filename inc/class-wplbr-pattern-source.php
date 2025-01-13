<?php
/**
 * Pattern source for ratings UI.
 *
 * @package WP_Learn_Bookstore_Ratings
 */

/**
 * Class WPLBR_Pattern_Source
 */
class WPLBR_Pattern_Source {

	/**
	 * Get the rating UI pattern if user is logged in.
	 *
	 * @return string The pattern content or empty string.
	 */
	public static function get_rating_ui() {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		return '<!-- wp:group {"className":"book-rating-stars","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
		<div class="wp-block-group book-rating-stars">
			<!-- wp:paragraph {"className":"average-rating"} -->
			<p class="average-rating">Average rating: 0 ★</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"rating-star"} -->
				<div class="wp-block-button rating-star"><a class="wp-block-button__link wp-element-button" href="#"><span data-rating="1">★</span></a></div>
				<!-- /wp:button -->
				<!-- wp:button {"className":"rating-star"} -->
				<div class="wp-block-button rating-star"><a class="wp-block-button__link wp-element-button" href="#"><span data-rating="2">★</span></a></div>
				<!-- /wp:button -->
				<!-- wp:button {"className":"rating-star"} -->
				<div class="wp-block-button rating-star"><a class="wp-block-button__link wp-element-button" href="#"><span data-rating="3">★</span></a></div>
				<!-- /wp:button -->
				<!-- wp:button {"className":"rating-star"} -->
				<div class="wp-block-button rating-star"><a class="wp-block-button__link wp-element-button" href="#"><span data-rating="4">★</span></a></div>
				<!-- /wp:button -->
				<!-- wp:button {"className":"rating-star"} -->
				<div class="wp-block-button rating-star"><a class="wp-block-button__link wp-element-button" href="#"><span data-rating="5">★</span></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
			<!-- wp:paragraph {"className":"rating-message","style":{"display":"none"}} -->
			<p class="rating-message"></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->';
	}
} 
