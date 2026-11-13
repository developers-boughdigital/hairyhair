<?php
/**
 * Adds the "Date of Birth" field to the native wp-admin user editor
 * (Profile / Users > Edit User), backed by the same 'dob' user meta key
 * already used by the frontend quiz and personal-information forms.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'show_user_profile', 'hh_render_dob_field' );
add_action( 'edit_user_profile', 'hh_render_dob_field' );

function hh_render_dob_field( $user ) {
	$dob = get_user_meta( $user->ID, 'dob', true );
	?>
	<h2><?php esc_html_e( 'Personal Information', 'hairy-hair' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="hh_dob"><?php esc_html_e( 'Date of Birth', 'hairy-hair' ); ?></label></th>
			<td>
				<input type="text" name="hh_dob" id="hh_dob" class="regular-text" placeholder="DD-MM-YYYY" value="<?php echo esc_attr( $dob ); ?>" />
				<p class="description"><?php esc_html_e( 'Format: DD-MM-YYYY', 'hairy-hair' ); ?></p>
			</td>
		</tr>
	</table>
	<?php
	wp_nonce_field( 'hh_save_dob_field', 'hh_dob_nonce' );
}

add_action( 'user_profile_update_errors', 'hh_validate_dob_field', 10, 3 );

function hh_validate_dob_field( $errors, $update, $user ) {
	if ( ! isset( $_POST['hh_dob_nonce'] ) || ! wp_verify_nonce( $_POST['hh_dob_nonce'], 'hh_save_dob_field' ) ) {
		return;
	}

	$dob = isset( $_POST['hh_dob'] ) ? sanitize_text_field( wp_unslash( $_POST['hh_dob'] ) ) : '';

	if ( '' === $dob ) {
		return;
	}

	$dob_parsed = DateTime::createFromFormat( 'd-m-Y', $dob );
	if ( ! $dob_parsed || $dob_parsed->format( 'd-m-Y' ) !== $dob ) {
		$errors->add( 'hh_dob_invalid', __( 'Please enter a valid Date of Birth in DD-MM-YYYY format.', 'hairy-hair' ) );
	}
}

add_action( 'personal_options_update', 'hh_save_dob_field' );
add_action( 'edit_user_profile_update', 'hh_save_dob_field' );

function hh_save_dob_field( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return;
	}

	if ( ! isset( $_POST['hh_dob_nonce'] ) || ! wp_verify_nonce( $_POST['hh_dob_nonce'], 'hh_save_dob_field' ) ) {
		return;
	}

	$dob = isset( $_POST['hh_dob'] ) ? sanitize_text_field( wp_unslash( $_POST['hh_dob'] ) ) : '';

	if ( '' === $dob ) {
		delete_user_meta( $user_id, 'dob' );
		return;
	}

	// Invalid format is already blocked by hh_validate_dob_field(), which runs first.
	update_user_meta( $user_id, 'dob', $dob );
}
