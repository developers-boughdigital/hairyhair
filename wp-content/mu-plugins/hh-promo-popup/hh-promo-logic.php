<?php
/**
 * HH Promo Popup — Logic, ACF Registration & Hooks
 * A/B Testing with 3 variants.
 *
 * Variant A  — Control: light-blue gradient hero, product image, coupon pill button (matches ref)
 * Variant B  — Dark/urgent: navy background, prominent code block, countdown urgency
 * Variant C  — Email capture: clean white card, email reveal flow
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'HH_POPUP_VERSION', '1.0.5' );
define( 'HH_POPUP_DIR', plugin_dir_path( __FILE__ ) );
define( 'HH_POPUP_URL', plugin_dir_url( __FILE__ ) );

// ─── Variant Assignment ───────────────────────────────────────────────────────

/**
 * Returns the variant key ('a', 'b', 'c') for the current visitor.
 * Assigns randomly on first visit and persists via a 30-day cookie.
 */
function hh_popup_get_variant(): string {
	if ( isset( $_COOKIE['hh_popup_variant'] ) ) {
		$v = sanitize_text_field( wp_unslash( $_COOKIE['hh_popup_variant'] ) );
		if ( in_array( $v, [ 'a', 'b', 'c' ], true ) ) {
			return $v;
		}
	}

	$assigned = [ 'a', 'b', 'c' ][ wp_rand( 0, 2 ) ];

	setcookie(
		'hh_popup_variant',
		$assigned,
		[
			'expires'  => time() + 30 * DAY_IN_SECONDS,
			'path'     => COOKIEPATH,
			'domain'   => COOKIE_DOMAIN,
			'secure'   => is_ssl(),
			'httponly' => true,
			'samesite' => 'Lax',
		]
	);

	return $assigned;
}

// ─── Settings ────────────────────────────────────────────────────────────────

function hh_popup_default_settings( string $variant ): array {
	$defaults = [
		'a' => [
			'enabled'  => true,
			'headline' => '20% Off Your First Order',
			'subtext'  => 'Start your hair regrowth journey today with Hairy Hair.',
			'code'     => 'HAIR20',
			'btn_text' => 'Claim My Offer',
			'cta_link' => '/shop/',
			'tc_link'  => '/promotions-terms/',
			'image'    => '',
			'delay'    => 12,
		],
		'b' => [
			'enabled'  => true,
			'headline' => '$100 Off Your First Purchase',
			'subtext'  => 'Start your hair regrowth journey today with Hairy Hair.',
			'code'     => 'HAIR100',
			'btn_text' => 'Claim My Offer',
			'cta_link' => '/shop/',
			'tc_link'  => '/promotions-terms/',
			'image'    => '',
			'delay'    => 12,
		],
		'c' => [
			'enabled'  => true,
			'headline' => 'First Month Free',
			'subtext'  => 'Start your hair regrowth journey today with Hairy Hair.',
			'code'     => 'FREEMONTH',
			'btn_text' => 'Claim My Offer',
			'cta_link' => '/shop/',
			'tc_link'  => '/promotions-terms/',
			'image'    => '',
			'delay'    => 12,
		],
	];

	return $defaults[ $variant ] ?? $defaults['a'];
}

function hh_popup_get_settings( string $variant ): array {
	$d = hh_popup_default_settings( $variant );

	if ( ! function_exists( 'get_field' ) ) {
		return $d;
	}

	$p = 'hh_popup_variant_' . $variant . '_';

	return [
		'enabled'  => get_field( $p . 'enabled', 'option' ) ?? $d['enabled'],
		'headline' => get_field( $p . 'headline', 'option' ) ?: $d['headline'],
		'subtext'  => get_field( $p . 'subtext', 'option' )  ?: $d['subtext'],
		'code'     => get_field( $p . 'code', 'option' )     ?: $d['code'],
		'btn_text' => get_field( $p . 'btn_text', 'option' ) ?: $d['btn_text'],
		'cta_link' => get_field( $p . 'cta_link', 'option' ) ?: $d['cta_link'],
		'tc_link'  => get_field( $p . 'tc_link', 'option' )  ?: $d['tc_link'],
		'image'    => get_field( $p . 'image', 'option' )    ?: $d['image'],
		'delay'    => (int) ( get_field( 'hh_popup_delay', 'option' ) ?: $d['delay'] ),
	];
}

