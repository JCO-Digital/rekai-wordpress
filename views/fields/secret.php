<?php
/**
 * Secret field.
 *
 * @package Rekai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_empty = empty( $rek_value );

?>
<span class="secret-field">
	<input
		type="<?php echo $is_empty ? 'text' : 'password'; ?>"
		id="<?php echo esc_attr( $rek_id ); ?>"
		name="<?php echo esc_attr( $rek_id ); ?>"
		value="<?php echo esc_attr( $rek_value ); ?>"
		placeholder="<?php echo esc_attr( $rek_placeholder ); ?>"
		autocomplete="off"
	/>
	<button type="button" class="button button-secondary secret-toggle
	<?php
	if ( ! $is_empty ) :
		?>
		show<?php endif; ?>" data-toggle-hide="<?php echo esc_attr( $rek_id ); ?>">
		<span class="dashicons"></span>
	</button>

	<?php if ( ! empty( $rek_help ) ) : ?>
		<p class="description">
			<?php
				echo esc_html( $rek_help );
			?>
		</p>
	<?php endif; ?>
</span>
