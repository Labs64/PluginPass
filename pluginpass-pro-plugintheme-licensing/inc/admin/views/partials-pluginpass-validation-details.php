<?php

/**
 * The plugin area to show plugin validation details
 */
?>

<div class="wrap">
    <h2> <?php
		/* translators: %s: Plugin name */
		echo esc_html( sprintf( __( 'Plugin validation details for "%s"', 'pluginpass-pro-plugintheme-licensing' ), $plugin_name ) );
		?> </h2>

    <div class="pluginpass-card">
		<?php if ( ! $plugin->validated_at ): ?>
			<?php esc_html_e( 'Please go back and run validation first', 'pluginpass-pro-plugintheme-licensing' ) ?>
		<?php else: ?>
			<?php if ( ! empty( $validation_details ) ): ?>
                <ul class="list-group clear-list">
					<?php
					// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Loop variables, not global
					foreach ( $validation_details as $name => $valid ):
						?>
                        <li class="list-group-item">
                            <span><?php echo esc_html( $name ); ?></span>

                            <span class="float-right">

						<?php if ( $valid ): ?>
                            <span class="dashicons dashicons-yes"></span>
						<?php else: ?>
                            <span class="dashicons dashicons-no-alt"></span>
						<?php endif; ?>
                  </span>
                        </li>
					<?php endforeach; ?>
                </ul>
			<?php else: ?>
				<?php esc_html_e( 'No results', 'pluginpass-pro-plugintheme-licensing' ) ?>
			<?php endif; ?>
		<?php endif; ?>
    </div>

    <?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Admin page parameter for back link ?>
    <a href="<?php echo esc_url( add_query_arg( array( 'page' => isset( $_REQUEST['page'] ) ? sanitize_key( wp_unslash( $_REQUEST['page'] ) ) : '' ), admin_url( 'options-general.php' ) ) ); ?>">
		<?php esc_html_e( 'Back', 'pluginpass-pro-plugintheme-licensing' ) ?>
    </a
</div>
