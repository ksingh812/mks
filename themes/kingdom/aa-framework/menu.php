<?php
/**
 * AA-Team - http://www.aa-team.com
 * ===============================+
 *
 * @package		kingdomAdminMenu
 * @author		Andrei Dinca
 * @version		1.0
 */
! defined( 'ABSPATH' ) and exit;

if(class_exists('kingdomAdminMenu') != true) {
	class kingdomAdminMenu {
		
		/*
        * Some required plugin information
        */
        const VERSION = '1.0';

        /*
        * Store some helpers config
        */
		public $the_plugin = null;
		private $the_menu = array();
		private $current_menu = '';
		private $ln = '';

		static protected $_instance;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $kingdom;
        	$this->the_plugin = $kingdom;
			$this->ln = $this->the_plugin->localizationName;
			
			// update the menu tree
			$this->the_menu_tree();
			
			return $this;
        }

		/**
	    * Singleton pattern
	    *
	    * @return kingdomDashboard Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
	    }
		
		private function the_menu_tree()
		{
			$this->the_menu['dashboard'] = array( 
				'title' => __( 'Dashboard', $this->ln ),
				'url' => admin_url("admin.php?page=kingdom#!/dashboard"),
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_dashboard.png'
			);
			
			$this->the_menu['layout'] = array( 
				'title' => __( 'Layout', $this->ln ),
				'url' => admin_url("admin.php?page=kingdom#!/layout"),
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_layout.png'
			);
			
			$this->the_menu['sidebars'] = array( 
				'title' => __( 'Sidebars', $this->ln ),
				'url' => "#!/",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_sidebarsection.png',
				'submenu' => array(
					'sidebars' => array(
						'title' => __( 'Sidebars Manager', $this->ln ),
						'url' => admin_url("admin.php?page=kingdom#!/sidebars"),
						'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
						'menu_icon' => 'images/16_sidebars.png'
					),
					
					'sidebars_per_sections' => array(
						'title' => __( 'Sidebars Per Sections', $this->ln ),
						'url' => admin_url("admin.php?page=kingdom#!/sidebars_per_sections"),
						'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
						'menu_icon' => 'images/16_sidebarsec.png',

					),
				)
			);
			
			$this->the_menu['widgets_manager'] = array( 
				'title' => __( 'Widgets Manager', $this->ln ),
				'url' => admin_url("admin.php?page=kingdom#!/widgets_manager"),
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_widgets.png'
			);
			
			$this->the_menu['bulk_products_colors_check'] = array( 
				'title' => __( 'Bulk Colors Check', $this->ln ),
				'url' => admin_url("admin.php?page=kingdom#!/bulk_products_colors_check"),
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_colors.png'
			);
			
			$this->the_menu['general'] = array( 
				'title' => __( 'Plugin Settings', $this->ln ),
				'url' => "#!/",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_pluginsett.png',
				'submenu' => array(
					'modules_manager' => array(
						'title' => __( 'Modules Manager', $this->ln ),
						'url' => admin_url("admin.php?page=kingdom#!/modules_manager"),
						'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
						'menu_icon' => 'images/16_modules.png'
					),
					
					'setup_backup' => array(
						'title' => __( 'Setup / Backup', $this->ln ),
						'url' => admin_url("admin.php?page=kingdom#!/setup_backup"),
						'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
						'menu_icon' => 'images/16_setupbackup.png'
					),
				)
			);
		}
		
		public function show_menu()
		{
			$plugin_data = $this->the_plugin->get_theme_data();
			//var_dump('<pre>',,'</pre>'); die;  
			$html = array();
			// id="kingdom-nav-dashboard" 
			$html[] = '<div id="kingdom-header">';
			$html[] = 	'<div id="kingdom-header-bottom">';
			$html[] = 		'<div id="kingdom-topMenu">';
			$html[] = 			'<a href="http://codecanyon.net/item/woocommerce-amazon-affiliates-wordpress-plugin/3057503?ref=AA-Team" target="_blank" class="kingdom-product-logo">
									<img src="' . ( $this->the_plugin->cfg['paths']['theme_dir_url'] ) . 'thumb.png" alt="">
									<h2>Kingdom Amazon Affiliate Theme</h2>
									<h3>' . ( $plugin_data['version'] ) . '</h3>
									
									<span class="kingdom-rate-now"></span>
									<img src="' . ( $this->the_plugin->cfg['paths']['freamwork_dir_url'] ) . 'images/rate-now.png" class="kingdom-rate-img">
									<img src="' . ( $this->the_plugin->cfg['paths']['freamwork_dir_url'] ) . 'images/star.gif" class="kingdom-rate-gif">
									<strong>Don’t forget to rate us!</strong>
								</a>';
			$html[] = 			'<ul>';
								foreach ($this->the_menu as $key => $value) {
									$iconImg = '<img src="' . ( $value['folder_uri'] . $value['menu_icon'] ) . '" />';
									$html[] = '<li id="kingdom-nav-' . ( $key ) . '" class="kingdom-section-' . ( $key ) . ' ' . ( isset($this->current_menu[0]) && ( $key == $this->current_menu[0] ) ? 'active' : '' ) . '">';
									
									if( $value['url'] == "#!/" ){
										$value['url'] = 'javascript: void(0)';
									}
									$html[] = 	'<a href="' . ( $value['url'] ) . '">' . ( $iconImg ) . '' . ( $value['title'] ) . '</a>';
									if( isset($value['submenu']) ){
										$html[] = 	'<ul class="kingdom-sub-menu">';
										foreach ($value['submenu'] as $kk2 => $vv2) {
											if( ($kk2 != 'synchronization_log') && !in_array( $kk2, array_keys($this->the_plugin->cfg['activate_modules'])) ) continue;
		
											$iconImg = '<img src="' . ( $vv2['folder_uri'] . $vv2['menu_icon'] ) . '" />';
											$html[] = '<li class="kingdom-section-' . ( $kk2 ) . '  ' . ( isset($this->current_menu[1]) && $kk2 == $this->current_menu[1] ? 'active' : '' ) . '" id="kingdom-sub-nav-' . ( $kk2 ) . '">';
											$html[] = 	$iconImg;
											$html[] = 	'<a href="' . ( $vv2['url'] ) . '">' . ( $vv2['title'] ) . '</a>'; 
											
											if( isset($vv2['submenu']) ){
												$html[] = 	'<ul class="kingdom-sub-sub-menu">';
												foreach ($vv2['submenu'] as $kk3 => $vv3) {
													$html[] = '<li id="kingdom-sub-sub-nav-' . ( $kk3 ) . '">';
													$html[] = 	'<a href="' . ( $vv3['url'] ) . '">' . ( $vv3['title'] ) . '</a>';
													$html[] = '</li>';
												}
												$html[] = 	'</ul>';
											}
											$html[] = '</li>';
										}
										$html[] = 	'</ul>';
									}
									$html[] = '</li>';
								}
			$html[] = 			'</ul>';
			$html[] = 		'</div>';
			$html[] = 	'</div>';
			
			$html[] = 	'<a href="http://codecanyon.net/user/AA-Team/portfolio?ref=AA-Team" class="kingdom-aateam-logo">AA-Team</a>';
			
			$html[] = '</div>';
			
			$html[] = '<script>
			(function($) {
				var kingdomMenu = $("#kingdom-topMenu");
				
				kingdomMenu.on("click", "a", function(e){
					
					var that = $(this),
						href = that.attr("href");
					
					if( href == "javascript: void(0)" ){
						var current_open = kingdomMenu.find("li.active");
						current_open.find(".kingdom-sub-menu").slideUp(350);
						current_open.removeClass("active");
						
						that.parent("li").eq(0).find(".kingdom-sub-menu").slideDown(350, function(){
							that.parent("li").eq(0).addClass("active");
						});
					}
				});
			})(jQuery);
			
			</script>';
			
			echo implode("\n", $html);
		}

		public function make_active( $section='' )
		{
			$this->current_menu = explode("|", $section);
			return $this;
		}
	}
}