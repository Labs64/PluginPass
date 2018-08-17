<?php

/**
 * The plugin area to show plugin validation details
 */

	if( current_user_can('edit_users' ) ) { ?>
		<h2> <?php echo __('Show plugin validation details for ' . $user->display_name . ' (' . $user->user_login . ')', $this->plugin_text_domain ); ?> </h2>
<?php

		$validation_details = get_user_meta( $plugin_id );
		echo '<div class="card">';
		foreach( $validation_details as $key => $value ) {
			$v = (is_array($value)) ? implode(', ', $value) : $value;
			echo '<p">'. $key . ': ' . $v . '</p>';
		}
		echo '</div><br>';
?>
		<a href="<?php echo esc_url( add_query_arg( array( 'page' => wp_unslash( $_REQUEST['page'] ) ) , admin_url( 'options-general.php' ) ) ); ?>"><?php _e( 'Back', $this->plugin_text_domain ) ?></a>
<?php
	}
	else {
?>
		<p> <?php echo __( 'You are not authorized to perform this operation.', $this->plugin_text_domain ) ?> </p>
<?php
	}
