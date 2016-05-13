<?php
/*
* Define class wwcAmzAffPriceSelect
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;

if (class_exists('wwcAmzAffPriceSelect') != true) {
    class wwcAmzAffPriceSelect
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
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/price_select/';
			$this->module = $this->the_plugin->cfg['modules']['price_select'];
			
			//$this->settings = $wwcAmzAff->getAllSettings('array', 'amazon');
  			 
			if (is_admin()) {
				add_action( 'save_post', array( $this, 'save_post' ));
				add_action( 'admin_footer', array( $this, 'add_product_inline' ) );
				
				// javascript & css module scripts inluding!
				add_action( "admin_print_styles", array( &$this, 'admin_load_styles') );
				add_action( "admin_print_scripts", array( &$this, 'admin_load_scripts') );
				
				// products list page
				if( isset($_GET['post_type']) && $_GET['post_type'] == 'product') {
					add_action('admin_head', array( $this, 'css_page_list') );
				}
			}
			
			add_action('wp_ajax_wwcAmzAffPriceSelectSave', array( $this, 'ajax_request' ));
        }
		
		public function admin_load_styles()
		{
			wp_enqueue_style( 'wwcAmzAff-price-select-styles', $this->module_folder . 'app.css' );
		}
		
		public function admin_load_scripts()
		{
			wp_enqueue_script( 'wwcAmzAff-price-select-scripts', $this->module_folder . 'app.class.js' );
		}
		
		public function is_prod_valid( $post_id ) {
			if ( empty($post_id) ) return false;

			$isProdAmz = $this->the_plugin->verify_product_isamazon($post_id);
			if ( empty($isProdAmz) ) return false;
			return true;
		}
		
		// inline content for woo amazon add new products
		public function add_product_inline( $post_id = 0 )
		{
			$req = array(
				'is_post_edit'		=> isset($_REQUEST['post']) ? true : false,
				'post_id'			=> isset($_REQUEST['post']) ? (int) $_REQUEST['post'] : $post_id,
			);
			extract($req);
			
			$arrProducts = array();

			if ( empty($post_id) ) return;

			$isProdValid = $this->is_prod_valid($post_id);
			if ( empty($isProdValid) ) return;
			
			//$arrProducts["$post_id"] = 1; // if using array_flip on variations!
			$arrProducts[0] = $post_id;
			
			$isProdVariation = $this->the_plugin->verify_product_isvariation($post_id);
			if ( $isProdVariation ) {
				$arrProducts = $this->the_plugin->get_product_variations($post_id);
				//$arrProducts = array_flip($arrProducts);
				
				if ( empty($arrProducts) ) return;
			};
  
			$post_id_orig = $post_id;
			$html = array();
			foreach ( $arrProducts as $post_id ) { // products loop!
  
				$amzResp = get_post_meta($post_id, '_amzaff_amzRespPrice', true);
				if ( empty($amzResp) ) continue 1;
	
				// start html
				$html[] = '<div class="wwcAmzAffPriceSelectHidden" data-post_id="' . ($post_id) . '" style="display: none;">';
	
				foreach (array('regular', 'sale') as $priceType) {
					$meta = (array) get_post_meta( $post_id, '_amzaff_'.$priceType.'_price', true );
					$meta_price = get_post_meta( $post_id, '_'.$priceType.'_price', true );
					
					foreach (array('auto', 'selected', 'ancestry', 'current') as $metaVal) {
						$_value = '';
						if ( !empty($meta) && isset($meta["$metaVal"]) ) {
							$_value = $meta["$metaVal"];
						} else {
							if ( $metaVal == 'auto' && !empty($meta_price) ) {
								$_value = $meta_price;
							}
						}
	
						$attr = array(
							'cssClass'			=> "wwcAmzAff-price-$priceType-$metaVal",
							'name'				=> "wwcAmzAff-price[$post_id][$priceType][$metaVal]",
							'value'				=> $_value,
						);
						$html[] = '<input type="hidden" class="'.$attr['cssClass'].'" name="'.$attr['name'].'" value="'.$attr['value'].'" />';					
					}
				}
				
				// is amazon product!
				$attr = array(
					'cssClass'			=> "wwcAmzAff-price-isprodamz",
					'name'				=> "wwcAmzAff-price[$post_id][isprodamz]",
					'value'				=> $isProdValid ? 1 : 0,
				);
				$html[] = '<input type="hidden" class="'.$attr['cssClass'].'" name="'.$attr['name'].'" value="'.$attr['value'].'" />';
	
				$html[] = '</div>';
				
				$html[] = '<div id="wwcAmzAffPriceSelectInline-' . ($post_id) . '" style="display: none;">';
				$html[] = 	'<div class="wwcAmzAffPriceSelectWrapper" data-post_id="' . ($post_id) . '">';
				
				$buttons = $this->build_buttons();
				$html[] = $buttons;
				
				$html[] = 		'<div id="wwcAmzAffPriceSelect">';
				$html[] = 			'<label>' . 'Amazon Price Response' . '</label>';
	
				$boxContent = $this->get_box_content( $post_id, $amzResp );
				$html[] = implode(PHP_EOL, $boxContent);
	
				$html[] = 		'</div>';
				
				$buttons = $this->build_buttons();
				$html[] = $buttons;
	
				$html[] = 	'</div>';
				$html[] = '</div>';
				// end html
			
			} // end // products loop!
			if ( empty($html) ) return;
			
			$ret = implode( PHP_EOL, $html );
			if ( $is_post_edit ) {
				echo $ret;
			} else {
				return $ret;
			}
		}
		
		public function build_buttons() {
			ob_start();
		?>
			<div class="wwcAmzAffPriceSelect-buttons">
				<a href="#" class="save button button-primary button-large" data-btn="manual">Save prices</a>
				<a href="#" class="cancel button button-secondary button-large" data-btn="auto">Cancel</a>
			</div>
		<?php
			$ret = ob_get_clean();
			return $ret;
		}
		
		// recursive function!
		public function get_box_content( $post_id, $response, $resp_key='', $ancestry=array() )
		{
			static $_post_id, $arr, $ancestry, $allowedTagPriceRegexp, $allowedTags;
			if ( $_post_id != $post_id ) {
				$arr = array();
				$ancestry = array();
				$allowedTagPriceRegexp = '/price/iu';
				$allowedTags = array(
					'ItemAttributes' => array(),
					'Offers' => array(
						'Offer' => array(
							'Merchant' => array(
								'Name' => 1
							),
							'OfferListing' => array(
								'PercentageSaved' => 1,
								'Availability' => 1,
								'IsEligibleForSuperSaverShipping' => 1,
							)
						)
					),
					'OfferSummary' => array(),
					'VariationSummary' => array(),
				);
				$_post_id = $post_id;
			}
			
			if ( empty($response) ) {
				return false;
			}
			if ( is_array($response) ) {
				$isPriceTag = preg_match($allowedTagPriceRegexp, $resp_key);
				if ( !empty($isPriceTag) ) {
					$price = array(
						'Amount'				=> isset($response['Amount']) ? $response['Amount'] : '',
						'FormattedPrice'		=> isset($response['FormattedPrice']) ? $response['FormattedPrice'] : '',
						'CurrencyCode'			=> isset($response['CurrencyCode']) ? $response['CurrencyCode'] : '',
					);
					
					if ( !empty($price['Amount']) ) {
						$price_attr = array(
							'id' 			=> $post_id . '-' . $this->build_ancestry($ancestry, '-'),
							'ancestry' 		=> $this->build_ancestry($ancestry, ','),
							'value' 		=> $price['Amount'] * 0.01,
						);
						$price_attr['value'] = number_format($price_attr['value'], 2, '.', '');

						// wwcAmzAffPriceSelect-Ancestry-' . $resp_key . '
						$arr[] = '<ul class="wwcAmzAff-priceselect-price">';
						$arr[] = 	'<li>';
						$arr[] = 		(string) $price['FormattedPrice'];
						$arr[] = 		'<span data-price="' . $price_attr['value'] . '" data-currency="' . $price['CurrencyCode'] . '" data-ancestry="' . $price_attr['ancestry'] . '">';
						$arr[] = 			'<label for="wwcAmzAff-price-regular-'.($price_attr['id']).'" class="wwcAmzAff-price-regular">' . 'regular' . '</label><input type="radio" id="wwcAmzAff-price-regular-'.($price_attr['id']).'" name="wwcAmzAff-price-regular['.($post_id).']" class="wwcAmzAff-price-regular" />';
						$arr[] = 			'<label for="wwcAmzAff-price-sale-'.($price_attr['id']).'" class="wwcAmzAff-price-sale">' . 'sale' . '</label><input type="radio" id="wwcAmzAff-price-sale-'.($price_attr['id']).'" name="wwcAmzAff-price-sale['.($post_id).']" class="wwcAmzAff-price-sale" />';
						$arr[] = 		'</span>';
						$arr[] = 	'</li>';
						$arr[] = '</ul>';
					}
					return false;	
				}
		
				foreach ($response as $key => $tag) {
					if ( empty($tag) ) continue 1;

					$ancestry["$key"] = 1;
										
					$isPriceTag = preg_match($allowedTagPriceRegexp, $key);
					if ( empty($isPriceTag) && !$this->verify_ancestry($ancestry, $allowedTags) ) {
						unset($ancestry["$key"]);
						continue 1;
					}

					$arr[] = '<ul class="wwcAmzAffPriceSelect-Ancestry-' . $key . '">';

					if ( is_array($tag) ) {
						$arr[] = '<label>' . $this->nice_name($key) . '</label>';

						$arr[] = '<li>';
						$this->get_box_content($post_id, $tag, $key, $ancestry);
						$arr[] = '</li>';

					} else {

						$arr[] = '<li><label>' . $this->nice_name($key) . '</label>: ' . $this->convert_tag_value( $tag ) . '</li>';
					}

					unset($ancestry["$key"]);

					$arr[] = '</ul>';
				}
			}
			return $arr;
		}

		private function nice_name( $name ) {
			$name = (string) $name;
			$name = preg_replace('/([A-Z])/', ' $1', $name);
			$name = trim($name);
			return $name;
		}
		
		private function convert_tag_value( $tag ) {
			if ( (string) $tag == '1' ) {
				return 'yes';
			} else if ( (string) $tag == '1' ) {
				return 'no';
			}
			return (string) $tag;
		}

		private function build_ancestry( $ancestry=array(), $sep='-' ) {
			if ( empty($ancestry) || !is_array($ancestry) ) return '';
			
			$ret = array();
			foreach ($ancestry as $key => $val) {
				$ret[] = $key;
			}
			return implode($sep, $ret);
		}

		private function verify_ancestry( $ancestry=array(), $allowed=array() ) {
			if ( empty($ancestry) || !is_array($ancestry) ) return true;
			if ( empty($allowed) || !is_array($allowed) ) return true;
			
			$current = $allowed;
			foreach ($ancestry as $key => $val) {
				if ( !isset($current["$key"]) ) return false;
				$current = $current["$key"];
			}
			return true;
		}
		
		public function save_post() {
			global $post;
			
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
      			return;
			
			$post_id = isset($post->ID) && (int) $post->ID > 0 ? $post->ID : 0;
			
			if ( empty($post_id) || !$this->the_plugin->verify_product_isamazon($post_id) )
				return;
			
			$req = array(
				'price'			=> isset($_REQUEST['wwcAmzAff-price']) ? $_REQUEST['wwcAmzAff-price'] : array()
			);
			extract($req);
			if ( empty($price) || !is_array($price) )
				return;

			//$priceTypes = isset($price["$post_id"]) ? $price["$post_id"] : array();
			foreach ( $price as $id => $priceTypes ) {

				$_meta = array('regular' => array(), 'sale' => array());

				if ( !empty($priceTypes) && is_array($priceTypes) ) {
					foreach ( $priceTypes as $priceType => $priceValues ) {
						
						if ( !empty($priceValues) && is_array($priceValues) ) {
							foreach ( $priceValues as $metaKey => $metaValue ) {
								
								$metaValue = (string) trim($metaValue);
								if ( in_array($metaKey, array('auto')) ) continue 1;
								if ( $metaKey == 'current' && empty($metaValue) ) $metaValue = 'auto';
								$_meta["$priceType"]["$metaKey"] = $metaValue;
							}
						}
					}
				}

				foreach ($_meta as $key => $val) {
					if ( empty($val) ) continue 1;
					
					$this->the_plugin->amzHelper->productPriceSetRegularSaleMeta($id, $key, $val);
				}
			}
		}
		
		public function ajax_request()
		{
			$html = array();
			$ret = array(
				'status' => 'invalid',
				'html'	=> implode( PHP_EOL, $html )
			);

			$req = array(
				'post_id'		=> isset($_REQUEST['post_id']) && (int) $_REQUEST['post_id'] > 0 ? (int) $_REQUEST['post_id'] : 0,
				'price'			=> isset($_REQUEST['wwcAmzAff-price']) ? $_REQUEST['wwcAmzAff-price'] : array(),
				'whatType'		=> isset($_REQUEST['whatType']) ? (string) $_REQUEST['whatType'] : 'both',
				'operation'		=> isset($_REQUEST['operation']) ? (string) $_REQUEST['operation'] : 'auto',
			);
			extract($req);
  
			if ( empty($post_id) || !$this->the_plugin->verify_product_isamazon($post_id) )
				die( json_encode( $ret ) );
			
			if ( empty($price) || !is_array($price) )
				die( json_encode( $ret ) );

			$priceTypes = isset($price["$post_id"]) ? $price["$post_id"] : array();
			if ( 1 ) {
				
				$_meta = array('regular' => array(), 'sale' => array());
  
				if ( !empty($priceTypes) && is_array($priceTypes) ) {
					foreach ( $priceTypes as $priceType => $priceValues ) {
						
						if ( $whatType != 'both' && $priceType != $whatType ) continue 1;
						
						if ( !empty($priceValues) && is_array($priceValues) ) {
							foreach ( $priceValues as $metaKey => $metaValue ) {
								
								$metaValue = (string) trim($metaValue);
								//if ( in_array($metaKey, array('auto')) ) continue 1;
								if ( $metaKey == 'current' && empty($metaValue) ) $metaValue = 'auto';
								$_meta["$priceType"]["$metaKey"] = $metaValue;
							}
						}
					}
				}
				
				foreach ($_meta as $key => $val) {
					if ( empty($val) ) continue 1;
					
					$this->the_plugin->amzHelper->productPriceSetRegularSaleMeta($post_id, $key, $val);
				}
				$_updatedPrices = $this->_update_woo_prices( $post_id, $_meta, $whatType, $operation );
			}
  
			die( json_encode(array(
				'status'	 => 'valid',
				'html'		 => implode( "\n", $html ),
				'prices'	 => $_updatedPrices
			)) );
		}
			
		private function _update_woo_prices( $post_id, $_amzaff_price, $whatType, $operation ) {
			$ret = array(
				'_regular_price'			=> '',
				'_sale_price'				=> '',
				'_price'					=> '',
			);
			
			switch ( $whatType ) {
				case 'regular':
					$ret = array_merge($ret, array(
						'_regular_price'			=> $_amzaff_price["$whatType"]["$operation"],
						'_sale_price'				=> get_post_meta( $post_id, '_sale_price', true),
					));
					break;
					
				case 'sale':
					$ret = array_merge($ret, array(
						'_regular_price'			=> get_post_meta( $post_id, '_regular_price', true),
						'_sale_price'				=> $_amzaff_price["$whatType"]["$operation"],
					));
					break;
					
				case 'both':
					$ret = array_merge($ret, array(
						'_regular_price'			=> $_amzaff_price["regular"]["$operation"],
						'_sale_price'				=> $_amzaff_price["sale"]["$operation"],
					));
					break;
				
				default:
					break;
			}
			
			$ret['_price'] = (isset($ret['_sale_price']) && !empty($ret['_sale_price']) && $ret['_sale_price'] < $ret['_regular_price'] ? $ret['_sale_price'] : $ret['_regular_price']);
			
			foreach ($ret as $key => $val) {
				update_post_meta($post_id, $key, $val);
			}
			return $ret;
		}
		
		
		/**
		 * Post Type Columns
		 */
		private function page_list_buttons( $post_id ) {
			$_regular_price = get_post_meta( $post_id, '_regular_price', true );
			$_sale_price = get_post_meta( $post_id, '_sale_price', true );
			
			$html[] = '
			<div class="wwcAmzAffPriceSelectButtons" data-post_id="' . ($post_id) . '">
				<ul>
					<li class="_wwcAmzAff_regular_price_field">
						<label for="_wwcAmzAff_regular_price">Regular Price:</label>
						<input id="_wwcAmzAff_regular_price" class="_wwcAmzAff_regular_price short wc_input_price" type="text" placeholder="" name="_wwcAmzAff_regular_price" value="' . $_regular_price . '" readonly="readonly">
					</li>
					<li class="_wwcAmzAff_sale_price_field">
						<label for="_wwcAmzAff_sale_price">Sale Price:</label>
						<input id="_wwcAmzAff_sale_price" class="_wwcAmzAff_sale_price short wc_input_price" type="text" placeholder="" name="_wwcAmzAff_sale_price" value="' . $_sale_price . '" readonly="readonly">
					</li>
				</ul>
			</div>';
			
			return implode(PHP_EOL, $html);
		}

		public function _edit_columns($columns) {
			$new_columns['wwcAmzAff_price'] 			= __('wwcAmzAff Price', $this->the_plugin->localizationName);
		
			//return $new_columns;
		    return array_merge( $columns, $new_columns );
		}
		
		public function _posts_columns($column_name, $id) {
		    global $id, $wpdb;

			$post_id = $id;

		    switch ($column_name) {

				case 'wwcAmzAff_price':

					if ( empty($post_id) ) break;
					
					$isProdValid = $this->is_prod_valid($post_id);
					if ( empty($isProdValid) ) break;
					
					$isProdVariation = $this->the_plugin->verify_product_isvariation($post_id);
					if ( $isProdVariation ) break;
						
					$amzResp = get_post_meta($post_id, '_amzaff_amzRespPrice', true);
					if ( empty($amzResp) ) break;

  					$html[] = $this->add_product_inline( $post_id );
					$html[] = $this->page_list_buttons( $post_id );
					echo implode(PHP_EOL, $html);
					break;
					
		        default:
		            break;
		    } // end switch
		}
        
        public function get_post_column($id) {
            global $wpdb;

            $post_id = $id;

            if ( empty($post_id) ) return '';
                    
            $isProdValid = $this->is_prod_valid($post_id);
            if ( empty($isProdValid) ) return '';
                    
            $isProdVariation = $this->the_plugin->verify_product_isvariation($post_id);
            if ( $isProdVariation ) return '';
                        
            $amzResp = get_post_meta($post_id, '_amzaff_amzRespPrice', true);
            if ( empty($amzResp) ) return '';

            $html[] = $this->add_product_inline( $post_id );
            $html[] = $this->page_list_buttons( $post_id );
            return implode(PHP_EOL, $html);
        }
		
		public function css_page_list() {
			ob_start();
            //wwcAmzAff_price
?>
<style type='text/css'>
	th#wwcAmzAff_product_info { width: 170px; }
	
	.wwcAmzAff_product_info .wwcAmzAffPriceSelectButtons {
	}
	.wwcAmzAff_product_info .wwcAmzAffPriceSelectButtons ul {
		display: inline-block;
		margin: 4px 0px;
	}
	.wwcAmzAff_product_info .wwcAmzAffPriceSelectButtons ul li {
		display: inline-block;
	}
	.wwcAmzAff_product_info .wwcAmzAffPriceSelectButtons ul li > label {
		display: inline-block;
		width: 85px;
	}
	.wwcAmzAff_product_info .wwcAmzAffPriceSelectButtons input {
		width: 70px;
	}
	.wwcAmzAff_product_info .wwcAmzAffPriceSelectButtons .wwcAmzAff-priceselect-wrapper {
		float: none;
		margin-left: 0px;
	}
</style>
<?php
			//";
			$content = ob_get_contents();
			ob_end_clean();
			//echo $content;
			return $content;
		}
		
	    public function __instanceActions() {
			// change the layout
	    	/*$screens = array('product');
		    foreach ($screens as $screen) {
				add_filter( 'manage_edit-' . $screen . '_columns', array( &$this, '_edit_columns' ), 10, 1 );
				//add_filter( 'manage_' . $screen . '_posts_columns', array( $this, '_edit_columns' ), 10, 1 );
				add_action( 'manage_' . $screen . '_posts_custom_column', array( $this, '_posts_columns' ), 10, 2 );
		    }*/
	    }

		/**
	    * Singleton pattern
	    *
	    * @return wwcAmzAffPriceSelect Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }
  
			if ( self::$_instance->the_plugin->is_admin === true ) {
				add_action( 'admin_init', array( self::$_instance, '__instanceActions' ) );
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
    }
}

// Initialize the wwcAmzAffPriceSelect class
$wwcAmzAffPriceSelect = wwcAmzAffPriceSelect::getInstance();