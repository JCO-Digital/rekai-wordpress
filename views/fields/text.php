<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$input_id          = $rek_id ?? '';
$input_value       = $rek_value ?? '';
$input_placeholder = $rek_placeholder ?? '';

?>
<input
		type="text"
		id="<?php echo esc_attr( $input_id ); ?>"
		name="<?php echo esc_attr( $input_id ); ?>"
		value="<?php echo esc_attr( $input_value ); ?>"
		placeholder="<?php echo esc_attr( $input_placeholder ); ?>"
/>
