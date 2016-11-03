<?php

if ( ! defined( 'ABSPATH' ) ) {
	global $isapage;
	$isapage = true;

	define( 'WP_USE_THEMES', false );
	require_once( '../../../../wp-load.php' );
}

global $pmproz_options, $pmpro_error;

if ( empty( $pmproz_options ) ) {
	$pmproz_options = get_option( 'pmproz_options' );
}

$data    = json_decode( file_get_contents( 'php://input' ) );
$api_key = ! empty( $_REQUEST['api_key'] ) ? sanitize_key( $_REQUEST['api_key'] ) : '';
$action  = ! empty( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';

header( 'Content-Type: application/json' );

if ( $api_key != $pmproz_options['api_key'] ) {
	status_header( 403 );
	echo json_encode( __( 'A valid API key is required.', 'pmproz' ) );
	exit;
}


switch ( $action ) {

	case 'change_membership_level':

		if ( is_numeric( $data->user ) ) {
			$user = get_userdata( $data->user );
		} elseif ( get_user_by( 'login', $data->user ) ) {
			$user = get_user_by( 'login', $data->user );
		} else {
			$user = get_user_by( 'email', $data->user );
		}

		$user_id          = $user->ID;
		$level            = $data->level;
		$old_level_status = ! empty( $data->status ) ? $data->status : 'zapier_changed';

		if ( pmpro_changeMembershipLevel( $level, $user_id, $old_level_status ) ) {
			echo json_encode( array( 'status' => 'success' ) );
		} else {
			echo json_encode( array( 'status' => 'failed', 'message' => $pmpro_error ) );
		}

		break;

	case 'add_order':

		if ( is_numeric( $data->user ) ) {
			$user = get_userdata( $data->user );
		} elseif ( get_user_by( 'login', $data->user ) ) {
			$user = get_user_by( 'login', $data->user );
		} else {
			$user = get_user_by( 'email', $data->user );
		}

		$order                = new MemberOrder();
		$order->user_id       = $user->ID;
		$order->membership_id = $data->level;

		//defaults
		$order->code                        = $order->getRandomCode();
		$order->subtotal                    = ! empty( $data->order->subtotal ) ? $data->order->subtotal : '';
		$order->tax                         = ! empty( $data->order->tax ) ? $data->order->tax : '';
		$order->couponamount                = ! empty( $data->order->couponamount ) ? $_POST['couponamount']->tax : '';
		$order->total                       = ! empty( $data->order->total ) ? $data->order->total : '';
		$order->payment_type                = ! empty( $data->order->payment_type ) ? $data->order->payment_type : '';
		$order->cardtype                    = ! empty( $data->order->cardtype ) ? $data->order->cardtype : '';
		$order->accountnumber               = ! empty( $data->order->accountnumber ) ? $data->order->accountnumber : '';
		$order->expirationmonth             = ! empty( $data->order->expirationmonth ) ? $data->order->expirationmonth : '';
		$order->expirationyear              = ! empty( $data->order->expirationyear ) ? $data->order->expirationyear : '';
		$order->status                      = ! empty( $data->order->status ) ? $data->order->status : 'success';
		$order->gateway                     = ! empty( $data->order->gateway ) ? $data->order->gateway : pmpro_getOption( 'gateway' );
		$order->gateway_environment         = ! empty( $data->order->gateway_environment ) ? $data->order->gateway_environment : pmpro_getOption( 'gateway_environment' );
		$order->payment_transaction_id      = ! empty( $data->order->payment_transaction_id ) ? $data->order->payment_transaction_id : '';
		$order->subscription_transaction_id = ! empty( $data->order->subscription_transaction_id ) ? $data->order->subscription_transaction_id : '';
		$order->affiliate_id                = ! empty( $data->order->affiliate_id ) ? $data->order->affiliate_id : '';
		$order->affiliate_subid             = ! empty( $data->order->affiliate_subid ) ? $data->order->affiliate_subid : '';
		$order->notes                       = ! empty( $data->order->notes ) ? $data->order->notes : '';
		$order->checkout_id                 = ! empty( $data->order->checkout_id ) ? $data->order->checkout_id : 0;
		$order->billing                     = new stdClass();
		$order->billing->name               = ! empty( $data->order->billing->name ) ? $data->order->billing->name : '';
		$order->billing->street             = ! empty( $data->order->billing->street ) ? $data->order->billing->street : '';
		$order->billing->city               = ! empty( $data->order->billing->city ) ? $data->order->billing->city : '';
		$order->billing->state              = ! empty( $data->order->billing->state ) ? $data->order->billing->state : '';
		$order->billing->zip                = ! empty( $data->order->billing->zip ) ? $data->order->billing->zip : '';
		$order->billing->country            = ! empty( $data->order->billing->country ) ? $data->order->billing->country : '';
		$order->billing->phone              = ! empty( $data->order->billing->phone ) ? $data->order->billing->phone : '';

		if ( $order->saveOrder() ) {
			echo json_encode( array( 'status' => 'success' ) );
		} else {
			echo json_encode( array( 'status' => 'failed', 'message' => $pmpro_error ) );
		}

		break;

	case 'update_order':
		if ( is_numeric( $data->order ) ) {
			$order = new MemberOrder( $data->order );
		} else {
			$order = new MemberOrder();
			$order->getMemberOrderByCode( $order );
		}

		$values = ! empty( $data->values ) ? $data->values : array();

		foreach ( $data->values as $key => $value ) {
			$order->$key = $value;
		}

		if ( $order->saveOrder() ) {
			echo json_encode( array( 'status' => 'success' ) );
		} else {
			echo json_encode( array( 'status' => 'failed', 'message' => $pmpro_error ) );
		}

	case 'has_membership_level':

		if ( is_numeric( $data->user ) ) {
			$user = get_userdata( $data->user );
		} elseif ( get_user_by( 'login', $data->user ) ) {
			$user = get_user_by( 'login', $data->user );
		} else {
			$user = get_user_by( 'email', $data->user );
		}

		$user_id = $user->ID;
		$level   = $data->level;

		if ( pmpro_hasMembershipLevel( $level, $user_id ) ) {
			echo json_encode( 'true' );
		} else {
			echo json_encode( 'false' );
		}

		break;

	default:
		//testing connection
		break;
}