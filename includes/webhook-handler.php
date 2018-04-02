<?php

if ( ! defined( 'ABSPATH' ) ) {
	global $isapage;
	$isapage = true;

	define( 'WP_USE_THEMES', false );
	require_once( '../../../../wp-load.php' );
}

global $pmproz_options, $pmpro_error, $logstr;

// Log string for debugging.
$logstr = "";

if ( empty( $pmproz_options ) ) {
	$pmproz_options = get_option( 'pmproz_options' );
}

$api_key = ! empty( $_REQUEST['api_key'] ) ? sanitize_key( $_REQUEST['api_key'] ) : '';
$action  = ! empty( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';

header( 'Content-Type: application/json' );

if ( $api_key != $pmproz_options['api_key'] ) {
	status_header( 403 );
	echo json_encode( __( 'A valid API key is required.', 'pmpro-zapier' ) );
	exit;
}

//for debugging
if ( defined( 'PMPRO_ZAPIER_DEBUG' ) ) {	
	$logstr = var_export($_REQUEST, true);
	
	if ( strpos( PMPRO_ZAPIER_DEBUG, "@" ) ) {
		$log_email = PMPRO_ZAPIER_DEBUG;
	} else {
		$log_email = get_option( "admin_email" );
	}
	
	wp_mail( $log_email, get_option( "blogname" ) . " Zapier Log", nl2br( $logstr ) );			
}



zapier_ipn_log( 'Data Received:' . var_export($_REQUEST, true) );
switch ( $action ) {

	case 'change_membership_level':

		zapier_ipn_log( 'change membership level called successfully.' );

		//need a user id, login, or email address and a membership level id
		$user = pmproz_get_user_data();
		$level_id = intval( pmpro_getParam( 'level_id' ) );
		$user_id = $user->ID;
		
		//old level status
		$old_level_status = pmpro_getParam('old_level_status', 'REQUEST', 'zapier_changed');
		
		$pmpro_error = '';

		// failed to get the user object.
		if( empty( $user ) ){
			$pmpro_error .= 'You must pass in a user_id, user_login, or user_email.';
		}
		
		//check the level
		if( empty( $level_id ) && $level_id !== '0' ) {
			$pmpro_error .= 'You must pass in a new level_id or 0. ';
		}
		
		if ( empty($pmpro_error) && pmpro_changeMembershipLevel( $level_id, $user_id, 'zapier_changed' ) ) {
			echo json_encode( array( 'status' => 'success' ) );
			zapier_ipn_log( 'changed level' );
		} else {

			echo json_encode( array( 'status' => 'failed', 'message' => $pmpro_error ) );
			zapier_ipn_log( $pmpro_error );
		}

		break;

	case 'add_order':

		$user = pmproz_get_user_data();

		$order                = new MemberOrder();
		$order->user_id       = $user->ID;
		$order->membership_id = intval( pmpro_getParam( 'level_id' ) );

		$order->code                        = $order->getRandomCode();
		$order->subtotal                    = pmpro_getParam( 'subtotal' );
		$order->tax                         = pmpro_getParam( 'tax' ) ;
		$order->couponamount                = pmpro_getParam( 'couponamount' );
		$order->total                       = pmpro_getParam( 'total' );
		$order->payment_type                = pmpro_getParam( 'payment_type' );
		$order->cardtype                    = pmpro_getParam( 'cardtype' );
		$order->accountnumber               = pmpro_getParam( 'accountnumber' );
		$order->expirationmonth             = pmpro_getParam( 'expirationmonth' );
		$order->expirationyear              = pmpro_getParam( 'expirationyear' );
		$order->status                      = pmpro_getParam( 'status' );
		$order->gateway                     = pmpro_getParam( 'gateway' );
		$order->gateway_environment         = pmpro_getParam( 'gateway_environment' );
		$order->payment_transaction_id      = pmpro_getParam( 'payment_transaction_id' );
		$order->subscription_transaction_id = pmpro_getParam( 'subscription_transaction_id' );
		$order->affiliate_id                = pmpro_getParam( 'affiliate_id' );
		$order->affiliate_subid             = pmpro_getParam( 'affiliate_subid' );
		$order->notes                       = pmpro_getParam( 'notes' );
		$order->checkout_id                 = pmpro_getParam( 'checkout_id' );
		$order->billing                     = new stdClass();
		$order->billing->name               = pmpro_getParam( 'billing_name' );
		$order->billing->street             = pmpro_getParam( 'billing_street' );
		$order->billing->city               = pmpro_getParam( 'billing_city' );
		$order->billing->state              = pmpro_getParam( 'billing_state' );
		$order->billing->zip                = pmpro_getParam( 'billing_zip' );
		$order->billing->country            = pmpro_getParam( 'billing_country' );
		$order->billing->phone              = pmpro_getParam( 'billing_phone' );

		if ( $order->saveOrder() ) {
			echo json_encode( array( 'status' => 'success' ) );
		} else {
			echo json_encode( array( 'status' => 'failed', 'message' => $pmpro_error ) );
		}

		break;

	case 'update_order':
		if ( is_numeric( pmpro_getParam( 'order' ) ) ) {
			$order = new MemberOrder( pmpro_getParam( 'order' ) );
		} else {
			$order = new MemberOrder();
			$order->getMemberOrderByCode( pmpro_getParam( 'order' ) );
		}

		$order->subtotal                    = pmpro_getParam( 'subtotal' );
		$order->tax                         = pmpro_getParam( 'tax' ) ;
		$order->couponamount                = pmpro_getParam( 'couponamount' );
		$order->total                       = pmpro_getParam( 'total' );
		$order->payment_type                = pmpro_getParam( 'payment_type' );
		$order->cardtype                    = pmpro_getParam( 'cardtype' );
		$order->accountnumber               = pmpro_getParam( 'accountnumber' );
		$order->expirationmonth             = pmpro_getParam( 'expirationmonth' );
		$order->expirationyear              = pmpro_getParam( 'expirationyear' );
		$order->status                      = pmpro_getParam( 'status' );
		$order->gateway                     = pmpro_getParam( 'gateway' );
		$order->gateway_environment         = pmpro_getParam( 'gateway_environment' );
		$order->payment_transaction_id      = pmpro_getParam( 'payment_transaction_id' );
		$order->subscription_transaction_id = pmpro_getParam( 'subscription_transaction_id' );
		$order->affiliate_id                = pmpro_getParam( 'affiliate_id' );
		$order->affiliate_subid             = pmpro_getParam( 'affiliate_subid' );
		$order->notes                       = pmpro_getParam( 'notes' );
		$order->checkout_id                 = pmpro_getParam( 'checkout_id' );
		$order->billing                     = new stdClass();
		$order->billing->name               = pmpro_getParam( 'billing_name' );
		$order->billing->street             = pmpro_getParam( 'billing_street' );
		$order->billing->city               = pmpro_getParam( 'billing_city' );
		$order->billing->state              = pmpro_getParam( 'billing_state' );
		$order->billing->zip                = pmpro_getParam( 'billing_zip' );
		$order->billing->country            = pmpro_getParam( 'billing_country' );
		$order->billing->phone              = pmpro_getParam( 'billing_phone' );

		if ( $order->saveOrder() ) {
			echo json_encode( array( 'status' => 'success' ) );
		} else {
			echo json_encode( array( 'status' => 'failed', 'message' => $pmpro_error ) );
		}

	case 'has_membership_level':

		$user = pmproz_get_user_data();

		$user_id = $user->ID;
		$level_id = intval( pmpro_getParam( 'level_id' ) );

		if ( pmpro_hasMembershipLevel( $level_id, $user_id ) ) {
			echo json_encode( 'true' );
		} else {
			echo json_encode( 'false' );
		}

		break;

	default:
		//testing connection
		break;
}
// write debug info to the text file.
zapier_ipn_exit();

/**
 * Helper function to retrieve the user object.
 * @return user (object)
 */
function pmproz_get_user_data(){

	$user_id = intval( pmpro_getParam( 'user_id' ) );
	$user_login = sanitize_user( pmpro_getParam( 'user_login' ) );
	$user_email = sanitize_email( pmpro_getParam('user_email' ) );

	if ( !empty($user_id) ) {
		$user = get_userdata( $user_id );
	} elseif ( !empty($user_login) ) {
		$user = get_user_by( 'login', $user_login );
	} elseif ( !empty($user_email) ) {
		$user = get_user_by( 'email', $user_email );
	}

	return $user;
}

/**
 * Serves as a buffer for logging details to text file.
 *
 * @param string $s string to log to log file.
 */
function zapier_ipn_log( $s ) {
    global $logstr;
    $logstr .= "\t" . $s . "\n";
}

/**
 * Output the log string to the text file and log what details are received.
 * Ensure PMPRO_ZAPIER_DEBUG_LOG is set to true
 */
function zapier_ipn_exit() {
    global $logstr;

    if ( $logstr ) {
        $logstr = "Logged On: " . date( "m/d/Y H:i:s" ) . "\n" . $logstr . "\n-------------\n";

        if( defined( 'PMPRO_ZAPIER_DEBUG_LOG' ) && true === PMPRO_ZAPIER_DEBUG_LOG ) {   
            echo $logstr;
            $loghandle = fopen(  PMPRO_ZAPIER_DIR . "/logs/zapier-logs.txt", "a+" );
            fwrite( $loghandle, $logstr );
            fclose( $loghandle );
        }
    }
    exit;
}
