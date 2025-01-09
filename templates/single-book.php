<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group">
    <!-- wp:group {"layout":{"type":"constrained"}} -->
    <div class="wp-block-group">
        <!-- wp:post-featured-image /-->
        
        <!-- wp:post-title {"level":1,"align":"wide"} /-->
        
        <!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
        <div class="wp-block-group">
            <!-- wp:paragraph {"className":"book-isbn"} -->
            <p class="book-isbn">ISBN: </p>
            <!-- /wp:paragraph -->
            
            <!-- wp:paragraph {
                "metadata":{
                    "bindings":{
                        "content":{
                            "source":"core/post-meta",
                            "args":{
                                "key":"isbn"
                            }
                        }
                    }
                }
            } -->
            <p></p>
            <!-- /wp:paragraph -->
        </div>
        <!-- /wp:group -->
        
        <!-- wp:post-content {"layout":{"inherit":true}} /-->

        <!-- wp:group {"className":"book-rating-stars","layout":{"type":"flex","flexWrap":"nowrap,"justifyContent":"center"}} -->
            <div class="wp-block-group book-rating-stars">
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
        <!-- /wp:group -->
        

    </div>
    <!-- /wp:group -->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /--> 
