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
 * Deliberately requests raw JSON (no format=html): Rek.ai's format=html output relies on
 * their own client-side script to style and make it interactive, but that script only ever
 * enhances elements it finds itself to fetch (i.e. empty client-side widgets) - pre-rendered
 * server-side markup gets no CSS and no click-to-expand behaviour from it, and attempting to
 * also mark the wrapper as a client-side widget just makes the script re-fetch and overwrite
 * the SSR content instead of enhancing it in place. Building our own HTML from the raw JSON
 * data lets us ship our own self-contained styling/interactivity instead, and as a bonus,
 * raw JSON is also the only format the predict API returns real content for when
 * "advanced_mockdata" is set - it silently ignores format=html for mock data.
 *
 * @param array $attributes The block/shortcode attributes being rendered.
 * @param int   $post_id    The post ID of the page being rendered.
 *
 * @return array The query parameters for the predict API request.
 */
function build_predict_query_args( array $attributes, int $post_id ): array {
	// generate_data_attributes_raw() copies some attributes (e.g. nrOfHits, headerText)
	// under their original block-attribute casing; the client-side path only gets away
	// with this because map_data_to_dataset() lowercases everything at the very end. SSR
	// talks to the API directly, so it must lowercase the keys itself.
	$args = array_change_key_case( generate_data_attributes_raw( $attributes ), CASE_LOWER );

	// handle_testing_mode() may have injected projectid/secretkey for the CLIENT-side script
	// under test mode. SSR always needs its own real credentials under the API's actual
	// param names, regardless of test mode, since PHP itself authenticates the request.
	unset( $args['projectid'], $args['secretkey'] );

	// The predict API's project ID parameter is "p", not "projectid" - the latter is only
	// the doc page's/data-attribute's label, not the actual query parameter name (confirmed
	// against the live API; see https://docs.rek.ai/advanced/REST-API/get-predictions).
	$args['p']        = get_option( 'rekai_project_id', '' );
	$args['secret']   = get_option( 'rekai_secret_key', '' );
	$args['unranked'] = 'true';
	$args['subtree']  = get_current_page_subtree( $post_id );

	return $args;
}

/**
 * Fetches the Q&A predictions for the current page from the Rek.ai predict API, with caching.
 *
 * @param int   $post_id    The post ID of the page being rendered.
 * @param array $attributes The block/shortcode attributes being rendered.
 *
 * @return array|null The list of predictions (each an assoc array with at least "question"
 *                     and "answer" keys) on success, or null if there's nothing to render
 *                     (missing credentials, network error, non-200 response, malformed body,
 *                     or zero matching predictions) - callers should render an empty block in
 *                     that case rather than falling back to client-side rendering, since the
 *                     client-side script scopes results differently (broader "related content"
 *                     matching, not this exact page) and would show a different result set.
 */
function fetch_qna_predictions( int $post_id, array $attributes ): ?array {
	$project_id = get_option( 'rekai_project_id', '' );
	$secret_key = get_option( 'rekai_secret_key', '' );
	if ( empty( $project_id ) || empty( $secret_key ) || empty( $post_id ) ) {
		return null;
	}

	$cache_key = 'rekai_qna_ssr_' . $post_id . '_' . md5( wp_json_encode( $attributes ) );
	$cached    = get_transient( $cache_key );
	if ( is_array( $cached ) ) {
		// Cached result, empty on a prior failure or a genuine zero-prediction response.
		return empty( $cached ) ? null : $cached;
	}

	$args = build_predict_query_args( $attributes, $post_id );
	$url  = add_query_arg( $args, get_predict_base_url() . '/predict' );

	$response = wp_remote_get(
		$url,
		array( 'timeout' => apply_filters( 'rekai_qna_ssr_timeout', 5 ) )
	);

	if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		set_transient( $cache_key, array(), apply_filters( 'rekai_qna_ssr_error_cache_ttl', MINUTE_IN_SECONDS ) );
		return null;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict -- json_decode() never returns int/float here.
	$predictions = is_array( $body['predictions'] ?? null ) ? $body['predictions'] : array();

	set_transient( $cache_key, $predictions, apply_filters( 'rekai_qna_ssr_cache_ttl', DAY_IN_SECONDS ) );

	return empty( $predictions ) ? null : $predictions;
}