function hh_popup_get_active_variant_payload(): array {
	$preferred_variant = hh_popup_get_variant();
	$variants          = [ 'a', 'b', 'c' ];
	$ordered_variants  = array_values( array_unique( array_merge( [ $preferred_variant ], $variants ) ) );

	foreach ( $ordered_variants as $variant ) {
		$settings = hh_popup_get_settings( $variant );
		if ( ! empty( $settings['enabled'] ) ) {
			return [
				'variant'  => $variant,
				'settings' => $settings,
			];
		}
	}

	return [
		'variant'  => $preferred_variant,
		'settings' => hh_popup_get_settings( $preferred_variant ),
	];
}

// ─── Enqueue ─────────────────────────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', 'hh_popup_enqueue' );

function hh_popup_enqueue(): void {
	if ( is_admin() ) {
		return;
	}

	// Disable popup on the affiliate landing page and all quiz pages.
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$blocked_slugs = [ 'affiliate-landing-page', 'quiz', 'the-hair-treatment-quiz' ];
	$uri_blocked   = false;
	foreach ( $blocked_slugs as $slug ) {
		if ( false !== strpos( $request_uri, $slug ) ) {
			$uri_blocked = true;
			break;
		}
	}
	if ( $uri_blocked || is_page( $blocked_slugs ) ) {
		return;
	}

	wp_enqueue_style(
		'hh-promo-popup',
		HH_POPUP_URL . 'assets/popup.css',
		[],
		HH_POPUP_VERSION
	);

	wp_enqueue_script(
		'hh-promo-popup',
		HH_POPUP_URL . 'assets/popup.js',
		[],
		HH_POPUP_VERSION,
		true
	);

	$payload  = hh_popup_get_active_variant_payload();
	$variant  = $payload['variant'];
	$settings = $payload['settings'];

	wp_localize_script(
		'hh-promo-popup',
		'hhPopupData',
		[
			'variant'       => $variant,
			'variantEnabled' => ! empty( $settings['enabled'] ),
			'delay'         => $settings['delay'],
			'dismissCookie' => 'hh_popup_dismissed',
			'enabledCookie' => 'hh_popup_enabled',
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'nonce'         => wp_create_nonce( 'hh_popup_nonce' ),
		]
	);
}

add_action( 'admin_enqueue_scripts', 'hh_popup_admin_enqueue' );

function hh_popup_admin_enqueue( string $hook ): void {
	if ( ! hh_popup_is_admin_page() ) {
		return;
	}

	wp_enqueue_script(
		'hh-promo-popup-admin',
		HH_POPUP_URL . 'assets/popup-admin.js',
		[],
		HH_POPUP_VERSION,
		true
	);

	wp_localize_script(
		'hh-promo-popup-admin',
		'hhPopupAdminData',
		[
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'hh_popup_nonce' ),
		]
	);
}

// ─── Render ───────────────────────────────────────────────────────────────────

add_action( 'wp_footer', 'hh_popup_render' );

function hh_popup_render(): void {
	if ( is_admin() ) {
		return;
	}

	// Disable popup on all quiz pages.
	$request_uri  = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$quiz_slugs   = [ 'quiz', 'the-hair-treatment-quiz' ];
	$uri_is_quiz  = false;
	foreach ( $quiz_slugs as $slug ) {
		if ( false !== strpos( $request_uri, $slug ) ) {
			$uri_is_quiz = true;
			break;
		}
	}
	if ( $uri_is_quiz || is_page( $quiz_slugs ) ) {
		return;
	}

	// Respect the global admin toggle (defaults to enabled if option has never been set).
	$globally_enabled = get_option( 'hh_popup_globally_enabled', 1 );
	if ( ! $globally_enabled ) {
		return;
	}

	$payload  = hh_popup_get_active_variant_payload();
	$variant  = $payload['variant'];
	$settings = $payload['settings'];
	$is_enabled = ! empty( $settings['enabled'] );
	?>
	<?php if ( $is_enabled ) : ?>
		<div
			id="hh-promo-popup"
			class="hh-popup-overlay"
			data-variant="<?php echo esc_attr( $variant ); ?>"
			style="display:none"
			role="dialog"
			aria-modal="true"
			aria-label="Promotional offer"
		>
			<div class="hh-popup-container hh-variant-<?php echo esc_attr( $variant ); ?>">
				<?php hh_popup_render_popup_card( $settings ); ?>
			</div>
		</div>
	<?php endif; ?>
	<?php
}

