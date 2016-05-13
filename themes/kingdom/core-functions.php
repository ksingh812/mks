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
! defined( 'ABSPATH' ) and exit;


/**
 * ===============================================
 * Table of Contents
 * ===============================================
 *
 * 1. The class constructor
 * 2. Favicon function
 * 3. Load the css files for the theme
 * 4. Load the javascript files for the theme
 * 5. Extra head html content
 * 
 */
 
if(class_exists('kingdomCoreFunctions') != true) {
	class kingdomCoreFunctions extends kingdom 
	{
		private $debug = false;
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
			$this->template_directory = $this->the_theme->cfg['paths']['theme_dir_url'];
			
			// load only for frotend
			if ( !is_admin() ) {
				add_action( 'wp_head', array( $this, 'favicon' ));
				add_action( 'wp_head', array( $this, 'html_head' ));
				
				add_action( 'wp_enqueue_scripts', array( $this, 'add_styles' ), 10);
				add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ));
				
				add_action('wp', array( $this, 'update_page_data' ), 10);
				add_action('wp', array( $this, 'update_settings' ), 10);
				
				// set size for blog featured image
				add_image_size( 'blog-featured-image', 700, 272, true );
				
				// Set the content width based on the theme's design and stylesheet.
				if(!isset($content_width)) $content_width = 960;
				
				// Add default posts and comments RSS feed links to <head>.
				add_theme_support( 'automatic-feed-links' );
				
				// Background customizer
				add_theme_support('custom-background');
				
				// This theme styles the visual editor with editor-style.css to match the theme style.
				add_editor_style(); 
			}
			
			add_filter('the_content', array( $this, 'remove_empty_p' ), 20, 1);
			
			// Declare WooCommerce support
			add_theme_support( 'woocommerce' );
			
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 20, 0);
			
			add_filter( 'woocommerce_get_price_html', array($this, 'superscript_price_html'), 100, 2 );
			
			add_filter( 'loop_shop_per_page', create_function( '$cols', 'return 9;' ), 20 );
			
			// ajax star rating for amazon products
			add_action('wp_ajax_kingdom_save_stars', array( $this, 'save_stars') );
			add_action('wp_ajax_nopriv_kingdom_save_stars', array( $this, 'save_stars') );
			
			add_filter( 'the_content', array($this, 'remove_gallery'), 6);
			
			// Loads the theme's translated strings
			load_theme_textdomain( 'kingdom', TEMPLATEPATH . '/languages' );
			
			add_action('init', array( $this,'register_sidebars'), 10);
			
			// navigation menu	
			if(function_exists('add_theme_support')){
			    add_theme_support('menus');
			    register_nav_menus(array(
			    	'top_nav' =>'Top Navigation',
			    	'main_nav' => 'Main Navigation'
				));
			}
			
			$this->data['slideshow_buttons'] = array(
				__('Buy NOW!', 'kingdom'),
				__('Get IT!', 'kingdom'),
				__('View Details', 'kingdom'),
				__('Read more', 'kingdom'),
				__('More', 'kingdom'),
			);
			
			// Include the TGM_Plugin_Activation class
			require_once( $this->the_theme->cfg['paths']['freamwork_dir_path'] . 'class-tgm-plugin-activation.php' );
			add_action( 'tgmpa_register', array( $this, 'register_required_plugins') );
	    }
		
		public function update_settings()
		{
			$this->settings['layout'] = maybe_unserialize( get_option( $this->the_theme->alias . '_config', true ) ); 
		}
		
		public function data_debug()
		{ 
			if( $this->debug == true ){
			?> 
				<script>console.log( '<?php echo json_encode($this->data);?>' )</script>
			<?php
			}	
		}
		
		public function update_page_data()
		{
			$page_object = get_queried_object();
			$page_id     = get_queried_object_id();
			if( $page_id == 0 && $page_object->query_var == 'product' ){
				$page_id = woocommerce_get_page_id('shop');	
			}
			if( (int) $page_id > 0 ){
				
				// get the sidebar position and id
				$this->data['sidebar'] = array(
					'position' => get_post_meta( $page_id, '_page_sidebar_position', true ),
					'sidebars' => get_post_meta( $page_id, '_page_sidebar_ids', true ),
				);
				
				$this->data['layout'] = get_post_meta( $page_id, '_layout', true );
			} 
			
			// get all sidebars
			$sidebars_list = get_option( 'kingdom_dynamic_sidebars', true ); 
			if( $sidebars_list && count($sidebars_list) > 0 && $sidebars_list !== true ){
				foreach ($sidebars_list as $sidebar ) {
					$this->data['sidebar'][sanitize_title($sidebar['title'])] = get_option( 'kingdom_ds_' . md5(sanitize_title($sidebar['title'])), true );
				}
			}
		}
		
		public function printSidebar( $pos='none' )
		{
			if( isset($this->data['page_sidebars']) && count($this->data['page_sidebars']) > 0 ){
				foreach ($this->data['page_sidebars'] as $sidebar_key => $sidebar_value) {
					if( $sidebar_value['settings']['position'] == $pos && $this->data['sidebar']['printed'] != true ){
						$this->data['sidebar']['printed'] = true;
		?>
						<div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
							<div class="kd_shop_sidebar">
								<?php
									dynamic_sidebar( $sidebar_key );
								?>
							</div>
						</div>
		<?php
					}
				}
			}
		}

		public function content_class()
		{
			$class = 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
			if( isset($this->data['page_sidebars']) && count($this->data['page_sidebars']) > 0 ){
				$class = 'col-lg-9 col-md-8 col-sm-8 col-xs-12';
			}
			
			return $class;
		}

		public function register_sidebars()
		{
			if ( function_exists('register_sidebar') ) {
				// default sidebar
				
				register_sidebar(array(
					'name' => 'Footer Content',
					'id' => 'footercontent',
					'description'   => '',
				    'class'         => '',
					'before_widget' => '<div class="kingdom-widget col-lg-3 col-sm-6 col-xs-12"><div class="kd_footer_column">',
					'after_widget'  => '</div></div>',
					'before_title'  => '<h3>',
					'after_title'   => '</h3>'
				));
				
				// custom sidebars (base on admin.php?page=kingdom#!/sidebars)
				$sidebars_meta =  get_option( 'kingdom_dynamic_sidebars' );
				if( $sidebars_meta !== false && count($sidebars_meta) > 0 && isset($sidebars_meta) ){
					$sidebars_meta = $sidebars_meta; 
					foreach ($sidebars_meta as $key => $value) {
						register_sidebar(array(
							'name' => $value['title'],
							'id' => sanitize_title($value['title']),
							'before_widget' => '<div id="%1$s" class="kingdom-widget %2$s">',
							'after_widget' => '</div>',
							'before_title' => '<h3>',
							'after_title' => '</h3>'
						));  
					}
				}
			}
		}

		public function limit_posts_per_archive_page() {
			if ( is_search() )
				set_query_var('posts_per_archive_page', 100); // or use variable key: posts_per_page
		}
		
		/* 2. Favicon function
		====================== */
		public function favicon()
		{
			if(isset($this->settings['layout']['favicon']) && !empty($this->settings['layout']['favicon'])){
				$image = wp_get_attachment_image_src( (int)$this->settings['layout']['favicon'], 'thumbnail' ); 
				$favicon = '<link rel="shortcut icon" href="' . $image[0] . '"/>';
			} else {
				$favicon = '<link rel="shortcut icon" href="'. ( $this->template_directory ) .'favicon.ico" />';
			}
			echo $favicon;
		}
		
		/* 3. Load the css files for the theme
		====================================== */
		public function add_styles()
		{
			global $kingdom;
			$protocol = is_ssl() ? 'https' : 'http';
			
			if(is_singular() && comments_open() && get_option('thread_comments')) wp_enqueue_script('comment-reply');
			
			wp_enqueue_style( $this->the_theme->alias . '-bootstrapcss', $this->template_directory . 'css/bootstrap.css', array(), '3.1.0' );
			wp_enqueue_style( $this->the_theme->alias . '-bootstrap-theme', $this->template_directory . 'css/bootstrap-theme.css', array(), '1.0' );	
			if( !isset( $this->settings['layout']["website_main_font"] ) ) {
				wp_enqueue_style( $this->the_theme->alias . '-Open-Sans', $protocol . '://fonts.googleapis.com/css?family=Open+Sans:400,300,600' );	
			} else {
				wp_enqueue_style( $this->the_theme->alias . '-' . str_replace( ' ', '', $this->settings['layout']["website_main_font"] ), $protocol . '://fonts.googleapis.com/css?family=' . $this->settings['layout']["website_main_font"] );	
			}
			if( isset( $this->settings['layout']["menu_font"] ) ) {
				wp_enqueue_style( $this->the_theme->alias . '-' . str_replace( ' ', '', $this->settings['layout']["menu_font"] ), $protocol . '://fonts.googleapis.com/css?family=' . $this->settings['layout']["menu_font"] );		
			}
			
			$style_url = $this->template_directory . 'load-style.php';
			  
			if( is_file( $this->template_directory . 'load-style.css' ) ){  
				$style_url = str_replace(".php", '.css', $style_url);
			}
			wp_enqueue_style( $this->the_theme->alias . '-main-style', $style_url, array( $this->the_theme->alias . '-bootstrapcss' ), '1.0' );
			
			//wp_enqueue_style( $this->the_theme->alias . '-main-style', $this->template_directory . 'load-style.php', array( $this->the_theme->alias . '-bootstrapcss' ), '1.0' );
			
			if( $kingdom->coreFunctions->settings['layout']['responsiveness'] == 'false' ){	
				wp_enqueue_style( $this->the_theme->alias . '-non-responsive-style', $this->template_directory . 'style-non-responsive.css', array(), '1.0' );
			}
			
			if( $kingdom->coreFunctions->settings['layout']['responsiveness'] == 'true' ){
				wp_enqueue_style( $this->the_theme->alias . '-responsive', $this->template_directory . 'css/responsive.css', array(), '1.0' );
			}
			wp_enqueue_style( $this->the_theme->alias . '-carousel', $this->template_directory . 'owl-carousel/owl.carousel.css', array(), '1.2' );
			wp_enqueue_style( $this->the_theme->alias . '-carousel-theme', $this->template_directory . 'owl-carousel/owl.theme.css', array(), '1.2' );
			
			wp_enqueue_style( $this->the_theme->alias . '-prettyPhoto', $this->template_directory . 'css/prettyPhoto.css', array(), '1.0' );
			wp_enqueue_style( $this->the_theme->alias . '-prettyphoto', $this->template_directory . 'addons/prettyphoto/prettyPhoto.css', array(), '1.0' );

			wp_enqueue_style( $this->the_theme->alias . '-font-awesome', $this->template_directory . 'css/font-awesome.css' );

			// DISABLE WOOCOMMERCE PRETTY PHOTO STYLE
			wp_deregister_style( 'woocommerce_prettyPhoto_css' );
		}
		
		/* 4. Load the javascript files for the theme
		============================================= */
		public function add_scripts()
		{
			// wp_enqueue_script( $this->the_theme->alias . '-jquery', '//code.jquery.com/jquery-1.10.1.min.js', array(), '1.10.1', true);
			wp_enqueue_script( $this->the_theme->alias . '-jquery-ui', '//code.jquery.com/ui/1.10.4/jquery-ui.js', array(), '1.10.4', true);
			wp_enqueue_script( $this->the_theme->alias . '-google-maps', '//maps.google.com/maps/api/js?sensor=false', array('jquery'), '2.0', true);
			wp_enqueue_script( $this->the_theme->alias . '-bootstrapjs', $this->template_directory . 'js/bootstrap.js', array('jquery'), '3.0.0', true);
			wp_enqueue_script( $this->the_theme->alias . '-owl-carousel', $this->template_directory . 'owl-carousel/owl.carousel.js', array('jquery'), '1.2', true);
			wp_enqueue_script( $this->the_theme->alias . '-bootstrap-rating', $this->template_directory . 'js/bootstrap-rating-input.min.js', array('jquery'), '1.0', true);
			wp_enqueue_script( $this->the_theme->alias . '-pretty-photo', $this->template_directory . 'js/jquery.prettyPhoto.min.js', array('jquery'), '1.0', true);
			wp_enqueue_script( $this->the_theme->alias . '-ddaccordion', $this->template_directory . 'js/ddaccordion.js', array('jquery'), '1.0', true);
			wp_enqueue_script( $this->the_theme->alias . '-responsive-nav', $this->template_directory . 'js/responsive-nav.js', array('jquery'), '1.0', true);
			// wp_enqueue_script( $this->the_theme->alias . '-responsive-nav', $this->template_directory . 'js/responsive-nav.min.js', array('jquery'), '1.0', true);
			wp_enqueue_script( $this->the_theme->alias . '-prettyphoto', $this->template_directory . 'addons/prettyphoto/jquery.prettyPhoto.js', array('jquery'), '1.0', true);
			wp_enqueue_script( $this->the_theme->alias . '-jquery-gmap', $this->template_directory . 'js/jquery.gmap.min.js', array('jquery', $this->the_theme->alias . '-google-maps'), '1.0', true);
			wp_enqueue_script( $this->the_theme->alias . '-main', $this->template_directory . 'js/main.js', array('jquery'), '1.0', true);
			wp_enqueue_script( $this->the_theme->alias . '-mobile-detect', $this->template_directory . 'js/mobile.detect.js', array('jquery', $this->the_theme->alias . '-main'), '0.3.8', true);
			
			// DISABLE WOOCOMMERCE PRETTY PHOTO SCRIPTS
			wp_deregister_script( 'prettyPhoto' );
			wp_deregister_script( 'prettyPhoto-init' );
		}
		
		/* 5. Extra head html content
		============================= */
		public function html_head()
		{
			global $kingdom;
			if (!empty( $this->data['facebook']['opengraph'] ) ) {
				?>
					<meta property="og:title" content=""/>
					<meta property="og:type" content=""/>
					<meta property="og:url" content="<?php echo "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];?>"/>
					<meta property="og:site_name" content="<?php bloginfo( 'name'); ?>"/>
					<meta property="fb:app_id" content="<?php echo $this->data['facebook']['opengraph'];?>"/>
					<meta property="og:description" content=" <?php bloginfo( 'description'); ?>"/>
				<?php
			}
			?>
			<?php if( $kingdom->coreFunctions->settings['layout']['responsiveness'] == 'true' ){ ?>
				<meta name="viewport" content="initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0, width=device-width" />
			<?php } ?>
			<?php
		
			?>
			<script type="text/javascript">
			var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
			</script>
			
			<!--[if lt IE 9]>
				<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->
			<?php
		}
		
		public function display_breadcrumbs()
	    {
	        $text['home']     = __('Home', 'kingdom'); // text for the 'Home' link
			$text['category'] = __('Archive by Category "%s"', 'kingdom'); // text for a category page
			$text['tax'] 	  = __('Archive for "%s"', 'kingdom'); // text for a taxonomy page
			$text['search']   = __('Search Results for "%s" Query', 'kingdom'); // text for a search results page
			$text['tag']      = __('Posts Tagged "%s"', 'kingdom'); // text for a tag page
			$text['author']   = __('Articles Posted by %s', 'kingdom'); // text for an author page
			$text['404']      = __('Error 404', 'kingdom'); // text for the 404 page
		
			$showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
			//$delimiter   = '<li class="arrow-breadcrumbs"></li>'; // delimiter between crumbs
			$before      = '<li class="current">'; // tag before the current crumb
			$after       = '</li>'; // tag after the current crumb
			/* === END OF OPTIONS === */
		
			global $post;
			$homeLink = home_url() . '/';
			$linkBefore = '<li typeof="v:Breadcrumb">';
			$linkAfter = '</li>';
			$linkAttr = ' rel="v:url" property="v:title"';
			$link = $linkBefore . '<a' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;
		
			echo '<ol id="crumbs" class="breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#">' . sprintf($link, $homeLink, $text['home']) . $delimiter;
			
			if ( is_category() ) {
				$thisCat = get_category(get_query_var('cat'), false);
				if ($thisCat->parent != 0) {
					$cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
					$cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
					$cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
					echo $cats;
				}
				echo $before . sprintf($text['category'], single_cat_title('', false)) . $after;
	
			} elseif ( is_product() ) { 
				$terms = get_the_terms( $post->ID, 'product_cat' );
				foreach ($terms as $term) {
				    $product_cat_id = $term->term_id;
					$product_cat_slug = $term->slug;
					$product_cat_name = $term->name;
					
					$cats .= '<li><a href="'. get_term_link($term) .'">'.$product_cat_name.'</a></li>';	
					$cats .= '<li><a href="'.get_permalink().'">'.$post->post_title.'</a></li>';			
					echo $cats;
					break; 
				}
			} elseif( is_tax() ){
				$thisCat = get_category(get_query_var('cat'), false);
				if ($thisCat->parent != 0) {
					$cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
					$cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
					$cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
					echo $cats;
				}
				echo $before . sprintf($text['tax'], single_cat_title('', false)) . $after;
			
			}elseif ( is_search() ) {
				echo $before . sprintf($text['search'], get_search_query()) . $after;
	
			} elseif ( is_day() ) {
				echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
				echo sprintf($link, get_month_link(get_the_time('Y'),get_the_time('m')), get_the_time('F')) . $delimiter;
				echo $before . get_the_time('d') . $after;
	
			} elseif ( is_month() ) {
				echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
				echo $before . get_the_time('F') . $after;
	
			} elseif ( is_year() ) {
				echo $before . get_the_time('Y') . $after;
	
			} elseif ( is_single() && !is_attachment() ) {
				if ( get_post_type() != 'post' ) {
					$post_type = get_post_type_object(get_post_type());
					$slug = $post_type->rewrite;
					printf($link, $homeLink . '/' . $slug['slug'] . '/', $post_type->labels->singular_name);
					if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;
				} else {
					$cat = get_the_category(); $cat = $cat[0];
					$cats = get_category_parents($cat, TRUE, $delimiter);
					if ($showCurrent == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
					$cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
					$cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
					echo $cats;
					if ($showCurrent == 1) echo $before . get_the_title() . $after;
				}
	
			} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
				$post_type = get_post_type_object(get_post_type());
				echo $before . $post_type->labels->singular_name . $after;
	
			} elseif ( is_page() && !$post->post_parent ) {
				if ($showCurrent == 1) echo $before . get_the_title() . $after;
	
			} elseif ( is_page() && $post->post_parent ) {
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					$breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				for ($i = 0; $i < count($breadcrumbs); $i++) {
					echo $breadcrumbs[$i];
					if ($i != count($breadcrumbs)-1) echo $delimiter;
				}
				if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;
	
			} elseif ( is_tag() ) {
				echo $before . sprintf($text['tag'], single_tag_title('', false)) . $after;
	
			} elseif ( is_author() ) {
		 		global $author;
				$userdata = get_userdata($author);
				echo $before . sprintf($text['author'], $userdata->display_name) . $after;
	
			} elseif ( is_404() ) {
				echo $before . $text['404'] . $after;
			}
	
			echo '</ol>';
	    }
		
		public function get_product_more_images($offset = 0, $size, $class = '',$num ='-1')
		{
			global $post;

			$args = array(
				'order'          	=> 'ASC',
				'orderby'        	=> 'menu_order',
				'post_type'      	=> 'attachment',
				'post_parent'   	 => $post->ID,
				'post_mime_type' 	=> 'image',
				'post_status'    	=> null,
				'numberposts'     	=> $num,
				//'exclude'    	=> get_post_thumbnail_id(),
			);
			
			$attachments = get_posts( $args, ARRAY_A );
 
			$images = array();
		  	if ($attachments) {
				foreach ($attachments as $attachment) {
					$full_img = wp_get_attachment_image_src( $attachment->ID, 'full' );
					$images[] = array(
						'thumb' => wp_get_attachment_image( $attachment->ID, $size, false, array('class' => $class) ),
						'full_img' => $full_img[0]
					);
				}
			}
			return $images;
		}

		/**
		* Get first paragraph from a WordPress post. Use inside the Loop.
		*
		* @return string
		*/
		function get_first_paragraph()
		{
			global $post;
			$str = wpautop( preg_replace("~(?:\[/?)[^/\]]+/?\]~s", '', get_the_content() ) );
			$str = substr( $str, 0, strpos( $str, '</p>' ) + 4 );
			$str = strip_tags($str);
			 
			return '<p>' . implode(' ', array_slice(explode(' ', $str), 0, 30)) . '</p>';
		}

		public function remove_gallery($content)
		{
			if( is_product() ){	
		    	return str_replace('[gallery]', '', $content);
			}
			return $content;
		}
		
		public function save_stars()
		{
			$_product_rating = (int)get_post_meta( $post->ID, '_product_rating', true );
			$_product_votes = (int)get_post_meta( $post->ID, '_product_votes', true );
			
			update_post_meta( (int)$_REQUEST['productid'], '_product_rating', $_product_rating + (int)$_REQUEST['value'] );
			update_post_meta( (int)$_REQUEST['productid'], '_product_votes', $_product_votes + 1 );
			die(json_encode(array(
				'status' => 'valid'
			)));
		}
		
		public function superscript_price_html( $price, $product )
		{
			$post_id = isset($product->id) ? $product->id : 0;
			if ( $post_id <=0 ) return $price;
			
			return preg_replace('/\.([0-9]*)/', '<sup>.$1</sup>', $price);
		}
		
		public function print_slideshow_button( $key=0 )
		{
			return isset($this->data['slideshow_buttons'][$key]) ? $this->data['slideshow_buttons'][$key] : ''; 
		}

		public function remove_empty_p($content)
		{
		    $content = force_balance_tags($content);
		    return preg_replace('#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content);
		}
		
		public function comment_template()
		{
			global $comment;
			$GLOBALS['comment'] = $comment;   
		?>
			<div class="kd_comments">
				<div class="kd_comment_author">
					<div class="kd_comment_image">
						<?php echo get_avatar( $comment, 50 );?>
					</div>
	
					<div class="kd_comment_name">
						<h3><?php comment_author();?></h3>
						<p><span><?php comment_date();?></span></p>
					</div>		
				</div>				
				<div class="kd_comment_container">
					<?php comment_text(); ?>
					<i class="icon icon_arrow_comment"></i>
					<div class="kd_comment_reply"> <a href="#">Reply</a></div>
				</div>
			</div>
		<?php
		}
		
		public function print_share_buttons( $post_id, $post_title='' )
		{
			$post_url = get_permalink( $post_id ); 
			$image_url = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );
		?>
			<!--a href="<?php echo $post_url;?>"><i class="icon icon_kd_dribbble"></i></a>
			<a href="<?php echo $post_url;?>"><i class="icon icon_kd_youtube"></i></a-->
			<a href="http://www.facebook.com/sharer.php?u=<?php echo $post_url;?>"><i class="icon icon_kd_facebook"></i></a>
			<a href="https://plus.google.com/share?url=<?php echo $post_url;?>"><i class="icon icon_kd_google"></i></a>
			<a href="https://pinterest.com/pin/create/bookmarklet/?url=<?php echo $post_url;?>&description=<?php echo $post_title;?>&media=<?php echo $image_url; ?>"><i class="icon icon_kd_pinterest"></i></a>
			<a href="https://twitter.com/share?url=<?php echo $post_url;?>&text=<?php echo $post_title;?>"><i class="icon icon_kd_twitter"></i></a>
		<?php
		}
		
		public function shorten_string($str, $limit=100, $strip = false)
		{
		    $str = ($strip == true)?strip_tags($str):$str;
		    if (strlen ($str) > $limit) {
		        $str = substr ($str, 0, $limit - 3);
		        return (substr ($str, 0, strrpos ($str, ' ')).'...');
		    }
		    return trim($str);
		}
		
		public function register_required_plugins() 
		{
			/**
			 * Array of plugin arrays. Required keys are name and slug.
			 * If the source is NOT from the .org repo, then source is also required.
			 */
			$plugins = array(
		
				array(
					'name'     				=> 'Kingdom - Shortcodes plugin',
					'slug'     				=> 'kingdom-shortcodes',
					'source'   				=> get_stylesheet_directory() . '/plugins/kingdom-shortcodes.zip',
					'required' 				=> true,
					'version' 				=> '1.0', 
					'force_activation' 		=> false,
					'force_deactivation' 	=> false,
					'external_url' 			=> '', 
				),
				
				array(
					'name'     				=> 'Revolution Slider',
					'slug'     				=> 'revslider',
					'source'   				=> get_stylesheet_directory() . '/plugins/revslider.zip',
					'required' 				=> true,
					'version' 				=> '4.6.0', 
					'force_activation' 		=> false,
					'force_deactivation' 	=> false,
					'external_url' 			=> '', 
				),
				
				array(
					'name'     				=> 'AA Backup Manager', 
					'slug'     				=> 'aa-backup-manager',
					'source'   				=> get_stylesheet_directory() . '/plugins/aa-backup-manager.zip',
					'required' 				=> true,
					'version' 				=> '1.0', 
					'force_activation' 		=> true,
					'force_deactivation' 	=> false,
					'external_url' 			=> '',
				),

		        array(
		            'name'      => 'Woocommerce',
		            'slug'      => 'woocommerce',
		            'required'  => true,
		        ),
		        
				array(
		            'name'      => 'Contact Form 7',
		            'slug'      => 'contact-form-7',
		            'required'  => false,
		        ),
		        
				array(
		            'name'      => 'Recent Posts Widget Extended',
		            'slug'      => 'recent-posts-widget-extended',
		            'required'  => false,
		        ),
		        
				array(
		            'name'      => 'Regenerate Thumbnails',
		            'slug'      => 'regenerate-thumbnails',
		            'required'  => false,
		        ),
				
			);
		
			// Change this to your theme text domain, used for internationalising strings
			$theme_text_domain = 'kingdom';
		
			/**
			 * Array of configuration settings. Amend each line as needed.
			 * If you want the default strings to be available under your own theme domain,
			 * leave the strings uncommented.
			 * Some of the strings are added into a sprintf, so see the comments at the
			 * end of each line for what each argument will be.
			 */
			$config = array(
				'domain'       		=> $theme_text_domain,         	// Text domain - likely want to be the same as your theme.
				'default_path' 		=> '',                         	// Default absolute path to pre-packaged plugins
				'parent_menu_slug' 	=> 'themes.php', 				// Default parent menu slug
				'parent_url_slug' 	=> 'themes.php', 				// Default parent URL slug
				'menu'         		=> 'install-required-plugins', 	// Menu slug
				'has_notices'      	=> true,                       	// Show admin notices or not
				'is_automatic'    	=> false,					   	// Automatically activate plugins after installation or not
				'message' 			=> '',							// Message to output right before the plugins table
				'strings'      		=> array(
					'page_title'                       			=> __( 'Install Required Plugins', $theme_text_domain ),
					'menu_title'                       			=> __( 'Install Plugins', $theme_text_domain ),
					'installing'                       			=> __( 'Installing Plugin: %s', $theme_text_domain ), // %1$s = plugin name
					'oops'                             			=> __( 'Something went wrong with the plugin API.', $theme_text_domain ),
					'notice_can_install_required'     			=> _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.' ), // %1$s = plugin name(s)
					'notice_can_install_recommended'			=> _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.' ), // %1$s = plugin name(s)
					'notice_cannot_install'  					=> _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.' ), // %1$s = plugin name(s)
					'notice_can_activate_required'    			=> _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
					'notice_can_activate_recommended'			=> _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.' ), // %1$s = plugin name(s)
					'notice_cannot_activate' 					=> _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.' ), // %1$s = plugin name(s)
					'notice_ask_to_update' 						=> _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.' ), // %1$s = plugin name(s)
					'notice_cannot_update' 						=> _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.' ), // %1$s = plugin name(s)
					'install_link' 					  			=> _n_noop( 'Begin installing plugin', 'Begin installing plugins' ),
					'activate_link' 				  			=> _n_noop( 'Activate installed plugin', 'Activate installed plugins' ),
					'return'                           			=> __( 'Return to Required Plugins Installer', $theme_text_domain ),
					'plugin_activated'                 			=> __( 'Plugin activated successfully.', $theme_text_domain ),
					'complete' 									=> __( 'All plugins installed and activated successfully. %s', $theme_text_domain ), // %1$s = dashboard link
					'nag_type'									=> 'updated' // Determines admin notice type - can only be 'updated' or 'error'
				)
			);
		
			tgmpa( $plugins, $config );
		}

		public function adjustBrightness($hex, $steps) 
		{
		    // Steps should be between -255 and 255. Negative = darker, positive = lighter
		    $steps = max(-255, min(255, $steps));
		
		    // Format the hex color string
		    $hex = str_replace('#', '', $hex);
		    if (strlen($hex) == 3) {
		        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
		    }
		
		    // Get decimal values
		    $r = hexdec(substr($hex,0,2));
		    $g = hexdec(substr($hex,2,2));
		    $b = hexdec(substr($hex,4,2));
		
		    // Adjust number of steps and keep it inside 0 to 255
		    $r = max(0,min(255,$r + $steps));
		    $g = max(0,min(255,$g + $steps));  
		    $b = max(0,min(255,$b + $steps));
		
		    $r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
		    $g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
		    $b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
		
		    return '#'.$r_hex.$g_hex.$b_hex;
		}
		
		public function getAllGfonts( $what='all' ) { 
			$fonts = json_decode(  
				$this->the_theme->wp_filesystem->get_contents( $this->the_theme->cfg['paths']['theme_dir_path'] . '/fonts/google-webfonts.json' ) 
			, true);
			
			$ret_fonts = array();
			if(count($fonts['items']) > 0 ){ 
				foreach ( $fonts['items'] as $font ) {
					$ret_fonts[$font['family']] = $font['family'];
				}
			}
			
			if( $what == 'all' ){
				return $ret_fonts;
			}
			
		}
	}
}