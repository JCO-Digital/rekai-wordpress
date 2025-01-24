<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$is_automatic          = $rek_is_automatic ?? false;
$autocomplete_selector = $rek_autocomplete_selector ?? '';
$autocomplete_options  = $rek_autocomplete_options ?? '{}';

?>

<?php if ( $is_automatic && ! empty( $autocomplete_selector ) ) : ?>
	<script>
		__rekai.ready(function () {
			var rekAutocomplete = rekai_autocomplete(
				'<?php echo esc_js( $autocomplete_selector ); ?>',
				<?php echo $autocomplete_options; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			);
		});
	</script>
<?php endif; ?>
