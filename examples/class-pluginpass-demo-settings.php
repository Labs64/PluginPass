<?php

/**
 * The settings of the plugin.
 *
 * How To add options page:
 * 1) Copy this file into <plugin>/admin folder
 *
 * 2) insert below statement into <plugin>/admin/class-pluginpass-demo-admin.php
 * require_once plugin_dir_path( dirname( __FILE__ ) ) .  'admin/class-pluginpass-demo-settings.php';
 *
 * 3) insert below statements into <plugin>/includes/class-pluginpass-demo.php -> define_admin_hooks()
 * $plugin_settings = new Pluginpass_Demo_Settings( $this->get_plugin_name(), $this->get_version() );
 * $this->loader->add_action( 'admin_menu', $plugin_settings, 'pluginpass_demo_options_menu' );
 * $this->loader->add_action( 'admin_init', $plugin_settings, 'pluginpass_demo_options_page' );
 *
 * @package    Pluginpass_Demo_Plugin
 * @subpackage Pluginpass_Demo_Plugin/admin
 */
class Pluginpass_Demo_Settings {
	private $plugin_name;

	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 */
	public function pluginpass_demo_options_menu() {
		//Add the menu to the Plugins set of menu items
		add_options_page(
			'PluginPass Demo',                    // The title to be displayed in the browser window for this page.
			'PluginPass Demo',                    // The text to be displayed for this menu item
			'manage_options',                    // Which type of users can see this menu item
			'pluginpass_demo_options',            // The unique ID - that is, the slug - for this menu item
			[
				$this,
				'render_settings_page_content'
			]
		);
	}

	/**
	 */
	public function pluginpass_demo_options_page() {
		add_settings_section(
			'general_settings_section',                        // ID used to identify this section and with which to register options
			__( 'Plugin Options', 'pluginpass-demo-plugin' ),                // Title to be displayed on the administration page
			[ $this, 'general_options_callback' ],        // Callback used to render the description of the section
			'pluginpass_demo_display_options'                        // Page on which to add this section of options
		);
	}

	/**
	 * Renders a simple page to display for the theme menu defined above.
	 */
	public function render_settings_page_content() {
		?>
        <!-- Create a header in the default WordPress 'wrap' container -->
        <div class="wrap">

            <h2><?php _e( 'PluginPass Demo Options', 'pluginpass-demo-plugin' ); ?></h2>
			<?php settings_errors(); ?>

			<?php
			do_settings_sections( 'pluginpass_demo_display_options' ); ?>

        </div><!-- /.wrap -->
		<?php
	}


	public function install_and_activate_pluginpass_as_dependency() {
    $pluginpass_slug   = 'pluginpass-pro-plugintheme-licensing';
		$pluginpass_folder = "$pluginpass_slug/pluginpass.php";

		// Require PluginPass plugin
		if ( is_admin() && current_user_can( 'activate_plugins' ) && ! is_plugin_active( $pluginpass_folder ) ) {
			// plugin installed but not activated
			if ( array_key_exists( $pluginpass_folder, get_plugins() ) ) {
				$pluginpass_activate_url = wp_nonce_url(
					self_admin_url( 'plugins.php?action=activate&plugin=' . $pluginpass_folder ),
					'activate-plugin_' . $pluginpass_folder
				);

				$message = sprintf(
					'This plugin/theme requires PluginPass plugin to be installed and active.<br><br> <a class="button button-primary" href="%s">%s</a>',
					$pluginpass_activate_url,
					'Activate PluginPass now &raquo;'
				);

			} else {
				$pluginpass_install_url = wp_nonce_url(
					self_admin_url( 'update.php?action=install-plugin&plugin=' . $pluginpass_slug ),
					'install-plugin_' . $pluginpass_slug
				);

				$message = sprintf(
					'This plugin/theme requires PluginPass plugin to be installed and active.<br><br> <a class="button button-primary" href="%s">%s</a>',
					$pluginpass_install_url,
					'Install PluginPass now &raquo;'
				);
			}

			$this->show_notice( $message, 'error', false );

			exit;
		}
	}

