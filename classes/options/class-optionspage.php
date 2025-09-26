<?php
/**
 * Options page class.
 *
 * @package Rekai\Options
 */

namespace Rekai\Options;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Rekai\Singleton;
use function Rekai\render_checkbox_field;
use function Rekai\render_number_field;
use function Rekai\render_secret_field;
use function Rekai\render_radiobuttons_field;
use function Rekai\render_template;
use function Rekai\render_text_field;

/**
 * Options page class.
 *
 * @since 0.1.0
 */
class OptionsPage extends Singleton {
	/**
	 * Options that should be set to autoload.
	 *
	 * @var array
	 */
	public static array $autoload_options = array(
		'rekai_is_enabled',
		'rekai_embed_code',
		'rekai_autocomplete_mode',
		'rekai_autocomplete_automatic_selector',
		'rekai_autocomplete_nrofhits',
		'rekai_autocomplete_navigate_on_click',
		'rekai_autocomplete_usecurrentlang',
		'rekai_test_mode',
		'rekai_use_mock_data',
		'rekai_project_id',
		'rekai_secret_key',
	);

	/**
	 * Initializes the options page.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_init', array( $this, 'enqueue_assets' ) );
		add_action( 'updated_option', array( $this, 'updated_option' ), 10, 3 );
	}

	/**
	 * Adds the options page to the admin menu.
	 *
	 * @return void
	 */
	final public function add_page(): void {
		$hook_suffix = add_options_page(
			'Rekai Settings',
			'Rek.ai Settings',
			'manage_options',
			'rekai-settings',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Checks if cache should be cleared.
	 *
	 * @param string $option  The name of the option that was updated.
	 * @param mixed  $old_value The old value of the option.
	 * @param mixed  $value   The new value of the option.
	 *
	 * @return void
	 */
	final public function updated_option( $option, $old_value, $value ): void {
		if ( in_array( $option, self::$autoload_options ) && $old_value !== $value ) {
			wp_cache_delete( $option, 'options' );
			wp_cache_delete( 'alloptions', 'options' );
		}
	}

	/**
	 * Handles registering the settings.
	 *
	 * @return void
	 */
	final public function register_settings(): void {
		$this->register_general_section();
		$this->register_autocomplete_section();
		$this->register_selector_section();
		$this->register_advanced_section();
	}

	/**
	 * Add settings link to plugin listing.
	 *
	 * @param array $links Array of plugin action links.
	 * @return array Modified array of plugin action links.
	 */
	final public function settings_link( array $links ): array {
		// Build and escape the URL.
		$url = esc_url(
			add_query_arg(
				'page',
				'rekai-settings',
				get_admin_url() . 'admin.php'
			)
		);
		// Create the link.
		$settings_link = "<a href='$url'>" . __( 'Settings', 'rek-ai' ) . '</a>';
		// Add the link to the end of the array.
		array_unshift(
			$links,
			$settings_link
		);
		return $links;
	}

	/**
	 * Renders the General section.
	 *
	 * @return void
	 */
	final public function render_general_section(): void {
		echo '<p>' . esc_html__( 'General settings required for Rek.ai integration.', 'rek-ai' ) . '</p>';
	}

	/**
	 * Renders the Autocomplete section.
	 *
	 * @return void
	 */
	final public function render_autocomplete_section(): void {
		echo '<p>' . esc_html__( 'Settings for autocomplete.', 'rek-ai' ) . '</p>';
	}

	/**
	 * Renders the Autocomplete Selector section.
	 *
	 * @return void
	 */
	final public function render_selector_section(): void {
		echo '<p>' . esc_html__( 'Settings autocomplete selector.', 'rek-ai' ) . '</p>';
	}

	/**
	 * Renders the Advanced section.
	 *
	 * @return void
	 */
	final public function render_advanced_section(): void {
		echo '<p>' . esc_html__( 'Advanced settings for Rek.ai.', 'rek-ai' ) . '</p>';
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
				'placeholder' => __( 'Enable Rek.ai', 'rek-ai' ),
				'help'        => __( 'Should the Rek.ai scripts be loaded? With this disabled no Rek.ai functions work.', 'rek-ai' ),
			)
		);
	}

	/**
	 * Handles rendering the embed code field.
	 *
	 * @return void
	 */
	final public function render_embed_code_field(): void {
		render_text_field(
			array(
				'id'          => 'rekai_embed_code',
				'value'       => get_option( 'rekai_embed_code', '' ),
				'placeholder' => __( 'https://static.rekai.se/XXXXXXXX.js', 'rek-ai' ),
				'size'        => '40',
				'help'        => array(
					__( 'The embed code can be found in your dashboard,', 'rek-ai' ),
					__( 'please refer to this document for more information.', 'rek-ai' ),
					__( 'https://docs.rek.ai/dashboard-guide#embed-code', 'rek-ai' ),
				),
			)
		);
	}

