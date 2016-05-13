<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;
?>

<?php do_action( 'woocommerce_before_mini_cart' ); ?>

<div class="cart-details">
	<i class="icon icon_arrow-cart"></i>

	<ul class="kd_small_cart_items">
		<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

			<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
	
					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
	
						$product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
						$thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
						$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						?>
						<li data-prodid="<?php echo $cart_item_key;?>">
							<div class="kd_cart_item-image">
								<a href="<?php echo get_permalink( $product_id ); ?>">
									<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . $product_name; ?>
								</a>
							</div>
							
							<div class="kd_cart_item-details">
								<h2><a href="<?php echo get_permalink( $product_id ); ?>"><?php echo $product_name;?></a></h2>
								<p>
									<span class="kd_qty"><?php echo sprintf( __( 'Quantity: %s', 'woocommerce' ), $cart_item['quantity']) ;?></span>
								</p>
								<p>
									<?php _e( 'Price', 'woocommerce' ); ?>: <span class="kd_price"><?php echo $product_price;?></span>
								</p>
								<a href="<?php echo WC()->cart->get_remove_url( $cart_item_key );?>" class="kd_cart_item-close-btn" data-toggle="tooltip" title="<?php _e( 'Delete', 'woocommerce' ); ?>"><i class="icon icon_delete"></i></a>
							</div>
						</li>
						<?php
					}
				}
			?>
	
		<?php else : ?>
	
			<li class="empty"><?php _e( 'No products in the cart.', 'woocommerce' ); ?></li>
	
		<?php endif; ?>
		
	</ul>
	
	<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>
		<div class="kd_cart_total">
			<?php _e( 'Subtotal', 'woocommerce' ); ?>: <span><?php echo WC()->cart->get_cart_subtotal(); ?></span>
		</div>
		<div class="kd_add_to_cart">
			<a class="btn btn_checkout" href="<?php echo WC()->cart->get_checkout_url(); ?>" role="button"><?php _e( 'Checkout', 'woocommerce' ); ?></a>
			<a class="btn btn_viewcart" href="<?php echo WC()->cart->get_cart_url(); ?>" role="button"><?php _e( 'View Cart', 'woocommerce' ); ?></a>
		</div>
	<?php endif; ?>
</div>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>