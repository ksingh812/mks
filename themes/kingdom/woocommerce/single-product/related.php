<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

$posts_per_page = 6; 
$related = $product->get_related( $posts_per_page );

if ( sizeof( $related ) == 0 ) return;

$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'            => 'product',
	'ignore_sticky_posts'  => 1,
	'no_found_rows'        => 1,
	'posts_per_page'       => $posts_per_page,
	'orderby'              => $orderby,
	'post__in'             => $related,
	'post__not_in'         => array( $product->id )
) );

$products = new WP_Query( $args );

$woocommerce_loop['columns'] = $columns;

$woocommerce_loop['item_size_class'] = 'col-lg-12 col-sm-12';
$woocommerce_loop['where'] = 'releated';

if ( $products->have_posts() ) : ?>

	<!--You might also like -->
	<div class="row">
		<div class="col-lg-12">
			<div class="kd_mightalso_like">
				<h1><?php _e( 'Related Products', 'woocommerce' ); ?></h1>
			</div>
			
			<div id="kd_related_products">
				<?php woocommerce_product_loop_start(); ?>
		
					<?php while ( $products->have_posts() ) : $products->the_post(); ?>
		
						<?php wc_get_template_part( 'content', 'product' ); ?>
		
					<?php endwhile; // end of the loop. ?>
		
				<?php woocommerce_product_loop_end(); ?>
		
			</div>
		</div>
	</div>

<?php endif;

wp_reset_postdata();
