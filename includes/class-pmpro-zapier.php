<?php

class PMPro_Zapier {

	public $options;
	public $webhook_url;

	function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	function init() {
		// Set up PMPro hooks.
		add_action( 'pmpro_added_order', array( $this, 'pmpro_added_order' ) );
		add_action( 'pmpro_updated_order', array( $this, 'pmpro_updated_order' ) );
		add_action( 'pmpro_after_change_membership_level', array(
			$this,
			'pmpro_after_change_membership_level'
		), 10, 3 );

		// Load text domain.
		load_plugin_textdomain( 'pmpro-zapier' );

		// Get PMPro Zapier settings.
		$this->options = get_option( 'pmproz_options' );
	}

	function pmpro_added_order( $order ) {

		// Get the saved order.
		$order = new MemberOrder($order->id);

		// Remove redundant and unnecessary things.
		unset($order->ExpirationDate);
		unset($order->ExpirationDate_YdashM);
		unset($order->Gateway);
		unset($order->paypal_token);
		unset($order->session_id);

		// Add some extra data to the result.
		$data = array();

		$user  = get_userdata( $order->user_id );

		$data['username'] = $user->user_login;

		$data['order'] = $order;

		$this->prepare_request( 'pmpro_added_order' );
		$this->post( $data );
	}

	function pmpro_updated_order( $order ) {

		// Get the updated order.
		$order = new MemberOrder($order->id);

		// Remove redundant and unnecessary things.
		unset($order->ExpirationDate);
		unset($order->ExpirationDate_YdashM);
		unset($order->Gateway);
		unset($order->paypal_token);
		unset($order->session_id);

		// Add some extra data to the result.
		$data = array();

		$user  = get_userdata( $order->user_id );

		$data['username'] = $user->user_login;

		$data['order'] = $order;

		echo json_encode($data);
		die();

		$this->prepare_request( 'pmpro_updated_order' );
		$this->post( $data );
	}

	function pmpro_after_change_membership_level( $level_id, $user_id, $cancel_level ) {
		global $wpdb;

		// Get user and level object.
		$user  = get_userdata( $user_id );

		// Cancelling
		if($level_id == 0) {
			$level = new StdClass();
			$level->id = '0';
		} else {
			$level = pmpro_getMembershipLevelForUser( $user_id );

			// Unset some unnecessary things.
			unset($level->allow_signups);
			unset($level->categories);
			unset($level->code_id);
			unset($level->description);
			unset($level->id);
			unset($level->subscription_id);
		}

		// Make dates human-readable.
		if(!empty($level->enddate))
			$level->enddate = date(get_option('date_format'), $level->enddate);
		if(!empty($level->startdate))
			$level->startdate = date(get_option('date_format'), $level->startdate);

		// Add some extra data to the result.
		$data = array();
		$data['user_id']  = $user_id;
		$data['username'] = $user->user_login;
		$data['user_email'] = $user->user_email;

		// Get old level's status so we know why they changed levels.
		$sqlQuery = "SELECT status FROM {$wpdb->pmpro_memberships_users} WHERE user_id = {$user_id} AND status NOT LIKE 'active' ORDER BY id DESC LIMIT 1";
		$data['old_level_status']        = $wpdb->get_var( $sqlQuery );

		$data['level'] = $level;

		$this->prepare_request( 'pmpro_after_change_membership_level' );
		$this->post( $data );
	}

	function prepare_request( $hook ) {
		if ( empty( $this->options[ $hook ] ) && $hook != 'test' ) {
			return false;
		}
		$this->webhook_url = $this->options[ $hook . '_url' ];
	}

	function post( $data = array() ) {
		$args['headers'] = array(
			'Content-Type:' => 'application/json'
		);
		$args['body']    = json_encode( $data );

		$r = wp_remote_post( $this->webhook_url, $args );

		if ( is_wp_error( $r ) )
			pmpro_setMessage( __( 'An error occurred: ', 'pmpro-zapier' ) . $r->get_error_message(), 'pmpro_error' );
		
		return $r;
	}
}
