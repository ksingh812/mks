<?php
/*
Plugin Name: AA Backup Manager
Plugin URI: http://www.aa-team.com
Description: Help you backup and restore data from AA-Team Themes and plugins.
Version: 1.0
Author: Andrei D. - www.aa-team.com
Author URI: http://www.aa-team.com
*/

// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * Current Backup Manager version
 */
if ( ! defined( 'BKM_VERSION' ) ) {
	/**
	 *
	 */
	define( 'BKM_VERSION', '1.0' );
}

/**
 * Backup manager starts here. Manager sets mode, adds required wp hooks and loads required object of structure
 *
 * Manager controls and access to all modules and classes of Backup Manager.
 *
 * @package AA Backup Manager
 * @since   1.0
 */
class BKM_Manager {
	/**
	 * Set status/mode for Backup Manager.
	 *
	 * It depends on what functionality is required from Backup Manager to work with current page/part of WP.
	 *
	 * Possible values:
	 *  none - current status is unknown, default mode;
	 *  page - simple wp page;
	 *  admin_page - wp dashboard;
	 *  admin_frontend_editor - Backup Manager front end editor version;
	 *  admin_settings_page - settings page
	 *  page_editable - inline version for iframe in front end editor;
	 *
	 * @since 1.0
	 * @var string
	 */
	private $mode = 'none';
	
	/**
	 * Enables Backup Manager to act as the theme plugin.
	 *
	 * @since 1.0
	 * @var bool
	 */
	 
	private $is_as_theme = false;
	/**
	 * Backup Manager is network plugin or not.
	 * @since 1.0
	 * @var bool
	 */
	private $is_network_plugin = null;
	
	/**
	 * List of paths.
	 *
	 * @since 1.0
	 * @var array
	 */
	private $paths = array();

	/**
	 * Set updater mode
	 * @since 1.0
	 * @var bool
	 */
	private $disable_updater = false;
	
	/**
	 * Modules and objects instances list
	 * @since 1.0
	 * @var array
	 */
	private $factory = array();
	
	/**
	 * File name for components manifest file.
	 *
	 * @since 4.4
	 * @var string
	 */
	private $components_manifest = 'components.json';
	
	/**
	 * @var string
	 */
	private $plugin_name = 'aa-backup-manager/plugin.php';
	
	/**
	 * The export object
	 */
	private $export = null;
	
	/**
	 * The import object
	 */
	private $import = null;
	
	/**
	 * The about object
	 */
	private $about = null;
	
	/**
	 * The wp_filesystem object
	 */
	public $wp_filesystem = null;
	
	/**
	 * The wpbd object
	 */
	public $db = null;
	

	/**
	 * Constructor loads API functions, defines paths and adds required wp actions
	 *
	 * @since  1.0
	 */
	public function __construct() 
	{
		$dir = dirname( __FILE__ );
		$upload_dir = wp_upload_dir();
		
		/**
		 * Define path settings for Backup Manager.
		 */
		$this->setPaths( array(
			'APP_ROOT' 			=> $dir,
			'WP_ROOT' 			=> preg_replace( '/$\//', '', ABSPATH ),
			'APP_DIR' 			=> basename( $dir ),
			'CONFIG_DIR' 		=> $dir . '/config',
			'ASSETS_DIR' 		=> $dir . '/assets',
			'ASSETS_DIR_NAME' 	=> 'assets',
			'HELPERS_DIR' 		=> $dir . '/include/helpers',
			'IMPORT_DIR' 		=> $dir . '/include/import',
			'EXPORT_DIR' 		=> $dir . '/include/export',
			'ABOUT_DIR' 		=> $dir . '/include/about',
			'INCLUDE_DIR' 		=> $dir . '/include',
			'PARAMS_DIR' 		=> $dir . '/include/params',
			'VENDORS_DIR' 		=> $dir . '/include/classes/vendors',
			'UPLOAD_BASE_DIR'  	=> $upload_dir['basedir'],
			'UPLOAD_BASE_URL'  	=> $upload_dir['baseurl']
		) );

		// Load API
		require_once $this->path( 'HELPERS_DIR', 'helpers.php' );
		require_once $this->path( 'HELPERS_DIR', 'class-create-archive.php' );
		
		// Add hooks
		add_action( 'plugins_loaded', array( &$this, 'pluginsLoaded' ), 9 );
		add_action( 'init', array( &$this, 'init' ), 9 );
		
		// load WP_Filesystem 
		include_once ABSPATH . 'wp-admin/includes/file.php';
	   	WP_Filesystem();
		global $wp_filesystem;
		$this->wp_filesystem = $wp_filesystem;
	}

