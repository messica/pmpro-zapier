<?php
/**
 * Settings page at Memberships > PMPro Zapier.
 */
global $pmproz_options;
$pmproz_options = get_option( 'pmproz_options' );

/**
 * The settings page display content at Memberships > PMPro Zapier.
 */
function pmproz_settings_general() {
	global $pmproz_options;
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

function pmproz_settings_triggers() {
}

/**
 * The API Key fields displayed in Memberships > PMPro Zapier.
 */
function pmproz_settings_field_api_key() {
	global $pmproz_options;
	if ( empty( $pmproz_options['api_key'] ) ) {
		// Generate a random API key.
		$pmproz_options['api_key'] = wp_generate_password( 32, false );
		update_option( 'pmproz_options', $pmproz_options );
	}
	?>
	<input type="text" name="pmproz_options[api_key]" size=40 value="<?php echo esc_attr( $pmproz_options['api_key'] ); ?>" readonly>
	<?php
}

/**
 * The New Order Added checkbox field displayed in Memberships > PMPro Zapier.
 */
function pmproz_settings_field_pmpro_added_order() {
	global $pmproz_options;
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
	global $pmproz_options;
	$value = ! empty( $pmproz_options['pmpro_added_order_url'] ) ? $pmproz_options['pmpro_added_order_url'] : ''; ?>
	<input type="text" name="pmproz_options[pmpro_added_order_url]" size=60 value="<?php echo esc_attr( $value ); ?>">
	<?php
}

/**
 * The Updated Order checkbox field displayed in Memberships > PMPro Zapier.
 */
function pmproz_settings_field_pmpro_updated_order() {
	global $pmproz_options;
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
	global $pmproz_options;
	$value = ! empty( $pmproz_options['pmpro_updated_order_url'] ) ? $pmproz_options['pmpro_updated_order_url'] : ''; ?>
	<input type="text" name="pmproz_options[pmpro_updated_order_url]" size=60 value="<?php echo esc_attr( $value ); ?>">
	<?php
}

/**
 * The Change Membership Level checkbox field displayed in Memberships > PMPro Zapier.
 */
function pmproz_settings_field_pmpro_after_change_membership_level() {
	global $pmproz_options;
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
	global $pmproz_options;
	$value = ! empty( $pmproz_options['pmpro_after_change_membership_level_url'] ) ? $pmproz_options['pmpro_after_change_membership_level_url'] : '';
	?>
	<input type="text" name="pmproz_options[pmpro_after_change_membership_level_url]" size=60 value="<?php echo esc_attr( $value ); ?>">
<?php } ?>

<?php require_once( PMPRO_DIR . '/adminpages/admin_header.php' ); ?>

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

<?php require_once( PMPRO_DIR . '/adminpages/admin_footer.php' ); ?>
