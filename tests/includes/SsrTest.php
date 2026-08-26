<?php
/**
 * Tests for includes/ssr.php.
 *
 * @package Rekai
 */

namespace Rekai\Tests\Includes;

use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Rekai\Tests\TestCase;

use function Rekai\build_predict_query_args;
use function Rekai\fetch_qna_predictions;
use function Rekai\fetch_qna_ssr_html;
use function Rekai\get_current_page_subtree;
use function Rekai\get_qna_ssr_json_ld;
use function Rekai\get_qna_ssr_style_tag;
use function Rekai\render_qna_ssr_markup;
use function Rekai\resolve_qna_render_mode;

/**
 * Runs every test in its own process: several functions under test (get_qna_ssr_style_tag's
 * "once per request" static flag, and the RekaiMain singleton reached via
 * build_predict_query_args()) rely on process-lifetime state that must start fresh per test,
 * not just per Brain Monkey setUp()/tearDown() cycle.
 */
#[RunTestsInSeparateProcesses]
final class SsrTest extends TestCase {

	/**
	 * Stubs get_option() to return values from the given map, falling back to the
	 * caller-supplied $default (mirroring get_option()'s own signature) otherwise.
	 *
	 * @param array $options Map of option name to value.
	 */
	private function stub_options( array $options ): void {
		Functions\when( 'get_option' )->alias(
			static function ( string $name, $default = false ) use ( $options ) {
				return $options[ $name ] ?? $default;
			}
		);
	}

	public function test_resolve_qna_render_mode_returns_explicit_override_without_reading_option(): void {
		// No get_option stub registered at all: if the option were read despite the explicit
		// override, Brain Monkey would fail the test with an unexpected-call error.
		self::assertSame( 'server', resolve_qna_render_mode( 'server' ) );
		self::assertSame( 'client', resolve_qna_render_mode( 'client' ) );
	}

	public function test_resolve_qna_render_mode_falls_back_to_the_global_option(): void {
		$this->stub_options( array( 'rekai_qna_render_mode' => 'client' ) );
		self::assertSame( 'client', resolve_qna_render_mode( 'default' ) );
	}

	public function test_resolve_qna_render_mode_defaults_to_server_when_option_unset(): void {
		$this->stub_options( array() );
		self::assertSame( 'server', resolve_qna_render_mode( 'default' ) );
	}

	public function test_resolve_qna_render_mode_treats_any_non_server_non_client_override_as_default(): void {
		$this->stub_options( array( 'rekai_qna_render_mode' => 'client' ) );
		self::assertSame( 'client', resolve_qna_render_mode( 'bogus' ) );
	}

	public function test_get_current_page_subtree_returns_empty_string_for_missing_post_id(): void {
		self::assertSame( '', get_current_page_subtree( 0 ) );
	}

	public function test_get_current_page_subtree_returns_empty_string_when_permalink_unavailable(): void {
		Functions\when( 'get_permalink' )->justReturn( '' );
		self::assertSame( '', get_current_page_subtree( 42 ) );
	}

	public function test_get_current_page_subtree_builds_an_anchored_exact_match_path(): void {
		Functions\when( 'get_permalink' )->justReturn( 'https://example.com/fi/case/case-pakopeli/' );
		self::assertSame( '^/fi/case/case-pakopeli/$', get_current_page_subtree( 42 ) );
	}

	public function test_get_current_page_subtree_handles_a_permalink_without_a_trailing_slash(): void {
		Functions\when( 'get_permalink' )->justReturn( 'https://example.com/fi/case/case-pakopeli' );
		self::assertSame( '^/fi/case/case-pakopeli/$', get_current_page_subtree( 42 ) );
	}

	public function test_get_qna_ssr_json_ld_builds_a_valid_faqpage_schema(): void {
		$this->stub_wp_json_encode();

		$html = get_qna_ssr_json_ld(
			array(
				array(
					'question' => 'What is Grand One?',
					'answer'   => 'A competition.',
				),
			)
		);

		self::assertStringStartsWith( '<script type="application/ld+json">', $html );
		self::assertStringEndsWith( '</script>', $html );

		$json = trim( str_replace( array( '<script type="application/ld+json">', '</script>' ), '', $html ) );
		$data = json_decode( $json, true );

		self::assertSame( 'https://schema.org', $data['@context'] );
		self::assertSame( 'FAQPage', $data['@type'] );
		self::assertSame( 'Question', $data['mainEntity'][0]['@type'] );
		self::assertSame( 'What is Grand One?', $data['mainEntity'][0]['name'] );
		self::assertSame( 'Answer', $data['mainEntity'][0]['acceptedAnswer']['@type'] );
		self::assertSame( 'A competition.', $data['mainEntity'][0]['acceptedAnswer']['text'] );
	}

