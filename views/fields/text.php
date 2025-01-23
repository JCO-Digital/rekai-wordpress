<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$input_id          = $rek_id ?? '';
$input_value       = $rek_value ?? '';
$input_placeholder = $rek_placeholder ?? '';
$input_help        = $rek_help ?? '';

?>
<input
		type="text"
		id="<?php echo esc_attr( $input_id ); ?>"
		name="<?php echo esc_attr( $input_id ); ?>"
		value="<?php echo esc_attr( $input_value ); ?>"
		x-model="<?php echo esc_attr( $input_id ); ?>"
		placeholder="<?php echo esc_attr( $input_placeholder ); ?>"
/>
<?php if ( ! empty( $input_help ) ) : ?>
	<p class="description">
		<?php
			echo $input_help; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</p>
<?php endif; ?>
