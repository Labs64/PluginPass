<?php

/**
 * The plugin area to show plugin validation details
 */
?>

<div class="wrap">
    <h2> <?php echo sprintf( __( 'Plugin validation details for "%s"', 'pluginpass-pro-plugintheme-licensing' ), esc_html( $plugin_name ) ); ?> </h2>

    <div class="pluginpass-card">
		<?php if ( ! $plugin->validated_at ): ?>
			<?php _e( 'Please go back and run validation first', 'pluginpass-pro-plugintheme-licensing' ) ?>
		<?php else: ?>
			<?php if ( ! empty( $validation_details ) ): ?>
                <ul class="list-group clear-list">
					<?php foreach ( $validation_details as $name => $valid ): ?>
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
				<?php _e( 'No results', 'pluginpass-pro-plugintheme-licensing' ) ?>
			<?php endif; ?>
		<?php endif; ?>
    </div>

    <a href="<?php echo esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ), admin_url( 'options-general.php' ) ) ); ?>">
		<?php _e( 'Back', 'pluginpass-pro-plugintheme-licensing' ) ?>
    </a
</div>
