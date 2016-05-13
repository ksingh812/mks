<?php
/*
* Define class wwcAmzAff_ActionAdminAjax
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('wwcAmzAff_ActionAdminAjax') != true) {
    class wwcAmzAff_ActionAdminAjax
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;
		public $amzHelper = null;

		static protected $_instance;
		
	
		/*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct( $parent )
        {
			$this->the_plugin = $parent;
    
			$this->amzHelper = $this->the_plugin->amzHelper;
  
			add_action('wp_ajax_wwcAmzAff_AttributesCleanDuplicate', array( $this, 'attributes_clean_duplicate' ));
			add_action('wp_ajax_wwcAmzAff_CategorySlugCleanDuplicate', array( $this, 'category_slug_clean_duplicate' ));
			add_action('wp_ajax_wwcAmzAff_clean_orphaned_amz_meta', array( $this, 'clean_orphaned_amz_meta' ));
			add_action('wp_ajax_wwcAmzAff_delete_zeropriced_products', array( $this, 'delete_zeropriced_products' ));
            add_action('wp_ajax_wwcAmzAff_clean_orphaned_prod_assets', array( $this, 'clean_orphaned_prod_assets' ));
			add_action('wp_ajax_wwcAmzAff_fix_product_attributes', array( $this, 'fix_product_attributes' ));
			add_action('wp_ajax_wwcAmzAff_fix_issues', array( $this, 'fix_issues' ));
            
            // cronjobs panel
            add_action('wp_ajax_wwcAmzAff_cronjobs', array( $this, 'cronjobs_actions' ));
            
            // report
            add_action('wp_ajax_wwcAmzAff_report_settings', array( $this, 'report_actions' ));
			
            if ( $this->the_plugin->is_admin ) {
            	// Insane Mode - cache delete!
                add_action('wp_ajax_wwcAmzAff_import_cache', array( $this, 'import_cache' ));
            }
        }
        
		/**
	    * Singleton pattern
	    *
	    * @return Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }
	        
	        return self::$_instance;
	    }
	    
	    
	    /**
	     * Clean Duplicate Attributes
	     *
	     */
		public function attributes_clean_duplicate( $retType = 'die' ) {
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'			=> 'invalid',
				'msg_html'			=> ''
			);

			if ($action != 'attr_clean_duplicate' ) die(json_encode($ret));

			return $this->amzHelper->attrclean_clean_all();
		}
		
	    /**
	     * Clean Duplicate Category Slug
	     *
	     */
		public function category_slug_clean_duplicate( $retType = 'die' ) {
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'			=> 'invalid',
				'msg_html'			=> ''
			);

			if ($action != 'category_slug_clean_duplicate' ) die(json_encode($ret));

			return $this->amzHelper->category_slug_clean_all();
		}
		
		/**
	     * Clean Orphaned Amz Meta
	     *
	     */
		public function clean_orphaned_amz_meta( $retType = 'die' ) {    
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'			=> 'invalid',
				'msg_html'			=> ''
			);

			if ($action != 'clean_orphaned_amz_meta' ) die(json_encode($ret));

			return $this->amzHelper->clean_orphaned_amz_meta_all();
		}

		/**
	     * Clean Orphaned Amz Meta
	     *
	     */
		public function delete_zeropriced_products( $retType = 'die' ) {    
			$action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

			$ret = array(
				'status'			=> 'invalid',
				'msg_html'			=> ''
			);

			if ($action != 'delete_zeropriced_products' ) die(json_encode($ret));
			
			return $this->the_plugin->delete_zeropriced_products_all();
		}
        
        /**
         * Clean Orphaned Amazon Products Assets
         *
         */
        public function clean_orphaned_prod_assets( $retType = 'die' ) {    
            $action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

            $ret = array(
                'status'            => 'invalid',
                'msg_html'          => ''
            );

            if ($action != 'clean_orphaned_prod_assets' ) die(json_encode($ret));

            return $this->amzHelper->clean_orphaned_prod_assets_all();
        }
		
        /**
         * Clean Orphaned Amazon Products Assets
         *
         */
        public function fix_product_attributes( $retType = 'die' ) {    
            $action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

            $ret = array(
                'status'            => 'invalid',
                'msg_html'          => ''
            );

            if ($action != 'fix_product_attributes' ) die(json_encode($ret));

            return $this->amzHelper->fix_product_attributes_all();
        }

        /**
         * Cronjobs Panel - ajax actions
         *
         */
        public function cronjobs_actions( $retType = 'die' ) {    
            // Initialize the wwcAmazonSyncronize class
            require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . '/modules/cronjobs/cronjobs.panel.php' );
            $cronObj = new wwcAmzAffCronjobsPanel($this->the_plugin, array());

            $cronObj->ajax_request();
        }
        
        /**
         * Report Panel - ajax actions
         *
         */
        public function report_actions( $retType = 'die' ) {    
            // Initialize the wwcAmzAffReport class
            require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . '/modules/report/init.php' );
            $reportObj = new wwcAmzAffReport();

            $reportObj->ajax_request_settings();
        }
		
        /**
         * Insane Mode - cache delete!
         */
        public function import_cache() {
            $action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

            $ret = array(
                'status'            => 'invalid',
                'start_date'        => date('Y-m-d H:i:s'),
                'start_time'        => 0,
                'end_time'          => 0,
                'duration'          => 0,
                'msg'               => '',
                'msg_html'          => ''
            );

            if ( in_array($action, array('getStatus', 'cache_delete')) ) {
              
                require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . '/modules/insane_import/init.php' );
                $im = wwcAmzAffInsaneImport::getInstance();
				
				$providers = array_keys( array('amazon' => 'amz') );
  				$cacheSettings = $im->getCacheSettings();

            } else {
                $ret['msg_html'] = 'unknown request';
                die(json_encode($ret));
            }

            if ( in_array($action, array('getStatus', 'cache_delete')) ) {

                //$notifyStatus = $this->the_plugin->get_theoption('psp_Minify');
                //if ( $notifyStatus === false || !isset($notifyStatus["cache"]) ) ;
                //else {
                    //$ret['msg_html'] = $notifyStatus["cache"]["msg_html"];
                    
	                $ret = array_merge($ret, array(
	                    'status'    => 'valid',
	                    'msg'       => 'success',
	                ));
					$ret['start_time'] = $this->the_plugin->microtime_float();
  
					$cache_count = $this->__cache_count(array(
						'action'			=> $action,
						'providers'			=> $providers,
						'cacheSettings'		=> $cacheSettings,
						'start_date'		=> $ret['start_date'],
					));
					$ret['msg_html'] = implode(PHP_EOL, $cache_count['html']);
                //}
                
				$ret['end_time'] = $this->the_plugin->microtime_float();
				$ret['duration'] = number_format( ($ret['end_time'] - $ret['start_time']), 2 );
                
                die(json_encode($ret));
            }
            
            //$notifyStatus = $this->the_plugin->get_theoption('psp_Minify');

            //$notifyStatus["cache"] = $ret;
            //$this->the_plugin->save_theoption('psp_Minify', $notifyStatus);
            die(json_encode($ret));
        }

		private function __cache_count( $pms=array() ) {
			extract($pms);

			$ret = array(
				'html'	=> array()
			);
			$ln = $this->the_plugin->localizationName;
			{
				{
					$cache_types = array('search', 'prods');

					$html = array(); $found = 0;
					$html[] = '<table class="wp-list-table widefat striped">';
					$html[] = 	'<thead>';
					$html[] = 		'<tr><th>' . __('Provider', $ln) . '</th><th colspan=2 style="text-align: center;">' . sprintf( __('Number of files in cache | date: %s.', $ln), $start_date ) . '</th></tr>';
					$html[] = 		'<tr><th></th><th>' . __('Search Products', $ln) . '</th><th>' . __('Product details', $ln) . '</th></tr>';
					$html[] = 	'</thead>';
					$html[] = 	'<tfoot></tfoot>';
					$html[] = 	'<tbody>';
					foreach ($providers as $provider) {

						$html[] = '<tr><td>' . strtoupper($provider) . '</td>';
						foreach ($cache_types as $cache_type) {

							$cache_folder = $cache_type . '_folder';
							$cache_folder = $cacheSettings["$cache_folder"];
							//$cache_folder .= $provider . '/';
							
							if ( 'cache_delete' == $action ) {
                				$files = glob( $cache_folder . '*.*' );
                				if ( is_array( $files ) ) array_map( "unlink", $files );
							}
							
                    		$nb = (int) $this->the_plugin->u->get_folder_files_recursive( $cache_folder );

							$html[] = '<td>' . '<span class="success">' . sprintf( __('%s files', $ln), $nb ) . '</span>' . '</td>';

							$found++;
						}
						$html[] = '</tr>';
					}
					$html[] = 	'</tbody>';
					$html[] = '</table>';
					
					if ( !$found ) $html = array();
					
					$html[] = '<span>' . __('Expiration (value in minutes): ', $ln);
					foreach ($cache_types as $cache_type) {
						
						$cache_lifetime = $cache_type . '_lifetime';
						$cache_lifetime = $cacheSettings["$cache_lifetime"];
							
						$html[] = 'search' == $cache_type ? __('Search Products: ', $ln) : __('Product details: ', $ln);
						$html[] = $cache_lifetime . '&nbsp;';
					}
					$html[] = '</span>';
				}
			}
			$ret['html'] = $html;
			return $ret;
		}
    
	
        /**
         * Fix issues
         *
         */
        public function fix_issues( $retType = 'die' ) {    
            $action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';

            $ret = array(
                'status'            => 'invalid',
                'msg_html'          => ''
            );

            if (!in_array($action, array('fix_issue_request_amazon'))) die(json_encode($ret));

            return $this->amzHelper->fix_issues();
        }
	}
}

// Initialize the wwcAmzAff_ActionAdminAjax class
//$wwcAmzAff_ActionAdminAjax = new wwcAmzAff_ActionAdminAjax();
