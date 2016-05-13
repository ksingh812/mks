<?php 
/**
 * kingdomSlideshow class
 * ================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 * @access 		public
 * @return 		void
 */  
!defined('ABSPATH') and exit;
if (class_exists('kingdomSlideshow') != true) {
    class kingdomSlideshow
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_theme = null;

		private $module_folder = '';

		static protected $_instance;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $kingdom;

        	$this->the_theme = $kingdom;
			$this->module_folder = $this->the_theme->cfg['paths']['theme_dir_url'] . 'modules/slideshow/';
			
			$this->init_postType();
			
			/* use save_post action to handle data entered */
			add_action( 'save_post', array( $this, 'meta_box_save_postdata' ) );
			
			if( isset($_GET['post_type']) && $_GET['post_type'] == 'slideshows') add_action('admin_head', array( $this, 'extra_css') );
			
			//add_image_size( 'slideshow-image', 300, 300, true );
        }

		/**
	    * Singleton pattern
	    *
	    * @return kingdomSlideshow Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }

		public function init_postType() 
		{
		    // get label
		    $labels = array(
		        'name' 					=> __('Slideshow', 'kingdom'),
		        'singular_name' 		=> __('slideshow', 'kingdom'),
		        'add_new' 				=> __('Add new slideshow', 'kingdom'),
		        'add_new_item' 			=> __('Add new slideshow', 'kingdom'),
		        'edit_item'			 	=> __('Edit slideshow', 'kingdom'),
		        'new_item' 				=> __('New slideshow', 'kingdom'),
		        'view_item' 			=> __('View slideshow', 'kingdom'),
		        'search_items' 			=> __('Search into slideshows', 'kingdom'),
		        'not_found' 			=> __('No slideshow found', 'kingdom'),
		        'not_found_in_trash' 	=> __('No slideshow in trash', 'kingdom')
		    );
		  
		    // start formationg arguments
		    $args = array(
		        'labels' => $labels,
		        'public' => false,
		        'publicly_queryable' => true,
		        'show_ui' => true,
		        'query_var' => true,
		        'menu_icon' => $this->module_folder . 'assets/16_icon.png',
		        'capability_type' => 'post',
		        'show_in_menu' => true,
		        'supports' => array( 'title' )
		    );
			 
		
		    register_post_type('slideshow', $args);
			
			add_action( 'admin_head', array( $this, 'add_32px_icon' ) );
			
			// add meta boxes to "slideshows" post type
			add_action('admin_menu', array($this, 'add_to_menu_metabox'));
			
			// change the layout of slideshows list
			add_filter('manage_edit-slideshows_columns', array( $this, 'slideshows_edit_columns' ) );
			add_action('manage_posts_custom_column', array( $this, 'slideshows_posts_columns' ), 10, 2);
	    }

		public function add_to_menu_metabox()
		{
			// add options meta box to "slideshows" post type
			add_meta_box(
				$this->the_theme->alias . '_options', 
				__('Slideshow Options', 'kingdom'), 
				array($this, 'general_options_metabox'), 
				'slideshow', 
				'normal'
			);
			// add meta box to "slideshows" post type
			add_meta_box(
				$this->the_theme->alias . '_details', 
				__('Slideshow Details', 'kingdom'), 
				array($this, 'custom_metabox'), 
				'slideshow', 
				'normal'
			);
			
			// add meta box to "slideshows" post type
			/*add_meta_box(
				$this->the_theme->alias . '_shortcode', 
				__('Slideshow Shortcode', 'kingdom'), 
				array($this, 'shortcode_metabox'), 
				'slideshow', 
				'side',
				'high'
			);*/
		}
		
		public function shortcode_metabox()
		{
			global $post_id;
			
			if( isset($post_id) && (int)$post_id > 0){
				echo '<input type="text" id="kingdom-slideshow-shortcode" value="[kingdom-slideshow id=\'' . ( $post_id ) . '\']" style="width:100%;" readonly />';
				echo '<p>You can use this shortcode in any wordpress post or page.</p>';
			}else{
				echo 'First you need to save the slideshow!';
			}
		}
		
		public function add_32px_icon()
		{
			?>
			<style type="text/css" media="screen">
    			.icon32-posts-slideshow {
    				background: url(<?php echo $this->module_folder . 'assets/32_icon.png';?>) no-repeat !important;
    			}
    		</style>
    		<?php 
		}
		
		public function slideshow_options( $defaults=array() )
		{ 
			if( !is_array($defaults) ) $defaults = array();
			$options = array(
				array(
					'details' => array(
						'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
						'header' 	=> false, // true|false
						'toggler' 	=> false, // true|false
						'buttons' 	=> false, // true|false
						'style' 	=> 'panel-widget', // panel|panel-widget
						
						// create the box elements array
						'elements'	=> array(
							'slideshow_images' => array(
								'type' 			=> 'images_gallery',
								'size' 			=> 'large',
								'value' 		=> __('Add New Image Slide', 'kingdom'),
								'title' 		=> __('Upload Images', 'kingdom'),
								'desc' 			=> __('Add images items for your slideshow', 'kingdom'),
								'options'		=> array(
								
								array(
									'details' => array(
										'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
										'header' 	=> false, // true|false
										'toggler' 	=> false, // true|false
										'buttons' 	=> false, // true|false
										'style' 	=> 'panel-widget', // panel|panel-widget
										
										// create the box elements array
										'elements'	=> array(
											
											'slide_title' => array(
												'type' 			=> 'text',
												'size' 			=> 'large',
												'title' 		=> __('Slide title', 'kingdom'),
												'std'			=> 'Untitled Slide',
											),
											
											'slide_subtitle' => array(
												'type' 			=> 'text',
												'size' 			=> 'large',
												'title' 		=> __('Slide second title', 'kingdom'),
												'std'			=> 'Untitled Slide',
											),
											
											'slide_sub_subtitle' => array(
												'type' 			=> 'text',
												'size' 			=> 'large',
												'title' 		=> __('Slide third title', 'kingdom'),
												'std'			=> 'Untitled Slide',
											),
											
											'slide_button_type' => array(
												'type' 			=> 'select',
												'size' 			=> 'large',
												'title' 		=> __('Slide link', 'kingdom'),
												'std'			=> '',
												'force_width' => '150',
												'options'		=> array(
													__('Buy NOW!', 'kingdom'),
													__('Get IT!', 'kingdom'),
													__('View Details', 'kingdom'),
													__('Read more', 'kingdom'),
													__('More', 'kingdom'),
												)
											),
											
											'slide_link' => array(
												'type' 			=> 'text',
												'size' 			=> 'large',
												'title' 		=> __('Slide link', 'kingdom'),
												'std'			=> 'http://',
											),
											
											'enable_button' => array(
												'type' 		=> 'select',
												'std' 		=> 'yes',
												'size' 		=> 'large',
												'force_width'=> '120',
												'title'		=> __('Enable Buy Button', 'kingdom'),
												'desc'		=> __('Enable Buy Button on Slide. Default is YES.', 'kingdom'),
												'options'	=> array(
													'yes' => 'YES',
													'no' => 'NO',
												)
											),
											
											'enable_textbox' => array(
												'type' 		=> 'select',
												'std' 		=> 'yes',
												'size' 		=> 'large',
												'force_width'=> '120',
												'title'		=> __('Enable Text Box', 'kingdom'),
												'desc'		=> __('Enable Text Box on Slide. Default is YES.', 'kingdom'),
												'options'	=> array(
													'yes' => 'YES',
													'no' => 'NO',
												)
											),
										)
									)
								)
								)
							),
							
							/*'slideshow_autoplay' => array(
								'type' 			=> 'select',
								'size' 			=> 'large',
								'title' 		=> __('Autoplay', 'kingdom'),
								'force_width'	=> '120',
								'std'			=> 'false',
								'desc' 			=> __('Want to autoplay the slideshow?', 'kingdom'),
								'options'		=> array(
									'true' => 'YES',
									'false' => 'NO'
								)
							),
							
							'slideshow_autoplay_speed' => array(
								'type' 			=> 'text',
								'size' 			=> 'large',
								'force_width'	=> '40',
								'title' 		=> __('Speed', 'kingdom'),
								'std'			=> '3',
								'desc' 			=> __('Autoplay after, in seconds', 'kingdom'),
								'options'		=> array(
									'true' => 'YES',
									'false' => 'NO'
								)
							)*/
						)
					)
				)
			);
			
			// setup the default value base on array with defaults
			if(count($defaults) > 0){
				foreach ($options as $option){ 
					foreach ($option as $box_id => $box){
						  
						foreach ($box['elements'] as $elm_id => $element){
							if(isset($defaults[$elm_id])){
								$option[$box_id]['elements'][$elm_id]['std'] = $defaults[$elm_id];
							}
						}
					}
				}
				
				// than update the options for returning
				$options = array( $option );
			}
			 
			return $options;
		}

		public function slideshow_general_options( $defaults=array() )
		{ 
			if( !is_array($defaults) ) $defaults = array();
			$options = array(
				array(
					'details' => array(
						'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
						'header' 	=> false, // true|false
						'toggler' 	=> false, // true|false
						'buttons' 	=> false, // true|false
						'style' 	=> 'panel-widget', // panel|panel-widget
						
						// create the box elements array
						'elements'	=> array(
							'slideshow_navigation' => array(
								'type' 			=> 'select',
								'size' 			=> 'large',
								'title' 		=> __('Enable Navigation', 'kingdom'),
								'force_width'	=> '120',
								'std'			=> 'false',
								'desc' 			=> __('Do you want to enable navigation ?', 'kingdom'),
								'options'		=> array(
									'true' => 'YES',
									'false' => 'NO'
								)
							),
							'slideshow_pagination' => array(
								'type' 			=> 'select',
								'size' 			=> 'large',
								'title' 		=> __('Enable Pagination', 'kingdom'),
								'force_width'	=> '120',
								'std'			=> 'false',
								'desc' 			=> __('Do you want to enable pagination ?', 'kingdom'),
								'options'		=> array(
									'true' => 'YES',
									'false' => 'NO'
								)
							),
							'slideshow_lazyload' => array(
								'type' 			=> 'select',
								'size' 			=> 'large',
								'title' 		=> __('Enable Lazyload', 'kingdom'),
								'force_width'	=> '120',
								'std'			=> 'false',
								'desc' 			=> __('Do you want to enable lazyload ?', 'kingdom'),
								'options'		=> array(
									'true' => 'YES',
									'false' => 'NO'
								)
							),
							'slideshow_autoplay_speed' => array(
								'type' 			=> 'text',
								'size' 			=> 'large',
								'force_width'	=> '40',
								'title' 		=> __('Speed', 'kingdom'),
								'std'			=> '3',
								'desc' 			=> __('Autoplay after, in seconds', 'kingdom'),
							)
						)
					)
				)
			);
			
			// setup the default value base on array with defaults
			if(count($defaults) > 0){
				foreach ($options as $option){ 
					foreach ($option as $box_id => $box){
						  
						foreach ($box['elements'] as $elm_id => $element){
							if(isset($defaults[$elm_id])){
								$option[$box_id]['elements'][$elm_id]['std'] = $defaults[$elm_id];
							}
						}
					}
				}
				
				// than update the options for returning
				$options = array( $option );
			}
			 
			return $options;
		}

		public function custom_metabox()
		{
			global $post_id;

			// load the settings template class
			require_once( $this->the_theme->cfg['paths']['freamwork_dir_path'] . 'settings-template.class.php' );
			
			// Initalize the your aaInterfaceTemplates
			$aaInterfaceTemplates = new aaInterfaceTemplates($this->the_theme->cfg);
			
			// retrieve the existing value(s) for this meta field. This returns an array
			$post_data = get_post_meta( $post_id, '_slideshow_data', true );
 			
 			$gallery_items = array();
			if( isset($post_data['slideshow_images']) ){
				$gallery_items = $post_data['slideshow_images'];
			}
			
			// then build the html, and return it as string
			$html = $aaInterfaceTemplates->bildThePage( $this->slideshow_options( $post_data ) , $this->the_theme->alias, array(), false, $gallery_items );
			?>
			<div class="kingdom-form">
				<?php echo $html;?>
			</div>
		<?php
		}
		
		public function general_options_metabox()
		{
			global $post_id;

			// load the settings template class
			require_once( $this->the_theme->cfg['paths']['freamwork_dir_path'] . 'settings-template.class.php' );
			
			// Initalize the your aaInterfaceTemplates
			$aaInterfaceTemplates = new aaInterfaceTemplates($this->the_theme->cfg);
			
			// retrieve the existing value(s) for this meta field. This returns an array
			$post_data = get_post_meta( $post_id, '_slideshow_data', true );
 			
 			$general_options_items = array();
			if( isset($post_data['elements']) ){
				$general_options_items = $post_data['elements'];
			}
			
			// then build the html, and return it as string
			$html = $aaInterfaceTemplates->bildThePage( $this->slideshow_general_options( $post_data ) , $this->the_theme->alias, array(), false, $general_options_items );
			?>
			<div class="kingdom-form">
				<?php echo $html;?>
			</div>
		<?php
		}
		
		/* when the post is saved, save the custom data */
		public function meta_box_save_postdata( $post_id ) 
		{
			global $post;
			
			if( isset($post) ) {
				// do not save if this is an auto save routine
				if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
					return $post_id;
				
				if($post->post_type == 'slideshow'){
					$slideshow_meta = array();
					$slideshow_options = $this->slideshow_options();
					$slideshow_general_options = $this->slideshow_general_options();
					foreach ($slideshow_options as $option){
						foreach ($option as $box_id => $box){
							foreach ($box['elements'] as $elm_id => $element){
								 
								if( $element['type'] == 'images_gallery' ){
									
									if( count($element['options'][0]['details']['elements']) > 0 ){
										$items = array();
										foreach ($element['options'][0]['details']['elements'] as $key_sub_elm => $value_sub_elm) {
											$items[$key_sub_elm] = $_POST[$key_sub_elm];
										}
										
										// add the image url
										$items['kingdom-gallery-images'] = isset($_POST['kingdom-gallery-images']) ? $_POST['kingdom-gallery-images'] : '';
									}
									
									// refactoring the items array, 1 item per key
									if( count($items) > 0 ){
										$items_size = count($items['kingdom-gallery-images']);
										$_items = $items;
										$items = array();
										 
										foreach ($_items as $key_filed => $value_filed) {
											for ($i=1; $i <= $items_size; $i++) { 
												$items[$i][$key_filed] = $value_filed[$i];
											}
										}
									}
									
									$slideshow_meta[$elm_id] = $items; 
								}
								else{
									$slideshow_meta[$elm_id] = $_POST[$elm_id];
								}
							}
						}
					}
					
					foreach ($slideshow_general_options as $option){
						foreach ($option as $box_id => $box){
							foreach ($box['elements'] as $elm_id => $element){  
								$slideshow_meta[$elm_id] = $_POST[$elm_id];
							}
						}
					}
					
					update_post_meta( $post_id, '_slideshow_data', $slideshow_meta );
				}
			}
		}

		public function slideshows_edit_columns($columns) 
		{
		    $new_columns['cb'] 					= '<input type="checkbox" />';
		    $new_columns['slideshow_id'] 		= __('ID', 'kingdom');
		    $new_columns['slideshow_thumbnail'] = __('Image', 'kingdom');
		    $new_columns['title'] 				= __('Title', 'kingdom');
			$new_columns['slideshow_website'] 	= __('Website', 'kingdom');
		    $new_columns['date'] 				= __('Date', 'kingdom');
		
		    return $new_columns;
		}
		
		public function slideshows_posts_columns($column_name, $id) 
		{
		    global $id; 
		    switch ($column_name) {
				case 'slideshow_id':
		            echo $id;
		            break;
				case 'slideshow_website':
					$link = get_post_meta( $id, '_slideshow_url', true ); 
					if( trim($link) != "" ){
						echo '<a href="' . ( $link ) . '">' . ( $link ) . '</a>';
					}else{
						echo '&ndash;';
					}
		            break;
		        default:
		            break;
		    } // end switch
		}
    	
		public function extra_css() 
		{
		    echo "
		        <style type='text/css'>
		        th#slideshow_id {width: 40px;}
		        th#slideshow_thumbnail {width: 130px;}
		        th#slideshow_website {width: 340px;}
		        th#slideshow_date {width: 100px;}
		        </style>
			";
		}
	}
}

new kingdomSlideshow();