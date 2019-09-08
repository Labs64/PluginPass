<?php

namespace PluginPass\Inc\Admin;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link https://www.labs64.com
 * @since 1.0.0
 *
 * @author Labs64 <info@labs64.com>
 */
class Pluginpass_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The text domain of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $plugin_text_domain The text domain of this plugin.
	 */
	private $plugin_text_domain;

	/**
	 * WP_List_Table object
	 *
	 * @since 1.0.0
	 * @access   private
	 * @var      pluginpass_table $pluginpass_table
	 */
	private $pluginpass_table;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version The version of this plugin.
	 * @param string $plugin_text_domain The text domain of this plugin
	 *
	 * @since 1.0.0
	 */
	public function __construct( $plugin_name, $version, $plugin_text_domain ) {
		$this->plugin_name        = $plugin_name;
		$this->version            = $version;
		$this->plugin_text_domain = $plugin_text_domain;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pluginpass-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		$params = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
		wp_enqueue_script( 'sweetalert2', plugin_dir_url( __FILE__ ) . 'js/sweetalert2.all.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'pluginpass_ajax_handle', plugin_dir_url( __FILE__ ) . 'js/pluginpass-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( 'pluginpass_ajax_handle', 'params', $params );
	}

	/**
	 * Callback for the options sub-menu in define_admin_hooks() for class Init.
	 *
	 * @since 1.0.0
	 */
	public function add_plugin_admin_menu() {
		$page_hook = add_options_page(
			__( 'PluginPass', $this->plugin_text_domain ), //page title
			__( 'PluginPass', $this->plugin_text_domain ), //menu title
			'manage_options', //capability
			$this->plugin_name, //menu_slug,
			array( $this, 'load_pluginpass_table' )
		);

		/*
		 * The $page_hook_suffix can be combined with the load-($page_hook) action hook
		 * https://codex.wordpress.org/Plugin_API/Action_Reference/load-(page)
		 *
		 * The callback below will be called when the respective page is loaded
		 *
		 */
		add_action( 'load-' . $page_hook, array( $this, 'load_pluginpass_table_screen_options' ) );
	}

	/*
	*  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
	*/
	public function add_plugin_action_links( $links ) {
		$links[] = '<a href="https://github.com/Labs64/PluginPass/wiki" target="_blank">Docs</a>';
	   $settings_link = array(
	      '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __( 'Settings', $this->plugin_name ) . '</a>',
	   );
	   return array_merge(  $settings_link, $links );
	}

	/**
	 * Screen options for the List Table
	 *
	 * Callback for the load-($page_hook_suffix)
	 * Called when the plugin page is loaded
	 *
	 * @since 1.0.0
	 */
	public function load_pluginpass_table_screen_options() {
		$arguments = array(
			'label'   => __( 'Number of items per page:', $this->plugin_text_domain ),
			'default' => 5,
			'option'  => 'plugins_per_page'
		);

		add_screen_option( 'per_page', $arguments );

		// instantiate the Plugins List Table
		$this->pluginpass_table = new Pluginpass_Table( $this->plugin_text_domain );
	}

	/*
	 * Display the Plugins List Table
	 *
	 * Callback for the load_pluginpass_table() in the add_plugin_admin_menu() method of this class.
	 *
	 * @since 1.0.0
	 */
	public function load_pluginpass_table() {
		// query, filter, and sort the data
		$this->pluginpass_table->prepare_items();

		// render the List Table
		include_once( 'views/partials-pluginpass-display.php' );
	}
}