	public function show_demonstration_form( $api_key, $product_number, $product_module_number, $plugin_folder, $has_consent ) {
		$plugins = get_plugins();
		?>
        <form method='get' action=''>
            <input type='hidden' name='page' value='pluginpass_demo_options'>
            <table class='form-table'>
                <tbody>
                <tr>
                    <th>
                        <label for='api_key'>API Key:</label>
                    </th>
                    <td>
                        <input id='api_key' type='text' name='api_key' value='<?php print $api_key ?>'
                               class='regular-text' required>
                        <p class='description'>
                            PluginPass uses the NetLicensing services to validate plugins/themes. You need to create an <a
                                    target='_blank' href='https://ui.netlicensing.io/#/settings'>API Key</a>;
                            recommended API Key role is "Licensee" role.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for='product_number'>Product Number:</label>
                    </th>
                    <td>
                        <input id='product_number' type='text' name='product_number'
                               value='<?php print $product_number ?>'
                               class='regular-text' required>
                        <p class='description'>
                            Provide NetLicensing "Product Number". Detailed configuration details can be found <a
                                    target='_blank' href='https://github.com/Labs64/PluginPass/wiki/Set-up-NetLicensing'>here</a>.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for='product_module_number'>Module Number:</label>
                    </th>
                    <td>
                        <input id='product_module_number' type='text' name='product_module_number'
                               value='<?php print $product_module_number ?>' class='regular-text' required>
                        <p class='description'>
                          Provide NetLicensing "Module Number" to be verified. Detailed configuration details can be found <a
                                  target='_blank' href='https://github.com/Labs64/PluginPass/wiki/Set-up-NetLicensing'>here</a>.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th><label for='plugin_folder'>Plugin:</label></th>
                    <td>
                        <select id="plugin_folder" name="plugin_folder">
							<?php foreach ( $plugins as $key => $plugin ): ?>
                                <option <?php if ( $key === $plugin_folder ): ?> selected <?php endif; ?>
                                        value="<?php print $key; ?>">
									<?php print $plugin['Name']; ?>
                                </option>
							<?php endforeach; ?>
                        </select>
                        <p class='description'>
                          Choose Plugin to be validated.
                        </p>
                    </td>
                </tr>
				<?php if ( ! $has_consent ): ?>
                    <tr>
                        <th>
                            <label for='product_module_number'>User Consent:</label>
                        </th>
                        <td>
                            <fieldset>
                                <legend class='screen-reader-text'>
                                    <span>User Consent</span>
                                </legend>
                                <label for='users_can_register'>
                                    <input name='has_consent' type='checkbox' value='1'>
                                    I agree to have my personal data <a
                                            target='_blank' href='https://www.labs64.com/legal/privacy-policy'>processed as follows</a>.
                                </label>
                            </fieldset>
                        </td>
                    </tr>
				<?php endif; ?>
                </tbody>
            </table>
            <p class='submit'>
                <input type='submit' name='submit' class='button button-primary' value='Validate'>
            </p>
        </form>
		<?php
	}

	protected function show_notice( $message, $type = 'success', $dismiss = true ) {
		$is_dismissible = $dismiss ? 'is-dismissible' : '';

		echo "<div class=\"notice notice-$type $is_dismissible\">
                <p>$message</p>
             </div>";
	}


	public function general_options_callback() {

		// check if pluginpass is installed and active
		$this->install_and_activate_pluginpass_as_dependency();

		$api_key = ! empty( $_GET['api_key'] )
			? $_GET['api_key']
			: get_option( 'pluginpass_api_key', '' );

		$product_number = ! empty( $_GET['product_number'] )
			? $_GET['product_number']
			: get_option( 'pluginpass_product_number', '' );

		$product_module_number = ! empty( $_GET['product_module_number'] )
			? $_GET['product_module_number']
			: get_option( 'pluginpass_product_module_number', '' );

		$plugin_folder = ! empty( $_GET['plugin_folder'] )
			? $_GET['plugin_folder']
			: get_option( 'pluginpass_plugin_folder', '' );

		$has_consent = ! empty( $_GET['has_consent'] )
			? $_GET['has_consent']
			: false;

		if ( ! empty( $_GET['submit'] ) ) {
			add_option( 'pluginpass_api_key', $api_key );
			add_option( 'pluginpass_product_number', $product_number );
			add_option( 'pluginpass_product_module_number', $product_module_number );
			add_option( 'pluginpass_plugin_folder', $plugin_folder );

			try {
				$guard = new \PluginPass\Inc\Common\PluginPass_Guard( $api_key, $product_number, $plugin_folder );

				if ( $guard->has_consent() ) {
					$has_consent = true;
				}

				if ( $has_consent ) {
					$guard->set_consent();
				}

				if ( $guard->validate( $product_module_number ) ) {
					$this->show_notice( "Module $product_module_number is valid", 'success' );
				} else {
					$shop_url = $guard->get_shop_url();
					$this->show_notice( "Module $product_module_number is not invalid. Renew or acquire license <a target='_blank' href='$shop_url'>here</a>.", 'warning' );
				}

				$result = print_r( $guard->validation_result(), true );

				$this->show_notice( "Validation result: $result", 'success' );
			} catch ( \PluginPass\Inc\Exceptions\Consent_Missing_Exception $consent_missing_exception ) {
				$this->show_notice( 'User consent must be available to process personal data!', 'error' );
			} catch ( Exception $exception ) {
				$this->show_notice( $exception->getMessage(), 'error' );
			}
		}

		$this->show_demonstration_form( $api_key, $product_number, $product_module_number, $plugin_folder, $has_consent );
	}
}
