<?php
/*
Plugin Name: wwd-lateral-info
Description: Internal plugin to provide lateral information.
Version: 1.0.0
Author: WWD
Author URI: https://wwd.ca.gov/
*/

if ( !defined('WWD_LAT_INFO_DIR') ) {
    define('WWD_LAT_INFO_DIR', dirname( __FILE__) );
}

if ( !defined('WWD_LAT_INFO_BASE') ) {
    define('WWD_LAT_INFO_BASE', __FILE__ );
}


require WWD_LAT_INFO_DIR . '/options/options.php';

// Classes
require WWD_LAT_INFO_DIR . '/class/wwd_api_info.php';
require WWD_LAT_INFO_DIR . '/class/wwd_auth.php';
require WWD_LAT_INFO_DIR . '/class/wwd_page_foot.php';
require WWD_LAT_INFO_DIR . '/class/wwd_auth_msg.php';
require WWD_LAT_INFO_DIR . '/class/wwd_menu.php';

require WWD_LAT_INFO_DIR . '/includes/wwd-qvars.php';
require WWD_LAT_INFO_DIR . '/includes/wwd-laterals.php';
require WWD_LAT_INFO_DIR . '/includes/wwd-lateral.php';
require WWD_LAT_INFO_DIR . '/includes/wwd-accounts.php';
require WWD_LAT_INFO_DIR . '/includes/wwd-account.php';
require WWD_LAT_INFO_DIR . '/includes/wwd-meter.php';
