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
			settings_fields( 'rekai-settings-general' );
			do_settings_sections( 'rekai-settings-general' );
			submit_button();
		elseif ( $rek_active_tab === 'autocomplete' ) :
			settings_fields( 'rekai-settings-autocomplete' );
			do_settings_sections( 'rekai-settings-autocomplete' );
			submit_button();
		elseif ( $rek_active_tab === 'advanced' ) :
			settings_fields( 'rekai-settings-advanced' );
			do_settings_sections( 'rekai-settings-advanced' );
			submit_button();
		endif;
		?>
	</form>
</div>
