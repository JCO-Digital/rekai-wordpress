<?php
/**
 * Provides the Rek.ai settings page.
 *
 * @package Rekai
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Rekai\Options\OptionsPage;

new OptionsPage();
?>
<div
	class="wrap">
	<h1><?php esc_html_e( 'Rek.ai Settings', 'rek-ai' ); ?></h1>

	<div class="nav-tab-wrapper">
		<?php foreach ( $rek_tabs as $rek_tab => $tab_data ) : ?>
			<a href="<?php echo esc_url( $tab_data['url'] ); ?>"
				class="nav-tab <?php echo $rek_active_tab === $rek_tab ? 'nav-tab-active' : ''; ?>">
				<?php if ( isset( $tab_data['icon'] ) ) : ?>
					<span class="<?php echo esc_attr( $tab_data['icon'] ); ?>"></span>
				<?php endif; ?>
				<?php echo esc_html( $tab_data['label'] ); ?>
			</a>
		<?php endforeach; ?>
	</div>

	<?php if ( 'docs' === $rek_active_tab ) : ?>
		<div class="docs-wrapper">
			<?php include trailingslashit( REKAI_PLUGIN_PATH ) . '/views/docs/docs.php'; ?>
		</div>
	<?php else : ?>
		<form
			method="post"
			action="<?php echo esc_url( admin_url( 'options.php' ) ); ?>">
			<?php
			if ( 'general' === $rek_active_tab ) :
				settings_fields( 'rekai-tab-general' );
				do_settings_sections( 'rekai-tab-general' );
				submit_button();
			elseif ( 'advanced' === $rek_active_tab ) :
				settings_fields( 'rekai-tab-advanced' );
				do_settings_sections( 'rekai-tab-advanced' );
				submit_button();
			endif;
			?>
		</form>
	<?php endif; ?>
</div>
