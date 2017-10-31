<?php

// Setup admin page.
function pmproz_admin_menu() {
	add_submenu_page( 'pmpro-membershiplevels', __( 'PMPro Zapier Settings', 'pmpro-zapier' ), __( 'PMPro Zapier', 'pmpro-zapier' ), 'manage_options', 'pmpro-zapier', 'pmproz_add_submenu_page' );
}

add_action( 'admin_menu', 'pmproz_admin_menu' );

function pmproz_add_submenu_page() {
	require_once( dirname( __FILE__ ) . '/settings.php' );
}

function pmproz_admin_init() {

	// Register setting.
	register_setting( 'pmproz_options', 'pmproz_options', 'pmproz_options_validate' );

	// Add sections.
	add_settings_section( 'pmproz_settings_general', __( 'Account Settings', 'pmpro-zapier' ), 'pmproz_settings_general', 'pmproz_options' );
	add_settings_section( 'pmproz_settings_triggers', __( 'Triggers', 'pmpro-zapier' ), 'pmproz_settings_triggers', 'pmproz_options' );

	// Add general fields.
	add_settings_field( 'pmproz_settings_field_api_key', __( 'API Key', 'pmpro-zapier' ), 'pmproz_settings_field_api_key', 'pmproz_options', 'pmproz_settings_general' );

	// Add trigger fields.
	add_settings_field( 'pmproz_settings_field_pmpro_added_order', __( 'New Order', 'pmpro-zapier' ), 'pmproz_settings_field_pmpro_added_order', 'pmproz_options', 'pmproz_settings_triggers' );
	add_settings_field( 'pmproz_settings_field_pmpro_added_order_url', __( 'New Order Webhook URL', 'pmpro-zapier' ), 'pmproz_settings_field_pmpro_added_order_url', 'pmproz_options', 'pmproz_settings_triggers' );
	add_settings_field( 'pmproz_settings_field_pmpro_updated_order', __( 'Updated Order', 'pmpro-zapier' ), 'pmproz_settings_field_pmpro_updated_order', 'pmproz_options', 'pmproz_settings_triggers' );
	add_settings_field( 'pmproz_settings_field_pmpro_updated_order_url', __( 'Updated Order Webhook URL', 'pmpro-zapier' ), 'pmproz_settings_field_pmpro_updated_order_url', 'pmproz_options', 'pmproz_settings_triggers' );
	add_settings_field( 'pmproz_settings_field_pmpro_after_change_membership_level', __( 'Changed Membership Level', 'pmproz' ), 'pmproz_settings_field_pmpro_after_change_membership_level', 'pmproz_options', 'pmproz_settings_triggers' );
	add_settings_field( 'pmproz_settings_field_pmpro_after_change_membership_level_url', __( 'Changed Membership Level Webhook URL', 'pmpro-zapier' ), 'pmproz_settings_field_pmpro_after_change_membership_level_url', 'pmproz_options', 'pmproz_settings_triggers' );

}

add_action( 'admin_init', 'pmproz_admin_init' );

function pmproz_options_validate( $input ) {

	$new_input                                            = array();
	$new_input['api_key']                                 = ! empty( $input['api_key'] ) ? sanitize_key( $input['api_key'] ) : '';
	$new_input['pmpro_added_order']                       = ! empty( $input['pmpro_added_order'] ) ? intval( $input['pmpro_added_order'] ) : '';
	$new_input['pmpro_updated_order']                     = ! empty( $input['pmpro_updated_order'] ) ? intval( $input['pmpro_updated_order'] ) : '';
	$new_input['pmpro_after_change_membership_level']     = ! empty( $input['pmpro_after_change_membership_level'] ) ? intval( $input['pmpro_after_change_membership_level'] ) : '';
	$new_input['pmpro_added_order_url']                   = ! empty( $input['pmpro_added_order_url'] ) ? esc_url( $input['pmpro_added_order_url'] ) : '';
	$new_input['pmpro_updated_order_url']                 = ! empty( $input['pmpro_updated_order_url'] ) ? esc_url( $input['pmpro_updated_order_url'] ) : '';
	$new_input['pmpro_after_change_membership_level_url'] = ! empty( $input['pmpro_after_change_membership_level_url'] ) ? esc_url( $input['pmpro_after_change_membership_level_url'] ) : '';

	return $new_input;
}