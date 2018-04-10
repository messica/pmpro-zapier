<?php
/**
 * Add PMPro Zapier Settings page to the admin menu at Memberships > PMPro Zapier.
 * Uses admin_menu hook
 */
function pmproz_admin_menu() {
	add_submenu_page( 'pmpro-membershiplevels', __( 'Zapier Settings - Paid Memberships Pro', 'pmpro-zapier' ), __( 'PMPro Zapier', 'pmpro-zapier' ), 'manage_options', 'pmpro-zapier', 'pmproz_add_submenu_page' );
}
add_action( 'admin_menu', 'pmproz_admin_menu' );

/**
 * Load the Zapier Settings page when the menu item is clicked on
 */
function pmproz_add_submenu_page() {
	require_once( PMPRO_DIR . '/adminpages/admin_header.php' );
	?>
	<div class="wrap">
		<?php settings_errors(); ?>
		<form action="options.php" method="POST">
			<h1><?php esc_attr_e( 'Paid Memberships Pro - Zapier Add On', 'pmpro-zapier' ); ?></h1>
			<p><?php printf( __( 'Integrate activity on your membership site with thousands of other apps via Zapier. <a href="%s" target="_blank">Read the documentation</a> for more information about this Add On.', 'pmpro-zapier' ), 'https://www.paidmembershipspro.com/add-ons/pmpro-zapier/' ); ?></p>
			<?php
			if ( isset( $_REQUEST['account_settings'] ) ) {
				$account = $_REQUEST['account_settings'];
			} else {
				$account = false;
			}
			?>
			<h2 class="nav-tab-wrapper">
				<a href="admin.php?page=pmpro-zapier" class="nav-tab<?php if ( empty( $account ) ) { ?> nav-tab-active<?php } ?>"><?php esc_attr_e( 'Trigger Settings', 'pmpro-zapier' ); ?></a>
				<a href="admin.php?page=pmpro-zapier&account_settings=1" class="nav-tab<?php if ( ! empty( $account ) ) { ?> nav-tab-active<?php } ?>"><?php esc_attr_e( 'Account Settings', 'pmpro-zapier' ); ?></a>
			</h2>
			<?php do_settings_sections( 'pmproz_options' ); ?>
			<?php settings_fields( 'pmproz_options' ); ?>
			<?php
			if ( ! $account ) {
				submit_button();
			}
			?>
		</form>
	</div>
	<?php 
	require_once( PMPRO_DIR . '/adminpages/admin_footer.php' );
}

/**
 * Register the settings sections and options
 */
function pmproz_admin_init() {
	// check to see if the account_settings tab is being displayed.
	if ( isset( $_REQUEST['account_settings'] ) ) {
		$account = $_REQUEST['account_settings'];
	} else {
		$account = false;
	}

	// Register setting.
	register_setting( 'pmproz_options', 'pmproz_options', 'pmproz_options_validate' );

	// Load settings for triggers.
	if( ! $account ){
		add_settings_section( 'pmproz_settings_triggers', __( 'Triggers', 'pmpro-zapier' ), 'pmproz_settings_triggers', 'pmproz_options' );
		// Add trigger fields.
		add_settings_field( 'pmproz_settings_field_pmpro_added_order', __( 'New Order', 'pmpro-zapier' ), 'pmproz_settings_field_pmpro_added_order', 'pmproz_options', 'pmproz_settings_triggers' );
		add_settings_field( 'pmproz_settings_field_pmpro_added_order_url', __( 'New Order Webhook URL', 'pmpro-zapier' ), 'pmproz_settings_field_pmpro_added_order_url', 'pmproz_options', 'pmproz_settings_triggers' );
		add_settings_field( 'pmproz_settings_field_pmpro_updated_order', __( 'Updated Order', 'pmpro-zapier' ), 'pmproz_settings_field_pmpro_updated_order', 'pmproz_options', 'pmproz_settings_triggers' );
		add_settings_field( 'pmproz_settings_field_pmpro_updated_order_url', __( 'Updated Order Webhook URL', 'pmpro-zapier' ), 'pmproz_settings_field_pmpro_updated_order_url', 'pmproz_options', 'pmproz_settings_triggers' );
		add_settings_field( 'pmproz_settings_field_pmpro_after_change_membership_level', __( 'Changed Membership Level', 'pmpro-zapier' ), 'pmproz_settings_field_pmpro_after_change_membership_level', 'pmproz_options', 'pmproz_settings_triggers' );
		add_settings_field( 'pmproz_settings_field_pmpro_after_change_membership_level_url', __( 'Changed Membership Level Webhook URL', 'pmpro-zapier' ), 'pmproz_settings_field_pmpro_after_change_membership_level_url', 'pmproz_options', 'pmproz_settings_triggers' );
	}

	// Load settings for account settings
	if( $account ) {
		// Add sections.
		add_settings_section( 'pmproz_settings_general', __( 'Account Settings', 'pmpro-zapier' ), 'pmproz_settings_general', 'pmproz_options' );
		// Add general fields.
		add_settings_field( 'pmproz_settings_field_api_key', __( 'API Key', 'pmpro-zapier' ), 'pmproz_settings_field_api_key', 'pmproz_options', 'pmproz_settings_general' );
	}
}
add_action( 'admin_init', 'pmproz_admin_init' );

