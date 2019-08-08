<?php

/**
 * The admin area of the plugin to load the Plugins List Table
 */
?>

<div class="wrap">
    <h2><?php _e( 'PluginPass Settings', $this->plugin_text_domain ); ?></h2>

    <div id="pluginpass">
        <div id="pluginpass-post-body">
            <form id="pluginpass-plugin-list-form" method="get">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
				<?php
				$this->pluginpass_table->search_box( __( 'Find', $this->plugin_text_domain ), 'pluginpass-plugin-find' );
				$this->pluginpass_table->display();
				?>
            </form>
        </div>
    </div>
</div>
