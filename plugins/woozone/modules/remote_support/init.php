<?php
/*
* Define class wwcAmzAffRemoteSupport
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;

if (class_exists('wwcAmzAffRemoteSupport') != true) {
    class wwcAmzAffRemoteSupport
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
		private $module = '';

		static protected $_instance;

        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct()
        {
        	global $wwcAmzAff;

        	$this->the_plugin = $wwcAmzAff;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/remote_support/';
			$this->module = $this->the_plugin->cfg['modules']['remote_support'];

			if (is_admin()) {
	            add_action('admin_menu', array( &$this, 'adminMenu' ));
			}

			// load the ajax helper
			require_once( $this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/remote_support/ajax.php' );
			new wwcAmzAffRemoteSupportAjax( $this->the_plugin );
        }

		/**
	    * Singleton pattern
	    *
	    * @return wwcAmzAffRemoteSupport Singleton instance
	    */
	    static public function getInstance()
	    {
	        if (!self::$_instance) {
	            self::$_instance = new self;
	        }

	        return self::$_instance;
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
    			$this->the_plugin->alias . " " . __('AA-Team Remote Support', $this->the_plugin->localizationName),
	            __('Remote Support', $this->the_plugin->localizationName),
	            'manage_options',
	            $this->the_plugin->alias . "_remote_support",
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
		private function printBaseInterface()
		{
			global $wpdb;
			
			$remote_access = get_option( 'wwcAmzAff_remote_access', true );
			$login_token = get_option( 'wwcAmzAff_support_login_token', true );
?>
		<script type="text/javascript" src="<?php echo $this->module_folder;?>app.class.js" ></script>
		<div id="wwcAmzAff-wrapper" class="fluid wrapper-wwcAmzAff">
			
			<?php
			// show the top menu
			wwcAmzAffAdminMenu::getInstance()->make_active('general|remote_support')->show_menu();
			?>
			
			<!-- Main loading box -->
			<div id="wwcAmzAff-main-loading">
				<div id="wwcAmzAff-loading-overlay"></div>
				<div id="wwcAmzAff-loading-box">
					<div class="wwcAmzAff-loading-text"><?php _e('Loading', $this->the_plugin->localizationName);?></div>
					<div class="wwcAmzAff-meter wwcAmzAff-animate" style="width:86%; margin: 34px 0px 0px 7%;"><span style="width:100%"></span></div>
				</div>
			</div>

			<!-- Content -->
			<div id="wwcAmzAff-content">
				
				<h1 class="wwcAmzAff-section-headline">
					<?php 
					if( isset($this->module['remote_support']['in_dashboard']['icon']) ){
						echo '<img src="' . ( $this->module_folder . $this->module['remote_support']['in_dashboard']['icon'] ) . '" class="wwcAmzAff-headline-icon">';
					}
					?>
					<?php echo $this->module['remote_support']['menu']['title'];?>
					<span class="wwcAmzAff-section-info"><?php echo $this->module['remote_support']['description'];?></span>
					<?php
					$has_help = isset($this->module['remote_support']['help']) ? true : false;
					if( $has_help === true ){
						
						$help_type = isset($this->module['remote_support']['help']['type']) && $this->module['remote_support']['help']['type'] ? 'remote' : 'local';
						if( $help_type == 'remote' ){
							echo '<a href="#load_docs" class="wwcAmzAff-show-docs" data-helptype="' . ( $help_type ) . '" data-url="' . ( $this->module['remote_support']['help']['url'] ) . '">HELP</a>';
						} 
					}
					echo '<a href="#load_docs" class="wwcAmzAff-show-feedback" data-helptype="' . ( 'remote' ) . '" data-url="' . ( $this->the_plugin->feedback_url ) . '" data-operation="feedback">Feedback</a>';
					?>
				</h1>

				<!-- Container -->
				<div class="wwcAmzAff-container clearfix">

					<!-- Main Content Wrapper -->
					<div id="wwcAmzAff-content-wrap" style="margin-top: 15px;">

						<!-- Content Area -->
						<div id="wwcAmzAff-content-area">
							
							<div class="wwcAmzAff-grid_4" id="wwcAmzAff-boxid-access">
							    <div class="wwcAmzAff-panel">
							        <div class="wwcAmzAff-panel-header">
							            <span class="wwcAmzAff-panel-title">
											Remote Support Details
										</span>
							        </div>
							        <div class="wwcAmzAff-panel-content">
							            <form id="wwcAmzAff_access_details" class="wwcAmzAff-form">
							                <div class="wwcAmzAff-form-row">
							                    <label for="protocol">Create WP Credential</label>
							                    <div class="wwcAmzAff-form-item large">
							                        <span class="formNote">This will automatically create a wordpress administrator account for AA-Team support team</span>
							                        
							                        <?php 
							                        $selected = 'yes';
													if( 
														!isset($remote_access['wwcAmzAff-create_wp_credential']) ||
														$remote_access['wwcAmzAff-create_wp_credential'] == 'no'
													){
														$selected = 'no';
													}
							                        ?>
							                        <select id="wwcAmzAff-create_wp_credential" name="wwcAmzAff-create_wp_credential" style="width:80px;">
							                            <option value="yes" <?php echo ($selected == 'yes' ? 'selected="selected"' : '');?>>Yes</option>
							                            <option value="no" <?php echo ($selected == 'no' ? 'selected="selected"' : '');?>>NO</option>
							                        </select>
							                        
							                        <div class="wwcAmzAff-wp-credential" <?php echo ( isset($remote_access['wwcAmzAff-create_wp_credential']) && trim($remote_access['wwcAmzAff-create_wp_credential']) == 'yes' ? 'style="display:block"' : 'style="display:none"' );?>>
							                        	<table class="wwcAmzAff-table" style="border-collapse: collapse;">
							                        		<tr>
							                        			<td width="160">Admin username:</td>
							                        			<td>aateam_support</td>
							                        		</tr>
							                        		<tr>
							                        			<td>Admin password:</td>
							                        			<td>
								                        			<?php  
									                        			$admin_password = isset($remote_access['wwcAmzAff-password']) ? $remote_access['wwcAmzAff-password'] : $this->generateRandomString(10);
								                        			?>
								                        			<input type="text" name="wwcAmzAff-password" id="wwcAmzAff-password" value="<?php echo $admin_password;?>" />
							                        			</td>
							                        		</tr>
							                        	</table>
							                        	<div class="wwcAmzAff-message wwcAmzAff-info"><i>(this details will be send automatically on your open ticket)</i></div>
							                        </div>
							                    </div>
							                </div>
							                <div class="wwcAmzAff-form-row">
							                    <label for="onsite_cart">Allow file remote access</label>
							                    <div class="wwcAmzAff-form-item large">
							                        <span class="formNote">This will automatically give access for AA-Team support team to your chosen server path</span>
							                        
							                        <?php 
							                        $selected = 'yes';
													if( 
														!isset($remote_access['wwcAmzAff-allow_file_remote']) ||
														$remote_access['wwcAmzAff-allow_file_remote'] == 'no'
													){
														$selected = 'no';
													}
							                        ?>
							                        <select id="wwcAmzAff-allow_file_remote" name="wwcAmzAff-allow_file_remote" style="width:80px;">
							                            <option value="yes" <?php echo ($selected == 'yes' ? 'selected="selected"' : '');?>>Yes</option>
							                            <option value="no" <?php echo ($selected == 'no' ? 'selected="selected"' : '');?>>NO</option>
							                        </select>
							                        
							                        <div class="wwcAmzAff-file-access-credential" <?php echo ( isset($remote_access['wwcAmzAff-allow_file_remote']) && trim($remote_access['wwcAmzAff-allow_file_remote']) == 'yes' ? 'style="display:block"' : 'style="display:none"' );?>>
							                        	<table class="wwcAmzAff-table" style="border-collapse: collapse;">
							                        		<tr>
							                        			<td width="120">Access key:</td>
							                        			<td>
							                        				<?php 
									                        			$access_key = isset($remote_access['wwcAmzAff-key']) ? $remote_access['wwcAmzAff-key'] : md5( $this->generateRandomString(12) );
								                        			?>
							                        				<input type="text" name="wwcAmzAff-key" id="wwcAmzAff-key" value="<?php echo $access_key;?>" />
							                        			</td>
							                        		</tr>
							                        		<tr>
							                        			<td width="120">Access path:</td>
							                        			<td>
							                        				<input type="text" name="wwcAmzAff-access_path" id="wwcAmzAff-access_path" value="<?php echo isset($remote_access['wwcAmzAff-access_path']) ? $remote_access['wwcAmzAff-access_path'] : ABSPATH;?>" />
							                        			</td>
							                        		</tr>
							                        	</table>
							                        	<div class="wwcAmzAff-message wwcAmzAff-info"><i>(this details will be send automatically on your open ticket)</i> </div>
							                        </div>
							                    </div>
							                </div>
							                <div style="display:none;" id="wwcAmzAff-status-box" class="wwcAmzAff-message"></div>
							                <div class="wwcAmzAff-button-row">
							                    <input type="submit" class="wwcAmzAff-button blue" value="Save Remote Access" style="float: left;" />
							                </div>
							            </form>
							        </div>
							    </div>
							</div>
							
							<div class="wwcAmzAff-grid_4" id="wwcAmzAff-boxid-logininfo">
	                        	<div class="wwcAmzAff-panel">
									<div class="wwcAmzAff-panel-content">
										<div class="wwcAmzAff-message wwcAmzAff-info">
											
											<?php
											if( !isset($login_token) || trim($login_token) == "" ){
											?>
												In order to contact AA-Team support team you need to login into support.aa-team.com
											<?php 
											}
											
											else{
											?>
												Test your token is still valid on AA-Team support website ...
												<script>
													wwcAmzAffRemoteSupport.checkAuth( '<?php echo $login_token;?>' );
												</script>
											<?php
											}
											?>
										</div>
				            		</div>
								</div>
							</div>
							
							<div class="wwcAmzAff-grid_2" id="wwcAmzAff-boxid-login" style="display:none">
	                        	<div class="wwcAmzAff-panel">
	                        		<div class="wwcAmzAff-panel-header">
										<span class="wwcAmzAff-panel-title">
											Login
										</span>
									</div>
									<div class="wwcAmzAff-panel-content">
										<form class="wwcAmzAff-form" id="wwcAmzAff-form-login">
											<div class="wwcAmzAff-form-row">
												<label class="wwcAmzAff-form-label" for="email">Email <span class="required">*</span></label>
												<div class="wwcAmzAff-form-item large">
													<input type="text" id="wwcAmzAff-email" name="wwcAmzAff-email" class="span12">
												</div>
											</div>
											<div class="wwcAmzAff-form-row">
												<label class="wwcAmzAff-form-label" for="password">Password <span class="required">*</span></label>
												<div class="wwcAmzAff-form-item large">
													<input type="password" id="wwcAmzAff-password" name="wwcAmzAff-password" class="span12">
												</div>
											</div>
											
											<div class="wwcAmzAff-form-row" style="height: 79px;">
												<input type="checkbox" id="wwcAmzAff-remember" name="wwcAmzAff-remember" style="float: left; position: relative; bottom: -12px;">
												<label for="wwcAmzAff-remember" class="wwcAmzAff-form-label" style="width: 120px;">&nbsp;Remember me</label>
											</div>
											
											<div class="wwcAmzAff-message wwcAmzAff-error" style="display:none;"></div>
	
											<div class="wwcAmzAff-button-row">
												<input type="submit" class="wwcAmzAff-button blue" value="Login" style="float: left;" />
											</div>
										</form>
				            		</div>
								</div>
							</div>
							
							<div class="wwcAmzAff-grid_2" id="wwcAmzAff-boxid-register" style="display:none">
	                        	<div class="wwcAmzAff-panel">
	                        		<div class="wwcAmzAff-panel-header">
										<span class="wwcAmzAff-panel-title">
											Register
										</span>
									</div>
									<div class="wwcAmzAff-panel-content">
										<form class="wwcAmzAff-form" id="wwcAmzAff-form-register">
											<div class="wwcAmzAff-message error" style="display:none;"></div>
											
											<div class="wwcAmzAff-form-row">
												<label class="wwcAmzAff-form-label">Your name <span class="required">*</span></label>
												<div class="wwcAmzAff-form-item large">
													<input type="text" id="wwcAmzAff-name-register" name="wwcAmzAff-name-register" class="span12">
												</div>
											</div>
											
											<div class="wwcAmzAff-form-row">
												<label class="wwcAmzAff-form-label">Your email <span class="required">*</span></label>
												<div class="wwcAmzAff-form-item large">
													<input type="text" id="wwcAmzAff-email-register" name="wwcAmzAff-email-register" class="span12">
												</div>
											</div>
											
											<div class="wwcAmzAff-form-row">
												<label class="wwcAmzAff-form-label">Create a password <span class="required">*</span></label>
												<div class="wwcAmzAff-form-item large">
													<input type="password" id="wwcAmzAff-password-register" name="wwcAmzAff-password-register" class="span6">
												</div>
											</div>
											
											<div class="wwcAmzAff-button-row">
												<input type="submit" class="wwcAmzAff-button blue" value="Register and login" style="float: left;" />
											</div>
										</form>
				            		</div>
								</div>
							</div>
							
							<div class="wwcAmzAff-grid_4" style="display: none;" id="wwcAmzAff-boxid-ticket">
							    <div class="wwcAmzAff-panel">
							        <div class="wwcAmzAff-panel-header">
							            <span class="wwcAmzAff-panel-title">
											Details about problem:
										</span>
							        </div>
							        <div class="wwcAmzAff-panel-content">
							            <form id="wwcAmzAff_add_ticket" class="wwcAmzAff-form">
							            	<input type="hidden" name="wwcAmzAff-token" id="wwcAmzAff-token" value="<?php echo $login_token;?>" />
							            	<input type="hidden" name="wwcAmzAff-site_url" id="wwcAmzAff-site_url" value="<?php echo admin_url();?>" />
							            	<input type="hidden" name="wwcAmzAff-wp_username" id="wwcAmzAff-wp_username" value="aateam_support" />
							            	<input type="hidden" name="wwcAmzAff-wp_password" id="wwcAmzAff-wp_password" value="" />
							            	
							            	<input type="hidden" name="wwcAmzAff-access_key" id="wwcAmzAff-access_key" value="" />
							            	<input type="hidden" name="wwcAmzAff-access_url" id="wwcAmzAff-access_url" value="<?php echo urlencode( str_replace("http://", "", $this->module_folder) . 'remote_tunnel.php');?>" />
							            	
							                
							                <div class="wwcAmzAff-form-row">
												<label class="wwcAmzAff-form-label">Ticket Subject<span class="required">*</span></label>
												<div class="wwcAmzAff-form-item large">
													<input type="text" id="ticket_subject" name="ticket_subject" class="span6">
												</div>
											</div>
											
							                <div class="wwcAmzAff-form-row">
						                        <?php
												wp_editor( 
													'', 
													'ticket_details', 
													array( 
														'media_buttons' => false,
														'textarea_rows' => 40,	
													) 
												); 
						                        ?>
							                </div>
							                <div style="display:none;" id="wwcAmzAff-status-box" class="wwcAmzAff-message wwcAmzAff-success"></div>
							                <div class="wwcAmzAff-button-row">
							                    <input type="submit" class="wwcAmzAff-button green" value="Open ticket on support.aa-team.com" style="float: left;" />
							                </div>
							            </form>
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

		private function generateRandomString($length = 6) 
		{
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@#$%^*()';
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, strlen($characters) - 1)];
		    }
		    return $randomString;
		}
    }
}

// Initialize the wwcAmzAffRemoteSupport class
$wwcAmzAffRemoteSupport = wwcAmzAffRemoteSupport::getInstance();
