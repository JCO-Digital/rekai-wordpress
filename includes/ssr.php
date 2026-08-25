<?php
/**
 * Server-side rendering support for Questions & Answers.
 *
 * @package Rekai
 */

namespace Rekai;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default base URL for the Rek.ai predict API, used when no embed code is configured.
 */
const REKAI_PREDICT_DEFAULT_URL = 'https://predict.rekai.se';

/**
 * Resolves the effective Q&A render mode from a block/shortcode-level override and the
 * site-wide default setting.
 *
 * @param string $override Block/shortcode-level override: 'default', 'server', or 'client'.
 *
 * @return string Either 'server' or 'client'.
 */
function resolve_qna_render_mode( string $override = 'default' ): string {
	if ( 'server' === $override || 'client' === $override ) {
		return $override;
	}
	return 'client' === get_option( 'rekai_qna_render_mode', 'server' ) ? 'client' : 'server';
}

/**
 * Gets the base URL for the Rek.ai predict API.
 *
 * @return string The predict API base URL.
 */
function get_predict_base_url(): string {
	return get_rekai_service_url( 'predict', REKAI_PREDICT_DEFAULT_URL );
}

/**
 * Builds an exact-match subtree parameter (`^/path/$`) for the given post, used to scope
 * server-side rendered Q&A to only the page currently being rendered.
 *
 * @param int $post_id The post ID of the page being rendered.
 *
 * @return string The subtree parameter value, or an empty string if it cannot be determined.
 */
function get_current_page_subtree( int $post_id ): string {
	if ( empty( $post_id ) ) {
		return '';
	}
	$permalink = get_permalink( $post_id );
	if ( empty( $permalink ) ) {
		return '';
	}
	$path = preg_replace( '|^https?://[^/]+/|', '^/', $permalink );
	return rtrim( $path, '/' ) . '/$';
}

/**
 * Builds the query parameters for a server-side Q&A predict API request.
 *
 * @param array $attributes The block/shortcode attributes being rendered.
 * @param int   $post_id    The post ID of the page being rendered.
 *
 * @return array The query parameters for the predict API request.
 */
function build_predict_query_args( array $attributes, int $post_id ): array {
	$args = generate_data_attributes_raw( $attributes );

	// handle_testing_mode() may have injected projectid/secretkey for the CLIENT-side
	// script under test mode. SSR always needs its own real credentials under the API's
	// actual param names, regardless of test mode, since PHP itself authenticates the request.
	unset( $args['projectid'], $args['secretkey'] );

	$args['projectid'] = get_option( 'rekai_project_id', '' );
	$args['secret']    = get_option( 'rekai_secret_key', '' );
	$args['unranked']  = 'true';
	$args['format']    = 'html';
	$args['subtree']   = get_current_page_subtree( $post_id );

	return $args;
}

/**
 * Fetches server-side rendered Q&A HTML from the Rek.ai predict API, with caching.
 *
 * @param int   $post_id    The post ID of the page being rendered.
 * @param array $attributes The block/shortcode attributes being rendered.
 *
 * @return string|null The rendered HTML on success, or null on any failure (missing
 *                      credentials, network error, non-200 response, or empty body) —
 *                      callers should fall back to client-side rendering in that case.
 */
function fetch_qna_ssr_html( int $post_id, array $attributes ): ?string {
	$project_id = get_option( 'rekai_project_id', '' );
	$secret_key = get_option( 'rekai_secret_key', '' );
	if ( empty( $project_id ) || empty( $secret_key ) || empty( $post_id ) ) {
		return null;
	}

	$cache_key = 'rekai_qna_ssr_' . $post_id . '_' . md5( wp_json_encode( $attributes ) );
	$cached    = get_transient( $cache_key );
	if ( '' === $cached ) {
		// Recently failed - negative cache, avoid hammering a down/misconfigured API.
		return null;
	}
	if ( false !== $cached ) {
		return $cached;
	}

	$args = build_predict_query_args( $attributes, $post_id );
	$url  = add_query_arg( $args, get_predict_base_url() . '/predict' );

	$response = wp_remote_get(
		$url,
		array( 'timeout' => apply_filters( 'rekai_qna_ssr_timeout', 5 ) )
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		set_transient( $cache_key, '', apply_filters( 'rekai_qna_ssr_error_cache_ttl', MINUTE_IN_SECONDS ) );
		return null;
	}

	$html = wp_remote_retrieve_body( $response );
	if ( empty( $html ) ) {
		set_transient( $cache_key, '', apply_filters( 'rekai_qna_ssr_error_cache_ttl', MINUTE_IN_SECONDS ) );
		return null;
	}

	set_transient( $cache_key, $html, apply_filters( 'rekai_qna_ssr_cache_ttl', DAY_IN_SECONDS ) );
	return $html;
}
