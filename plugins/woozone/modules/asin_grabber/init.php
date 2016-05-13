<?php
/*
* Define class wwcAmzAffASINGrabber
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;

if (class_exists('wwcAmzAffASINGrabber') != true) {
    class wwcAmzAffASINGrabber
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
		
		private $settings;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $wwcAmzAff;

        	$this->the_plugin = $wwcAmzAff;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/asin_grabber/';
			$this->module = $this->the_plugin->cfg['modules']['asin_grabber'];
			
			$this->settings = $wwcAmzAff->getAllSettings('array', 'amazon');
  
			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
			}
			
			add_action('wp_ajax_wwcAmzAff_grabb_asins', array( &$this, 'grabb_assins' ));
			
			$this->settings['page_types'] = array(
				'Best Sellers',
				//'Deals',
				'Top Rated',
				'Most Wished For',
				'Movers & Shakers',
				'Hot New Releases',
				//'Best Sellers Cattegory',
				//'Gift Ideas',
				//'New Arrivals',
			);
        }

		/**
	    * Singleton pattern
	    *
	    * @return wwcAmzAffASINGrabber Singleton instance
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
    			$this->the_plugin->alias . " " . __('ASIN Grabber', $this->the_plugin->localizationName),
	            __('ASIN Grabber', $this->the_plugin->localizationName),
	            'manage_options',
	            $this->the_plugin->alias . "_asin_grabber",
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
		<link rel='stylesheet' href='<?php echo $this->module_folder;?>app.css' type='text/css' media='all' />
		<div id="wwcAmzAff-wrapper" class="fluid wrapper-wwcAmzAff wwcAmzAff-asin-grabber">
			
			<?php
			// show the top menu
			wwcAmzAffAdminMenu::getInstance()->make_active('import|asin_grabber')->show_menu();
			?>

			<!-- Content -->
			<div id="wwcAmzAff-content">
				
				<h1 class="wwcAmzAff-section-headline">
					<?php 
					if( isset($this->module['asin_grabber']['in_dashboard']['icon']) ){
						echo '<img src="' . ( $this->module_folder . $this->module['asin_grabber']['in_dashboard']['icon'] ) . '" class="wwcAmzAff-headline-icon">';
					}
					?>
					<?php echo $this->module['asin_grabber']['menu']['title'];?>
					<span class="wwcAmzAff-section-info"><?php echo $this->module['asin_grabber']['description'];?></span>
					<?php
					$has_help = isset($this->module['asin_grabber']['help']) ? true : false;
					if( $has_help === true ){
						
						$help_type = isset($this->module['asin_grabber']['help']['type']) && $this->module['asin_grabber']['help']['type'] ? 'remote' : 'local';
						if( $help_type == 'remote' ){
							echo '<a href="#load_docs" class="wwcAmzAff-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $this->module['asin_grabber']['help']['url'] ) . '">HELP</a>';
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
					<div id="wwcAmzAff-content-wrap" class="clearfix" style="padding-top: 5px;">

						<!-- Content Area -->
						<div id="wwcAmzAff-content-area">
							<div class="wwcAmzAff-grid_4">
	                        	<div class="wwcAmzAff-panel">
	                        		<div class="wwcAmzAff-panel-header">
										<span class="wwcAmzAff-panel-title">
											<?php _e('ASIN Grabber', $this->the_plugin->localizationName);?>
										</span>
									</div>
									<div class="wwcAmzAff-panel-content">
							            <form id="wwcAmzAff-grabb-asins" class="wwcAmzAff-form">
							            	<div class="wwcAmzAff-form-row">
												<label for="protocol">Amazon URL:</label>
												<div class="wwcAmzAff-form-item large">
													<span class="formNote">The Amazon Page from where you want to import the ASIN codes. E.g: http://www.amazon.com/gp/top-rated</span>
													<input type="text" value="" name="wwcAmzAff[grabb-url]" placeholder="Paste the Amazon page URL here" />
												</div>
											</div>
											
											<div class="wwcAmzAff-form-row">
												<label for="protocol">Page type:</label>
												<div class="wwcAmzAff-form-item large">
													<span class="formNote">....</span>
													
													<select name="wwcAmzAff[page-type]" style="width: 120px; float: left;">
														<?php
														if( count($this->settings['page_types']) > 0 ){
															foreach ($this->settings['page_types'] as $page) {
																echo '<option value="' . ( strtolower( $page )) . '">' . ( $page ) . '</option>';
															}
														}
														?>
													</select>
													<div style="display: none;" id="wwcAmzAff-filter-by-page-nr" style="float: left; width: inherit;">
														<label style="margin-left: 100px; width: 130px;">Number of pages:</label>
														<select class="wwcAmzAff-number-of-results" style="width: 200px; float: left;">
															<option value="1">1</option>
															<option value="2">2</option>
															<option value="3">3</option>
															<option value="4">4</option>
															<option value="5">5</option>
															<option value="0">Custom number of pages</option>
														</select>
														<div class="wwcAmzAffCustomNrPages" style="float: left; width: inherit; margin-left: 29px; display: none;">
															<span>OR:</span>
															<input type="text" style="width: 120px; margin-left: 30px;" class="wwcAmzAff-custom-nr-pages" value="6" /> 
														</div>
													</div>
												</div>
											</div>
											
											<div class="wwcAmzAff-form-row">
												<div class="wwcAmzAff-form-item" style="margin-left: 0px;">
													<input type="button" class="wwcAmzAff-button orange" id="wwcAmzAff-grabb-button" value="GET ASIN codes" style="width:132px">
												</div>
											</div>
				            			</form>
				            			
				            			<form id="wwcAmzAff-asin-codes" class="wwcAmzAff-form" style="display: none;">
				            				<div class="wwcAmzAff-form-row">
												<label for="protocol">ASIN codes:</label>
												<div class="wwcAmzAff-form-item large">
													<span class="formNote">....</span>
													<textarea name="wwcAmzAff[asin-codes]" id="wwcAmzAff[asin-codes]"></textarea>
												</div>
											</div>
											
											<div class="wwcAmzAff-form-row">
												<div class="wwcAmzAff-form-item" style="margin-left: 0px;">
													<input type="button" class="wwcAmzAff-button blue" id="wwcAmzAff-import-to-queue" value="Add ASIN codes to Import Queue" style="width:212px">
												</div>
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
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>

<?php
		}

		/*
		* ajax_request, method
		* --------------------
		*
		* this will create requesto 
		*/
		public function grabb_assins()
		{
			$params = array();
			$base = array();
			$base['status'] = 'invalid';
			parse_str( $_REQUEST['params'], $params ); 
			
			$remote_url = $params['wwcAmzAff']['grabb-url'];
			$page_type = $params['wwcAmzAff']['page-type'];
			
			if( trim($remote_url) != "" ){
				require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/php-query/phpQuery.php' );
 
				$input = wp_remote_get( 
					$remote_url, 
					array( 'timeout' => 30 ) 
				);
				
				$response = wp_remote_retrieve_body( $input );
				$doc = phpQuery::newDocument( $response );
				 
				// Best Sellers page type
				if( $page_type == 'best sellers' ){
					$container = $doc->find( '#zg_left_col1' );
					
					$asins = array();
					
					if (strpos($remote_url, 'ref=') !== false) {
						$products = $container->find(".zg_itemImmersion .zg_itemWrapper .zg_image"); 
					} else {
						$products = $container->find(".zg_item .zg_image");
					}
					
					if( (int)$products->size() > 0 ){
						foreach ( $products as $product ) {
							$product_url = trim(pq( $product )->find("a")->attr('href'));
							if( $product_url != "" ){
								$product_url = @urldecode( $product_url );
								$asins[] = end( explode("/", $product_url ) );
							} 					
						} 
					}
				}
				
				// Deals page type
				elseif( $page_type == 'deals' ){
					$container = $doc->find( '#mainResults' );
					 
					$asins = array();
					if ($container->find( ".prod" ) != "") {
						$products = $container->find( ".prod" );
					} else {
							$products = $container->find( ".product" );
						}

					if( (int)$products->size() > 0 ){
						foreach ( $products as $product ) {
							$asin_item = pq( $product )->attr('name');     
							$asins[] = $asin_item; 					
						} 
					}
				}

				// Top Rated, Most Wished For, Movers & Shakers, Hot New Releases, Best Sellers Cattegory, Gift Ideas page type
				elseif( $page_type == 'top rated' || 'most wished for' || 'movers & shakers' || 'hot new releases' || 'best sellers cattegory' || 'gift ideas' ){
						$container = $doc->find( '#zg_left_col1' );
  
						$asins = array();
						if (strpos($remote_url, 'ref=') !== false) {
							$products = $container->find(".zg_itemImmersion .zg_itemWrapper .zg_image"); 
						} else {
							$products = $container->find(".zg_item .zg_image");
						}
						if( (int)$products->size() > 0 ){
							foreach ( $products as $product ) {
								$product_url = trim(pq( $product )->find("a")->attr('href'));
								if( $product_url != "" ){
									$product_url = @urldecode( $product_url );
									$asins[] = end( explode("/", $product_url ) );
								} 					
							} 
						}
					}



				// New Arrivals page type
				if( $page_type == 'new arrivals' ){
					$container = $doc->find( '#resultsCol' );
					
					$asins = array();
					$products = $container->find(".prod .image");
					if( (int)$products->size() > 0 ){
						foreach ( $products as $product ) {
							$product_url = trim(pq( $product )->find("a")->attr('href'));
							if( $product_url != "" ){
								$product_url = @urldecode( $product_url );
								$asins[] = end( explode("/", $product_url ) );
							} 					
						} 
					}
				}

				$base['status'] = 'valid';

				if( count($asins) == 0 ){

					$base['status'] = 'invalid';
					$base['msg'] = 'The script was unable to grab any ASIN codes. Please try again using another Page Type parameter.';
				}
				$base['asins'] = $asins; 
			}
			
			die( json_encode( $base ) );
		}
    }
}

if ( !function_exists('wwcAmzAffASINGrabber_cronjob') ) {
function wwcAmzAffASINGrabber_cronjob() {
	// Initialize the wwcAmzAffASINGrabber class
	$amzaffAssetDownload = new wwcAmzAffASINGrabber();
	$amzaffAssetDownload->cronjob();
}
}

// Initialize the wwcAmzAffASINGrabber class
$wwcAmzAffASINGrabber = wwcAmzAffASINGrabber::getInstance();