/**
 * Validate PMPro Zapier settings/options
 */
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

/**
 * Settings on the From Zapier Tab
 */
function pmproz_settings_general() {
	$pmproz_options = PMPro_Zapier::get_options();
	?>
	<p><?php esc_attr_e( 'This information will be used when connecting to a Paid Memberships Pro account in Zapier.', 'pmpro-zapier' ); ?></p>
	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_attr_e( 'Webhook Handler', 'pmpro-zapier' ); ?></th>
			<td>
				<input type="text" readonly size="80" value="<?php echo esc_attr( PMPROZ_PLUGIN_URL ) . 'includes/webhook-handler.php?api_key=' . esc_attr( $pmproz_options['api_key'] ); ?>"><br/>
				<small><?php esc_attr_e( 'This is the Webhook URL used when passing data from Zapier to Paid Memberships Pro.', 'pmpro-zapier' ); ?></small>
			</td>
		</tr>
	</table>
	<?php
}

/**
 * Settings on the To Zapier Tab
 */
function pmproz_settings_triggers() {
	// no content above the settings registered above
}

/**
 * The API Key fields displayed in Memberships > PMPro Zapier.
 */
function pmproz_settings_field_api_key() {
	$pmproz_options = PMPro_Zapier::get_options();
	?>
	<input type="text" name="pmproz_options[api_key]" size=40 value="<?php echo esc_attr( $pmproz_options['api_key'] ); ?>" readonly>
	<?php
}

/**
 * The New Order Added checkbox field displayed in Memberships > PMPro Zapier.
 */
function pmproz_settings_field_pmpro_added_order() {
	$pmproz_options = PMPro_Zapier::get_options();
	$value = ! empty( $pmproz_options['pmpro_added_order'] ) ? $pmproz_options['pmpro_added_order'] : ''; ?>
	<label for="pmpro_added_order">
		<input type="checkbox" value=1 name="pmproz_options[pmpro_added_order]" id="pmpro_added_order" <?php checked( $value ); ?>>
		<?php esc_attr_e( 'Update Zapier when a new order is added.', 'pmpro-zapier' ); ?>
	</label>
	<?php
}

/**
 * The New Order Added URL field displayed in Memberships > PMPro Zapier.
 */
function pmproz_settings_field_pmpro_added_order_url() {
	$pmproz_options = PMPro_Zapier::get_options();
	$value = ! empty( $pmproz_options['pmpro_added_order_url'] ) ? $pmproz_options['pmpro_added_order_url'] : ''; ?>
	<input type="text" name="pmproz_options[pmpro_added_order_url]" size=60 value="<?php echo esc_attr( $value ); ?>">
	<?php
}

/**
 * The Updated Order checkbox field displayed in Memberships > PMPro Zapier.
 */
function pmproz_settings_field_pmpro_updated_order() {
	$pmproz_options = PMPro_Zapier::get_options();
	$value = ! empty( $pmproz_options['pmpro_updated_order'] ) ? $pmproz_options['pmpro_updated_order'] : '';
	?>
	<label for="pmpro_updated_order">
		<input type="checkbox" value=1 name="pmproz_options[pmpro_updated_order]" id="pmpro_updated_order" <?php checked( $value ); ?>>
		<?php esc_attr_e( 'Update Zapier when an order is updated.', 'pmpro-zapier' ); ?>
	</label>
	<?php
}

/**
 * The Updated Order URL field displayed in Memberships > PMPro Zapier.
 */
function pmproz_settings_field_pmpro_updated_order_url() {
	$pmproz_options = PMPro_Zapier::get_options();
	$value = ! empty( $pmproz_options['pmpro_updated_order_url'] ) ? $pmproz_options['pmpro_updated_order_url'] : ''; ?>
	<input type="text" name="pmproz_options[pmpro_updated_order_url]" size=60 value="<?php echo esc_attr( $value ); ?>">
	<?php
}

/**
 * The Change Membership Level checkbox field displayed in Memberships > PMPro Zapier.
 */
function pmproz_settings_field_pmpro_after_change_membership_level() {
	$pmproz_options = PMPro_Zapier::get_options();
	$value = !empty( $pmproz_options['pmpro_after_change_membership_level'] ) ? $pmproz_options['pmpro_after_change_membership_level'] : '';
	?>
	<label for="pmpro_after_change_membership_level">
		<input type="checkbox" value=1 name="pmproz_options[pmpro_after_change_membership_level]" id="pmpro_after_change_membership_level" <?php checked( $value ); ?>>
		<?php _e( 'Update Zapier when a user changes membership levels. The old level status will be added if available.', 'pmpro-zapier' ); ?>
	</label>
	<?php
}

/**
 * The Change Membership Level URL field displayed in Memberships > PMPro Zapier.
 */
function pmproz_settings_field_pmpro_after_change_membership_level_url() {
	$pmproz_options = PMPro_Zapier::get_options();
	$value = ! empty( $pmproz_options['pmpro_after_change_membership_level_url'] ) ? $pmproz_options['pmpro_after_change_membership_level_url'] : '';
	?>
	<input type="text" name="pmproz_options[pmpro_after_change_membership_level_url]" size=60 value="<?php echo esc_attr( $value ); ?>">
<?php } ?>
