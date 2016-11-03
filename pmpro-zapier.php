<?php

/**
 * Plugin Name: Paid Memberships Pro Zapier
 * Description: Easily integrate Paid Memberships Pro and hundreds of other apps with Zapier.
 * Author: Stranger Studios
 * Author URI: http://strangerstudios.com
 * Version: .1
 */

// Includes.
require_once( dirname(__FILE__) . '/includes/class-pmpro-zapier.php');
require_once( dirname(__FILE__) . '/admin/init.php');

$pmproz = new PMPro_Zapier();