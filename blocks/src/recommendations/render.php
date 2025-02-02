<?php // phpcs:ignore

use Rekai\Scripts\RekaiMain;


function generate_data_attributes() {
	$is_test   = RekaiMain::get_instance()->get_test_mode();
	$mock_data = get_option( 'rekai_use_mock_data' );
	$add_test  = false;

	if ( $is_test ) {
			$project_id = get_option( 'rekai_project_id' );
			$secret_key = get_option( 'rekai_secret_key' );
			$add_test   = ! empty( $project_id ) && ! empty( $secret_key );
	}

	$data = array();
	if ( $is_test && $add_test ) {
		$data['projectid'] = $project_id;
		$data['secretkey'] = $secret_key;
	}
	if ( $is_test && $add_test && $mock_data === '1' ) {
		$data['advanced_mockdata'] = 'true';
	}
	return data_to_string( $data );
}

function data_to_string( $data ) {
	$data_string = '';
	foreach ( $data as $key => $value ) {
		$data_string .= sprintf( ' data-%s="%s"', $key, esc_attr( $value ) );
	}
	return trim( $data_string );
}

?>
<div
		class="rek-prediction"
		<?php echo generate_data_attributes(); ?>
></div>
