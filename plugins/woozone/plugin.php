<?php
/*
Plugin Name:	WooZone - WooCommerce Amazon Affiliates
Plugin URI: 	http://codecanyon.net/user/AA-Team/portfolio
Description: 	Choose from over a million products & earn advertising fees from the 1’st internet retailer online! You can earn up to 10% advertising fees from the 1’st trusted e-commerce leader with minimal effort. This plugin allows you to import unlimited number of products directly from Amazon right into your Wordpress WooCommerce Store! EnjoY!
Version: 		8.4.1.3
Author: 		AA-Team
Author URI:		http://codecanyon.net/user/AA-Team/portfolio
*/
! defined( 'ABSPATH' ) and exit;

// Derive the current path and load up wwcAmzAff
$plugin_path = dirname(__FILE__) . '/';
if(class_exists('wwcAmzAff') != true) {
    require_once($plugin_path . 'aa-framework/framework.class.php');

	// Initalize the your plugin
	$wwcAmzAff = new wwcAmzAff();

	// Add an activation hook
	register_activation_hook(__FILE__, array(&$wwcAmzAff, 'activate'));
}

// load textdomain
add_action( 'plugins_loaded', 'woozone_load_textdomain' );

function woozone_load_textdomain() {  
	load_plugin_textdomain( 'woozone', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}