/**
 * Returns the inline stylesheet for server-side rendered Q&A markup, once per request.
 *
 * Printed inline (rather than via wp_enqueue_style()) because block rendering happens during
 * the_content(), after wp_head() has already run.
 *
 * @return string The <style> tag on the first call in a request, or an empty string after.
 */
function get_qna_ssr_style_tag(): string {
	static $printed = false;
	if ( $printed ) {
		return '';
	}
	$printed = true;

	return '<style>' .
		'.rekai-qna-ssr-list{border-top:1px solid #868e96}' .
		'.rekai-qna-ssr-item{border-bottom:1px solid #868e96}' .
		'.rekai-qna-ssr-question{display:flex;align-items:center;justify-content:space-between;' .
		'gap:1rem;padding:1rem 8px;min-height:60px;cursor:pointer;font-weight:600;list-style:none}' .
		'.rekai-qna-ssr-question::-webkit-details-marker{display:none}' .
		'.rekai-qna-ssr-question::after{content:"+";font-size:1.5rem;line-height:1;flex-shrink:0}' .
		'.rekai-qna-ssr-item[open] .rekai-qna-ssr-question::after{content:"\2212"}' .
		'.rekai-qna-ssr-question:hover{background-color:#f1f3f5}' .
		'.rekai-qna-ssr-answer{padding:.25rem 8px 1rem;margin:.5rem 0 1rem;max-width:700px}' .
		'</style>';
}

/**
 * Builds a schema.org FAQPage JSON-LD block for the given predictions.
 *
 * @param array $predictions The list of predictions, each with "question" and "answer" keys.
 *
 * @return string The <script type="application/ld+json"> tag.
 */
function get_qna_ssr_json_ld( array $predictions ): string {
	$json_ld = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => array_map(
			static function ( array $prediction ): array {
				return array(
					'@type'          => 'Question',
					'name'           => (string) ( $prediction['question'] ?? '' ),
					'acceptedAnswer' => array(
						'@type' => 'Answer',
						'text'  => (string) ( $prediction['answer'] ?? '' ),
					),
				);
			},
			$predictions
		),
	);

	// Deliberately not passing JSON_UNESCAPED_SLASHES: keeping "/" escaped as "\/" is what
	// stops a "</script>" inside a question/answer from breaking out of this script tag.
	return '<script type="application/ld+json">' . wp_json_encode( $json_ld, JSON_UNESCAPED_UNICODE ) . '</script>';
}

/**
 * Renders server-side Q&A predictions as accessible, self-contained HTML.
 *
 * Uses native <details>/<summary> for expand/collapse so it works without any JavaScript,
 * and ships its own minimal styles/markup rather than Rek.ai's, since their default styling
 * and interactivity are only ever applied by their script to client-side-fetched widgets.
 *
 * @param array $predictions The list of predictions, each with "question" and "answer" keys.
 *
 * @return string The rendered HTML.
 */
function render_qna_ssr_markup( array $predictions ): string {
	$items = '';
	foreach ( $predictions as $prediction ) {
		$question = (string) ( $prediction['question'] ?? '' );
		$answer   = (string) ( $prediction['answer'] ?? '' );
		if ( '' === $question || '' === $answer ) {
			continue;
		}
		$items .= '<details class="rekai-qna-ssr-item">' .
			'<summary class="rekai-qna-ssr-question">' . esc_html( $question ) . '</summary>' .
			'<div class="rekai-qna-ssr-answer"><p>' . esc_html( $answer ) . '</p></div>' .
			'</details>';
	}

	if ( '' === $items ) {
		return '';
	}

	return get_qna_ssr_style_tag() .
		'<div class="rekai-qna-ssr-list">' . $items . '</div>' .
		get_qna_ssr_json_ld( $predictions );
}

/**
 * Fetches and renders server-side Q&A HTML for the current page.
 *
 * @param int   $post_id    The post ID of the page being rendered.
 * @param array $attributes The block/shortcode attributes being rendered.
 *
 * @return string|null The rendered HTML on success, or null if there's nothing to render -
 *                      callers should render an empty block in that case (see
 *                      fetch_qna_predictions() for why not to fall back to client-side
 *                      rendering).
 */
function fetch_qna_ssr_html( int $post_id, array $attributes ): ?string {
	$predictions = fetch_qna_predictions( $post_id, $attributes );
	if ( null === $predictions ) {
		return null;
	}

	$html = render_qna_ssr_markup( $predictions );

	return '' === $html ? null : $html;
}
