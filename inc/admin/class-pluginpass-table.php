<?php

namespace PluginPass\Inc\Admin;

use NetLicensing\Constants;
use NetLicensing\NetLicensingService;
use NetLicensing\RestException;
use PluginPass\Inc\Common\Traits\PluginPass_Plugable;
use PluginPass\Inc\Common\Traits\PluginPass_Validatable;
use PluginPass\Inc\Core\Activator;
use PluginPass\Inc\Libraries;
use PluginPass\Inc\Common\PluginPass_Dot;
use Exception;
use DateTime;
use PluginPass as NS;

/**
 * Display PluginPass registered plugins
 *
 *
 * @link       hhttps://www.labs64.com
 * @since 1.0.0
 *
 * @author     Labs64 <info@labs64.com>
 */
class PluginPass_Table extends Libraries\WP_List_Table {
	use PluginPass_Validatable;
	use PluginPass_Plugable;


	/**
	 * The text domain of this plugin.
	 *
	 * @since 1.0.0
	 * @access   private
	 * @var      string $plugin_text_domain The text domain of this plugin.
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
			'plural'   => 'plugins',    // Plural value used for labels and the objects being listed.
			'singular' => 'plugin',  // Singular label for an object being listed, e.g. 'post'.
			'ajax'     => false,        // If true, the parent class will call the _js_vars() method in the footer
		) );

		NetLicensingService::getInstance()->curl()->setUserAgent( 'NetLicensing/PHP/' . NS\PLUGIN_NAME . ' ' . PHP_VERSION . '/' . NS\PLUGIN_VERSION . ' (https://netlicensing.io)' . '; ' . $_SERVER['HTTP_USER_AGENT'] );

	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * Query, filter data, handle sorting, and pagination, and any other data-manipulation required prior to rendering
	 *
	 * @since 1.0.0
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();
		// check and process any actions such as bulk actions.
		$this->handle_table_actions();

		// check if a search was performed.
		$search_key = isset( $_REQUEST['s'] ) ? wp_unslash( trim( $_REQUEST['s'] ) ) : null;

		if ( $search_key ) {
			$search_key = "%$search_key%";
		}

		// required for pagination
		$page     = $this->get_pagenum() - 1;
		$per_page = $this->get_items_per_page( 'plugins_per_page' );
		$order_by = ( isset( $_GET['orderby'] ) ) ? esc_sql( $_GET['orderby'] ) : 'expires_ttl_at';
		$order    = ( isset( $_GET['order'] ) ) ? esc_sql( $_GET['order'] ) : 'ASC';

		// fetch table data
		$data = $this->fetch_table_data( $page, $per_page, $order_by, $order, $search_key );

		// provide the ordered data to the List Table.
		// we need to manually slice the data based on the current pagination.
		$this->items = $data['items'];

		// set the pagination arguments
		$total_items = $data['total_items'];

		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page )
		) );
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * @return array
	 * @since 1.0.0
	 *
	 */
	public function get_columns() {
		$table_columns = array(
			'cb'           => '<input type="checkbox" />', // to display the checkbox.
			'plugin_name'  => __( 'Plugin Name', $this->plugin_text_domain ),
			'expires_at'   => _x( 'Expiration Date', 'column name', $this->plugin_text_domain ),
			'validated_at' => __( 'Last Validated', $this->plugin_text_domain ),
			'status'       => __( 'Status', $this->plugin_text_domain ),
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
	 * @return array
	 * @since 1.1.0
	 *
	 */
	protected function get_sortable_columns() {
		/*
		 * actual sorting still needs to be done by prepare_items.
		 * specify which columns should have the sort icon.
		 *
		 * key => value
		 * column name_in_list_table => columnname in the db
		 */
		$sortable_columns = array(
			'validated_at' => 'validated_at',
		);

		return $sortable_columns;
	}

	/**
	 * Text displayed when no plugin data is available
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function no_items() {
		_e( 'No plugins registered', $this->plugin_text_domain );
	}

	/*
	 * Fetch table data from the WordPress database.
	 *
	 * @since 1.0.0
	 *
	 * @return	Array
	 */

	public function fetch_table_data( $page, $per_page, $order_by = 'plugin_name', $order = 'ASC', $search_key = null ) {
		global $wpdb;

		$plugins_table = Activator::get_plugins_table_name();

		$query      = "SELECT * FROM $plugins_table";
		$countQuery = "SELECT COUNT(ID) as total_items FROM $plugins_table";

		if ( $search_key ) {
			$query      .= " WHERE plugin_name LIKE '$search_key'";
			$countQuery .= " WHERE plugin_name LIKE '$search_key'";
		}

		$query .= " ORDER BY $order_by $order LIMIT $page, $per_page";

		// query output_type will be an associative array with ARRAY_A.
		$plugins     = $wpdb->get_results( $query, ARRAY_A );
		$total_items = $wpdb->get_row( $countQuery )->total_items;

		$items = [];

		foreach ( $plugins as $plugin ) {
			if ( array_key_exists( $plugin['plugin_folder'], get_plugins() ) ) {
				$plugin['validation_result'] = json_decode( $plugin['validation_result'], true );

				$items[] = $plugin;
			}
		}

		// return result array to prepare_items.
		return [ 'items' => $items, 'total_items' => $total_items ];
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
		return $item[ $column_name ];
	}

	/**
	 * Get value for checkbox column.
	 *
	 * The special 'cb' column
	 *
	 * @param object $item A row's data
	 *
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<label class="screen-reader-text" for="plugins_' . $item['ID'] . '">' . sprintf( __( 'Select %s', $this->plugin_text_domain ), $this->get_plugin_name( $item['plugin_folder'] ) ) . '</label>'
			. "<input type='checkbox' name='plugins[]' id='plugins_{$item['ID']}' value='{$item['ID']}' />"
		);
	}


	/*
	 * Method for rendering the name column.
	 *
	 * Adds row action links to the name column.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 *
	 */
	/**
	 * @param $item
	 *
	 * @return string
	 */
	protected function column_plugin_name( $item ) {
		/*
		 *  Build table row actions.
		 *
		 * e.g. /options-general.php?page=pluginpass&action=validate&plugin_id=18&_wpnonce=1984253e5e
		 */

		$admin_page_url = admin_url( 'options-general.php' );

		// row actions to validate plugin
		$query_args_validate_plugin = array(
			'page'      => wp_unslash( $_REQUEST['page'] ),
			'action'    => 'validate_plugin',
			'plugin_id' => absint( $item['ID'] ),
			'_wpnonce'  => wp_create_nonce( 'validate_plugin_nonce' ),
		);

		$class = empty( $item['consented_at'] ) ? 'need-consent-before-validation' : '';

		$validate_plugin_link       = esc_url( add_query_arg( $query_args_validate_plugin, $admin_page_url ) );
		$actions['validate_plugin'] = '<a class="' . $class . '" href="' . $validate_plugin_link . '">' . __( 'Validate', $this->plugin_text_domain ) . '</a>';

		if ( ! empty( $item['validated_at'] ) ) {
			// row actions to show validation details
			$query_args_validation_details = array(
				'page'      => wp_unslash( $_REQUEST['page'] ),
				'action'    => 'validation_details',
				'plugin_id' => absint( $item['ID'] ),
				'_wpnonce'  => wp_create_nonce( 'validation_details_nonce' ),
			);
			$validation_details_link       = esc_url( add_query_arg( $query_args_validation_details, $admin_page_url ) );
			$actions['validation_details'] = '<a href="' . $validation_details_link . '">' . __( 'Details', $this->plugin_text_domain ) . '</a>';
		}

		// row actions to deregister plugin
		$query_args_deregister_plugin = array(
			'page'      => wp_unslash( $_REQUEST['page'] ),
			'action'    => 'deregister_plugin',
			'plugin_id' => absint( $item['ID'] ),
			'_wpnonce'  => wp_create_nonce( 'deregister_plugin_nonce' ),
		);
		$deregister_plugin_link       = esc_url( add_query_arg( $query_args_deregister_plugin, $admin_page_url ) );
		$actions['deregister_plugin'] = '<a class="need-deregister-confirmation" href="' . $deregister_plugin_link . '">' . __( 'Deregister', $this->plugin_text_domain ) . '</a>';
		$row_value                    = '<strong>' . $this->get_plugin_name( $item['plugin_folder'] ) . '</strong>';

		return $row_value . $this->row_actions( $actions );
	}

	protected function column_expires_at( $item ) {
		$expires_at = '';

		if ( ! empty( $item['validation_result'] ) ) {
			foreach ( $item['validation_result'] as $result ) {
				if ( ! empty( $result['expires'] ) ) {
					if ( ! $expires_at || strtotime( $result['expires'] ) < strtotime( $expires_at ) ) {
						$expires_at = $result['expires'];
					}
				}
			}
		}

		return ( $expires_at ) ? date( 'Y-m-d H:i:s', strtotime( $expires_at ) ) : '';
	}

	/*
	 * Method for rendering the status column.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 *
	 */
	/**
	 * @param $item
	 *
	 * @return string
	 */
	protected function column_status( $item ) {
		$valid   = 0;
		$invalid = 0;

		if ( ! empty( $item['validation_result'] ) ) {
			foreach ( $item['validation_result'] as $product_module => $results ) {
				$licensing_model = $results['licensingModel'];

				if ( $licensing_model === Constants::LICENSING_MODEL_MULTI_FEATURE ) {
					foreach ( $results as $key => $result ) {
						if ( is_array( $result ) ) {
							if ( PluginPass_Dot::get( $result, '0.valid' ) === 'true' ) {
								$valid ++;
							} else {
								$invalid ++;
							}
						}
					}
				} else {
					if ( $results['valid'] === 'true' ) {
						$valid ++;
					} else {
						$invalid ++;
					}
				}
			}
		}

		if ( ! $item['validated_at'] ) {
			return '<span class="label label-danger">' . __( 'not validated', $this->plugin_text_domain ) . '</span>';
		}

		if ( $valid > 0 && $invalid === 0 ) {
			return '<span class="label label-primary">' . __( 'valid', $this->plugin_text_domain ) . '</span>';
		}

		if ( $invalid > 0 && $valid === 0 ) {
			return '<span class="label label-danger">' . __( 'invalid', $this->plugin_text_domain ) . '</span>';
		}

		return '<span class="label label-warning">' . sprintf( __( '%s valid', $this->plugin_text_domain ), $valid . '/' . $invalid ) . '</span>';
	}


	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 * @since 1.0.0
	 *
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
			'bulk-validate'   => 'Validate Plugins',
			'bulk-deregister' => 'Deregister Plugins'
		);

		return $actions;
	}

