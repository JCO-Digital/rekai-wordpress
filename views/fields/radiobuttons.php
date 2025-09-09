<?php
/**
 * Radiobuttons field.
 *
 * @package Rekai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$counter = 0;
foreach ( $rek_options ?? array() as $value => $label ) :
	$item_id = $rek_id . '_' . ( $counter++ );
	?>
	<div>
		<input
			type="radio"
			id="<?php echo esc_attr( $item_id ); ?>"
			name="<?php echo esc_attr( $rek_id ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			<?php
			if ( $rek_value === $value ) :
				?>
				checked="checked"
			<?php endif; ?>
		/>
		<label for="<?php echo esc_attr( $item_id ); ?>"><?php echo esc_html( $label ); ?></label>
	</div>
<?php endforeach; ?>
<?php if ( ! empty( $rek_help ) ) : ?>
	<p class="description">
		<?php
			echo esc_html( $rek_help );
		?>
	</p>
<?php endif; ?>
