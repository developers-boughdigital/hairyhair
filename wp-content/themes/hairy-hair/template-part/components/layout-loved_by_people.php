<?php
/**
 * Flexible Content Layout: Loved by People
 * Sub-fields: title, testimonial (post_object, multiple, post_type: review)
 */

$title        = get_sub_field( 'title' ) ?: 'Loved by people';
$testimonials = get_sub_field( 'testimonial' );

if ( ! $testimonials ) {
    return;
}

// Delegate to the shared template
set_query_var( 'hh_lbp_title', $title );
set_query_var( 'hh_lbp_testimonials', $testimonials );
include locate_template( 'template-part/loved-by-people.php' );
