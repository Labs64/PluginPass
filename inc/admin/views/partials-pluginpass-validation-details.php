<?php

/**
 * The plugin area to show plugin validation details
 */
?>

<div class="wrap">
    <h2><?php echo $plugin->name; ?></h2>

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
</div>