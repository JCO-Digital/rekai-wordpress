<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing ?>
<div class="wrap">
	<h1><?php echo esc_html__( 'Rek.ai Shortcode Generator', 'rek-ai' ); ?></h1>
	<p><?php echo esc_html__( 'Use this tool to generate shortcodes for Rek.ai functionality.', 'rek-ai' ); ?></p>

	<div class="rekai-shortcode-generator">
		<div class="rekai-shortcode-type">
			<h2><?php echo esc_html__( 'Select Shortcode Type', 'rek-ai' ); ?></h2>
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
			<h2><?php echo esc_html__( 'Configure Attributes', 'rek-ai' ); ?></h2>
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
			<h2><?php echo esc_html__( 'Generated Shortcode', 'rek-ai' ); ?></h2>
			<div class="rekai-shortcode-output">
				<code id="rekai-shortcode-output"></code>
				<button type="button" class="button" id="rekai-copy-shortcode">
					<?php echo esc_html__( 'Copy Shortcode', 'rek-ai' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>