	/**
	 * Callback function WP plugin_loaded action hook. Loads locale
	 *
	 * @since  1.0
	 * @access public
	 */
	public function pluginsLoaded() 
	{
		// Setup locale
		do_action( 'BKM_plugins_loaded' );
		load_plugin_textdomain( 'aa-backup-manager', false, $this->path( 'APP_DIR', 'locale' ) );
	}

	/**
	 * Callback function for WP init action hook. Sets Backup Manager mode and loads required objects.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return void
	 */
	public function init() 
	{
		do_action( 'BKM_before_init' );
		
		global $wpdb;
		$this->db = $wpdb;
		 	
		// Set current mode
		$this->setMode();
		
		// Load the admin menu hook
		$this->adminInterface();
		
		/**
		 * if is admin and not frontend.
		 */
		if( $this->mode === 'admin' ) {
			// load import interface
			require_once $this->path( 'IMPORT_DIR', 'import.class.php' );
			$this->import = new BKM_import( $this );
			
			// load export interface
			require_once $this->path( 'EXPORT_DIR', 'export.class.php' );
			$this->export = new BKM_export( $this );
			
			// load about interface
			require_once $this->path( 'ABOUT_DIR', 'about.class.php' );
			$this->about = new BKM_about( $this );
		}
		
		do_action( 'BKM_after_init' );
	}

	/**
	 * Load required logic for operating in Wp Admin dashboard.
	 *
	 * @since  1.0
	 * @access protected
	 *
	 * @return void
	 */
	public function adminInterface() 
	{
		// Settings page. Adds menu page in admin panel.
		$this->addMenuPageHooks();
		
	}
	
	public function addMenuPageHooks() 
	{
		if ( current_user_can( 'manage_options' ) ) {
			add_action( 'admin_menu', array( &$this, 'addMenuPage' ) );
		}
	}
	
	public function addMenuPage() 
	{
		$page = add_menu_page( __( "Backup Manager", "BKM" ),
			__( "Backup Manager", "BKM" ),
			'manage_options',
			'BKM',
			array( &$this, 'render' ),
			BKM_asset_url( 'images/plugin-icon.png' ) 
		);
 
		add_action( "load-$page", array( &$this, 'adminLoad' ) );
	}
	
	/**
	 * Set up the enqueue for the CSS & JavaScript files.
	 *
	 */
	public function adminLoad()
	{
		wp_enqueue_style( 'BKM-install', BKM_asset_url( 'style.css' ), array(), BKM_VERSION );
		wp_enqueue_style( 'BKM-font-awesome', BKM_asset_url( 'font-awesome.min.css' ), array(), '4.3.0' ); 
		
		wp_enqueue_script( 'BKM-script', BKM_asset_url( 'app.class.js' ), array(), '1.0.0' );
	}
	
	/**
	 * Create Render points.
	 *
	 * Loaded interface depends on which page is requested by client from server and request parameters like BKM_action.
	 *
	 * @since  1.0
	 * @access protected
	 *
	 * @return void
	 */
	public function render()
	{

		$html[] = '
			<div class="BKM_iw">
				<div class="BKM_iw-loader"><ul class="BKM_iw-preloader"><li></li><li></li><li></li><li></li><li></li></ul></div>';
			
		$html[] = '<aside>';
		$html[] = 	'<div id="BKM_iw-logo"><i class="fa fa-bolt"></i></div>';
		$html[] = 	'<ul>';
		$html[] = 		'<li class="' . ( BKM_action() == 'import' ? "open" : '' ) . '"><a href="' . ( admin_url('admin.php?page=BKM&BKM_action=import') ) . '"><i class="fa fa-download"></i>Import</a></li>';
		$html[] = 		'<li class="' . ( BKM_action() == 'export' ? "open" : '' ) . '"><a href="' . ( admin_url('admin.php?page=BKM&BKM_action=export') ) . '"><i class="fa fa-upload"></i>Export</a></li>';
		$html[] = 	'</ul>';
		
		$html[] = '</aside>';
		
		$html[] = '<section>';
		if( BKM_action() == 'import' ){
				
			$html[] = 	'
				<div class="BKM_iw-header">
					<h3>AA Backup Manager / Import</h3>
					<a href="http://aa-team.com" target="_blank"><img src="' . ( BKM_asset_url( 'images/aa-logo.png' ) ) . '" class="aa-logo" /></a>
				</div>';
				
			$html[] = $this->import->print_interface();
		}
		
		else if( BKM_action() == 'export' ){
			$html[] = 	'
				<div class="BKM_iw-header">
					<h3>AA Backup Manager / Export</h3>
					<a href="http://aa-team.com" target="_blank"><img src="' . ( BKM_asset_url( 'images/aa-logo.png' ) ) . '" class="aa-logo" /></a>
				</div>';
				
			$html[] = $this->export->print_interface();
		}

		else if( BKM_action() == 'about' ){
			$html[] = 	'
				<div class="BKM_iw-header">
					<h3>AA Backup Manager / About</h3>
					<a href="http://aa-team.com" target="_blank"><img src="' . ( BKM_asset_url( 'images/aa-logo.png' ) ) . '" class="aa-logo" /></a>
				</div>';
			
			$html[] = $this->about->print_interface();
		}
		
		$html[] = '</section>';	
			
		$html[] = '</div>';
		
		echo implode( "\n", $html );
	}
	
