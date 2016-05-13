<?php 
/**
 * kingdomPartners class
 * ================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 * @access 		public
 * @return 		void
 */  
!defined('ABSPATH') and exit;
if (class_exists('kingdomPartners') != true) {
    class kingdomPartners
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
			$this->module_folder = $this->the_theme->cfg['paths']['theme_dir_url'] . 'modules/partners/';
			
			$this->init_postType();
			
			/* use save_post action to handle data entered */
			add_action( 'save_post', array( $this, 'meta_box_save_postdata' ) );
			
			if( isset($_GET['post_type']) && $_GET['post_type'] == 'partners') add_action('admin_head', array( $this, 'extra_css') );
			
			//add_image_size( 'partner-image', 150, 100, true );
        }

		/**
	    * Singleton pattern
	    *
	    * @return pspGoogleAnalytics Singleton instance
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
		        'name' 					=> __('Partners', $this->the_theme->localizationName),
		        'singular_name' 		=> __('partner', $this->the_theme->localizationName),
		        'add_new' 				=> __('Add new partner', $this->the_theme->localizationName),
		        'add_new_item' 			=> __('Add new partner', $this->the_theme->localizationName),
		        'edit_item'			 	=> __('Edit partner', $this->the_theme->localizationName),
		        'new_item' 				=> __('New partner', $this->the_theme->localizationName),
		        'view_item' 			=> __('View partner', $this->the_theme->localizationName),
		        'search_items' 			=> __('Search into partners', $this->the_theme->localizationName),
		        'not_found' 			=> __('No partners found', $this->the_theme->localizationName),
		        'not_found_in_trash' 	=> __('No partners in trash', $this->the_theme->localizationName)
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
			 
		
		    register_post_type('partners', $args);
			
			add_action( 'admin_head', array( $this, 'add_32px_icon' ) );
			
			// add meta boxes to "partners" post type
			add_action('admin_menu', array($this, 'add_to_menu_metabox'));
			
			// change the layout of partners list
			add_filter('manage_edit-partners_columns', array( $this, 'partners_edit_columns' ) );
			add_action('manage_posts_custom_column', array( $this, 'partners_posts_columns' ), 10, 2);
	    }

		public function add_to_menu_metabox()
		{
			// add meta box to "partners" post type
			add_meta_box(
				$this->the_theme->alias . '_details', 
				__('Partner Details', $this->the_theme->localizationName), 
				array($this, 'custom_metabox'), 
				'partners', 
				'normal'
			);
		}

		public function add_32px_icon()
		{
			?>
			<style type="text/css" media="screen">
    			.icon32-posts-partners {
    				background: url(<?php echo $this->module_folder . 'assets/32_icon.png';?>) no-repeat !important;
    			}
    		</style>
    		<?php 
		}
		
		public function partner_options( $defaults=array() )
		{ 
			if( !is_array($defaults) ) $defaults = array();
			$options = array(
				array(
					/* define the form_sizes  box */
					'details' => array(
						'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
						'header' 	=> false, // true|false
						'toggler' 	=> false, // true|false
						'buttons' 	=> false, // true|false
						'style' 	=> 'panel-widget', // panel|panel-widget
						
						// create the box elements array
						'elements'	=> array(
							'partner_image' => array(
								'type' 			=> 'upload_image_wp',
								'size' 			=> 'large',
								'force_width'	=> '80',
								'value' 		=> __('Add New Logo', $this->the_theme->localizationName),
								'title' 		=> __('Upload Image', $this->the_theme->localizationName),
								'desc' 			=> __('Partner footer image.', $this->the_theme->localizationName),
							),
							
							'partner_url' => array(
								'type' 			=> 'text',
								'size' 			=> 'large',
								'title' 		=> __('Partner link', $this->the_theme->localizationName),
								'std'			=> 'http://',
								'desc' 			=> __('Link to partner website. With http://', $this->the_theme->localizationName),
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
			$partner_url = array( 
				'partner_url' => get_post_meta( $post_id, '_partner_url', true ),
				'partner_image' => get_post_meta( $post_id, '_partner_image', true ),
			);

			// then build the html, and return it as string
			$html = $aaInterfaceTemplates->bildThePage( $this->partner_options( $partner_url ) , $this->the_theme->alias, array(), false, false);
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
					
				if($post->post_type == 'partners'){
					
					$partner_url = isset($_POST["partner_url"]) ? $_POST["partner_url"] : '';
					update_post_meta( $post_id, '_partner_url', $partner_url ); 
					
					$partner_image = isset($_POST["partner_image"]) ? $_POST["partner_image"] : '';
					update_post_meta( $post_id, '_partner_image', $partner_image ); 
				}
			}
		}

		public function partners_edit_columns($columns) 
		{
		    $new_columns['cb'] 					= '<input type="checkbox" />';
		    $new_columns['partner_id'] 			= __('ID', $this->the_theme->localizationName);
		    $new_columns['partner_thumbnail'] 	= __('Image', $this->the_theme->localizationName);
		    $new_columns['title'] 				= __('Title', $this->the_theme->localizationName);
			$new_columns['partner_website'] 	= __('Website', $this->the_theme->localizationName);
		    $new_columns['date'] 				= __('Date', $this->the_theme->localizationName);
		
		    return $new_columns;
		}
		
		public function partners_posts_columns($column_name, $id) 
		{
		    global $id; 
		    switch ($column_name) {
				case 'partner_id':
		            echo $id;
		            break;
					
		        case 'partner_thumbnail':
					$partner_image = get_post_meta( $id, '_partner_image', true );
					$before_picture = wp_get_attachment_image( $partner_image, array(100, 80) );
					echo $before_picture;
		            break;
				case 'partner_website':
					$link = get_post_meta( $id, '_partner_url', true ); 
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
		        th#partner_id {width: 40px;}
		        th#partner_thumbnail {width: 100px;}
		        th#partner_website {width: 340px;}
		        th#partner_date {width: 100px;}
		        </style>
			";
		}
	}
}

new kingdomPartners();