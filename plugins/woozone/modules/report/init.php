<?php
/*
* Define class wwcAmzAffReport
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
      
if (class_exists('wwcAmzAffReport') != true) {
    class wwcAmzAffReport
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
        private $module_folder_path = '';
		private $module = '';

		static protected $_instance;
        
        public $is_admin = false;
        
        public $alias = '';
        public $localizationName = '';
        
        static private $report_alias = '';
        static private $report_alias_act = '';
        
        static private $settings = array();
        
        static private $sql_chunk_limit = 2000;
        static private $current_time = null;
		
		private $device = '';
		private $view_in_browser = '';


        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $wwcAmzAff;

            $this->the_plugin = $wwcAmzAff;

            $this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/report/';
            $this->module_folder_path = $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/report/';
            //$this->module = $module; // gives warning undefined variable.
            
            $this->alias = $this->the_plugin->alias;
            $this->localizationName = $this->the_plugin->localizationName;
 
            $this->is_admin = $this->the_plugin->is_admin;
            
            self::$report_alias = $this->alias.'_report';
            self::$report_alias_act = $this->alias.'_report_act';
            
            $ss = get_option($this->alias . '_report', array());
            $ss = maybe_unserialize($ss);
            self::$settings = $ss !== false ? $ss : array();

            self::$current_time = time();
			
			$this->device = isset($_REQUEST['device']) ? $_REQUEST['device'] . "_" : '';
			
            if (is_admin()) {
                add_action('admin_menu', array( &$this, 'adminMenu' ));
            }

            // ajax helper
            add_action('wp_ajax_wwcAmzAff_report', array( &$this, 'ajax_request' ));
            
            // ajax helper
            // ...see also /utils/action_admin_ajax.php
        }

		/**
	    * Singleton pattern
	    *
	    * @return wwcAmzAffReport Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }
            //self::$_instance->debug();
	        return self::$_instance;
	    }
        
        private function debug() {
            $this->build_current_report();
        }
        
        
        /**
        * Hooks
        */
        static public function adminMenu()
        {
           self::getInstance()
                ->_registerAdminPages();
        }

        /**
        * Register plug-in module admin pages and menus
        */
        protected function _registerAdminPages()
        { 
            add_submenu_page(
                $this->the_plugin->alias,
                $this->the_plugin->alias . " " . __('Report logs', $this->the_plugin->localizationName),
                __('Report logs'),
                'manage_options',
                $this->the_plugin->alias . "_report",
                array($this, 'display_index_page')
            );

            return $this;
        }

        public function display_index_page()
        {
            $this->printBaseInterface();
        }

        /*
        * printBaseInterface, method
        * --------------------------
        *
        * this will add the base DOM code for you options interface
        */
        public function printBaseInterface( $module='report' ) {
            global $wpdb;
            
            $ss = self::$settings;

            $mod_vars = array();

            // Sync
            $mod_vars['mod_menu'] = 'info|report';
            $mod_vars['mod_title'] = __('Report logs', $this->the_plugin->localizationName);

            extract($mod_vars);
            
            $module_data = $this->the_plugin->cfg['modules']["$module"];
            $module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . "modules/$module/";
?>
        <script type="text/javascript" src="<?php echo $this->module_folder;?>app.report.js" ></script>
        <script type="text/javascript" src="<?php echo $this->module_folder;?>assets/jquery.tipsy.js" ></script>
        
        <link rel='stylesheet' href='<?php echo $this->module_folder;?>assets/report-log.css' type='text/css' media='all' />
        <link rel='stylesheet' href='<?php echo $this->module_folder;?>assets/tipsy.css' type='text/css' media='all' />
        
        <div id="wwcAmzAff-wrapper" class="fluid wrapper-wwcAmzAff wwcAmzAff-report-log">
            
            <?php
            // show the top menu
            wwcAmzAffAdminMenu::getInstance()->make_active($mod_menu)->show_menu(); 
            ?>
            
            <!-- Content -->
            <div id="wwcAmzAff-content">
                
                <h1 class="wwcAmzAff-section-headline">
                    <?php 
                    if( isset($module_data["$module"]['in_dashboard']['icon']) ){
                        echo '<img src="' . ( $module_folder . $module_data["$module"]['in_dashboard']['icon'] ) . '" class="wwcAmzAff-headline-icon">';
                    }
                    ?>
                    <?php echo $module_data["$module"]['menu']['title'];?>
                    <span class="wwcAmzAff-section-info"><?php echo $module_data["$module"]['description'];?></span>
                    <?php
                    $has_help = isset($module_data["$module"]['help']) ? true : false;
                    if( $has_help === true ){
                        
                        $help_type = isset($module_data["$module"]['help']['type']) && $module_data["$module"]['help']['type'] ? 'remote' : 'local';
                        if( $help_type == 'remote' ){
                            echo '<a href="#load_docs" class="wwcAmzAff-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $module_data["$module"]['help']['url'] ) . '">HELP</a>';
                        } 
                    }
                    echo '<a href="#load_docs" class="wwcAmzAff-show-feedback" data-helptype="' . ( 'remote' ) . '" data-url="' . ( $this->the_plugin->feedback_url ) . '" data-operation="feedback">Feedback</a>';
                    ?>
                </h1>

                <!-- Container -->
                <div class="wwcAmzAff-container clearfix">

                    <!-- Main Content Wrapper -->
                    <div id="wwcAmzAff-content-wrap" class="clearfix" style="padding-top: 20px;">

                        <!-- Content Area -->
                        <div id="wwcAmzAff-content-area">
                            <div class="wwcAmzAff-grid_4">
                                <div class="wwcAmzAff-panel">
                                    <div class="wwcAmzAff-panel-header">
                                        <span class="wwcAmzAff-panel-title"><?php echo $mod_title; ?></span>
                                        <span class="wwcAmzAff-panel-sync-all"></span>
                                    </div>
                                    <div id="wwcAmzAff-sync-log" class="wwcAmzAff-panel-content" data-module="<?php echo $module; ?>">

                                        <?php
                                           $lang = array(
                                               'no_products'          => __('No report logs available.', 'wwcAmzAff'),
                                               'loading'              => __('Loading.', 'wwcAmzAff'),
                                           );
                                        ?>
                                        <div id="wwcAmzAff-lang-translation" style="display: none;"><?php echo htmlentities(json_encode( $lang )); ?></div>

                                        <!-- Main loading box -->
                                        <div id="wwcAmzAff-main-loading">
                                            <div id="wwcAmzAff-loading-overlay"></div>
                                            <div id="wwcAmzAff-loading-box">
                                                <div class="wwcAmzAff-loading-text"><?php _e('Loading', $this->the_plugin->localizationName);?></div>
                                                <div class="wwcAmzAff-meter wwcAmzAff-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
                                            </div>
                                        </div>
            
                                        <div class="wwcAmzAff-sync-filters">
                                            <span>
                                                <?php _e('Total report logs', $this->the_plugin->localizationName);?>: <span class="count"></span>
                                            </span>
                                            <span class="right">
                                                <button class="load_rows"><?php _e('Reload report logs list', $this->the_plugin->localizationName);?></button>
                                            </span>
                                        </div>
                                        <div class="wwcAmzAff-sync-table <?php echo ( $module == 'report' ? 'report' : '' ); ?>">
                                          <table cellspacing="0">
                                            <thead>
                                                <tr class="wwcAmzAff-sync-table-header">
                                                    <th style="width:3%;"><?php _e('ID', $this->the_plugin->localizationName);?></th>
                                                    <th style="width:10%;"><?php _e('Log Id', $this->the_plugin->localizationName);?></th>
                                                    <th style="width:10%;"><?php _e('Log Action', $this->the_plugin->localizationName);?></th>
                                                    <th style="width:53%;"><?php _e('Log Desc', $this->the_plugin->localizationName);?></th>
                                                    <th style="width:14%;"><?php _e('Date Added', $this->the_plugin->localizationName);?></th>
                                                    <th style="width:10%;"><?php _e('Action', $this->the_plugin->localizationName);?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                //require_once( $this->module_folder_path . '_html.php');
                                            ?>
                                            </tbody>
                                          </table>
                                        </div>
                                        <?php /*if ( $module == 'report' ) { ?>
                                            <div class="wwcAmzAff-sync-info">
                                              <h3><?php _e('Settings', $this->the_plugin->localizationName);?></h3>
                                              <?php //echo $this->report_settings(); ?>
                                            </div>
                                        <?php }*/ ?>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php
        }


        private function get_rows( $module='report' ) {
            global $wpdb;
           
            $table_name_report = $wpdb->prefix . "amz_report_log";
            $sql = "SELECT p.ID, p.log_id, p.log_action, p.desc, p.date_add FROM $table_name_report as p WHERE 1=1 ORDER BY p.ID DESC;";
            $res = $wpdb->get_results( $sql, OBJECT_K );
            
            if ( empty($res) ) return array();
            
            // build html table with products rows
            $default = array(
                'module'        => $module,
            );
 
            $ret = array('status' => 'valid', 'html' => array(), 'nb' => 0);
            $nbprod = 0;
            foreach ($res as $id => $val) {
                
                $__p = $this->row_build(array_merge($default, array(
                    'id'            => $id,
                    'val'           => $val,
                )));
                $__p = array_merge($__p, array(
                    'id'            => $id,
                ));
                
                // product
                $ret['html'][] = $this->row_view_html($__p);
                
                $nbprod++;
            } // end products loop
            
            $ret = array_merge($ret, array(
                'nb'        => $nbprod,
            ));
            
            return $ret;
        }

        private function row_build( $pms ) {
            extract($pms);

            $log_id = $val->log_id;
            $log_action = $val->log_action;
            $desc = $val->desc;
                
            $add_data = $val->date_add;
            $add_data = $this->the_plugin->last_update_date('true', strtotime($add_data), true);

            if ( $module == 'report' ) {
                $ret = compact('module', 'add_data', 'log_id', 'log_action', 'desc');
            }
            return $ret;
        }

        private function row_view_html( $row ) {
            $tr_css = '';
            
            if ( $row['module'] == 'report' ) {
                $text_log_id = $this->log_nice_format( $row['log_id'] );
                $text_log_action = $this->log_nice_format( $row['log_action'] );
                $text_viewlog = __('View log', $this->the_plugin->localizationName);
            }
            
            if ( $row['module'] == 'report' ) {
            $ret = '
                    <tr class="wwcAmzAff-sync-table-row' . $tr_css . '" data-id=' . $row['id'] . ' data-log_id=' . $row['log_id'] . ' data-log_action=' . $row['log_action'] . '>
                        <td><span>' . $row['id'] . '</span></td>
                        <td>' . $text_log_id . '</td>
                        <td>' . $text_log_action . '</td>
                        <td>' . $row['desc'] . '</td>
                        <td>' . $row['add_data'] . '</td>
                        <td class="wwcAmzAff-sync-now"><button>' . $text_viewlog . '</button></td>
                    </tr>
                ';
            }
            return $ret;
        }

        private function get_view_log( $pms=array() ) {
            extract($pms);

            $row_data = (array) $this->get_log_data( $id );
            extract($row_data);

            $log_id = $this->log_nice_format( $log_id );
            $log_action = $this->log_nice_format( $log_action );
            $date_add = $this->the_plugin->last_update_date('true', strtotime($date_add), true);

            $html = array();
            $html[] = '<div class="wwcAmzAff-report-log-lightbox">';
            $html[] =   '<div class="wwcAmzAff-donwload-in-progress-box">';
            $html[] =       '<h1>' . __('View log box', $this->localizationName ) . '<a href="#" class="wwcAmzAff-button red" id="wwcAmzAff-close-btn">' . __('CLOSE', $this->localizationName ) . '</a></h1>';
            $html[] =       '<p class="wwcAmzAff-message wwcAmzAff-info wwcAmzAff-donwload-notice">';
            $html[] =       sprintf( __('Log id: <strong>%s</strong> | Log action: <strong>%s</strong> | Date: <em>%s</em>', $this->localizationName ), $log_id, $log_action, $date_add );
            $html[] =       '</p>';

            /*
            $html[] =       '<table class="wwcAmzAff-table wwcAmzAff-debug-info">';
            $html[] =           '<tr>';
            $html[] =               '<td width="150">' . __('Total Images:', $this->localizationName ) . '</td>';
            $html[] =               '<td>' . ( count($assets) ) . '</td>';
            $html[] =           '</tr>';
            $html[] =       '</table>';
            */
            $log_code = "{$row_data['log_id']}|{$row_data['log_action']}";
            switch ($log_code) {
                case 'report|products_status':
                    $html[] = $this->_get_report_products_status($row_data, 'view_log');
                    break;
            }

            $html[] =   '</div>';
            $html[] = '</div>';

            return implode("\n", $html);
        }

        private function get_log_data($id) {
            global $wpdb;
            
            $table_name_report = $wpdb->prefix . "amz_report_log";
            $sql = "SELECT p.log_id, p.log_action, p.desc, p.date_add, p.log_data_type, p.log_data FROM $table_name_report as p WHERE 1=1 AND p.ID = '%s';";
            $sql = sprintf($sql, $id);
            $ret = $wpdb->get_row( $sql );
            if ( is_null($ret) || $ret === false ) {
                return array();
            }
            
            $ret = (array) $ret;
            
            // get report data - products
            $log_data = array();
            switch ( $ret['log_data_type'] ) {
                case 'serialize':
                    $log_data = !empty($ret['log_data']) ? (array) maybe_unserialize($ret['log_data']) : array();
                    break;
            }
            $ret['log_data'] = (array) $log_data;

            return (array) $ret;
        }

        
        /**
         * Get Report Products Sync & Performance Status
         */
        private function get_report_products( $module='synchronization' ) {
            global $wpdb;
            
			$prod_key == '_amzASIN';

            $ret = array('status' => 'valid', 'products' => array(), 'nb' => 0, 'nbv' => 0);

            $report_last_date = (int) get_option('wwcAmzAff_report_last_date', 0);
             
            $clause = array();
            if ( $module == 'synchronization' ) {
                $clause[] = " AND ( pm.meta_key = '_amzaff_sync_last_date' AND pm.meta_value > $report_last_date ) ";
            } else if ( $module == 'performance' ) {
            }
            $clause = implode('', $clause);
            
            // get products (simple or just parents without variations)
            $sql = "SELECT p.ID, p.post_title, p.post_parent, p.post_date FROM $wpdb->posts as p LEFT JOIN $wpdb->postmeta as pm ON p.ID = pm.post_id WHERE 1=1 AND p.post_status = 'publish' AND p.post_parent = 0 AND p.post_type = 'product' %s ORDER BY p.ID ASC;";
            $sql = sprintf($sql, $clause);
            $res = $wpdb->get_results( $sql, OBJECT_K );
            
            // get product variations (only childs, no parents)
            $sql_childs = "SELECT p.ID, p.post_title, p.post_parent, p.post_date FROM $wpdb->posts as p LEFT JOIN $wpdb->postmeta as pm ON p.ID = pm.post_id WHERE 1=1 AND p.post_status = 'publish' AND p.post_parent > 0 AND p.post_type = 'product_variation' %s ORDER BY p.ID ASC;";
            $sql_childs = sprintf($sql_childs, $clause);
            $res_childs = $wpdb->get_results( $sql_childs, OBJECT_K );
            
            //var_dump('<pre>', $sql, $sql_childs, '</pre>'); die('debug...'); 
            if ( empty($res) && empty($res_childs) ) return $ret;
            
            // array with parents and their associated childrens
            $parent2child = array();
            foreach ($res_childs as $id => $val) {
                $parent = $val->post_parent;
                
                if ( !isset($parent2child["$parent"]) ) {
                    $parent2child["$parent"] = array();
                }
                $parent2child["$parent"]["$id"] = $val; 
            }
 
            // products IDs
            $prods = array_merge(array(), array_keys($res), array_keys($res_childs));
            $prods = array_unique($prods);

            // get ASINs
            $prods2asin = array();
            foreach (array_chunk($prods, self::$sql_chunk_limit, true) as $current) {

                $currentP = implode(',', array_map(array($this->the_plugin, 'prepareForInList'), $current));
                $sql_getasin = "SELECT pm.post_id, pm.meta_value FROM $wpdb->postmeta as pm WHERE 1=1 AND pm.meta_key = '$prod_key' AND pm.post_id IN ($currentP) ORDER BY pm.post_id ASC;";
                $res_getasin = $wpdb->get_results( $sql_getasin, OBJECT_K );
                $prods2asin = $prods2asin + $res_getasin; //array_replace($prods2asin, $res_getasin);
            }
            
            if ( $module == 'synchronization' ) {
                $__meta_toget = array(
                    '_amzaff_sync_last_date', '_amzaff_sync_hits', '_amzaff_sync_last_status',
                    '_amzaff_sync_hits_prev'
                );
            } else if ( $module == 'performance' ) {
                $__meta_toget = array(
                    '_amzaff_hits', '_amzaff_addtocart', '_amzaff_redirect_to_amazon',
                    '_amzaff_hits_prev', '_amzaff_addtocart_prev', '_amzaff_redirect_to_amazon_prev'
                );
            }
            // get sync last date & sync hits
            $prods2meta = array();
            //foreach ( (array) $__meta_toget as $meta) {
                //$prods2meta["$meta"] = array();

                foreach (array_chunk($prods, self::$sql_chunk_limit, true) as $current) {
    
                    $currentP = implode(',', array_map(array($this->the_plugin, 'prepareForInList'), $current));
                    $currentMeta = implode(',', array_map(array($this->the_plugin, 'prepareForInList'), $__meta_toget));
    
                    $sql_getmeta = "SELECT pm.post_id, pm.meta_key, pm.meta_value FROM $wpdb->postmeta as pm WHERE 1=1 AND pm.meta_key IN ($currentMeta) AND pm.post_id IN ($currentP) ORDER BY pm.post_id ASC;";
                    $res_getmeta = $wpdb->get_results( $sql_getmeta );
                    foreach ((array) $res_getmeta as $k => $v) {
                        $_post_id = $v->post_id;
                        $_meta_key = $v->meta_key;
                        $_meta_value = $v->meta_value;
                        $prods2meta["$_post_id"]["$_meta_key"] = $_meta_value;
                    }
                    //$prods2meta["$meta"] = $prods2meta["$meta"] + $res_getmeta; //array_replace($prods2meta["$meta"], $res_getmeta);
                }
            //}
 
            // init report
            $prods2meta = $this->report_init($prods, $prods2asin, $prods2meta);

            if ( $module == 'synchronization' ) {
                $nb_success = 0;
                $nb_error = 0;
            } else if ( $module == 'performance' ) {
                $total_nb = 0;
                $total_hits = 0;
                $total_addtocart = 0;
                $total_redirect_to_amazon = 0;
            }
  
            $default = array(
                'module'        => $module,
            );
            $nbprod = 0;
            $nbprodv = 0;
            foreach ($res as $id => $val) {
  
                // exclude products without ASIN
                if ( !isset($prods2asin["$id"]) ) continue 1;

                // product meta is invalid
                if ( !$this->is_valid_prod($module, $id, $prods2meta) ) continue 1;

                $ret['products']["$id"] = $this->row_build_report(array_merge($default, array(
                    'id'            => $id,
                    'val'           => $val,
                    'prods2asin'    => $prods2asin,
                    'prods2meta'    => $prods2meta,
                )));
                if ( $module == 'synchronization' ) {
                    if ( $ret['products']["$id"]['sync_last_status'] ) $nb_success++;
                    else $nb_error++;
                } else if ( $module == 'performance' ) {
                    $total_nb++;
                    $total_hits += $ret['products']["$id"]['hits'];
                    $total_addtocart += $ret['products']["$id"]['addtocart'];
                    $total_redirect_to_amazon += $ret['products']["$id"]['redirect_to_amazon'];
                }

                if ( isset($parent2child["$id"]) ) {
                    $childs = $parent2child["$id"];
                    $childs_nb = count($childs);
                    $cc = 0;
                    foreach ($childs as $childId => $childVal) {
                        // exclude products without ASIN
                        if ( !isset($prods2asin["$childId"]) ) continue 1;
        
                        // product meta is invalid
                        if ( !$this->is_valid_prod($module, $childId, $prods2meta) ) continue 1;
 
                        $ret['products']["$childId"] = $this->row_build_report(array_merge($default, array(
                            'id'            => $childId,
                            'val'           => $childVal,
                            'prods2asin'    => $prods2asin,
                            'prods2meta'    => $prods2meta,
                        )));
                        if ( $module == 'synchronization' ) {
                            if ( $ret['products']["$childId"]['sync_last_status'] ) $nb_success++;
                            else $nb_error++;
                        } else if ( $module == 'performance' ) {
                            $total_nb++;
                            $total_hits += $ret['products']["$childId"]['hits'];
                            $total_addtocart += $ret['products']["$childId"]['addtocart'];
                            $total_redirect_to_amazon += $ret['products']["$childId"]['redirect_to_amazon'];
                        }

                        $cc++;
                    }
                    
                    $nbprodv += $cc;
                } // end product variations loop
                
                $nbprod++;
            } // end products loop
            
            // no products found!
            if ( empty($ret['products']) ) return $ret;
 
            $ret = array_merge($ret, array(
                'nb'        => $nbprod,
                'nbv'       => $nbprodv,
            ));
            if ( $module == 'synchronization' ) {
                $ret = array_merge($ret, array(
                    'nb_success'        => $nb_success,
                    'nb_error'          => $nb_error,
                ));
            } else if ( $module == 'performance' ) {
                if ( !empty($ret['products']) ) {
                    $ret['products'] = $this->sort_hight_to_low( $ret['products'], 'score' );
                }

                $ret = array_merge($ret, array(
                    'total_nb'                      => $total_nb,
                    'total_hits'                    => $total_hits,
                    'total_addtocart'               => $total_addtocart,
                    'total_redirect_to_amazon'      => $total_redirect_to_amazon,
                ));
            }
            //var_dump('<pre>', $ret, '</pre>'); die('debug...'); 
            return $ret;
        }

        private function report_init($prods, $prods2asin, $prods2meta) {
            $is_first = (int) get_option('wwcAmzAff_report_first_time', 0);
            $is_first = !empty($is_first) ? false : true;
            
            if (!$is_first || empty($prods)) return $prods2meta;
 
            $metas = array('_amzaff_sync_hits_prev', '_amzaff_hits_prev', '_amzaff_addtocart_prev', '_amzaff_redirect_to_amazon_prev');
            foreach ($prods as $id) {
  
                // exclude products without ASIN
                if ( !isset($prods2asin["$id"]) ) continue 1;
                
                foreach ($metas as $meta) {
                    $_meta = str_replace('_prev', '', $meta);
 
                    if ( isset($prods2meta["$id"], $prods2meta["$id"]["$_meta"]) ) {
                        update_post_meta($id, $meta, (int) $prods2meta["$id"]["$_meta"]);
                        $prods2meta["$id"]["$meta"] = (int) $prods2meta["$id"]["$_meta"];
                    }
                }
            } // end foreach
            return $prods2meta;
        }

        private function is_valid_prod($module, $id, $prods2meta) {
            {
                if ( $module == 'synchronization' ) {
                    // debug...
                    //update_post_meta($id, '_amzaff_sync_hits_prev', (int) get_post_meta($id, '_amzaff_sync_hits', true));
                    
                    if ( !isset($prods2meta["$id"], $prods2meta["$id"]['_amzaff_sync_hits_prev'])
                        || empty($prods2meta["$id"]['_amzaff_sync_hits_prev']) ) {
                        if ( !isset($prods2meta["$id"], $prods2meta["$id"]['_amzaff_sync_hits_prev']) ) {
                            update_post_meta($id, '_amzaff_sync_hits_prev', 0);
                        }
                        return false;
                    }
                    return true;
                } else if ( $module == 'performance' ) {
                    // debug...
                    //update_post_meta($id, '_amzaff_hits_prev', (int) get_post_meta($id, '_amzaff_hits', true));
                    //update_post_meta($id, '_amzaff_addtocart_prev', (int) get_post_meta($id, '_amzaff_addtocart', true));
                    //update_post_meta($id, '_amzaff_redirect_to_amazon_prev', (int) get_post_meta($id, '_amzaff_redirect_to_amazon', true));

                    $has_hits = true;
                    if ( !isset($prods2meta["$id"], $prods2meta["$id"]['_amzaff_hits_prev'])
                        || empty($prods2meta["$id"]['_amzaff_hits_prev']) ) {
                        if ( !isset($prods2meta["$id"], $prods2meta["$id"]['_amzaff_hits_prev']) ) {
                            update_post_meta($id, '_amzaff_hits_prev', 0);
                        }
                        $has_hits = false;
                    }
                    $has_addtocart = true;
                    if ( !isset($prods2meta["$id"], $prods2meta["$id"]['_amzaff_addtocart_prev'])
                        || empty($prods2meta["$id"]['_amzaff_addtocart_prev']) ) {
                        if ( !isset($prods2meta["$id"], $prods2meta["$id"]['_amzaff_addtocart_prev']) ) {
                            update_post_meta($id, '_amzaff_addtocart_prev', 0);
                        }
                        $has_addtocart = false;
                    }
                    $has_redirect_to_amazon = true;
                    if ( !isset($prods2meta["$id"], $prods2meta["$id"]['_amzaff_redirect_to_amazon_prev'])
                        || empty($prods2meta["$id"]['_amzaff_redirect_to_amazon_prev']) ) {
                        if ( !isset($prods2meta["$id"], $prods2meta["$id"]['_amzaff_redirect_to_amazon_prev']) ) {
                            update_post_meta($id, '_amzaff_redirect_to_amazon_prev', 0);
                        }
                        $has_redirect_to_amazon = false;
                    }
                    $has = $has_hits || $has_addtocart || $has_redirect_to_amazon;
                    return $has;
                }
                return false;
            }
        }

        private function row_build_report( $pms ) {
            extract($pms);

            $title = $val->post_title;
            $asin = isset($prods2asin["$id"]) ? $prods2asin["$id"]->meta_value : 0;
            
            $post_date = $val->post_date;
            $post_parent = $val->post_parent;
            
            if ( $module == 'synchronization' ) {

                $sync_hits = isset($prods2meta["$id"], $prods2meta["$id"]['_amzaff_sync_hits_prev']) ? $prods2meta["$id"]['_amzaff_sync_hits_prev'] : 0;

                $sync_last_date = isset($prods2meta["$id"], $prods2meta["$id"]['_amzaff_sync_last_date']) ? $prods2meta["$id"]['_amzaff_sync_last_date'] : '';

                $sync_last_status = isset($prods2meta["$id"], $prods2meta["$id"]['_amzaff_sync_last_status']) ? $prods2meta["$id"]['_amzaff_sync_last_status'] : 0;

                $ret = compact('id', 'title', 'asin', 'post_date', 'post_parent', 'sync_hits', 'sync_last_date', 'sync_last_status');
            } else if ( $module == 'performance' ) {

                $hits = isset($prods2meta["$id"], $prods2meta["$id"]['_amzaff_hits_prev']) ? $prods2meta["$id"]['_amzaff_hits_prev'] : 0;

                $addtocart = isset($prods2meta["$id"], $prods2meta["$id"]['_amzaff_addtocart_prev']) ? $prods2meta["$id"]['_amzaff_addtocart_prev'] : 0;

                $redirect_to_amazon = isset($prods2meta["$id"], $prods2meta["$id"]['_amzaff_redirect_to_amazon_prev']) ? $prods2meta["$id"]['_amzaff_redirect_to_amazon_prev'] : 0;
                
                $score = ($redirect_to_amazon * 3) + ($addtocart * 2) + ($hits * 1);

                $ret = compact('id', 'title', 'asin', 'post_date', 'post_parent', 'hits', 'addtocart', 'redirect_to_amazon', 'score');
            }
            unset($ret['title']);
            return $ret;
        }
        
        private function set_report_products_meta_prev() {
            global $wpdb;
            
            $__meta_toget = array(
                '_amzaff_hits_prev', '_amzaff_addtocart_prev', '_amzaff_redirect_to_amazon_prev',
                '_amzaff_sync_hits_prev'
            );
            
            $currentMeta = implode(',', array_map(array($this->the_plugin, 'prepareForInList'), $__meta_toget));

            $sql = "UPDATE $wpdb->postmeta as pm SET pm.meta_value = '0' WHERE 1=1 AND pm.meta_key IN ($currentMeta);";
            return $wpdb->query( $sql );
        }
        
        private function build_current_report() {
            $now = self::$current_time;

            $ret = array(
                'log_id'            => 'report',
                'log_action'        => 'products_status',
                'desc'              => 'report products synchronization and performance status',
                'date_add'          => $now,
            );
            
            // get report data - products
            $ret['log_data'] = array();
            $ret['log_data']['synchronization'] = (array) $this->get_report_products('synchronization');
            $ret['log_data']['performance'] = (array) $this->get_report_products('performance');
 
            // set old meta for report data - products
            $this->set_report_products_meta_prev();
            
            // update last report date
            update_option('wwcAmzAff_report_last_date', $now);
            update_option('wwcAmzAff_report_first_time', $now);
            
            // save report
            $ret['new_id'] = $this->save_current_report( $ret );
            
            return $ret;
        }
        
        private function save_current_report( $pms ) {
            global $wpdb;
            
            extract($pms);
            
            $table_name_report = $wpdb->prefix . "amz_report_log";
            {
                $log_data = serialize($log_data);
                $log_data_type = 'serialize';

                $wpdb->insert( 
                    $table_name_report, 
                    array( 
                        'log_id'            => $log_id,
                        'log_action'        => $log_action,
                        'desc'              => $desc,
                        'log_data_type'     => $log_data_type,
                        'log_data'          => $log_data,
                        //'source'            => '',
                        //'date_add'          => $date_add,
                    ), 
                    array( 
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        //'%s',
                        //'%s',
                    )
                );
                $insert_id = $wpdb->insert_id;
                return $insert_id;
            }
        }

        public function cronjob( $pms, $return='die' ) {
            $ret = array('status' => 'failed');
            
            $current_cron_status = $pms['status']; //'new'; //
            $now = self::$current_time;
            $report_last_date = (int) get_option('wwcAmzAff_report_last_date', 0);
            $recurrence = isset(self::$settings['recurrency']) ? (int) self::$settings['recurrency'] : 12;
            $recurrence = (int) ( $recurrence * 3600 );
            
            // recurrence interval fulfilled
            if ( /*1 || */$now >= ( $report_last_date + $recurrence ) ) {
                
                // assurance verification: reset in any case after more than 3 times the current setted recurrence interval
                //$do_reset = $now >= ( $report_last_date + $recurrence * 3 ) ? true : false;
                
                $report_data = $this->build_current_report();
                $this->report_send_mail( $report_data );
            }

            $ret = array_merge($ret, array(
                'status'            => 'done',
            ));
            return $ret;
        }

        private function report_send_mail( $data=array() ) {
            extract($data);
 			
			// aici am nevoie de help
			$log_id = $new_id;
			$this->view_in_browser = admin_url( 'admin-ajax.php?action=wwcAmzAff_report_settings&subaction=view_in_browser&log_id=' . $log_id );
			
            // send email
            add_filter('wp_mail_content_type', array($this->the_plugin, 'set_content_type'));
            //add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
            
            $email_to = isset(self::$settings['email_to']) ? self::$settings['email_to'] : '';
            if ( empty($email_to) ) {
                return array(
                    'mailStat'          => false,
                    'mailFields'        => array(),
                );
            }
			
            $subject = isset(self::$settings['email_subject']) ? __(self::$settings['email_subject'], $this->the_plugin->localizationName) : __('WooZone Report', $this->the_plugin->localizationName);
            
            $details = array('plugin_name' => 'wwcAmzAff');
            $from_name = __($details['plugin_name'].' Report module | ', $this->the_plugin->localizationName) . get_bloginfo('name');
            $from_email = get_bloginfo('admin_email');
            $headers = array();
            $headers[] = __('From: ', $this->the_plugin->localizationName) . $from_name . " <" . $from_email . ">";
            $headers[] = "MIME-Version: 1.0";
            
            //$html = '<p>The <em>HTML</em> message</p>';
            $html = $this->_get_report_products_status( $data, 'email' );

            // wordpress mail function
            $sendStat = wp_mail( $email_to, $subject, $html, $headers );
			
            // reset content-type to avoid conflicts -- http://core.trac.wordpress.org/ticket/23578
            remove_filter('wp_mail_content_type', array($this->the_plugin, 'set_content_type'));

            // phpmailer fallback
            if ( !$sendStat ) {
                require_once( $this->the_plugin->cfg['paths']['scripts_dir_path'] . '/PHPMailer_5.2.9/class.phpmailer.php' );
            
                $mail = new PHPMailer();
                
                $mail->SetFrom( $from_email, $from_name );
                
                $mail->AddAddress( $email_to, $email_to );
                
                // add us as BCC of reply
                $mail->AddBCC( $from_email, $from_name );
        
                $mail->Subject = $subject;
                $mail->AltBody    = __("To view the message, please use an HTML compatible email viewer!", $this->the_plugin->localizationName); // optional, comment out and test
                
                // load the header 
                $body  = $html;     
                
                // append body html to email transporter
                $mail->MsgHTML( $body );
                
                $sendStat = (bool) $mail->Send();
                
                // Clear Addresses
                $mail->ClearAddresses();
            }
            
            return array(
                'mailStat'          => $sendStat,
                'mailFields'        => compact( 'email_to', 'subject' ), //compact( 'email_to', 'subject', 'html' ),
            );
        }

        private function _get_report_products_status( $data, $view_type ) {
            extract($data);
			
            // get the email template
            /*ob_start();
            require_once( $this->module_folder_path . 'tpl/products_status/index.html' );
            $html = ob_get_contents();
            ob_end_clean();*/

            $lang = array(
                'no_products'       => __('no products', $this->localizationName),
            );
			
            $parts = array(
                'header'                    => file_get_contents( $this->module_folder_path . 'tpl/products_status/parts_header.html' ),
                'content'                   => file_get_contents( $this->module_folder_path . 'tpl/products_status/parts_content.html' ),
                'content_synchronization'   => file_get_contents( $this->module_folder_path . 'tpl/products_status/' . ( $this->device ) . 'parts_content_synchronization.html' ),
                'content_performance'       => file_get_contents( $this->module_folder_path . 'tpl/products_status/' . ( $this->device ) . 'parts_content_performance.html' ),
            );

            if ( $view_type == 'email' ) {
                $html = file_get_contents( $this->module_folder_path . 'tpl/products_status/index.html' );
                $html = str_replace("{{__parts_header__}}", $parts['header'], $html);
                $html = str_replace("{{__parts_content__}}", $parts['content'], $html);

            } else if ( $view_type == 'view_log' ) {
                $html = $parts['header'] . "\n" . $parts['content'];
            }
             
            $resContent = $this->_products_status_content($data, $view_type);

            // synchronization
            $has_prods_sync = false;
            if ( isset($log_data['synchronization'], $log_data['synchronization']['products'])
                && !empty($log_data['synchronization']) && !empty($log_data['synchronization']['products']) ) {
                $has_prods_sync = true;
                $html = str_replace("{{__parts_content_synchronization__}}", $parts['content_synchronization'], $html);                
            } else {
                $html = str_replace("{{__parts_content_synchronization__}}", "<tr><td style='text-align: center;'>{$lang['no_products']}</td></tr>", $html);
            }

            $html = str_replace("{{sync_title}}", __('WooZone Synchronisation Status', $this->localizationName), $html);
            if ( $has_prods_sync ) {
            $html = str_replace("{{sync_success_text}}", __('Successfully synchronised :', $this->localizationName), $html);
            $html = str_replace("{{sync_success_nb}}", sprintf( __('%s products', $this->localizationName), $resContent['nb_success'] ), $html);
            $html = str_replace("{{sync_error_text}}", __('Errors occured :', $this->localizationName), $html);
            $html = str_replace("{{sync_error_nb}}", sprintf( __('%s products', $this->localizationName), $resContent['nb_error'] ), $html);

            $html = str_replace("{{sync_table_head}}", $resContent['sync_head'], $html);
            $html = str_replace("{{sync_table_body}}", $resContent['sync_body'], $html);
            }

            // performance
            $has_prods_perf = false;
            if ( isset($log_data['performance'], $log_data['performance']['products'])
                && !empty($log_data['performance']) && !empty($log_data['performance']['products']) ) {
                $has_prods_perf = true;
                $html = str_replace("{{__parts_content_performance__}}", $parts['content_performance'], $html);                
            } else {
                $html = str_replace("{{__parts_content_performance__}}", "<tr><td style='text-align: center;'>{$lang['no_products']}</td></tr>", $html);
            }

            $html = str_replace("{{perf_title}}", __('WooZone Performance', $this->localizationName), $html);
            if ( $has_prods_perf ) {
	            $html = str_replace("{{perf_total_nb}}", sprintf( __('<span>%s</span> <span>total</span>', $this->localizationName), $resContent['total_nb'] ), $html); //  <span>products</span>
	            $html = str_replace("{{perf_total_nb_text}}", __('<span>Number of products</span>', $this->localizationName), $html);
	            $html = str_replace("{{perf_total_views}}", sprintf( __('<span>%s</span> <span>total</span>', $this->localizationName), $resContent['total_hits'] ), $html);
	            $html = str_replace("{{perf_total_views_text}}", __('<span>Views</span>', $this->localizationName), $html);
	            $html = str_replace("{{perf_total_addtocart}}", sprintf( __('<span>%s</span> <span>total</span>', $this->localizationName), $resContent['total_addtocart'] ), $html);
	            $html = str_replace("{{perf_total_addtocart_text}}", __('<span>Added to cart</span>', $this->localizationName), $html);
	            $html = str_replace("{{perf_total_redtoamz}}", sprintf( __('<span>%s</span> <span>total</span>', $this->localizationName), $resContent['total_redirect_to_amazon'] ), $html);
	            $html = str_replace("{{perf_total_redtoamz_text}}", __('<span>Redirected to Amazon</span>', $this->localizationName), $html);
	
	            $html = str_replace("{{perf_table_head}}", $resContent['perf_head'], $html);
	            $html = str_replace("{{perf_table_body}}", $resContent['perf_body'], $html);
            }

            // header & general
            $date_add = $this->the_plugin->last_update_date('true', strtotime($date_add), true);
            $title = sprintf( __('WooZone Report - %s', $this->localizationName), $date_add );
            $html = str_replace("{{title}}", $title, $html);
            $html = str_replace("{{images_base_url}}", $this->module_folder . 'tpl/products_status/', $html);

            // footer
            $html = str_replace("{{content_notice}}", __('<span>It contains all products status from the time of the last report.</span>', $this->localizationName), $html);
            $html = str_replace("{{aateam_notice}}", __('© AA-Team, 2015 <br />You are receiving this email because<br /> you\'re an awesome customer of AA-Team.', $this->localizationName), $html);

            return $html;
        }
        
        private function _products_status_content( $data, $view_type ) {
            extract($data);
 
            $s = isset($log_data['synchronization']) ? $log_data['synchronization'] : array();
            $p = isset($log_data['performance']) ? $log_data['performance'] : array();
            $limit = $this->device == 'email_' ? 5 : 0;
			
            // synchronize & performance header
            $sync_head = '<tr>
                <th style="width:35%;">' . __('Product (ASIN / ID)', $this->localizationName) . '</th>
                <th>' . __('Syncs number', $this->localizationName) . '</th>
                <th>' . __('Sync last status', $this->localizationName) . '</th>
                <th>' . __('Sync last date', $this->localizationName) . '</th>
            </tr>';

            $perf_head = '<tr>
                <th style="width:35%;">' . __('Product (ASIN / ID)', $this->localizationName) . '</th>
                <th>' . __('Views', $this->localizationName) . '</th>
                <th>' . __('Added to cart', $this->localizationName) . '</th>
                <th>' . __('Redirect to Amazon', $this->localizationName) . '</th>
            </tr>';

            // synchronize & performance body content
            $sync_body = array();
            $cc = 0;
            foreach ( (array) $s['products'] as $key => $val ) {
				if( $limit != 0 && $cc >= $limit ){
            		continue;
            	}
                $link_edit = sprintf( admin_url('post.php?post=%s&action=edit'), $val['id']);
                $is_child = $val['post_parent'] > 0 ? true : false;

                $sync_hits = sprintf( __('%s Syncs', $this->localizationName), $val['sync_hits'] );
                $sync_last_date = $this->the_plugin->last_update_date('true', $val['sync_last_date']);
                $sync_last_status = $val['sync_last_status'] ? __('Success', $this->localizationName) : __('Error', $this->localizationName);
                $sync_last_status_css = $val['sync_last_status'] ? 'success' : 'error';

                $sync_body[] = '<tr>
                    <td style="' . ($is_child ? 'padding-left: 20px;' : '') . '">
                        <a href="' . $link_edit . '" target="_blank" style="color: #b3b3b3;">' . $val['asin'] . '</a> / <a href="' . $link_edit . '" target="_blank" style="color: #b3b3b3;">#' . $val['id'] . '</a>
                    </td>
                    <td>' . $sync_hits . '</td>
                    <td><span class="' . $sync_last_status_css . '">' . $sync_last_status . '</span></td>
                    <td>' . $sync_last_date . '</td>
                </tr>';
                $cc++;
            }
			if( $limit != 0 ){
				$sync_body[] = '<tr>
                    <td colspan="5"><a href="' . ( $this->view_in_browser ) . '" style="background:#bdc3c7;padding: 2px 10px 2px 10px;color: #fff;text-decoration: none;border-radius: 4px;">View all statistics on Web Browser</a></td>
                </tr>';
			}
            $sync_body = implode("\n", $sync_body);

            $perf_body = array();
            $cc = 0;
            foreach ( (array) $p['products'] as $key => $val ) {
            	if( $limit != 0 && $cc >= $limit ){
            		continue;
            	}
                $link_edit = sprintf( admin_url('post.php?post=%s&action=edit'), $val['id']);
                $is_child = $val['post_parent'] > 0 ? true : false;

                $perf_body[] = '<tr>
                    <td style="' . ($is_child ? 'padding-left: 20px;' : '') . '">
                        <span style="width: 45px; height: 20px; position: relative;"><span style="background: #5A1977;width: 34px;height: 22px;line-height: 22px;border-radius: 5px;font-weight: bold;color: #fff;text-align: center;margin-top: -10px;vertical-align: center; padding: 2px 5px 2px 5px;margin-right: 5px;">#' . ($cc+1) . '</span></span>
                        <a href="' . $link_edit . '" target="_blank" style="color: #b3b3b3;">' . $val['asin'] . '</a> / <a href="' . $link_edit . '" target="_blank" style="color: #b3b3b3;">#' . $val['id'] . '</a>
                    </td>
                    <td><i style="padding: 2px 8px 2px 8px;border-radius: 4px;background: #f39c12;color: #fff;" original-title="">' . $val['hits'] . '</i></td>
                    <td><i style="padding: 2px 8px 2px 8px;border-radius: 4px;background: #1abc9c;color: #fff;" original-title="">' . $val['addtocart'] . '</i></td>
                    <td><i style="padding: 2px 8px 2px 8px;border-radius: 4px;background: #3498db;color: #fff;" original-title="">' . $val['redirect_to_amazon'] . '</i></td>
                </tr>';
                $cc++;
            }

			if( $limit != 0 ){
				$perf_body[] = '<tr>
                    <td colspan="5"><a href="' . ( $this->view_in_browser ) . '" style="background:#bdc3c7;padding: 2px 10px 2px 10px;color: #fff;text-decoration: none;border-radius: 4px;">View all statistics on Web Browser</a></td>
                </tr>';
			}
            $perf_body = implode("\n", $perf_body);

            $ret = array(
                // synchronization
                'nb_success'                    => isset($s['nb_success']) ? (int) $s['nb_success'] : 0,
                'nb_error'                      => isset($s['nb_error']) ? (int) $s['nb_error'] : 0,
                'sync_head'                     => $sync_head,
                'sync_body'                     => $sync_body,

                // performance
                'total_nb'                      => isset($p['total_nb']) ? (int) $p['total_nb'] : 0,
                'total_hits'                    => isset($p['total_hits']) ? (int) $p['total_hits'] : 0,
                'total_addtocart'               => isset($p['total_addtocart']) ? (int) $p['total_addtocart'] : 0,
                'total_redirect_to_amazon'      => isset($p['total_redirect_to_amazon']) ? (int) $p['total_redirect_to_amazon'] : 0,
                'perf_head'                     => $perf_head,
                'perf_body'                     => $perf_body,
            );
 
            return $ret;
        }


        /**
         * Ajax requests
         */
        public function ajax_request_settings()
        {
            //global $wpdb;
            $request = array(
                'action'                        => isset($_REQUEST['subaction']) ? $_REQUEST['subaction'] : '',
            );
            extract($request);
            
            $ret = array(
                'status'            => 'invalid',
                'current_date'      => date('Y-m-d H:i:s'),
                'html'              => '<span class="error">' . __('Invalid action!', $this->the_plugin->localizationName) . '</span>',
            );
            
            if ( empty($action) || !in_array($action, array('getStatus', 'send_report', 'view_in_browser')) ) {
                die(json_encode($ret));
            }
    
            if ( $action == 'getStatus' ) {
                
                $notifyStatus = get_option( self::$report_alias_act, array() );
                if ( $notifyStatus === false || !isset($notifyStatus["report"]) ) {
                    $ret = array_merge($ret, array(
                        'html'      => '<span class="error">' . __('No status saved yet from Send Report Now!', $this->the_plugin->localizationName) . '</span>',
                    ));
                } else {
                    $ret = array_merge($ret, array(
                        'status'    => 'valid',
                        'html'      => $notifyStatus["report"]["html"],
                    ));
                }
                die(json_encode($ret));
			
			} else if ( $action == 'view_in_browser' ) {
				
				$log_id = isset($_REQUEST['log_id']) ? $_REQUEST['log_id'] : 0;
				$this->view_in_browser = admin_url( 'admin-ajax.php?action=wwcAmzAff_report_settings&subaction=view_in_browser&log_id=' . $log_id );
				
				$row_data = (array) $this->get_log_data( $log_id );
				$html = $this->_get_report_products_status( $row_data, 'email' );
				die( $html );
				
			} else if ( $action == 'send_report' ) {

				$this->device = 'email_';
				
                // current report
                $report_data = $this->build_current_report();
                $this->report_send_mail( $report_data );

                $notifyStatus = get_option( self::$report_alias_act, array() );
                {
                    $ret = array_merge($ret, array(
                        'status'    => 'valid',
                        'html'      => '<span class="success">' . sprintf( __('last operation: <em>'.str_replace('_', ' ', $action).'</em> | execution date: <em>%s</em>.', $this->the_plugin->localizationName), $ret['current_date'] ) . '</span>',
                    ));
                }
                
                $notifyStatus["report"] = $ret;
                update_option( self::$report_alias_act, (array) $notifyStatus );
            }
            die(json_encode($ret));
        }

        public function ajax_request()
        {
            global $wpdb;
            $request = array(
                'action'                        => isset($_REQUEST['subaction']) ? $_REQUEST['subaction'] : '',
                'module'                        => isset($_REQUEST['module']) ? $_REQUEST['module'] : 'synchronization',
                
                'id'                            => isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0,
            );
            extract($request);
            
            $ret = array(
                'status'        => 'invalid',
                'msg'           => '<div class="wwcAmzAff-sync-settings-msg wwcAmzAff-message wwcAmzAff-error">' . __('Invalid action!', $this->the_plugin->localizationName) . '</div>',
            );
            
            if ( empty($action) || !in_array($action, array('load_logs', 'view_log')) ) {
                die(json_encode($ret));
            }
   
            if ( $action == 'load_logs' ) {
                
                $productsList = $this->get_rows( $module );

                $ret = array_merge($ret, array(
                    'status'    => 'valid',
                    'msg'       => '',
                    'html'      => implode(PHP_EOL, isset($productsList['html']) ? $productsList['html'] : array()),
                    'nb'        => isset($productsList['nb']) ? $productsList['nb'] : 0,
                    'nbv'       => isset($productsList['nbv']) ? $productsList['nbv'] : 0,
                ));

            } else if ( $action == 'view_log' ) {
                
                $html = $this->get_view_log( $request );
                
                $ret = array_merge($ret, array(
                    'status'    => 'valid',
                    'msg'       => '',
                    'html'      => $html,
                ));
            }
            die(json_encode($ret));
        }


        /**
         * Utils
         */
        private function log_nice_format( $val ) {
            return ucwords( str_replace('_', ' ', $val) );
        }
        
        private function sort_hight_to_low( $a, $subkey ) {
            if ( empty($a) || !is_array($a) ) return array();

            $b = array();
            foreach($a as $k=>$v) {
                $b["$k"] = strtolower($v["$subkey"]);
            }
            arsort($b);
            foreach($b as $key=>$val) {
                $c["$key"] = $a["$key"];
            }
            return $c;
        }
    }
}
 
// Initialize the wwcAmzAffReport class
$wwcAmzAffReport = wwcAmzAffReport::getInstance();