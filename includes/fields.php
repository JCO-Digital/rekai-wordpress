<?php
/**
 * Generic handlers for different field types.
 *
 * @package Rekai
 */

namespace Rekai;

/**
 * Renders a text field.
 *
 * Echoes the rendered field.
 *
 * @param array $args {
 *     Optional. Array of field arguments.
 *
 *     @type string $id    The ID of the field. Default empty string.
 *     @type string $value The value of the field. Default empty string.
 *     @type string $placeholder The placeholder of the field. Default empty string.
 *     @type string $help The help text of the field. Default empty string.
 * }
 * @return void
 */
function render_text_field( array $args = array() ): void {
	$data = wp_parse_args(
		$args,
		array(
			'id'          => '',
			'value'       => '',
			'placeholder' => '',
			'help'        => '',
		)
	);
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo render_template( 'fields/text', $data );
}

/**
 * Renders a secret field. This field is a text field when the value is empty.
 * Otherwise, it is a password field.
 *
 * Echoes the rendered field.
 *
 * @param array $args {
 *     Optional. Array of field arguments.
 *
 *     @type string $id    The ID of the field. Default empty string.
 *     @type string $value The value of the field. Default empty string.
 *     @type string $placeholder The placeholder of the field. Default empty string.

 * }
 * @return void
 */
function render_secret_field( array $args = array() ): void {
	$data = wp_parse_args(
		$args,
		array(
			'id'          => '',
			'value'       => '',
			'placeholder' => '',
			'help'        => '',
		)
	);
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo render_template( 'fields/secret', $data );
}

/**
 * Renders a checkbox field.
 *
 * Echoes the rendered field.
 *
 * @param array $args {
 *     Optional. Array of field arguments.
 *
 *     @type string $id    The ID of the field. Default empty string.
 *     @type string $value The value of the field. Default empty string.
 * }
 *
 * @return void
 */
function render_checkbox_field( array $args = array() ): void {
	$data = wp_parse_args(
		$args,
		array(
			'id'    => '',
			'value' => '',
			'help'  => '',
		)
	);
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo render_template( 'fields/checkbox', $data );
}
