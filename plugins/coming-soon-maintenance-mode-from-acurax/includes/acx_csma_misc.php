<?php 
if(ISSET($_POST['acx_csma_misc_hidden']))
{
	$acx_csma_misc_hidden = $_POST['acx_csma_misc_hidden'];
} 
else
{
	$acx_csma_misc_hidden = "";
}
if($acx_csma_misc_hidden == 'Y') 
{	//Form data sent
$acx_csma_service_banners = $_POST['acx_csma_service_banners'];
update_option('acx_csma_service_banners', $acx_csma_service_banners);
$acx_csma_hide_expert_support_menu = $_POST['acx_csma_hide_expert_support_menu'];
update_option('acx_csma_hide_expert_support_menu', $acx_csma_hide_expert_support_menu);
?>
<div class="updated"><p><strong><?php _e('Misc Settings Saved!.' ); ?></strong></p></div>
<?php
}
else
{	//Normal page display

$acx_csma_service_banners = get_option('acx_csma_service_banners');
$acx_csma_hide_expert_support_menu = get_option('acx_csma_hide_expert_support_menu');

// Setting Defaults
if ($acx_csma_service_banners == "") {	$acx_csma_service_banners = "yes"; }
if ($acx_csma_hide_expert_support_menu == "") {	$acx_csma_hide_expert_support_menu = "no"; }
} //Main else
?>
<div class="wrap">
<div style='background: white none repeat scroll 0% 0%; height: 100%; margin-top: 5px; border-radius: 15px; min-height: 450px; box-sizing: border-box; margin-left: auto; margin-right: auto; width: 98%; padding: 1%;display: table;'>

<?php echo "<h2 class='acx_csma_page_h2'>" . __( 'Misc Settings', 'acx_csma_config' ) . "</h2>"; ?>

<form name="acurax_csma_misc_form" id="acurax_csma_misc_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<hr/>

<table class="form-table">
<tbody>
<tr>
<th scope="row">
<label for="blogname"><?php _e("Acurax Service Banners: " ); ?></label>
</th>
<td>
<input type="hidden" name="acx_csma_misc_hidden" value="Y">
<select name="acx_csma_service_banners">
<option value="yes" <?php if ($acx_csma_service_banners == "yes") { echo 'selected="selected"'; } ?>>Yes, Show Them </option>
<option value="no" <?php if ($acx_csma_service_banners == "no") { echo 'selected="selected"'; } ?>>No, Hide Them </option>
</select> <br/>
<?php _e("Show Acurax Service Banners On Plugin Settings Page?" ); ?>
</td>
</tr>
<tr>
<th scope="row">
<label for="blogname"><?php _e("Hide Expert Support Menu?: " ); ?></label>
</th>
<td>
<select name="acx_csma_hide_expert_support_menu">
<option value="yes" <?php if ($acx_csma_hide_expert_support_menu == "yes") { echo 'selected="selected"'; } ?>>Yes </option>
<option value="no" <?php if ($acx_csma_hide_expert_support_menu == "no") { echo 'selected="selected"'; } ?>>No </option>
</select> <br/>
<?php _e("Would you like to hide the expert support sub menu?" ); ?>
</td>
</tr>
</tbody>
</table>
<p class="submit">
<input type="submit" name="Submit" class="button button-primary" value="<?php _e('Save Settings', 'acx_csma_config' ) ?>" />
</p>
</form>
<div id="acx_csma_sidebar">
<?php acx_csma_hook_function('acx_csma_hook_sidebar_widget'); ?>
</div> <!-- acx_csma_sidebar -->
</div>
</div>