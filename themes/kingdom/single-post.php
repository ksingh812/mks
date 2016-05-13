<?php 
/**
 * @package WordPress
 * @subpackage:
 *	Name: 	Kingdom Amazon Affiliate Theme
 *	Alias: 	kingdom
 *	Author: AA-Team
 *	Name: 	http://themeforest.net/user/AA-Team/portfolio
 *	
**/

get_header();
?>
   	<div class="container">
		<div class="row">
			<?php
				$kingdom->coreFunctions->printSidebar( 'left' ); 
			?>
			<!-- Main Container -->
			<section id="post-<?php the_ID(); ?>" class="<?php echo $kingdom->coreFunctions->content_class();?>">
				<?php if( have_posts() ) : while( have_posts() ) : the_post();?>
				<div class="kd_featured_post kd_post_details">
					
					<?php if( has_post_thumbnail() ) :
						$thumb_id = get_post_thumbnail_id( $post->ID ); 
						$image = wp_get_attachment_image_src( $thumb_id, 'full' );
					?>
					
					<div class="kd_hovereffect">
						<a href="<?php echo $image[0];?>" class="prettyPhoto" title="Image" >
							<?php echo wp_get_attachment_image( $thumb_id, 'blog-featured-image' );?>
							<div class="mask">
						 		<div class="kd_bk_icon">
									<i class="icon icon_hover"></i>
								</div>
							</div>
						</a>
					</div>
					
					<?php endif; ?>
					
					<div class="kd_product_rating_pagination">
						<input class="rating" data-max="5" data-min="1" name="rating" type="number" value="5" />
						<ul class="kd_pager">
							<li><?php previous_post_link('%link', 'Previous'); ?></li>
							<li><?php next_post_link('%link', ' / Next'); ?></li>
						</ul>
					</div>
					
					<div class="clearfix"></div>
					
					<h2><?php the_title(); ?></h2>
					<p>
						<span><?php echo get_the_date(); ?> / <?php comments_number( 'no comments', 'one comment', '% comments' ); ?>, <?php _e('on', 'kingdom'); ?> <?php the_category(', ', 'multiple'); ?></span>
					</p>
					
					<div class="post-entry">
						<?php the_content(); ?>
					</div>
					<?php wp_link_pages(); ?>
					
					<div class="kd_social_tags">
						 <div class="kd_social_share">
						 	<?php 
						 		$kingdom->coreFunctions->print_share_buttons( $post->ID, $post->post_title );
						 	?>
						</div>

						<div class="kd_tags">
							<?php the_tags(); ?>
						</div>
					</div>
				</div>
				
				<?php comments_template(); ?>

				<?php endwhile; endif; ?>
				
			</section>
			
			<?php
				$kingdom->coreFunctions->printSidebar( 'right' );
			?>
  		</div>
  	</div>
    <!-- end of content -->

<?php get_footer(); ?>
