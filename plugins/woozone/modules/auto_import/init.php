<?php
/*
* Define class wwcAmzAffAutoImport
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;

if (class_exists('wwcAmzAffAutoImport') != true) {
    class wwcAmzAffAutoImport
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
        private $module_folder_path = '';
		private $module = '';

		static protected $_instance;
		
		public $localizationName;
		
		private $settings;
		
		private $searchParameters = array();
		private $searchParametersCore = array();


        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $wwcAmzAff;

        	$this->the_plugin = $wwcAmzAff;

			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/auto_import/';
            $this->module_folder_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/auto_import/';
			$this->module = $this->the_plugin->cfg['modules']['auto_import'];
			
			$this->localizationName = $this->the_plugin->localizationName;
			
			$this->settings = $this->the_plugin->getAllSettings('array', 'amazon');
			
			$countries_list = $this->the_plugin->amzHelper->get_countries( 'country' );
			// search parameters details:
			//		- title = title text
			//		- type = html element type (input, select)
			//		- options = drop down options array
			$this->searchParameters = array(
				// extra params
				'provider'				=> array(
					'title'				=> __('Provider', $this->the_plugin->localizationName),
				),
				'country'				=> array(
					'title'				=> __('Country', $this->the_plugin->localizationName),
					'editable'			=> true,
					'type'				=> 'select',
					'options'			=> $countries_list, //array(),
				),
				'search_title'			=> array(
					'title'				=> __('Search title', $this->the_plugin->localizationName),
					'editable'			=> true,
					'type'				=> 'input',
				),
				'recurrency'			=> array(
					'title'				=> __('Recurrency', $this->the_plugin->localizationName),
					'editable'			=> true,
					'type'				=> 'select',
					'options'			=> array(
				        12      => __('Every 12 hours', $this->the_plugin->localizationName),
				        24      => __('Every single day', $this->the_plugin->localizationName),
				        48      => __('Every 2 days', $this->the_plugin->localizationName),
				        72      => __('Every 3 days', $this->the_plugin->localizationName),
				        96      => __('Every 4 days', $this->the_plugin->localizationName),
				        120     => __('Every 5 days', $this->the_plugin->localizationName),
				        144     => __('Every 6 days', $this->the_plugin->localizationName),
				        168     => __('Every 1 week', $this->the_plugin->localizationName),
				        336     => __('Every 2 weeks', $this->the_plugin->localizationName),
				        504     => __('Every 3 weeks', $this->the_plugin->localizationName),
				        720     => __('Every 1 month', $this->the_plugin->localizationName), // ~ 4 weeks + 2 days
    				),
				),
				
				// search params
				'keyword'				=> array(
					'title'				=> __('Keyword', $this->the_plugin->localizationName), 
				),
				'category'				=> array(
					'title'				=> __('Category', $this->the_plugin->localizationName), 
					'readonly'			=> true,
				),
				'category_id'			=> array(
					'title'				=> __('Category ID', $this->the_plugin->localizationName),
					'readonly'			=> true,
				),
				'nbpages'				=> array(
					'title'				=> __('Grab Nb Pages', $this->the_plugin->localizationName), 
				),
				'page'					=> array(
					'title'				=> __('Page Nb', $this->the_plugin->localizationName), 
				),
				'site'					=> array(
					'title'				=> __('Choose Site', $this->the_plugin->localizationName), 
				),
				'BrowseNode_list'		=> array(
					'title'				=> __('Browse Node Tree', $this->the_plugin->localizationName), 
				),
				
				// import params
				'import_type'			=> array(
					'title'				=> __('Image Import Type', $this->the_plugin->localizationName), 
				),
				'nbimages'				=> array(
					'title'				=> __('Number of Images', $this->the_plugin->localizationName), 
				),
				'nbvariations'			=> array(
					'title'				=> __('Number of Variations', $this->the_plugin->localizationName), 
				),
				'spin'					=> array(
					'title'				=> __('Spin on Import', $this->the_plugin->localizationName), 
				),
				'attributes'			=> array(
					'title'				=> __('Import attributes', $this->the_plugin->localizationName), 
				),
				'to_category'			=> array(
					'title'				=> __('Import in category', $this->the_plugin->localizationName), 
				),
				'prods_import_type'			=> array(
					'title'				=> __('Products Import Type', $this->the_plugin->localizationName), 
				),
			);
			// core search parameters (cannot be deselected)
			// (key, value) => (parameter key, is editable?)
			$this->searchParametersCore = array(
				'provider'			=> false,
				'country'			=> true,
				'search_title'		=> true,
				'recurrency'		=> true,
			);
  
			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
			}
			
            // ajax requests
			add_action('wp_ajax_wwcAmzAff_AutoImportAjax', array( &$this, 'ajax_request' ), 10, 2);
        }

		/**
	    * Singleton pattern
	    *
	    * @return wwcAmzAffAutoImport Singleton instance
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
    			$this->the_plugin->alias . " " . __('Auto Import', $this->the_plugin->localizationName),
	            __('Auto Import', $this->the_plugin->localizationName),
	            'manage_options',
	            $this->the_plugin->alias . "_auto_import",
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
		?>
			<link rel='stylesheet' href='<?php echo $this->module_folder;?>app.css' type='text/css' media='all' />
			<script type='text/javascript' src='<?php echo $this->module_folder;?>app.class.js' ></script>
		<?php
		}
		
		
		/**
		 * Insane Mode related
		 */
		// load asset file: css, javascript or else!
		public function load_asset( $what='', $print=true ) {
			$asset = '';
			if ( 'css' == $what ) {
				$asset = "<link rel='stylesheet' href='".$this->module_folder."app.css' type='text/css' media='all' />";
			}
			else if ( 'js' == $what ) {
				$asset = "<script type='text/javascript' src='".$this->module_folder."app.class.js' ></script>";
			}
			else {
				$asset = $what;
			}
			if ( empty($asset) ) return;

			if ( $print ) echo $asset;
			else return $asset;
		}
		
		public function print_schedule_button( $pms=array(), $print=true ) {
			extract($pms);

			$asset = '<li class="button-block">
				<input type="button" value="' . $title . '" class="wwcAmzAff-button green wwcAmzAff-add-to-schedule" />
			</li>';
			
			if ( $print ) echo $asset;
			else return $asset;
		}
		
		public function print_auto_import_options( $pms=array(), $print=true ) {
			extract($pms);
			
			ob_start();

?>
                                        <li>
                                            <h4><?php _e('Products Import Type', $this->the_plugin->localizationName);?></h4>
                                            <span class="wwcAmzAff-checked-product squaredThree">
                                                <input type="radio" value="default" name="import-parameters[prods_import_type]" id="import-parameters-import_type-default" <?php echo $import_params['prods_import_type'] == 'default' ? 'checked="checked"' : ''; ?>></span>
                                            <label for="import-parameters-prods_import_type-default"><?php _e('Do it NOW!', $this->the_plugin->localizationName);?></label>
                                            <br />
                                            <span class="wwcAmzAff-checked-product squaredThree">
                                                <input type="radio" value="asynchronous" name="import-parameters[prods_import_type]" id="import-parameters-prods_import_type-asynchronous" <?php echo $import_params['prods_import_type'] == 'asynchronous' ? 'checked="checked"' : ''; ?>></span>
                                            <label for="import-parameters-prods_import_type-asynchronous"><?php _e('Asynchronuous products import', $this->the_plugin->localizationName);?></label>
                                        </li>
<?php
			$asset = ob_get_contents();
			ob_end_clean();

			if ( $print ) echo $asset;
			else return $asset;
		}
		
		
		/**
		 * Add Search To Schedule
		 */
		public function ajax_request( $retType='die', $pms=array() ) {
            $requestData = array(
                'action'             => isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '',
            );
            extract($requestData);
			//var_dump('<pre>', $requestData, '</pre>'); die('debug...');

            $ret = array(
                'status'        => 'invalid',
                'html'          => '',
            );

            if ( 'search_get_params' == $action ) {
            	$opStatus = $this->schedule_search_get_params();
				$ret = array_merge($ret, $opStatus);
            }
			else if ( 'search_save_params' == $action ) {
            	$opStatus = $this->schedule_search_save_params();
				$ret = array_merge($ret, $opStatus);
            }

            if ( $retType == 'return' ) { return $ret; }
            else { die( json_encode( $ret ) ); }
		}

		// schedule: get search parameters
		public function schedule_search_get_params( $pms=array() ) {
            $requestData = array(
                'provider'			 => isset($_REQUEST['provider']) ? $_REQUEST['provider'] : 'amazon',
                'country'			 => isset($_REQUEST['country']) ? $_REQUEST['country'] : 'com',
                'search_title'		 => isset($_REQUEST['search_title']) ? $_REQUEST['search_title'] : '--Search Unnamed',
                'recurrency'		 => isset($_REQUEST['recurrency']) ? $_REQUEST['recurrency'] : 24,

                'extra_params'		 => isset($_REQUEST['extra_params']) ? $_REQUEST['extra_params'] : '',
                'params'			 => isset($_REQUEST['params']) ? $_REQUEST['params'] : '',
                'import_params'		 => isset($_REQUEST['import_params']) ? $_REQUEST['import_params'] : '',
            );
			
            // search params
            // & import params: import_type, nbimages, nbvariations, spin, attributes, to-category
            // & extra params for schedule
            foreach (array('extra_params', 'params', 'import_params') as $what_params) {
	            $params = array();
	            parse_str( ( $requestData["$what_params"] ), $params);
	            if( !empty($params) ) {
		            if( isset($params['wwcAmzAff-search'])) {
		                //$requestData = array_merge($requestData, $params['wwcAmzAff-search']);
		                $requestData["$what_params"] = $params['wwcAmzAff-search'];
		            } else {
	                	//$requestData = array_merge($requestData, $params);
	                	$requestData["$what_params"] = $params;
		            }
	            }
				//unset( $requestData["$what_params"] );
            }

            foreach ($requestData as $rk => $rv) {
				if ( isset($pms["$rk"]) ) {
					$new_val = $pms["$rk"];
                    $new_val = in_array($rk, array()) ? (array) $new_val : $new_val;
                    $requestData["$rk"] = $new_val;
                }
            }
			
			// extra params
			//if ( !isset($requestData['extra_params']['country']) ) {
			//	$country = isset($this->settings['country']) ? $this->settings['country'] : 'com';
			//	$_country = $this->the_plugin->amzHelper->get_countries( 'country' );
			//	$_country = $_country["$country"];
			//	$requestData['extra_params']['country'] = $country;
			//	$requestData['extra_params']['_country'] = $_country;
			//}
			foreach ($this->searchParametersCore as $key => $val) {
				if ( !isset($requestData['extra_params']["$key"]) ) {
					$requestData['extra_params']["$key"] = $requestData["$key"];
				}				
			}

            foreach ($requestData as $key => $val) {
                if ( strpos($key, '-') !== false ) {
                    $_key = str_replace('-', '_', $key); 
                    $requestData["$_key"] = $val;
                    unset($requestData["$key"]);
                	$key = $_key;
                }
                
				if ( !empty($val) && is_array($val) ) {
					foreach ($val as $key2 => $val2) {
		                if ( strpos($key2, '-') !== false ) {
		                    $_key2 = str_replace('-', '_', $key2); 
		                    $requestData["$key"]["$_key2"] = $val2;
		                    unset($requestData["$key"]["$key2"]);
							$key2 = $_key2;
		                }
					}
				}
            }
            extract($requestData);
			//var_dump('<pre>', $requestData, '</pre>'); die('debug...');
			
			$search_params = array_diff_key($requestData, $this->searchParametersCore);
			//var_dump('<pre>', $search_params, '</pre>'); die('debug...');

			$search_params_ = $search_params;
			foreach ( $search_params_ as $k => $v ) {
				if ( empty($v) || !is_array($v) ) {
					unset($search_params_["$k"]);
					continue 1;
				}
				foreach ($v as $key => $val) {
					$val = is_array($val) ? $val : trim($val);
					if ( empty($val) ) {
						unset($search_params_["$k"]["$key"]);
						continue 1;
					}
					
					if ( '_' == substr($key, 0, 1) ) {
						$__ = substr($key, 1);
						if (!empty($val) && isset($search_params_["$k"]["$__"])) {
							$search_params_["$k"]["$__"] = $val;
							unset( $search_params_["$k"]["$key"] );
						}
					}
				}
			}
			//var_dump('<pre>', $search_params_, '</pre>'); die('debug...');

            $ret = array(
                'status'        => 'invalid',
                'html'          => '',
            );


            $css = array();
            $css['container'] = '';
			
			$html = array();
			//$html[] = '<div class="wwcAmzAff-big-overlay-lightbox '.$css['container'].'">';
			$html[] = 	'<div class="wwcAmzAff-donwload-in-progress-box">';
			$html[] = 		'<h1>' . __('Add Search to schedule', $this->localizationName ) . '<a href="#" class="wwcAmzAff-button red" id="wwcAmzAff-close-btn">' . __('CLOSE', $this->localizationName ) . '</a></h1>';
			
			$html[] = 		'<p class="wwcAmzAff-message wwcAmzAff-info wwcAmzAff-donwload-notice">';
			$html[] = 		__('Search Parameters', $this->localizationName );
			$html[] = 		'</p>';
			
			$html[] = 		'<form id="wwcAmzAff-search-add-schedule" class="wwcAmzAff-search-add-schedule">';
			//$html[] = 		'<h2 class="wwcAmzAff-process-headline">' . __('Debugging Information:', $this->localizationName ) . '</h2>';
			$html[] = 		'<div class="wwcAmzAff-autoimport-search-params">';
			$html[] = 		'<table class="wwcAmzAff-table wwcAmzAff-debug-info">';
			$html[] = 			'<thead>';
			$html[] = 				'<tr>';
			$html[] = 					'<th width="250">' . __('Parameter Name', $this->localizationName ) . '</th>';
			$html[] = 					'<th>' . __('Parameter Value', $this->localizationName ) . '</th>';
			$html[] = 				'</tr>';
			$html[] = 			'</thead>';
			$html[] = 			'<tbody>';
			foreach ( $search_params_ as $k => $v ) {
				foreach ($v as $key => $val) {
					$val = is_array($val) ? $val : trim($val);
					$val_orig = $search_params["$k"]["$key"];

					if ( isset($this->searchParameters["$key"], $this->searchParameters["$key"]['title']) ) {
						$nice_name = $this->searchParameters["$key"]['title'];
					} else {
						$nice_name = $this->the_plugin->__category_nice_name( $key );
					}
					$nice_name = trim($nice_name);
					
					$readonly = '';
					if ( in_array($key, array_keys($this->searchParametersCore))
						|| (
							isset($this->searchParameters["$key"], $this->searchParameters["$key"]['readonly'])
							&& $this->searchParameters["$key"]['readonly'] )
					) {
						$readonly = 'readonly="readonly"';
					}
					
					$elem = array(
						'chk'			=> array(
							'name' 			=> "sschedule_stat[$k][$key]",
							'id' 			=> "sschedule_stat[$k][$key]",
						),
						'param'			=> array(
							'name' 			=> "sschedule[$k][$key]",
							'id' 			=> "sschedule[$k][$key]",
							'value'			=> $val_orig,
						),
						'_param'		=> array(
							'name' 			=> "sschedule[$k][_$key]",
							'id' 			=> "sschedule[$k][_$key]",
							'value'			=> $val,
						),
					);

					// BrowseNode list/tree parameter
					$is_bnl = false; // parameter is browse node list?
					if ( 'BrowseNode_list' == $key ) {
						if ( !empty($val_orig) ) {

							$browsenode_list = array();
							foreach ($val_orig as $key2 => $val2) {

								$browsenode_list['hidden'][] = '<input type="hidden" name="' . $elem['param']['name'] . '['.$key2.']" id="' . $elem['param']['id'] . '['.$key2.']" value="' . $elem['param']['value'][$key2] . '"/>';
								if ( isset($search_params["$k"]["_$key"]) ) {
									$browsenode_list['hidden'][] = '<input type="hidden" name="' . $elem['_param']['name'] . '['.$key2.']" id="' . $elem['_param']['id'] . '['.$key2.']" value="' . $elem['_param']['value'][$key2] . '"/>';
								}

								$browsenode_list['show'][] = $elem['_param']['value'][$key2];
							}
							/*
							$html[] = 			'<tr>';
							$html[] = 				'<td class="wwcAmzAff-bn">' . $nice_name . '</td>';
							$html[] = 				'<td>';
							
							$html[] = 				implode(PHP_EOL, $browsenode_list['hidden']);
							$html[] = 				implode(' &gt; ', $browsenode_list['show']);
								
							$html[] = 				'</td>';
							$html[] = 			'</tr>';
							*/
							$readonly = 'style="visibility: hidden;"';
							$is_bnl = true;
						}
					}
					// All the Other parameters
					//else {
					if (1) {
						$is_editable = false; // parameter is editable?
						if ( isset($this->searchParameters["$key"], $this->searchParameters["$key"]['editable'])
							&& $this->searchParameters["$key"]['editable'] ) {
							$is_editable = true;
						}

						$html[] = 			'<tr>';
						$html[] = 				'<td>';
						
						$html[] = 					'<input type="checkbox" name="' . $elem['chk']['name'] . '" id="' . $elem['chk']['id'] . '" checked="checked" '.$readonly.'/>';
						$html[] = 					'<label for="' . $elem['chk']['id'] . '">' . ($nice_name) . '</label>';
						
						if ($is_editable) {
							$__el = $this->searchParameters["$key"];
							$__el_type = $__el['type'];
							$__el_value = 'select' == $__el_type ? $__el['options'] : '';
							$__el_default = $elem['_param']['value'];
			                $__el_extra = array(
			                    'global_desc'       => '',
			                    'desc'              => '',
			                    
								'field_name'		=> $elem['param']['name'],
								'field_id'			=> $elem['param']['id'],
			                );

							$editable_html = $this->build_searchform_element( $__el_type, $key, $__el_value, $__el_default, $__el_extra );
						}
						else {
							if ($is_bnl) {
								$html[] = 			implode(PHP_EOL, $browsenode_list['hidden']);
							}
							else {
								$html[] = 			'<input type="hidden" name="' . $elem['param']['name'] . '" id="' . $elem['param']['id'] . '" value="' . $elem['param']['value'] . '"/>';
								if ( isset($search_params["$k"]["_$key"]) ) {
									$html[] = 		'<input type="hidden" name="' . $elem['_param']['name'] . '" id="' . $elem['_param']['id'] . '" value="' . $elem['_param']['value'] . '"/>';
								}
							}
						}
						
						$html[] = 				'</td>';
						if ($is_editable) {
							$html[] = 			'<td>' . ( $editable_html ) . '</td>';
						}
						else {
							if ($is_bnl) {
								$html[] = 		'<td>' . implode(' &gt; ', $browsenode_list['show']) . '</td>';
							}
							else {
								$html[] = 		'<td>' . ( $elem['_param']['value'] ) . '</td>';								
							}
						}
						$html[] = 			'</tr>';
					}
				}
			}
			$html[] = 			'</tbody>';
			/*
			$html[] = 			'<tfoot>';
			$html[] = 				'<tr>';
			$html[] = 					'<td></td>';
			$html[] = 					'<td><input type="submit" value="' . __('Save Search to schedule', $this->the_plugin->localizationName) . '" class="wwcAmzAff-button green" /></td>';
			$html[] = 				'</tr>';
			$html[] = 			'</tfoot>';
			*/
			$html[] = 		'</table>';
			$html[] = 		'</div>';
			
			$html[] = 		'<div class="wwcAmzAff-autoimport-search-button">';
			$html[] =			'<input type="submit" value="' . __('Save Search to schedule', $this->the_plugin->localizationName) . '" class="wwcAmzAff-button green" />';
			$html[] = 		'</div>';
			
			$html[] = 		'<div class="wwcAmzAff-autoimport-search-msg">';
			$html[] = 		'</div>';
			
			$html[] = 		'</form>';

			$html[] = 	'</div>';
			//$html[] = '</div>';
			
			$ret = array_merge($ret, array(
				'status' 	=> 'valid',
				'html'		=> implode("\n", $html)
			));
			return $ret;
		}

		public function schedule_search_save_params( $pms=array() ) {
            $requestData = array(
                'allparams'		 => isset($_REQUEST['allparams']) ? $_REQUEST['allparams'] : '',
            );
			
			$allparams = array();
			parse_str( ( $requestData["allparams"] ), $allparams);
			$requestData['allparams'] = $allparams;
			var_dump('<pre>', $requestData, '</pre>'); die('debug...'); 
			
            // search params
            // & import params: import_type, nbimages, nbvariations, spin, attributes, to-category
            // & extra params for schedule
            foreach (array('extra_params', 'params', 'import_params') as $what_params) {
	            $params = array();
	            parse_str( ( $requestData["$what_params"] ), $params);
	            if( !empty($params) ) {
		            if( isset($params['wwcAmzAff-search'])) {
		                //$requestData = array_merge($requestData, $params['wwcAmzAff-search']);
		                $requestData["$what_params"] = $params['wwcAmzAff-search'];
		            } else {
	                	//$requestData = array_merge($requestData, $params);
	                	$requestData["$what_params"] = $params;
		            }
	            }
				//unset( $requestData["$what_params"] );
            }

            foreach ($requestData as $rk => $rv) {
				if ( isset($pms["$rk"]) ) {
					$new_val = $pms["$rk"];
                    $new_val = in_array($rk, array()) ? (array) $new_val : $new_val;
                    $requestData["$rk"] = $new_val;
                }
            }
			
			// extra params
			//if ( !isset($requestData['extra_params']['country']) ) {
			//	$country = isset($this->settings['country']) ? $this->settings['country'] : 'com';
			//	$_country = $this->the_plugin->amzHelper->get_countries( 'country' );
			//	$_country = $_country["$country"];
			//	$requestData['extra_params']['country'] = $country;
			//	$requestData['extra_params']['_country'] = $_country;
			//}
			foreach ($this->searchParametersCore as $key => $val) {
				if ( !isset($requestData['extra_params']["$key"]) ) {
					$requestData['extra_params']["$key"] = $requestData["$key"];
				}				
			}

            foreach ($requestData as $key => $val) {
                if ( strpos($key, '-') !== false ) {
                    $_key = str_replace('-', '_', $key); 
                    $requestData["$_key"] = $val;
                    unset($requestData["$key"]);
                	$key = $_key;
                }
                
				if ( !empty($val) && is_array($val) ) {
					foreach ($val as $key2 => $val2) {
		                if ( strpos($key2, '-') !== false ) {
		                    $_key2 = str_replace('-', '_', $key2); 
		                    $requestData["$key"]["$_key2"] = $val2;
		                    unset($requestData["$key"]["$key2"]);
							$key2 = $_key2;
		                }
					}
				}
            }
            extract($requestData);
			//var_dump('<pre>', $requestData, '</pre>'); die('debug...');
			
			$search_params = array_diff_key($requestData, $this->searchParametersCore);
			//var_dump('<pre>', $search_params, '</pre>'); die('debug...');

			$search_params_ = $search_params;
			foreach ( $search_params_ as $k => $v ) {
				if ( empty($v) || !is_array($v) ) {
					unset($search_params_["$k"]);
					continue 1;
				}
				foreach ($v as $key => $val) {
					$val = is_array($val) ? $val : trim($val);
					if ( empty($val) ) {
						unset($search_params_["$k"]["$key"]);
						continue 1;
					}
					
					if ( '_' == substr($key, 0, 1) ) {
						$__ = substr($key, 1);
						if (!empty($val) && isset($search_params_["$k"]["$__"])) {
							$search_params_["$k"]["$__"] = $val;
							unset( $search_params_["$k"]["$key"] );
						}
					}
				}
			}
			//var_dump('<pre>', $search_params_, '</pre>'); die('debug...');

            $ret = array(
                'status'        => 'invalid',
                'html'          => '',
            );

			$ret = array_merge($ret, array(
				'status' 	=> 'valid',
				'html'		=> implode("\n", $html)
			));
			return $ret;
		}


		/**
		 * Utils
		 */
        private function build_select( $param, $values, $default='', $extra=array() ) {
            $extra = array_replace_recursive(array(
                'prefix'        => 'wwcAmzAff-search',
                'desc'          => array(),
                'nodeid'        => array(),
                
                'field_name'			=> '',
                'field_id'				=> '',
            ), $extra);
            extract($extra);

            $html = array();
            if (empty($values) || !is_array($values)) return '';
            foreach ($values as $k => $v) {
                
                $__selected = ($k == $default ? ' selected="selected"' : '');
                $__desc = (!empty($desc) && isset($desc["$k"]) ? ' data-desc="'.$desc["$k"].'"' : '');
                $__nodeid = (!empty($nodeid) && isset($nodeid["$k"]) ? ' data-nodeid="'.$nodeid["$k"].'"' : '');
                $html[] = '<option value="' . $k . '"' . $__selected . $__desc . $__nodeid . '>' . $v . '</option>';
            }
            return implode('', $html);
        }

        private function build_input_text( $param, $placeholder, $default='', $extra=array() ) {
            $extra = array_replace_recursive(array(
                'prefix'        => 'wwcAmzAff-search',
                'desc'          => array(),
                'nodeid'        => array(),
                
                'field_name'			=> '',
                'field_id'				=> '',
            ), $extra);
            extract($extra);

            $name = $prefix.'['.$param.']';
            $id = "$prefix-$param";
			if ( isset($field_name) && !empty($field_name) ) $name = str_replace('%s', $param, $field_name);
			if ( isset($field_id) && !empty($field_id) ) $id = str_replace('%s', $param, $field_id);

            return '<input placeholder="' . $placeholder . '" name="' . $name . '" id="' . $id . '" type="text" value="' . (isset($default) && !empty($default) ? $default : '') . '"' . '>';
        }
		
        public function build_searchform_element( $elm_type, $param, $value, $default, $extra=array() ) {
            $extra = array_replace_recursive(array(
                'prefix'        		=> 'wwcAmzAff-search',
                'global_desc'           => '',
                'desc'                  => array(),

                'field_name'			=> '',
                'field_id'				=> '',
            ), $extra);
            extract($extra);

            $css = array();
            /*$fa = 'fa-bars';
            if ( $param == 'Sort' ) {
                $fa = 'fa-sort';
            } else if ( $param == 'BrowseNode' ) {
                $fa = 'fa-sitemap';
                $css[] = 'wwcAmzAff-param-node';
            }*/
            $css = !empty($css) ? ' ' .implode(' ', $css) : '';
			
            $name = $prefix.'['.$param.']';
            $id = "$prefix-$param";
			if ( isset($field_name) && !empty($field_name) ) $name = str_replace('%s', $param, $field_name);
			if ( isset($field_id) && !empty($field_id) ) $id = str_replace('%s', $param, $field_id);
            
            $html = array();
            //$html[] = '<li class="wwcAmzAff-param-optional'.$css.'">';
            //$html[] =       '<span class="tooltip" title="'.$global_desc.'" data-title="'.$global_desc.'"><i class="fa '.$fa.'"></i></span>';
            $nice_name = $this->the_plugin->__category_nice_name( $param );
            if ( $elm_type == 'input' ) {
                //$value = $nice_name;
                $html[] =   $this->build_input_text( $param, $value, $default, $extra );
            } else if ( $elm_type == 'select' ) {
                $html[] =   '<select id="'.$id.'" name="'.$name.'">';
                $html[] =       '<option value="" disabled="disabled">'.$nice_name.'</option>';
                $html[] =   $this->build_select( $param, $value, $default, $extra );
                $html[] =   '</select>';
            }
            //$html[] = '</li>';
            return implode('', $html);
        }
	}
}

// Initialize the wwcAmzAffAutoImport class
$wwcAmzAffAutoImport = wwcAmzAffAutoImport::getInstance();