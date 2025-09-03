<?php
/**
 * File handler.
 *
 * @package Rekai
 */

namespace Rekai;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$extra          = generate_data_attributes( $attributes ?? array() );
$extra['class'] = 'rek-prediction';
handle_extra_attributes( $attributes['extraAttributes'] ?? '', $extra );

?>
<div
		<?php
		// phpcs:ignore
		echo get_block_wrapper_attributes( $extra );
		?>
></div>
