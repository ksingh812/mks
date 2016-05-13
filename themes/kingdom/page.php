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

$page_size = 'col-lg-9 col-md-8 col-sm-8 col-xs-12';

if( isset($kingdom->coreFunctions->data['page_sidebars']) == false || count($kingdom->coreFunctions->data['page_sidebars']) == 0 ){
	$page_size = 'col-lg-12 col-sm-12 col-xs-12';
}
?>
	<div class="container <?php post_class();?>">
		<div class="row">
			<?php
				$kingdom->coreFunctions->printSidebar( 'left' );
			?>
			<div class="<?php echo $page_size;?>">
				<!-- Main Content Section -->
				<div class="main-content-box post-entry">
					<div class="extra-container-box">	
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
								
								<?php 
								if( isset($kingdom->coreFunctions->data['layout']['print_page_title']) && $kingdom->coreFunctions->data['layout']['print_page_title'] == 'no' ){}else{
								?>
									<h1><?php the_title(); ?></h1>
								<?php }?>
								<?php the_content(); ?>
							</div>
						</article>
		
					<?php endwhile; endif; ?> 
					<?php //comments_template(); ?>
					</div>
				</div>
			</div>
			<?php
				$kingdom->coreFunctions->printSidebar( 'right' );
			?>
		</div>
	</div>
    <!-- end of content -->

<?php get_footer(); ?>
