<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $product, $woocommerce_loop;
// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) ) {
	$woocommerce_loop['loop'] = 0;
}
// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) ) {
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
}
// Ensure visibility
if ( ! $product || ! $product->is_visible() ) {
	return;
}
// Increase loop count
$woocommerce_loop['loop']++;
// Extra post classes
$classes = array();
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] ) {
	$classes[] = 'first';
}
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] ) {
	$classes[] = 'last';
}
$item_size_class = isset($woocommerce_loop['item_size_class']) ? $woocommerce_loop['item_size_class'] : 'col-lg-4 col-sm-6 col-xs-12'; 
if( isset($kingdom->coreFunctions->data['page_sidebars']) && count($kingdom->coreFunctions->data['page_sidebars']) > 0 ){
	if( !isset($woocommerce_loop['where']) || $woocommerce_loop['where'] != "releated" ){
		$item_size_class = 'col-lg-4 col-sm-6';
	}
}
?>



<div class="item <?php echo $item_size_class;?>" id="product-<?php echo $product->id;?>">
	<div class="kd_hp_item_hover"></div>
	
	<?php // do_action( 'woocommerce_before_shop_loop_item' ); ?>
	
	<div class="kd_hp_item">
		<div class="kd_hp_item_image">
			<?php if( $product->is_on_sale() ){?>
				<i class="icon icon_salelabel"></i>
	    <?php }?>
			<?php 
			$_product_rating = (int)get_post_meta( $product->id, '_product_rating', true );
			$_product_votes = (int)get_post_meta( $product->id, '_product_votes', true );
			
			if( $_product_votes == 0 ){
				$rating = 0;
			}else {
				$rating = floor($_product_rating / $_product_votes);
			}
			?>
			<input class="rating" data-max="5" value="<?php echo $rating;?>" data-productid="<?php echo $product->id; ?>" data-min="1" name="rating" type="number" />
			<a href="<?php the_permalink(); ?>"><?php echo woocommerce_template_loop_product_thumbnail();?></a>
		</div>
		<div class="kd_hp_item_title">
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<p>
				<?php echo trim(woocommerce_template_loop_price()) != '' ? woocommerce_template_loop_price() : '&nbsp;'; ?>
			</p>
		</div>
		<div class="kd_add_cart">
			<?php echo woocommerce_template_loop_add_to_cart(); ?>
		</div>
	</div>
	
</div>