	/**
	 * Process plugin actions
	 *
	 * @since 1.0.0
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
			} else {
				$this->validate_plugin( absint( $_REQUEST['plugin_id'] ) );
//				$this->graceful_exit();
			}
		}

		if ( 'validation_details' === $the_table_action ) {
			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );
			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'validation_details_nonce' ) ) {
				$this->invalid_nonce_redirect();
			} else {
				$this->show_validation_details( absint( $_REQUEST['plugin_id'] ) );
//				$this->graceful_exit();
			}
		}

		if ( 'deregister_plugin' === $the_table_action ) {
			$nonce = wp_unslash( $_REQUEST['_wpnonce'] );
			// verify the nonce.
			if ( ! wp_verify_nonce( $nonce, 'deregister_plugin_nonce' ) ) {
				$this->invalid_nonce_redirect();
			} else {
				$this->deregister_plugin( absint( $_REQUEST['plugin_id'] ) );
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
			} else {
				$this->bulk_validate( $_REQUEST['plugins'] );
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
			} else {
				$this->bulk_deregister( $_REQUEST['plugins'] );
			}
		}
	}

	/**
	 * Validate plugin.
	 *
	 * @param int $plugin_id plugin's ID
	 *
	 * @since 1.0.0
	 *
	 */
	public function validate_plugin( $plugin_id ) {
		$this->bulk_validate( [ $plugin_id ] );
	}

