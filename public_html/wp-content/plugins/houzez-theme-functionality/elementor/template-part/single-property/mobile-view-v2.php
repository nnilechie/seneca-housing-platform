<?php global $settings; ?>
<div class="visible-on-mobile">
    <div class="mobile-top-wrap">
        <div class="mobile-property-tools clearfix">
            <?php 
            if( $settings['hide_favorite'] || $settings['hide_social'] || $settings['hide_print']  ) {
                get_template_part('property-details/partials/tools'); 
            }?> 
        </div><!-- mobile-property-tools -->
        <div class="mobile-property-title clearfix">
            <?php 

            get_template_part('template-parts/listing/partials/item-featured-label');

            if( $settings['show_title'] ) {
                get_template_part('property-details/partials/title'); 
            }

            if( $settings['show_address'] ) {
                get_template_part('property-details/partials/item-address');
            } 

            if( $settings['show_price'] ) {
                get_template_part('property-details/partials/item-price'); 
            }

            if( $settings['show_labels'] ) {
                get_template_part('property-details/partials/item-labels-mobile'); 
            }

            ?>
        </div><!-- mobile-property-title -->
    </div><!-- mobile-top-wrap -->
</div>