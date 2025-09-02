<?php
/**
 * Rest backend functions.
 *
 * @package Rekai
 */

namespace Rekai;

use WP_REST_Request;
use WP_REST_Response;

add_action( 'rest_api_init', 'Rekai\add_endpoints' );

const NS = 'rekai/v1';
/**
 * Define rest endpoints.
 *
 * @return void
 */
function add_endpoints(): void {
	register_rest_route(
		NS,
		'/posts',
		array(
			'methods'             => 'GET',
			'callback'            => 'Rekai\get_posts',
			'permission_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);
}


function get_posts( WP_REST_Request $request ): WP_REST_Response {
	$response      = new \WP_REST_Response();
	$blocked_types = array( 'attachment' );
	$types         = array();
	$results       = array();
	foreach ( get_post_types( array( 'public' => 'true' ), 'objects' ) as $type ) {
		if ( in_array( $type->name, $blocked_types, true ) ) {
			continue;
		}
		$types[] = $type->name;

		if ( $type->rewrite ) {
			$results[] = array(
				'id'    => 0,
				'link'  => '/' . $type->rewrite['slug'] . '/',
				'label' => $type->label,
			);
		}
	}

	$args = array(
		'post_type'      => $types,
		'posts_per_page' => -1,
	);
	foreach ( \get_posts( $args ) as $post ) {
		$results[] = array(
			'id'    => $post->ID,
			'link'  => get_post_link( $post->ID ),
			'label' => $post->post_title,
		);
	}

	$response->set_data( $results );

	return $response;
}

function get_post_link( $id ) {
	return wp_make_link_relative( get_permalink( $id ) );
}