	/**
	 * Print Backup Manager interface
	 *
	*/
	public function print_interface()
	{
		$html = array();
		
	}
	
	/**
	 * Set Backup Manager mode.
	 *
	 * Mode depends on which page is requested by client from server and request parameters like BKM_action.
	 *
	 * @since  1.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function setMode() 
	{
		if ( is_admin() ) {
			$this->mode = 'admin';
		} else {
			$this->mode = 'frontend';
		}
	}

	/**
	 * Sets version of the Backup Manager in DB as option `BKM_version`
	 *
	 * @since 1.0
	 * @access protected
	 *
	 * @return void
	 */
	protected function setVersion() {
		$version = get_option( 'BKM_version' );
		if ( ! is_string( $version ) || version_compare( $version, BKM_VERSION ) !== 0 ) {
			add_action( 'BKM_after_init', array( BKM_settings(), 'rebuild' ) );
			update_option( 'BKM_version', BKM_VERSION );
		}
	}

	/**
	 * Get current mode for Backup Manager.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return string
	 */
	public function mode() {
		return $this->mode;
	}

	/**
	 * Setter for paths
	 *
	 * @since  1.0
	 * @access protected
	 *
	 * @param $paths
	 */
	protected function setPaths( $paths ) {
		$this->paths = $paths;
	}

	/**
	 * Gets absolute path for file/directory in filesystem.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param $name - name of path dir
	 * @param string $file - file name or directory inside path
	 *
	 * @return string
	 */
	public function path( $name, $file = '' ) {
		$path = $this->paths[ $name ] . ( strlen( $file ) > 0 ? '/' . preg_replace( '/^\//', '', $file ) : '' );

		return apply_filters( 'BKM_path_filter', $path );
	}

	/**
	 * Set default post types. Backup Manager editors are enabled for such kind of posts.
	 *
	 * @param array $type - list of default post types.
	 */
	public function setEditorDefaultPostTypes( array $type ) {
		$this->editor_default_post_types = $type;
	}

	/**
	 * Returns list of default post types where user can use Backup Manager editors.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 */
	public function editorDefaultPostTypes() {
		return $this->editor_default_post_types;
	}

	/**
	 * Get post types where Backup Manager editors are enabled.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 */
	public function editorPostTypes() {
		if ( ! isset( $this->editor_post_types ) ) {
			$pt_array = BKM_settings()->get( 'content_types' );
			$this->editor_post_types = $pt_array ? $pt_array : $this->editorDefaultPostTypes();
		}

		return $this->editor_post_types;
	}

	/**
	 * Setter for as network plugin for MultiWP.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param bool $value
	 */
	public function setAsNetworkPlugin( $value = true ) {
		$this->is_network_plugin = $value;
	}

	/**
	 * Directory name where template files will be stored.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return string
	 */
	public function uploadDir() {
		return 'aa-backup-manager';
	}

	/**
	 * Getter for plugin name variable.
	 * @since 1.0
	 *
	 * @return string
	 */
	public function pluginName() {
		return $this->plugin_name;
	}
	
	/**
	 * Get absolute url for Backup Manager asset file.
	 *
	 * Assets are css, javascript, less files and images.
	 *
	 * @since 4.2
	 *
	 * @param $file
	 *
	 * @return string
	 */
	public function assetUrl( $file ) {
		return preg_replace( '/\s/', '%20', plugins_url( $this->path( 'ASSETS_DIR_NAME', $file ), __FILE__ ) );
	}
	
	public function mb_unserialize($serial_str) 
	{
        static $adds_slashes = -1;
        if ($adds_slashes === -1) // Check if preg replace adds slashes
            $adds_slashes = (false !== strpos( preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", 's:1:""";'), '\"' ));

        $result = @unserialize( preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $serial_str) );
        return ( $adds_slashes ? stripslashes_deep( $result ) : $result );
    }
	
	public function print_filters_for( $hook = '' ) 
	{
	    global $wp_filter;
	    if( empty( $hook ) || !isset( $wp_filter[$hook] ) )
	        return;
	
	   	var_dump('<pre>',$wp_filter[$hook],'</pre>');
	}
}

/**
 * Main Backup Manager manager.
 * @var BKM_Manager $BKM_manager - instance of composer management.
 * @since 1.0
 */
global $BKM_manager;
$BKM_manager = new BKM_Manager();