	/**
	 * Renders the Autocomplete Enabled field.
	 *
	 * @return void
	 */
	final public function render_autocomplete_mode_field(): void {
		render_radiobuttons_field(
			array(
				'id'      => 'rekai_autocomplete_mode',
				'value'   => get_option( 'rekai_autocomplete_mode', 'disabled' ),
				'options' => array(
					'disabled' => __( 'Disabled', 'rek-ai' ),
					'auto'     => __( 'Enabled with selector', 'rek-ai' ),
					'manual'   => __( 'Enabled with custom script', 'rek-ai' ),
				),
				'help'    => __( 'Should the plugin load the autocomplete script', 'rek-ai' ),
			),
		);
	}

	/**
	 * Renders the Autocomplete selector field.
	 *
	 * @return void
	 */
	final public function render_autocomplete_selector_field(): void {
		render_text_field(
			array(
				'id'          => 'rekai_autocomplete_automatic_selector',
				'value'       => get_option( 'rekai_autocomplete_automatic_selector', 'input[name=s]' ),
				'placeholder' => __( '#search-input', 'rek-ai' ),
				'help'        => __( 'Enter the HTML selector for the field you want to enable autocomplete for.', 'rek-ai' ),
			)
		);
	}

	/**
	 * Renders the Autocomplete Use current language field.
	 *
	 * @return void
	 */
	final public function render_autocomplete_currentlang_field(): void {
		render_checkbox_field(
			array(
				'id'          => 'rekai_autocomplete_usecurrentlang',
				'value'       => get_option( 'rekai_autocomplete_usecurrentlang', '' ),
				'placeholder' => __( 'Use current language', 'rek-ai' ),
				'help'        => __( 'Use the current language for the autocomplete.', 'rek-ai' ),
			)
		);
	}

	/**
	 * Renders the Autocomplete Options field.
	 *
	 * @return void
	 */
	final public function render_autocomplete_nrofhits_field(): void {
		render_number_field(
			array(
				'id'          => 'rekai_autocomplete_nrofhits',
				'value'       => get_option( 'rekai_autocomplete_nrofhits', 10 ),
				'placeholder' => __( '10', 'rek-ai' ),
				'help'        => __( 'Number of results to show in the autocomplete dropdown.', 'rek-ai' ),
				'min'         => 1,
				'max'         => 100,
			)
		);
	}

	/**
	 * Renders the Autocomplete Options field.
	 *
	 * @return void
	 */
	final public function render_autocomplete_navigate_on_click_field(): void {
		render_checkbox_field(
			array(
				'id'          => 'rekai_autocomplete_navigate_on_click',
				'value'       => get_option( 'rekai_autocomplete_navigate_on_click', false ),
				'placeholder' => __( 'Navigate on click', 'rek-ai' ),
				'help'        => __( 'Navigate to the selected item when clicking on it.', 'rek-ai' ),
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
				'placeholder' => __( 'Test Mode', 'rek-ai' ),
				'help'        => __( 'Enables test mode. This will not send any data to Rek.ai.', 'rek-ai' ),
			)
		);
	}

	/**
	 * Handles rendering the use mock data field.
	 *
	 * @return void
	 */
	final public function render_use_mock_data_field(): void {
		render_checkbox_field(
			array(
				'id'          => 'rekai_use_mock_data',
				'value'       => get_option( 'rekai_use_mock_data', '' ),
				'placeholder' => __( 'Use Mock Data', 'rek-ai' ),
				'help'        => __( 'Use mock data instead of getting real data from Rek.ai.', 'rek-ai' ),
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
				'placeholder' => esc_html__( 'Project ID', 'rek-ai' ),
				'help'        => array(
					__( 'The project ID can be found in your dashboard, ', 'rek-ai' ),
					__( 'please refer to this document for more information.', 'rek-ai' ),
					__( 'https://docs.rek.ai/getting-started/installation#how-do-i-know-which-project-id-and-secret-key-my-project-has', 'rek-ai' ),
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
				'placeholder' => esc_html__( 'Secret Key', 'rek-ai' ),
				'help'        => array(
					__( 'The secret key can be found in your dashboard, ', 'rek-ai' ),
					__( 'please refer to this document for more information.', 'rek-ai' ),
					__( 'https://docs.rek.ai/getting-started/installation#how-do-i-know-which-project-id-and-secret-key-my-project-has', 'rek-ai' ),
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
		if ( ! isset( $_GET['page'] ) || 'rekai-settings' !== $_GET['page'] ) {
			return;
		}
		wp_enqueue_style( 'rekai-admin' );
		wp_enqueue_script( 'rekai-backend' );
	}

	/**
	 * Handles the rendering of the options page.
	 *
	 * @return void
	 */
	final public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'rek-ai' ) );
		}
		$tab  = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general';
		$data = array(
			'tabs'       => array(
				'general'  => array(
					'label' => esc_html__( 'General', 'rek-ai' ),
					'url'   => add_query_arg( array( 'tab' => 'general' ), admin_url( 'admin.php?page=rekai-settings' ) ),
				),
				'advanced' => array(
					'label' => esc_html__( 'Advanced', 'rek-ai' ),
					'url'   => add_query_arg( array( 'tab' => 'advanced' ), admin_url( 'admin.php?page=rekai-settings' ) ),
				),
				'docs'     => array(
					'label' => esc_html__( 'Documentation', 'rek-ai' ),
					'url'   => add_query_arg( array( 'tab' => 'docs' ), admin_url( 'admin.php?page=rekai-settings' ) ),
				),
			),
			'active_tab' => $tab,
		);
		render_template( 'admin-settings', $data );
	}

