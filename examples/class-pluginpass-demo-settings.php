<?php

/**
 * The settings of the plugin.
 *
 * How To add options page:
 * 1) Copy this file into /admin folder
 *
 * 2) insert below statement into class-pluginpass-demo-admin.php
 * require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class-pluginpass-demo-settings.php';
 *
 * 3) insert below statements into class-pluginpass-demo.php -> define_admin_hooks()
 * $plugin_settings = new Pluginpass_Demo_Settings( $this->get_plugin_name(), $this->get_version() );
 * $this->loader->add_action( 'admin_menu', $plugin_settings, 'pluginpass_demo_options_menu' );
 * $this->loader->add_action( 'admin_init', $plugin_settings, 'pluginpass_demo_options_page' );
 *
 * @package    Pluginpass_Demo_Plugin
 * @subpackage Pluginpass_Demo_Plugin/admin
 */
class Pluginpass_Demo_Settings
{
    private $plugin_name;

    private $version;

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     */
    public function pluginpass_demo_options_menu()
    {
        //Add the menu to the Plugins set of menu items
        add_options_page(
            'PluginPass Demo', 					// The title to be displayed in the browser window for this page.
            'PluginPass Demo',					// The text to be displayed for this menu item
            'manage_options',					// Which type of users can see this menu item
            'pluginpass_demo_options',			// The unique ID - that is, the slug - for this menu item
            array( $this, 'render_settings_page_content')				// The name of the function to call when rendering this menu's page
        );
    }

    /**
     */
    public function pluginpass_demo_options_page()
    {
        add_settings_section(
            'general_settings_section',			            // ID used to identify this section and with which to register options
            __('Plugin Options', 'pluginpass-demo-plugin'),		        // Title to be displayed on the administration page
            array( $this, 'general_options_callback'),	    // Callback used to render the description of the section
            'pluginpass_demo_display_options'		                // Page on which to add this section of options
        );
    }

    /**
     * Renders a simple page to display for the theme menu defined above.
     */
    public function render_settings_page_content()
    {
        ?>
		<!-- Create a header in the default WordPress 'wrap' container -->
		<div class="wrap">

			<h2><?php _e('PluginPass Demo Options', 'pluginpass-demo-plugin'); ?></h2>
			<?php settings_errors(); ?>

				<?php
        do_settings_sections('pluginpass_demo_display_options'); ?>

		</div><!-- /.wrap -->
	<?php
    }

    /**
     */
    public function general_options_callback()
    {
        if (!is_plugin_active('pluginpass/pluginpass.php') and current_user_can('activate_plugins')) {
            // Stop activation redirect and show error
            wp_die('Sorry, but this plugin requires the PluginPass Plugin to be installed and active. <br>');
        }

        $api_key = '588a16b3-d8b8-4a37-8965-b217eb93dc70';
        $product_number = 'P6N6UW7U4';
        $product_module_number = 'MN5VYRR54';

        $plugin_slug = 'pluginpass-demo';
        $plugin_name = 'PluginPass Demo';

        $quard = new \PluginPass\Inc\Common\PluginPass_Guard($api_key, $product_number, $plugin_name);

        if ($quard->validate($product_module_number)) {
            echo "<div class=\"notice notice-success\"><p>Valid license for $product_module_number found</p></div>";
        } else {
            $go_to_shop = $quard->get_shop_url('Acquire licenses');
            echo "<div class=\"notice notice-error\"><p>No valid license for $product_module_number found</p><p>$go_to_shop</p></div>";
        }
    }
}
