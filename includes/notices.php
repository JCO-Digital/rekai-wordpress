<?php
/**
 * Handles notices.
 *
 * @package Rekai
 */

namespace Rekai;

use Rekai\Scripts\RekaiMain;

/**
 * Handles notices.
 *
 * @return void
 */
function handle_notices(): void {
	$is_enabled = get_option( 'rekai_is_enabled' ) === '1';
	$is_test    = get_option( 'rekai_test_mode' ) === '1';
	$embed_code = get_option( 'rekai_embed_code' );

	if ( $is_enabled && ( empty( $embed_code ) ) ) {
		wp_admin_notice(
			sprintf(
				/* translators: 1: Url to the options page. */
				__(
					'Rek.ai is enabled, but no embed code is set. Rek.ai will not work until you set a project ID. Configure it <a href="%s">here</a>.',
					'rekai-wordpress'
				),
				admin_url( 'admin.php?page=rekai-settings&tab=general' )
			),
			array(
				'type' => 'error',
			)
		);
	}

	$rekai = RekaiMain::get_instance();
	if ( $is_enabled && $rekai->is_incomplete_test_mode() ) {
		wp_admin_notice(
			sprintf(
				/* translators: 1: Url to the options page. */
				__(
					'Rek.ai is enabled and in test mode, but Project ID or Secret Key is not set. They can be set <a href="%s">here</a>.',
					'rekai-wordpress'
				),
				admin_url( 'admin.php?page=rekai-settings&tab=advanced' )
			),
			array(
				'type' => 'warning',
			)
		);
	}

	if ( wp_get_environment_type() !== 'production' && $is_enabled && ! $is_test ) {
		wp_admin_notice(
			sprintf(
				/* translators: 1: Url to the options page. */
				__(
					'Rek.ai is enabled, but the WP environment type is not set to production. Rek.ai scripts will load but are set to not send any visitor data. If this is not intended, you can override it by following the documentation <a href="%s">here</a>.',
					'rekai-wordpress'
				),
				admin_url( 'admin.php?page=rekai-settings&tab=docs#overriding-test-mode' )
			),
			array(
				'type' => 'warning',
			)
		);
	}
}
