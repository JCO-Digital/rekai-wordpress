<?php
/**
 * Options page class.
 *
 * @package Rekai\Options
 */

namespace Rekai\Options;

use Rekai\Singleton;
use function Rekai\render_checkbox_field;
use function Rekai\render_secret_field;
use function Rekai\render_template;
use function Rekai\render_text_field;
use function Sodium\add;

/**
 * Options page class.
 *
 * @since 0.1.0
 */
class OptionsPage extends Singleton {

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
			array( 'sanitize_callback' => 'boolval' )
		);
		register_setting(
			'rekai-options-general',
			'rekai_script_key',
			array( 'sanitize_callback' => 'sanitize_text_field' )
		);
		register_setting(
			'rekai-options-advanced',
			'rekai_test_mode',
			array( 'sanitize_callback' => 'boolval' )
		);
		register_setting(
			'rekai-options-advanced',
			'rekai_project_id',
			array( 'sanitize_callback' => 'sanitize_text_field' )
		);
		register_setting(
			'rekai-options-advanced',
			'rekai_secret_key',
			array( 'sanitize_callback' => 'sanitize_text_field' )
		);

		// Settings sections.
		add_settings_section(
			'rekai-general',
			__( 'General', 'rekai-wordpress' ),
			array( $this, 'render_general_section' ),
			'rekai-options-general',
		);
		add_settings_section(
			'rekai-advanced',
			__( 'Advanced', 'rekai-wordpress' ),
			array( $this, 'render_advanced_section' ),
			'rekai-options-advanced',
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
			'rekai_script_key',
			__( 'Script Key', 'rekai-wordpress' ),
			array( $this, 'render_script_key_field' ),
			'rekai-options-general',
			'rekai-general',
			array(
				'label_for' => 'rekai_script_key',
			)
		);
		add_settings_field(
			'rekai_test_mode',
			__( 'Test Mode', 'rekai-wordpress' ),
			array( $this, 'render_test_mode_field' ),
			'rekai-options-advanced',
			'rekai-advanced',
			array(
				'label_for' => 'rekai_test_mode',
			)
		);
		add_settings_field(
			'rekai_project_id',
			__( 'Project ID', 'rekai-wordpress' ),
			array( $this, 'render_project_id_field' ),
			'rekai-options-advanced',
			'rekai-advanced',
			array(
				'label_for' => 'rekai_project_id',
			)
		);
		add_settings_field(
			'rekai_secret_key',
			__( 'Secret Key', 'rekai-wordpress' ),
			array( $this, 'render_secret_key_field' ),
			'rekai-options-advanced',
			'rekai-advanced',
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
	 * Renders the Advanced section.
	 *
	 * @return void
	 */
	final public function render_advanced_section(): void {
		echo '<p>' . esc_html__( 'Advanced settings for Rek.ai.', 'rekai-wordpress' ) . '</p>';
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
				'placeholder' => esc_html__( 'Enable Rek.ai', 'rekai-wordpress' ),
			)
		);
	}

	/**
	 * Handles rendering the ID field.
	 *
	 * @return void
	 */
	final public function render_script_key_field(): void {
		render_text_field(
			array(
				'id'          => 'rekai_script_key',
				'value'       => get_option( 'rekai_script_key', '' ),
				'placeholder' => esc_html__( 'Script Key', 'rekai-wordpress' ),
				'help'        => sprintf(
					/* translators: 1: is a link to a support document. 2: closing link */
					esc_html__( 'The script key can be found in your dashboard, %1$splease refer to this document%2$s for more information.', 'rekai-wordpress' ),
					'<a href="' . esc_url( 'https://docs.rek.ai/dashboard-guide#embed-code' ) . '" target="_blank" rel="noopener noreferrer">',
					'</a>'
				),
			)
		);
	}

	/**
	 * Renders the Test Mode field.
	 *
	 * @return void
	 */
	final public function render_test_mode_field(): void {
		render_checkbox_field(
			array(
				'id'          => 'rekai_test_mode',
				'value'       => get_option( 'rekai_test_mode', '' ),
				'placeholder' => esc_html__( 'Test Mode', 'rekai-wordpress' ),
				'help'        => esc_html__( 'Enables test mode. This will not send any data to Rek.ai.', 'rekai-wordpress' ),
			)
		);
	}

	/**
	 * Renders the Project ID field.
	 *
	 * @return void
	 */
	final public function render_project_id_field(): void {
		render_text_field(
			array(
				'id'          => 'rekai_project_id',
				'value'       => get_option( 'rekai_project_id', '' ),
				'placeholder' => esc_html__( 'Project ID', 'rekai-wordpress' ),
				'help'        => sprintf(
					/* translators: 1: is a link to a support document. 2: closing link */
					esc_html__( 'The project ID can be found in your dashboard, %1$splease refer to this document%2$s for more information.', 'rekai-wordpress' ),
					'<a href="' . esc_url( 'https://docs.rek.ai/getting-started/installation#how-do-i-know-which-project-id-and-secret-key-my-project-has' ) . '" target="_blank" rel="noopener noreferrer">',
					'</a>'
				),
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
				'placeholder' => esc_html__( 'Secret Key', 'rekai-wordpress' ),
				'help'        => sprintf(
					/* translators: 1: is a link to a support document. 2: closing link */
					esc_html__( 'The secret key can be found in your dashboard, %1$splease refer to this document%2$s for more information.', 'rekai-wordpress' ),
					'<a href="' . esc_url( 'https://docs.rek.ai/getting-started/installation#how-do-i-know-which-project-id-and-secret-key-my-project-has' ) . '" target="_blank" rel="noopener noreferrer">',
					'</a>'
				),
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
				'general'  => array(
					'label' => esc_html__( 'General', 'rekai-wordpress' ),
					'url'   => add_query_arg( array( 'tab' => 'general' ), admin_url( 'admin.php?page=rekai-options' ) ),
				),
				'advanced' => array(
					'label' => esc_html__( 'Advanced', 'rekai-wordpress' ),
					'url'   => add_query_arg( array( 'tab' => 'advanced' ), admin_url( 'admin.php?page=rekai-options' ) ),
				),
			),
			'active_tab' => $tab,
		);
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo render_template( 'admin-settings', $data );
	}
}
