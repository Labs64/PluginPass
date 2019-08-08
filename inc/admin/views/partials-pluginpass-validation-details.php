<?php

/**
 * The plugin area to show plugin validation details
 */
?>

<div class="wrap">
    <h2> <?php echo __('Plugin validation details for "' . $plugin->name . '"', $this->plugin_text_domain ); ?> </h2>

    <div class="pluginpass-card">
        <ul class="list-group clear-list">
			<?php foreach ( $validation_details as $name => $valid ): ?>
                <li class="list-group-item">
                    <span><?php echo $name; ?></span>

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
    </div>

    <a href="<?php echo esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ) , admin_url( 'options-general.php' ) ) ); ?>">
        <?php _e( 'Back', $this->plugin_text_domain ) ?>
    </a
</div>