// ── Popup Card — Unified design (variations differ only in headline and code) ──

function hh_popup_render_popup_card( array $s ): void {
	$img    = ! empty( $s['image'] )   ? esc_url( $s['image'] )   : '';
	$tclink = ! empty( $s['tc_link'] ) ? esc_url( $s['tc_link'] ) : '#';
	?>
	<button
		class="hh-popup-close"
		type="button"
		aria-label="<?php esc_attr_e( 'Close promotion', 'hh-promo-popup' ); ?>"
	>&#x2715;</button>
	<div class="hh-popup-hero">
		<?php if ( $img ) : ?>
			<img src="<?php echo $img; ?>" alt="Hairy Hair products" class="hh-popup-product-img">
		<?php else : ?>
			<!-- CSS bottle stage (replaced by ACF product image once set) -->
			<div class="hh-popup-stage" aria-hidden="true">
				<span class="hh-popup-orb hh-popup-orb-1"></span>
				<span class="hh-popup-orb hh-popup-orb-2"></span>
				<span class="hh-popup-orb hh-popup-orb-3"></span>
				<div class="hh-popup-bottle hh-popup-bottle-back">
					<div class="hh-popup-bottle-cap"></div>
					<div class="hh-popup-bottle-label">
						<svg width="22" height="27" viewBox="0 0 18 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 0C9 0 2 7.5 2 13a7 7 0 0014 0C16 7.5 9 0 9 0z" fill="#5bb8e8"/></svg>
						<span class="hh-popup-bottle-brand"><b>hairy</b>hair</span>
					</div>
				</div>
				<div class="hh-popup-bottle hh-popup-bottle-front">
					<div class="hh-popup-bottle-cap"></div>
					<div class="hh-popup-bottle-label">
						<svg width="22" height="27" viewBox="0 0 18 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9 0C9 0 2 7.5 2 13a7 7 0 0014 0C16 7.5 9 0 9 0z" fill="#5bb8e8"/></svg>
						<span class="hh-popup-bottle-brand"><b>hairy</b>hair</span>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
	<div class="row hh-popup-body">
		<h2 class="hh-popup-headline"><?php echo esc_html( $s['headline'] ); ?></h2>
		<p class="hh-popup-subtext"><?php echo esc_html( $s['subtext'] ); ?></p>
		<button
			class="hh-popup-cta hh-copy-btn"
			data-code="<?php echo esc_attr( $s['code'] ); ?>"
		>USE CODE:&nbsp;&nbsp;<strong><?php echo esc_html( $s['code'] ); ?></strong></button>
		<!-- <a
			href="<?php echo $tclink; ?>"
			class="hh-popup-tc"
			target="_blank"
			rel="noopener noreferrer"
		>T&amp;Cs apply</a> -->
	</div>
	<?php
}

// ─── AJAX: Track Impression ───────────────────────────────────────────────────

add_action( 'wp_ajax_hh_popup_impression',        'hh_popup_ajax_impression' );
add_action( 'wp_ajax_nopriv_hh_popup_impression', 'hh_popup_ajax_impression' );

function hh_popup_ajax_impression(): void {
	check_ajax_referer( 'hh_popup_nonce', 'nonce' );

	$variant = sanitize_text_field( wp_unslash( $_POST['variant'] ?? '' ) );
	if ( in_array( $variant, [ 'a', 'b', 'c' ], true ) ) {
		$key   = 'hh_popup_impressions_' . $variant;
		$count = (int) get_option( $key, 0 );
		update_option( $key, $count + 1, false );
	}

	wp_send_json_success();
}

// ─── AJAX: Track Conversion (code copied / email submitted) ──────────────────

