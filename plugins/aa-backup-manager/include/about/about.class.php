<?php
 /**
 * About module
 * http://www.aa-team.com
 * ======================
 *
 * @package		BKM_
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
! defined( 'ABSPATH' ) and exit;

if(class_exists('BKM_about') != true) {
	class BKM_about {
		
		const WXR_VERSION = '1.0';
		
		/**
		 * parent storage
		 *
		 * @var array
		 */
		public $parent = array();
		
		private $action = '';
		private $wp_import = null;
		
		/**
		 * The constructor
		 */
		public function __construct( $parent=array() )
		{
			// load parent
			$this->parent = $parent;
		}
		
		public function print_interface()
		{
			$html = array();
			
			
			$html[] = '<div id="BKM_iw-section-content">
	<h3>About</h3>
	<p>Suspendisse feugiat ut lectus quis facilisis. Proin id vehicula sapien, fringilla mattis enim. Nulla neque libero, mattis eget est at, convallis convallis sem. Nunc quis arcu a ipsum luctus tristique gravida nec sapien. Sed auctor vulputate sollicitudin. Integer suscipit mauris vitae interdum imperdiet. </p>
</div>';
			
			return implode( "\n", $html );
		}
	}
}