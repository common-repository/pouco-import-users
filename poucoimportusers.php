<?php
 /*
  *  Plugin Name:  Pouco Import Users
  *  Plugin URI:   https://wordpress.org/plugins/pouco-import-users/
  *  Description: 	Import list of users by a csv file. If users not exit, the plugin create à new user with a random password (an e-mail will be send). If users exist, the plugin update the users without changing the password.
  *  Version:      1.0.0
  *  Author:       POUCO Agency
  *  Author URI:   http://agence.pouco.ooo/
  *
  *  Text Domain:  poucoimportusers
  *  Domain Path: 	/languages
  *
  *  License:      GPLv2
  *  Copyright 2019 POUCO Agency
*/

require_once(ABSPATH . "wp-includes/pluggable.php");

if (! defined('POUCOIMPORTUSERS_VERSION')) {
    define('POUCOIMPORTUSERS_VERSION', '1.0.0');
}

if (! defined('POUCOIMPORTUSERS_MINIMUM_WP_VERSION')) {
    define('POUCOIMPORTUSERS_MINIMUM_WP_VERSION', '4.9');
}

if (! defined('POUCOIMPORTUSERS_PLUGIN_DIR')) {
    define('POUCOIMPORTUSERS_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (! defined('POUCO_UPLOADS')) {
    define('POUCO_UPLOADS', WP_CONTENT_DIR . '/uploads/');
}

if (! class_exists('POUCOImportUsers')) {
    require_once(POUCOIMPORTUSERS_PLUGIN_DIR . 'class.poucoimportusers.php');
}

$poucoimportusers = new POUCOImportUsers;

register_activation_hook(__FILE__, array( &$poucoimportusers, 'plugin_activation' ));
register_deactivation_hook(__FILE__, array( &$poucoimportusers, 'plugin_desactivation' ));
