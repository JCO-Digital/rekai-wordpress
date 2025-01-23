<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$input_id          = $rek_id ?? '';
$input_value       = $rek_value ?? '';
$input_placeholder = $rek_placeholder ?? '';
$input_help        = $rek_help ?? '';
$input_on_text     = $rek_on_text ?? '';
$input_off_text    = $rek_off_text ?? '';

$input_checked = ! empty( $input_value );

?>
<div class="wp-switch-wrapper" x-data="{
	minWidth: 0,
	wrapperWidth: 0,
	checked: <?php echo $input_checked ? 'true' : 'false'; ?>,
	init() {
		if (this.$refs.checkbox.checked) {
			this.minWidth = this.$refs.onText.offsetWidth + 33;
		} else {
			this.minWidth = this.$refs.offText.offsetWidth + 33;
		}
		$watch('checked', (value) => {
			if (value) {
				this.minWidth = this.$refs.onText.offsetWidth + 33;
			} else {
				this.minWidth = this.$refs.offText.offsetWidth + 33;
			}
		});
	},
}"
	:style="{ width: wrapperWidth + 'px', '--on-translate': wrapperWidth - 26 + 'px' }"
>
	<input
			type="checkbox"
			id="<?php echo esc_attr( $input_id ); ?>"
			name="<?php echo esc_attr( $input_id ); ?>"
			value="<?php echo esc_attr( $input_id ); ?>"
			class="wp-switch-checkbox"
			x-ref="checkbox"
			@click="checked = ! checked"
            x-model="<?php echo esc_attr( $input_id ); ?>"
			<?php checked( $input_checked ); ?>
	/>
	<span
			class="wp-switch-slider"
			@click="$refs.checkbox.click()"
			x-ref="slider"
			:style="{ width: minWidth + 'px' }"
			x-resize="wrapperWidth = $width"
	></span>
	<span class="wp-switch-text wp-switch-text-on" x-ref="onText"><?php echo esc_html( $input_on_text ); ?></span>
	<span class="wp-switch-text wp-switch-text-off" x-ref="offText"><?php echo esc_html( $input_off_text ); ?></span>
</div>
<p class="description">
	<?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $input_help;
	?>
</p>