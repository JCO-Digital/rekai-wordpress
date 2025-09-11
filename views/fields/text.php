<?php
/**
 * Text field.
 *
 * @package Rekai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$input_size        = $rek_size ?? '20';

?>
<input
		type="text"
		id="<?php echo esc_attr( $rek_id ); ?>"
		name="<?php echo esc_attr( $rek_id ); ?>"
		value="<?php echo esc_attr( $rek_value ); ?>"
		placeholder="<?php echo esc_attr( $rek_placeholder ); ?>"
		size="<?php echo esc_attr( $input_size ); ?>"
/>
<?php if ( ! empty( $rek_help ) ) : ?>
	<p class="description">
		<?php \Rekai\render_help( $rek_help ); ?>
	</p>
<?php endif; ?>
