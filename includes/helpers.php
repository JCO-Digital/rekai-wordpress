<?php
/**
 * Helper functions for the plugin.
 *
 * @package Rekai
 */

namespace Rekai;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register script wrapper.
 *
 * @param string $name Script name.
 * @param string $file Filename.
 * @param array  $dependencies Dependencies.
 * @param string $version Optional version number.
 */
function script_register( string $name, string $file, array $dependencies = array(), string $version = '' ): void {
	$info = get_file_info( $file, $version );

	if ( false !== $info ) {
		wp_register_script(
			$name,
			$info['uri'],
			$dependencies,
			$info['version'],
			true
		);
	}
}

/**
 * Register style wrapper.
 *
 * @param string $name Style name.
 * @param string $file Filename.
 * @param array  $dependencies Dependencies.
 * @param string $version Optional version number.
 */
function style_register( string $name, string $file, array $dependencies = array(), string $version = '' ): void {
	$info = get_file_info( $file, $version );

	if ( false !== $info ) {
		wp_register_style(
			$name,
			$info['uri'],
			$dependencies,
			$info['version']
		);
	}
}

/**
 * Get file info for script/style registration.
 *
 * @param string $file Filename.
 * @param string $version Optional version number.
 *
 * @return bool|string[]
 */
function get_file_info( string $file, string $version = '' ): array|bool {
	if ( ! empty( $version ) ) {
		$version .= '-';
	}
	$location = array(
		'path' => join_path( untrailingslashit( REKAI_PLUGIN_PATH ), $file ),
		'uri'  => join_path( untrailingslashit( REKAI_PLUGIN_URI ), $file ),
	);
	if ( file_exists( $location['path'] ) ) {
		$version .= filemtime( $location['path'] );

		return array(
			'uri'     => $location['uri'],
			'path'    => $location['path'],
			'version' => $version,
		);
	}
	return false;
}

/**
 * A function that joins together all parts of a path.
 *
 * @param string $path Base path.
 * @param string ...$parts Path parts to be joined.
 *
 * @return string
 */
function join_path( string $path, string ...$parts ): string {
	foreach ( $parts as $part ) {
		$path .= '/' . trim( $part, '/ ' );
	}

	return $path;
}

/**
 * Can be used to render a template file.
 *
 * @param string $template The template file name. E.g. "my-template".
 * @param array  $data An array of data to be passed to the template. Will be available as $rek_my_variable.
 *
 * @return void
 */
function render_template( string $template, array $data = array() ): void {
	// Validate that $template contains only aplhanumeric characters or dash.
	if ( ! preg_match( '/^[a-z0-9-\/]+$/', $template ) ) {
		echo 'Error: Invalid template name';
		return;
	}

	$final_path = sprintf( '%s/views/%s.php', untrailingslashit( REKAI_PLUGIN_PATH ), $template );
	if ( ! file_exists( $final_path ) ) {
		echo 'Error: Template not found';
		return;
	}
	extract( $data, EXTR_PREFIX_ALL, 'rek' ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
	require $final_path;
}

function render_help( string|array $help ): void {
	if ( is_string( $help ) ) {
		echo esc_html( $help );
	} elseif ( count( $help ) >= 3 ) {
		printf(
			'%1$s <a href="%3$s" target="_blank" rel="noopener noreferrer">%2$s</a>',
			esc_html( $help[0] ),
			esc_html( $help[1] ),
			esc_url( $help[2] )
		);
	}
}
