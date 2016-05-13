<?php
/*
* Define class wwcAmzAffContentSpinner
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;

if (class_exists('wwcAmzAffContentSpinner') != true) {
    class wwcAmzAffContentSpinner
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;

		private $module_folder = '';
		private $module = '';

		static protected $_instance;
		
		private $amz_settings;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $wwcAmzAff;

        	$this->the_plugin = $wwcAmzAff;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/content_spinner/';
			$this->module = $this->the_plugin->cfg['modules']['content_spinner'];
			
			if (is_admin()) {
				$this->amz_settings = array(); // $wwcAmzAff->getAllSettings('array', 'amazon');

	            add_action('admin_menu', array( &$this, 'adminMenu' ));
			}

			// ajax  helper
			add_action('wp_ajax_wwcAmzAffSpinContentRequest', array( &$this, 'ajax_request' ));
			add_action('wp_ajax_wwcAmzAff_rollback_content', array( &$this, 'ajax_request' ));
			add_action('wp_ajax_wwcAmzAff_save_content', array( &$this, 'ajax_request' ));
        }

		/**
	    * Singleton pattern
	    *
	    * @return wwcAmzAffContentSpinner Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }

		/**
	    * Hooks
	    */
	    static public function adminMenu()
	    {
	       self::getInstance()
	    		->_registerAdminPages();
	    }

	    /**
	    * Register plug-in module admin pages and menus
	    */
		protected function _registerAdminPages()
    	{ 
    		add_submenu_page(
    			$this->the_plugin->alias,
    			$this->the_plugin->alias . " " . __('Content Spinner', $this->the_plugin->localizationName),
	            __('Content Spinner', $this->the_plugin->localizationName),
	            'manage_options',
	            $this->the_plugin->alias . "_content_spinner",
	            array($this, 'display_index_page')
	        );

			return $this;
		}

		public function display_index_page()
		{
			$this->printBaseInterface();
		}
		
		/*
		* printBaseInterface, method
		* --------------------------
		*
		* this will add the base DOM code for you options interface
		*/
		private function printBaseInterface()
		{
			global $wpdb;
?>
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<div id="wwcAmzAff-wrapper" class="fluid wrapper-wwcAmzAff">
			
			<?php
			// show the top menu
			wwcAmzAffAdminMenu::getInstance()->make_active('import|content_spinner')->show_menu(); 
			?>

			<!-- Content -->
			<div id="wwcAmzAff-content">
				
				<h1 class="wwcAmzAff-section-headline">
					<?php 
					if( isset($this->module['content_spinner']['in_dashboard']['icon']) ){
						echo '<img src="' . ( $this->module_folder . $this->module['content_spinner']['in_dashboard']['icon'] ) . '" class="wwcAmzAff-headline-icon">';
					}
					?>
					<?php echo $this->module['content_spinner']['menu']['title'];?>
					<span class="wwcAmzAff-section-info"><?php echo $this->module['content_spinner']['description'];?></span>
					<?php
					$has_help = isset($this->module['content_spinner']['help']) ? true : false;
					if( $has_help === true ){
						
						$help_type = isset($this->module['content_spinner']['help']['type']) && $this->module['content_spinner']['help']['type'] ? 'remote' : 'local';
						if( $help_type == 'remote' ){
							echo '<a href="#load_docs" class="wwcAmzAff-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $this->module['content_spinner']['help']['url'] ) . '">HELP</a>';
						}
					}
					echo '<a href="#load_docs" class="wwcAmzAff-show-feedback" data-helptype="' . ( 'remote' ) . '" data-url="' . ( $this->the_plugin->feedback_url ) . '" data-operation="feedback">Feedback</a>';
					?>
				</h1>
				
				<!-- Main loading box -->
				<div id="wwcAmzAff-main-loading">
					<div id="wwcAmzAff-loading-overlay"></div>
					<div id="wwcAmzAff-loading-box">
						<div class="wwcAmzAff-loading-text"><?php _e('Loading', $this->the_plugin->localizationName);?></div>
						<div class="wwcAmzAff-meter wwcAmzAff-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
					</div>
				</div>

				<!-- Container -->
				<div class="wwcAmzAff-container clearfix">

					<!-- Main Content Wrapper -->
					<div id="wwcAmzAff-content-wrap" class="clearfix">

						<!-- Content Area -->
						<div id="wwcAmzAff-content-area">
							<div class="wwcAmzAff-grid_4">
	                        	<div class="wwcAmzAff-panel">
	                        		<div class="wwcAmzAff-panel-header">
										<span class="wwcAmzAff-panel-title">
											<?php _e('Synchronization logs', $this->the_plugin->localizationName);?>
										</span>
									</div>
									<div class="wwcAmzAff-panel-content">
										<form class="wwcAmzAff-form" action="#save_with_ajax">
											<div class="wwcAmzAff-form-row wwcAmzAff-table-ajax-list" id="wwcAmzAff-table-ajax-response">
											<?php
											wwcAmzAffAjaxListTable::getInstance( $this->the_plugin )
												->setup(array(
													'id' 				=> 'wwcAmzAffContentSpinner',
													'show_header' 		=> true,
													'search_box' 		=> false,
													'items_per_page' 	=> 5,
													'post_statuses' 	=> array(
														'publish'   => __('Published', $this->the_plugin->localizationName)
													),
													'list_post_types'	=> array('product'),
													'columns'			=> array(
														
														'preview'		=> array(
															'th'	=> __('Preview', $this->the_plugin->localizationName),
															'td'	=> '%preview%',
															'align' => 'left',
															'valign'=> 'top',
															'width' => '100'
														),
														
														'spinn_content'		=> array(
															'th'	=> __('Spinn Content', $this->the_plugin->localizationName),
															'td'	=> '%spinn_content%',
															'align' => 'left',
															'valign'=> 'top'
														),

													)
												))
												->print_html();
								            ?>
								            </div>
							            </form>
				            		</div>
								</div>
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

<?php
		}

		/*
		* ajax_request, method
		* --------------------
		*
		* this will create requesto to 404 table
		*/
		public function ajax_request()
		{
			$request = array(
				'prodID' 		=> isset($_REQUEST['prodID']) ? $_REQUEST['prodID'] : 0,
				'replacements' 	=> isset($_REQUEST['replacements']) ? $_REQUEST['replacements'] : '',
				'sub_action' 	=> isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '',
				'post_content' 	=> isset($_REQUEST['post_content']) ? $_REQUEST['post_content'] : '',
				'spinned_content' 	=> isset($_REQUEST['spinned_content']) ? $_REQUEST['spinned_content'] : '',
				'reorder_content' 	=> isset($_REQUEST['reorder_content']) ? $_REQUEST['reorder_content'] : '',
			);
			
			$return = array();
			$return[$request['sub_action']] = array(
				'status' => 'invalid',
				'data' => array()
			);
			
			// rollback content action
			if( $request['sub_action'] == 'rollback_content' ){

				// first check if you have the original content saved into DB
				$post_content = get_post_meta( $request['prodID'], 'wwcAmzAff_old_content', true );
				
				// if not, retrive from DB
				if( $post_content == false ){
					// make the final return
					die(json_encode($return));
				}
				
				delete_post_meta( $request['prodID'], 'wwcAmzAff_spinned_content' );
				delete_post_meta( $request['prodID'], 'wwcAmzAff_reorder_content' );
				delete_post_meta( $request['prodID'], 'wwcAmzAff_finded_replacements' );
				
				// Update the post into the database
				wp_update_post( array(
				      'ID'           => $request['prodID'],
				      'post_content' => $post_content
				) );
  
				$return[$request['sub_action']] = array(
					'status' => 'valid',
					'data' => array(
						'reorder_content' => ''
					)
				);

				// make the final return
				die(json_encode($return));
			}
			
			// save content action
			if( $request['sub_action'] == 'save_content' ){
				
				update_post_meta( $request['prodID'], 'wwcAmzAff_spinned_content', $request['spinned_content'] );
				update_post_meta( $request['prodID'], 'wwcAmzAff_reorder_content', $request['reorder_content'] );
				
				// Update the post into the database
				wp_update_post( array(
				      'ID'           => $request['prodID'],
				      'post_content' => $request['post_content']
				) );
  
				$return[$request['sub_action']] = array(
					'status' => 'valid',
					'data' => array(
						'reorder_content' => $request['reorder_content']
					)
				);  

				// make the final return
				die(json_encode($return));				
			}
			
			if( $request['sub_action'] == 'spin_content' ){
				
				/*// spin content action
				require_once( $this->module["folder_path"]. 'phpQuery.php' );
				require_once( $this->module["folder_path"]. 'spin.class.php' );

				$spinner = wwcAmzAffSpinner::getInstance();
				$spinner->set_syn_language( $this->set_syn_language() );
				$spinner->set_replacements_number( $request['replacements'] );
				
				// first check if you have the original content saved into DB
				$post_content = get_post_meta( $request['prodID'], 'wwcAmzAff_old_content', true );
				
				// if not, retrive from DB
				if( $post_content == false ){
					$live_post = get_post( $request['prodID'], ARRAY_A );
					$post_content = $live_post['post_content'];
				}
				
				$spinner->load_content( $post_content );
				$spin_return = $spinner->spin_content();
				$reorder_content = $spinner->reorder_synonyms();
				$fresh_content = $spinner->get_fresh_content( $reorder_content );
				
				update_post_meta( $request['prodID'], 'wwcAmzAff_spinned_content', $spin_return['spinned_content'] );
				update_post_meta( $request['prodID'], 'wwcAmzAff_reorder_content', $reorder_content );
				update_post_meta( $request['prodID'], 'wwcAmzAff_old_content', $spin_return['old_content'] );
				update_post_meta( $request['prodID'], 'wwcAmzAff_finded_replacements', $spin_return['finded_replacements'] );
				
				// Update the post into the database
				wp_update_post( array(
				      'ID'           => $request['prodID'],
				      'post_content' => $fresh_content
				) );
  
				$return[$request['sub_action']] = array(
					'status' => 'valid',
					'data' => array(
						'reorder_content' => $reorder_content
					)
				);*/
				$return[$request['sub_action']] = $this->the_plugin->spin_content(array(
					'prodID'		=> $request['prodID'],
					'replacements'	=> $request['replacements']
				));
				
				// make the final return
				die(json_encode($return));
			} 

			// make the final return
			die(json_encode($return));
		}
    }
}
 
// Initialize the wwcAmzAffContentSpinner class
$wwcAmzAffContentSpinner = wwcAmzAffContentSpinner::getInstance();
