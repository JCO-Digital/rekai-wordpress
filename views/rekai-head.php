<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$project_id = $rek_script_key ?? '';
$is_admin   = $rek_is_admin ?? false;
$is_test    = $rek_is_test ?? false;

?>

<?php if ( $is_admin || $is_test ) : ?>
	<script>
		window.rek_blocksaveview = true;
	</script>
<?php endif; ?>