	public function test_get_qna_ssr_json_ld_keeps_slashes_escaped_to_prevent_a_script_tag_breakout(): void {
		$this->stub_wp_json_encode();

		$html = get_qna_ssr_json_ld(
			array(
				array(
					'question' => 'Can you inject </script><script>alert(1)</script> here?',
					'answer'   => 'No.',
				),
			)
		);

		self::assertStringNotContainsString( '</script><script>alert(1)</script>', $html );
		self::assertStringContainsString( '<\/script>', $html );
	}

	public function test_render_qna_ssr_markup_escapes_question_and_answer_text(): void {
		$this->stub_wp_json_encode();
		Functions\when( 'esc_html' )->alias(
			static fn( $text ) => htmlspecialchars( (string) $text, ENT_QUOTES )
		);

		$html = render_qna_ssr_markup(
			array(
				array(
					'question' => 'Is <b>this</b> safe?',
					'answer'   => 'Yes, <script>alert(1)</script> is escaped.',
				),
			)
		);

		self::assertStringContainsString( 'Is &lt;b&gt;this&lt;/b&gt; safe?', $html );
		self::assertStringContainsString( '&lt;script&gt;alert(1)&lt;/script&gt;', $html );
		self::assertStringNotContainsString( '<script>alert(1)</script>', $html );
	}

	public function test_render_qna_ssr_markup_skips_predictions_missing_a_question_or_answer(): void {
		$this->stub_wp_json_encode();
		Functions\when( 'esc_html' )->alias( static fn( $text ) => htmlspecialchars( (string) $text, ENT_QUOTES ) );

		$html = render_qna_ssr_markup(
			array(
				array( 'question' => 'Only a question' ),
				array( 'answer' => 'Only an answer' ),
				array(
					'question' => 'A complete one',
					'answer'   => 'With an answer',
				),
			)
		);

		self::assertSame( 1, substr_count( $html, '<details class="rekai-qna-ssr-item">' ) );
		self::assertStringContainsString( 'A complete one', $html );
	}

	public function test_render_qna_ssr_markup_returns_empty_string_when_nothing_survives_filtering(): void {
		self::assertSame( '', render_qna_ssr_markup( array( array( 'question' => 'Only a question' ) ) ) );
		self::assertSame( '', render_qna_ssr_markup( array() ) );
	}

	public function test_get_qna_ssr_style_tag_is_only_printed_once_per_request(): void {
		$first  = get_qna_ssr_style_tag();
		$second = get_qna_ssr_style_tag();

		self::assertStringContainsString( '.rekai-qna-ssr-list', $first );
		self::assertSame( '', $second );
	}

	public function test_build_predict_query_args_uses_p_not_projectid_and_a_page_scoped_subtree(): void {
		$this->stub_options(
			array(
				'rekai_project_id' => '14231580',
				'rekai_secret_key' => 'a-secret',
			)
		);
		Functions\when( 'wp_get_environment_type' )->justReturn( 'production' );
		Functions\when( 'apply_filters' )->alias( static fn( $tag, $value ) => $value );
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'get_permalink' )->justReturn( 'https://example.com/fi/grand-one-2024/' );

		$args = build_predict_query_args( array( 'blockType' => 'qna' ), 1 );

