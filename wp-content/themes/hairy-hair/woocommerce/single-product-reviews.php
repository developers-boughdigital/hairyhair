<?php
defined( 'ABSPATH' ) || exit;
global $product;

$post_id = $product->get_id();
$avg     = wc_format_decimal( $product->get_average_rating(), 1 );
$count   = $product->get_review_count();

// Build the image/video gallery
$all = get_comments( [ 'post_id'=>$post_id, 'status'=>'approve', 'type'=>'review' ] );
$ids = [];
foreach ( $all as $c ) {
    $m = get_comment_meta( $c->comment_ID, 'review_attachments', true );
    if ( is_array( $m ) ) {
        $ids = array_merge( $ids, $m );
    }
}
$gallery = array_slice( $ids, 0, 12 );
?>
<section class="cust-reviews">
  <div class="container">
    <!-- Title & Avg Rating & Button -->
    <div class="global-title d-flex justify-content-between align-items-center">
      <div>
        <h2><?php esc_html_e( 'Customer Reviews', 'yourtheme' ); ?></h2>
        <div class="rating-review">
          <div class="star-icon">
            <?php for ( $i=1; $i<=5; $i++ ):
              $full = ( $i <= round( $avg ) );
              $star = get_template_directory_uri() . '/assets/rating-star' . ( $full ? '' : '-empty' ) . '.png';
            ?>
              <img src="<?php echo esc_url( $star ); ?>" alt="" />
            <?php endfor; ?>
          </div>
          <p>
            <?php echo esc_html( $avg ); ?>
            <span>(<?php echo esc_html( $count ); ?> <?php esc_html_e( 'Review', 'yourtheme' ); ?><?php echo $count>1?'s':''; ?>)</span>
          </p>
        </div>
      </div>
      <a href="#review_form_wrapper" class="btn dark-btn">
        <span><?php esc_html_e( 'Write A Review', 'yourtheme' ); ?></span>
      </a>
    </div>

    <!-- Gallery -->
    <?php if ( $gallery ): ?>
    <div class="gallary-wrapper">
      <div class="d-flex justify-content-between gallery-title">
        <h6><?php esc_html_e( 'Image & Video Gallery', 'yourtheme' ); ?></h6>
        <!-- <a href="#all-reviews"><?php esc_html_e( 'see all', 'yourtheme' ); ?></a> -->
      </div>
      <div class="gallry-slider">
        <?php foreach ( $gallery as $aid ):
          $url  = wp_get_attachment_url( $aid );
          $mime = get_post_mime_type( $aid );
          if ( 0 === strpos( $mime, 'video/' ) ): ?>
            <div><video src="<?php echo esc_url( $url ); ?>" controls></video></div>
          <?php else: ?>
            <div><img src="<?php echo esc_url( $url ); ?>" alt="" /></div>
          <?php endif;
        endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Reviews + AJAX Sort -->
    <div class="reviews-container" id="all-reviews">
    <?php if ( $product->get_review_count() > 0 ) : ?>
  <div class="reviews-header">
    <label for="sort"><?php esc_html_e( 'Sort by', 'yourtheme' ); ?></label>
    <select id="sort">
      <option value="recent"><?php esc_html_e( 'Most Recent', 'yourtheme' ); ?></option>
      <option value="high"><?php esc_html_e( 'Highest Rated', 'yourtheme' ); ?></option>
      <option value="low"><?php esc_html_e( 'Lowest Rated', 'yourtheme' ); ?></option>
    </select>
  </div>
<?php endif; ?>
      <!-- AJAX‐replaceable reviews -->
      <div id="reviews-list">
        <?php echo mytheme_get_reviews_html( $post_id, 'recent', 1 ); ?>
      </div>

      <!-- Review Form (hidden by default) -->
      <?php
  // right before the closing of your reviews-container
  $commenter = wp_get_current_commenter();
  $form_args = [
    'id_form'             => 'review_form_wrapper',
    'title_reply'         => have_comments()
                              ? __( 'Add your review', 'yourtheme' )
                              : sprintf( __( 'Be the first to review “%s”', 'yourtheme' ), get_the_title() ),
    'label_submit'        => __( 'Submit Review', 'yourtheme' ),
    'class_submit'        => 'btn dark-btn',
    'logged_in_as'        => '',
    'comment_notes_before'=> '',
    'comment_notes_after' => '',
    // Name & Email
    'fields' => [
      'author' => '<p class="comment-form-author">
                     <label for="author">' . __( 'Name', 'yourtheme' ) . ' <span class="required">*</span></label>
                     <input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" required />
                   </p>',
      'email'  => '<p class="comment-form-email">
                     <label for="email">' . __( 'Email', 'yourtheme' ) . ' <span class="required">*</span></label>
                     <input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" required />
                   </p>',
    ],
    // **define the full comment_field yourself:**
    'comment_field' => '
      <p class="comment-form-rating">
        <label for="rating">' . __( 'Your Rating', 'woocommerce' ) . '</label>
        <select name="rating" id="rating" required>
          <option value="">' . __( 'Rate…', 'woocommerce' ) . '</option>
          <option value="5">' . __( 'Perfect', 'woocommerce' ) . '</option>
          <option value="4">' . __( 'Good', 'woocommerce' ) . '</option>
          <option value="3">' . __( 'Average', 'woocommerce' ) . '</option>
          <option value="2">' . __( 'Not that bad', 'woocommerce' ) . '</option>
          <option value="1">' . __( 'Very poor', 'woocommerce' ) . '</option>
        </select>
      </p>
      <p class="comment-form-comment">
        <label for="comment">' . __( 'Your Review', 'woocommerce' ) . ' <span class="required">*</span></label>
        <textarea id="comment" name="comment" cols="45" rows="8" required></textarea>
      </p>
      <p class="comment-form-media">
        <label for="review_media">' . __( 'Upload photos & videos', 'yourtheme' ) . '</label>
        <input type="file" name="review_media[]" id="review_media" multiple accept="image/*,video/*" />
      </p>
    ',
  ];

  comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $form_args ) );
?>


    </div>
  </div>
</section>
