<?php
/**
 * Plugin Name: wwd-lateral-info
 * Version: 1.3.2
 * Plugin URI: https://jcarter62.wordpress.com/wwd-lateral-info/
 * Description: Internal plugin for WWD to provide lateral information.
 * Author: WWD
 * Author URI: https://wwd.ca.gov
 * Text Domain: wwd-lateral-info
 */
/**
 * @Author WWD
 * @Copyright (c) 2018. Westlands Water District. (https://wwd.ca.gov)
 * This code is released under the GPL licence version 3 or later, available here
 * http://www.gnu.org/licenses/gpl.txt
 *
 */

if (!defined('WWD_LAT_INFO_DIR')) {
    define('WWD_LAT_INFO_DIR', dirname(__FILE__));
}

if (!defined('WWD_LAT_INFO_BASE')) {
    define('WWD_LAT_INFO_BASE', __FILE__);
}

require WWD_LAT_INFO_DIR . '/options/options.php';

// Classes
require_once WWD_LAT_INFO_DIR . '/class/wwd_api_info.php';
require_once WWD_LAT_INFO_DIR . '/class/wwd_auth.php';
require_once WWD_LAT_INFO_DIR . '/class/wwd_page_foot.php';
require_once WWD_LAT_INFO_DIR . '/class/wwd_auth_msg.php';
require_once WWD_LAT_INFO_DIR . '/class/wwd_menu.php';
require_once WWD_LAT_INFO_DIR . '/class/wwd_oddrow.php';
require_once WWD_LAT_INFO_DIR . '/class/wwd_db.php';

require WWD_LAT_INFO_DIR . '/includes/wwd-qvars.php';
require WWD_LAT_INFO_DIR . '/includes/wwd-laterals.php';
require WWD_LAT_INFO_DIR . '/includes/wwd-lateral.php';
require WWD_LAT_INFO_DIR . '/includes/wwd-accounts.php';
require WWD_LAT_INFO_DIR . '/includes/wwd-account.php';
require WWD_LAT_INFO_DIR . '/includes/wwd-meter.php';
require WWD_LAT_INFO_DIR . '/includes/wwd-meters.php';