	/**
	 * Registers the general section settings.
	 *
	 * @return void
	 */
	final public function register_general_section(): void {
		$page    = 'rekai-tab-general';
		$section = 'rekai-general';

		$this->register_section(
			$page,
			$section,
			__( 'General', 'rek-ai' ),
			array( $this, 'render_general_section' ),
		);

		// Script Enabled.
		$this->register_field(
			$page,
			$section,
			'rekai_is_enabled',
			__( 'Load Scripts', 'rek-ai' ),
			'boolval',
			array( $this, 'render_enabled_field' ),
			array(
				'label_for' => 'rekai_is_enabled',
				'type'      => 'bool',
			)
		);

		// Embed Code.
		$this->register_field(
			$page,
			$section,
			'rekai_embed_code',
			__( 'Embed Code', 'rek-ai' ),
			array( $this, 'sanitize_embed_code' ),
			array( $this, 'render_embed_code_field' ),
			array(
				'label_for' => 'rekai_embed_code',
				'type'      => 'string',
			)
		);
	}

	/**
	 * Registers the autocomplete section settings.
	 *
	 * @return void
	 */
	final public function register_autocomplete_section(): void {
		$page    = 'rekai-tab-general';
		$section = 'rekai-autocomplete';

		$this->register_section(
			$page,
			$section,
			__( 'Autocomplete', 'rek-ai' ),
			array( $this, 'render_autocomplete_section' ),
		);

		$this->register_field(
			$page,
			$section,
			'rekai_autocomplete_mode',
			__( 'Autocomplete mode', 'rek-ai' ),
			array( $this, 'sanitize_autocomplete_mode' ),
			array( $this, 'render_autocomplete_mode_field' ),
			array(
				'type' => 'string',
			)
		);
	}

	/**
	 * Registers the selector section settings.
	 *
	 * @return void
	 */
	final public function register_selector_section(): void {
		$page    = 'rekai-tab-general';
		$section = 'rekai-selector';

		$this->register_section(
			$page,
			$section,
			__( 'Autocomplete Settings', 'rek-ai' ),
			array( $this, 'render_selector_section' )
		);

		$this->register_field(
			$page,
			$section,
			'rekai_autocomplete_automatic_selector',
			__( 'Autocomplete selector', 'rek-ai' ),
			'sanitize_text_field',
			array( $this, 'render_autocomplete_selector_field' ),
			array(
				'label_for' => 'rekai_autocomplete_automatic_selector',
			)
		);

		$this->register_field(
			$page,
			$section,
			'rekai_autocomplete_navigate_on_click',
			__( 'Open on click', 'rek-ai' ),
			'boolval',
			array( $this, 'render_autocomplete_navigate_on_click_field' ),
			array(
				'label_for' => 'rekai_autocomplete_navigate_on_click',
				'type'      => 'bool',
			)
		);

		$this->register_field(
			$page,
			$section,
			'rekai_autocomplete_usecurrentlang',
			__( 'Use current language', 'rek-ai' ),
			'boolval',
			array( $this, 'render_autocomplete_currentlang_field' ),
			array(
				'label_for' => 'rekai_autocomplete_usecurrentlang',
				'type'      => 'bool',
			)
		);

		$this->register_field(
			$page,
			$section,
			'rekai_autocomplete_nrofhits',
			__( 'Number of results', 'rek-ai' ),
			'intval',
			array( $this, 'render_autocomplete_nrofhits_field' ),
			array(
				'label_for' => 'rekai_autocomplete_nrofhits',
				'type'      => 'number',
				'default'   => 10,
			)
		);
	}

