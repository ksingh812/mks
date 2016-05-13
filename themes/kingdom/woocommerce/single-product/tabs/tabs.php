<?php
/**
 * Single Product tabs
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Filter tabs and allow third parties to add their own
 *
 * Each tab is an array containing title, callback and priority.
 * @see woocommerce_default_product_tabs()
 */
$tabs = apply_filters( 'woocommerce_product_tabs', array() );
global $kingdom;
if ( ! empty( $tabs ) ) : ?>
<div class="row">
	<div class="kd_tabs col-lg-12 col-sm-12">
		<ul class="nav nav-tabs">
			<?php 
			$cc = 0;
		
			if( isset($kingdom->coreFunctions->settings['layout']["enable_product_description_tab"]) && $kingdom->coreFunctions->settings['layout']["enable_product_description_tab"] == 'false' ){
				unset( $tabs['description'] ); 
			}
			
			//unset( $tabs['description'] );
			
			foreach ( $tabs as $key => $tab ) :
		   
			// skip comments template
			if( $tab['callback'] == 'comments_template' ) continue; 
			?>

				<li class="<?php echo $key ?>_tab <?php echo $cc == 0 ? 'active': '';?>">
					<a href="#tab-<?php echo $key ?>"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ?></a>
				</li>

			<?php
				$cc++; 
			endforeach; 
			?>
		</ul>
		
		<!-- Tab panes -->
		<div class="tab-content">
			<?php 
			$cc = 0;
			foreach ( $tabs as $key => $tab ) : 
			// skip comments template
			if( $tab['callback'] == 'comments_template' ) continue;
			?>
				<div class="entry-content tab-pane <?php echo $cc == 0 ? 'active': '';?>" id="tab-<?php echo $key ?>">
					<div class="kd_tabs_entry">
					<?php call_user_func( $tab['callback'], $key, $tab ) ?>
					</div>
				</div>
			<?php
				$cc++; 
			endforeach; 
			?>
		</div>
	</div>
</div>
<?php endif; ?>