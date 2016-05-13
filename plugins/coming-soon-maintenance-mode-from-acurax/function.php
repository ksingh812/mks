<?php
function acx_csma_display_template_array_filter_hook()
{
	global $acx_csma_template_array;
	$acx_csma_template_array = array();
	$acx_csma_template_array = apply_filters('acx_csma_display_template_array_filter',$acx_csma_template_array);	
	
} add_action('init','acx_csma_display_template_array_filter_hook');
function acx_csma_filter_lw_pr($acx_csma_template_array)
{
		return $acx_csma_template_array;
} add_filter('acx_csma_display_template_array_filter','acx_csma_filter_lw_pr',5);

function acx_csma_appearence_array_default_filter_hook()
{
	global $acx_csma_appearence_array_default;
	$acx_csma_appearence_array_default = array();
	$acx_csma_appearence_array_default = apply_filters('acx_csma_appearence_array_default_filter',$acx_csma_appearence_array_default);	
	
} add_action('init','acx_csma_appearence_array_default_filter_hook');

function acx_csma_appearence_array_default_lw_pr($acx_csma_appearence_array_default)
{
		return $acx_csma_appearence_array_default;
} add_filter('acx_csma_appearence_array_default_filter','acx_csma_appearence_array_default_lw_pr',5);

$acx_csma_display_var_arr = get_option('acx_csma_display_var_arr');
if(is_serialized($acx_csma_display_var_arr))
{ 
	$acx_csma_display_var_arr = unserialize($acx_csma_display_var_arr); 
}
if(empty($acx_csma_display_var_arr))
{
	$acx_csma_display_var_arr=array(
								'year'=>array(
											'singular'=>'Year',
											'plural'=>'Years',
											'default_singular'=>'Year',
											'default_plural'=>'Years'
											),
								'month'=>array(
											'singular'=>'Month',
											'plural'=>'Months',
											'default_singular'=>'Month',
											'default_plural'=>'Months'
											),
								'week'=>array(
											'singular'=>'Week',
											'plural'=>'Weeks',
											'default_singular'=>'Week',
											'default_plural'=>'Weeks'
											),
								'day'=>array(
											'singular'=>'Day',
											'plural'=>'Days',
											'default_singular'=>'Day',
											'default_plural'=>'Days'
											),
								'hour'=>array(
											'singular'=>'Hour',
											'plural'=>'Hours',
											'default_singular'=>'Hour',
											'default_plural'=>'Hours'
											),
								'minute'=>array(
											'singular'=>'Minute',
											'plural'=>'Minutes',
											'default_singular'=>'Minute',
											'default_plural'=>'Minutes'
											),
								'second'=>array(
											'singular'=>'Second',
											'plural'=>'Seconds',
											'default_singular'=>'Second',
											'default_plural'=>'Seconds'
											),
								'next'=>array(
											'singular'=>'Next',
											'default_singular'=>'Next'
											)
							);
	if(!is_serialized($acx_csma_display_var_arr))
	{ 
		$acx_csma_display_var_arr = serialize($acx_csma_display_var_arr); 
	}
	update_option('acx_csma_display_var_arr',$acx_csma_display_var_arr);
}
function acx_csma_styles() 
{	
	wp_register_style('acx_csmaadmin_style', plugins_url('css/admin.css', __FILE__));
	wp_enqueue_style('acx_csmaadmin_style');
	wp_register_style('acx_csmabox_style', plugins_url('css/acx_csma_box.css', __FILE__));
	wp_enqueue_style('acx_csmabox_style');
	wp_register_style('acx_datepick_style', plugins_url('css/jquery.datetimepicker.css', __FILE__));
	wp_enqueue_style('acx_datepick_style');
}
add_action('admin_enqueue_scripts', 'acx_csma_styles');
function acx_csma_date_picker_scripts()
{
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
}
add_action('admin_enqueue_scripts','acx_csma_date_picker_scripts');

function acx_csma_colorpicker_scripts() 
{
	wp_enqueue_style( 'farbtastic' );
	wp_enqueue_script( 'farbtastic','',array( 'jquery' ) );
}

// color picker
 if(ISSET($_GET['page']))
{
	$acx_csma_page=$_GET['page'];
}
else
{
	$acx_csma_page="";
}	
if($acx_csma_page == "Acurax-Coming-Soon-Maintenance-Mode-Settings")
{
	add_action('admin_init','acx_csma_colorpicker_scripts');
}
//Date picker
function acx_csma_script()
{
	wp_register_script('acx_csma_datepick_script', plugins_url('js/jquery.datetimepicker.js', __FILE__)); 
	wp_enqueue_script('acx_csma_datepick_script','',array( 'jquery' ));
}
add_action('admin_enqueue_scripts', 'acx_csma_script');
function acx_csma_color_pick()
{
	echo '<script type="text/javascript" src="'.plugins_url('js/color.js', __FILE__). '"></script>';
}	
if($acx_csma_page == "Acurax-Coming-Soon-Maintenance-Mode-Settings")
{
	add_action('admin_head','acx_csma_color_pick');	
}
function filter_acx_csma_template_array($acx_csma_template_array)
{

		$acx_site_url = get_site_url();
		$acx_csma_parent_folder = basename(dirname(__FILE__));
		$acx_csma_template_array['0'] = array(
												'id' => 0,
												'name' =>'Custom Html',
												'index' =>'acx_csma_custom_template',
												'description' => '',
												'parent' =>  $acx_csma_parent_folder,
												'path' => $acx_site_url,
												'thumb' => ACX_CSMA_BASE_LOCATION
												);
		$acx_csma_template_array['1'] = array(
												'id' => 1,
												'name' =>'Template 1',
												'index' =>'acx_csma_template1',
												'description' => '',
												'parent' => $acx_csma_parent_folder,
												'path' => $acx_site_url,
												'thumb' => ACX_CSMA_BASE_LOCATION
												);
		$acx_csma_template_array['2'] = array(
												'id' => 2,
												'name' =>'Template 2',
												'index' =>'acx_csma_template2',
												'description' => '',
												'parent' => $acx_csma_parent_folder,
												'path' => $acx_site_url,
												'thumb' => ACX_CSMA_BASE_LOCATION
												);	
		$acx_csma_template_array['3'] = array(
												'id' => 3,
												'name' =>'Template 3',
												'index' =>'acx_csma_template3',
												'description' => '',
												'parent' => $acx_csma_parent_folder,
												'path' => $acx_site_url,
												'thumb' => ACX_CSMA_BASE_LOCATION
												);
		$acx_csma_template_array['4'] = array(
												'id' => 4,
												'name' =>'Template 4',
												'index' =>'acx_csma_template4',
												'description' => '',
												'parent' => $acx_csma_parent_folder,
												'path' => $acx_site_url,
												'thumb' => ACX_CSMA_BASE_LOCATION
												);
		$acx_csma_template_array['5'] = array(
												'id' => 5,
												'name' =>'Template 5',
												'index' =>'acx_csma_template5',
												'description' => '',
												'parent' => $acx_csma_parent_folder,
												'path' => $acx_site_url,
												'thumb' => ACX_CSMA_BASE_LOCATION
												);

	return $acx_csma_template_array;
}
add_filter('acx_csma_display_template_array_filter','filter_acx_csma_template_array');

