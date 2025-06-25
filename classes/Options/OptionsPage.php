<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Options page class.
 *
 * @package Rekai\Options
 */

namespace Rekai\Options;

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
		add_action( "load-{$hook_suffix}", array( $this, 'prime_options' ) );
	}

	/**
	 * Primes the option caches for various groups of settings.
	 *
	 * @return void
	 */
	final public function prime_options(): void {
		// wp_prime_option_caches_by_group( $this->sections['general'] );
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
		$settings_link = "<a href='$url'>" . __( 'Settings', 'rekai' ) . '</a>';
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
		echo '<p>' . esc_html__( 'General settings required for Rek.ai integration.', 'rekai' ) . '</p>';
	}

	/**
	 * Renders the Autocomplete section.
	 *
	 * @return void
	 */
	final public function render_autocomplete_section(): void {
		echo '<p>' . esc_html__( 'Settings for autocomplete.', 'rekai' ) . '</p>';
	}

	/**
	 * Renders the Autocomplete Selector section.
	 *
	 * @return void
	 */
	final public function render_selector_section(): void {
		echo '<p>' . esc_html__( 'Settings autocomplete selector.', 'rekai' ) . '</p>';
	}

	/**
	 * Renders the Advanced section.
	 *
	 * @return void
	 */
	final public function render_advanced_section(): void {
		echo '<p>' . esc_html__( 'Advanced settings for Rek.ai.', 'rekai' ) . '</p>';
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
				'placeholder' => esc_html__( 'Enable Rek.ai', 'rekai' ),
				'help'        => esc_html__( 'Should the Rek.ai scripts be loaded? With this disabled no Rek.ai functions work.', 'rekai' ),
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
				'placeholder' => esc_html__( 'https://static.rekai.se/XXXXXXXX.js', 'rekai' ),
				'size'        => '40',
				'help'        => sprintf(
					/* translators: 1: is a link to a support document. 2: closing link */
					esc_html__( 'The embed code can be found in your dashboard, %1$splease refer to this document%2$s for more information.', 'rekai' ),
					'<a href="' . esc_url( 'https://docs.rek.ai/dashboard-guide#embed-code' ) . '" target="_blank" rel="noopener noreferrer">',
					'</a>'
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
					'disabled' => esc_html__( 'Disabled', 'rekai' ),
					'auto'     => esc_html__( 'Enabled with selector', 'rekai' ),
					'manual'   => esc_html__( 'Enabled with custom script', 'rekai' ),
				),
				'help'    => esc_html__( 'Should the plugin load the autocomplete script', 'rekai' ),
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
				'placeholder' => esc_html__( '#search-input', 'rekai' ),
				'help'        => esc_html__( 'Enter the HTML selector for the field you want to enable autocomplete for.', 'rekai' ),
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
				'placeholder' => esc_html__( 'Use current language', 'rekai' ),
				'help'        => esc_html__( 'Use the current language for the autocomplete.', 'rekai' ),
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
				'placeholder' => esc_html__( '10', 'rekai' ),
				'help'        => esc_html__( 'Number of results to show in the autocomplete dropdown.', 'rekai' ),
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
				'placeholder' => esc_html__( 'Navigate on click', 'rekai' ),
				'help'        => esc_html__( 'Navigate to the selected item when clicking on it.', 'rekai' ),
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
				'placeholder' => esc_html__( 'Test Mode', 'rekai' ),
				'help'        => esc_html__( 'Enables test mode. This will not send any data to Rek.ai.', 'rekai' ),
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
				'placeholder' => esc_html__( 'Use Mock Data', 'rekai' ),
				'help'        => esc_html__( 'Use mock data instead of getting real data from Rek.ai.', 'rekai' ),
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
				'placeholder' => esc_html__( 'Project ID', 'rekai' ),
				'help'        => sprintf(
					/* translators: 1: is a link to a support document. 2: closing link */
					esc_html__( 'The project ID can be found in your dashboard, %1$splease refer to this document%2$s for more information.', 'rekai' ),
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
				'placeholder' => esc_html__( 'Secret Key', 'rekai' ),
				'help'        => sprintf(
					/* translators: 1: is a link to a support document. 2: closing link */
					esc_html__( 'The secret key can be found in your dashboard, %1$splease refer to this document%2$s for more information.', 'rekai' ),
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
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
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
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'rekai' ) );
		}
		$tab  = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$data = array(
			'tabs'       => array(
				'general'   => array(
					'label' => esc_html__( 'General', 'rekai' ),
					'url'   => add_query_arg( array( 'tab' => 'general' ), admin_url( 'admin.php?page=rekai-settings' ) ),
				),
				'advanced'  => array(
					'label' => esc_html__( 'Advanced', 'rekai' ),
					'url'   => add_query_arg( array( 'tab' => 'advanced' ), admin_url( 'admin.php?page=rekai-settings' ) ),
				),
				'docs'      => array(
					'label' => esc_html__( 'Documentation', 'rekai' ),
					'url'   => add_query_arg( array( 'tab' => 'docs' ), admin_url( 'admin.php?page=rekai-settings' ) ),
				),
				'shortcode' => array(
					'label' => esc_html__( 'Shortcode Generator', 'rekai' ),
					'icon'  => 'dashicons dashicons-external',
					'url'   => admin_url( 'admin.php?page=rekai-shortcodes' ),
				),
			),
			'active_tab' => $tab,
		);
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo render_template( 'admin-settings', $data );
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
			__( 'General', 'rekai' ),
			array( $this, 'render_general_section' ),
		);

		// Enabled
		$this->register_field(
			$page,
			$section,
			'rekai_is_enabled',
			__( 'Load Scripts', 'rekai' ),
			'boolval',
			array( $this, 'render_enabled_field' ),
			array(
				'label_for' => 'rekai_is_enabled',
				'type'      => 'bool',
			)
		);

		// Embed Code
		$this->register_field(
			$page,
			$section,
			'rekai_embed_code',
			__( 'Embed Code', 'rekai' ),
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
			__( 'Autocomplete', 'rekai' ),
			array( $this, 'render_autocomplete_section' ),
		);

		$this->register_field(
			$page,
			$section,
			'rekai_autocomplete_mode',
			__( 'Autocomplete mode', 'rekai' ),
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
			__( 'Autocomplete Settings', 'rekai' ),
			array( $this, 'render_selector_section' )
		);

		$this->register_field(
			$page,
			$section,
			'rekai_autocomplete_automatic_selector',
			__( 'Autocomplete selector', 'rekai' ),
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
			__( 'Open on click', 'rekai' ),
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
			__( 'Use current language', 'rekai' ),
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
			__( 'Number of results', 'rekai' ),
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
			__( 'Advanced', 'rekai' ),
			array( $this, 'render_advanced_section' ),
		);

		$this->register_field(
			$page,
			$section,
			'rekai_test_mode',
			__( 'Test Mode', 'rekai' ),
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
			__( 'Use Mock Data', 'rekai' ),
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
			__( 'Project ID', 'rekai' ),
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
			__( 'Secret Key', 'rekai' ),
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
	 * @param string $input The embed code to sanitize
	 * @return string The sanitized embed code URL or empty string if invalid
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

	public function sanitize_autocomplete_mode( string $input ): string {
		if ( $input === 'auto' || $input === 'manual' ) {
			return $input;
		}
		return 'disabled';
	}
}
