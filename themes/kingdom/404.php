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
 
$page_size = 'col-lg-9 col-sm-8 col-xs-12';

$page_id     = get_queried_object_id();
$sidebar_position = get_post_meta( $page_id, '_page_sidebar_position', true );
if( $sidebar_position != false && $sidebar_position == 'nosidebar' ){
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
				<section class="<?php echo (isset($kingdom->coreFunctions->data['sidebar']['position']) && $kingdom->coreFunctions->data['sidebar']['position'] == 'nosidebar' ? 'span16' : 'span12'  );?>">
					<div class="main-content-box post-entry">
						<div class="extra-container-box">	
							<article  <?php post_class(); ?>>
								<div class="kd_errorpage">
									<h2><span>404</span><br/> <?php _e('Page', 'kingdom' );?></h2>
									<hr/>
									<p><?php _e("Sorry, but you are looking for something that isn't here.", 'kingdom' );?></p>
									<form role="search" action="<?php echo esc_url( home_url() ); ?>" method="get">
										<div class="input-group">
											<input name="s" type="text" class="input_comment" placeholder="Type and search"> 
											<input type="submit" class="kd_loupe" />
										</div>
									</form>
								</div>
							</article>
						</div>
					</div>
				</section>
			</div>
			<?php
				$kingdom->coreFunctions->printSidebar( 'right' );
			?>
		</div>
	</div>
    <!-- end of content -->

<?php get_footer(); ?>
