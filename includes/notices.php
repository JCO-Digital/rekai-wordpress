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
	$project_id = get_option( 'rekai_project_id' );
	$secret_key = get_option( 'rekai_secret_key' );

	if ( $is_enabled && ( empty( $project_id ) ) ) {
		wp_admin_notice(
			sprintf(
			// translators: 1: Url to the options page.
				__(
					'Rek.ai is enabled, but no project ID is set. Rek.ai will not work until you set a project ID. Configure it <a href="%s">here</a>.',
					'rekai-wordpress'
				),
				admin_url( 'admin.php?page=rekai-options&tab=general' )
			),
			array(
				'type' => 'error',
			)
		);
	}
}
