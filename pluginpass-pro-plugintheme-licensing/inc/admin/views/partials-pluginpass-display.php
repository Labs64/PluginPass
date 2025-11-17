<?php

/**
 * The admin area of the plugin to load the Plugins List Table
 */
?>

<div class="wrap">
	<h2><?php esc_html_e( 'PluginPass Settings', 'pluginpass-pro-plugintheme-licensing' ); ?></h2>

	<div id="pluginpass">
		<div id="pluginpass-post-body">
			<form id="pluginpass-plugin-list-form" method="get">
                <?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Admin page parameter for form preservation ?>
				<input type="hidden" name="page" value="<?php echo isset( $_REQUEST['page'] ) ? esc_attr( sanitize_key( wp_unslash( $_REQUEST['page'] ) ) ) : ''; ?>"/>
				<?php
				$this->pluginpass_table->search_box( __( 'Find', 'pluginpass-pro-plugintheme-licensing' ), 'pluginpass-plugin-find' );
				$this->pluginpass_table->display();
				?>
			</form>
		</div>
	</div>
</div>
