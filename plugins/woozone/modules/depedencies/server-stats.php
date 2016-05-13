<?php
// soap
if (extension_loaded('soap')) {
?>
<div class="wwcAmzAff-message wwcAmzAff-success">
	SOAP extension installed on server
</div>
<?php
}else{
?>
<div class="wwcAmzAff-message wwcAmzAff-error">
	SOAP extension not installed on your server, please talk to your hosting company and they will install it for you.
</div>
<?php
}

// Woocommerce
if( class_exists( 'Woocommerce' ) ){
?>
<div class="wwcAmzAff-message wwcAmzAff-success">
	 WooCommerce plugin installed
</div>
<?php
}else{
?>
<div class="wwcAmzAff-message wwcAmzAff-error">
	WooCommerce plugin not installed, in order the product to work please install WooCommerce wordpress plugin.
</div>
<?php
}

// curl
if ( function_exists('curl_init') ) {
?>
<div class="wwcAmzAff-message wwcAmzAff-success">
	cURL extension installed on server
</div>
<?php
}else{
?>
<div class="wwcAmzAff-message wwcAmzAff-error">
	cURL extension not installed on your server, please talk to your hosting company and they will install it for you.
</div>
<?php
}
?>
<?php
