<?php 
/**
 * kingdomLayout class
 * ================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 * @access 		public
 * @return 		void
 */  
!defined('ABSPATH') and exit;

if (class_exists('kingdomLayout') != true) {
    class kingdomLayout
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
		
		private $where = array('post', 'page', 'product' );

		static protected $_instance;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $kingdom;

        	$this->the_theme = $kingdom;
			$this->module_folder = $this->the_theme->cfg['paths']['theme_dir_url'] . 'modules/layout/';
			
			/* use save_post action to handle data entered */
			add_action( 'save_post', array( $this, 'meta_box_save_postdata' ) );
			
			add_action( 'init', array( $this, 'module_int' ) );
        }

		/**
	    * Singleton pattern
	    *
	    * @return kingdomLayout Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }
		
		public function module_int()
		{
			// add meta boxe
			add_action('admin_menu', array($this, 'add_to_menu_metabox'));
		}

		public function add_to_menu_metabox()
		{
			foreach ($this->where as $key => $value) {
				// add the meta box
				add_meta_box(
					$this->the_theme->alias . '_layout_setup', 
					__('Layout options', 'kingdom'), 
					array($this, 'custom_metabox'), 
					$value, 
					'normal'
				);
			}
		}
		
		public function module_options( $defaults=array() )
		{
			global $wpdb;	
			
			$revslider_arr_no = array('no-revsliders' => 'No revsliders created');
			$revslider_arr = array();
			$revsliders = $wpdb->get_results( "SELECT title, alias FROM ".$wpdb->prefix."revslider_sliders" );
			if( count( $revsliders ) > 0 && $revsliders != '' ) {
				foreach( $revsliders as $slider ){
					$revslider_arr[$slider->alias] = $slider->title;
				}
			} 
			
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
							'home_slider' => array(
								'type' 		=> 'select',
								'std' 		=> 'true',
								// 'force_width'=> '80',
								'size' 		=> 'small',
								'title'		=> __('Home Slider Type', 'kingdom'),
								'desc'		=> __('Select Slider Type for Homepage.', 'kingdom'),
								'options'	=> array(
									'kingdom-slider' => 'Kingdom Slider',
									'revolution-slider'=> 'Revolution Slider'
								)
							),
							
							'revolution_slider_select' => array(
								'type' 			=> 'select',
								'size' 			=> 'small',
								// 'force_width'	=> '100',
								'title' 		=> __('Select Revslider', 'kingdom'),
								'std'			=> 'true',
								'desc' 			=> __('Choose wich revolution slider you want to use on this page', 'kingdom'),
								'options'		=> count( $revslider_arr ) > 0 && $revslider_arr != '' ? $revslider_arr : $revslider_arr_no
							),
							
							'full_page_slideshow' => array(
								'type' 			=> 'select',
								'size' 			=> 'large',
								'force_width'	=> '200',
								'title' 		=> __('Full page slideshow', $this->the_theme->localizationName),
								'desc' 			=> __('Choose a full page slideshow from list for this page', $this->the_theme->localizationName),
								'options'		=> $this->slideshows_list()
							),
							
							'print_page_title' => array(
								'type' 			=> 'select',
								'size' 			=> 'large',
								'force_width'	=> '100',
								'title' 		=> __('Show post title', $this->the_theme->localizationName),
								'std'			=> 'true',
								'desc' 			=> __('Show the page title for this post', $this->the_theme->localizationName),
								'options'		=> array(
									'true' => 'YES',
									'no'	=> 'NO'
								)
							)
							
							/*'slideshow_url' => array(
								'type' 			=> 'text',
								'size' 			=> 'large',
								'title' 		=> __('Slideshow link', $this->the_theme->localizationName),
								'std'			=> 'http://',
								'desc' 			=> __('Link to slideshow website. With http://', $this->the_theme->localizationName),
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
		
		public function custom_metabox()
		{
			global $post_id, $post;
			
			if( (int) $post_id == 0 ){
				$post_id = $post->ID;
			}
			
			// load the settings template class
			require_once( $this->the_theme->cfg['paths']['freamwork_dir_path'] . 'settings-template.class.php' );
			
			// Initalize the your aaInterfaceTemplates
			$aaInterfaceTemplates = new aaInterfaceTemplates($this->the_theme->cfg);
			
			// retrieve the existing value(s) for this meta field. This returns an array
			$_layout = get_post_meta( $post_id, '_layout', true );
 
			// then build the html, and return it as string
			$html = $aaInterfaceTemplates->bildThePage( $this->module_options( $_layout ) , $this->the_theme->alias, array(), false, false);
			?>
			<div class="kingdom-form">
				<?php echo $html;?>
				
			    <!--div class="kingdom-grid_4">
			        <div class="kingdom-panel-widget">
			            <div class="kingdom-panel-content">
			                <div class="kingdom-form-row">
			                    <label for="full_page_slideshow"><?php _e('Sidebar Layout', 'kingdom');?></label>
			                    <div class="kingdom-form-item large">
			                        <span class="formNote">Choose a custom sidebar position for this page</span>
			                
								<?php
									
									$sidebars_meta = get_option( 'kingdom_dynamic_sidebars' ); 
									if( $sidebars_meta !== false && count($sidebars_meta) > 0 && isset($sidebars_meta) ){
										$sidebars_meta = $sidebars_meta;
										
										$sidebar_position = get_post_meta( $post_id, '_page_sidebar_position', true );
										$sidebar_ids = get_post_meta( $post_id, '_page_sidebar_ids', true );
										if( $sidebar_ids === false || !is_array($sidebar_ids) ){
											$sidebar_ids = array();
										}
										
										 
										// if page don't have any sidebars use default nosidebar
										if( !isset($sidebar_ids) || count($sidebar_ids) == 0 ) { 
											$sidebar_ids = array('default-sidebar');
										}
										if( !isset($sidebar_position) || trim($sidebar_position) == "" ) {
											$sidebar_position = 'right';
										}
								?>
										<div id="kingdom-sidebar-position">
											<div>
												<input type="radio" <?php echo $sidebar_position == 'left' ? 'checked' : '';?> name="sidebar-position" value="left" data-replaceimg='<?php echo $this->module_folder . 'assets/2cl.png';?>' data-tooltip="<?php _e('Left Sidebar', 'kingdom');?>">
												<input type="radio" <?php echo $sidebar_position == 'right' ? 'checked' : '';?> name="sidebar-position" value="right" data-replaceimg='<?php echo $this->module_folder . 'assets/2cr.png';?>' data-tooltip="<?php _e('Right Sidebar', 'kingdom');?>">
												<input type="radio" <?php echo $sidebar_position == 'nosidebar' ? 'checked' : '';?> name="sidebar-position" value="nosidebar" data-replaceimg='<?php echo $this->module_folder . 'assets/1col.png';?>' data-tooltip="<?php _e('NO Sidebar', 'kingdom');?>">
											</div>
										</div>
										<div id="kingdom-sidebar-items">
											<table>
												<tr id="kingdom-left-sidebar-item" style="display: none">
													<td width="130" valign="top"><?php _e('Left Sidebar', 'kingdom');?></td>
													<td>
														<select name="left-sidebar[]" multiple>
														<?php
														foreach ($sidebars_meta as $key => $value) {
														?>
															<option value="<?php echo sanitize_title($value['title']);?>" <?php echo in_array(sanitize_title($value['title']), $sidebar_ids) ? 'selected' : '';?>><?php echo $value['title'];?></option>
														<?php
														} 
														?>
														</select>
													</td>
												</tr>
												<tr id="kingdom-right-sidebar-item" style="display: none">
													<td width="130" valign="top"><?php _e('Right Sidebar', 'kingdom');?></td>
													<td>
														<select name="right-sidebar[]" multiple>
														<?php
														foreach ($sidebars_meta as $key => $value) {
														?>
															<option value="<?php echo sanitize_title($value['title']);?>" <?php echo in_array(sanitize_title($value['title']), $sidebar_ids) ? 'selected' : '';?>><?php echo $value['title'];?></option>
														<?php
														} 
														?>
														</select>
													</td>
												</tr>
											</table>
										</div>
								<?php
									}
								?>
								</div>
							</div>
			            </div>
			        </div>
			    </div>
			</div-->
		<?php
		}
		
		public function slideshows_list()
		{
			global $post_id;
			
			$slideshows = array();
			$slideshows[] = __( 'Choose an option', 'woocommerce' ) . '&hellip;';
			$current_page_slide_id = (int)get_post_meta( $post_id, '_kd_slideshow_id', true );
			$args = array(
				'post_type' => 'slideshow',
				'posts_per_page' => '-1'
			);
			$the_slideshows = new WP_Query( $args );
			if( $the_slideshows->have_posts() ){
				foreach ($the_slideshows->posts as $slide) {
					$slide_id = $slide->ID;
					$slideshows[$slide_id] = $slide->post_title;
				}
			}
			
			return $slideshows;
		}
		
		/* when the post is saved, save the custom data */
		public function meta_box_save_postdata( $post_id ) 
		{
			global $post, $post_id;
			
			if( isset($post) ) {
				// do not save if this is an auto save routine
				if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
					return $post_id;
				
				if( in_array( $post->post_type, $this->where ) ){
					
					$layout_meta = array();
					$layout_options = $this->module_options();
					foreach ($layout_options as $option){
						foreach ($option as $box_id => $box){
							foreach ($box['elements'] as $elm_id => $element){
								$layout_meta[$elm_id] = $_POST[$elm_id];
							}
						}
					}
					
					update_post_meta( $post_id, '_layout', $layout_meta );
					
					/*
					if( (int) $_POST['kd_slideshow'] > 0 ){
						update_post_meta( $post_id, '_kd_slideshow_id', $_POST['kd_slideshow'] );
					}else{
						delete_post_meta( $post_id, '_kd_slideshow_id');
					}*/
					
					// save the sidebar
					$opts = array(
						'sidebar-position' => isset($_POST["sidebar-position"]) ? $_POST["sidebar-position"] : '',
						'left-sidebar' => isset($_POST["left-sidebar"]) ? $_POST["left-sidebar"] : '',
						'right-sidebar' => isset($_POST["right-sidebar"]) ? $_POST["right-sidebar"] : '',
					);
					
					if( $opts['sidebar-position'] == 'left' ){
						update_post_meta( $post_id, '_page_sidebar_position', 'left' );
						update_post_meta( $post_id, '_page_sidebar_ids', $opts['left-sidebar'] );
					}
					elseif( $opts['sidebar-position'] == 'right' ){
						update_post_meta( $post_id, '_page_sidebar_position', 'right' );
						update_post_meta( $post_id, '_page_sidebar_ids', $opts['right-sidebar'] );
					}
					// remove all sidebars
					else{
						update_post_meta( $post_id, '_page_sidebar_position', 'nosidebar' );
						delete_post_meta( $post_id, '_page_sidebar_ids' );
					}
				}
			}
		}
	}
}

new kingdomLayout();