add_action( 'wp_ajax_hh_popup_conversion',        'hh_popup_ajax_conversion' );
add_action( 'wp_ajax_nopriv_hh_popup_conversion', 'hh_popup_ajax_conversion' );

function hh_popup_ajax_conversion(): void {
	check_ajax_referer( 'hh_popup_nonce', 'nonce' );

	$variant = sanitize_text_field( wp_unslash( $_POST['variant'] ?? '' ) );
	if ( in_array( $variant, [ 'a', 'b', 'c' ], true ) ) {
		$key   = 'hh_popup_conversions_' . $variant;
		$count = (int) get_option( $key, 0 );
		update_option( $key, $count + 1, false );
	}

	wp_send_json_success();
}

// ─── AJAX: Admin Enable/Disable Toggle ───────────────────────────────────────

add_action( 'wp_ajax_hh_popup_admin_toggle', 'hh_popup_ajax_admin_toggle' );

function hh_popup_ajax_admin_toggle(): void {
	check_ajax_referer( 'hh_popup_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( [ 'message' => 'Unauthorized' ], 403 );
	}

	$enabled = filter_var( wp_unslash( $_POST['enabled'] ?? '' ), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
	if ( null === $enabled ) {
		wp_send_json_error( [ 'message' => 'Invalid value' ], 400 );
	}

	update_option( 'hh_popup_globally_enabled', (int) $enabled, false );

	wp_send_json_success( [ 'enabled' => $enabled ] );
}

// ─── Admin Stats Page ─────────────────────────────────────────────────────────

add_action( 'admin_menu', 'hh_popup_admin_menu' );
add_action( 'admin_init', 'hh_popup_redirect_legacy_stats_page' );
add_action( 'all_admin_notices', 'hh_popup_render_stats_on_settings_page' );
add_action( 'admin_head', 'hh_popup_hide_admin_notices' );

function hh_popup_admin_menu(): void {
	// Keep standalone stats page only when ACF options page is unavailable.
	if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
		add_options_page(
			'Promo Popup A/B Stats',
			'Promo Popup',
			'manage_options',
			'hh-popup-stats',
			'hh_popup_stats_page'
		);
	}

	// Keep standalone preview page only when ACF options page is unavailable.
	if ( ! function_exists( 'acf_add_options_sub_page' ) ) {
		add_submenu_page(
			'options-general.php',
			'Promo Popup Preview',
			'— Preview',
			'manage_options',
			'hh-popup-preview',
			'hh_popup_preview_page'
		);
	}
}

function hh_popup_get_current_admin_page(): string {
	if ( ! isset( $_GET['page'] ) ) {
		return '';
	}

	return sanitize_key( wp_unslash( $_GET['page'] ) );
}

function hh_popup_is_acf_settings_page(): bool {
	return 'acf-options-promo-popup' === hh_popup_get_current_admin_page();
}

function hh_popup_is_admin_page(): bool {
	return in_array( hh_popup_get_current_admin_page(), [ 'acf-options-promo-popup', 'hh-popup-stats', 'hh-popup-preview' ], true );
}

function hh_popup_redirect_legacy_stats_page(): void {
	if ( ! function_exists( 'acf_add_options_sub_page' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$page = hh_popup_get_current_admin_page();
	if ( ! in_array( $page, [ 'hh-popup-stats', 'hh-popup-preview' ], true ) ) {
		return;
	}

	wp_safe_redirect( admin_url( 'options-general.php?page=acf-options-promo-popup#hh-popup-admin-preview' ) );
	exit;
}

function hh_popup_hide_admin_notices(): void {
	if ( ! current_user_can( 'manage_options' ) || ! hh_popup_is_admin_page() ) {
		return;
	}
	?>
	<style>
		#wpbody-content .notice,
		#wpbody-content .update-nag,
		#wpbody-content .updated,
		#wpbody-content .error {
			display: none !important;
		}
	</style>
	<?php
}

// Enqueue popup CSS on our admin pages only
add_action( 'admin_enqueue_scripts', 'hh_popup_admin_enqueue_preview' );

function hh_popup_admin_enqueue_preview( string $hook ): void {
	if ( ! in_array( $hook, [ 'settings_page_hh-popup-stats', 'settings_page_hh-popup-preview', 'settings_page_acf-options-promo-popup' ], true ) ) {
		return;
	}
	wp_enqueue_style(
		'hh-promo-popup-preview',
		HH_POPUP_URL . 'assets/popup.css',
		[],
		HH_POPUP_VERSION
	);
}

// ─── Stats Page ───────────────────────────────────────────────────────────────

function hh_popup_handle_stats_reset(): bool {
	if (
		isset( $_POST['hh_reset_stats'], $_POST['hh_reset_nonce'] ) &&
		wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['hh_reset_nonce'] ) ), 'hh_reset_stats' ) &&
		current_user_can( 'manage_options' )
	) {
		foreach ( [ 'a', 'b', 'c' ] as $v ) {
			delete_option( 'hh_popup_impressions_' . $v );
			delete_option( 'hh_popup_conversions_' . $v );
		}
		return true;
	}

	return false;
}

