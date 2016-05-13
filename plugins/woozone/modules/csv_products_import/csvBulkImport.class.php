<?php
/*
* Define class Modules Manager List
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
! defined( 'ABSPATH' ) and exit;

if(class_exists('csvBulkImport') != true) {

	class csvBulkImport {
		/*
		* Some required plugin information
		*/
		const VERSION = '1.0';

		/*
		* Store some helpers config
		*
		*/
		public $cfg	= array();
		public $module	= array();
		public $networks	= array();
		public $the_plugin = null;

		/*
		* Required __construct() function that initalizes the AA-Team Framework
		*/
		public function __construct($cfg, $module)
		{
			global $wwcAmzAff;
			
			$this->the_plugin = $wwcAmzAff;
			$this->cfg = $cfg;
			$this->module = $module;
		}
		
		public function moduleValidation() {
			$ret = array(
				'status'			=> false,
				'html'				=> ''
			);
			
			// AccessKeyID, SecretAccessKey, AffiliateId, main_aff_id
			
			// find if user makes the setup
			$module_settings = $this->the_plugin->getAllSettings('array', 'amazon');

			$module_mandatoryFields = array(
				'AccessKeyID'			=> false,
				'SecretAccessKey'		=> false,
				'main_aff_id'			=> false
			);
			if ( isset($module_settings['AccessKeyID']) && !empty($module_settings['AccessKeyID']) ) {
				$module_mandatoryFields['AccessKeyID'] = true;
			}
			if ( isset($module_settings['SecretAccessKey']) && !empty($module_settings['SecretAccessKey']) ) {
				$module_mandatoryFields['SecretAccessKey'] = true;
			}
			if ( isset($module_settings['main_aff_id']) && !empty($module_settings['main_aff_id']) ) {
				$module_mandatoryFields['main_aff_id'] = true;
			}
			$mandatoryValid = true;
			foreach ($module_mandatoryFields as $k=>$v) {
				if ( !$v ) {
					$mandatoryValid = false;
					break;
				}
			}
			if ( !$mandatoryValid ) {
				$error_number = 1; // from config.php / errors key
				
				$ret['html'] = $this->the_plugin->print_module_error( $this->module, $error_number, 'Error: Unable to use CSV Bulk Import module, yet!' );
				return $ret;
			}
			
			if( !$this->the_plugin->is_woocommerce_installed() ) {  
				$error_number = 2; // from config.php / errors key
				
				$ret['html'] = $this->the_plugin->print_module_error( $this->module, $error_number, 'Error: Unable to use Advanced Search module, yet!' );
				return $ret;
			}
			
			if( !extension_loaded('soap') ) {  
				$error_number = 3; // from config.php / errors key
				
				$ret['html'] = $this->the_plugin->print_module_error( $this->module, $error_number, 'Error: Unable to use Advanced Search module, yet!' );
				return $ret;
			}

			if( !(extension_loaded("curl") && function_exists('curl_init')) ) {  
				$error_number = 4; // from config.php / errors key
				
				$ret['html'] = $this->the_plugin->print_module_error( $this->module, $error_number, 'Error: Unable to use Advanced Search module, yet!' );
				return $ret;
			}
			
			$ret['status'] = true;
			return $ret;
		}

		public function printListInterface ()
		{
			global $wwcAmzAff;

			// find if user makes the setup
			$moduleValidateStat = $this->moduleValidation();
			if ( !$moduleValidateStat['status'] || !is_object($this->the_plugin->amzHelper) || is_null($this->the_plugin->amzHelper) )
				echo $moduleValidateStat['html'];
			else{
				
        	/*if ( !is_object($this->the_plugin->amzHelper) || is_null($this->the_plugin->amzHelper) ) {
        		$html = array();
        		$html[] = '<div class="wwcAmzAff-message blue">You need to set the Access Key ID, Secret Access Key and Your Affiliate IDs first!</div>';
				return implode('\n', $html);
        	} else {*/

			$amazon_settings = $wwcAmzAff->getAllSettings('array', 'amazon');
        		
			$html = array();
			$html[] = 	'<style>#wwcAmzAff-csvBulkImport { display: block } </style>';
			$html[] = '<script type="text/javascript" src="' . ( $this->module['folder_uri'] ) . 'bulk.js" ></script>';
			$html[] = "<link rel='stylesheet' id='wwcAmzAff-bulk-css' href='" . ( $this->module['folder_uri'] ) . "extra-style.css' type='text/css' media='all' />";

			$html[] = '<div id="wwcAmzAff-csvBulkImport">';
			$html[] = 	'<div id="wwcAmzAff-csvBulkImport-left-panel">';

			$html[] = 	'<h3>ASIN codes:</h3>';
			$html[] = 	'<textarea id="wwcAmzAff-csv-asin"></textarea>';

			$html[] = 	'<div class="wwcAmzAff-delimiters">';
			$html[] = 		'<h3>ASIN delimiter by:</h3>';
			$html[] = 		'<input id="wwcAmzAff-csv-radio-newline" type="radio" class="wwcAmzAff-csv-radio" checked name="wwcAmzAff-csv-delimiter" val="newline" /><label for="wwcAmzAff-csv-radio-newline">New line <code>\n</code></label>';
			$html[] = 		'<input id="wwcAmzAff-csv-radio-comma" type="radio" name="wwcAmzAff-csv-delimiter" val="comma" /><label for="wwcAmzAff-csv-radio-comma">Comma <code>,</code></label>';
			$html[] = 		'<input id="wwcAmzAff-csv-radio-tab" type="radio" name="wwcAmzAff-csv-delimiter" val="tab" /><label for="wwcAmzAff-csv-radio-tab">TAB <code>TAB</code></label>';
			$html[] = 		'<div style="clear:both;"></div>';
			$html[] = 	'</div>';

			$html[] = 	'<div class="amzStore-delimiters">';
			$html[] = 		'<h3>Import to category:</h3>';

			$args = array(
				'orderby' 	=> 'menu_order',
				'order' 	=> 'ASC',
				'hide_empty' => 0,
				'post_per_page' => '-1'
			);
			$categories = get_terms('product_cat', $args);
			  
			$args = array(
				'show_option_all'    => '',
				'show_option_none'   => 'Use category from Amazon',
				'orderby'            => 'ID', 
				'order'              => 'ASC',
				'show_count'         => 0,
				'hide_empty'         => 0, 
				'child_of'           => 0,
				'exclude'            => '',
				'echo'               => 0,
				'selected'           => 0,
				'hierarchical'       => 1, 
				'name'               => 'wwcAmzAff-to-category',
				'id'                 => 'wwcAmzAff-to-category',
				'class'              => 'postform',
				'depth'              => 0,
				'tab_index'          => 0,
				'taxonomy'           => 'product_cat',
				'hide_if_empty'      => false,
			);
			$html[] = wp_dropdown_categories( $args );
			$html[] = 		'<div style="clear:both;"></div>';
			$html[] = 	'</div>';

			$html[] = 	'<a href="#" class="wwcAmzAff-button blue" id="wwcAmzAff-addASINtoQueue">Add ASIN codes to Queue</a>';
			$html[] = 	'</div>';
			$html[] = 	'<div id="wwcAmzAff-csvBulkImport-right-panel">';
			$html[] = 	'<div id="wwcAmzAff-csvBulkImport-queue-response" style="display:none">';
			$html[] = 	'<table class="wwcAmzAff-table" style="border-collapse: collapse;">';
			$html[] = 		'<thead>';
			$html[] = 			'<tr>';
			$html[] = 				'<th width="150">ASINs</th>';
			$html[] = 				'<th>Status</th>';
			$html[] = 			'</tr>';
			$html[] = 		'</thead>';
			$html[] = 		'<tbody id="wwcAmzAff-print-response">';
			$html[] = 		'</tbody>';
			$html[] = 	'</table>';

			$html[] = 	'<a href="#" class="wwcAmzAff-button orange" id="wwcAmzAff-startImportASIN">Start import all</a>';
			$html[] = 	'<div class="wwcAmzAff-status-block">Importing product(s) ...<span id="wwcAmzAff-status-ready">0</span> ready, <span id="wwcAmzAff-status-remaining">0</span> remaining</div>';

			$html[] = 	'</div>';
			$html[] = 	'<p id="wwcAmzAff-no-ASIN" class="wwcAmzAff-message wwcAmzAff-info"><em>Please first add some ASIN codes to Queue!</em></p>';
			$html[] = 	'</div>';
			$html[] = 	'<div style="clear:both;"></div>';
			$html[] = '</div>';

			return implode("\n", $html);
			
			}
		}
	}
}

// Initalize the your csvBulkImport
$csvBulkImport = new csvBulkImport($this->cfg, $module);