	/**
	 * Show validation details.
	 *
	 * @param int $plugin_id plugin's ID
	 *
	 * @since 1.0.0
	 *
	 */
	public function show_validation_details( $plugin_id ) {
		try {
			// get plugin
			$plugin = $this->get_plugin( [ 'ID' => $plugin_id ] );

			if ( ! $plugin ) {
				throw new Exception( __( 'Plugin not found', $this->plugin_text_domain ) );
			}

			$plugin_name = $this->get_plugin_name( $plugin->plugin_folder );

			$validation_details = [];

			if ( ! empty( $plugin->validation_result ) ) {
				foreach ( $plugin->validation_result as $data ) {
					if ( $data['licensingModel'] === Constants::LICENSING_MODEL_MULTI_FEATURE ) {
						foreach ( $data as $features ) {
							if ( is_array( $features ) ) {
								$feature                                       = reset( $features );
								$validation_details[ $feature['featureName'] ] = $feature['valid'] === 'true';
							}
						}

						continue;
					}

					$validation_details[ $data['productModuleName'] ] = $data['valid'] === 'true';
				}
			}

			include_once( 'views/partials-pluginpass-validation-details.php' );
			$this->graceful_exit();
		} catch ( Exception $exception ) {
			$this->show_notice( $exception->getMessage(), 'error', true );
		}
	}

	/**
	 * Add a meta information for a plugin.
	 *
	 * @param int $plugin_id plugin's ID
	 *
	 * @since 1.0.0
	 *
	 */
	public function deregister_plugin( $plugin_id ) {
		$this->bulk_deregister( [ $plugin_id ] );
	}

