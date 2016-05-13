<?php
/*
* Define class wwcAmzAffServerStatus
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
!defined('ABSPATH') and exit;
if (class_exists('wwcAmzAffRemoteSupportAjax') != true) {
    class wwcAmzAffRemoteSupportAjax extends wwcAmzAffRemoteSupport
    {
    	public $the_plugin = null;
		private $module_folder = null;
		private $the_api_url = 'http://support.aa-team.com/endpoint.php?';
		
		/*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct( $the_plugin=array() )
        {
        	$this->the_plugin = $the_plugin;
			$this->module_folder = $this->the_plugin->cfg['paths']['plugin_dir_url'] . 'modules/remote_support/';
			
			// ajax  helper
			add_action('wp_ajax_wwcAmzAffRemoteSupportRequest', array( &$this, 'ajax_request' ));
		}
		
		/*
		* ajax_request, method
		* --------------------
		*
		* this will create requests to 404 table
		*/
		public function ajax_request()
		{
			$return = array();
			$actions = isset($_REQUEST['sub_actions']) ? explode(",", $_REQUEST['sub_actions']) : '';
			
			if( in_array( 'open_ticket', array_values($actions)) ){
				$params = array();
				if( isset($_REQUEST['params']) ){
					parse_str( $_REQUEST['params'], $params );
				}
				
				// get plugin details based on IPC 
				$ipc = get_option( 'wwcAmzAff_register_key', true );
				
				// validate the IPC
				$validateIPC = $this->getRemote( array(
					'act' => 'validateIPC',
					'params' => 'ipc=' . $ipc
				) ); 
				
				$validateIPCResponse = isset($validateIPC['response']['validateIPC']) ? $validateIPC['response']['validateIPC'] : array(); 
				 
				if( isset($validateIPC['response']['validateIPC']['status']) && $validateIPC['response']['validateIPC']['status'] == 'valid' ){ 
					// validate the IPC
					$newTicket = $this->getRemote( array(
						'act' => 'addTicketRemote',
						'params' => array(
							'ticket' => array(
								'subject' => $params['ticket_subject'],
								'message' => $params['ticket_details'],
								'site_url' => $params['wwcAmzAff-site_url'],
								'wp_username' => $params['wwcAmzAff-wp_username'],
								'wp_password' => $params['wwcAmzAff-wp_password'],
								'ftp_username' => '',
								'ftp_password' => '',
								'access_key' => $params['wwcAmzAff-access_key'],
								'access_url' => $params['wwcAmzAff-access_url'],
							)
						),
						'ipc-code' => $ipc,
						'token' => isset($_REQUEST['token']) ? $_REQUEST['token'] : '',
						'envato-details' => $validateIPC['response']['validateIPC']
					) );
					
					$return = isset($newTicket['response']['addTicket']) ? $newTicket['response']['addTicket'] : array(); 
				}
				else{
					$return = array(
						'status' => 'invalid',
						'msg'	=>  $validateIPC['response']['validateIPC']['msg']
					);
				}
			}
			 
			if( in_array( 'check_auth', array_values($actions)) ){

				if( isset($_REQUEST['params']['token']) ){
					$token = $_REQUEST['params']['token'];
					
					$checkAuth = $this->getRemote( array(
						'act' => 'check_auth',
						'token' => $token
					) ); 
					
					$return = isset($checkAuth['response']['check_auth']) ? $checkAuth['response']['check_auth'] : array(); 
				}
			}

			if( in_array( 'remote_register_and_login', array_values($actions)) ){
				$params = array();
				if( isset($_REQUEST['params']) ){
					parse_str( $_REQUEST['params'], $params );
				}
				
				$envato_username = get_option('wwcAmzAff_register_buyer', true);
				
				// try to login
				$register = $this->getRemote( array(
					'act' => 'register',
					'name' => isset($params["wwcAmzAff-name-register"]) ? $params["wwcAmzAff-name-register"] : '',
					'email' => isset($params["wwcAmzAff-email-register"]) ? $params["wwcAmzAff-email-register"] : '',
					'envato-username' => $envato_username != false ? $envato_username : '',
					'password' => isset($params["wwcAmzAff-password-register"]) ? $params["wwcAmzAff-password-register"] : ''
				) );
		
				$return = isset($register['response']['register']) ? $register['response']['register'] : array(); 
				
				if( isset($return['token']) && trim($return['token']) != "" ){ 
					// save the user support token
					update_option( 'wwcAmzAff_support_login_token', $return['token'] );
				}
			}
			
			if( in_array( 'remote_login', array_values($actions)) ){
				$params = array();
				if( isset($_REQUEST['params']) ){
					parse_str( $_REQUEST['params'], $params );
				}
				
				// try to login
				$login = $this->getRemote( array(
					'act' => 'login',
					'email' => isset($params["wwcAmzAff-email"]) ? $params["wwcAmzAff-email"] : '',
					'password' => isset($params["wwcAmzAff-password"]) ? $params["wwcAmzAff-password"] : '',
					'remember' => isset($params["wwcAmzAff-remember"]) && $params["wwcAmzAff-remember"] == 'on' ? true : false
				) );
				
				$return = isset($login['response']['login']) ? $login['response']['login'] : array(); 
				
				if( isset($return['token']) && trim($return['token']) != "" ){ 
					// save the user support token
					update_option( 'wwcAmzAff_support_login_token', $return['token'] );
				}
			}
			
			if( in_array( 'access_details', array_values($actions)) ){
				$params = array();
				if( isset($_REQUEST['params']) ){
					parse_str( $_REQUEST['params'], $params );
				}
			
				// create wordpress user administrator
				if( isset($params['wwcAmzAff-create_wp_credential']) && trim($params['wwcAmzAff-create_wp_credential']) == 'yes' ){
					$user_id = wp_create_user( 
						'aateam_support', 
						$params['wwcAmzAff-password'], 
						'support@aa-team.com'
					);
				    if ( is_int($user_id) ){
				      $wp_user_object = new WP_User($user_id);
				      $wp_user_object->set_role('administrator');
					}
					
					// update user password
					else{
						$user = get_user_by( 'email', 'support@aa-team.com' );
						wp_update_user( array ( 
							'ID' => $user->ID, 
							'user_pass' => $params['wwcAmzAff-password'] 
						) ) ;
					} 
				}
				
				// create file access 
				if( isset($params['wwcAmzAff-allow_file_remote']) && trim($params['wwcAmzAff-allow_file_remote']) == 'yes' ){
					$key = isset($params['wwcAmzAff-key']) ? $params['wwcAmzAff-key'] : md5(uniqid());
					$access_path = isset($params['wwcAmzAff-access_path']) ? $params['wwcAmzAff-access_path'] : ABSPATH;
					
					// try to write the file access path
					// load WP_Filesystem 
					include_once ABSPATH . 'wp-admin/includes/file.php';
				   	WP_Filesystem();
					global $wp_filesystem;
					
					$acces_content = '<?php
$aa_tunnel_config = array(
	"key" => "' . ( $key ) . '",
	"url" => "' . ( $this->module_folder ) . 'remote_tunnel.php",
	"path"=> "' . ( $access_path ) . '"
);';
					$wp_filesystem->put_contents( 
						$this->the_plugin->cfg['paths']['plugin_dir_path'] . 'modules/remote_support/remote_init.php', 
						$acces_content 
					); 
				}
				
				// save the user details into DB on options table 
				update_option( 'wwcAmzAff_remote_access', $params ); 
				
				$return = array(
					'status' => 'valid'
				);
			}
			
			die(json_encode($return));
		}
		
		private function getRemote( $params=array() )
		{ 
			$response = wp_remote_post( $this->the_api_url, array(
					'method' => 'POST',
					'timeout' => 20,
					'redirection' => 5,
					'httpversion' => '1.0',
					'blocking' => true,
					'headers' => array(),
					'body' => $params
				)
			);

			// If there's error
            if ( is_wp_error( $response ) ){
            	return array(
					'status' 	=> 'invalid',
					'error_code' => '500',
					'url' 		=> $this->the_api_url . http_build_query( $params )
				);
            }
        	$body = wp_remote_retrieve_body( $response );
			
			//var_dump('<pre>',$this->the_api_url . http_build_query( $params ),$body,'</pre>');  
	        
	        return json_decode( $body, true );
		}
    }
}