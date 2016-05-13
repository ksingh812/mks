<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.14
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce, $product, $kingdom;
?>

<div class="kd_gallery_container">
    <div class="kd_image_large">
    	<?php if( $product->is_on_sale() ){?>
        	<i class="icon icon_salelabel"></i>
        <?php }?>
        <div id="kd_image_large_gallery">
	        <?php
			if ( has_post_thumbnail() ) {
		
				$image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
				$image_link  = wp_get_attachment_url( get_post_thumbnail_id() );
				$image       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
					'title' => $image_title
					) );
				
				$attachment_ids = $product->get_gallery_attachment_ids();
				$attachment_count = count( $attachements_id );
				
				$gallery = '-prod-details';
				
				if ( $attachment_ids ) { 
                    $loop = 0;
                    $columns = apply_filters( 'woocommerce_product_thumbnails_columns', 3 );						
                    
                    foreach ( $attachment_ids as $attachment_id ) {

                        $classes = array( 'zoom' );
            
                        if ( $loop == 0 || $loop % $columns == 0 )
                            $classes[] = 'first';
            
                        if ( ( $loop + 1 ) % $columns == 0 )
                            $classes[] = 'last';
            
                        $image_link = wp_get_attachment_url( $attachment_id );
            
                        if ( ! $image_link )
                            continue;
            
                        $image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
                        $image_class = esc_attr( implode( ' ', $classes ) );
                        $image_title = esc_attr( get_the_title( $attachment_id ) );

						echo sprintf( '<div class="item"><a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s" data-rel="prettyPhoto' . $gallery . '">%s</a></div>', wp_get_attachment_url( $attachment_id ), $image_title, wp_get_attachment_image( $attachment_id, 'shop_single' ) );
						
                        $loop++;
                    }
                }
			} else {
				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="Placeholder" />', wc_placeholder_img_src() ), $post->ID );
			}
			?>
    	</div>
    </div>
    
    <?php 
    $thumbs = $kingdom->coreFunctions->get_product_more_images( array(150, 150), 'class_image', 1);
	if( count($thumbs) > 1 ){
    ?>
    <div id="kd_product_gallery">
    	<?php
		foreach ( $thumbs as $thumb ) { 
		?>
			<div class="item">
	            <a href="<?php echo $thumb['full_img'];?>">
	                <?php echo $thumb['thumb'];?>
	            </a>
	        </div>
		<?php
		}
		?>
    </div>
    <?php
	}
	?>
</div>





