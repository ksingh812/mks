<?php 
/**
 * kingdomSidebars class
 * ================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 * @access 		public
 * @return 		void
 */  
!defined('ABSPATH') and exit;
if (class_exists('kingdomSidebars') != true) {
    class kingdomSidebars
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
			$this->module_folder = $this->the_theme->cfg['paths']['theme_dir_url'] . 'modules/sidebars/';
			
			add_action('kingdom_widgets_admin_panel', array($this, 'add_widgets_box_admin'));
			
			add_action('wp_ajax_kingdomSaveSidebars', array( &$this, 'save_sidebars' ));
        }

		/**
	    * Singleton pattern
	    *
	    * @return kingdomSidebars Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }

		
		public function add_to_menu_metabox()
		{
			$post_types = get_post_types(); 
			$exclude_post_types = array(
				'partners',
			);
			foreach ($post_types as $key => $value) {
				if( in_array($value, $exclude_post_types)) {
					continue;
				}
				// add meta box to all selected post types
				add_meta_box(
					$this->the_theme->alias . '_sidebar', 
					__('Page Sidebar', 'kingdom'), 
					array($this, 'page_sidebar_box'), 
					$value, 
					'side',
					'high'
				);
			}
		}
		
		/* when the post is saved, save the custom data */
		public function meta_box_save_postdata( $post_id ) 
		{
			global $post;
			
			if( isset($post) ) {
				// do not save if this is an auto save routine
				if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
					return $post_id;
				
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

		public function page_sidebar_box()
		{
			global $post_id;
			if( $post_id == 0 ) $post_id = isset($_REQUEST['post']) ? $_REQUEST['post'] : 0;
			 
			$sidebars_meta = get_option( 'kingdom_dynamic_sidebars' );
			if( $sidebars_meta !== false && count($sidebars_meta) > 0 && isset($sidebars_meta['sidebar']) ){
				$sidebars_meta = $sidebars_meta['sidebar'];
				
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
				<label><?php _e('Sidebar Layout', 'kingdom');?>:</label>
				<div>
					<input type="radio" <?php echo $sidebar_position == 'left' ? 'checked' : '';?> name="sidebar-position" value="left" data-replaceimg='<?php echo $this->module_folder . 'assets/2cl.png';?>' data-tooltip="<?php _e('Left Sidebar', 'kingdom');?>">
					<input type="radio" <?php echo $sidebar_position == 'right' ? 'checked' : '';?> name="sidebar-position" value="right" data-replaceimg='<?php echo $this->module_folder . 'assets/2cr.png';?>' data-tooltip="<?php _e('Right Sidebar', 'kingdom');?>">
					<input type="radio" <?php echo $sidebar_position == 'nosidebar' ? 'checked' : '';?> name="sidebar-position" value="nosidebar" data-replaceimg='<?php echo $this->module_folder . 'assets/1col.png';?>' data-tooltip="<?php _e('NO Sidebar', 'kingdom');?>">
				</div>
			</div>
			<div id="kingdom-sidebar-items">
				<table>
					<tr id="kingdom-left-sidebar-item" style="display: none">
						<td width="210" valign="top"><?php _e('Left Sidebar', 'kingdom');?></td>
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
						<td width="210" valign="top"><?php _e('Right Sidebar', 'kingdom');?></td>
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
			<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<?php	
			}
		}
		
		public function add_widgets_box_admin()
		{
			ob_start();
			?>
			<a class="kingdom-button blue small" id="kingdom-add-new-sidebar" href="#">
				<img alt="" src="<?php echo $this->module_folder . 'assets/plus.png';?>">
				<?php _e('Add new sidebar', 'kingdom');?>
			</a>
			<div class="kingdom-form" id="kingdom-form-widget-manager">
				<div id="kingdom-template-sidebar" style="display:none;">
					<div class='kingdom-form-row'>
						<div class='kingdom-form-col-7-8'>
							<label><?php _e('Title', 'kingdom');?></label>
							<div style='clear:both;'></div>
							<input type='text' data-name='title' name='sidebar' value=''>
						</div>
						<div class='kingdom-form-col-1-8' style='position: relative;'>
							<a href='#' class='sidebar-delete-btn'><?php _e('Delete this', 'kingdom');?></a>
						</div>
					</div>
				</div>
				
				
				<?php
				// retrieve the existing value(s) for this meta field. This returns an array
				$sidebars_meta = get_option( 'kingdom_dynamic_sidebars' );
				echo '<input type="hidden" name="kingdom-sidebars-nr" id="kingdom-sidebars-nr" value="' . ( count($sidebars_meta) ) . '" />';
				?>
				<div class="kingdom-panel-content" id="kingdom-panel-content-sidebar">
				<?php 
				if( $sidebars_meta != false && count($sidebars_meta) > 0) {
					$cc = 1;
					foreach ($sidebars_meta as $key => $value){  
						echo "<div class='kingdom-form-row'>
								<div class='kingdom-form-col-7-8'>
									<label>" . __('Title', 'kingdom') . "</label>
									<input type='text' data-name='title' name='sidebar[" . ( $cc ) . "][title]' value='" . ( $value['title'] ) . "'>
								</div>
								<div class='kingdom-form-col-1-8' style='position: relative;'>
									<a href='#' class='sidebar-delete-btn'>" . __('Delete this', 'kingdom') . "</a>
								</div>
							</div>";
							
						$cc++;
					}
				} 
				
				// no sidebar yet
				else {
				?>
					<div class="kingdom-message kingdom-info" id="kingdom-sidebar-no-items"><?php _e('You need to add sidebars. You can do that by click on the <i>"Add new sidebar"</i>', 'kingdom');?> .</div>
				<?php 
				}
				?>
			</div>
			
			<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<?php
		
			$output = ob_get_contents();
			ob_end_clean();
			
			return $output;
		}

		public function save_sidebars()
		{
			$settings = isset($_REQUEST['settings']) ? $_REQUEST['settings'] : '';
			parse_str($settings, $settings_arr);
			
			if( isset($settings_arr['sidebar']) && count($settings_arr['sidebar']) ){
				
				$sidebars = array();
				foreach ($settings_arr['sidebar'] as $key => $value) {
					// check if sidebar not exists 
					if( !get_option( 'kingdom_ds_' . md5( sanitize_title($value['title']) ) ) ){
						update_option( 'kingdom_ds_' . md5( sanitize_title($value['title']) ), array(
							'title' => $value['title']
						) );
					}
				}
				
				update_option( 'kingdom_dynamic_sidebars', $settings_arr['sidebar'] );
			} 
			else{
				// !!! need to search on each kingdom_ds_% and compare with $settings_arr['sidebar'] ...
				//delete_option( 'kingdom_dynamic_sidebars' );
			}

			die( json_encode(array(
				'status' => 'valid',
				'msg' => 'Sidebars saved successfully!'
			)) );
		}
	}
}

new kingdomSidebars();