$acx_csma_activation_status = get_option('acx_csma_activation_status');
if($acx_csma_activation_status=='')
{
	update_option('acx_csma_activation_status',0);
}
if($acx_csma_activation_status==1)
{
	$acx_csma_display_template=true;
}
else
{
	$acx_csma_display_template=false;
}
if($acx_csma_display_template == true)
{
	$acx_csma_max_date = get_option('acx_csma_date_time');
	$acx_csma_timestamp=current_time('timestamp');
	$acx_csma_auto_launch=get_option('acx_csma_auto_launch'); 

	if($acx_csma_timestamp > $acx_csma_max_date)
	{
		if($acx_csma_auto_launch==0)
		{	
			$acx_csma_display_template=true;
		} else
		{
			$acx_csma_display_template=false;
		}
	}
}
if($acx_csma_display_template==true)
{
	add_action('template_redirect','acx_csma_plugin_activation');
}
function acx_csma_plugin_activation()
{
	global $wpdb,$acx_csma_display_template,$acx_csma_template_path,$acx_csma_template_array;
	if($acx_csma_display_template==true)
	{
		if (is_user_logged_in()) 
		{
			$acx_csma_role_array=get_option('acx_csma_restrict_role');
			if(is_serialized($acx_csma_role_array))
			{
				$acx_csma_role_array = unserialize($acx_csma_role_array); 
			}
			$current_user = wp_get_current_user();
			$roles = $current_user->roles;   //$roles -array
			
			foreach($roles as $key=>$value)
			{
				$user_roles=ucwords($value);	
			}
			if(is_array($acx_csma_role_array))
			{
				if(in_array($user_roles,$acx_csma_role_array)|| $user_roles=="Administrator" || is_super_admin())
				{
					//do not display maintenance page.....
					$acx_csma_display_template=false;
				}
			}
		}
	}
	if($acx_csma_display_template==true)
	{
		$acx_csma_ip_array=get_option('acx_csma_ip_list');
		if($acx_csma_ip_array=="")
		{
		$acx_csma_ip_array = array();	
		}
		if(is_serialized($acx_csma_ip_array))
		{
			$acx_csma_ip_array = unserialize($acx_csma_ip_array); 
		}
		$current_ip = acx_csma_getrealip();
		
		if(is_array($acx_csma_ip_array) && in_array($current_ip,$acx_csma_ip_array))
		{
			// do not display maintenance page.....
			$acx_csma_display_template=false;
		}
	}
	 $acx_csma_display_template = apply_filters('acx_csma_display_template_filter',$acx_csma_display_template);
	function filter_acx_csma_template($acx_csma_display_template)
	{
		if($acx_csma_display_template != '')
		{
			return $acx_csma_display_template;
		}
	}
	
	add_filter('acx_csma_display_template_filter','filter_acx_csma_template',5); 

	if($acx_csma_display_template == true)
	{
		
		$protocol = "HTTP/1.0";
		if ( "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] )
		$protocol = "HTTP/1.1";
		header( "$protocol 503 Service Unavailable", true, 503 );
		$end_time = get_option('acx_csma_date_time');
		if($end_time != "")
		{
		$end_time = date_i18n("D, j M Y H:i:s e", $end_time);
		header( "Retry-After: $end_time" );
		}
		$acx_csma_template=get_option('acx_csma_template');
		if($acx_csma_template == "" || !is_numeric($acx_csma_template))
		{
			$acx_csma_template = 1;
		}
		$acx_csma_base_template=get_option('acx_csma_base_template');
		if(is_array($acx_csma_template_array) && !array_key_exists($acx_csma_template,$acx_csma_template_array) && $acx_csma_base_template !== "")
		{
			$acx_csma_template= $acx_csma_base_template;
		}
		$acx_csma_template_path_loc = WP_CONTENT_DIR."/plugins/".$acx_csma_template_array[$acx_csma_template]['parent'];
		$acx_csma_template_path = $acx_csma_template_path_loc."/templates/".$acx_csma_template."/index.php";
		include_once($acx_csma_template_path);
		exit;
	}
	
}
	
function acx_csma_template_preview()
{
	global $acx_csma_template_array;
	
	if(ISSET($_GET['acx_csma_preview']) && current_user_can( 'manage_options' )){
		$acx_csma_preview=$_GET['acx_csma_preview'];
		if(array_key_exists($acx_csma_preview,$acx_csma_template_array))
		{
			$protocol = "HTTP/1.0";
			if ( "HTTP/1.1" == $_SERVER["SERVER_PROTOCOL"] )
			$protocol = "HTTP/1.1";
			header( "$protocol 503 Service Unavailable", true, 503 );
			$end_time = get_option('acx_csma_date_time');
			if($end_time != "")
			{
			$end_time = date_i18n("D, j M Y H:i:s e", $end_time);
			header( "Retry-After: $end_time" );
			}
			$acx_csma_template_path_loc = WP_CONTENT_DIR."/plugins/".$acx_csma_template_array[$acx_csma_preview]['parent'];
			include_once($acx_csma_template_path_loc."/templates/".$acx_csma_preview."/index.php");
			exit;
		}
	}
}
add_action('template_redirect','acx_csma_template_preview');
// changing launch text
	$acx_csma_appearence_array=acx_csma_get_db_array_value(); 
	if(is_array($acx_csma_appearence_array))
	{
		if(array_key_exists('3',$acx_csma_appearence_array) && array_key_exists('acx_csma_inside_title3',$acx_csma_appearence_array['3']))
		{
			$acx_csma_inside_title3 = $acx_csma_appearence_array['3']['acx_csma_inside_title3'];
			if(strcmp($acx_csma_inside_title3,"Estimate Time Before Lunching") === 0 )
				{
					$acx_csma_appearence_array['3']['acx_csma_inside_title3'] = "Estimate Time Before Launching";
				}
		}
		acx_csma_update_array_value($acx_csma_appearence_array);
	}
// upload images and logos
function acx_csma_upload_images_template_1() 
{
	if(function_exists('wp_enqueue_media'))
	{
		wp_enqueue_media();	
	}
?>
<script type="text/javascript">
function acx_csma_upload_images_template_loader(button_id,uploader_title,uploader_button,hidden_field_id,preview_id)
{                                                       
	if(button_id)
	{
	button_id = "#"+button_id;
	}
	if(uploader_title == "")
	{
	uploader_title = "Choose Image";
	}
	if(uploader_button == "")
	{
	uploader_button = "Select";
	}
	if(hidden_field_id)
	{
	hidden_field_id = "#"+hidden_field_id;
	}
	if(preview_id)
	{
	preview_id = "#"+preview_id;
	}
	var custom_uploader_template_1_1;
	jQuery(button_id).click(function(e) 
	{
		e.preventDefault();
		//If the uploader object has already been created, reopen the dialog
		if (custom_uploader_template_1_1) 
		{
		custom_uploader_template_1_1.open();
		return;
		}
		//Extend the wp.media object
		custom_uploader_template_1_1 = wp.media.frames.file_frame = wp.media({
		title: uploader_title,
		button:
		{
		text: uploader_button
		},
		multiple: false	});
		//When a file is selected, grab the URL and set it as the text field's value
		custom_uploader_template_1_1.on('select', function() 
		{
		attachment = custom_uploader_template_1_1.state().get('selection').first().toJSON();
		// console.log(attachment);
		if(hidden_field_id)
		{
		jQuery(hidden_field_id).val(attachment.id);
		}
		if(preview_id != "")
		{
		jQuery(preview_id).attr('src',attachment.url);
		}
		});
		//Open the uploader dialog
		custom_uploader_template_1_1.open();
	});
}
</script>
<?php
} 
add_action('admin_head', 'acx_csma_upload_images_template_1'); 	

//Quick Request Form
function acx_csma_quick_request_submit_callback()
{
	$acx_name =  $_POST['acx_name'];
	$acx_email =  $_POST['acx_email'];
	$acx_phone =  $_POST['acx_phone'];
	$acx_csma_es =  $_POST['acx_csma_es'];
	$acx_weburl =  $_POST['acx_weburl'];
	$acx_subject =  stripslashes($_POST['acx_subject']);
	$acx_question =  stripslashes($_POST['acx_question']);
	if (!wp_verify_nonce($acx_csma_es,'acx_csma_es'))
	{
	$acx_csma_es == "";
	}
	if(!current_user_can('manage_options'))
	{
	$acx_csma_es == "";
	}
	if($acx_csma_es == "" || $acx_name == "" || $acx_phone == "" || $acx_email == "" || $acx_weburl == "" || $acx_subject == "" || $acx_question == "")
	{
		echo 2;
	} 
else
{
	$current_user = wp_get_current_user();
	$current_user_acx = $current_user->user_email;
	if($current_user_acx == "")
	{
		$current_user_acx = $acx_email;
	}
	$headers[] = 'From: ' . $acx_name . ' <' . $current_user_acx . '>';
	$headers[] = 'Content-Type: text/html; charset=UTF-8'; 
	$message = "Name: ".$acx_name . "\r\n <br>";
	$message = $message . "Email: ".$acx_email . "\r\n <br>";
	if($acx_phone != "")
	{
		$message = $message . "Phone: ".$acx_phone . "\r\n <br>";
	}
	// In case any of our lines are larger than 70 characters, we should use wordwrap()
	$acx_question = wordwrap($acx_question, 70, "\r\n <br>");
	$message = $message . "Request From: CSMA - Expert Help Request Form \r\n <br>";
	$message = $message . "Website: ".$acx_weburl . "\r\n <br>";
	$message = $message . "Question: ".$acx_question . "\r\n <br>";
	$emailed = wp_mail( 'info@acurax.com', $acx_subject, $message, $headers );
	if($emailed)
	{
		echo 1;
	} else
	{
		echo 0;
	}
}
	die(); // this is required to return a proper result
}add_action('wp_ajax_acx_csma_quick_request_submit','acx_csma_quick_request_submit_callback');

 
function acx_csma_add_items($admin_bar)
{
	$args = array(
    'id'    => 'acx_csma_activation_msg',
    'parent' => 'top-secondary',
    'title' => 'Maintenance Mode is Activated',
    'href'  => 'admin.php?page=Acurax-Coming-Soon-Maintenance-Mode-Settings'
    );
	if (!current_user_can('manage_options') ) {
        return;
    }
    $admin_bar->add_menu($args);
}
$acx_csma_activation_status=get_option('acx_csma_activation_status');
if($acx_csma_activation_status == 1 && is_admin())
{
	add_action('admin_bar_menu', 'acx_csma_add_items'); 
}

function acx_csma_subscribe_email()
{
	if (!isset($_POST['acx_csma_subscribe_es'])) die("<br><br>Unknown Error Occurred, Try Again... <a href=''>Click Here</a>");
	if (!wp_verify_nonce($_POST['acx_csma_subscribe_es'],'acx_csma_subscribe_es')) die("<br><br>Unknown Error Occurred, Try Again... <a href=''>Click Here</a>");

	if(ISSET($_POST['name']))
	{
		$name=$_POST['name'];
	}
	else{
		$name="";
	}
	if(ISSET($_POST['email']))
	{
		$email=$_POST['email'];
	}
	else{
		$email="";
	}
	if(ISSET($_POST['ip']))
	{
		$ip=$_POST['ip'];
	}
	else{
		$ip="";
	}

	if(ISSET($_POST['timestamp']))
	{
		$timestamp=$_POST['timestamp'];
	}
	else{
		$timestamp = "";
	}

	$acx_csma_subscribe_details=get_option('acx_csma_subscribe_user_details');
	if($acx_csma_subscribe_details != "")
	{
		if(is_serialized($acx_csma_subscribe_details))
		{ 
			$acx_csma_subscribe_details = unserialize($acx_csma_subscribe_details); 
		}
	}	
	else{
	$acx_csma_subscribe_details=array();
	}	 
	$found=0;
	foreach($acx_csma_subscribe_details as $key => $value)
	{
		if($value['email'] == $email)
		{
		$found=1;
		}
	}
	if($found == 1)
	{
		echo "Exists";
	}
	else{
	$acx_csma_subscribe_details[]= array (
										'name'=> $name,
										'email' => sanitize_email($email),
										'ip' => $ip,
										'timestamp' => $timestamp
											);
	if(!is_serialized($acx_csma_subscribe_details))
	{
		$acx_csma_subscribe_details = serialize($acx_csma_subscribe_details); 
	}
	update_option('acx_csma_subscribe_user_details',$acx_csma_subscribe_details);
	echo "success";
	} 
	die(); // this is required to return a proper result
}
add_action( 'wp_ajax_nopriv_acx_csma_subscribe_email', 'acx_csma_subscribe_email' );

function acx_csma_subscribe_ajax()
{	
	$acx_csma_subscribe_details=get_option('acx_csma_subscribe_user_details');
	if(is_serialized($acx_csma_subscribe_details ))
	{
		$acx_csma_subscribe_details = unserialize($acx_csma_subscribe_details); 
	}	
	if(!empty($acx_csma_subscribe_details)) {
		$filename = 'subscribers-list-' . date('Y-m-d') . '.csv';
		header('Content-Type: text/csv');
		header('Content-Disposition: attachment;filename='.$filename);
		$fp = fopen('php://output', 'w');
		fputcsv($fp, array('Name','Email','Date'));
		foreach ($acx_csma_subscribe_details as $item=> $value) {
			if(ISSET($value['ip']))
			{
				unset($acx_csma_subscribe_details[$item]['ip']);
			}
			if(ISSET($value['timestamp']))
			{	
				$format="Y-m-d H:i:s";
				$acx_csma_subscribe_details[$item]['timestamp']=date_i18n($format, $acx_csma_subscribe_details[$item]['timestamp']);
			}
		}
		foreach ($acx_csma_subscribe_details as $item=> $value) {
			fputcsv($fp, $value);
		}
		fclose($fp);
	}
	die();
}
add_action( 'wp_ajax_acx_csma_subscribe_ajax', 'acx_csma_subscribe_ajax' );
function acx_csma_addon_ua_demo()
{
echo "<div id='acx_csma_addon_demo_ua'><br><hr>
<img src='".plugins_url('/images/addon_ua_demo.png',__FILE__)."' style='border:0px;width:100%;height:auto;' class='acx_csma_info_lb' lb_title='Private Access URL Feature - Premium Addon Plugin' lb_content='<p style=\"font-size:13px;\">You may needs to showcase the website to your friends, contacts or clients to get approval, or get suggestions.<br><br>When website is in under construction mode, they may needs to login or provide you their ip address to grand them access.<br><br>But using Private Access URL Addon, You will get the option to generate a private URL which you can provide to anyone, and they can access your website, They wont see the under construction page until the url expire.<br><br> While generating a URL, You can set expiry, It can be Never, So URL is valid until you delete it. Can set expiry as Hours, So URL will be active for specified hours from their first visit.<br><br>Can also set expiry as page views. Lets say, you generated a URL for 10 Page views, and when someone visit the URL and access 10 pages or visited 10 times.URL will automatically gets expired.<br><br><a href=\"https://clients.acurax.com/order.php?pid=csmauapa&utm_source=link_1&utm_medium=csma_1&utm_campaign=csma\" style=\"float:right;\" target=\"_blank\">Order Now</a></p>'>
</div>";
}
add_action('acx_csma_hook_mainoptions_below_general_settings','acx_csma_addon_ua_demo',50);
function acx_csma_service_addon_demo()
{
	$acx_theme_addon_demo_array['STPA1'] = array(

			'STP1_A' => array(
				'image' => plugins_url('/images/csma_01.png',__FILE__),
				'preview' => 'https://clients.acurax.com/link.php?id=18',
				'name' => 'STP1-A'
			),
			'STP1_B' => array(
				'image' => plugins_url('/images/csma_02.png',__FILE__),
				'preview' => 'https://clients.acurax.com/link.php?id=19',
				'name' => 'STP1-B'
			),
			'STP1_C' => array(
				'image' => plugins_url('/images/csma_03.png',__FILE__),
				'preview' => 'https://clients.acurax.com/link.php?id=20',
				'name' => 'STP1-C'
			),
			'STP1_D' => array(
				'image' => plugins_url('/images/csma_04.png',__FILE__),
				'preview' => 'https://clients.acurax.com/link.php?id=21',
				'name' => 'STP1-D'
			),
	);
	$acx_theme_addon_demo_array = apply_filters('acx_csma_demo_theme_filter_hook',$acx_theme_addon_demo_array);
	if($acx_theme_addon_demo_array == '')
	{
		$acx_theme_addon_demo_array = array();
	}
	$acx_csma_lb_title = "Showcase Products and Services While Your Website Is Under Construction";
	$acx_csma_lb_content = "<p style=\"font-size:14px;\">We have prepared 4 special themes ( <a href=\"https://clients.acurax.com/link.php?id=18\" target=\"_blank\" title=\"Preview This Theme\">STP1-A</a>, <a href=\"https://clients.acurax.com/link.php?id=19\" target=\"_blank\" title=\"Preview This Theme\">STP1-B</a>, <a href=\"https://clients.acurax.com/link.php?id=20\" target=\"_blank\" title=\"Preview This Theme\">STP1-C</a> and <a href=\"https://clients.acurax.com/link.php?id=21\" target=\"_blank\" title=\"Preview This Theme\">STP1-D</a> ) as an Addon plugin labeled \"Service Theme Pack 1\" which you can install just like a normal plugin after purchase. And the additional themes will be available here. These themes are designed and developed with high customizable options.<br><br>This theme pack have 4 highly customizable themes with Contact/Lead Capture Form,Service/Product Showcase, About us Section etc... Check preview for a live preview.<br><a href=\"https://clients.acurax.com/order.php?pid=csmastp1&utm_source=preview_link&utm_medium=csma&utm_campaign=csma\" style=\"float:right;\" target=\"_blank\" class=\"button\">Click Here to Order Now</a><br></p>";
	foreach($acx_theme_addon_demo_array as $key => $value)
	{
		foreach($value as $k => $v)
		{
		echo "<div id='img_holder' class='img_holder_demo'><label for='".$k."'><img src='".$v['image']."' class='acx_csma_info_lb' lb_title='".$acx_csma_lb_title."' lb_content='".$acx_csma_lb_content."'></label><br /><input type='radio' class='acx_csma_info_lb acx_csma_info_lb_demo' name='acx_csma_template_demo' id='".$k."' lb_title='".$acx_csma_lb_title."' lb_content='".$acx_csma_lb_content."' />".$v['name']."<br /><a href='".$v['preview']."' target='_blank'>Preview</a></div>";
		}
	}
}
add_action('acx_csma_hook_mainoptions_below_add_template','acx_csma_service_addon_demo');
function acx_csm_info_lb()
{
?>
<script type="text/javascript">
jQuery( ".img_holder_demo .acx_csma_info_lb_demo" ).click(function() {
jQuery(this).attr('checked', false);
});
jQuery( ".acx_csma_info_lb" ).click(function() {
var lb_title = jQuery(this).attr('lb_title');
var lb_content = jQuery(this).attr('lb_content');
var html= '<div id="acx_csma_c_icon_p_info_lb_h" style="display:none;"><div class="acx_csma_c_icon_p_info_c"><span class="acx_csma_c_icon_p_info_close" onclick="acx_csma_remove_info()"></span><h4>'+lb_title+'</h4><div class="acx_csma_c_icon_p_info_content">'+lb_content+'</div></div></div> <!-- acx_csma_c_icon_p_info_lb_h -->';
jQuery( "body" ).append(html)
jQuery( "#acx_csma_c_icon_p_info_lb_h" ).fadeIn();
});

function acx_csma_remove_info()
{
jQuery( "#acx_csma_c_icon_p_info_lb_h" ).fadeOut()
jQuery( "#acx_csma_c_icon_p_info_lb_h" ).remove();
var lb_title = "";
var lb_content = "";
};
</script>
<?php
}
add_action('acx_csma_hook_mainoptions_below_general_settings','acx_csm_info_lb');
add_action('acx_csma_hook_mainoptions_below_javascript','acx_csm_info_lb');

function acx_csma_updated_fields_content()
{
$acx_csma_appearence_array=acx_csma_get_db_array_value(); 
	if(is_array($acx_csma_appearence_array))
	{
	if(ISSET($acx_csma_appearence_array['1']))
	{
		if(!array_key_exists('acx_csma_show_subscription',$acx_csma_appearence_array['1']))
		{
			$acx_csma_appearence_array['1']['acx_csma_show_subscription'] = 1;
		}
		if(!array_key_exists('acx_csma_custom_html_top_sub1',$acx_csma_appearence_array['1']))
		{
			$acx_csma_appearence_array['1']['acx_csma_custom_html_top_sub1'] = "";
		}
		if(!array_key_exists('acx_csma_custom_html_bottom_sub1',$acx_csma_appearence_array['1']))
		{
			$acx_csma_appearence_array['1']['acx_csma_custom_html_bottom_sub1'] = "";
		}
		if(!array_key_exists('acx_csma_custom_css_temp1',$acx_csma_appearence_array['1']))
		{
			$acx_csma_appearence_array['1']['acx_csma_custom_css_temp1'] = "";
		}
		if(!array_key_exists('acx_csma_custom_html_top_temp1_title',$acx_csma_appearence_array['1']))
		{
			$acx_csma_appearence_array['1']['acx_csma_custom_html_top_temp1_title'] = "";
		}
	}
	if(ISSET($acx_csma_appearence_array['2']))
	{
		if(!array_key_exists('acx_csma_custom_html_top_timer',$acx_csma_appearence_array['2']))
		{
			$acx_csma_appearence_array['2']['acx_csma_custom_html_top_timer'] = "";
		}
		if(!array_key_exists('acx_csma_show_subscription2',$acx_csma_appearence_array['2']))
		{
			$acx_csma_appearence_array['2']['acx_csma_show_subscription2'] = 1;
		}
		if(!array_key_exists('acx_csma_subscribe_btn_text2',$acx_csma_appearence_array['2']))
		{
			$acx_csma_appearence_array['2']['acx_csma_subscribe_btn_text2'] = "Submit";
		}
		if(!array_key_exists('acx_csma_custom_html_above_timer',$acx_csma_appearence_array['2']))
		{
			$acx_csma_appearence_array['2']['acx_csma_custom_html_above_timer'] = "";
		}
		if(!array_key_exists('acx_csma_show_timer2',$acx_csma_appearence_array['2']))
		{
			$acx_csma_appearence_array['2']['acx_csma_show_timer2'] = 1;
		}
		if(!array_key_exists('acx_csma_custom_css_temp2',$acx_csma_appearence_array['2']))
		{
			$acx_csma_appearence_array['2']['acx_csma_custom_css_temp2'] = "";
		}
	}
	if(ISSET($acx_csma_appearence_array['3']))
	{
		if(!array_key_exists('acx_csma_primary_color3',$acx_csma_appearence_array['3']))
		{
			$acx_csma_appearence_array['3']['acx_csma_primary_color3'] = "#ffffff";
		}
		if(!array_key_exists('acx_csma_secondary_color3',$acx_csma_appearence_array['3']))
		{
			$acx_csma_appearence_array['3']['acx_csma_secondary_color3'] = "#fe7e01";
		}
		if(!array_key_exists('acx_csma_left_bar_color3',$acx_csma_appearence_array['3']))
		{
			$acx_csma_appearence_array['3']['acx_csma_left_bar_color3'] = "#000000";
		}
		if(!array_key_exists('acx_csma_timer_color3',$acx_csma_appearence_array['3']))
		{
			$acx_csma_appearence_array['3']['acx_csma_timer_color3'] = "#ffffff";
		}
		if(!array_key_exists('acx_csma_show_subscription3',$acx_csma_appearence_array['3']))
		{
			$acx_csma_appearence_array['3']['acx_csma_show_subscription3'] = 1;
		}
		if(!array_key_exists('acx_csma_show_timer3',$acx_csma_appearence_array['3']))
		{
			$acx_csma_appearence_array['3']['acx_csma_show_timer3'] = 1;
		}
		if(!array_key_exists('acx_csma_custom_html_top_timer_temp3',$acx_csma_appearence_array['3']))
		{
			$acx_csma_appearence_array['3']['acx_csma_custom_html_top_timer_temp3'] = "";
		}
		if(!array_key_exists('acx_csma_custom_html_bottom_temp3',$acx_csma_appearence_array['3']))
		{
			$acx_csma_appearence_array['3']['acx_csma_custom_html_bottom_temp3'] = "";
		}
		if(!array_key_exists('acx_csma_custom_css_temp3',$acx_csma_appearence_array['3']))
		{
			$acx_csma_appearence_array['3']['acx_csma_custom_css_temp3'] = "";
		}
	}
	if(ISSET($acx_csma_appearence_array['4']))
	{
		if(!array_key_exists('acx_csma_custom_css_temp4',$acx_csma_appearence_array['4']))
		{
			$acx_csma_appearence_array['4']['acx_csma_custom_css_temp4'] = "";
		}
	}
	if(ISSET($acx_csma_appearence_array['5']))
	{
		if(!array_key_exists('acx_csma_show_subscription5',$acx_csma_appearence_array['5']))
		{
			$acx_csma_appearence_array['5']['acx_csma_show_subscription5'] = 1;
		}
		if(!array_key_exists('acx_csma_subscribe_btn_text5',$acx_csma_appearence_array['5']))
		{
			$acx_csma_appearence_array['5']['acx_csma_subscribe_btn_text5'] = "Submit";
		}
		if(!array_key_exists('acx_csma_custom_html_top_sub',$acx_csma_appearence_array['5']))
		{
			$acx_csma_appearence_array['5']['acx_csma_custom_html_top_sub'] = "";
		}
		if(!array_key_exists('acx_csma_custom_html_bottom_sub',$acx_csma_appearence_array['5']))
		{
			$acx_csma_appearence_array['5']['acx_csma_custom_html_bottom_sub'] = "";
		}
		if(!array_key_exists('acx_csma_launch_title_color5',$acx_csma_appearence_array['5']))
		{
			$acx_csma_appearence_array['5']['acx_csma_launch_title_color5'] = "#4b4b4b";
		}
		if(!array_key_exists('acx_csma_custom_css_temp5',$acx_csma_appearence_array['5']))
		{
			$acx_csma_appearence_array['5']['acx_csma_custom_css_temp5'] = "";
		}
	}
		$acx_csma_appearence_array=acx_csma_get_db_array_value(); 
	}
}
add_action('acx_csma_hook_mainoptions_inside_else_submit','acx_csma_updated_fields_content');
function acx_csma_getrealip()
{
	if ( isset( $_SERVER["HTTP_CF_CONNECTING_IP"] ) ) {
      return $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if ( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
      return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    if ( isset( $_SERVER["REMOTE_ADDR"] ) ) {
      return $_SERVER['REMOTE_ADDR'];
    }
}
// custom html before saving
function acx_csma_custom_html_before_save_hook_fn($name,$value)
{
	$value = apply_filters('acx_csma_custom_html_before_save_hook',$name,$value);
	return $value;
} 
function acx_csma_custom_html_after_save_hook_fn($value)
{
	$value = apply_filters('acx_csma_text_after_save_hook',$value);
	return $value;
}
function acx_csma_text_before_save_hook_fn($name,$value)
{
	$value = apply_filters('acx_csma_text_before_save_hook',$name,$value);
	return $value;
}
function acx_csma_text_after_save_hook_fn($value)
{
	$value = apply_filters('acx_csma_text_after_save_hook',$value);
	return $value;
}
function acx_csma_option_text_after_save_hook_fn($value)
{
	$value = apply_filters('acx_csma_option_text_after_save_hook',$value);
	$value = apply_filters('acx_csma_text_after_save_hook',$value);
	return $value;
}
function acx_csma_textarea_before_save_hook_function($name,$value)
{
	$value = apply_filters('acx_csma_text_area_before_save_hook',$name,$value);
	return $value;
}
function acx_csma_textarea_after_save_hook_function($value)
{
	$value = apply_filters('acx_csma_text_after_save_hook',$value);
	return $value;
}
function acx_customhtml_stripslashes($name,$value)
{
	if(ISSET($_POST[$name]))
	{
		if($_POST[$name] == $value)
		{
		$value = stripslashes($_POST[$name]);
		}
	}
	return $value;
}
add_filter('acx_csma_custom_html_before_save_hook','acx_customhtml_stripslashes',25,2);
function acx_custom_html_trim($name,$value)
{
	if(ISSET($_POST[$name]))
	{
		if($_POST[$name] == $value)
		{
			$value = trim($_POST[$name]);
		}
	}
	return $value;
}
add_filter('acx_csma_custom_html_before_save_hook','acx_custom_html_trim',20,2);

function acx_sanitize_text($name,$value)
{
	if(ISSET($_POST[$name]))
	{
		if($_POST[$name] == $value)
		{
			$value = sanitize_text_field($_POST[$name]);
		}
	}
	return $value;
}
add_filter('acx_csma_text_before_save_hook','acx_sanitize_text',10,2); 
function acx_stripslashes_text_after_save($value)
{
	$value = stripslashes($value);
	return $value;
}
add_filter('acx_csma_text_after_save_hook','acx_stripslashes_text_after_save',20,1);

function acx_esc_attr_text_after_save($value)
{
	$value = esc_attr($value);
	return $value;
}
add_filter('acx_csma_option_text_after_save_hook','acx_esc_attr_text_after_save',15,1);
function acx_text_area_stripslashes($name,$value)
{
	if(ISSET($_POST[$name]))
	{
		if($_POST[$name] == $value)
		{
			$value = stripslashes($_POST[$name]);
		}
	}
	return $value;
}
add_filter('acx_csma_text_area_before_save_hook','acx_text_area_stripslashes',20,2);
function acx_csma_display_var_content()
{ 
	$display_content = '';
	$acx_csma_display_var_arr=get_option('acx_csma_display_var_arr');
	if(is_serialized($acx_csma_display_var_arr))
	{
		$acx_csma_display_var_arr=unserialize($acx_csma_display_var_arr);
	}
	if($acx_csma_display_var_arr=="")
	{
		$acx_csma_display_var_arr=array();
	}
	$display_content.="<hr />";
	ksort($acx_csma_display_var_arr);
	$display_content.="<table class='wp-list-table widefat fixed striped'><th>Text</th><th>Variable</th><th>Action</th>";
	
	foreach($acx_csma_display_var_arr as $key => $values)
	{
		$singular = '';
		$plural = '';
		if(ISSET($values['singular']))
		{
			$singular=$values['singular'];
		}
		if(ISSET($values['plural']))
		{
			$plural=$values['plural'];
		}
		if($singular != '' && $plural != '' )
		{
			$value = $singular."/".$plural;
		}
		else if($singular == '')
		{
			$value = $plural;
		}
		else{
			$value = $singular;
		}
	$display_content.="<tr><td>".ucfirst($key)."</td><td>".$value."</td><td><span id='acx_disp_edit_link'><a onclick='acx_csma_disp_var_edit(\"$key\",\"$singular\",\"$plural\");' id='acx_csma_disp_var_edit'>&nbspEdit</a></span><span id='acx_disp_reset_link' style='margin-left: 23px;'><a onclick='acx_csma_disp_var_reset(\"$key\");' id='acx_csma_disp_var_edit'>&nbspReset to Default</a></span></td></tr>";
	}
	$display_content.="</table>";
	return $display_content;
}
function acx_csma_display_variables()
{
	
	echo "<div id='acx_csma_display_variable_content'>";
	$acx_csma_dis_cont=acx_csma_display_var_content();
	echo $acx_csma_dis_cont;
	echo "</div>";
	?>
<div class="acx_csma_disp_edit_litbx" style="display:none;">
		<div class="acx_csma_disp_edit_inner">
			<div class='acx_csma_disp_edit_close_btn' onclick='acx_csma_disp_edit_cls();'></div>
				<div class="acx_csma_disp_edit_ltbx1" id="acx_csma_disp_edit_ltbx">
				</div>
		</div>
</div> 
	<script>
	function acx_csma_disp_var_edit(key,singular,plural)
	{
		
		var acx_load="<div id='acx_csmap_loading_1'><div class='load_1'></div></div>";
		jQuery('body').append(acx_load);
		var acx_csma_open_lb ="<?php echo admin_url('admin-ajax.php'); ?>";
		var order = 'key='+key+'&singular='+singular+'&plural='+plural+'&action=acx_csma_open_disp_var'+'&acx_csma_open_disp_var_e=<?php echo wp_create_nonce('acx_csma_open_disp_var_e'); ?>';
		jQuery.post(acx_csma_open_lb, order, function(theResponse)
		{
			jQuery("#acx_csmap_loading_1").remove();
			if(theResponse)
			{
				jQuery('#acx_csma_disp_edit_ltbx').html(theResponse);
				jQuery('.acx_csma_disp_edit_litbx').show();
			}
		});
			
	}
	function acx_csma_disp_edit_cls()
	{
		jQuery('.acx_csma_disp_edit_litbx').hide();
	}
	function acx_csma_edit_disp_var()
	{
		var key=jQuery('#acx_csma_edit_key').val();
		if(key=="")
		{
			alert('Something Wrong\nTry Again!!!');
			return false;
		}
		var singular=jQuery('#acx_csma_edit_singular').val();
		var plural=jQuery('#acx_csma_edit_plural').val();
		var acx_load="<div id='acx_csmap_loading_1'><div class='load_1'></div></div>";
		jQuery('#acx_csma_edit_box').append(acx_load);
		var acx_csma_edit_ajaxurl ="<?php echo admin_url('admin-ajax.php'); ?>";
		var order = 'key='+key+'&singular='+singular+'&plural='+plural+'&action=acx_csma_edit_disp_var'+'&acx_csma_edit_var=<?php echo wp_create_nonce('acx_csma_edit_var'); ?>';
		jQuery.post(acx_csma_edit_ajaxurl, order, function(theResponse)
		{
			jQuery("#acx_csmap_loading_1").remove();
			if(theResponse)
			{
				jQuery('#acx_csma_display_variable_content').html(theResponse);
				jQuery('#acx_csma_edit_key').val('');
				jQuery('#acx_csma_edit_singular').val('');
				jQuery('#acx_csma_edit_plural').val('');
				jQuery('.acx_csma_disp_edit_litbx').hide();
			}
			else{
				alert('Something Went Wrong\nPlease Try Again!!!');
			}
		});
	}
	function acx_csma_disp_var_reset(key)
	{
		var acx_load1="<div id='acx_csmap_loading_1'><div class='load_1'></div></div>";
		jQuery('body').append(acx_load1);
		var acx_csma_edit_ajaxurl1 ="<?php echo admin_url('admin-ajax.php'); ?>";
		var order = 'key='+key+'&action=acx_csma_reset_disp_var'+'&acx_csma_reset_var=<?php echo wp_create_nonce('acx_csma_reset_var'); ?>';
		jQuery.post(acx_csma_edit_ajaxurl1, order, function(theResponse)
		{
			jQuery("#acx_csmap_loading_1").remove();
			if(theResponse)
			{
				jQuery('#acx_csma_display_variable_content').html(theResponse);
			}
			else
			{
				alert('Something Went Wrong\nPlease Try Again!!!');
			}
		});	
	}
	</script>
	<?php
}

function acx_csma_reset_disp_var_callback()
{
	if (!isset($_POST['acx_csma_reset_var'])) die("<br><br>Unknown Error Occurred, Try Again... <a href = ''>Click Here</a>");
	if (!wp_verify_nonce($_POST['acx_csma_reset_var'],'acx_csma_reset_var')) die("<br><br>Unknown Error Occurred, Try Again... <a href = ''>Click Here</a>");
	if(!current_user_can('manage_options')) die("<br><br>Sorry, You have no permission to do this action...</a>");
	if (isset($_POST['key']))
	{
		$acx_csma_reset_key = $_POST['key'];
	}
	else
	{
		$acx_csma_reset_key = '';
	}
	$acx_csma_display_var_arr=get_option('acx_csma_display_var_arr');
	if(is_serialized($acx_csma_display_var_arr))
	{
		$acx_csma_display_var_arr=unserialize($acx_csma_display_var_arr);
	}
	if(ISSET($acx_csma_display_var_arr[$acx_csma_reset_key]['default_singular']))
	{
		$acx_csma_display_var_arr[$acx_csma_reset_key]['singular']=$acx_csma_display_var_arr[$acx_csma_reset_key]['default_singular'];
	}
	if(ISSET($acx_csma_display_var_arr[$acx_csma_reset_key]['default_plural']))
	{
	$acx_csma_display_var_arr[$acx_csma_reset_key]['plural']=$acx_csma_display_var_arr[$acx_csma_reset_key]['default_plural'];
	}
	if(!is_serialized($acx_csma_display_var_arr))
	{ 
		$acx_csma_display_var_arr = serialize($acx_csma_display_var_arr); 
	}
	
	update_option('acx_csma_display_var_arr',$acx_csma_display_var_arr);
	$acx_csma_dis_cont=acx_csma_display_var_content();
	echo $acx_csma_dis_cont;
	
	die();
}
add_action( 'wp_ajax_acx_csma_reset_disp_var', 'acx_csma_reset_disp_var_callback' );


function acx_csma_edit_disp_var_callback()
{
	if (!isset($_POST['acx_csma_edit_var'])) die("<br><br>Unknown Error Occurred, Try Again... <a href = ''>Click Here</a>");
	if (!wp_verify_nonce($_POST['acx_csma_edit_var'],'acx_csma_edit_var')) die("<br><br>Unknown Error Occurred, Try Again... <a href = ''>Click Here</a>");
	if(!current_user_can('manage_options')) die("<br><br>Sorry, You have no permission to do this action...</a>");
	if (isset($_POST['key']))
	{
		$acx_csma_edit_key = $_POST['key'];
	}
	else
	{
		$acx_csma_edit_key = '';
	}
	if (isset($_POST['singular']))
	{
		$acx_csma_edit_singular = $_POST['singular'];
	}
	else
	{
		$acx_csma_edit_singular = '';
	}
	if (isset($_POST['plural']))
	{
		$acx_csma_edit_plural = $_POST['plural'];
	}
	else
	{
		$acx_csma_edit_plural = '';
	}
	$acx_csma_display_var_arr=get_option('acx_csma_display_var_arr');
	if(is_serialized($acx_csma_display_var_arr))
	{
		$acx_csma_display_var_arr=unserialize($acx_csma_display_var_arr);
	}
	if(ISSET($acx_csma_display_var_arr[$acx_csma_edit_key]['default_singular']))
	{
		$acx_csma_display_var_arr[$acx_csma_edit_key]['singular']=$acx_csma_edit_singular;
	}
	if(ISSET($acx_csma_display_var_arr[$acx_csma_edit_key]['default_plural']))
	{
		$acx_csma_display_var_arr[$acx_csma_edit_key]['plural']=$acx_csma_edit_plural;
	}
	if(!is_serialized($acx_csma_display_var_arr))
	{ 
		$acx_csma_display_var_arr = serialize($acx_csma_display_var_arr); 
	}
	
	update_option('acx_csma_display_var_arr',$acx_csma_display_var_arr);
	
	$acx_csma_dis_cont=acx_csma_display_var_content();
	echo $acx_csma_dis_cont;
	die();
}
add_action( 'wp_ajax_acx_csma_edit_disp_var', 'acx_csma_edit_disp_var_callback' );
function acx_csma_open_disp_var_callback()
{
	if (!isset($_POST['acx_csma_open_disp_var_e'])) die("<br><br>Unknown Error Occurred, Try Again... <a href = ''>Click Here</a>");
	if (!wp_verify_nonce($_POST['acx_csma_open_disp_var_e'],'acx_csma_open_disp_var_e')) die("<br><br>Unknown Error Occurred, Try Again... <a href = ''>Click Here</a>");
	if(!current_user_can('manage_options')) die("<br><br>Sorry, You have no permission to do this action...</a>");
	$response ='';
	if (isset($_POST['key']))
	{
		$acx_csma_key = $_POST['key'];
	}
	else
	{
		$acx_csma_key = '';
	}
	if (isset($_POST['singular']))
	{
		$acx_csma_singular = $_POST['singular'];
	}
	else
	{
		$acx_csma_singular = '';
	}
	if (isset($_POST['plural']))
	{
		$acx_csma_plural = $_POST['plural'];
	}
	else
	{
		$acx_csma_plural = '';
	}
	$acx_csma_display_var_arr=get_option('acx_csma_display_var_arr');
	if(is_serialized($acx_csma_display_var_arr))
	{
		$acx_csma_display_var_arr=unserialize($acx_csma_display_var_arr);
	}
	$heading = ucfirst($acx_csma_key);
	$response .= "<div id='acx_csma_edit_box'><div id='acx_csma_edit_box_inner'><span id='acx_csma_heading'><h3>Edit Text for ".$heading."</h3></span><hr><div id='acx_csma_disp_var_inside_cnt'>";
	if(ISSET($acx_csma_display_var_arr[$acx_csma_key]['default_singular']))
	{
		$response .= "<div class='acx_csma_input_cvr'><span class='acx_csma_disp_label'>Text for Singular:</span><span class='acx_csma_disp_input'><input type='text' name='acx_csma_edit_singular' id='acx_csma_edit_singular' value='".$acx_csma_singular."'></span></div>";
	}
	if(ISSET($acx_csma_display_var_arr[$acx_csma_key]['default_plural']))
	{
	$response .= "<div class='acx_csma_input_cvr'><span class='acx_csma_disp_label'>Text for Plural: </span><span class='acx_csma_disp_input'><input type='text' name='acx_csma_edit_plural' id='acx_csma_edit_plural' value='".$acx_csma_plural."'></span></div>";
	}
	$response .= "<span class='acx_csma_disp_input'><input type='hidden' name='acx_csma_edit_key' id='acx_csma_edit_key' value='".$acx_csma_key."'></span><span class='acx_csma_disp_btn'><button type='button' class='button' onclick='acx_csma_edit_disp_var();'>Save</button></span></div></div></div>";
	echo $response;
	
	
	die();
}
add_action( 'wp_ajax_acx_csma_open_disp_var', 'acx_csma_open_disp_var_callback' );
if(!function_exists('acx_csma_disp_var_to_show'))
{
	function acx_csma_disp_var_to_show($acx_csma_disp_key)
	{
		
		$display_response=array();
		$acx_csma_edit_plural="";
		$acx_csma_edit_singular="";
		$acx_csma_display_var_arr=get_option('acx_csma_display_var_arr');
		if(is_serialized($acx_csma_display_var_arr))
		{
			$acx_csma_display_var_arr=unserialize($acx_csma_display_var_arr);
		}
		if(isset($acx_csma_display_var_arr[$acx_csma_disp_key]['singular']))
		{
			$acx_csma_edit_singular=$acx_csma_display_var_arr[$acx_csma_disp_key]['singular'];
		}
		if(isset($acx_csma_display_var_arr[$acx_csma_disp_key]['plural']))
		{
			$acx_csma_edit_plural=$acx_csma_display_var_arr[$acx_csma_disp_key]['plural'];
		}
		$display_response['singular']=$acx_csma_edit_singular;
		$display_response['plural']=$acx_csma_edit_plural;
		return $display_response;
	}
}
function acx_csma_get_db_array_value()
{
	$acx_csma_appearence_array=get_option('acx_csma_appearence_array');
	$acx_csma_appearence_array = apply_filters('acx_csma_demo_get_array_filter',$acx_csma_appearence_array);
	if($acx_csma_appearence_array != "")
	{
			if(is_serialized($acx_csma_appearence_array))
			{ 
				$acx_csma_appearence_array = unserialize($acx_csma_appearence_array); 
			}
	}
	return $acx_csma_appearence_array;
}
function acx_csma_demo_get_array_filter_low_priority($acx_csma_appearence_array)
{
 return $acx_csma_appearence_array;
}
add_filter('acx_csma_demo_get_array_filter','acx_csma_demo_get_array_filter_low_priority',5);
function acx_csma_update_array_value($acx_csma_appearence_array)
{
	$acx_csma_appearence_array = apply_filters('acx_csma_demo_update_array_filter',$acx_csma_appearence_array);
	if(!is_serialized($acx_csma_appearence_array))
	{
		$acx_csma_appearence_array = serialize($acx_csma_appearence_array); 
	}
	update_option('acx_csma_appearence_array',$acx_csma_appearence_array);
}


// --- Making Sure acx_csma_appearence_array has all indexes starts here -----
function acx_csma_appearence_array_refresh()
{
	global $acx_csma_appearence_array_default;
	$acx_csma_appearence_array = acx_csma_get_db_array_value();
	$changes_happened = false;
	if($acx_csma_appearence_array == "")
	{
		$acx_csma_appearence_array = array();
	}
	$acx_csma_appearence_array_cloned = $acx_csma_appearence_array;
	foreach($acx_csma_appearence_array_default as $key => $value)
	{
		if(!array_key_exists($key,$acx_csma_appearence_array_cloned)) // If Template Not Available then add
		{
			$acx_csma_appearence_array_cloned[$key] = $acx_csma_appearence_array_default[$key];
			$changes_happened = true;
		} else // If Template is Available Then Checking Keys
		{
			if(is_array($value))
			{
				foreach($value as $key2 => $value2)
				{
					if(!array_key_exists($key2,$acx_csma_appearence_array_cloned[$key])) // If Template Not Available then add
					{
						$acx_csma_appearence_array_cloned[$key][$key2] = $acx_csma_appearence_array_default[$key][$key2];
						$changes_happened = true;
					}
				}
			}
		}
	}
	if($changes_happened == true)
	{
		acx_csma_update_array_value($acx_csma_appearence_array_cloned);
	}
}
add_action( 'init', 'acx_csma_appearence_array_refresh');
// --- Making Sure acx_csma_appearence_array has all indexes ends here -----
?>