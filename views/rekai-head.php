<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing

$project_id = $rek_script_key ?? '';
$is_admin   = $rek_is_admin ?? '';

?>

<?php if ( $is_admin ) : ?>
	<script>
		window.rek_blocksaveview = true;
	</script>
<?php endif; ?>
