<!-- wp:template-part {"slug":"header","area":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main"} -->
<main class="wp-block-group">
    <!-- wp:group {"layout":{"type":"constrained"}} -->
    <div class="wp-block-group">
        <!-- wp:post-featured-image {"align":"wide"} /-->
        
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
         
    </div>
    <!-- /wp:group -->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","area":"footer","tagName":"footer"} /--> 