function hh_popup_render_stats_content( bool $show_preview_link = true ): void {
	$variants = [
		'a' => 'Variation 1 — 20% Off Your First Order',
		'b' => 'Variation 2 — $100 Off Your First Purchase',
		'c' => 'Variation 3 — First Month Free',
	];

	// Determine winner.
	$best_rate = -1;
	$winner    = null;
	foreach ( array_keys( $variants ) as $v ) {
		$imp  = (int) get_option( 'hh_popup_impressions_' . $v, 0 );
		$conv = (int) get_option( 'hh_popup_conversions_' . $v, 0 );
		if ( $imp > 0 ) {
			$rate = $conv / $imp;
			if ( $rate > $best_rate ) { $best_rate = $rate; $winner = $v; }
		}
	}
	?>
	<?php if ( $winner ) : ?>
		<div style="padding:10px 14px;margin-top:12px;background:#eef7ff;border:1px solid #d3e5f6;border-left:4px solid #4f94d4">
			<strong>Leading variant:</strong> <?php echo esc_html( $variants[ $winner ] ); ?>
			(<?php echo esc_html( number_format( $best_rate * 100, 1 ) ); ?>% conversion rate)
		</div>
	<?php endif; ?>
	<table class="widefat striped" style="max-width:640px;margin-top:16px">
		<thead>
			<tr>
				<th>Variant</th>
				<th>Impressions</th>
				<th>Conversions</th>
				<th>Conv. Rate</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $variants as $key => $label ) :
			$imp  = (int) get_option( 'hh_popup_impressions_' . $key, 0 );
			$conv = (int) get_option( 'hh_popup_conversions_' . $key, 0 );
			$rate = $imp > 0 ? number_format( ( $conv / $imp ) * 100, 1 ) . '%' : '—';
			$is_winner = ( $key === $winner );
			?>
			<tr style="<?php echo $is_winner ? 'background:#edfff2;font-weight:600' : ''; ?>">
				<td>
					<?php echo esc_html( $label ); ?>
					<?php if ( $is_winner ) echo ' 🏆'; ?>
				</td>
				<td><?php echo esc_html( number_format( $imp ) ); ?></td>
				<td><?php echo esc_html( number_format( $conv ) ); ?></td>
				<td><?php echo esc_html( $rate ); ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<p style="margin-top:12px;color:#666;font-size:12px">
		Impressions = popup shown &nbsp;·&nbsp; Conversions = code copied or email submitted.
	</p>
	<form method="post" style="margin-top:18px"
		onsubmit="return confirm('Reset all A/B stats? This cannot be undone.')">
		<?php wp_nonce_field( 'hh_reset_stats', 'hh_reset_nonce' ); ?>
		<input type="hidden" name="hh_reset_stats" value="1">
		<button type="submit" class="button button-secondary">Reset Stats</button>
	</form>
	<?php if ( $show_preview_link ) : ?>
		<p style="margin-top:16px">
			<a href="<?php echo esc_url( admin_url( 'options-general.php?page=hh-popup-preview' ) ); ?>" class="button">
				Preview All Variants →
			</a>
		</p>
	<?php endif; ?>
	<?php
}

// ─── Admin Enable/Disable Toggle Card ────────────────────────────────────────

