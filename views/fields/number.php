<?php
/**
 * Number field.
 *
 * @package Rekai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<input
		type="number"
		id="<?php echo esc_attr( $rek_id ); ?>"
		name="<?php echo esc_attr( $rek_id ); ?>"
		value="<?php echo esc_attr( $rek_value ); ?>"
		placeholder="<?php echo esc_attr( $rek_placeholder ); ?>"
		<?php if ( ! empty( $rek_min ) ) : ?>
			min="<?php echo esc_attr( $rek_min ); ?>"
		<?php endif; ?>
		<?php if ( ! empty( $rek_max ) ) : ?>
			max="<?php echo esc_attr( $rek_max ); ?>"
		<?php endif; ?>
		<?php if ( ! empty( $rek_step ) ) : ?>
			step="<?php echo esc_attr( $rek_step ); ?>"
		<?php endif; ?>
/>
<?php if ( ! empty( $rek_help ) ) : ?>
	<p class="description">
		<?php
			echo esc_html( $rek_help );
		?>
	</p>
<?php endif; ?>
