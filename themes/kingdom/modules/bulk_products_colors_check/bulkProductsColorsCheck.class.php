<?php 
/*
* Define class Modules Manager List
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
! defined( 'ABSPATH' ) and exit;

if(class_exists('bulkProductsColorsCheck') != true) {

	class bulkProductsColorsCheck {
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

		/*
		* Required __construct() function that initalizes the AA-Team Framework
		*/
		public function __construct($cfg, $module) 
		{
			$this->cfg = $cfg;
			$this->module = $module;
		}

		public function printListInterface ()
		{
			global $kingdom;
			$amazon_settings = $kingdom->getAllSettings('array', 'amazon');
			
			$html = array();
			$html[] = '<script type="text/javascript" src="' . ( $this->module['folder_uri'] ) . 'bulk.js" ></script>';
			$html[] = "<link rel='stylesheet' id='kingdom-bulk-css' href='" . ( $this->module['folder_uri'] ) . "extra-style.css' type='text/css' media='all' />";

			$html[] = '<div id="kingdom-bulkimport">';
			$html[] = 	'</form><form id="kingdom-search-form" action="/" method="POST">';
			$html[] = 	'<div style="bottom: 0px; top: 0px;" class="kingdom-shadow"></div>';
			$html[] = 		'<div id="kingdom-search-bar">';
			$html[] = 			'<div class="kingdom-search-content">';
			$html[] = 				'<div class="kingdom-search-block" style="width:350px;">&nbsp;';
			$html[] = 					'<span class="caption">Filter by category:</span>';
										ob_start();
										wc_product_dropdown_categories(array(), 1, 1, 0 );
			$html[] = 					ob_get_clean();
			
			$html[] = 				'</div>';
			
			
			$html[] = 				'<input type="submit" class="button-primary" id="kingdom-search-link" value="Show products" />';
			$html[] = 		'</form>';
			$html[] = 		'<div id="kingdom-ajax-loader"><img src="'. ( $this->module['folder_uri'] ) .'assets/ajax-loader.gif" /> loading products</div>';
			$html[] = 	'</div>';
			
			$html[] = '</div>';
	
			$html[] = '<div id="kingdom-search-bar" class="kingdom-import-bar" style="display:none">';
			$html[] = 	'<div id="kingdom-import-status">';
			$html[] = 		'<div style="float: left;width: 150px; ">';
			$html[] = 			'<span id="kingdom-status-ready">0</span> product(s) add. <br />';
			$html[] = 			'From <span id="kingdom-status-remaining">0</span> total product(s) <br />';
			$html[] = 		'</div>';
			$html[] = 		'<div style="float: left;width: 550px;">';
			$html[] = 			'<div id="progress_bar" class="ui-progress-bar ui-container">';
			$html[] = 				'<div class="ui-progress" style="width: 0%;">';
			$html[] = 					'<span class="ui-label"><b class="value">0%</b></span>';
			$html[] = 				'</div>';
			$html[] = 			'</div>';
			$html[] = 		'</div>';
			$html[] =	'</div>';
		
			$html[] = 	'<div class="kingdom-search-content">';
			$html[] = 		'<a href="#" class="button-primary" id="kingdom-import-btn"> Process all selected products! </a>';
			$html[] = 	'</div>';
			$html[] = '</div>';
			
			$html[] = '<div id="kingdom-results">';
			$html[] = 	'<div id="kingdom-ajax-results"><!-- dynamic content here --></div>';
			$html[] = 	'<div style="clear:both;"></div>';
			$html[] = '</div>';
			$html[] = '</div>';
			
			return implode("\n", $html);
		}
	}
}

// Initalize the your bulkProductsColorsCheck
$bulkProductsColorsCheck = new bulkProductsColorsCheck($this->cfg, $module);