	/**
	 * Bulk validate plugins.
	 *
	 * @param $plugin_ids
	 *
	 * @throws \ErrorException
	 * @since 1.0.0
	 */
	public function bulk_validate( $plugin_ids ) {
		$errors = [];

		$count = 0;

		$has_consent = ! empty( $_GET['has_consent'] ) ? true : false;

		$plugins = $this->get_plugins( [ 'ID' => $plugin_ids ] );

		foreach ( $plugin_ids as $plugin_id ) {
			// get plugin
			$plugin = isset( $plugins[ $plugin_id ] ) ? $plugins[ $plugin_id ] : null;

			try {
				if ( ! $plugin ) {
					throw new Exception( sprintf( __( 'Plugin not found', $this->plugin_text_domain ) ) );
				}

				if ( empty( $plugin->consented_at ) && ! $has_consent ) {
					continue;
				}

				$result = self::validate( $plugin->api_key, $plugin->product_number );

				/** @var  $ttl DateTime */
				$ttl            = $result->getTtl();
				$expires_ttl_at = $ttl->format( DATE_ATOM );
				$validation     = json_encode( $result->getValidations() );

				$data = [
					'expires_ttl_at'    => $expires_ttl_at,
					'validated_at'      => date( DATE_ATOM ),
					'validation_result' => $validation,
				];

				if ( empty( $plugin->consented_at ) ) {
					$data['consented_at'] = date( DATE_ATOM );
				}

				$this->update_plugin( $data, [ 'ID' => $plugin_id ] );

				$count ++;
			} catch ( RestException $rest_exception ) {
				$request = NetLicensingService::getInstance()->lastCurlInfo();

				if ( $request->httpStatusCode === 401 ) {
					$errors[] = sprintf( __( 'Failed to validate the plugin %s, please contact the plugin developer.', $this->plugin_text_domain ), $this->get_plugin_name( $plugin->plugin_folder ) );
				} else {
					$errors[] = $rest_exception->getMessage();
				}
			} catch ( Exception $exception ) {
				$errors[] = $exception->getMessage();
			}
		}

		if ( ! empty( $errors ) ) {
			foreach ( $errors as $error ) {
				$this->show_notice( $error, 'error', true );
			}
		}

		if ( $count > 0 ) {
			$this->show_notice( $count . __( ' plugin(s) have been validated', $this->plugin_text_domain ), 'success', true );
		}
	}

	/**
	 * Bulk deregister plugins.
	 *
	 * @param $plugin_ids
	 */
	public function bulk_deregister( $plugin_ids ) {
		$errors = [];

		$count = 0;

		$plugins = $this->get_plugins( [ 'ID' => $plugin_ids ] );

		foreach ( $plugin_ids as $plugin_id ) {
			// get plugin
			$plugin = isset( $plugins[ $plugin_id ] ) ? $plugins[ $plugin_id ] : null;

			try {
				if ( ! $plugin ) {
					throw new Exception( sprintf( __( 'Plugin not found', $this->plugin_text_domain ) ) );
				}

				$this->delete_plugin( [ 'ID' => $plugin_id ] );

				$count ++;
			} catch ( Exception $exception ) {
				$errors[] = $exception->getMessage();
			}
		}

		if ( ! empty( $errors ) ) {
			foreach ( $errors as $error ) {
				$this->show_notice( $error, 'error', true );
			}
		}

		if ( $count > 0 ) {
			$this->show_notice( $count . __( ' plugin(s) have been deregistered', $this->plugin_text_domain ), 'success', true );
		}
	}

	/**
	 * Stop execution and exit
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function graceful_exit() {
		exit;
	}

	/**
	 * Die when the nonce check fails.
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function invalid_nonce_redirect() {
		wp_die( __( 'Invalid Nonce', $this->plugin_text_domain ),
			__( 'Error', $this->plugin_text_domain ),
			array(
				'response'  => 403,
				'back_link' => esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ), admin_url( 'options-general.php' ) ) ),
			)
		);
	}


	protected function show_notice( $message, $type = 'success', $dismiss = true ) {
		$is_dismissible = $dismiss ? 'is-dismissible' : '';

		echo "<div class=\"notice notice-$type $is_dismissible\">
                <p>$message</p>
             </div>";
	}

	protected function get_plugin_name( $plugin_folder, $default = '' ) {
		return isset( get_plugins()[ $plugin_folder ]['Name'] ) ? get_plugins()[ $plugin_folder ]['Name'] : $default;
	}
}
