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
    <!-- Main Gray Content Background -->
	<div class="gray-content-background">
		<div class="container">
			<div class="row-fluid">
				
				<?php
					$kingdom->coreFunctions->printSidebar( 'left' );
				?>
				<!-- Main Container -->
				<section class="<?php echo (isset($kingdom->coreFunctions->data['sidebar']['position']) && $kingdom->coreFunctions->data['sidebar']['position'] == 'nosidebar' ? 'span16' : 'span12'  );?>">
			        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				        <article id="post_<?php echo $post->ID; ?>" <?php post_class(); ?>>
				        	<div class="the-content" <?php post_class(); ?>>
					            <?php if(isset($data['single_image']) && $data['single_image'] == "1"): $blog_thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumb-post-featured'); ?>
						            <?php if(has_post_thumbnail()): ?>
						            <div class="row-fluid">
							          	<div class="span16">
							          		<a class="s_thumb" href="<?php echo $preview[0]; ?>" rel="prettyPhoto">
								            	<img src="<?php echo $thumb[0]; ?>" alt="<?php echo the_title(); ?>" />
								            </a>
							          	</div>
							        </div>
						            <?php endif; ?>
					            <?php endif; ?>
					            
					            <?php the_content(); ?>
					        </div>
				        </article>
	    
	    			<?php endwhile; endif; ?> 
	   				<?php //comments_template(); ?>
  				</section>
  				
  				<?php
					$kingdom->coreFunctions->printSidebar( 'right' );
				?>
  			</div>
  		</div>
  	</div>
    <!-- end of content -->

<?php get_footer(); ?>
