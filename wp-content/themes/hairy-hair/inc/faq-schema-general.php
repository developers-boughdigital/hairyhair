<?php
/**
 * inc/faq-schema.php
 * Outputs FAQPage JSON-LD whenever the ACF 'faq' repeater has rows.
 */

if ( ! function_exists( 'mytheme_output_faq_schema' ) ) :

function mytheme_output_faq_schema() {
    // Bail early if ACF repeater 'faq' has no rows
    if ( ! function_exists( 'have_rows' ) || ! have_rows( 'faq' ) ) {
        return;
    }

    $schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => [],
    ];

    // Loop through each FAQ “section”
    while ( have_rows( 'faq' ) ) {
        the_row();

        // Optional: use section title if needed
        // $section_title = get_sub_field( 'title' );

        // Loop through the nested question/answer repeater
        if ( have_rows( 'faq_question_answer' ) ) {
            while ( have_rows( 'faq_question_answer' ) ) {
                the_row();

                $q = trim( get_sub_field( 'question' ) );
                $a = trim( get_sub_field( 'answer' ) );

                if ( $q && $a ) {
                    $schema['mainEntity'][] = [
                        '@type'          => 'Question',
                        'name'           => wp_strip_all_tags( $q ),
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text'  => wp_kses_post( $a ),
                        ],
                    ];
                }
            }
        }
    }

    // Output the JSON-LD
    echo '<script type="application/ld+json">'
       . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
       . '</script>';
}
add_action( 'wp_head', 'mytheme_output_faq_schema', 99 );

endif;