function hh_popup_render_admin_toggle(): void {
	$is_enabled = (bool) get_option( 'hh_popup_globally_enabled', 1 );
	?>
	<div id="hh-popup-pref" class="hh-popup-pref card shadow-sm border-0"
		style="display:inline-flex;margin-bottom:16px;background:#fff;border:1px solid #ccd0d4!important;border-radius:6px"
		aria-live="polite">
		<div class="card-body" style="padding:12px 16px">
			<div style="display:flex;align-items:center;gap:10px;margin:0">
				<label for="hh-popup-enabled-switch"
					style="font-weight:600;font-size:13px;margin:0;cursor:pointer">Promo Popup</label>
				<input type="checkbox" role="switch"
					id="hh-popup-enabled-switch"
					style="width:38px;height:20px;cursor:pointer;accent-color:#2f8f57"
					<?php checked( $is_enabled ); ?>>
				<span id="hh-popup-pref-state"
					style="font-size:12px;min-width:66px">
					<?php echo $is_enabled
						? '<span style="color:#2f8f57;font-weight:600">Enabled</span>'
						: '<span style="color:#b32d2e;font-weight:600">Disabled</span>'; ?>
				</span>
				<span id="hh-popup-pref-saving" style="font-size:11px;color:#888;display:none">Saving…</span>
			</div>
			<p style="margin:6px 0 0;font-size:11px;color:#888">
				Toggles the promo popup for all front-end visitors site-wide.
			</p>
		</div>
	</div>
	<?php
}

function hh_popup_stats_page(): void {
	$did_reset = hh_popup_handle_stats_reset();
	?>
	<div class="wrap">
		<h1>Promo Popup A/B Test Stats</h1>
		<?php if ( $did_reset ) : ?>
			<div style="padding:10px 14px;margin-top:12px;background:#edfff2;border:1px solid #c4e5cc;border-left:4px solid #2f8f57"><p style="margin:0">Stats reset.</p></div>
		<?php endif; ?>
		<?php hh_popup_render_admin_toggle(); ?>
		<?php hh_popup_render_stats_content(); ?>
	</div>
	<?php
}

