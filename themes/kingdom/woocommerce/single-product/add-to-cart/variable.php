<?php
/**
 * Variable product add to cart
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$attribute_keys = array_keys( $attributes );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo $post->ID; ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>">
	<?php if ( ! empty( $available_variations ) ) : ?>
		<div class="variations">
			<?php $loop = 0; foreach ( $attributes as $attribute_name => $options ) : $loop++;?>
					<div class="kd_pick_up_color kd_size">
					<p><?php echo wc_attribute_label( $attribute_name ); ?></p>
					<div class="kd_dropdown_pick_up_color kd_choosesize btn-group kd_custom_select">
						<button data-toggle="dropdown" class="btn dropdown-toggle" type="button">
						    <span class="current_value"></span><span class="caret"></span>
						</button>
						<ul role="menu" class="dropdown-menu"></ul>
						<?php
						/*
						<select id="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>" name="attribute_<?php echo sanitize_title( $attribute_name ); ?>">
							<option value="" selected><?php echo __( 'Choose an option', 'woocommerce' ) ?>&hellip;</option>
							<?php
								$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $product->get_variation_default_attribute( $attribute_name );
								wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
								echo end( $attribute_keys ) === $attribute_name ? '<a class="reset_variations" href="#">' . __( 'Clear selection', 'woocommerce' ) . '</a>' : '';							?>
						</select>
						*/
						?>
						<?php
							$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $product->get_variation_default_attribute( $attribute_name );
							wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
						?>
					</div>
					</div>
				<?php
				if ( sizeof( $attributes ) === $loop )
					echo '<div class="kd_variations"><a class="reset_variations" href="#reset">' . __( 'Clear selection', 'woocommerce' ) . '</a></div>';
				?>
	        <?php endforeach;?>
		</div>
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<div class="single_variation_wrap" style="display:none;">
			<?php do_action( 'woocommerce_before_single_variation' ); ?>

			<div class="single_variation"></div>

			<div class="variations_button">
				<?php woocommerce_quantity_input(); ?>
				<button type="submit" class="btn_addcart alt"><?php echo $product->single_add_to_cart_text(); ?></button>
			</div>

			<input type="hidden" name="add-to-cart" value="<?php echo $product->id; ?>" />
			<input type="hidden" name="product_id" value="<?php echo esc_attr( $post->ID ); ?>" />
			<input type="hidden" name="variation_id" value="" />

			<?php do_action( 'woocommerce_after_single_variation' ); ?>
		</div>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<?php else : ?>

		<p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>

	<?php endif; ?>

</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
