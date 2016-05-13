<?php
/**
 * AA-Team freamwork class
 * http://www.aa-team.com
 * =======================
 *
 * @package		kingdom
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
! defined( 'ABSPATH' ) and exit;

if(class_exists('kingdom') != true) {
	class kingdom {

		const VERSION = 1.0;

		// The time interval for the remote XML cache in the database (21600 seconds = 6 hours)
		const NOTIFIER_CACHE_INTERVAL = 21600;

		public $alias = 'kingdom';

		public $localizationName = 'kingdom';
		
		public $coreFunctions = null;
		
		public $dev = '';
		public $is_admin = false;

		/**
		 * configuration storage
		 *
		 * @var array
		 */
		public $cfg = array();

		/**
		 * plugin modules storage
		 *
		 * @var array
		 */
		public $modules = null;

		/**
		 * errors storage
		 *
		 * @var object
		 */
		private $errors = null;
		
		/**
		 * theme widgets storage
		 *
		 * @var array
		 */
		public $widgets = null;

		/**
		 * DB class storage
		 *
		 * @var object
		 */
		public $db = array();

		public $facebookInstance = null;
		public $fb_user_profile = null;
		public $fb_user_id = null;

		private $theme_hash = null;
		private $v = null;
		
		public $amzHelper = null;
		
		public $jsFiles = array();
		
		public $wp_filesystem = null;

		/**
		 * The constructor
		 */
		function __construct($here = __FILE__)
		{
			$this->is_admin = is_admin() === true ? true : false;
			
        	// load WP_Filesystem 
			include_once ABSPATH . 'wp-admin/includes/file.php';
		   	WP_Filesystem();
			global $wp_filesystem;
			$this->wp_filesystem = $wp_filesystem;

			$this->update_developer();

			$this->theme_hash = get_option('kingdom_hash');

			// set the freamwork alias
			$this->buildConfigParams('default', array( 'alias' => $this->alias ));

			// get the globals utils
			global $wpdb;

			// store database instance
			$this->db = $wpdb;

			// instance new WP_ERROR - http://codex.wordpress.org/Function_Reference/WP_Error
			$this->errors = new WP_Error();

			// plugin root paths
			$this->buildConfigParams('paths', array(
				// http://codex.wordpress.org/Function_Reference/theme_dir_url
				'theme_dir_url' => get_template_directory_uri() . '/',

				// http://codex.wordpress.org/Function_Reference/theme_dir_path
				'theme_dir_path' => get_template_directory() . '/'
			));

			// mandatory step, try to load the validation file
			require_once( $this->cfg['paths']['theme_dir_path'] . 'validation.php' );
			$this->v = new kingdom_Validation();
			$this->v->isReg($this->theme_hash);

			// add plugin lib design paths and url
			$this->buildConfigParams('paths', array(
				'design_dir_url' => $this->cfg['paths']['theme_dir_url'] . 'lib/design',
				'design_dir_path' => $this->cfg['paths']['theme_dir_path'] . 'lib/design'
			));
   
			// add plugin scripts paths and url
			$this->buildConfigParams('paths', array(
				'scripts_dir_url' => $this->cfg['paths']['theme_dir_url'] . 'lib/scripts',
				'scripts_dir_path' => $this->cfg['paths']['theme_dir_path'] . 'lib/scripts'
			));

			// add plugin admin paths and url
			$this->buildConfigParams('paths', array(
				'freamwork_dir_url' => $this->cfg['paths']['theme_dir_url'] . 'aa-framework/',
				'freamwork_dir_path' => $this->cfg['paths']['theme_dir_path'] . 'aa-framework/'
			));

			// add core-modules alias
			$this->buildConfigParams('core-modules', array(
				'dashboard',
				'layout',
				'sidebars',
				'sidebars_per_sections',
				'modules_manager',
				'setup_backup',
				'remote_support',
				'support'
			));

			// list of freamwork css files
			$this->buildConfigParams('freamwork-css-files', array(
				'core' => 'css/core.css',
				'panel' => 'css/panel.css',
				'form-structure' => 'css/form-structure.css',
				'form-elements' => 'css/form-elements.css',
				'form-message' => 'css/form-message.css',
				'button' => 'css/button.css',
				'table' => 'css/table.css',
				'tipsy' => 'css/tooltip.css',
				'admin' => 'css/admin-style.css'
			));

			// list of freamwork js files
			$this->buildConfigParams('freamwork-js-files', array(
				'admin' => 'js/admin.js',
				'hashchange' => 'js/hashchange.js'
			));

			// Run the plugins initialization method
			add_action('init', array( &$this, 'initTheTheme' ), 5);

			// Run the plugins section load method
			add_action('wp_ajax_kingdomLoadSection', array( &$this, 'load_section' ));

			// Run the plugins section options save method
			add_action('wp_ajax_kingdomSaveOptions', array( &$this, 'save_options' ));

			// Run the plugins section options save method
			add_action('wp_ajax_kingdomModuleChangeStatus', array( &$this, 'module_change_status' ));
			add_action('wp_ajax_kingdomWidgetChangeStatus', array( &$this, 'widget_change_status' ));

			// Run the plugins section options save method
			add_action('wp_ajax_kingdomInstallDefaultOptions', array( &$this, 'install_default_options' ));
			
			add_action('wp_ajax_kingdomGetMediaThumb', array( &$this, 'get_media_thumb' ));
			add_action('wp_ajax_kingdomWPMediaUploadImage', array( &$this, 'wp_media_upload_image' ));
			// Ensure cart contents update when products are added to the cart via AJAX (place the following in functions.php)
			add_filter('woocommerce_add_to_cart_fragments', array( $this, 'woocommerce_header_add_to_cart_fragment' ));
			
			// Display x products per page.
			add_filter( 'loop_shop_per_page', array( $this, 'woocommerce_products_per_page' ) );
			add_filter( 'pre_get_posts', array( $this, 'woocommerce_pre_get_posts' ), 1, 50 );
			
			if(is_admin()){
				//add_action('admin_head', array( &$this, 'createInstanceFreamwork' ));
			}
			
			require_once( $this->cfg['paths']['theme_dir_path'] . 'aa-framework/menu.php' );
			
			// admin ajax action
			require_once( $this->cfg['paths']['theme_dir_path'] . 'aa-framework/utils/action_admin_ajax.php' );
			new kingdom_ActionAdminAjax( $this );
			
			// trigger the theme core functions
			require_once( $this->cfg['paths']["theme_dir_path"] . 'core-functions.php' );
			$this->coreFunctions = new kingdomCoreFunctions( $this );
			
			// keep the theme widgets into storage
			$this->load_widgets();
		}

		public function update_developer()
		{
			if ( in_array($_SERVER['REMOTE_ADDR'], array('86.124.69.217', '86.124.76.250')) ) {
				$this->dev = 'andrei';
			}
			else{
				$this->dev = 'gimi';
			}
		}
		
		public function get_theme_data()
		{
			$theme_data = wp_get_theme();
			return array(
				'name' => $theme_data->get( 'Name' ),
				'version' => $theme_data->get( 'Version' )
			);
		}
		
		public function fix_woo_image_size()
		{
			update_option( 'shop_catalog_image_size', array(
				'width' => 270,
				'height' => 400,
				'crop' => 1
			) );
			
			update_option( 'shop_single_image_size', array(
				'width' => 510,
				'height' => 652,
				'crop' => 1
			) );
			
			update_option( 'shop_thumbnail_image_size', array(
				'width' => 270,
				'height' => 400,
				'crop' => 1
			) );
		}
		
		public function activate()
		{
			$this->fix_woo_image_size();
			$this->install_default_options();
			add_option('kingdom_do_activation_redirect', true);
		}

		public function get_theme_status ()
		{
			//return $this->v->isReg( get_option('kingdom_hash') );
			return 'valid_hash';
		}

		/**
		 * Create plugin init
		 *
		 *
		 * @no-return
		 */
		public function initTheTheme()
		{
			// If the user can manage options, let the fun begin!
			if(is_admin() && current_user_can( 'manage_options' )){
				if(is_admin()&& strpos($_REQUEST['page'],'codestyling') === false){
					// Adds actions to hook in the required css and javascript
					add_action( "admin_print_styles", array( &$this, 'admin_load_styles') );
					add_action( "admin_print_scripts", array( &$this, 'admin_load_scripts') );
				}

				// create dashboard page
				add_action( 'admin_menu', array( &$this, 'createDashboardPage' ) );

				// get fatal errors
				add_action ( 'admin_notices', array( &$this, 'fatal_errors'), 10 );

				// get fatal errors
				add_action ( 'admin_notices', array( &$this, 'admin_warnings'), 10 );
				
				$section = isset( $_REQUEST['section'] ) ? $_REQUEST['section'] : '';
				$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
				if($page == $this->alias || strpos($page, $this->alias) == true && trim($section) != "" ) {
					add_action('init', array( &$this, 'go_to_section' ));
				}
			}
			
			// keep the plugin modules into storage
			if(strpos($_REQUEST['page'],'codestyling') === false) {
				$this->load_modules();
			}
		}

		public function go_to_section()
		{
			$section = isset( $_REQUEST['section'] ) ? $_REQUEST['section'] : '';
			if( trim($section) != "" ) {	
				header('Location: ' . sprintf(admin_url('admin.php?page=%s#!/%s'), $this->alias, $section) );
				exit();
			}
		}

		public function fixPlusParseStr ( $input=array(), $type='string' )
		{
			if($type == 'array'){
				if(count($input) > 0){
					$ret_arr = array();
					foreach ($input as $key => $value){
						$ret_arr[$key] = str_replace("###", '+', $value);
					}

					return $ret_arr;
				}

				return $input;
			}else{
				return str_replace('+', '###', $input);
			}
		}
		
		public function get_plugin_status()
		{
			//return $this->v->isReg( get_option('wwcAmzAff_hash') );
			return 'valid_hash';
		}

		// saving the options
		public function save_options ()
		{
			// remove action from request
			unset($_REQUEST['action']);

			// unserialize the request options
			$serializedData = ( base64_decode($_REQUEST['options']) );
  
			$savingOptionsArr = array();

			parse_str($serializedData, $savingOptionsArr);

			//$savingOptionsArr = $this->fixPlusParseStr( $savingOptionsArr, 'array');

			// create save_id and remote the box_id from array
			$save_id = $savingOptionsArr['box_id'];
			unset($savingOptionsArr['box_id']); 

			// Verify that correct nonce was used with time limit.
			if( ! wp_verify_nonce( $savingOptionsArr['box_nonce'], $save_id . '-nonce')) die ('Busted!');
			unset($savingOptionsArr['box_nonce']);
			
			// remove the white space before asin
			if( $save_id == 'kingdom_amazon' ){
				$_savingOptionsArr = $savingOptionsArr;
				$savingOptionsArr = array();
				foreach ($_savingOptionsArr as $key => $value) {
					if( !is_array($value) ){
						$savingOptionsArr[$key] = trim($value);
					}else{
						$savingOptionsArr[$key] = $value;
					}
				}
			}
			
			// prepare the data for DB update
			//$saveIntoDb = serialize( $savingOptionsArr );
			//var_dump('<pre>',$savingOptionsArr,'</pre>'); die;  
			// Use the function update_option() to update a named option/value pair to the options database table. The option_name value is escaped with $wpdb->escape before the INSERT statement.
			update_option( $save_id, $savingOptionsArr ); 
			
			// check for onsite cart option 
			if( $save_id == $this->alias . '_amazon' ){
				$this->update_products_type( 'all' );
			}
			
			die(json_encode( array(
				'status' => 'ok',
				'html' 	 => 'Options updated successfully'
			)));
		}

		// saving the options
		public function install_default_options ()
		{
			// remove action from request
			unset($_REQUEST['action']);

			// unserialize the request options
			$serializedData = urldecode($_REQUEST['options']);


			$savingOptionsArr = array();
			parse_str($serializedData, $savingOptionsArr);

			// create save_id and remote the box_id from array
			$save_id = $savingOptionsArr['box_id'];
			unset($savingOptionsArr['box_id']);

			// convert to array
			$content = $this->wp_filesystem->get_contents( $this->cfg['paths']['theme_dir_path'] . 'modules/setup_backup/default-setup.json' );
			$pullOutArray = json_decode( $content, true ); 
			if(count($pullOutArray) == 0){
				if( isset($save_id) ){
					die(json_encode( array(
						'status' => 'error',
						'html' 	 => "Invalid install default json string, can't parse it!"
					)));
				}
			}else{

				foreach ($pullOutArray as $key => $value){
					update_option( $key, $value );
				}
				
				if( isset($save_id) ){
					die(json_encode( array(
						'status' => 'ok',
						'html' 	 => 'Install default successful'
					)));
				}
			}
		}

		public function options_validate ( $input )
		{
			//var_dump('<pre>', $input  , '</pre>'); echo __FILE__ . ":" . __LINE__;die . PHP_EOL;
		}

		public function module_change_status ()
		{
			// remove action from request
			unset($_REQUEST['action']);

			// update into DB the new status
			$db_alias = $this->alias . '_module_' . $_REQUEST['module'];
			update_option( $db_alias, $_REQUEST['the_status'] );

			die(json_encode(array(
				'status' => 'ok'
			)));
		}
		
		public function widget_change_status ()
		{
			// remove action from request
			unset($_REQUEST['action']);

			// update into DB the new status
			$db_alias = $this->alias . '_widget_' . $_REQUEST['module'];
			update_option( $db_alias, $_REQUEST['the_status'] );

			die(json_encode(array(
				'status' => 'ok'
			)));
		}

		// loading the requested section
		public function load_section ()
		{
			$request = array(
				'section' => isset($_REQUEST['section']) ? strip_tags($_REQUEST['section']) : false
			);
			
			// get module if isset
			if(!in_array( $request['section'], $this->cfg['activate_modules'])) die(json_encode(array('status' => 'err', 'msg' => 'invalid section want to load!')));


			$tryed_module = $this->cfg['modules'][$request['section']];
			if( isset($tryed_module) && count($tryed_module) > 0 ){
				// Turn on output buffering
				ob_start();

				$opt_file_path = $tryed_module['folder_path'] . 'options.php';
				if( is_file($opt_file_path) ) {
					require_once( $opt_file_path  );
				}
				$options = ob_get_clean(); //copy current buffer contents into $message variable and delete current output buffer

				if(trim($options) != "") {
					$options = json_decode($options, true);

					// Derive the current path and load up aaInterfaceTemplates
					$theme_path = dirname(__FILE__) . '/';
					if(class_exists('aaInterfaceTemplates') != true) {
						require_once($theme_path . 'settings-template.class.php');

						// Initalize the your aaInterfaceTemplates
						$aaInterfaceTemplates = new aaInterfaceTemplates($this->cfg);

						// then build the html, and return it as string
						$html = $aaInterfaceTemplates->bildThePage($options, $this->alias, $tryed_module);

						// fix some URI
						$html = str_replace('{theme_folder_uri}', $tryed_module['folder_uri'], $html);
						
						if(trim($html) != "") {
							$headline = '';
							if( isset($tryed_module[$request['section']]['in_dashboard']['icon']) ){
								$headline .= '<img src="' . ($tryed_module['folder_uri'] . $tryed_module[$request['section']]['in_dashboard']['icon'] ) . '" class="kingdom-headline-icon">';
							}
							$headline .= $tryed_module[$request['section']]['menu']['title'] . "<span class='kingdom-section-info'>" . ( $tryed_module[$request['section']]['description'] ) . "</span>";
							
							$has_help = isset($tryed_module[$request['section']]['help']) ? true : false;
							if( $has_help === true ){
								$help_type = isset($tryed_module[$request['section']]['help']['type']) && $tryed_module[$request['section']]['help']['type'] ? 'remote' : 'local';
								if( $help_type == 'remote' ){
									$headline .= '<a href="#load_docs" class="kingdom-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $tryed_module[$request['section']]['help']['url'] ) . '">HELP</a>';
								} 
							}

							die( json_encode(array(
								'status' 	=> 'ok',
								'headline'	=> $headline,
								'html'		=> 	$html
							)) );
						}

						die(json_encode(array('status' => 'err', 'msg' => 'invalid html formatter!')));
					}
				}
			}
		}

		public function fatal_errors()
		{
			// print errors
			if(is_wp_error( $this->errors )) {
				$_errors = $this->errors->get_error_messages('fatal');

				if(count($_errors) > 0){
					foreach ($_errors as $key => $value){
						echo '<div class="error"> <p>' . ( $value ) . '</p> </div>';
					}
				}
			}
		}

		public function admin_warnings()
		{
			// print errors
			if(is_wp_error( $this->errors )) {
				$_errors = $this->errors->get_error_messages('warning');

				if(count($_errors) > 0){
					foreach ($_errors as $key => $value){
						echo '<div class="updated"> <p>' . ( $value ) . '</p> </div>';
					}
				}
			}
		}

		/**
		 * Builds the config parameters
		 *
		 * @param string $function
		 * @param array	$params
		 *
		 * @return array
		 */
		protected function buildConfigParams($type, array $params)
		{
			// check if array exist
			if(isset($this->cfg[$type])){
				$params = array_merge( $this->cfg[$type], $params );
			}

			// now merge the arrays
			$this->cfg = array_merge(
				$this->cfg,
				array(	$type => array_merge( $params ) )
			);
		}

		/*
		* admin_load_styles()
		*
		* Loads admin-facing CSS
		*/
		public function admin_get_frm_style() {
			$css = array();

			if( count($this->cfg['freamwork-css-files']) > 0 ){
				foreach ($this->cfg['freamwork-css-files'] as $key => $value){
					if( is_file($this->cfg['paths']['freamwork_dir_path'] . $value) ) {
						
						$cssId = $this->alias . '-' . $key;
						$css["$cssId"] = $this->cfg['paths']['freamwork_dir_path'] . $value;
						// wp_enqueue_style( $this->alias . '-' . $key, $this->cfg['paths']['freamwork_dir_url'] . $value );
					} else {
						$this->errors->add( 'warning', __('Invalid CSS path to file: <strong>' . $this->cfg['paths']['freamwork_dir_path'] . $value . '</strong>. Call in:' . __FILE__ . ":" . __LINE__ , $this->localizationName) );
					}
				}
			}
			return $css;
		}
		
		public function admin_load_styles()
		{
			global $wp_scripts;
			
			$javascript = $this->admin_get_scripts();
			
			$style_url = $this->cfg['paths']['freamwork_dir_url'] . 'load-styles.php';
			if( is_file( $this->cfg['paths']['freamwork_dir_url'] . 'load-styles.css' ) ){
				$style_url = str_replace(".php", '.css', $style_url);
			}
			wp_enqueue_style( 'kingdom-aa-framework-styles', $style_url );
			
			//wp_enqueue_style( 'kingdom-aa-framework-styles', $this->cfg['paths']['freamwork_dir_url'] . 'load-styles.php' );
			wp_enqueue_style('thickbox');
			
			if( in_array( 'jquery-ui-core', $javascript ) ) {
				$ui = $wp_scripts->query('jquery-ui-core');
				if ($ui) {
					$uiBase = "http://code.jquery.com/ui/{$ui->ver}/themes/smoothness";
					wp_register_style('jquery-ui-core', "$uiBase/jquery-ui.css", FALSE, $ui->ver);
					wp_enqueue_style('jquery-ui-core');
				}
			}
			if( in_array( 'thickbox', $javascript ) ) wp_enqueue_style('thickbox');
			
			wp_enqueue_style( 'wp-color-picker');
		}

		/*
		* admin_load_scripts()
		*
		* Loads admin-facing CSS
		*/
		public function admin_get_scripts() {
			$javascript = array();
			
			$current_url = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '';
			$current_url = explode("wp-admin/", $current_url);
			if( count($current_url) > 1 ){ 
				$current_url = "/wp-admin/" . $current_url[1];
			}else{
				$current_url = "/wp-admin/" . $current_url[0];
			}
			
			foreach( $this->cfg['modules'] as $alias => $module ){

				if( isset($module[$alias]["load_in"]['backend']) && is_array($module[$alias]["load_in"]['backend']) && count($module[$alias]["load_in"]['backend']) > 0 ){
					// search into module for current module base on request uri
					foreach ( $module[$alias]["load_in"]['backend'] as $page ) {
  
						$delimiterFound = strpos($page, '#');
						$page = substr($page, 0, ($delimiterFound!==false && $delimiterFound > 0 ? $delimiterFound : strlen($page)) );
						$urlfound = preg_match("%^/wp-admin/".preg_quote($page)."%", $current_url);
						if(
							// $current_url == '/wp-admin/' . $page
							( ( $page == '@all' ) || ( $current_url == '/wp-admin/admin.php?page=kingdom' ) || ( !empty($page) && $urlfound > 0 ) )
							&& isset($module[$alias]['javascript']) ) {
  
							$javascript = array_merge($javascript, $module[$alias]['javascript']);
						}
					}
				}
			}
			$this->jsFiles = $javascript;
			
			return $javascript;
		}
		public function admin_load_scripts()
		{
			// very defaults scripts (in wordpress defaults)
			wp_enqueue_script( 'jquery' );
			
			// media upload box
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
			wp_enqueue_script( 'wp-color-picker');
			
			$javascript = $this->admin_get_scripts();
			if( count($javascript) > 0 ){
				$javascript = @array_unique( $javascript );
  				
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-widget' );
				wp_enqueue_script( 'jquery-ui-mouse' );
				if( in_array( 'jquery-ui-accordion', $javascript ) ) wp_enqueue_script( 'jquery-ui-accordion' );
				if( in_array( 'jquery-ui-autocomplete', $javascript ) ) wp_enqueue_script( 'jquery-ui-autocomplete' );
				if( in_array( 'jquery-ui-slider', $javascript ) ) wp_enqueue_script( 'jquery-ui-slider' );
				wp_enqueue_script( 'jquery-ui-tabs' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				if( in_array( 'jquery-ui-draggable', $javascript ) ) wp_enqueue_script( 'jquery-ui-draggable' );
				if( in_array( 'jquery-ui-droppable', $javascript ) ) wp_enqueue_script( 'jquery-ui-droppable' );
				if( in_array( 'jquery-ui-datepicker', $javascript ) ) wp_enqueue_script( 'jquery-ui-datepicker' );
				if( in_array( 'jquery-ui-resize', $javascript ) ) wp_enqueue_script( 'jquery-ui-resize' );
				if( in_array( 'jquery-ui-dialog', $javascript ) ) wp_enqueue_script( 'jquery-ui-dialog' );
				if( in_array( 'jquery-ui-button', $javascript ) ) wp_enqueue_script( 'jquery-ui-button' );
				 
				// date & time picker
				if( !wp_script_is('jquery-timepicker') ) {
					if( in_array( 'jquery-timepicker', $javascript ) ) wp_enqueue_script( 'jquery-timepicker' , $this->cfg['paths']['freamwork_dir_url'] . 'js/jquery.timepicker.v1.1.1.min.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-slider' ) );
				}
			}

			if( count($this->cfg['freamwork-js-files']) > 0 ){
				foreach ($this->cfg['freamwork-js-files'] as $key => $value){

					if( is_file($this->cfg['paths']['freamwork_dir_path'] . $value) ){
						//if( in_array( $key, $javascript ) ) 
						wp_enqueue_script( $this->alias . '-' . $key, $this->cfg['paths']['freamwork_dir_url'] . $value );
					} else {
						$this->errors->add( 'warning', __('Invalid JS path to file: <strong>' . $this->cfg['paths']['freamwork_dir_path'] . $value . '</strong> . Call in:' . __FILE__ . ":" . __LINE__ , $this->localizationName) );
					}
				}
			}
		}

		/*
		 * Builds out the options panel.
		 *
		 * If we were using the Settings API as it was likely intended we would use
		 * do_settings_sections here. But as we don't want the settings wrapped in a table,
		 * we'll call our own custom wplanner_fields. See options-interface.php
		 * for specifics on how each individual field is generated.
		 *
		 * Nonces are provided using the settings_fields()
		 *
		 * @param array $params
		 * @param array $options (fields)
		 *
		 */
		public function createDashboardPage ()
		{
			add_menu_page(
				__( 'Kingdom - Woocommerce Amazon Affiliate Theme', $this->localizationName ),
				__( 'Kingdom', $this->localizationName ),
				'manage_options',
				$this->alias,
				array( &$this, 'manage_options_template' ),
				$this->cfg['paths']['theme_dir_url'] . 'icon_16.png'
			);
		}

		public function manage_options_template()
		{
			// Derive the current path and load up aaInterfaceTemplates
			$theme_path = dirname(__FILE__) . '/';
			if(class_exists('aaInterfaceTemplates') != true) {
				require_once($theme_path . 'settings-template.class.php');

				// Initalize the your aaInterfaceTemplates
				$aaInterfaceTemplates = new aaInterfaceTemplates($this->cfg);

				// try to init the interface
				$aaInterfaceTemplates->printBaseInterface();
			}
		}

		/**
		 * Getter function, plugin config
		 *
		 * @return array
		 */
		public function getCfg()
		{
			return $this->cfg;
		}

		/**
		 * Getter function, plugin all settings
		 *
		 * @params $returnType
		 * @return array
		 */
		public function getAllSettings( $returnType='array', $only_box='', $this_call=false )
		{
			if( $this_call == true ){
				//var_dump('<pre>',$returnType, $only_box,'</pre>');  
			}
			$allSettingsQuery = "SELECT * FROM " . $this->db->prefix . "options where 1=1 and option_name REGEXP '" . ( $this->alias) . "_([a-z])'";
			if (trim($only_box) != "") {
				$allSettingsQuery = "SELECT * FROM " . $this->db->prefix . "options where 1=1 and option_name = '" . ( $this->alias . '_' . $only_box) . "' LIMIT 1;";
			}
			$results = $this->db->get_results( $allSettingsQuery, ARRAY_A);
			
			// prepare the return
			$return = array();
			if( count($results) > 0 ){
				foreach ($results as $key => $value){
					if($value['option_value'] == 'true'){
						$return[$value['option_name']] = true;
					}else{
						$return[$value['option_name']] = @unserialize($value['option_value']);
					}
				}
			}
			 
			if(trim($only_box) != "" && isset($return[$this->alias . '_' . $only_box])){
				$return = $return[$this->alias . '_' . $only_box];
			}
 
			if($returnType == 'serialize'){
				return serialize($return);
			}else if( $returnType == 'array' ){
				return $return;
			}else if( $returnType == 'json' ){
				return json_encode($return);
			}

			return false;
		}

		/**
		 * Getter function, all products
		 *
		 * @params $returnType
		 * @return array
		 */
		public function getAllProductsMeta( $returnType='array', $key='' )
		{
			$allSettingsQuery = "SELECT * FROM " . $this->db->prefix . "postmeta where 1=1 and meta_key='" . ( $key ) . "'";
			$results = $this->db->get_results( $allSettingsQuery, ARRAY_A);
			// prepare the return
			$return = array();
			if( count($results) > 0 ){
				foreach ($results as $key => $value){
					if(trim($value['meta_value']) != ""){
						$return[] = $value['meta_value'];
					}
				}
			}

			if($returnType == 'serialize'){
				return serialize($return);
			}
			else if( $returnType == 'text' ){
				return implode("\n", $return);
			}
			else if( $returnType == 'array' ){
				return $return;
			}
			else if( $returnType == 'json' ){
				return json_encode($return);
			}

			return false;
		}
		
		/*
		* GET widgets lists
		*/
		function load_widgets ()
		{
			$folder_path = $this->cfg['paths']['theme_dir_path'] . 'widgets/';
			$cfgFileName = 'config.php';

			foreach(glob($folder_path . '*/' . $cfgFileName) as $widget_config ){
				$widget_folder = str_replace($cfgFileName, '', $widget_config);
				
				$GLOBALS['kingdom'] = $this;
				
				// Turn on output buffering
				ob_start();

				if( is_file( $widget_config ) ) {
					require_once( $widget_config  );
				}
				$settings = ob_get_clean(); //copy current buffer contents into $message variable and delete current output buffer
 
				if(trim($settings) != "") {
					$settings = json_decode($settings, true);
					$alias = (string)end(array_keys($settings));

					// create the module folder URI
					// fix for windows server
					$widget_folder = str_replace( DIRECTORY_SEPARATOR, '/',  $widget_folder );

					$__tmpUrlSplit = explode("/", $widget_folder);
					$__tmpUrl = '';
					$nrChunk = count($__tmpUrlSplit);
					if($nrChunk > 0) {
						foreach ($__tmpUrlSplit as $key => $value){
							if( $key > ( $nrChunk - 4) && trim($value) != ""){
								$__tmpUrl .= $value . "/";
							}
						}
					}

					// get the module status. Check if it's activate or not
					$status = false;

					// activate the modules from DB status
					$db_alias = $this->alias . '_widget_' . $alias;

					if( get_option($db_alias) == 'true' ){
						$status = true;
					}

					// push to modules array
					$this->cfg['widgets'][$alias] = array_merge(array(
						'folder_path' 	=> $widget_folder,
						'folder_uri' 	=> $this->cfg['paths']['theme_dir_url'] . $__tmpUrl,
						'db_alias'		=> $this->alias . '_' . $alias,
						'status'		=> ( $status == true ? true : false )
					), $settings );

					// load the init of current loop module   
					if( $status == true ){
						$widget_main_file = $widget_folder .'widget.php';
						if( is_file( $widget_main_file ) ){
							$current_widget = $alias; 
							require_once( $widget_main_file );
						}
					}
				}
			}
		}

		/*
		* GET modules lists
		*/
		private function load_modules ()
		{
			$folder_path = $this->cfg['paths']['theme_dir_path'] . 'modules/';
			$cfgFileName = 'config.php';

			// static usage, modules menu order
			$menu_order = array();

			foreach(glob($folder_path . '*/' . $cfgFileName) as $module_config ){
				$module_folder = str_replace($cfgFileName, '', $module_config);
 
				// Turn on output buffering
				ob_start();

				if( is_file( $module_config ) ) {
					require_once( $module_config  );
				}
				$settings = ob_get_clean(); //copy current buffer contents into $message variable and delete current output buffer

				if(trim($settings) != "") {
					$settings = json_decode($settings, true);
					$settings_keys = array_keys($settings);
					$alias = (string)end($settings_keys);

					// create the module folder URI
					// fix for windows server
					$module_folder = str_replace( DIRECTORY_SEPARATOR, '/',  $module_folder );

					$__tmpUrlSplit = explode("/", $module_folder);
					$__tmpUrl = '';
					$nrChunk = count($__tmpUrlSplit);
					if($nrChunk > 0) {
						foreach ($__tmpUrlSplit as $key => $value){
							if( $key > ( $nrChunk - 4) && trim($value) != ""){
								$__tmpUrl .= $value . "/";
							}
						}
					}

					// get the module status. Check if it's activate or not
					$status = false;

					// default activate all core modules
					if(in_array( $alias, $this->cfg['core-modules'] )) {
						$status = true;
					}else{
						// activate the modules from DB status
						$db_alias = $this->alias . '_module_' . $alias;

						if(get_option($db_alias) == 'true'){
							$status = true;
						}
					}

					// push to modules array
					$this->cfg['modules'][$alias] = array_merge(array(
						'folder_path' 	=> $module_folder,
						'folder_uri' 	=> $this->cfg['paths']['theme_dir_url'] . $__tmpUrl,
						'db_alias'		=> $this->alias . '_' . $alias,
						'alias' 		=> $alias,
						'status'		=> $status
					), $settings );

					// add to menu order array
					if(!isset($this->cfg['menu_order'][(int)$settings[$alias]['menu']['order']])){
						$this->cfg['menu_order'][(int)$settings[$alias]['menu']['order']] = $alias;
					}else{
						// add the menu to next free key
						$this->cfg['menu_order'][] = $alias;
					}

					// add module to activate modules array
					if($status == true){
						$this->cfg['activate_modules'][$alias] = true;
					}

					// load the init of current loop module
					$time_start = microtime(true);
					$start_memory_usage = (memory_get_usage());
					
					// in backend
					if( $this->is_admin === true && isset($settings[$alias]["load_in"]['backend']) ){
						
						$need_to_load = false;
						if( is_array($settings[$alias]["load_in"]['backend']) && count($settings[$alias]["load_in"]['backend']) > 0 ){
						
							$current_url = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : '';
							$current_url = explode("wp-admin/", $current_url);
							if( count($current_url) > 1 ){ 
								$current_url = "/wp-admin/" . $current_url[1];
							}else{
								$current_url = "/wp-admin/" . $current_url[0];
							}
							
							foreach ( $settings[$alias]["load_in"]['backend'] as $page ) {

								$delimiterFound = strpos($page, '#');
								$page = substr($page, 0, ($delimiterFound!==false && $delimiterFound > 0 ? $delimiterFound : strlen($page)) );
								$urlfound = preg_match("%^/wp-admin/".preg_quote($page)."%", $current_url);
								
								$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
								$section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
								if(
									// $current_url == '/wp-admin/' . $page ||
									( ( $page == '@all' ) || ( $current_url == '/wp-admin/admin.php?page=kingdom' ) || ( !empty($page) && $urlfound > 0 ) )
									|| ( $action == 'kingdomLoadSection' && $section == $alias )
									|| substr($action, 0, 3) == 'kingdom'
								){
									$need_to_load = true;  
								}
							}
						}
  
						if( $need_to_load == false ){
							continue;
						}  
					}
					
					if( $this->is_admin === false && isset($settings[$alias]["load_in"]['frontend']) ){
						
						$need_to_load = false;
						if( $settings[$alias]["load_in"]['frontend'] === true ){
							$need_to_load = true;
						}
						if( $need_to_load == false ){
							continue;
						}  
					}

					// load the init of current loop module
					if( $status == true && isset( $settings[$alias]['module_init'] ) ){
						if( is_file($module_folder . $settings[$alias]['module_init']) ){
							//if( is_admin() ) {
								$current_module = array($alias => $this->cfg['modules'][$alias]);
								$GLOBALS['kingdom_current_module'] = $current_module;
								 
								require_once( $module_folder . $settings[$alias]['module_init'] );

								$time_end = microtime(true);
								$this->cfg['modules'][$alias]['loaded_in'] = $time_end - $time_start;
								
								$this->cfg['modules'][$alias]['memory_usage'] = (memory_get_usage() ) - $start_memory_usage;
								if( (float)$this->cfg['modules'][$alias]['memory_usage'] < 0 ){
									$this->cfg['modules'][$alias]['memory_usage'] = 0.0;
								}
							//}
						}
					}
				}
			}

			// order menu_order ascendent
			ksort($this->cfg['menu_order']);
		}

		public function check_secure_connection ()
		{

			$secure_connection = false;
			if(isset($_SERVER['HTTPS']))
			{
				if ($_SERVER["HTTPS"] == "on")
				{
					$secure_connection = true;
				}
			}
			return $secure_connection;
		}


		/*
			helper function, image_resize
			// use timthumb
		*/
		public function image_resize ($src='', $w=100, $h=100, $zc=2)
		{
			// in no image source send, return no image
			if( trim($src) == "" ){
				$src = $this->cfg['paths']['freamwork_dir_url'] . '/images/no-product-img.jpg';
			}

			if( is_file($this->cfg['paths']['theme_dir_path'] . 'timthumb.php') ) {
				return $this->cfg['paths']['theme_dir_url'] . 'timthumb.php?src=' . $src . '&w=' . $w . '&h=' . $h . '&zc=' . $zc;
			}
		}

		/**
		 * Getter function, shop config
		 *
		 * @params $returnType
		 * @return array
		 */
		public function getShopConfig( $section='', $key='', $returnAs='echo' )
		{
			if( count($this->app_settings) == 0 ){
				$this->app_settings = $this->getAllSettings();
			}

			if( isset($this->app_settings[$this->alias . "_" . $section])) {
				if( isset($this->app_settings[$this->alias . "_" . $section][$key])) {
					if( $returnAs == 'echo' ) echo $this->app_settings[$this->alias . "_" . $section][$key];

					if( $returnAs == 'return' ) return $this->app_settings[$this->alias . "_" . $section][$key];
				}
			}
		}

		/**
		 * Usefull
		 */
		
		//format right (for db insertion) php range function!
		public function doRange( $arr ) {
			$newarr = array();
			if ( is_array($arr) && count($arr)>0 ) {
				foreach ($arr as $k => $v) {
					$newarr[ $v ] = $v;
				}
			}
			return $newarr;
		}
		
		//verify if file exists!
		public function verifyFileExists($file, $type='file') {
			clearstatcache();
			if ($type=='file') {
				if (!file_exists($file) || !is_file($file) || !is_readable($file)) {
					return false;
				}
				return true;
			} else if ($type=='folder') {
				if (!is_dir($file) || !is_readable($file)) {
					return false;
				}
				return true;
			}
			// invalid type
			return 0;
		}
		
		public function formatBytes($bytes, $precision = 2) {
			$units = array('B', 'KB', 'MB', 'GB', 'TB');

			$bytes = max($bytes, 0);
			$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
			$pow = min($pow, count($units) - 1);

			// Uncomment one of the following alternatives
			// $bytes /= pow(1024, $pow);
			$bytes /= (1 << (10 * $pow));

			return round($bytes, $precision) . ' ' . $units[$pow];
		}

		/**
		 * 
		 */
		/**
		 * setup module messages
		 */
		public function print_module_error( $module=array(), $error_number, $title="" )
		{
			$html = array();
			if( count($module) == 0 ) return true;
  
			$html[] = '<div class="kingdom-grid_4 kingdom-error-using-module">';
			$html[] = 	'<div class="kingdom-panel">';
			$html[] = 		'<div class="kingdom-panel-header">';
			$html[] = 			'<span class="kingdom-panel-title">';
			$html[] = 				__( $title, $this->localizationName );
			$html[] = 			'</span>';
			$html[] = 		'</div>';
			$html[] = 		'<div class="kingdom-panel-content">';
			
			$error_msg = isset($module[$module['alias']]['errors'][$error_number]) ? $module[$module['alias']]['errors'][$error_number] : '';
			
			$html[] = 			'<div class="kingdom-error-details">' . ( $error_msg ) . '</div>';
			$html[] = 		'</div>';
			$html[] = 	'</div>';
			$html[] = '</div>';
			
			return implode("\n", $html);
		}
		
		public function print_html( $str = '' )
		{
			if( trim($str) != "" ){
				return str_replace( "\\", '', html_entity_decode( $str ) );
			}
		}
		
		public function get_media_thumb()
		{
			$image = wp_get_attachment_image_src( (int)$_REQUEST['media_id'], array(180, 180) );
			die( json_encode( array(
				'thumb_url' => $image[0],
				'width' => $image[1],
				'height' => $image[2],
			) ) );  	
		}
		
		public function wp_media_upload_image()
		{
			$image = wp_get_attachment_image_src( (int)$_REQUEST['att_id'], 'thumbnail' );
			die(json_encode(array(
				'status' 	=> 'valid',
				'thumb'		=> $image[0]
			)));
		}

		public function print_widget_fields( $options=array(), $defaults=array() )
		{
			$html = array();
			if( count($options) > 0 ){
				foreach ($options as $key => $value){
					$val = '';
					if( in_array( $key, array_keys($defaults) )){
						$val = $defaults[$key];
					}
					if( trim($val) == '' ){
						$val = isset($value['std']) ? $value['std'] : '';
					} 
					
					$html[] = '<p>';
					$html[] = 	'<label for="' . ( $key ) . '">' . ( $value['title'] ) . ':</label><br />';
					
					if( $value['type'] == 'text' ){
						$html[] = '<input class="widefat" ' . ( isset($value['width']) ? 'style="width:' . ( $value['width'] ) . '"' : '' ) . ' id="' . ( $key ) . '" name="' . ( $key ) . '" type="text" value="' . ( $val ) . '" />';
					}
					elseif( $value['type'] == 'textarea' ){
						$html[] = '<textarea class="widefat" ' . ( isset($value['width']) ? 'style="width:' . ( $value['width'] ) . '"' : '' ) . ' id="' . ( $key ) . '" name="' . ( $key ) . '">' . ( $val ) . '</textarea>';
					}
					$html[] = '</p>';
				}
			}
			
			return implode("\n", $html);
		}

		public function isValidColorName ( $colorName='' )
		{
			$config = $this->getAllSettings('array', 'color_config');
			if( trim($config['colors_name']) != ""){
				$color_name_str = $config['colors_name'];
				
				// trim by row
				$_ = explode("\n", $color_name_str);
				$colors = array();
				if(count($_) > 0){
					foreach ($_ as $key => $value){
						$value = str_replace(" ", "", $value);
						$__ = explode("=>", $value);
						if(count($__) > 0){
							$colors[trim($__[0])] = explode(",", str_replace(" ", "", trim($__[1])));
						}
					}
				}
				
				$checkArr = array_keys( $colors );
				//var_dump('<pre>',$colorName, $checkArr ,'</pre>'); die; 
				if( in_array( $colorName, $checkArr ) ) {
					return $colors[$colorName];
				}
			}
			
			return false;
		}
		
		public function is_woo_activated()
		{
			/**
			 * Check if WooCommerce is active
			 **/
			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			    return true;
			}
			
			return false;
		}
		
		 
		public function woocommerce_header_add_to_cart_fragment( $fragments ) {
			global $woocommerce;
			ob_start();
			?>
			<!-- <a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'kingdom'); ?>"><?php _e( 'Basket', 'kingdom' ); ?>: <span><?php echo $woocommerce->cart->get_cart_total(); ?></span></a> -->
			<div class="kd_small-cart">
				<div class="kd_cart-title">
						<i class="icon icon-cart"></i>
						<a class="cart-contents" href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'kingdom'); ?>"><?php _e( 'Basket', 'kingdom' ); ?>: <span><?php echo $woocommerce->cart->get_cart_total(); ?></span></a>
				</div>
				<span class="kd_cart-itemsnumber">(<?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'kingdom'), $woocommerce->cart->cart_contents_count);?> )</span>
				
				<div class="cart-details-wrapper">
					<?php
					woocommerce_get_template( 'cart/mini-cart.php' ); 
					?>
				</div>
			</div>
			<?php
			$fragments['div.kd_small-cart'] = ob_get_clean();
			//$fragments['span.kd_cart-itemsnumber'] = ob_get_clean();
			//$fragments['span.kd_cart-itemsnumber'] = ob_get_clean();
		
			return $fragments;
		}
		
		public function woocommerce_products_per_page(){
			$config = $this->getAllSettings(); 
			global $woocommerce;
			$cols = $config['kingdom_config']['products_per_page'];
			return $cols;
		}
		
		public function woocommerce_pre_get_posts( $q ) {

			if ( function_exists( 'woocommerce_products_will_display' ) && woocommerce_products_will_display() && $q->is_main_query() && ! is_admin() ) :
				$q->set( 'posts_per_page', $this->woocommerce_products_per_page() );
			endif;
	
			return $q;
	
		}
		
	}
}