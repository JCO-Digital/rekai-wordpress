<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$input_id          = $rek_id ?? '';
$input_value       = $rek_value ?? '';
$input_placeholder = $rek_placeholder ?? '';
$input_help        = $rek_help ?? '';

$is_empty = empty( $input_value );

?>
<span class="secret-field" x-data="{
	type: '<?php echo $is_empty ? 'text' : 'password'; ?>',
	toggle() {
		this.type = this.type === 'text' ? 'password' : 'text'
		this.$refs.input.type = this.type
	}
}">
	<input
		type="<?php echo $is_empty ? 'text' : 'password'; ?>"
		id="<?php echo esc_attr( $input_id ); ?>"
		name="<?php echo esc_attr( $input_id ); ?>"
		value="<?php echo esc_attr( $input_value ); ?>"
		placeholder="<?php echo esc_attr( $input_placeholder ); ?>"
		autocomplete="off"
		x-ref="input"
		x-model="<?php echo esc_attr( $input_id ); ?>"
	/>
	<?php if ( ! $is_empty ) : ?>
		<button type="button" class="button button-secondary secret-toggle" @click="toggle()">
			<span x-show="type === 'text'" x-cloak>
				<span class="dashicons dashicons-visibility"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Show', 'rekai-wordpress' ); ?></span>
			</span>
			<span x-show="type === 'password'" x-cloak>
				<span class="dashicons dashicons-hidden"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Hide', 'rekai-wordpress' ); ?></span>
			</span>
		</button>
	<?php endif; ?>
	<p class="description">
		<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $input_help;
		?>
	</p>
</span>
