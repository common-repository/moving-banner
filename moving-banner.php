<?php
/**
 * @package MovingBanner
 */

/*
Plugin Name: Moving Banner
Description: Show your users beautifully animated banner on the site. Set your own animation speed and direction, banner height and time frame.
Plugin URI: http://moving-banner.codelabi.com/
Version: 1.0.0
Author: Rafal Stepien
Author URI: https://rafalstepien.com/
License: GPLv2 or later
Text Domain: moving-banner
*/

if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

load_theme_textdomain( 'moving-banner', get_template_directory() . '/languages' );


define("ENABLED",  1);
define("DISABLED",  0);
define('MOVING_BANNER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( MOVING_BANNER_PLUGIN_DIR . 'class.moving-banner.php' );
register_activation_hook( __FILE__, array( 'MovingBanner', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'MovingBanner', 'plugin_deactivation' ) );

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
    require_once( MOVING_BANNER_PLUGIN_DIR . 'class.moving-banner-admin.php' );
    add_action( 'init', array( MovingBanner_Admin::get_instance(), 'init' ) );
}

if (!is_admin()) {
	require_once( MOVING_BANNER_PLUGIN_DIR . 'class.moving-banner-front.php' );
	add_action( 'init', array( MovingBanner_Front::get_instance(), 'init' ) );
}