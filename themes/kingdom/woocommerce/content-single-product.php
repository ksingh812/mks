<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
global $kingdom, $product;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php
	/**
	 * woocommerce_before_single_product hook
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class( array('kingdom-prod-details', 'col-lg-12') ); ?>>
	<meta itemprop="url" content="<?php the_permalink(); ?>" />
	<div class="row">
		<div class="col-lg-5">
			<?php
				/**
				 * woocommerce_before_single_product_summary hook
				 *
				 * @hooked woocommerce_show_product_sale_flash - 10
				 * @hooked woocommerce_show_product_images - 20
				 */
				woocommerce_show_product_images();
			?>
		</div>
		
		<div class="col-lg-7">
	
			<?php
				/**
				 * woocommerce_single_product_summary hook
				 *
				 * @hooked woocommerce_template_single_title - 5
				 * @hooked woocommerce_template_single_rating - 10
				 * @hooked woocommerce_template_single_price - 10
				 * @hooked woocommerce_template_single_excerpt - 20
				 * @hooked woocommerce_template_single_add_to_cart - 30
				 * @hooked woocommerce_template_single_meta - 40
				 * @hooked woocommerce_template_single_sharing - 50
				 */
				//do_action( 'woocommerce_single_product_summary' );
			?>
		    <div class="kd_product_rating_pagination">
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
		        <ul class="kd_pager">
		            <li><?php previous_post_link('%link', __('Previous', 'kingdom')); ?></li>
					<li><?php next_post_link('%link', __(' / Next', 'kingdom')); ?></li>
		        </ul>
		    </div>
		
		    <div class="kd_description">
		        <?php woocommerce_template_single_title();?>
		        <h2><?php woocommerce_template_single_price();?></h2>
		        <div id="kd_general_description">
                    <?php
                        $gdt = isset($kingdom->coreFunctions->settings['layout']['product_general_description_type'])
                            ? $kingdom->coreFunctions->settings['layout']['product_general_description_type']
                            : 'first_paragraph';
                        if ( 'first_paragraph' == $gdt ) {
                            echo $kingdom->coreFunctions->get_first_paragraph();
                        } else {
                            woocommerce_template_single_excerpt();
                        }
                    ?>
		        </div>
		        <?php if( isset($kingdom->coreFunctions->settings['layout']["enable_product_in_category"]) && $kingdom->coreFunctions->settings['layout']["enable_product_in_category"] == 'true' ) { ?>
					<h3 class="kd_post_in_cat"><?php echo $product->get_categories( ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', $cat_count, 'kingdom' ) . ' ', '.</span>' ); ?></h3>
				<?php } ?>
				<?php if( isset($kingdom->coreFunctions->settings['layout']["enable_product_tags"]) && $kingdom->coreFunctions->settings['layout']["enable_product_tags"] == 'true' ) { ?>
					<h3 class="kd_post_in_tags"><?php echo $product->get_tags( ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', $tag_count, 'woocommerce' ) . ' ', '.</span>' ); ?></h3>
				<?php } ?>
				<?php
					woocommerce_template_single_add_to_cart(); 
				?>
		
		        <div class="kd_social_share">
		            <?php 
				 		$kingdom->coreFunctions->print_share_buttons( $post->ID, $post->post_title );
				 	?>
		        </div>
		    </div>
		</div>
	</div>
	
	<?php
		/**
		 * woocommerce_after_single_product_summary hook
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( 'woocommerce_after_single_product_summary' );
	?>

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>
