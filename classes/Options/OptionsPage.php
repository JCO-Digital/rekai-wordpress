<?php
/**
 * Options page class.
 *
 * @package Rekai\Options
 */

namespace Rekai\Options;

use function Rekai\render_checkbox_field;
use function Rekai\render_secret_field;
use function Rekai\render_template;
use function Rekai\render_text_field;

/**
 * Options page class.
 *
 * @since 0.1.0
 */
class OptionsPage {

	/**
	 * Initializes the options page.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Adds the options page to the admin menu.
	 *
	 * @return void
	 */
	final public function add_page(): void {
		add_options_page(
			'Rekai Options',
			'Rek.ai Settings',
			'manage_options',
			'rekai-options',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Handles registering the settings.
	 *
	 * @return void
	 */
	final public function register_settings(): void {
		// Settings registration.
		register_setting(
			'rekai-options-general',
			'rekai_is_enabled',
			array(
				'sanitize_callback' => 'boolval',
			)
		);
		register_setting(
			'rekai-options-general',
			'rekai_project_id',
			array(
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		register_setting(
			'rekai-options-general',
			'rekai_secret_key',
			array(
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		// Settings sections.
		add_settings_section(
			'rekai-general',
			__( 'General', 'rekai-wordpress' ),
			array( $this, 'render_general_section' ),
			'rekai-options-general',
		);

		// Settings fields.
		add_settings_field(
			'rekai_is_enabled',
			__( 'Enabled', 'rekai-wordpress' ),
			array( $this, 'render_enabled_field' ),
			'rekai-options-general',
			'rekai-general'
		);
		add_settings_field(
			'rekai_project_id',
			__( 'Project ID', 'rekai-wordpress' ),
			array( $this, 'render_id_field' ),
			'rekai-options-general',
			'rekai-general',
			array(
				'label_for' => 'rekai_project_id',
			)
		);
		add_settings_field(
			'rekai_secret_key',
			__( 'Secret Key', 'rekai-wordpress' ),
			array( $this, 'render_secret_key_field' ),
			'rekai-options-general',
			'rekai-general',
			array(
				'label_for' => 'rekai_secret_key',
			)
		);
	}

	/**
	 * Renders the General section.
	 *
	 * @return void
	 */
	final public function render_general_section(): void {
		echo '<p>' . esc_html__( 'General settings required for Rek.ai integration.', 'rekai-wordpress' ) . '</p>';
	}

	/**
	 * Renders the Enabled field.
	 *
	 * @return void
	 */
	final public function render_enabled_field(): void {
		render_checkbox_field(
			array(
				'id'          => 'rekai_is_enabled',
				'value'       => get_option( 'rekai_is_enabled', '' ),
				'placeholder' => __( 'Enable Rek.ai', 'rekai-wordpress' ),
			)
		);
	}

	/**
	 * Handles rendering the ID field.
	 *
	 * @return void
	 */
	final public function render_id_field(): void {
		render_text_field(
			array(
				'id'          => 'rekai_project_id',
				'value'       => get_option( 'rekai_project_id', '' ),
				'placeholder' => __( 'ID-123', 'rekai-wordpress' ),
			)
		);
	}

	/**
	 * Renders the Secret Key field.
	 *
	 * @return void
	 */
	final public function render_secret_key_field(): void {
		render_secret_field(
			array(
				'id'          => 'rekai_secret_key',
				'value'       => get_option( 'rekai_secret_key', '' ),
				'placeholder' => __( 'Secret Key', 'rekai-wordpress' ),
			)
		);
	}

	/**
	 * Enqueues the assets for the options page.
	 *
	 * @return void
	 */
	final public function enqueue_assets(): void {
		wp_enqueue_style( 'rekai-admin' );
		wp_enqueue_script( 'rekai-backend' );
	}

	/**
	 * Handles the rendering of the options page.
	 *
	 * @return void
	 */
	final public function render_page(): void {
		$tab  = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$data = array(
			'tabs'       => array(
				'general' => array(
					'label' => __( 'General', 'rekai-wordpress' ),
					'url'   => add_query_arg( array( 'tab' => 'general' ), admin_url( 'admin.php?page=rekai-options' ) ),
				),
			),
			'active_tab' => $tab,
		);
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo render_template( 'admin-settings', $data );
	}
}
