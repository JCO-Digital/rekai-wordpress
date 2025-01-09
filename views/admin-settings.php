<?php // phpcs:ignore Squiz.Commenting.FileComment.Missing ?>
<div class="wrap">
	<h1><?php esc_html_e( 'Rek.ai Settings', 'rekai-wordpress' ); ?></h1>

	<div class="nav-tab-wrapper">
		<?php foreach ( $rek_tabs as $rek_tab => $tab_data ) : ?>
			<a href="<?php echo esc_url( $tab_data['url'] ); ?>"
				class="nav-tab <?php echo $rek_active_tab === $rek_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $tab_data['label'] ); ?></a>
		<?php endforeach; ?>
	</div>

	<form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
		<?php
		if ( $rek_active_tab === 'general' ) :
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			settings_fields( 'rekai-options-general' );
			do_settings_sections( 'rekai-options-general' );
			submit_button();
		endif;
		?>
	</form>
</div>