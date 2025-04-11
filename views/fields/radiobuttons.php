<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$input_id      = $rek_id ?? '';
$input_value   = $rek_value ?? '';
$input_options = $rek_options ?? array();
$input_help    = $rek_help ?? '';

?>
<?php
$counter = 0;
foreach ( $input_options as $value => $label ) :
	$item_id = $input_id . '_' . $counter++;
	?>
	<div>
		<input
			type="radio"
			id="<?php echo esc_attr( $item_id ); ?>"
			name="<?php echo esc_attr( $input_id ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			<?php
			if ( $input_value === $value ) :
				?>
				checked="checked"
			<?php endif; ?>
		/>
		<label for="<?php echo esc_attr( $item_id ); ?>"><?php echo esc_html( $label ); ?></label>
	</div>
<?php endforeach; ?>
<?php if ( ! empty( $input_help ) ) : ?>
	<p class="description">
		<?php
			echo $input_help; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</p>
<?php endif; ?>
