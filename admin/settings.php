<?php

global $pmproz_options;
$pmproz_options = get_option('pmproz_options');

function pmproz_settings_general() {
	?>
	<p>
		<?php _e( 'Enter these when connecting to a Paid Memberships Pro account in Zapier.', 'pmpro-zapier' ); ?>
	</p>
	<table class="form-table">
		<tr>
			<th scope="row"><?php _e( 'Webhook Handler', 'pmpro-zapier' ); ?></th>
			<td>
				<input type="text" readonly size=100 value="<?php echo plugins_url('/includes/webhook-handler.php', __DIR__ ); ?>">
			</td>
		</tr>
	</table>
	<?php

}

function pmproz_settings_triggers() {}

function pmproz_settings_field_api_key() {

	global $pmproz_options;

	if( empty( $pmproz_options[ 'api_key' ] ) ) {

		// Generate a random API key.
		$pmproz_options['api_key'] = wp_generate_password( 32, false );
		update_option('pmproz_options', $pmproz_options);
	}
	?>
	<input type="text" name="pmproz_options[api_key]" size=40 value="<?php echo $pmproz_options['api_key']; ?>" readonly>
	<?php
}

function pmproz_settings_field_pmpro_added_order() {

	global $pmproz_options;
	$value = !empty( $pmproz_options[ 'pmpro_added_order' ] ) ? $pmproz_options[ 'pmpro_added_order' ] : '';
	?>
	<label for="pmpro_added_order">
		<input type="checkbox" value=1 name="pmproz_options[pmpro_added_order]" id="pmpro_added_order" <?php checked($value); ?>>
		<?php _e( 'Update Zapier when a new order is added.', 'pmpro-zapier' ); ?>
	</label>
	<?php
}

function pmproz_settings_field_pmpro_added_order_url() {
	global $pmproz_options;
	$value = !empty( $pmproz_options[ 'pmpro_added_order_url' ] ) ? $pmproz_options[ 'pmpro_added_order_url' ] : '';
	?>
	<input type="text" name="pmproz_options[pmpro_added_order_url]" size=60 value="<?php echo $value; ?>">
	<?php
}

function pmproz_settings_field_pmpro_updated_order() {
	global $pmproz_options;
	$value = !empty( $pmproz_options[ 'pmpro_updated_order' ] ) ? $pmproz_options[ 'pmpro_updated_order' ] : '';
	?>
	<label for="pmpro_updated_order">
		<input type="checkbox" value=1 name="pmproz_options[pmpro_updated_order]" id="pmpro_updated_order" <?php checked($value); ?>>
		<?php _e( 'Update Zapier when an order is updated.', 'pmpro-zapier' ); ?>
	</label>
	<?php
}

function pmproz_settings_field_pmpro_updated_order_url() {
	global $pmproz_options;
	$value = !empty( $pmproz_options[ 'pmpro_updated_order_url' ] ) ? $pmproz_options[ 'pmpro_updated_order_url' ] : '';
	?>
	<input  type="text" name="pmproz_options[pmpro_updated_order_url]" size=60 value="<?php echo $value; ?>">
	<?php
}

function pmproz_settings_field_pmpro_after_change_membership_level() {
	global $pmproz_options;
	$value = !empty( $pmproz_options[ 'pmpro_after_change_membership_level' ] ) ? $pmproz_options[ 'pmpro_after_change_membership_level' ] : '';
	?>
	<label for="pmpro_after_change_membership_level">
		<input type="checkbox" value=1 name="pmproz_options[pmpro_after_change_membership_level]" id="pmpro_after_change_membership_level" <?php checked( $value ); ?>>
		<?php _e( 'Update Zapier when a user changes membership levels. The old level status will be added if available.', 'pmpro-zapier' ); ?>
	</label>
	<?php
}

function pmproz_settings_field_pmpro_after_change_membership_level_url() {
	global $pmproz_options;
	$value = !empty( $pmproz_options[ 'pmpro_after_change_membership_level_url' ] ) ? $pmproz_options[ 'pmpro_after_change_membership_level_url' ] : '';
	?>
	<input  type="text" name="pmproz_options[pmpro_after_change_membership_level_url]" size=60 value="<?php echo $value; ?>">

<?php }

?>

<div class="wrap">
	<form action="options.php" method="POST">
		<h1><?php _e('Paid Memberships Pro Zapier', 'pmproz'); ?></h1>
		<h3><?php _e('Easily integrate Paid Memberships Pro and hundreds of other apps with Zapier.', 'pmproz'); ?></h3>
		<p>
			<?php echo __( 'Paid Memberships Pro  Zapier enables 2-way communication between Paid Memberships Pro and hundreds of other services using Zapier.
			For more information, visit ', 'pmpro-zapier' ) . '<a href="https://zapier.com" target="_blank" rel="noopener">Zapier.com</a>'; ?>
		</p>
		<?php do_settings_sections( 'pmproz_options' ); ?>
		<?php settings_fields( 'pmproz_options' ); ?>
		<?php submit_button(); ?>
	</form>
</div>