<?php // phpcs:ignore
use function Rekai\generate_data_attributes;
use function Rekai\handle_extra_attributes;

$nrofhits = $attributes['nrofhits'] ?? 5;

$base_attributes = array(
	'entitytype'      => 'rekai-qna',
	'nrofhits'        => $nrofhits,
	'headertext'      => $attributes['headerText'] ?? '',
	'currentLanguage' => $attributes['useCurrentLanguage'] ?? false,
	'pathOption'      => $attributes['pathOption'] ?? 'all',
	'depth'           => $attributes['depth'] ?? 1,
	'limit'           => $attributes['limit'] ?? 'none',
	'limitDepth'      => $attributes['limitDepth'] ?? 'none',
);

$tags = $attributes['tags'] ?? array();
if ( ! empty( $tags ) && is_array( $tags ) ) {
	$base_attributes['tags'] = implode( ',', $tags );
}

$generated_attributes          = generate_data_attributes( $base_attributes );
$generated_attributes['class'] = 'rek-prediction';
handle_extra_attributes( $attributes['extraAttributes'] ?? '', $generated_attributes );
?>
<div
	<?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in the get_block_wrapper_attributes
		echo get_block_wrapper_attributes( $generated_attributes );
	?>
>
</div>

