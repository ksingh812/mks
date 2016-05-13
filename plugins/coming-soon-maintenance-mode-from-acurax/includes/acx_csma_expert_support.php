<?php
if(ISSET($_SERVER['HTTP_HOST']))
{
$acx_installation_url = $_SERVER['HTTP_HOST'];
} else
{
$acx_installation_url = "";
}
?>
<div class="acx_csma_es_common_raw acx_csma_es_common_bg">
	<div class="acx_csma_es_middle_section">
    
    <div class="acx_csma_es_acx_content_area">
    	<div class="acx_csma_es_wp_left_area">
        <div class="acx_csma_es_wp_left_content_inner">
        	<div class="acx_csma_es_wp_main_head">Do you Need Technical Support Services to Get the Best Out of Your Wordpress Site ?</div> <!-- wp_main_head -->
            <div class="acx_csma_es_wp_sub_para_des">Acurax offer a number of WordPress related services: Form installing WordPress on your domain to offering support for existing WordPress sites.</div> <!-- acx_csma_es_wp_sub_para_des -->
            <div class="acx_csma_es_wp_acx_service_list">
            	<ul>
                <li>Troubleshoot WordPress Site Issues</li>
                    <li>Recommend & Install Plugins For Improved WordPress Performance</li>
                    <li>Create, Modify, Or Customise, Themes</li>
                    <li>Explain Errors And Recommend Solutions</li>
                    <li>Custom Plugin Development According To Your Needs</li>
                    <li>Plugin Integration Support</li>
                    <li>Many <a href="http://wordpress.acurax.com/?utm_source=csma&utm_campaign=expert_support" target="_blank">More...</a></li>
               </ul>
            </div> <!-- acx_csma_es_wp_acx_service_list -->
            
   <div class="acx_csma_es_wp_send_ylw_para">We Have Extensive Experience in WordPress Troubleshooting,Theme Design & Plugin Development.</div> <!-- acx_csma_es_wp_secnd_ylw_para-->
   
        </div> <!-- acx_csma_es_wp_left_content_inner -->
        </div> <!-- acx_csma_es_wp_left_area -->
        
        <div class="acx_csma_es_wp_right_area">
        	<div class="acx_csma_es_wp_right_inner_form_wrap">
            	<div class="acx_csma_es_wp_inner_wp_form">
                <div class="acx_csma_es_wp_form_head">WE ARE DEDICATED TO HELP YOU. SUBMIT YOUR REQUEST NOW..!</div> <!-- acx_csma_es_wp_form_head -->
                <form class="acx_csma_es_wp_support_acx">
                <span class="acx_csma_es_cnvas_input acx_csma_es_half_width_sec acx_csma_es_haif_marg_right"><input type="text" placeholder="Name" id="acx_name"></span> <!-- acx_csma_es_cnvas_input -->
                <span class="acx_csma_es_cnvas_input acx_csma_es_half_width_sec acx_csma_es_haif_marg_left"><input type="email" placeholder="Email" id="acx_email"></span> <!-- acx_csma_es_cnvas_input -->
                <span class="acx_csma_es_cnvas_input acx_csma_es_half_width_sec acx_csma_es_haif_marg_right"><input type="text" placeholder="Phone Number" id="acx_phone"></span> <!-- acx_csma_es_cnvas_input -->
                <span class="acx_csma_es_cnvas_input acx_csma_es_half_width_sec acx_csma_es_haif_marg_left"><input type="text" placeholder="Website URL" value="<?php echo $acx_installation_url; ?>" id="acx_weburl"></span> <!-- acx_csma_es_cnvas_input -->
                <span class="acx_csma_es_cnvas_input"><input type="text" placeholder="Subject" id="acx_subject"></span> <!-- acx_csma_es_cnvas_input -->
                <span class="acx_csma_es_cnvas_input"><textarea placeholder="Question" id="acx_question"></textarea></span> <!-- acx_csma_es_cnvas_input -->
                <span class="acx_csma_es_cnvas_input"><input class="acx_csma_es_wp_acx_submit" type="button" value="SUBMIT REQUEST" onclick="acx_csma_quick_request_submit();"></span> <!-- acx_csma_es_cnvas_input -->
                </form>
                </div> <!-- acx_csma_es_wp_inner_wp_form -->
            </div> <!-- acx_csma_es_wp_right_inner_form_wrap -->
        </div> <!-- acx_csma_es_wp_left_area -->
    </div> <!-- acx_csma_es_acx_content_area -->
    
    <div class="acx_csma_es_footer_content_cvr">
    <div class="acx_csma_es_wp_footer_area_desc">Its our pleasure to thank you for using our plugin and being with us. We always do our best to help you on your needs. If you like to hide this menu, you can do so at <a href="admin.php?page=Acurax-Coming-Soon-Maintenance-Mode-Misc">Misc</a> page which is under our plugin options.</div> <!--acx_csma_es_wp_footer_area_desc -->
    </div> <!-- acx_csma_es_footer_content_cvr -->
    
    </div> <!-- acx_csma_es_middle_section -->
</div> <!--acx_csma_es_common_raw -->
<script type="text/javascript">
var request_acx_form_status = 0;
function acx_quick_form_reset()
{
jQuery("#acx_subject").val('');
jQuery("#acx_question").val('');
}
acx_quick_form_reset();
function acx_csma_quick_request_submit()
{
var acx_name = jQuery("#acx_name").val();
var acx_email = jQuery("#acx_email").val();
var acx_phone = jQuery("#acx_phone").val();
var acx_weburl = jQuery("#acx_weburl").val();
var acx_subject = jQuery("#acx_subject").val();
var acx_question = jQuery("#acx_question").val();
var order = '&action=acx_csma_quick_request_submit&acx_name='+acx_name+'&acx_email='+acx_email+'&acx_phone='+acx_phone+'&acx_weburl='+acx_weburl+'&acx_subject='+acx_subject+'&acx_question='+acx_question+'&acx_csma_es=<?php echo wp_create_nonce("acx_csma_es"); ?>';  
if(request_acx_form_status == 0)
{
request_acx_form_status = 1;
jQuery.post(ajaxurl, order, function(quick_request_acx_response)
{
if(quick_request_acx_response == 1)
{
alert('Your Request Submitted Successfully!');
acx_quick_form_reset();
request_acx_form_status = 0;
} else if(quick_request_acx_response == 2)
{
alert('Please Fill Mandatory Fields.');
request_acx_form_status = 0;
} else
{
alert('There was an error processing the request, Please try again.');
acx_quick_form_reset();
request_acx_form_status = 0;
}
});
} else
{
alert('A request is already in progress.');
}
}
</script>
<?php ?>