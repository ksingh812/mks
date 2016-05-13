<?php 
/*
Plugin Name: Kingdom - Shortcodes plugin
Version: 1.0
Description: Add shortcodes for Kingdom Theme
Author: AA-Team
Plugin URI: http://themeforest.net/user/AA-Team/portfolio
*/

if(class_exists('kingdomShortcodes') != true) {
	class kingdomShortcodes
	{
		public $the_theme = null;
		public $data = array();
		public $settings = array();
		
		// shortcuts 
		public $template_directory = '';
		public $locName = '';
		
		/* 1. The class constructor
		=========================== */
		public function __construct( $the_theme=array() ) 
		{
			$this->the_theme = $the_theme;
			
			if( isset($this->the_theme->alias) && $this->the_theme->alias == 'kingdom' ){
				$this->template_directory = $this->the_theme->cfg['paths']['theme_dir_url'];
				$this->locName = $this->the_theme->localizationName;
				
				// define shortcodes lists
				add_shortcode( 'kingdom_button', array( $this, 'button') );
				add_shortcode( 'kingdom_list_type', array( $this, 'list_type') );
				add_shortcode( 'kingdom_column', array( $this, 'column') );
				add_shortcode( 'kingdom_row', array( $this, 'row') );
				add_shortcode( 'kingdom_box_headline', array( $this, 'box_headline') );
				add_shortcode( 'kingdom_footer_menu', array( $this, 'footer_menu') );
				add_shortcode( 'kingdom_horizontal_line_blue', array( $this, 'horizontal_line_blue') );
				add_shortcode( 'kingdom_horizontal_line_gray', array( $this, 'horizontal_line_gray') );
				add_shortcode( 'kingdom_code', array( $this, 'code') );
				add_shortcode( 'kingdom_google_maps', array( $this, 'google_maps') );
				
				add_shortcode( 'kingdom_blog_slideshow', array( $this, 'blog_slideshow') );
				
				//[kingdom_box_headline class="kd_featured"][/kingdom_box_headline]
			}
		}
		
		public function google_maps( $atts, $content )
		{
			$html = array();
			extract( shortcode_atts( array(
				'address' 	=> '',
				'zoom'		=> 20
			), $atts ) ); 
			
			$content = str_replace( array( '<p></p>', '<p>  </p>', '<br />', '<br>' ), '', $content );
			$html[] = "<div class='kingdom-map' data-address='{$address}' data-zoom='{$zoom}'></div>";
			
			return implode("\n", $html);
		}
	
		
		public function footer_menu( $atts, $content )
		{
			$html = array();
			extract( shortcode_atts( array(
				'ids' 	=> ''
			), $atts ) );
			
			if( trim($ids) != "" ){
				$ids = explode(",", $ids);
				if( count($ids) > 0 ){
					$html[] = '<ul>';
					foreach ( $ids as $key => $value){
						$html[] = '<li>';
						$html[] = 	'<a href="' . ( get_permalink( trim($value) ) ) . '">' . ( get_the_title( trim($value) ) ) . '</a>';
						$html[] = '</li>';
					}
					$html[] = '</ul>';					
				}
			}
			
			return implode("\n", $html);
		}
		
		public function horizontal_line_blue( $atts, $content )
		{
			return '<div class="horizontal-line-blue"></div>';	
		}
		
		public function horizontal_line_gray( $atts, $content )
		{
			return '<div class="horizontal-line-gray"></div>';	
		}

		public function code( $atts, $content )
		{
			return '<code>' . ( $content ) . '</code>';	
		}
		
		public function column( $atts, $content )
		{
			extract( shortcode_atts( array(
				'size' 			=> '4'
			), $atts ) );
			
			return '<div class="col-lg-' . ( $size ) . '">' . ( do_shortcode($content) ) . '</div>';
		}
		
		public function box_headline( $atts, $content )
		{
			extract( shortcode_atts( array(
				'class'	=> 'kd_featured'
			), $atts ) );
 
			return 
				'<div class="' . ( $class ) . '">
					<h1>' . ( strip_tags( $content ) ) . '</h1>
				</div>';
		}
		
		public function row( $atts, $content )
		{
			extract( shortcode_atts( array(
				'type' 			=> '-fluid'
			), $atts ) );
			$content = str_replace( array( '<p></p>', '<p>  </p>', '<br />', '<br>' ), '', $content );
			return '<div class="row">' . ( do_shortcode($content) ) . '</div>';
		}
		
		public function button( $atts, $content )
		{
			extract( shortcode_atts( array(
				'link' 			=> '',
				'color' 		=> 'green-blue',
				'color' 		=> 'blue-green',
				'color' 		=> 'dark-red',
				'color' 		=> 'gray-blue',
				'color' 		=> 'red-dark',
				'color' 		=> 'gray-green',
				'show_icon' 	=> 'true',
			), $atts ) );
			
			$content = str_replace( array( '<p></p>', '<p>  </p>', '<br />', '<br>' ), '', $content );
			
			return '<a href="' . ( $link ) . '" class="btn-' . ( $color ) . '">' . ( $content ) . '' . ( $show_icon == 'true' ? '<i class="icon"></i>' : '' ) . '</a>';
		}

		public function list_type( $atts, $content )
		{
			extract( shortcode_atts( array(
				'type' 	=> 'list-arrow',
				'type' 	=> 'list-dots',
				'type' 	=> 'list-box'
			), $atts ) );
			$content = str_replace( array( '<ul>', '</ul>', '<p></p>', '<p>  </p>', '<br />', '<br>' ), '', $content );
			return '
				<ul class="' . ( $type ) . '">
					' . ( $content ) . '
				</ul>';
		}
		
		public function blog_slideshow( $atts, $content )
		{
			$html = array();
			
			extract( shortcode_atts( array(
				'per_page' 	=> '10',
				'orderby' => 'post_date'
			), $atts ) );
			
			$args = array(
			    'numberposts' => (int)$per_page,
			    'orderby' => 'post_date',
			    'order' => 'DESC',
			    'post_type' => 'post',
			    'post_status' => 'publish',
			    'suppress_filters' => true 
			);
		
		    $recent_posts = wp_get_recent_posts( $args, ARRAY_A );
			if( count($recent_posts) > 0 ){
				
				$html[] = '<div class="col-lg-12"><div id="kd_blog_slider">';
				
				foreach ($recent_posts as $post) {
					
					$post_link = get_permalink( $post['ID'] );
					$post_thumbnail_id = get_post_thumbnail_id( $post['ID'] );
					$thumb = wp_get_attachment_image_src( $post_thumbnail_id, array(250,250) );
					
					$html[] = '<div class="row item">';
					
					if( $thumb != false ){
						$html[] = 	'<div class="col-lg-3 col-md-3 col-sm-5 col-xs-12 kd_blog_slider_img">';
						$html[] = 		'<a href="' . ( $post_link ) . '"><img src="' . ( $thumb[0] ) . '" alt="image"/></a>';
						$html[] = 	'</div>';
						
						$html[] = 	'<div class="col-lg-9 col-md-9 col-sm-7 col-xs-12">';
					}
					else{
						$html[] = 	'<div class="col-lg-12">';
					}
					
					$html[] = 		'<h2><a href="' . ( $post_link ) . '">' . ( $post['post_title'] ) . '</a></h2>';
					$html[] = 		'<p>';
					$html[] = 			'<span>'.get_the_date( $d, $post['ID'] ). ' / ' .get_comments_number( $post['ID'] ).' '.__('Comments', $this->locName).'</span>';
					$html[] = 		'</p>';
					$html[] = 		'<p>';
					//wpautop
					$html[] = 			( $this->the_theme->coreFunctions->shorten_string( strip_tags($post['post_content']) , 300));
					$html[] = 		'</p>';
					$html[] = 		'<a href="' . ( $post_link ) . '"> '.__('Read More', $this->locName).' + </a>';
					$html[] = 	'</div>';
					$html[] = '</div>';
				}

				$html[] = '</div></div>';
			}
			
			return implode("\n", $html);
		}
	}
	
	add_action( 'init', create_function( '', 'global $kingdom; $kingdomShortcodes = new kingdomShortcodes( $kingdom );' ) );
}
