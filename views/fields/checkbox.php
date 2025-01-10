<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$input_id          = $rek_id ?? '';
$input_value       = $rek_value ?? '';
$input_placeholder = $rek_placeholder ?? '';

$input_checked = ! empty( $input_value );

?>
<input
		type="checkbox"
		id="<?php echo esc_attr( $input_id ); ?>"
		name="<?php echo esc_attr( $input_id ); ?>"
		value="<?php echo esc_attr( $input_id ); ?>"
		<?php checked( $input_checked ); ?>
		placeholder="<?php echo esc_attr( $input_placeholder ); ?>"
/>