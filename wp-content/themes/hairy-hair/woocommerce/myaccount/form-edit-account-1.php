<?php

defined( 'ABSPATH' ) || exit;
$user_id  = get_current_user_id();
$user     = get_userdata( $user_id );
$first    = get_user_meta( $user_id, 'first_name', true );
$last     = get_user_meta( $user_id, 'last_name',  true );
$full     = trim( $first . ' ' . $last );
$pic_id   = get_user_meta( $user_id, 'profile_picture_id', true );
$pic_url  = $pic_id
  ? wp_get_attachment_url( $pic_id )
  : get_avatar_url( $user_id, [ 'size' => 100 ] );
?>

<h2><?php esc_html_e( 'My Profile', 'your-text-domain' ); ?></h2>
<hr>

<!-- PROFILE PICTURE + CONTROLS -->
<div class="profile-pic-wrapper">
  <div class="profile-pic-container">
  <img
    src="<?php echo esc_url( $pic_url ); ?>"
    alt="Profile Picture"
    class="profile-pic"
    data-default-avatar="<?php echo $default_avatar; ?>"
  />
  <?php if ( $pic_id ) : ?>
    <button type="button" id="remove-profile-pic" class="remove-pic-btn">&times;</button>
  <?php endif; ?>
  </div>
  <button type="button" id="upload-profile-pic" class="upload-pic-btn">
    <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/upload-icon.png' ); ?>"
         alt="" class="upload-icon" />
    <?php esc_html_e( 'Upload new picture', 'your-text-domain' ); ?>
  </button>
</div>

<form class="woocommerce-EditAccountForm edit-account"
      action="" method="post" enctype="multipart/form-data">

  <!-- HIDDEN FIELD FOR MEDIA UPLOAD -->
  <input
        type="hidden"
        name="profile_picture_id"
        id="profile_picture_id"
        value="<?php echo esc_attr( $pic_id ); ?>"
        data-default-avatar="<?php echo $default_avatar; ?>"
        />

  <!-- FULL NAME + EMAIL -->
  <div class="form-row form-row-first">
    <label for="account_full_name">
      <?php esc_html_e( 'Full Name', 'woocommerce' ); ?>
    </label>
    <input type="text" name="account_full_name"
           id="account_full_name"
           class="input-text"
           value="<?php echo esc_attr( $full ); ?>" />
  </div>

  <div class="form-row form-row-last">
    <label for="account_email">
      <?php esc_html_e( 'Email Address', 'woocommerce' ); ?>
    </label>
    <input type="email" name="account_email"
           id="account_email"
           class="input-text"
           value="<?php echo esc_attr( $user->user_email ); ?>" />
  </div>
  <div class="clear"></div>

  <!-- PASSWORD CHANGE -->
  <h3><?php esc_html_e( 'Change Password', 'woocommerce' ); ?></h3>
  <?php foreach ( [
    'password_current' => 'Old Password',
    'password_1'       => 'New Password',
    'password_2'       => 'Confirm New Password',
  ] as $field => $label ) : ?>
    <div class="form-row <?php echo $field==='password_current'? 'form-row-first'
                            : ($field==='password_1'? 'form-row-mid' : 'form-row-last'); ?>
                       password-field">
      <label for="<?php echo esc_attr( $field ); ?>">
        <?php esc_html_e( $label, 'woocommerce' ); ?>
      </label>
      <div class="password-wrapper">
        <input type="password"
               name="<?php echo esc_attr( $field ); ?>"
               id="<?php echo esc_attr( $field ); ?>"
               class="input-text password-input"
               placeholder="<?php echo esc_attr( 'Enter ' . strtolower( $label ), 'woocommerce' ); ?>" />
        <span class="toggle-password">&#128065;</span>
      </div>
    </div>
  <?php endforeach; ?>
  <div class="clear"></div>

  <?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>

  <button type="submit" class="btn save-account-btn">
    <?php esc_html_e( 'Save Changes', 'woocommerce' ); ?>
  </button>
</form>

<script>
jQuery( document ).ready( function( $ ) {
  var frame; 

  $( '#upload-profile-pic' ).on( 'click', function( e ) {
    e.preventDefault();

    // reuse the frame if it’s already open
    if ( frame ) {
      frame.open();
      return;
    }

    // create a new media frame
    frame = wp.media({
      title: 'Select Profile Picture',
      button: { text: 'Use this Picture' },
      multiple: false
    });

    // when a file is selected, grab the URL and ID and update the form
    frame.on( 'select', function() {
      var attachment = frame.state().get('selection').first().toJSON();
      // set hidden input
      $( '#profile_picture_id' ).val( attachment.id );
      // swap out the <img>
      $( '.profile-pic' ).attr( 'src', attachment.url );
      // add the remove button if missing
      if ( ! $( '#remove-profile-pic' ).length ) {
        $( '.profile-pic-container' )
          .append('<button type="button" id="remove-profile-pic" class="remove-pic-btn">&times;</button>');
      }
    });

    frame.open();
  });

  // handle the “remove” click
  $( document ).on( 'click', '#remove-profile-pic', function() {
    $( '#profile_picture_id' ).val( '' );
    // reset to gravatar or placeholder
    var defaultAvatar = $( '#profile_picture_id' ).data('default-avatar');
    $( '.profile-pic' ).attr( 'src', defaultAvatar );
    $( this ).remove();
  });
});


</script>
