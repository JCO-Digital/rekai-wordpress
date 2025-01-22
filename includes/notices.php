<?php
/**
 * Handles notices.
 *
 * @package Rekai
 */

namespace Rekai;

/**
 * Handles notices.
 *
 * @return void
 */
function handle_notices(): void {
	$is_enabled = get_option( 'rekai_is_enabled' ) === '1';
	$script_key = get_option( 'rekai_script_key' );

	if ( $is_enabled && ( empty( $script_key ) ) ) {
		wp_admin_notice(
			sprintf(
			// translators: 1: Url to the options page.
				__(
					'Rek.ai is enabled, but no Script key is set. Rek.ai will not work until you set a project ID. Configure it <a href="%s">here</a>.',
					'rekai-wordpress'
				),
				admin_url( 'admin.php?page=rekai-settings&tab=general' )
			),
			array(
				'type' => 'error',
			)
		);
	}

	if ( wp_get_environment_type() !== 'production' && $is_enabled ) {
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
