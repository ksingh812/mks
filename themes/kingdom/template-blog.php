<?php  
/**
 * @package WordPress
 * @subpackage:
 *	Name: 	Kingdom Amazon Affiliate Theme
 *	Alias: 	kingdom
 *	Author: AA-Team
 *	Name: 	http://themeforest.net/user/AA-Team/portfolio
 *  Template Name: Template Blog
 *	
**/
get_header();
global $tag, $cat; 
?>
	<div class="container">
		<div class="row">
			<?php
				$kingdom->coreFunctions->printSidebar( 'left' );
			?>
				
			<!-- Main Container -->
			<section class="<?php echo $kingdom->coreFunctions->content_class();?>">
				<?php
				$args = array(
					'post_type' => 'post',
					'paged' => $paged,
				);
				
				if( isset($tag) && trim($tag) != "" ){
					$args['tag'] = $tag;
				}
				if( isset($cat) && trim($cat) != "" ){
					$args['cat'] = $cat;
				}
				query_posts($args);
				?>
				<?php if ( have_posts() ) : ?>
				
				<div class="blog-box">
					<?php while( have_posts() ) : the_post(); ?>
					
					<div class="kd_simple_post">
						
						<?php
						if( has_post_thumbnail() ) {
						?>
							<div class="kd_simple_post_image kd_hovereffect">
								<a href="<?php the_permalink(); ?>">
									<?php
										echo get_the_post_thumbnail( $post->ID, 'blog-featured-image' );
									?>
									<div class="mask">
									 	<div class="kd_bk_icon">
											<i class="icon icon_hover"></i>
										</div>
									</div>
								</a>
							</div>
						<?php } ?>
						
						<div class="kd_simple_post_description <?php echo has_post_thumbnail() == false ? 'full' : '';?> ">
							<h2><a href="<?php the_permalink(); ?>"><?php  the_title(); ?></a></h2>
							<input class="rating" data-max="5" data-min="1" name="rating" type="number" value="4" />
							<p><span><?php echo get_the_date(); ?> / <?php comments_number( 'no comments', 'one comment', '% comments' ); ?>, <?php _e('on', 'kingdom'); ?> <?php the_category(', ', 'multiple'); ?></span></p>
							<div class="kd_blog_short_desc"><?php the_excerpt(); ?></div>
							<a href="<?php the_permalink(); ?>" class="kd_read_more"> <?php _e('Read More', 'kingdom'); ?> <sup>+</sup> </a>
						</div>
					</div>
					
					<div class="clearfix"></div>
					<hr class="kd_line"/>
					<?php endwhile; ?>
				</div>
			
				<div class="clearfix"></div>

				<?php if( $wp_query->post_count < $wp_query->found_posts ) { ?>
				<div class="row-fluid">
					<!-- Pagination -->
					<div class="pagination-container">
						<div class="pagination-left">
							<?php previous_posts_link( __( '<i class="first-arrow"></i>Newer', 'kingdom' ) ); ?>
						</div>
						<div class="pagination-right">
							<?php next_posts_link( __( '<i class="last-arrow"></i>Older', 'kingdom' ) ); ?>
						</div>
					</div>
				</div>
				<?php } ?>
				
				<?php wp_reset_query(); ?>
				<?php else : ?>
					<?php get_template_part( 'template', 'none' ); ?>
				<?php endif; ?>
			</section>
			
			<?php
				$kingdom->coreFunctions->printSidebar( 'right' );
			?>
		</div>
	</div>

<?php get_footer(); ?>