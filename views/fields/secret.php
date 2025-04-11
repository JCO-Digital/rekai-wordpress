<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$input_id          = $rek_id ?? '';
$input_value       = $rek_value ?? '';
$input_placeholder = $rek_placeholder ?? '';
$input_help        = $rek_help ?? '';

$is_empty = empty( $input_value );

?>
<span class="secret-field">
	<input
		type="<?php echo $is_empty ? 'text' : 'password'; ?>"
		id="<?php echo esc_attr( $input_id ); ?>"
		name="<?php echo esc_attr( $input_id ); ?>"
		value="<?php echo esc_attr( $input_value ); ?>"
		placeholder="<?php echo esc_attr( $input_placeholder ); ?>"
		autocomplete="off"
	/>
	<button type="button" class="button button-secondary secret-toggle 
	<?php
	if ( ! $is_empty ) :
		?>
		show<?php endif; ?>" data-toggle-hide="<?php echo esc_attr( $input_id ); ?>">
		<span class="dashicons"></span>
	</button>

	<p class="description">
		<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $input_help;
		?>
	</p>
</span>
