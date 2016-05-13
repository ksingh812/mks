<?php
/**
 * Description tab
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce, $post, $kingdom;
if( isset($kingdom->coreFunctions->settings['layout']["enable_product_description_tab"]) && $kingdom->coreFunctions->settings['layout']["enable_product_description_tab"] == 'true' ){
	$heading = esc_html( apply_filters( 'woocommerce_product_description_heading', __( 'Product Description', 'woocommerce' ) ) );
?>
	<?php the_content(); ?>
<?php } ?>