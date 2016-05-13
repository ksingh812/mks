<?php
/**
 * AA-Team - http://www.aa-team.com
 * ===============================+
 *
 * @package		wwcAmzAffAdminMenu
 * @author		Andrei Dinca
 * @version		1.0
 */
! defined( 'ABSPATH' ) and exit;

if(class_exists('wwcAmzAffAdminMenu') != true) {
	class wwcAmzAffAdminMenu {
		
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
		
		private $menu_depedencies = array();

		static protected $_instance;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $wwcAmzAff;
        	$this->the_plugin = $wwcAmzAff;
			$this->ln = $this->the_plugin->localizationName;
			
			// update the menu tree
			$this->the_menu_tree();
			
			return $this;
        }

		/**
	    * Singleton pattern
	    *
	    * @return wwcAmzAffDashboard Singleton instance
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
			if ( isset($this->the_plugin->cfg['modules']['depedencies']['folder_uri'])
				&& !empty($this->the_plugin->cfg['modules']['depedencies']['folder_uri']) ) {
				$this->menu_depedencies['depedencies'] = array( 
					'title' => __( 'Plugin depedencies', $this->ln ),
					'url' => admin_url("admin.php?page=wwcAmzAff"),
					'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
					'menu_icon' => 'images/16_dashboard.png'
				);
                
                $this->clean_menu();
				return true;
			}

			$this->the_menu['dashboard'] = array( 
				'title' => __( 'Dashboard', $this->ln ),
				'url' => admin_url("admin.php?page=wwcAmzAff#!/dashboard"),
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_dashboard.png'
			);
			
			$this->the_menu['configuration'] = array( 
				'title' => __( 'Configuration', $this->ln ),
				'url' => "#!/",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_config.png',
				'submenu' => array(
					'amazon' => array(
						'title' => __( 'Amazon config', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff#!/amazon"),
						'folder_uri' => $this->the_plugin->cfg['modules']['amazon']['folder_uri'],
						'menu_icon' => 'assets/16_amzconfig.png'
					),
					
					/*'synchronization' => array(
						'title' => __( 'Synchronization', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff#!/synchronization"),
						'folder_uri' => $this->the_plugin->cfg['modules']['synchronization']['folder_uri'],
						'menu_icon' => 'assets/16_sync.png'
					),*/
				)
			);
			
			$this->the_menu['import'] = array( 
				'title' => __( 'Import Products', $this->ln ),
				'url' => "#!/",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_import.png',
				'submenu' => array(
					'advanced_search' => array(
						'title' => __( 'Advanced Search', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff#!/advanced_search"),
						'folder_uri' => $this->the_plugin->cfg['modules']['advanced_search']['folder_uri'],
						'menu_icon' => 'assets/16_advancedsearch.png'
					),
					
					'csv_products_import' => array(
						'title' => __( 'CSV Bulk Import', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff#!/csv_products_import"),
						'folder_uri' => $this->the_plugin->cfg['modules']['csv_products_import']['folder_uri'],
						'menu_icon' => 'assets/16_csv.png'
					),
					
					'asin_grabber' => array(
						'title' => __( 'ASIN Grabber', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff_asin_grabber"),
						'folder_uri' => $this->the_plugin->cfg['modules']['asin_grabber']['folder_uri'],
						'menu_icon' => 'assets/16_assets.png'
					),
					
                    /*'insane_import' => array(
                        'title' => __( 'Insane Import Mode', $this->ln ),
                        'url' => admin_url("admin.php?page=wwcAmzAff_insane_import"),
                        'folder_uri' => $this->the_plugin->cfg['modules']['insane_import']['folder_uri'],
                        'menu_icon' => 'assets/16_icon.png'
                    ),*/
                    'insane_import' => array(
                        'title' => __( 'Insane Import Mode', $this->ln ),
                        'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_insane_import"),
                        'folder_uri' => $this->the_plugin->cfg['modules']['insane_import']['folder_uri'],
                        'menu_icon' => 'assets/16_icon.png',
                        'submenu' => array(
                            'report_View' => array(
                                'title' => __( 'Insane Import', $this->ln ),
                                'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_insane_import")
                            ),
                            'report_Settings' => array(
                                'title' => __( 'Insane Import Settings', $this->ln ),
                                'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . '#!/insane_import')
                            ),
                        )
                    ),
                    
                    'content_spinner' => array(
                        'title' => __( 'Amazon Content Spinner', $this->ln ),
                        'url' => admin_url("admin.php?page=wwcAmzAff_content_spinner"),
                        'folder_uri' => $this->the_plugin->cfg['modules']['content_spinner']['folder_uri'],
                        'menu_icon' => 'assets/16_spinner.png'
                    ),
					
					/*'prices_fix' => array(
						'title' => __( 'Prices Update Fix', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff_prices_update"),
						'folder_uri' => $this->the_plugin->cfg['modules']['prices_fix']['folder_uri'],
						'menu_icon' => 'assets/16_pricesfix.png'
					),*/
					
					/*'stats_cart' => array(
						'title' => __( 'ShopCarts Stats', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff_stats_cart"),
						'folder_uri' => $this->the_plugin->cfg['modules']['stats_cart']['folder_uri'],
						'menu_icon' => 'assets/16_statsprod.png'
					)*/
				)
			);
			
			$this->the_menu['info'] = array( 
				'title' => __( 'Plugin Status', $this->ln ),
				'url' => "#!/",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_pluginstatus.png',
				'submenu' => array(
					'assets_download' => array(
						'title' => __( 'Assets Download', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff_assets_download"),
						'folder_uri' => $this->the_plugin->cfg['modules']['assets_download']['folder_uri'],
						'menu_icon' => 'assets/16_assets.png'
					),
					
					'stats_prod' => array(
						'title' => __( 'Products Stats', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff_stats_prod"),
						'folder_uri' => $this->the_plugin->cfg['modules']['stats_prod']['folder_uri'],
						'menu_icon' => 'assets/16_statsprod.png'
					),
					
					/*'synchronization_log' => array(
						'title' => __( 'Synchronization log', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff_synclog"),
						'folder_uri' => $this->the_plugin->cfg['modules']['synchronization']['folder_uri'],
						'menu_icon' => 'assets/16_synclog.png'
					),*/
                    'synchronization_log' => array(
						'title' => __( 'Synchronization log', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff_synclog"),
						'folder_uri' => $this->the_plugin->cfg['modules']['synchronization']['folder_uri'],
						'menu_icon' => 'assets/16_synclog.png',
                        'submenu' => array(
                            'synchronization_log_View' => array(
                                'title' => __( 'Synchronization log', $this->ln ),
                                'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_synclog")
                            ),
                            'synchronization_log_Settings' => array(
                                'title' => __( 'Synchronization log Settings', $this->ln ),
                                'url' => admin_url("admin.php?page=wwcAmzAff#!/synchronization")
                            ),
                        )
                    ),
					
                    'cronjobs' => array(
                        'title' => __( 'Plugin Cronjobs', $this->ln ),
                        'url' => admin_url("admin.php?page=wwcAmzAff#!/cronjobs"),
                        'folder_uri' => $this->the_plugin->cfg['modules']['cronjobs']['folder_uri'],
                        'menu_icon' => 'assets/16.png'
                    ),
					
					'amazon_debug' => array(
						'title' => __( 'Amazon Debug', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff#!/amazon_debug"),
						'folder_uri' => $this->the_plugin->cfg['modules']['amazon_debug']['folder_uri'],
						'menu_icon' => 'assets/16.png'
					),
					
                    'report' => array(
                        'title' => __( 'Woozone Report', $this->ln ),
                        'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_report"),
                        'folder_uri' => $this->the_plugin->cfg['modules']['report']['folder_uri'],
                        'menu_icon' => 'assets/menu_icon.png',
                        'submenu' => array(
                            'report_View' => array(
                                'title' => __( 'Woozone Report', $this->ln ),
                                'url' => admin_url('admin.php?page=' . $this->the_plugin->alias . "_report")
                            ),
                            'report_Settings' => array(
                                'title' => __( 'Woozone Report Settings', $this->ln ),
                                'url' => admin_url("admin.php?page=wwcAmzAff#!/report")
                            ),
                        )
                    ),
					
                    'server_status' => array(
                        'title' => __( 'Server Status', $this->ln ),
                        'url' => admin_url("admin.php?page=wwcAmzAff_server_status"),
                        'folder_uri' => $this->the_plugin->cfg['modules']['server_status']['folder_uri'],
                        'menu_icon' => 'assets/16_serversts.png'
                    ),
				)
			);
			
			$this->the_menu['general'] = array( 
				'title' => __( 'Plugin Settings', $this->ln ),
				'url' => "#!/",
				'folder_uri' => $this->the_plugin->cfg['paths']['freamwork_dir_url'],
				'menu_icon' => 'images/16_pluginsett.png',
				'submenu' => array(
					'modules_manager' => array(
						'title' => __( 'Modules Manager', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff#!/modules_manager"),
						'folder_uri' => $this->the_plugin->cfg['modules']['modules_manager']['folder_uri'],
						'menu_icon' => 'assets/16_modules.png'
					),
					
					'setup_backup' => array(
						'title' => __( 'Setup / Backup', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff#!/setup_backup"),
						'folder_uri' => $this->the_plugin->cfg['modules']['setup_backup']['folder_uri'],
						'menu_icon' => 'assets/16_setupbackup.png'
					),
					
					'remote_support' => array(
						'title' => __( 'Remote Support', $this->ln ),
						'url' => admin_url("admin.php?page=wwcAmzAff_remote_support"),
						'folder_uri' => $this->the_plugin->cfg['modules']['remote_support']['folder_uri'],
						'menu_icon' => 'assets/16_remotesupport.png'
					),
				)
			);
            
            $this->clean_menu();
		}

        public function clean_menu() {
            foreach ($this->the_menu as $key => $value) {
                if( isset($value['submenu']) ){
                    foreach ($value['submenu'] as $kk2 => $vv2) {
                        $kk2orig = $kk2;
                        // fix to support same module multiple times in menu
                        $kk2 = substr( $kk2, 0, (($t = strpos($kk2, '--'))!==false ? $t : strlen($kk2)) );
  
                        if( ($kk2 != 'synchronization_log')
                            && !in_array( $kk2, array_keys($this->the_plugin->cfg['activate_modules'])) ) {
                            unset($this->the_menu["$key"]['submenu']["$kk2orig"]);
                        }
                    }
                }
            }

            foreach ($this->the_menu as $k=>$v) { // menu
                if ( isset($v['submenu']) && empty($v['submenu']) ) {
                    unset($this->the_menu["$k"]);
                }
            }
        }
		
		public function show_menu( $pluginPage='' )
		{
			$plugin_data = $this->the_plugin->get_plugin_data();
			
			$html = array();
			// id="wwcAmzAff-nav-dashboard" 
			$html[] = '<div id="wwcAmzAff-header">';
			$html[] = 	'<div id="wwcAmzAff-header-bottom">';
			$html[] = 		'<div id="wwcAmzAff-topMenu">';
			$html[] = 			'<a href="http://codecanyon.net/item/woocommerce-amazon-affiliates-wordpress-plugin/3057503?ref=AA-Team" target="_blank" class="wwcAmzAff-product-logo">
									<img src="' . ( $this->the_plugin->cfg['paths']['plugin_dir_url'] ) . 'thumb.png" alt="">
									<h2>WooZone <br /> Amazon Associates</h2>
									<h3>' . ( $plugin_data['version'] ) . '</h3>
									
									<span class="wwcAmzAff-rate-now"></span>
									<img src="' . ( $this->the_plugin->cfg['paths']['freamwork_dir_url'] ) . 'images/rate-now.png" class="wwcAmzAff-rate-img">
									<img src="' . ( $this->the_plugin->cfg['paths']['freamwork_dir_url'] ) . 'images/star.gif" class="wwcAmzAff-rate-gif">
									<strong>Don’t forget to rate us!</strong>
								</a>';
			$html[] = 			'<ul>';

			if ( $pluginPage == 'depedencies' ) {
				$menu = $this->menu_depedencies;
				$this->current_menu = array(
					0 => 'depedencies',
					1 => 'depedencies'
				);
			} else {
				$menu = $this->the_menu;
			}

								foreach ($menu as $key => $value) {
									$iconImg = '<img src="' . ( $value['folder_uri'] . $value['menu_icon'] ) . '" />';
									$html[] = '<li id="wwcAmzAff-nav-' . ( $key ) . '" class="wwcAmzAff-section-' . ( $key ) . ' ' . ( isset($this->current_menu[0]) && ( $key == $this->current_menu[0] ) ? 'active' : '' ) . '">';
									
									if( $value['url'] == "#!/" ){
										$value['url'] = 'javascript: void(0)';
									}
									$html[] = 	'<a href="' . ( $value['url'] ) . '">' . ( $iconImg ) . '' . ( $value['title'] ) . '</a>';
									if( isset($value['submenu']) ){
										$html[] = 	'<ul class="wwcAmzAff-sub-menu">';
										foreach ($value['submenu'] as $kk2 => $vv2) {
											if( ($kk2 != 'synchronization_log') && isset($this->the_plugin->cfg['activate_modules']) && is_array($this->the_plugin->cfg['activate_modules']) && !in_array( $kk2, array_keys($this->the_plugin->cfg['activate_modules'])) ) continue;
		
											$iconImg = '<img src="' . ( $vv2['folder_uri'] . $vv2['menu_icon'] ) . '" />';
											$html[] = '<li class="wwcAmzAff-section-' . ( $kk2 ) . '  ' . ( isset($this->current_menu[1]) && $kk2 == $this->current_menu[1] ? 'active' : '' ) . '" id="wwcAmzAff-sub-nav-' . ( $kk2 ) . '">';
											$html[] = 	$iconImg;
											$html[] = 	'<a href="' . ( $vv2['url'] ) . '">' . ( $vv2['title'] ) . '</a>'; 
											
											if( isset($vv2['submenu']) ){
												$html[] = 	'<ul class="wwcAmzAff-sub-sub-menu">';
												foreach ($vv2['submenu'] as $kk3 => $vv3) {
													$html[] = '<li id="wwcAmzAff-sub-sub-nav-' . ( $kk3 ) . '">';
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
			
			$html[] = 	'<a href="http://codecanyon.net/user/AA-Team/portfolio?ref=AA-Team" class="wwcAmzAff-aateam-logo">AA-Team</a>';
			
			$html[] = '</div>';
			
			$html[] = '<script>
			(function($) {
				var wwcAmzAffMenu = $("#wwcAmzAff-topMenu");
				
				wwcAmzAffMenu.on("click", "a", function(e){
					
					var that = $(this),
						href = that.attr("href");
					
					if( href == "javascript: void(0)" ){
						var current_open = wwcAmzAffMenu.find("li.active");
						current_open.find(".wwcAmzAff-sub-menu").slideUp(350);
						current_open.removeClass("active");
						
						that.parent("li").eq(0).find(".wwcAmzAff-sub-menu").slideDown(350, function(){
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