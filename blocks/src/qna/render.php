<?php // phpcs:ignore
use function Rekai\generate_data_attributes;
$nrofhits = $attributes['nrofhits'] ?? 5;

$base_attributes = array(
	'class'           => 'rek-prediction',
	'data-entitytype' => 'rekai-qna',
	'data-nrofhits'   => $nrofhits,
);

$header_text = $attributes['headerText'] ?? '';
if ( ! empty( $header_text ) ) {
	$base_attributes['data-headertext'] = $header_text;
}

$tags = $attributes['tags'] ?? array();
if ( ! empty( $tags ) && is_array( $tags ) ) {
	$base_attributes['data-tags'] = implode( ',', $tags );
}

$use_root = $attributes['useRoot'] ?? false;
if ( $use_root ) {
	$base_attributes['data-userootpath'] = 'true';
}

?>
<div
<?php
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in the get_block_wrapper_attributes
echo get_block_wrapper_attributes(
	generate_data_attributes(
		$base_attributes
	)
);
?>
>
</div>

