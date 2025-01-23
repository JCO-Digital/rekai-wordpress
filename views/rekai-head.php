<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$project_id            = $rek_script_key ?? '';
$is_admin              = $rek_is_admin ?? false;
$is_test               = $rek_is_test ?? false;
$is_automatic          = $rek_is_automatic ?? false;
$autocomplete_selector = $rek_autocomplete_selector ?? '';
$autocomplete_options  = $rek_autocomplete_options ?? '{}';

?>

<?php if ( $is_admin || $is_test ) : ?>
	<script>
		window.rek_blocksaveview = true;
	</script>
<?php endif; ?>

<?php if ( $is_automatic && ! empty( $autocomplete_selector ) ) : ?>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			__rekai.ready(function () {
				var rekAutocomplete = rekai_autocomplete(
					'<?php echo esc_js( $autocomplete_selector ); ?>',
					<?php echo $autocomplete_options; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				);
			});
		});
	</script>
<?php endif; ?>
