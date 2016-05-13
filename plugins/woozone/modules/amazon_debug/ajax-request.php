<?php 
if (class_exists('_wwcAmzAffAmazonDebugUtils') != true) {
    class _wwcAmzAffAmazonDebugUtils
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';
		
		static protected $_instance;
		

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct() {
		}
		
		public function escape($str) {
			return preg_replace("!([\b\t\n\r\f\"\\'])!", "\\\\\\1", $str);
		}

		// php.net / bohwaz / This is intended to be a simple readable json encode function for PHP 5.3+ (and licensed under GNU/AGPLv3 or GPLv3 like you prefer)
		public function json_readable_encode($in, $indent = 0, $from_array = false) {
		    $out = '';
		
		    foreach ($in as $key=>$value)
		    {
		        $out .= str_repeat("\t", $indent + 1);
		        $out .= "\"".$this->escape((string)$key)."\": ";
		
		        if (is_object($value) || is_array($value))
		        {
		            $out .= "\n";
		            $out .= $this->json_readable_encode($value, $indent + 1);
		        }
		        elseif (is_bool($value))
		        {
		            $out .= $value ? 'true' : 'false';
		        }
		        elseif (is_null($value))
		        {
		            $out .= 'null';
		        }
		        elseif (is_string($value))
		        {
		            $out .= "\"" . $this->escape($value) ."\"";
		        }
		        else
		        {
		            $out .= $value;
		        }
		
		        $out .= ",\n";
		    }
		
		    if (!empty($out))
		    {
		        $out = substr($out, 0, -2);
		    }
		
		    $out = str_repeat("\t", $indent) . "{\n" . $out;
		    $out .= "\n" . str_repeat("\t", $indent) . "}";
		
		    return $out;
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
  
	        return self::$_instance;
	    }
    }
}
// Initialize the _wwcAmzAffAmazonDebugUtils class
//$_wwcAmzAffAmazonDebugUtils = _wwcAmzAffAmazonDebugUtils::getInstance();


add_action('wp_ajax_wwcAmzAffAmazonDebugGetResponse', 'wwcAmzAffAmazonDebugGetResponse');
function wwcAmzAffAmazonDebugGetResponse() {
	$html = array();
	$ret = array(
		'status' => 'invalid',
		'html'	=> implode( PHP_EOL, $html )
	);
   
	$req = array(
		'asin'	=> isset($_REQUEST['asin']) ? (string) $_REQUEST['asin'] : 0,
		'rg'	=> isset($_REQUEST['rg']) ? $_REQUEST['rg'] : 'ItemAttributes,Large,OfferFull,PromotionSummary,Variations',
	);
	extract($req);
	
	global $wwcAmzAff;
	//$wwcAmzAff->amzHelper->setupAmazonWS();

	// create new amazon instance
	$aaAmazonWS = $wwcAmzAff->amzHelper->aaAmazonWS;
  
	// create request by ASIN
	// Large,ItemAttributes,Offers,OfferSummary,OfferFull,Variations,VariationOffers,Reviews,PromotionSummary
	$product = $aaAmazonWS->responseGroup($rg)
		->optionalParameters(array('MerchantId' => 'All'))
		->lookup($asin);
  
  	//global $_wwcAmzAffAmazonDebugUtils;
  	$_wwcAmzAffAmazonDebugUtils = _wwcAmzAffAmazonDebugUtils::getInstance();
	//$product = $_wwcAmzAffAmazonDebugUtils->json_readable_encode( $product );
	$product = json_encode(	$product ); 
	
	die( json_encode(array(
		'status' => 'valid',
		'html' => $product
	)) );
}