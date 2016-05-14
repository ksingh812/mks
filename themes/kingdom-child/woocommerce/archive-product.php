<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */
global $woocommerce, $kingdom, $wp_query;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
get_header( 'shop' ); ?>
	<div class="container kd_product_list_grid" id="kd_products_listitems">
		<div class="row">	
			<?php
				$kingdom->coreFunctions->printSidebar( 'left' );
			?>
			
			<div class="col-md-12" id="products">
				
				<div class="row">
					<div class="kd_cat-description col-lg-12">
						<?php do_action( 'woocommerce_archive_description' ); ?>
					</div>
					
						
					<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
						<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>
	          <?php
							woocommerce_get_template( 'loop/result-count.php' );
						?>
					</div>
					
					<?php 
					if( $wp_query->found_posts > 1 && woocommerce_products_will_display() ):
					?>
						<!-- Sorting -->
						<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
							<?php
							$orderby = isset( $_GET['orderby'] ) ? woocommerce_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
							?>
							<div class="kd_dropdown btn-group kd_custom_select kd_loop_orderby">
								<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">
									<span class="current_value"></span><span class="caret"></span>
								</button>
								<ul class="dropdown-menu" role="menu"></ul>
								<?php
								woocommerce_get_template( 'loop/orderby.php', array( 'orderby' => $orderby ) ); 
								?>
							</div>
						</div>
					<?php
					endif;
					?>
				</div>
				
				<hr class="kd_line"/>

				<?php
				/**
				 * woocommerce_before_main_content hook
				 *
				 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
				 * @hooked woocommerce_breadcrumb - 20
				 */
				do_action( 'woocommerce_before_main_content' );
				?>
				
				<div class="row">
					<?php if ( have_posts() ) : ?>
			
						<?php
							/**
							 * woocommerce_before_shop_loop hook
							 *
							 * @hooked woocommerce_result_count - 20
							 * @hooked woocommerce_catalog_ordering - 30
							 */
							//do_action( 'woocommerce_before_shop_loop' );
						?>
						
						
			
						<?php woocommerce_product_loop_start(); ?>
			
							<?php woocommerce_product_subcategories(); ?>
							<div class="clear"></div>
							<?php while ( have_posts() ) : the_post(); ?>
			
								<?php wc_get_template_part( 'content', 'product' ); ?>
			
							<?php endwhile; // end of the loop. ?>
			
						<?php woocommerce_product_loop_end(); ?>
			
						<?php
							/**
							 * woocommerce_after_shop_loop hook
							 *
							 * @hooked woocommerce_pagination - 10
							 */
							do_action( 'woocommerce_after_shop_loop' );
						?>
			
					<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>
			
						<?php wc_get_template( 'loop/no-products-found.php' ); ?>
			
					<?php endif; ?>
					
				</div>
				<?php
					/**
					 * woocommerce_after_main_content hook
					 *
					 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
					 */
					do_action( 'woocommerce_after_main_content' );
				?>
			</div>
			
			<?php
				//$kingdom->coreFunctions->printSidebar( 'right' );
			?>
		</div>
	</div>
<?php get_footer( 'shop' ); ?>