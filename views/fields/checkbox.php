<?php
/**
 * Checkbox field.
 *
 * @package Rekai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<input
		type="checkbox"
		id="<?php echo esc_attr( $rek_id ); ?>"
		name="<?php echo esc_attr( $rek_id ); ?>"
		value="<?php echo esc_attr( $rek_id ); ?>"
		<?php checked( ! empty( $rek_value ) ); ?>
		placeholder="<?php echo esc_attr( $rek_placeholder ); ?>"
/>
<?php if ( ! empty( $rek_help ) ) : ?>
	<p class="description">
		<?php
			echo esc_html( $rek_help );
		?>
	</p>
<?php endif; ?>
