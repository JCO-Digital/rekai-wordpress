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
use function Rekai\render_switch_field;
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
		'rekai_autocomplete_enabled',
		'rekai_autocomplete_automatic',
		'rekai_autocomplete_automatic_selector',
		'rekai_autocomplete_usecurrentlang',
		'rekai_autocomplete_nrofhits',
		'rekai_autocomplete_navigate_on_click',
		'rekai_test_mode',
		'rekai_use_mock_data',
		'rekai_project_id',
		'rekai_secret_key',
	);

	/**
	 * An array of all sections.
	 *
	 * @var array|string[]
	 */
	private array $sections = array(
		'general'                => 'rekai-settings-general',
		'autocomplete'           => 'rekai-settings-autocomplete',
		'autocomplete_automatic' => 'rekai-settings-autocomplete-automatic',
		'advanced'               => 'rekai-settings-advanced',
		'docs'                   => 'rekai-settings-docs',
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
		wp_prime_option_caches_by_group( $this->sections['general'] );
		wp_prime_option_caches_by_group( $this->sections['autocomplete'] );
		wp_prime_option_caches_by_group( $this->sections['autocomplete_automatic'] );
		wp_prime_option_caches_by_group( $this->sections['advanced'] );
	}

	/**
	 * Handles registering the settings.
	 *
	 * @return void
	 */
	final public function register_settings(): void {
		$this->register_general_section();
		$this->register_autocomplete_section();
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
		$settings_link = "<a href='$url'>" . __( 'Settings', 'rekai-wordpress' ) . '</a>';
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
		echo '<p>' . esc_html__( 'General settings required for Rek.ai integration.', 'rekai-wordpress' ) . '</p>';
	}

	/**
	 * Renders the Autocomplete section.
	 *
	 * @return void
	 */
	final public function render_autocomplete_section(): void {
		echo '<p>' . esc_html__( 'Settings for autocomplete search.', 'rekai-wordpress' ) . '</p>';
	}

	/**
	 * Handles rendering the Automatic section.
	 *
	 * @return void
	 */
	final public function render_autocomplete_automatic_section(): void {
		echo '<p>' . esc_html__( 'Automatic mode will load the autocomplete script and attach to the provided input.', 'rekai-wordpress' ) . '</p>';
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
	final public function render_embed_code_field(): void {
		render_text_field(
			array(
				'id'          => 'rekai_embed_code',
				'value'       => get_option( 'rekai_embed_code', '' ),
				'placeholder' => esc_html__( 'https://static.rekai.se/XXXXXXXX.js', 'rekai-wordpress' ),
				'size'        => '40',
				'help'        => sprintf(
					/* translators: 1: is a link to a support document. 2: closing link */
					esc_html__( 'The embed code can be found in your dashboard, %1$splease refer to this document%2$s for more information.', 'rekai-wordpress' ),
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
	final public function render_autocomplete_enabled_field(): void {
		render_checkbox_field(
			array(
				'id'          => 'rekai_autocomplete_enabled',
				'value'       => get_option( 'rekai_autocomplete_enabled', '' ),
				'placeholder' => esc_html__( 'Enable Autocomplete', 'rekai-wordpress' ),
				'help'        => esc_html__( 'Enables autocomplete for the Rek.ai plugin.', 'rekai-wordpress' ),
			)
		);
	}

	/**
	 * Renders the Autocomplete Enabled field.
	 *
	 * @return void
	 */
	final public function render_autocomplete_mode_field(): void {
		render_switch_field(
			array(
				'id'          => 'rekai_autocomplete_automatic',
				'value'       => get_option( 'rekai_autocomplete_automatic', '' ),
				'placeholder' => esc_html__( 'Autocomplete mode', 'rekai-wordpress' ),
				'help'        => esc_html__( 'Select the mode for the autocomplete.', 'rekai-wordpress' ),
				'on_text'     => esc_html__( 'Auto', 'rekai-wordpress' ),
				'off_text'    => esc_html__( 'Manual', 'rekai-wordpress' ),
			)
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
				'value'       => get_option( 'rekai_autocomplete_automatic_selector', '' ),
				'placeholder' => esc_html__( 'Autocomplete selector', 'rekai-wordpress' ),
				'help'        => esc_html__( 'Insert the selector for the autocomplete.', 'rekai-wordpress' ),
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
				'placeholder' => esc_html__( 'Use current language', 'rekai-wordpress' ),
				'help'        => esc_html__( 'Use the current language for the autocomplete.', 'rekai-wordpress' ),
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
				'placeholder' => esc_html__( '10', 'rekai-wordpress' ),
				'help'        => esc_html__( 'Number of results to show in the autocomplete dropdown.', 'rekai-wordpress' ),
				'min'         => 1,
				'max'         => 10,
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
				'placeholder' => esc_html__( 'Navigate on click', 'rekai-wordpress' ),
				'help'        => esc_html__( 'Navigate to the selected item when clicking on it.', 'rekai-wordpress' ),
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
	 * Handles rendering the use mock data field.
	 *
	 * @return void
	 */
	final public function render_use_mock_data_field(): void {
		render_checkbox_field(
			array(
				'id'          => 'rekai_use_mock_data',
				'value'       => get_option( 'rekai_use_mock_data', '' ),
				'placeholder' => esc_html__( 'Use Mock Data', 'rekai-wordpress' ),
				'help'        => esc_html__( 'Use mock data instead of getting real data from Rek.ai.', 'rekai-wordpress' ),
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
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'rekai-wordpress' ) );
		}
		$tab  = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$data = array(
			'tabs'       => array(
				'general'      => array(
					'label' => esc_html__( 'General', 'rekai-wordpress' ),
					'url'   => add_query_arg( array( 'tab' => 'general' ), admin_url( 'admin.php?page=rekai-settings' ) ),
				),
				'autocomplete' => array(
					'label' => esc_html__( 'Autocomplete', 'rekai-wordpress' ),
					'url'   => add_query_arg( array( 'tab' => 'autocomplete' ), admin_url( 'admin.php?page=rekai-settings' ) ),
				),
				'advanced'     => array(
					'label' => esc_html__( 'Advanced', 'rekai-wordpress' ),
					'url'   => add_query_arg( array( 'tab' => 'advanced' ), admin_url( 'admin.php?page=rekai-settings' ) ),
				),
				'docs'         => array(
					'label' => esc_html__( 'Documentation', 'rekai-wordpress' ),
					'url'   => add_query_arg( array( 'tab' => 'docs' ), admin_url( 'admin.php?page=rekai-settings' ) ),
				),
				'shortcode'    => array(
					'label' => esc_html__( 'Shortcode Generator', 'rekai-wordpress' ),
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
		register_setting(
			$this->sections['general'],
			'rekai_is_enabled',
			array( 'sanitize_callback' => 'boolval' )
		);
		register_setting(
			$this->sections['general'],
			'rekai_embed_code',
			array( 'sanitize_callback' => array( $this, 'sanitize_embed_code' ) )
		);

		add_settings_section(
			'rekai-general',
			__( 'General', 'rekai-wordpress' ),
			array( $this, 'render_general_section' ),
			$this->sections['general'],
		);

		add_settings_field(
			'rekai_is_enabled',
			__( 'Enabled', 'rekai-wordpress' ),
			array( $this, 'render_enabled_field' ),
			'rekai-settings-general',
			'rekai-general',
			array(
				'label_for' => 'rekai_is_enabled',
				'type'      => 'bool',
			)
		);
		add_settings_field(
			'rekai_embed_code',
			__( 'Embed Code', 'rekai-wordpress' ),
			array( $this, 'render_embed_code_field' ),
			$this->sections['general'],
			'rekai-general',
			array(
				'label_for' => 'rekai_embed_code',
			)
		);
	}

	/**
	 * Registers the advanced settings section.
	 *
	 * @return void
	 */
	final public function register_advanced_section(): void {
		register_setting(
			$this->sections['advanced'],
			'rekai_test_mode',
			array( 'sanitize_callback' => 'boolval' )
		);
		register_setting(
			$this->sections['advanced'],
			'rekai_use_mock_data',
			array( 'sanitize_callback' => 'boolval' )
		);
		register_setting(
			$this->sections['advanced'],
			'rekai_project_id',
			array( 'sanitize_callback' => 'sanitize_text_field' )
		);
		register_setting(
			$this->sections['advanced'],
			'rekai_secret_key',
			array( 'sanitize_callback' => 'sanitize_text_field' )
		);

		add_settings_section(
			'rekai-advanced',
			__( 'Advanced', 'rekai-wordpress' ),
			array( $this, 'render_advanced_section' ),
			$this->sections['advanced'],
		);

		add_settings_field(
			'rekai_test_mode',
			__( 'Test Mode', 'rekai-wordpress' ),
			array( $this, 'render_test_mode_field' ),
			$this->sections['advanced'],
			'rekai-advanced',
			array(
				'label_for' => 'rekai_test_mode',
				'type'      => 'bool',
			)
		);
		add_settings_field(
			'rekai_use_mock_data',
			__( 'Use Mock Data', 'rekai-wordpress' ),
			array( $this, 'render_use_mock_data_field' ),
			$this->sections['advanced'],
			'rekai-advanced',
			array(
				'label_for' => 'rekai_use_mock_data',
				'type'      => 'bool',
			)
		);
		add_settings_field(
			'rekai_project_id',
			__( 'Project ID', 'rekai-wordpress' ),
			array( $this, 'render_project_id_field' ),
			$this->sections['advanced'],
			'rekai-advanced',
			array(
				'label_for' => 'rekai_project_id',
			)
		);
		add_settings_field(
			'rekai_secret_key',
			__( 'Secret Key', 'rekai-wordpress' ),
			array( $this, 'render_secret_key_field' ),
			$this->sections['advanced'],
			'rekai-advanced',
			array(
				'label_for' => 'rekai_secret_key',
			)
		);
	}

	/**
	 * Registers the autocomplete settings section.
	 *
	 * @return void
	 */
	final public function register_autocomplete_section(): void {
		register_setting(
			$this->sections['autocomplete'],
			'rekai_autocomplete_enabled',
			array( 'sanitize_callback' => 'boolval' )
		);
		register_setting(
			$this->sections['autocomplete'],
			'rekai_autocomplete_automatic',
			array( 'sanitize_callback' => 'boolval' )
		);
		register_setting(
			$this->sections['autocomplete_automatic'],
			'rekai_autocomplete_automatic_selector',
			array( 'sanitize_callback' => 'sanitize_text_field' )
		);
		register_setting(
			$this->sections['autocomplete_automatic'],
			'rekai_autocomplete_navigate_on_click',
			array( 'sanitize_callback' => 'boolval' )
		);
		register_setting(
			$this->sections['autocomplete_automatic'],
			'rekai_autocomplete_usecurrentlang',
			array( 'sanitize_callback' => 'boolval' )
		);
		register_setting(
			$this->sections['autocomplete_automatic'],
			'rekai_autocomplete_nrofhits',
			array( 'sanitize_callback' => 'intval' )
		);

		add_settings_section(
			'rekai-autocomplete',
			__( 'Autocomplete', 'rekai-wordpress' ),
			array( $this, 'render_autocomplete_section' ),
			$this->sections['autocomplete']
		);

		add_settings_section(
			'rekai-autocomplete-automatic',
			__( 'Automatic settings', 'rekai-wordpress' ),
			array( $this, 'render_autocomplete_automatic_section' ),
			$this->sections['autocomplete_automatic']
		);

		add_settings_field(
			'rekai_autocomplete_enabled',
			__( 'Enabled', 'rekai-wordpress' ),
			array( $this, 'render_autocomplete_enabled_field' ),
			$this->sections['autocomplete'],
			'rekai-autocomplete',
			array(
				'label_for' => 'rekai_autocomplete_enabled',
				'type'      => 'bool',
			)
		);
		add_settings_field(
			'rekai_autocomplete_automatic',
			__( 'Autocomplete mode', 'rekai-wordpress' ),
			array( $this, 'render_autocomplete_mode_field' ),
			$this->sections['autocomplete'],
			'rekai-autocomplete',
			array(
				'label_for' => 'rekai_autocomplete_automatic',
				'type'      => 'bool',
			)
		);
		add_settings_field(
			'rekai_autocomplete_automatic_selector',
			__( 'Autocomplete selector', 'rekai-wordpress' ),
			array( $this, 'render_autocomplete_selector_field' ),
			$this->sections['autocomplete_automatic'],
			'rekai-autocomplete-automatic',
			array(
				'label_for' => 'rekai_autocomplete_automatic_selector',
			)
		);
		add_settings_field(
			'rekai_autocomplete_navigate_on_click',
			__( 'Open on click', 'rekai-wordpress' ),
			array( $this, 'render_autocomplete_navigate_on_click_field' ),
			$this->sections['autocomplete_automatic'],
			'rekai-autocomplete-automatic',
			array(
				'label_for' => 'rekai_autocomplete_navigate_on_click',
				'type'      => 'bool',
			)
		);
		add_settings_field(
			'rekai_autocomplete_usecurrentlang',
			__( 'Use current language', 'rekai-wordpress' ),
			array( $this, 'render_autocomplete_currentlang_field' ),
			$this->sections['autocomplete_automatic'],
			'rekai-autocomplete-automatic',
			array(
				'label_for' => 'rekai_autocomplete_usecurrentlang',
				'type'      => 'bool',
			)
		);
		add_settings_field(
			'rekai_autocomplete_nrofhits',
			__( 'Number of results', 'rekai-wordpress' ),
			array( $this, 'render_autocomplete_nrofhits_field' ),
			$this->sections['autocomplete_automatic'],
			'rekai-autocomplete-automatic',
			array(
				'label_for' => 'rekai_autocomplete_nrofhits',
				'type'      => 'number',
				'default'   => 10,
			)
		);
	}

	/**
	 * Handles getting all settings and returning them as a json encoded string.
	 *
	 * @return string
	 */
	final public function get_alpine_settings(): string {
		global $wp_settings_sections, $wp_settings_fields;
		$final_array = array();
		foreach ( $this->sections as $_section ) {
			if ( ! isset( $wp_settings_sections[ $_section ] ) ) {
				continue;
			}
			foreach ( (array) $wp_settings_sections[ $_section ] as $section ) {
				if ( ! isset( $section['id'] ) ) {
					continue;
				}
				if ( ! isset( $wp_settings_fields[ $_section ][ $section['id'] ] ) ) {
					continue;
				}
				foreach ( (array) $wp_settings_fields[ $_section ][ $section['id'] ] as $field ) {
					if ( ! isset( $field['id'] ) ) {
						continue;
					}
					$type          = $field['args']['type'] ?? 'string';
					$default_value = $field['args']['default'] ?? '';
					$value         = match ( $type ) {
						'bool' => get_option( $field['id'] ) === '1',
						'number' => (int) get_option( $field['id'], $default_value ),
						default => get_option( $field['id'], $default_value ),
					};
					$final_array[ $field['id'] ] = $value;
				}
			}
		}
		return wp_json_encode( $final_array );
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
	 */public function sanitize_embed_code( string $input ): string {
		if ( preg_match( '/^[0-9a-f]{4,}$/', $input, $matches ) ) {
			return 'https://static.rekai.se/' . $matches[0] . '.js';
		}
		if ( preg_match( '/https:\/\/[^\/]+\/[0-9a-f]{4,}\.js/', $input, $matches ) ) {
			return esc_url( $matches[0] );
		}
		return '';
	}
}
