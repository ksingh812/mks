<?php 
/**
 * @package WordPress
 * @subpackage:
 *	Name: 	Kingdom - Woocommerce Amazon Affiliate Theme
 *	Author: AA-Team
 *	Name: 	http://themeforest.net/user/AA-Team/portfolio
 *	
**/
! defined( 'ABSPATH' ) and exit;

// load the AA-Freamwork init file
$kingdom_theme_path = dirname(__FILE__) . '/';
if(class_exists('kingdom') != true) {
    require_once( $kingdom_theme_path . 'aa-framework/freamwork.class.php' );

	// Initalize the theme
	$kingdom = new kingdom(); 

	// Add an activation hook
	add_action( "after_switch_theme", array( $kingdom, 'activate' ), 10 , 2); 
}

add_filter( 'woocommerce_product_add_to_cart_text' , 'custom_woocommerce_product_add_to_cart_text' );
/**
* custom_woocommerce_template_loop_add_to_cart
*/
function custom_woocommerce_product_add_to_cart_text() {
	global $product;
	$product_type = $product->product_type;
	switch ( $product_type ) {
		case 'external':
			return __( 'Buy product', 'woocommerce' );
		break;
		case 'grouped':
			return __( 'View products', 'woocommerce' );
		break;
		case 'simple':
			return __( 'Add to cart', 'woocommerce' );
		break;
		case 'variable':
			return __( 'Select options', 'woocommerce' );
		break;
		default:
			return __( 'Read more', 'woocommerce' );
	}
} 