<?php
/**
 * Init Amazon
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		0.1
 */

// load metabox
if(	is_admin() ) {
	require_once( 'ajax-request.php' );

	/* Use the admin_menu action to define the custom box */
    add_action('admin_menu', 'wwcAmzAff_api_search_metabox');

    /* Adds a custom section to the "side" of the product edit screen */
    function wwcAmzAff_api_search_metabox() {
		//add_meta_box('wwcAmzAff_api_search', 'Search product(s) on Amazon', 'wwcAmzAff_api_search_custom_box', 'product', 'normal', 'high');
    }

	/* The code for api search custom metabox */
	function wwcAmzAff_api_search_custom_box() {
		global $wwcAmzAff;

		$amazon_settings = $wwcAmzAff->getAllSettings('array', 'amazon');
		$plugin_uri = $wwcAmzAff->cfg['paths']['plugin_dir_url'] . 'modules/amazon/';
	?>
		<link rel='stylesheet' id='wwcAmzAff-metabox-css' href='<?php echo $plugin_uri . 'meta-box.css';?>' type='text/css' media='all' />

		<script type='text/javascript' src='<?php echo $plugin_uri . 'meta-box.js';?>'></script>

		</form> <!-- closing the top form -->
			<form id="wwcAmzAff-search-form" action="/" method="POST">
			<div style="bottom: 0px; top: 0px;" class="wwcAmzAff-shadow"></div>
			<div id="wwcAmzAff-search-bar">
				<div class="wwcAmzAff-search-content">
					<div class="wwcAmzAff-search-block">
						<label for="wwcAmzAff-search">Search by Keywords or ASIN:</label>
						<input type="text" name="wwcAmzAff-search" id="wwcAmzAff-search" value="" />
					</div>

					<div class="wwcAmzAff-search-block" style="width: 220px">
						<span class="caption">Category:</span>
						<select name="wwcAmzAff-category" id="wwcAmzAff-category">
						<?php
							foreach ($wwcAmzAff->amazonCategs() as $key => $value){
								echo '<option value="' . ( $value ) . '">' . ( $value ) . '</option>';
							}
						?>
						</select>
					</div>

					<div class="wwcAmzAff-search-block" style="width: 320px">
						<span>Import to category:</span>
						<?php
						$args = array(
							'orderby' 	=> 'menu_order',
							'order' 	=> 'ASC',
							'hide_empty' => 0
						);
						$categories = get_terms('product_cat', $args);
						echo '<select name="wwcAmzAff-to-category" id="wwcAmzAff-to-category" style="width: 200px;">';
						echo '<option value="amz">Use category from Amazon</option>';
						if(count($categories) > 0){
							foreach ($categories as $key => $value){
								echo '<option value="' . ( $value->name ) . '">' . ( $value->name ) . '</option>';
							}
						}
						echo '</select>';
						?>
					</div>

					<input type="submit" class="button-primary" id="wwcAmzAff-search-link" value="Search" />
				</form>
				<div id="wwcAmzAff-ajax-loader"><img src="<?php echo $plugin_uri;?>assets/ajax-loader.gif" /> searching on <strong>Amazon.<?php echo $amazon_settings['country'];?></strong> </div>
			</div>
		</div>
		<div id="wwcAmzAff-results">
			<div id="wwcAmzAff-ajax-results"><!-- dynamic content here --></div>
			<div style="clear:both;"></div>
		</div>

		<?php
		if($_REQUEST['action'] == 'edit'){
			echo '<style>#amzStore_shop_products_price, #amzStore_shop_products_markers { display: block; }</style>';
		}
		?>
	<?php
	}
}
require_once( 'product-tabs.php' );