		self::assertSame( '14231580', $args['p'] );
		self::assertSame( 'a-secret', $args['secret'] );
		self::assertArrayNotHasKey( 'projectid', $args );
		self::assertSame( 'true', $args['unranked'] );
		self::assertSame( '^/fi/grand-one-2024/$', $args['subtree'] );
		self::assertSame( 'rekai-qna', $args['entitytype'] );
	}

	public function test_fetch_qna_predictions_returns_null_when_credentials_are_missing(): void {
		$this->stub_options( array() );
		self::assertNull( fetch_qna_predictions( 1, array( 'blockType' => 'qna' ) ) );
	}

	public function test_fetch_qna_predictions_returns_predictions_on_a_successful_response(): void {
		$this->stub_options(
			array(
				'rekai_project_id' => '14231580',
				'rekai_secret_key' => 'a-secret',
			)
		);
		Functions\when( 'wp_get_environment_type' )->justReturn( 'production' );
		Functions\when( 'apply_filters' )->alias( static fn( $tag, $value ) => $value );
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'get_permalink' )->justReturn( 'https://example.com/fi/grand-one-2024/' );
		Functions\when( 'add_query_arg' )->alias(
			static fn( $args, $url ) => $url . '?' . http_build_query( $args )
		);
		$this->stub_wp_json_encode();
		Functions\when( 'get_transient' )->justReturn( false );
		Functions\expect( 'set_transient' )->once();
		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			wp_json_encode( array( 'predictions' => array( array( 'question' => 'Q', 'answer' => 'A' ) ) ) )
		);
		Functions\when( 'wp_remote_get' )->justReturn( array() );

		$predictions = fetch_qna_predictions( 1, array( 'blockType' => 'qna' ) );

		self::assertSame( array( array( 'question' => 'Q', 'answer' => 'A' ) ), $predictions );
	}

	public function test_fetch_qna_predictions_returns_a_cached_result_without_a_new_request(): void {
		$this->stub_options(
			array(
				'rekai_project_id' => '14231580',
				'rekai_secret_key' => 'a-secret',
			)
		);
		Functions\when( 'get_transient' )->justReturn( array( array( 'question' => 'Cached', 'answer' => 'A' ) ) );
		Functions\expect( 'wp_remote_get' )->never();

		$predictions = fetch_qna_predictions( 1, array( 'blockType' => 'qna' ) );

		self::assertSame( array( array( 'question' => 'Cached', 'answer' => 'A' ) ), $predictions );
	}

	public function test_fetch_qna_predictions_returns_null_and_negative_caches_on_a_network_error(): void {
		$this->stub_options(
			array(
				'rekai_project_id' => '14231580',
				'rekai_secret_key' => 'a-secret',
			)
		);
		Functions\when( 'wp_get_environment_type' )->justReturn( 'production' );
		Functions\when( 'apply_filters' )->alias( static fn( $tag, $value ) => $value );
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'get_permalink' )->justReturn( 'https://example.com/fi/grand-one-2024/' );
		Functions\when( 'add_query_arg' )->alias( static fn( $args, $url ) => $url . '?' . http_build_query( $args ) );
		$this->stub_wp_json_encode();
		Functions\when( 'get_transient' )->justReturn( false );
		Functions\when( 'is_wp_error' )->justReturn( true );
		Functions\when( 'wp_remote_get' )->justReturn( array() );
		Functions\expect( 'set_transient' )->once()->with(
			\Mockery::any(),
			array(),
			\Mockery::any()
		);

		self::assertNull( fetch_qna_predictions( 1, array( 'blockType' => 'qna' ) ) );
	}

	public function test_fetch_qna_predictions_uses_a_different_cache_key_when_mock_data_is_toggled(): void {
		Functions\when( 'wp_get_environment_type' )->justReturn( 'production' );
		Functions\when( 'apply_filters' )->alias( static fn( $tag, $value ) => $value );
		Functions\when( 'add_action' )->justReturn( true );
		Functions\when( 'get_permalink' )->justReturn( 'https://example.com/fi/grand-one-2024/' );
		Functions\when( 'add_query_arg' )->alias( static fn( $args, $url ) => $url . '?' . http_build_query( $args ) );
		$this->stub_wp_json_encode();
		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn(
			wp_json_encode( array( 'predictions' => array( array( 'question' => 'Q', 'answer' => 'A' ) ) ) )
		);
		Functions\when( 'wp_remote_get' )->justReturn( array() );

		$seen_keys = array();
		Functions\when( 'get_transient' )->justReturn( false );
		Functions\when( 'set_transient' )->alias(
			static function ( $key ) use ( &$seen_keys ) {
				$seen_keys[] = $key;
			}
		);

		$this->stub_options(
			array(
				'rekai_project_id'   => '14231580',
				'rekai_secret_key'   => 'a-secret',
				'rekai_use_mock_data' => '',
			)
		);
		fetch_qna_predictions( 1, array( 'blockType' => 'qna' ) );

		$this->stub_options(
			array(
				'rekai_project_id'   => '14231580',
				'rekai_secret_key'   => 'a-secret',
				'rekai_use_mock_data' => '1',
			)
		);
		fetch_qna_predictions( 1, array( 'blockType' => 'qna' ) );

		self::assertCount( 2, $seen_keys );
		self::assertNotSame( $seen_keys[0], $seen_keys[1] );
	}

	public function test_fetch_qna_ssr_html_returns_null_when_there_are_no_predictions_to_render(): void {
		$this->stub_options( array() );
		self::assertNull( fetch_qna_ssr_html( 1, array( 'blockType' => 'qna' ) ) );
	}

	/**
	 * Stubs wp_json_encode() to behave like PHP's json_encode(), since the code under test
	 * relies on real default flag behaviour (e.g. slashes staying escaped).
	 */
	private function stub_wp_json_encode(): void {
		Functions\when( 'wp_json_encode' )->alias(
			static fn( $data, $options = 0 ) => json_encode( $data, $options )
		);
	}
}
