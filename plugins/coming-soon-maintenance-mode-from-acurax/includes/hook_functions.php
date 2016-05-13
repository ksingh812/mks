<?php
function acx_csma_service_banners()
{
?>
<div id="acx_ad_banners_csma">
<?php
$acx_csma_service_banners = get_option('acx_csma_service_banners');
if ($acx_csma_service_banners != "no") { ?>
<div id="acx_ad_banners_csma">
<a href="http://wordpress.acurax.com/partner-with-us/?utm_source=csma&utm_campaign=agency_banner" target="_blank" class="acx_ad_csma_1">
<div class="acx_ad_csma_title">Are You an Agency?</div> <!-- acx_ad_csma_title -->
<div class="acx_ad_csma_desc_partner"  style="padding-top: 0px; height: 32px; font-size: 13px; text-align: center;">Outsourcing Solutions From Acurax Can Add Value to Your Business</div> <!-- acx_ad_csma_desc -->
</a> <!--  acx_ad_csma_1 -->


<a href="http://wordpress.acurax.com/?utm_source=csma&utm_campaign=sidebar_banner_1" target="_blank" class="acx_ad_csma_1">
<div class="acx_ad_csma_title">Need Help on Wordpress?</div> <!-- acx_ad_csma_title -->
<div class="acx_ad_csma_desc">Instant Solutions for your wordpress Issues</div> <!-- acx_ad_csma_desc -->
</a> <!--  acx_ad_csma_1 -->

<a href="http://wordpress.acurax.com/?utm_source=csma&utm_campaign=sidebar_banner_2" target="_blank" class="acx_ad_csma_1">
<div class="acx_ad_csma_title">Unique Design For Better Branding</div> <!-- acx_ad_csma_title -->
<div class="acx_ad_csma_desc acx_ad_csma_desc2" style="padding-top: 0px; padding-left: 50px; height: 41px; font-size: 13px; text-align: center;">Get Responsive Custom Designed Website For High Conversion</div> <!-- acx_ad_csma_desc -->
</a> <!--  acx_ad_csma_1 -->

<a href="http://wordpress.acurax.com/?utm_source=csma&utm_campaign=sidebar_banner_3" target="_blank" class="acx_ad_csma_1">
<div class="acx_ad_csma_title">Affordable Website Packages</div> <!-- acx_ad_csma_title -->
<div class="acx_ad_csma_desc acx_ad_csma_desc3" style="padding-top: 0px; height: 32px; font-size: 13px; text-align: center;">Get Feature Rich Packages For a Custom Designed Website</div> <!-- acx_ad_csma_desc -->
</a> <!--  acx_ad_csma_1 -->

</div> <!--  acx_ad_banners_csma -->
<?php } else { ?>


<div class="acx_csma_sidebar_widget">
<div class="acx_csma_sidebar_w_title">Partner With Us</div> <!-- acx_ad_csma_title -->
<div class="acx_csma_sidebar_w_content">
Acurax offers a strong partnership program for agencies which has a strong sales channel. Our team of creative designers and developers will be surely an added value to your services. We can completely take care of the projects or can work with your existing team. <a href="http://wordpress.acurax.com/partner-with-us/?utm_source=csma&utm_campaign=agency_text" target="_blank">Get in touch</a>
</div>
</div> <!-- acx_csma_sidebar_widget -->


<div class="acx_csma_sidebar_widget">
<div class="acx_csma_sidebar_w_title">We Are Always Available</div> <!-- acx_ad_csma_title -->
<div class="acx_csma_sidebar_w_content">
We know you are in the process of improving your website, and we the team at Acurax is always available for any help or support that you need. <a href="http://wordpress.acurax.com/?utm_source=csma&utm_campaign=sidebar_text_1" target="_blank">Get in touch</a>
</div>
</div> <!-- acx_csma_sidebar_widget -->


<div class="acx_csma_sidebar_widget">
<div class="acx_csma_sidebar_w_title">Do You Know?</div> <!-- acx_ad_csma_title -->
<div class="acx_csma_sidebar_w_content acx_csma_sidebar_w_content_p_slide">
</div>
</div> <!-- acx_csma_sidebar_widget -->
<script type="text/javascript">
var acx_csma = new Array("A professionally designed website is the most cost effective marketing tool available in the world today...","Personalizing your website can create a unique one to one experience and convert your visitors into customers.","70% of searches from mobile devices are followed up with an action within 1 hour.");
// jQuery(".acx_csma_sidebar_w_content_p_slide p").height('30px');
function acx_csma_t_rotate()
{
acx_csma_text = acx_csma[Math.floor(Math.random()*acx_csma.length)];
jQuery(".acx_csma_sidebar_w_content_p_slide").fadeOut('slow')
jQuery(".acx_csma_sidebar_w_content_p_slide").text(acx_csma_text);
jQuery(".acx_csma_sidebar_w_content_p_slide").fadeIn('fast');
}
jQuery(document).ready(function() {
acx_csma_t_rotate();
 setInterval(function(){ acx_csma_t_rotate(); }, 8000);
});
</script>
<div class="acx_csma_sidebar_widget">
<div class="acx_csma_sidebar_w_title">Grab The Blending Creativity</div>
<div class="acx_csma_sidebar_w_content">Make your website user friendly and optimized for mobile devices for better user interaction and satisfaction <a href="http://wordpress.acurax.com/?utm_source=csma&utm_campaign=sidebar_text_2" target="_blank">Click Here</a></div>
</div> <!-- acx_csma_sidebar_widget -->
<?php } ?>
<div class="acx_csma_sidebar_widget">
<div class="acx_csma_sidebar_w_title">Rate us on wordpress.org</div>
<div class="acx_csma_sidebar_w_content" style="text-align:center;font-size:13px;"><b>Thank you for being with us... If you like our plugin then please show us some love </b></br>
<a href="https://wordpress.org/support/view/plugin-reviews/coming-soon-maintenance-mode-from-acurax" target="_blank" style="text-decoration:none;">
<span id="acx_csma_stars">
<span class="dashicons dashicons-star-filled"></span>
<span class="dashicons dashicons-star-filled"></span>
<span class="dashicons dashicons-star-filled"></span>
<span class="dashicons dashicons-star-filled"></span>
<span class="dashicons dashicons-star-filled"></span>
</span>
<span class="acx_csma_star_button button button-primary">Click Here</span>
</a>
<p>If you are facing any issues, kindly post them at plugins support forum <a href="http://wordpress.org/support/plugin/coming-soon-maintenance-mode-from-acurax" target="_blank">here</a>
</div>
</div> <!-- acx_csma_sidebar_widget -->



</div> <!--  acx_ad_banners_csma -->

<?php
} add_action('acx_csma_hook_sidebar_widget','acx_csma_service_banners',100);
?>