function hh_popup_render_stats_on_settings_page(): void {
	if ( ! function_exists( 'acf_add_options_sub_page' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! hh_popup_is_acf_settings_page() ) {
		return;
	}

	$did_reset = hh_popup_handle_stats_reset();
	?>
	<div style="background:#fff;border:1px solid #ccd0d4;padding:16px 20px;margin:16px 0 8px 0;max-width:960px">
		<h2 style="margin:0">Promo Popup A/B Test Stats</h2>
		<?php if ( $did_reset ) : ?>
			<div style="padding:10px 14px;margin-top:12px;background:#edfff2;border:1px solid #c4e5cc;border-left:4px solid #2f8f57"><p style="margin:0">Stats reset.</p></div>
		<?php endif; ?>
		<?php hh_popup_render_admin_toggle(); ?>
		<?php hh_popup_render_stats_content( false ); ?>

		<div id="hh-popup-admin-preview" style="margin-top:24px;padding-top:20px;border-top:1px solid #e5e7eb">
			<h2 style="margin:0 0 8px 0">Promo Popup — Variant Preview</h2>
			<p style="color:#666;margin:0 0 16px 0">
				Live renders using current ACF settings (or defaults). Popups are static here — no JS behaviour.
			</p>
			<?php hh_popup_render_preview_content(); ?>
		</div>
	</div>
	<?php
}

// ─── Preview Page ─────────────────────────────────────────────────────────────

function hh_popup_render_preview_content(): void {
	$variants = [ 'a', 'b', 'c' ];
	$labels   = [
		'a' => 'Variation 1 — 20% Off Your First Order',
		'b' => 'Variation 2 — $100 Off Your First Purchase',
		'c' => 'Variation 3 — First Month Free',
	];
	?>
	<div style="display:flex;flex-wrap:wrap;gap:32px;align-items:flex-start">
	<?php foreach ( $variants as $v ) :
		$s = hh_popup_get_settings( $v );
		?>
		<div style="flex:0 0 420px">
			<h3 style="margin-bottom:10px;font-size:14px;color:#1a2f48"><?php echo esc_html( $labels[ $v ] ); ?></h3>
			<!-- Static preview wrapper — mirrors front-end markup without overlay -->
			<div class="hh-popup-container hh-variant-<?php echo esc_attr( $v ); ?>"
				style="position:relative;box-shadow:0 8px 32px rgba(0,0,0,.18)">
			
				<?php hh_popup_render_popup_card( $s ); ?>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
	<?php
}

function hh_popup_preview_page(): void {
	?>
	<div class="wrap">
		<h1>Promo Popup — Variant Preview</h1>
		<p style="color:#666;margin-bottom:24px">
			Live renders using current ACF settings (or defaults). Popups are static here — no JS behaviour.
		</p>
		<?php hh_popup_render_preview_content(); ?>
	</div>
	<?php
}

// ─── ACF Options Fields ───────────────────────────────────────────────────────

add_action( 'acf/init', 'hh_popup_register_acf_fields' );

function hh_popup_register_acf_fields(): void {
	if ( ! function_exists( 'acf_add_options_sub_page' ) || ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_options_sub_page( [
		'page_title'  => 'Promo Popup Settings',
		'menu_title'  => 'Promo Popup',
		'parent_slug' => 'options-general.php',
	] );

	$variant_labels = [
		'a' => 'Variation 1 — 20% Off',
		'b' => 'Variation 2 — $100 Off',
		'c' => 'Variation 3 — First Month Free',
	];

	$fields = [];

	foreach ( $variant_labels as $key => $label ) {
		$p = 'hh_popup_variant_' . $key . '_';

		$fields[] = [
			'key'   => 'field_hh_tab_' . $key,
			'label' => $label,
			'name'  => '',
			'type'  => 'tab',
		];

		$fields[] = [
			'key'           => 'field_' . $p . 'enabled',
			'label'         => 'Enable ' . $label,
			'name'          => $p . 'enabled',
			'type'          => 'true_false',
			'default_value' => 1,
			'ui'            => 1,
		];

		$fields[] = [
			'key'   => 'field_' . $p . 'headline',
			'label' => 'Headline',
			'name'  => $p . 'headline',
			'type'  => 'text',
		];

		$fields[] = [
			'key'   => 'field_' . $p . 'subtext',
			'label' => 'Subtext',
			'name'  => $p . 'subtext',
			'type'  => 'textarea',
			'rows'  => 2,
		];

		$fields[] = [
			'key'   => 'field_' . $p . 'code',
			'label' => 'Coupon Code',
			'name'  => $p . 'code',
			'type'  => 'text',
		];

		$fields[] = [
			'key'   => 'field_' . $p . 'btn_text',
			'label' => 'Button Text',
			'name'  => $p . 'btn_text',
			'type'  => 'text',
		];

		$fields[] = [
			'key'   => 'field_' . $p . 'cta_link',
			'label' => 'CTA Link (shop/treatment URL)',
			'name'  => $p . 'cta_link',
			'type'  => 'text',
		];

		$fields[] = [
			'key'   => 'field_' . $p . 'tc_link',
			'label' => 'T&Cs Page URL',
			'name'  => $p . 'tc_link',
			'type'  => 'text',
		];

		$fields[] = [
			'key'           => 'field_' . $p . 'image',
			'label'         => 'Product Image',
			'name'          => $p . 'image',
			'type'          => 'image',
			'return_format' => 'url',
		];
	}

	$fields[] = [
		'key'   => 'field_hh_tab_global',
		'label' => 'Global Settings',
		'name'  => '',
		'type'  => 'tab',
	];

	$fields[] = [
		'key'           => 'field_hh_popup_delay',
		'label'         => 'Popup Delay (seconds)',
		'name'          => 'hh_popup_delay',
		'type'          => 'number',
		'default_value' => 12,
		'min'           => 0,
		'max'           => 60,
	];

	acf_add_local_field_group( [
		'key'      => 'group_hh_promo_popup',
		'title'    => 'HH Promo Popup',
		'fields'   => $fields,
		'location' => [
			[ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'acf-options-promo-popup' ] ],
		],
		'active'   => true,
	] );
}