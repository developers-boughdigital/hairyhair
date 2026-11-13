<?php
/**
 * ACF Flexible Content dispatcher.
 * Loops "page_components" and "dual_gender_card_sections" and loads the matching layout template.
 *
 * Usage: get_template_part( 'template-part/components/flexible-content' );
 */

if ( function_exists( 'have_rows' ) && have_rows( 'page_components' ) ) {
    while ( have_rows( 'page_components' ) ) :
        the_row();
        // e.g. layout name "hero_banner" → loads template-part/components/layout-hero_banner.php
        get_template_part( 'template-part/components/layout', get_row_layout() );
    endwhile;
}
