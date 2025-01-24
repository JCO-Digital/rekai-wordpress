<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$is_automatic          = $rek_is_automatic ?? false;
$autocomplete_selector = $rek_autocomplete_selector ?? '';
$autocomplete_options  = $rek_autocomplete_options ?? '{}';
$autocomplete_navigate = $rek_autocomplete_navigate ?? false;

?>

<?php if ( $is_automatic && ! empty( $autocomplete_selector ) ) : ?>
	<script>
		__rekai.ready(function () {
			const rekAutocomplete = rekai_autocomplete(
				'<?php echo esc_js( $autocomplete_selector ); ?>',
				<?php echo $autocomplete_options; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			);

			<?php if ( $autocomplete_navigate ) : ?>
				rekAutocomplete.on('rekai_autocomplete:selected', function (event, suggestion) {
					window.location.href = suggestion.url;
				});
			<?php endif; ?>
		});
	</script>
<?php endif; ?>
