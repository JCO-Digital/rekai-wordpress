<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Shortcode generator page class.
 *
 * @package Rekai\Options
 */

namespace Rekai\Options;

use Rekai\Singleton;
use function Rekai\render_text_field;
use function Rekai\render_number_field;
use function Rekai\render_checkbox_field;
use function Rekai\render_template;

/**
 * Shortcode generator page class.
 *
 * @since 0.1.0
 */
class ShortcodeGenerator extends Singleton {
	/**
	 * Initializes the shortcode generator page.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		add_action( 'admin_init', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Adds the shortcode generator page to the tools menu.
	 *
	 * @return void
	 */
	final public function add_page(): void {
		add_management_page(
			'Rek.ai Shortcode Generator',
			'Rek.ai Shortcodes',
			'manage_options',
			'rekai-shortcodes',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Enqueues the assets for the shortcode generator page.
	 *
	 * @return void
	 */
	final public function enqueue_assets(): void {
		wp_enqueue_style( 'rekai-admin' );
		wp_enqueue_script( 'rekai-backend' );
	}

	/**
	 * Handles the rendering of the shortcode generator page.
	 *
	 * @return void
	 */
	final public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'rekai-wordpress' ) );
		}

		$data = array(
			'shortcode_types'   => array(
				'prediction' => array(
					'label'       => esc_html__( 'Prediction', 'rekai-wordpress' ),
					'shortcode'   => 'rek-prediction',
					'description' => esc_html__( 'Generate a prediction shortcode for displaying Rek.ai predictions.', 'rekai-wordpress' ),
				),
				'qna'        => array(
					'label'       => esc_html__( 'Questions & Answers', 'rekai-wordpress' ),
					'shortcode'   => 'rek-qna',
					'description' => esc_html__( 'Generate a Q&A shortcode for displaying Rek.ai questions and answers.', 'rekai-wordpress' ),
				),
			),
			'common_attributes' => array(
				'nrofhits'     => array(
					'label'   => esc_html__( 'Number of Hits', 'rekai-wordpress' ),
					'type'    => 'number',
					'default' => 5,
					'min'     => 1,
					'max'     => 10,
				),
				'subtree'      => array(
					'label'       => esc_html__( 'Subtree', 'rekai-wordpress' ),
					'type'        => 'text',
					'placeholder' => '/news',
				),
				'tags'         => array(
					'label'       => esc_html__( 'Tags', 'rekai-wordpress' ),
					'type'        => 'text',
					'placeholder' => 'tag1,tag2,tag3',
				),
				'allowedlangs' => array(
					'label'       => esc_html__( 'Allowed Languages', 'rekai-wordpress' ),
					'type'        => 'text',
					'placeholder' => 'sv,fi',
				),
			),
		);

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo render_template( 'admin-shortcode-generator', $data );
	}
}
