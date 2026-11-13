<?php
/**
 * inc/faq-schema.php
 * Outputs FAQPage JSON-LD only on the faq.php template when 
 * ACF 'faq' repeater has rows.
 */

function mytheme_has_faqs() {
    return function_exists('get_field') && ! empty( get_field('faq') );
}

function mytheme_output_faq_schema() {
    // Bail unless:
    // 1) We're on the faq.php template
    // 2) We actually have FAQ rows
    if ( ! is_page_template( 'faq.php' ) || ! mytheme_has_faqs() ) {
        return;
    }

    $faqs = get_field( 'faq' );
    $schema = [
        '@context'   => 'https://schema.org',
        '@type'      => 'FAQPage',
        'mainEntity' => [],
    ];

    foreach ( $faqs as $section ) {
        if ( ! empty( $section['faq_question_answer'] ) ) {
            foreach ( $section['faq_question_answer'] as $qa ) {
                $q = trim( $qa['question'] ?? '' );
                $a = trim( $qa['answer']   ?? '' );
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

    echo '<script type="application/ld+json">'
         . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
         . '</script>';
}
add_action( 'wp_head', 'mytheme_output_faq_schema', 99 );