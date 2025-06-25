<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing ?>
<div class="wrap">
	<h1><?php echo esc_html__( 'Rek.ai Shortcode Generator', 'rekai' ); ?></h1>
	<p><?php echo esc_html__( 'Use this tool to generate shortcodes for Rek.ai functionality.', 'rekai' ); ?></p>

	<div class="rekai-shortcode-generator">
		<div class="rekai-shortcode-type">
			<h2><?php echo esc_html__( 'Select Shortcode Type', 'rekai' ); ?></h2>
			<select id="rekai-shortcode-type">
				<?php foreach ( $rek_shortcode_types as $shortcode_type => $info ) : ?>
					<option value="<?php echo esc_attr( $shortcode_type ); ?>" data-shortcode="<?php echo esc_attr( $info['shortcode'] ); ?>">
						<?php echo esc_html( $info['label'] ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<p class="description" id="rekai-shortcode-description"></p>
		</div>

		<div class="rekai-shortcode-attributes">
			<h2><?php echo esc_html__( 'Configure Attributes', 'rekai' ); ?></h2>
			<table class="form-table">
				<tbody>
					<?php foreach ( $rek_common_attributes as $attr => $config ) : ?>
						<tr>
							<th scope="row">
								<label for="rekai-attr-<?php echo esc_attr( $attr ); ?>">
									<?php echo esc_html( $config['label'] ); ?>
								</label>
							</th>
							<td>
								<?php if ( $config['type'] === 'number' ) : ?>
									<input
										type="number"
										id="rekai-attr-<?php echo esc_attr( $attr ); ?>"
										name="<?php echo esc_attr( $attr ); ?>"
										value="<?php echo esc_attr( $config['default'] ?? '' ); ?>"
										min="<?php echo esc_attr( $config['min'] ?? '' ); ?>"
										max="<?php echo esc_attr( $config['max'] ?? '' ); ?>"
										class="regular-text"
									/>
								<?php elseif ( $config['type'] === 'checkbox' ) : ?>
									<input
										type="checkbox"
										id="rekai-attr-<?php echo esc_attr( $attr ); ?>"
										name="<?php echo esc_attr( $attr ); ?>"
									/>
								<?php else : ?>
									<input
										type="text"
										id="rekai-attr-<?php echo esc_attr( $attr ); ?>"
										name="<?php echo esc_attr( $attr ); ?>"
										placeholder="<?php echo esc_attr( $config['placeholder'] ?? '' ); ?>"
										class="regular-text"
									/>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<div class="rekai-shortcode-preview">
			<h2><?php echo esc_html__( 'Generated Shortcode', 'rekai' ); ?></h2>
			<div class="rekai-shortcode-output">
				<code id="rekai-shortcode-output"></code>
				<button type="button" class="button" id="rekai-copy-shortcode">
					<?php echo esc_html__( 'Copy Shortcode', 'rekai' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($) {
	function updateShortcodeDescription() {
		var selectedType = $('#rekai-shortcode-type').find(':selected');
		var shortcode_type = selectedType.val();
		var description = <?php echo wp_json_encode( wp_list_pluck( $rek_shortcode_types, 'description', 'prediction' ) ); ?>[shortcode_type];
		$('#rekai-shortcode-description').text(description);
	}

	function generateShortcode() {
		var selectedType = $('#rekai-shortcode-type').find(':selected');
		var shortcode = selectedType.data('shortcode');
		var attributes = [];

		$('.rekai-shortcode-attributes input').each(function() {
			var $input = $(this);
			var name = $input.attr('name');
			var value = $input.val();

			if ($input.attr('type') === 'checkbox') {
				if ($input.is(':checked')) {
					attributes.push(name + '="true"');
				}
			} else if (value) {
				attributes.push(name + '="' + value + '"');
			}
		});

		var output = '[' + shortcode;
		if (attributes.length > 0) {
			output += ' ' + attributes.join(' ');
		}
		output += ']';

		$('#rekai-shortcode-output').text(output);
	}

	$('#rekai-shortcode-type').on('change', function() {
		updateShortcodeDescription();
		generateShortcode();
	});

	$('.rekai-shortcode-attributes input').on('change keyup', generateShortcode);

	$('#rekai-copy-shortcode').on('click', function() {
		var shortcode = $('#rekai-shortcode-output').text();
		navigator.clipboard.writeText(shortcode).then(function() {
			var $button = $('#rekai-copy-shortcode');
			var originalText = $button.text();
			$button.text(<?php echo wp_json_encode( esc_html__( 'Copied!', 'rekai' ) ); ?>);
			setTimeout(function() {
				$button.text(originalText);
			}, 2000);
		});
	});

	// Initial update
	updateShortcodeDescription();
	generateShortcode();
});
</script>
