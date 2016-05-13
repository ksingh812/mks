<!-- Footer -->
<?php
global $kingdom;
$partners_module_active = $kingdom->coreFunctions->the_theme->cfg["modules"]["partners"]["status"]; 
?>	
	<?php 
	if( $partners_module_active != false ){ 
		$args = array(
			'post_type' => 'partners',
			'posts_per_page' => '-1'
		);
		$the_partners = new WP_Query( $args );
		
		if( $the_partners->have_posts() ){
		?>
			<!-- Partners Carousel -->
			<div id="kd_footer_partners">
				<?php 
				while ( $the_partners->have_posts() ) : $the_partners->the_post();
	
				$partner_url = get_post_meta( $post->ID, '_partner_url', true);
				$partner_image = get_post_meta( $post->ID, '_partner_image', true);
				?>
				<div class="item">
					<a href="<?php echo $partner_url;?>"><?php echo wp_get_attachment_image( $partner_image, 'full' );?></a>
				</div>
				<?php
				endwhile;
				?>
			</div>
		<?php 
		}
	}
		?>
	
	<!-- Footer Menu -->
	<div class="container">
		
		
		<div class="row kd_footercolumns">
			<?php 
			if ( is_active_sidebar( 'footercontent' ) ) {
				dynamic_sidebar( 'footercontent' );
			}
			?>
		</div><!-- END row -->
	</div><!-- END Container -->

	<div class="kd_footer_smallmenu">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<?php
					if( isset($kingdom->coreFunctions->settings['layout']['footer_copyright']) && trim($kingdom->coreFunctions->settings['layout']['footer_copyright']) ){
					?>
						<p><?php echo stripslashes( $kingdom->coreFunctions->settings['layout']['footer_copyright'] ); ?></p>
					<?php
					} 
					?>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<div class="kd_credit_cards">
						<?php
						$cards = array('amazon', 'discover', 'money', 'visa', 'paypal', 'mastercard');
						foreach ($cards as $card) {
							if( isset($kingdom->coreFunctions->settings['layout'][ $card . '_url' ]) && trim($kingdom->coreFunctions->settings['layout'][ $card . '_url' ]) ){
								echo '<a href="' . ( $kingdom->coreFunctions->settings['layout'][ $card . '_url' ] ) . '"><i class="icon icon_card_' . ( $card ) . '"></i></a>';
							}
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php 
		$kingdom->coreFunctions->data_debug();
		wp_footer(); 
	?>
	<?php echo '<!-- ' . basename( get_page_template() ) . ' -->' . PHP_EOL; ?>
	<?php
	$kd_page_layout = $kingdom->coreFunctions->data['layout'];
	if( isset($kd_page_layout['full_page_slideshow']) && $kd_page_layout['full_page_slideshow'] != '' && (int)$kd_page_layout['full_page_slideshow'] > 0 ){
		$slideshow_data = get_post_meta( $kd_page_layout['full_page_slideshow'], '_slideshow_data', true ); 
		if( $slideshow_data && count($slideshow_data) > 0 && count($slideshow_data["slideshow_images"]) > 0 ){  
	?>
		<script type="text/javascript">
			$("#kd-slider").owlCarousel({
				<?php if( $slideshow_data["slideshow_autoplay_speed"] != '' ){ ?>
				autoPlay : <?php echo ((int)$slideshow_data["slideshow_autoplay_speed"])*1000; ?>, //Set AutoPlay to x seconds
				<?php } ?>
				<?php if( $slideshow_data["slideshow_navigation"] != '' ) { ?>
				navigation : <?php echo $slideshow_data["slideshow_navigation"]; ?>,
				<?php } ?>
				<?php if( $slideshow_data["slideshow_pagination"] != '' ) { ?>
				pagination : <?php echo $slideshow_data["slideshow_pagination"]; ?>,
				<?php } ?>
				itemsScaleUp : true,
				<?php if( $slideshow_data["slideshow_lazyload"] != '' ) { ?>
				lazyLoad: <?php echo $slideshow_data["slideshow_lazyload"]; ?>,
				<?php } ?>
				autoHeight : true,
				//items : 2,
				singleItem:true,
				navigationText: ["<span></span>", "<span></span>"],
				afterUpdate: kdAfterUpdateEffect( $("#kd-slider") )
			});
		</script>
	<?php } 
	} ?>
</body>
</html>