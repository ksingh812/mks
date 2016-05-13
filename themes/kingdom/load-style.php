<?php
if ( !defined('ABSPATH') ) {
	$absolute_path = __FILE__;
	$path_to_file = explode( 'wp-content', $absolute_path );
	$path_to_wp = $path_to_file[0];

	/** Set up WordPress environment */
	if( file_exists( $path_to_wp.'/wp-load.php' ) ) {
		require_once( $path_to_wp.'/wp-load.php' );
	}
	else{
		require_once( '../../../wp-load.php' );
	}
	
	global $kingdom;
	
	$kingdom->coreFunctions->update_settings();
	
	$cssFiles = array( $kingdom->cfg['paths']['theme_dir_path'] . 'style.css' );
	if( file_exists( get_template_directory() . '-child/style.css' ) ) {
		$cssFiles[] = get_template_directory() . '-child/style.css';
	}
	
	$buffer = "";
	foreach ($cssFiles as $cssFile) {
		$buffer .= file_get_contents($cssFile);
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']['primary_color']) && trim($kingdom->coreFunctions->settings['layout']['primary_color']) != "" && $kingdom->coreFunctions->settings['layout']['primary_color'] != strtolower('#C5403F') ){
		$buffer = str_replace( array( strtolower("#C5403F"), "#C5403F" ), strtoupper($kingdom->coreFunctions->settings['layout']['primary_color']), $buffer);
		$buffer = str_replace( array( strtolower("#A21312"), "#A21312" ), $kingdom->coreFunctions->adjustBrightness( strtoupper($kingdom->coreFunctions->settings['layout']['primary_color']), -30 ), $buffer);
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']['secondary_color']) && trim($kingdom->coreFunctions->settings['layout']['secondary_color']) != "" && $kingdom->coreFunctions->settings['layout']['secondary_color'] != strtolower('#2980B9') ){
		$buffer = str_replace( array( strtolower("#2980B9"),"#2980B9" ) , strtoupper($kingdom->coreFunctions->settings['layout']['secondary_color']), $buffer);
		$buffer = str_replace( array( strtolower("#196EA6"), "#196EA6" ), $kingdom->coreFunctions->adjustBrightness( strtoupper($kingdom->coreFunctions->settings['layout']['secondary_color']), -30 ), $buffer);
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']["menu_normal_state_color"]) ){
		$buffer .= 'body .navigationbar .kd_main_menu li a { color: '.$kingdom->coreFunctions->settings['layout']["menu_normal_state_color"].';}';
		$buffer .= 'body .navigationbar .kd_main_menu .sub-menu { box-shadow: 0 3px 0 0 '.$kingdom->coreFunctions->settings['layout']["menu_normal_state_color"].' inset; }';
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']["menu_hover_state_color"]) ){
		$buffer .= 'body .navigationbar .kd_main_menu li a:hover, body .navigationbar .kd_main_menu > li.current-menu-item a { color: '.$kingdom->coreFunctions->settings['layout']["menu_hover_state_color"].';}';
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']["menu_background_color"]) ){
		$buffer .= 'body .navigationbar { background-color: '.$kingdom->coreFunctions->settings['layout']["menu_background_color"].';}';
		$buffer .= '.navigationbar .kd_main_menu .sub-menu { background-color: '.$kingdom->coreFunctions->settings['layout']["menu_background_color"].';}';
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']['border_radius']) && $kingdom->coreFunctions->settings['layout']['border_radius'] == 'no' ){
		$pattern = '~border-radius:\s*([^;$]+)~si';
		$buffer = preg_replace($pattern, 'border-radius: 0px !important', $buffer);
	} 
	
	// start
	if( isset($kingdom->coreFunctions->settings['layout']["website_main_font"]) ){
		$buffer = str_replace( "Open Sans", $kingdom->coreFunctions->settings['layout']["website_main_font"], $buffer );
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']["menu_font"]) ){
		$buffer .= 'body .navigationbar .kd_main_menu li a { font-family: '.$kingdom->coreFunctions->settings['layout']["menu_font"].' !important;}';
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']["main_menu_font_size"]) ){
		$buffer .= 'body .navigationbar .kd_main_menu li a { font-size: '.$kingdom->coreFunctions->settings['layout']["main_menu_font_size"].'px !important;}';
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']["main_menu_submenu_font_size"]) ){
		$buffer .= 'body .navigationbar .kd_main_menu li ul li a { font-size: '.$kingdom->coreFunctions->settings['layout']["main_menu_submenu_font_size"].'px !important;}';
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']["main_menu_vertical_spacing"]) ){
		$buffer .= 'body .navigationbar .kd_main_menu > li a { padding-bottom: '.$kingdom->coreFunctions->settings['layout']["main_menu_vertical_spacing"].'px; padding-top: '.$kingdom->coreFunctions->settings['layout']["main_menu_vertical_spacing"].'px;}';
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']["main_menu_horizontal_spacing"]) ){
		$buffer .= 'body .navigationbar .kd_main_menu > li a { padding-left: '.$kingdom->coreFunctions->settings['layout']["main_menu_horizontal_spacing"].'px; padding-right: '.$kingdom->coreFunctions->settings['layout']["main_menu_horizontal_spacing"].'px;}';
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']["main_menu_submenu_top_margin"]) ){
		$buffer .= 'body .navigationbar .kd_main_menu .sub-menu { top: '.$kingdom->coreFunctions->settings['layout']["main_menu_submenu_top_margin"].'px;}';
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']["main_content_font_size"]) ){
		$buffer .= 'body .row p { font-size: '.$kingdom->coreFunctions->settings['layout']["main_content_font_size"].'px !important;}';
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']["h1-font-size"]) ){
		$buffer .= 'body .row h1 { font-size: '.$kingdom->coreFunctions->settings['layout']["h1-font-size"].'px !important;}';
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']["h2-font-size"]) ){
		$buffer .= 'body .row h2 { font-size: '.$kingdom->coreFunctions->settings['layout']["h2-font-size"].'px !important;}';
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']["enable_star_rating"]) && $kingdom->coreFunctions->settings['layout']["enable_star_rating"] == 'false' ){
		$buffer .= 'body .rating-input { display: none !important;}';
	}
	
	if( isset($kingdom->coreFunctions->settings['layout']["enable_partners_module"]) && $kingdom->coreFunctions->settings['layout']["enable_partners_module"] == 'false' ){
		$buffer .= 'body #kd_footer_partners { display: none !important;}';
	}

	if( isset($kingdom->coreFunctions->settings['layout']["enable_product_general_description"]) && $kingdom->coreFunctions->settings['layout']["enable_product_general_description"] == 'false' ){
		$buffer .= 'body #kd_general_description { display: none !important;}';
	}
	
	// end 
	
	// Remove comments
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	 
	// Remove space after colons
	$buffer = str_replace(': ', ':', $buffer);
	 
	// Remove whitespace
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '    ', '    '), '', $buffer);
	 
	// Enable GZip encoding.
	if ( ! ini_get('zlib.output_compression') || 'ob_gzhandler' != ini_get('output_handler') ) ob_start();
	else ob_start("ob_gzhandler");
	 
	// Enable caching
	header('Cache-Control: public');
	 
	// Expire in one day
	header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
	 
	// Set the correct MIME type, because Apache won't set it for us
	header("Content-type: text/css");
	 
	// Write everything out
	echo $buffer;  
	
	// try to write the buffer as .css
	if( !is_file( get_template_directory() . '/load-styles.css' ) ){
		file_put_contents( get_template_directory() . '/load-styles.css', $buffer);
	} 
}