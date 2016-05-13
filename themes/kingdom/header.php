<?php 
// retrive the main class as global
global $kingdom; 
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
 	<title>
    <?php
	    if (!defined('WPSEO_VERSION')) {
	   		echo bloginfo( 'name') . wp_title( '|', true, '');
	    }
	    else {
	        wp_title();
	    }
	?>
    </title>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
    <link rel="icon" href="<?php $favicon = wp_get_attachment_image_src( $kingdom->coreFunctions->settings['layout']['favicon'] ); echo $favicon[0]; ?>" type="image/x-icon"> 
	<?php wp_head(); ?>
	
</head>
<body <?php body_class(); ?>>
	<?php 
		//var_dump('<pre>',$kingdom->coreFunctions->settings['layout'],'</pre>');  
	?>
	<div class="kd_header_top">
		<div class="container ">
			<div class="col-lg-6 col-md-5 col-sm-4 col-xs-12">
				<?php
				if(has_nav_menu( 'top_nav' )){
				?>
					<!-- Main Menu -->
					<nav class="kd_header_top_menu">
						<?php
						wp_nav_menu(array(
							'theme_location' => 'top_nav',
					        'menu_id' => 'top_nav'
					    ));
						?>
					</nav>
				<?php
				}
				?>
			</div>
			<div class="col-lg-6 col-md-7 col-sm-8 col-xs-12">
				<?php
				if( isset($kingdom->coreFunctions->settings['layout']['phone_support']) && trim($kingdom->coreFunctions->settings['layout']['phone_support']) ){
				?>
					<div class="kd_phone_support"><?php echo $kingdom->coreFunctions->settings['layout']['phone_support'];?></div>
				<?php
				} 
				?>
				
				<ul class="kd_header_social_list">
					<?php
					$socials = array('facebook', 'linkedin', 'youtube', 'twitter', 'google', 'pinterest');
					foreach ($socials as $network) {
					    $net_url = isset($kingdom->coreFunctions->settings['layout'][ $network . '_url' ])
                            ? trim($kingdom->coreFunctions->settings['layout'][ $network . '_url' ]) : '';
						if( !empty($net_url) && $net_url != '#' ){
							echo '<li><a href="' . ( $net_url ) . '"><i class="fa kd_social_icons fa-' . ( $network ) . '"></i></a></li>';
						}
					}
					?>
				</ul>
			</div>
		</div>
	</div>
	
	<!-- Header Elements -->
	<div class="container ">
		<div class="kd_header_elements">
			<div class="row">
				<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
					<div class="kd_logo">
						<a href="<?php echo home_url();?>">
							<?php 
							if( !isset($kingdom->coreFunctions->settings['layout']['logo']) || empty($kingdom->coreFunctions->settings['layout']['logo']) ){
								echo '<img src="' . ( $kingdom->cfg['paths']['theme_dir_url'] ) . '/images/kd_logo.png">';
							}else{
								echo wp_get_attachment_image( $kingdom->coreFunctions->settings['layout']['logo'], 'full' );
							}
							?>
						</a>
					</div>
				</div>
				<div class="col-lg-8 col-md-6 col-sm-6 col-xs-12">
					<div class="kd_search">
						<form role="search" action="<?php echo esc_url( home_url() ); ?>" method="get">
							<input type="hidden" name="post_type" value="product" />
							<input type="submit" class="kd_search_submit" value="<?php _e( 'Search', 'kingdom');?>" />
							<input type="text" id="search" name="s" placeholder="<?php _e( 'Search for products', 'kingdom'); ?>" />
						</form>
					</div>
					<?php
					if( $kingdom->is_woo_activated() ) {
					?>
					<div id="kd_checkout_wrap">
						<a href="<?php echo WC()->cart->get_checkout_url(); ?>" class="kd_checkout_button"><?php _e( 'Checkout', 'kingdom'); ?></a>
						<div class="kd_small-cart">
							<div class="kd_cart-title">
									<i class="icon icon-cart"></i>
									<?php global $woocommerce; ?>
									<a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'kingdom'); ?>"><?php _e( 'Basket', 'kingdom' ); ?>: <span><?php echo $woocommerce->cart->get_cart_total(); ?></span></a>
							</div>
							<span class="kd_cart-itemsnumber">(<?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'kingdom'), $woocommerce->cart->cart_contents_count);?> )</span>
							
							<div class="cart-details-wrapper">
								<?php
								woocommerce_get_template( 'cart/mini-cart.php' ); 
								?>
							</div>
						</div>
					</div>
					<?php
					}
					?>
				</div>
			</div>
		</div>
	</div><!-- END HEADER ELEMENTS -->
	
	<?php
	if(has_nav_menu( 'main_nav' )){
	?>
		<!-- Main Menu -->
		<nav class="navigationbar" role="navigation">
			<div class="container">
				<a class="kd-mobilemenunav" href="#mobilenav">
					<span></span>
					<span></span>
					<span></span>
				</a>
				<span id="kd-mobilemenu-title"><?php _e('Main Menu', 'kingdom'); ?></span>
				<?php
				wp_nav_menu(array(
			        'theme_location' => 'main_nav',
			        'menu_class' => 'kd_main_menu' 
			    ));
				?>
			</div>
		</nav>
	<?php
	}
	?>
	
	<?php
	$kd_page_layout = $kingdom->coreFunctions->data['layout'];
	 
	if( isset( $kd_page_layout["home_slider"] ) && count( $kd_page_layout["home_slider"] ) > 0 && $kd_page_layout["home_slider"] != "" && $kd_page_layout["home_slider"] == "kingdom-slider" ){
		
		if( isset($kd_page_layout['full_page_slideshow']) && (int)$kd_page_layout['full_page_slideshow'] > 0 ){
			$slideshow_data = get_post_meta( $kd_page_layout['full_page_slideshow'], '_slideshow_data', true );
			if( $slideshow_data && count($slideshow_data) > 0 && count($slideshow_data["slideshow_images"]) > 0 ){
		?>
				<!-- Fullpage carousel -->
				<div id="kd-slider" class="owl-carousel owl-theme">
					<?php
					foreach ($slideshow_data["slideshow_images"] as $slide) { 
					?>
						<div class="item">
							<img src="<?php echo $slide['kingdom-gallery-images'];?>" alt="<?php echo $slide['slide_title'];?>" class="center-block">
							<?php if( $slide['enable_textbox'] == 'yes' ) { ?>
							<div class="container">
								<div class="col-lg-5"></div>
								<div class="col-lg-7">
									<div class="kd_slider_content">
										<h2><?php echo $slide['slide_title'];?></h2>
										<h3><?php echo $slide['slide_subtitle'];?></h3>
										<h4><?php echo $slide['slide_sub_subtitle'];?></h4>
										<?php if( $slide['enable_button'] == 'yes' ) { ?>
											<a href="<?php echo $slide['slide_link'];?>" class="btn">
												<?php echo $kingdom->coreFunctions->print_slideshow_button( $slide['slide_button_type']);?>
											</a>
										<?php } ?>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>
					<?php	
					} 
					?>
				</div><!-- END Fullpage carousel -->
		<?php
			} 
		} 
	} elseif( is_search() == false && isset( $kd_page_layout["home_slider"] ) && count( $kd_page_layout["home_slider"] ) > 0 && $kd_page_layout["home_slider"] != "" && $kd_page_layout["home_slider"] == "revolution-slider" ) {
		$revslider_select = $kd_page_layout['revolution_slider_select']; 
	?>
		<?php echo do_shortcode('[rev_slider alias="' . $revslider_select . '"]'); ?>
	<?php } ?>
	
	<!-- Breadcrumbs -->
	<?php 
	if( !is_front_page() ){
	?>
		<div class="kd_breadcrumbs_bk">
			<div class="container">
				<div class="row">
					<?php echo $kingdom->coreFunctions->display_breadcrumbs();?>
				</div>
			</div>
		</div>
	<?php
	}
	?>