<?php // phpcs:ignore

use Rekai\Scripts\RekaiMain;

$is_test   = RekaiMain::get_instance()->get_test_mode();
$mock_data = get_option( 'rekai_use_mock_data' );
$add_test  = false;

if ( $is_test ) {
	$project_id = get_option( 'rekai_project_id' );
	$secret_key = get_option( 'rekai_secret_key' );
	$add_test   = ! empty( $project_id ) && ! empty( $secret_key );
}

?>
<div
		class="rek-prediction"
		<?php if ( $is_test && $add_test ) : ?>
			data-projectid="<?php echo esc_attr( $project_id ); ?>"
			data-secretkey="<?php echo esc_attr( $secret_key ); ?>"
		<?php endif; ?>
		<?php if ( $is_test && $add_test && $mock_data === '1' ) : ?>
			data-advanced_mockdata="true"
		<?php endif; ?>
></div>
