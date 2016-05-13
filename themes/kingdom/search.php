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

global $wp_query; 
?>

<!-- Main Gray Content Background -->
<div class="gray-content-background">
	<div class="container">
		<div class="row-fluid">
		
			<?php 
				if( isset($kingdom->coreFunctions->data['sidebar']['position']) && $kingdom->coreFunctions->data['sidebar']['position'] == 'left' ){
						require_once('sidebar.php');
				}
			?>
			
			<!-- Main Container -->
			<section class="<?php echo (isset($kingdom->coreFunctions->data['sidebar']['position']) && $kingdom->coreFunctions->data['sidebar']['position'] == 'nosidebar' ? 'span16' : 'span12'  );?>">
				<div class="main-content-box">
					<div class="extra-container-box">
						<div class="row-fluid">
							<div class="span16">
								<h1><?php _e('Search results:', 'kingdom'); ?></h1>
								<?php if( have_posts() ){ ?>
								<ul class="search-results">
									<?php 
									$last_post_type = '';
									while ( have_posts() ) : the_post(); 
										if( in_array( $post->post_type, array('testimonials', 'slideshow', 'partners')) ) continue;
									
										if( $post->post_type != $last_post_type ){
											$post_type_obj = get_post_type_object( $post->post_type ); 
									?>
											<h2><?php echo $post_type_obj->labels->name;?> <?php _e('results:', 'kingdom'); ?></h2>
									<?php		
										}
									?>
										<li class="post_type-<?php echo $post->post_type;?>">
												<div class="row-fluid">
													
													<?php 
													if(has_post_thumbnail()){
														$thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail'); 
													?>
														<div class="span2">
															<a href="<?php the_permalink(); ?>"><img src="<?php echo $thumb[0]; ?>" alt="<?php echo the_title(); ?>"></a>
														</div>
													<?php } ?>
													<div class="span<?php echo (has_post_thumbnail() ? '14' : '16');?>">
														<h4 class="test-overflow"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
														<p><?php echo $kingdom->string_limit_caracters( $post->post_content, 120); ?></p>
													</div>	
												</div>
										</li>
									<?php 
										$last_post_type = $post->post_type;
									endwhile; ?>
								</ul>
								<?php }else{
									?>
									<p><?php _e('Sorry, but nothing matched your search terms. Please try again with different keywords.', 'kingdom'); ?></p>
								<?php
								} ?>
							</div>
						</div>
					</div>
			</section>
					
				<?php 
				if( isset($kingdom->coreFunctions->data['sidebar']['position']) && $kingdom->coreFunctions->data['sidebar']['position'] == 'right' ){
						require_once('sidebar.php');
				}
				?>
				</div>
		</div>
	</div>
</div>
<!-- end of content -->

<?php get_footer(); ?>
