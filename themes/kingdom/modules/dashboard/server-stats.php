<?php
// soap
if (extension_loaded('soap')) {
?>
<div class="kingdom-message kingdom-success">
	SOAP extension installed on server
</div>
<?php
}else{
?>
<div class="kingdom-message kingdom-error">
	SOAP extension not installed on your server, please talk to your hosting company and they will install it for you.
</div>
<?php
}

// Woocommerce
if( class_exists( 'Woocommerce' ) ){
?>
<div class="kingdom-message kingdom-success">
	 WooCommerce plugin installed
</div>
<?php
}else{
?>
<div class="kingdom-message kingdom-error">
	WooCommerce plugin not installed, in order the product to work please install WooCommerce wordpress plugin.
</div>
<?php
}

// curl
if ( function_exists('curl_init') ) {
?>
<div class="kingdom-message kingdom-success">
	cURL extension installed on server
</div>
<?php
}else{
?>
<div class="kingdom-message kingdom-error">
	cURL extension not installed on your server, please talk to your hosting company and they will install it for you.
</div>
<?php
}
?>
<?php
