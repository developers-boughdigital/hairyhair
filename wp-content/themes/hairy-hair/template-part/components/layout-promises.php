<?php
/**
 * Component: Promises Bar
 *
 * ACF Flexible Content layout name: promises
 *
 * Sub-fields:
 *   title     (text)       — section heading
 *   promises  (repeater)   — list of promise items
 *     └ icon  (image)      — icon image
 *     └ title (text)       — promise label
 */

$section_title = get_sub_field( 'title' );
$promises      = get_sub_field( 'promises' );
?>

<section class="promises">
    <div class="container">
        <div class="bg-blue-3d">

            <?php if ( $section_title ) : ?>
                <div class="global-title">
                    <h2><?php echo esc_html( $section_title ); ?></h2>
                </div>
            <?php endif; ?>

            <?php if ( $promises ) : ?>
                <div class="all-promises">
                    <?php foreach ( $promises as $promise ) :
                        $icon  = $promise['icon']  ?? null;
                        $label = $promise['title'] ?? '';
                    ?>
                    <div class="each-promise">
                        <?php if ( $icon ) : ?>
                            <img src="<?php echo esc_url( $icon['url'] ); ?>"
                                 alt="<?php echo esc_attr( $icon['alt'] ); ?>" />
                        <?php endif; ?>
                        <?php if ( $label ) : ?>
                            <p><?php echo esc_html( $label ); ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>