	/**
	 * Registers the advanced settings section.
	 *
	 * @return void
	 */
	final public function register_advanced_section(): void {
		$page    = 'rekai-tab-advanced';
		$section = 'rekai-advanced';

		$this->register_section(
			$page,
			$section,
			__( 'Advanced', 'rek-ai' ),
			array( $this, 'render_advanced_section' ),
		);

		$this->register_field(
			$page,
			$section,
			'rekai_test_mode',
			__( 'Test Mode', 'rek-ai' ),
			'boolval',
			array( $this, 'render_test_mode_field' ),
			array(
				'label_for' => 'rekai_test_mode',
				'type'      => 'bool',
			)
		);

		$this->register_field(
			$page,
			$section,
			'rekai_use_mock_data',
			__( 'Use Mock Data', 'rek-ai' ),
			'boolval',
			array( $this, 'render_use_mock_data_field' ),
			array(
				'label_for' => 'rekai_use_mock_data',
				'type'      => 'bool',
			)
		);

		$this->register_field(
			$page,
			$section,
			'rekai_project_id',
			__( 'Project ID', 'rek-ai' ),
			'sanitize_text_field',
			array( $this, 'render_project_id_field' ),
			array(
				'label_for' => 'rekai_project_id',
			)
		);

		$this->register_field(
			$page,
			$section,
			'rekai_secret_key',
			__( 'Secret Key', 'rek-ai' ),
			'sanitize_text_field',
			array( $this, 'render_secret_key_field' ),
			array(
				'label_for' => 'rekai_secret_key',
			)
		);
	}

	/**
	 * Registers a field with the WordPress settings API.
	 *
	 * This helper method simplifies the registration of a field by wrapping
	 * the register_setting and add_settings_field functions in a single call.
	 *
	 * @param string   $page     The slug-name of the settings page on which to show the section.
	 * @param string   $section  The slug-name of the section of the settings page in which to show the field.
	 * @param string   $id       The ID of the field to register.
	 * @param string   $title    The title of the field to display.
	 * @param callable $sanitize The sanitization callback function.
	 * @param callable $render   The rendering callback function.
	 * @param array    $args     Additional arguments to pass to add_settings_field.
	 *
	 * @return void
	 */
	final public function register_field(
		$page,
		$section,
		$id,
		$title,
		$sanitize,
		$render,
		$args
	) {
		register_setting(
			$page,
			$id,
			array( 'sanitize_callback' => $sanitize )
		);
		add_settings_field(
			$id,
			$title,
			$render,
			$page,
			$section,
			$args
		);
	}

	/**
	 * Registers a section with the WordPress settings API.
	 *
	 * This helper method simplifies the registration of a section.
	 *
	 * @param string   $page     The slug-name of the settings page on which to show the section.
	 * @param string   $section  The slug-name of the section to register.
	 * @param string   $title    The title of the section to display.
	 * @param callable $render   The rendering callback function.
	 *
	 * @return void
	 */
	final public function register_section( $page, $section, $title, $render ) {
		add_settings_section(
			$section,
			$title,
			$render,
			$page,
			array(
				'before_section' => '<section id="' . $section . '-section">',
				'after_section'  => '</section>',
			)
		);
	}

	/**
	 * Sanitizes an embed code. Converts a code to a static URL if necessary.
	 *
	 * Accepts either:
	 * - Just the hex code (e.g. '1234abcd')
	 * - Full URL (e.g. 'https://static.rekai.se/1234abcd.js')
	 *
	 * Will convert hex codes to full URLs and validate full URLs match expected format.
	 * Returns empty string if input is invalid.
	 *
	 * @param string $input The embed code to sanitize.
	 * @return string The sanitized embed code URL or empty string if invalid.
	 */
	public function sanitize_embed_code( string $input ): string {
		if ( preg_match( '/^[0-9a-f]{4,}$/', $input, $matches ) ) {
			return 'https://static.rekai.se/' . $matches[0] . '.js';
		}
		if ( preg_match( '/https:\/\/[^\/]+\/[0-9a-f]{4,}\.js/', $input, $matches ) ) {
			return esc_url( $matches[0] );
		}
		return '';
	}

	/**
	 * Sanitizes the autocomplete mode. Only allows 'auto', 'manual' or 'disabled'.
	 *
	 * @param string $input The autocomplete mode to sanitize.
	 * @return string The sanitized autocomplete mode.
	 */
	public function sanitize_autocomplete_mode( string $input ): string {
		if ( $input === 'auto' || $input === 'manual' ) {
			return $input;
		}
		return 'disabled';
	}
}
