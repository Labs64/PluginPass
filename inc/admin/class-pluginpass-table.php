<?php

namespace PluginPass\Inc\Admin;
use PluginPass\Inc\Libraries;

/**
 * Display PluginPass registered plugins
 *
 *
 * @link       hhttps://www.labs64.com
 * @since      1.0.0
 *
 * @author     Labs64 <info@labs64.com>
 */
class Pluginpass_Table extends Libraries\WP_List_Table  {

	/**
	 * The text domain of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_text_domain    The text domain of this plugin.
	 */
	protected $plugin_text_domain;

    /*
	 * Call the parent constructor to override the defaults $args
	 *
	 * @param string $plugin_text_domain	Text domain of the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $plugin_text_domain ) {

		$this->plugin_text_domain = $plugin_text_domain;

		parent::__construct( array(
				'plural'	=>	'plugins',	// Plural value used for labels and the objects being listed.
				'singular'	=>	'plugin',		// Singular label for an object being listed, e.g. 'post'.
				'ajax'		=>	false,		// If true, the parent class will call the _js_vars() method in the footer
			) );
	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * Query, filter data, handle sorting, and pagination, and any other data-manipulation required prior to rendering
	 *
	 * @since   1.0.0
	 */
	public function prepare_items() {

		// check if a search was performed.
		$user_search_key = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : '';

		$this->_column_headers = $this->get_column_info();

		// check and process any actions such as bulk actions.
		$this->handle_table_actions();

		// fetch table data
		$table_data = $this->fetch_table_data();
		// filter the data in case of a search.
		if( $user_search_key ) {
			$table_data = $this->filter_table_data( $table_data, $user_search_key );
		}

		// required for pagination
		$plugins_per_page = $this->get_items_per_page( 'plugins_per_page' );
		$table_page = $this->get_pagenum();

		// provide the ordered data to the List Table.
		// we need to manually slice the data based on the current pagination.
		$this->items = array_slice( $table_data, ( ( $table_page - 1 ) * $plugins_per_page ), $plugins_per_page );

		// set the pagination arguments
		$total_users = count( $table_data );
		$this->set_pagination_args( array (
					'total_items' => $total_users,
					'per_page'    => $plugins_per_page,
					'total_pages' => ceil( $total_users/$plugins_per_page )
				) );
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_columns() {

		$table_columns = array(
			'cb'				=> '<input type="checkbox" />', // to display the checkbox.
			'plugin_name'		=>	__( 'Plugin Name', $this->plugin_text_domain ),
			'user_registered'	=> _x( 'Expiration Date', 'column name', $this->plugin_text_domain ),
			'ID'				=>	__( 'Last validated', $this->plugin_text_domain ),
		);

		return $table_columns;

	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * @since 1.1.0
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {

		/*
		 * actual sorting still needs to be done by prepare_items.
		 * specify which columns should have the sort icon.
		 *
		 * key => value
		 * column name_in_list_table => columnname in the db
		 */
		$sortable_columns = array (
				'ID' => array( 'ID', true ),
				'plugin_name'=>'plugin_name',
				'user_registered'=>'user_registered'
			);

		return $sortable_columns;
	}

	/**
	 * Text displayed when no plugin data is available
	 *
	 * @since   1.0.0
	 *
	 * @return void
	 */
	public function no_items() {
		_e( 'No plugins avaliable.', $this->plugin_text_domain );
	}

	/*
	 * Fetch table data from the WordPress database.
	 *
	 * @since 1.0.0
	 *
	 * @return	Array
	 */

	public function fetch_table_data() {

		global $wpdb;

		$wpdb_table = $wpdb->prefix . 'users';
		$orderby = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'user_registered';
		$order = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'ASC';

    // TODO: get plugins list: https://codex.wordpress.org/Function_Reference/get_plugins

		$user_query = "SELECT
							user_login, user_registered, ID
						FROM $wpdb_table ORDER BY $orderby $order";

		// query output_type will be an associative array with ARRAY_A.
		$query_results = $wpdb->get_results( $user_query, ARRAY_A  );

		// return result array to prepare_items.
		return $query_results;
	}

	/*
	 * Filter the table data based on the plugin search key
	 *
	 * @since 1.0.0
	 *
	 * @param array $table_data
	 * @param string $search_key
	 * @returns array
	 */
	public function filter_table_data( $table_data, $search_key ) {
		$filtered_table_data = array_values( array_filter( $table_data, function( $row ) use( $search_key ) {
			foreach( $row as $row_val ) {
				if( stripos( $row_val, $search_key ) !== false ) {
					return true;
				}
			}
		} ) );

		return $filtered_table_data;

	}

	/**
	 * Render a column when no column specific method exists.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'user_registered':
			case 'ID':
				return $item[$column_name];
			default:
			  return $item[$column_name];
		}
	}

	/**
	 * Get value for checkbox column.
	 *
	 * The special 'cb' column
	 *
	 * @param object $item A row's data
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_cb( $item ) {
		return sprintf(
				'<label class="screen-reader-text" for="user_' . $item['ID'] . '">' . sprintf( __( 'Select %s' ), $item['user_login'] ) . '</label>'
				. "<input type='checkbox' name='users[]' id='user_{$item['ID']}' value='{$item['ID']}' />"
			);
	}


	/*
	 * Method for rendering the plugin_name column.
	 *
	 * Adds row action links to the plugin_name column.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 *
	 */
	protected function column_plugin_name( $item ) {

		/*
		 *  Build table row actions.
		 *
		 * e.g. /options-general.php?page=pluginpass&action=validate&plugin_id=18&_wpnonce=1984253e5e
		 */

		$admin_page_url =  admin_url( 'options-general.php' );

		// row actions to validate plugin
		$query_args_validate_plugin = array(
			'page'		=>  wp_unslash( $_REQUEST['page'] ),
			'action'	=> 'validate_plugin',
			'plugin_id'		=> absint( $item['ID']),
			'_wpnonce'	=> wp_create_nonce( 'validate_plugin_nonce' ),
		);
		$validate_plugin_link = esc_url( add_query_arg( $query_args_validate_plugin, $admin_page_url ) );
		$actions['validate_plugin'] = '<a href="' . $validate_plugin_link . '">' . __( 'Validate', $this->plugin_text_domain ) . '</a>';

		// row actions to show validation details
		$query_args_validation_details = array(
			'page'		=>  wp_unslash( $_REQUEST['page'] ),
			'action'	=> 'validation_details',
			'plugin_id'		=> absint( $item['ID']),
			'_wpnonce'	=> wp_create_nonce( 'validation_details_nonce' ),
		);
		$validation_details_link = esc_url( add_query_arg( $query_args_validation_details, $admin_page_url ) );
		$actions['validation_details'] = '<a href="' . $validation_details_link . '">' . __( 'Details', $this->plugin_text_domain ) . '</a>';

		// row actions to deregister plugin
		$query_args_deregister_plugin = array(
			'page'		=>  wp_unslash( $_REQUEST['page'] ),
			'action'	=> 'deregister_plugin',
			'plugin_id'		=> absint( $item['ID']),
			'_wpnonce'	=> wp_create_nonce( 'deregister_plugin_nonce' ),
		);
		$deregister_plugin_link = esc_url( add_query_arg( $query_args_deregister_plugin, $admin_page_url ) );
		$actions['deregister_plugin'] = '<a href="' . $deregister_plugin_link . '">' . __( 'Deregister', $this->plugin_text_domain ) . '</a>';


		$row_value = '<strong>' . $item['user_login'] . '</strong>';
		return $row_value . $this->row_actions( $actions );
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @since    1.0.0
	 *
	 * @return array
	 */
	public function get_bulk_actions() {

		/*
		 * on hitting apply in bulk actions the url paramas are set as
		 * ?action=bulk-validate/bulk-deregister&paged=1&action2=-1
		 *
		 * action and action2 are set based on the triggers above or below the table
		 *
		 */
		 $actions = array(
			 'bulk-validate' => 'Validate Plugins',
			 'bulk-deregister' => 'Deregister Plugins'
		 );

		 return $actions;
	}

	/**
	 * Process plugin actions
	 *
	 * @since    1.0.0
	 *
	 */
	public function handle_table_actions() {

		/*
		 * Note: Table bulk_actions can be identified by checking $_REQUEST['action'] and $_REQUEST['action2']
		 *
		 * action - is set if checkbox from top-most select-all is set, otherwise returns -1
		 * action2 - is set if checkbox the bottom-most select-all checkbox is set, otherwise returns -1
		 */

		// check for individual row actions
		$the_table_action = $this->current_action();

		if ( 'validate_plugin' === $the_table_action ) {
			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );
			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'validate_plugin_nonce' ) ) {
				$this->invalid_nonce_redirect();
			}
			else {
				$this->validate_plugin( absint( $_REQUEST['plugin_id']) );
				$this->graceful_exit();
			}
		}

		if ( 'validation_details' === $the_table_action ) {
			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );
			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'validation_details_nonce' ) ) {
				$this->invalid_nonce_redirect();
			}
			else {
				$this->show_validation_details( absint( $_REQUEST['plugin_id']) );
				$this->graceful_exit();
			}
		}

		if ( 'deregister_plugin' === $the_table_action ) {
			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );
			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'deregister_plugin_nonce' ) ) {
				$this->invalid_nonce_redirect();
			}
			else {
				$this->deregister_plugin( absint( $_REQUEST['user_id']) );
				$this->graceful_exit();
			}
		}

		// check for table bulk actions
		if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'bulk-validate' ) || ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'bulk-validate' ) ) {

			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );
			// verify the nonce.
			/*
			 * Note: the nonce field is set by the parent class
			 * wp_nonce_field( 'bulk-' . $this->_args['plural'] );
			 *
			 */
			if ( ! wp_verify_nonce( $nonce, 'bulk-plugins' ) ) {
				$this->invalid_nonce_redirect();
			}
			else {
				$this->bulk_validate( $_REQUEST['plugins']);
				$this->graceful_exit();
			}
		}

		if ( ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] === 'bulk-deregister' ) || ( isset( $_REQUEST['action2'] ) && $_REQUEST['action2'] === 'bulk-deregister' ) ) {

			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );
			// verify the nonce.
			/*
			 * Note: the nonce field is set by the parent class
			 * wp_nonce_field( 'bulk-' . $this->_args['plural'] );
			 *
			 */
			if ( ! wp_verify_nonce( $nonce, 'bulk-plugins' ) ) {
				$this->invalid_nonce_redirect();
			}
			else {
				$this->bulk_deregister( $_REQUEST['plugins']);
				$this->graceful_exit();
			}
		}

	}

	/**
	 * Validate plugin.
	 *
	 * @since   1.0.0
	 *
	 * @param int $plugin_id  plugin's ID
	 */
	public function validate_plugin( $plugin_id ) {
		$user = get_user_by( 'id', $plugin_id );
		// TODO: validate plugin
	}

	/**
	 * Show validation details.
	 *
	 * @since   1.0.0
	 *
	 * @param int $plugin_id  plugin's ID
	 */
	public function show_validation_details( $plugin_id ) {
		$user = get_user_by( 'id', $plugin_id );
		include_once( 'views/partials-pluginpass-validation-details.php' );
	}

	/**
	 * Add a meta information for a plugin.
	 *
	 * @since   1.0.0
	 *
	 * @param int $plugin_id  plugin's ID
	 */
	public function deregister_plugin( $plugin_id ) {
		$user = get_user_by( 'id', $plugin_id );
		// TODO: degeregister plugin
	}

	/**
	 * Bulk validate plugins.
	 *
	 * @since   1.0.0
	 *
	 * @param array $bulk_plugin_ids
	 */
	public function bulk_validate( $plugin_ids ) {
		// TODO: bulk plugin validate
	}

	/**
	 * Bulk deregister plugins.
	 *
	 * @since   1.0.0
	 *
	 * @param array $bulk_plugin_ids
	 */
	public function bulk_deregister( $plugin_ids ) {
		// TODO: bulk deregister plugin
	}

	/**
	 * Stop execution and exit
	 *
	 * @since    1.0.0
	 *
	 * @return void
	 */
	 public function graceful_exit() {
		 exit;
	 }

	/**
	 * Die when the nonce check fails.
	 *
	 * @since    1.0.0
	 *
	 * @return void
	 */
	 public function invalid_nonce_redirect() {
		wp_die( __( 'Invalid Nonce', $this->plugin_text_domain ),
				__( 'Error', $this->plugin_text_domain ),
				array(
						'response' 	=> 403,
						'back_link' =>  esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ) , admin_url( 'options-general.php' ) ) ),
					)
		);
	 }


}
