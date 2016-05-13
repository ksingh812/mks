<?php
add_action('wp_ajax_kingdom_bulk_products_request', 'kingdom_bulk_products_request_callback');
function kingdom_bulk_products_request_callback() {
	global $kingdom;
	$requestData = array(
		'category' => isset($_REQUEST['category']) ? htmlentities($_REQUEST['category']) : ''
	);
	$args = array();
	$args['post_type'] = 'product';
	
	// if sent some category, limit the loop
	if(trim($requestData['category']) != ""){
		$args['taxonomy'] = 'product_cat';   
		$args['term'] = $requestData['category'];
	}
	// show all posts
	$args['posts_per_page'] = -1;
	
	$loop = new WP_Query( $args );
	echo '<div class="kingdom-product-box">';
	echo '<table class="product">
		<thead>
			<tr class="kingdom-tabel-title">
				<td style="text-align: center;border-right: 1px solid #DADADA;">Nr.</td>
				<td style="text-align: center;border-right: 1px solid #DADADA;"><input type="checkbox" checked id="kingdom-check-all" /></td>
				<td style="text-align: center;border-right: 1px solid #DADADA;">&nbsp;Image</td>
				<td style="text-align: center;border-right: 1px solid #DADADA;">&nbsp;Main Color</td>
				<td style="border-right: 1px solid #DADADA;">&nbsp;Title</td>
			</tr>
		</thead>
		<tbody>';
	
	$cc = 0;
	while ( $loop->have_posts() ) : $loop->the_post();
		global $post;
		
		if ( has_post_thumbnail() ){
			$prev_thumb = get_the_post_thumbnail( $post->ID, array(50, 50) ); 
		}
?>
		<tr>
			<td class="product-number"><?php echo ++$cc;?>.</td>
			<td class="product-check" valign="top"><input style="margin-top: 10px;" type="checkbox" checked id="kingdom-check-<?php echo $post->ID;?>" class="kingdom-elements" /></td>
			<td class="product-image">
				<a href="<?php echo $thumb;?>" target="_blank">
					<?php echo $prev_thumb;?>
				</a>
			</td>
			<td class="product-color-palette">
				<?php
					$preview_color = '';
					// get the current color palette for this product
					$colors = wp_get_object_terms( $post->ID, 'pa_color');
					if(count($colors) > 0){
						foreach ($colors as $key => $value){
							// check if is color 
							
							$__color = $kingdom->isValidColorName($value->name); 
							if( !$__color === false ){
								if(count($__color) > 0){
									$rgb = "rgb(" . ( implode(",", $__color) ) . ")";
								}
								$preview_color .= '<div style="font-size: 10px; color:#fff; text-shadow: 0.1em 0.1em 0.2em black; line-height: 24px; border: 1px solid #dadada;background-color: ' . ( $rgb ) . ';width: 100px;height: 22px; float: left; margin: 0px 2px 2px 0px;">' . ( $value->name ) . '</div>';
							}
						}
					}
				?>
				<div id="pcp-response-<?php echo $post->ID;?>" style="margin: 10px 0px 0px 8px;float: left;"><?php echo $preview_color;?></div>
			</td>
			<td class="product-data">
				<h4 class="product-title">
					<a href="<?php echo get_permalink($post->ID);?>" target="_blank"><?php the_title();?></a>
				</h4>
			</td>
		</tr>	
		<?php
	endwhile;
	echo '</tbody></table></div>'; // close the table

	die(); // this is required to return a proper result
}

function kingdom_prepareForInList($v) {
	return "'".$v."'";
}

function kingdom_db_custom_insert($table, $fields, $ignore=false, $wp_way=false) {
	global $wpdb;
	if ( $wp_way && !$ignore ) {
		$wpdb->insert( 
			$table, 
			$fields['values'], 
			$fields['format']
		);
	} else {
	 
		$formatVals = implode(', ', array_map('kingdom_prepareForInList', $fields['format']));
		$theVals = array();
		foreach ( $fields['values'] as $k => $v ) $theVals[] = $k;

		$q = "INSERT " . ($ignore ? "IGNORE" : "") . " INTO $table (" . implode(', ', $theVals) . ") VALUES (" . $formatVals . ");";
		foreach ($fields['values'] as $kk => $vv)
			$fields['values']["$kk"] = esc_sql($vv);
  
				$q = vsprintf($q, $fields['values']);
		$r = $wpdb->query( $q );
	}
}

function kingdom_load_terms($taxonomy){
	global $wpdb;
	
	$query = "SELECT DISTINCT t.name FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_id = t.term_id WHERE 1=1 AND tt.taxonomy = '".esc_sql($taxonomy)."'";
	$result =  $wpdb->get_results($query , OBJECT);
	return $result;                 
}

function kingdom_add_attribute($post_id, $key, $value) 
{
    global $wpdb;
    global $woocommerce;
	 
	 
	$attribute_label = $key;
    $attribute_name = woocommerce_sanitize_taxonomy_name($key);
 
    // set attribute type
    $attribute_type = 'select';
    
    // check for duplicates
    $attribute_taxonomies = $wpdb->get_var("SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = '".esc_sql($attribute_name)."'");
    
    if ($attribute_taxonomies) {
        // update existing attribute
        $wpdb->update(
            $wpdb->prefix . 'woocommerce_attribute_taxonomies', array(
                'attribute_label' => $attribute_label,
                'attribute_name' => $attribute_name,
                'attribute_type' => $attribute_type,
                'attribute_orderby' => 'name'
            ), array('attribute_name' => $attribute_name)
        );
    } else {
        // add new attribute
        $wpdb->insert(
            $wpdb->prefix . 'woocommerce_attribute_taxonomies', array(
            	'attribute_label' => $attribute_label,
            	'attribute_name' => $attribute_name,
            	'attribute_type' => $attribute_type,
            	'attribute_orderby' => 'name'
            )
        );
    }

    // avoid object to be inserted in terms
    if (is_object($value))
        return;

    // add attribute values if not exist
    $taxonomy = wc_attribute_taxonomy_name($attribute_name);
	 
    if( is_array( $value ) )
    {
        $values = $value;
    }
    else
    {
        $values = array($value);
    }
   
	// check taxonomy
    if( !taxonomy_exists( $taxonomy ) ) 
    { 
        // add attribute value
        foreach ($values as $attribute_value) {
        	$attribute_value = (string) $attribute_value;
            if(is_string($attribute_value)) {
                // add term
                $name = stripslashes($attribute_value);
                $slug = sanitize_title($name);
				
                if( !term_exists($name) ) {
                    if( trim($slug) != '' && trim($name) != '' ) {
                    	kingdom_db_custom_insert(
                    		$wpdb->terms,
                    		array(
                    			'values' => array(
                                	'name' => $name,
                                	'slug' => $slug
								),
								'format' => array(
									'%s', '%s'
								)
                    		),
                    		true
                    	);

                        // add term taxonomy
                        $term_id = $wpdb->insert_id;
                    	kingdom_db_custom_insert(
                    		$wpdb->term_taxonomy,
                    		array(
                    			'values' => array(
                                	'term_id' => $term_id,
                                	'taxonomy' => $taxonomy
								),
								'format' => array(
									'%d', '%s'
								)
                    		),
                    		true
                    	);
						$term_taxonomy_id = $wpdb->insert_id;
						$__dbg = compact('taxonomy', 'attribute_value', 'term_id', 'term_taxonomy_id');
                    }
                } else {
                    // add term taxonomy
                    $term_id = $wpdb->get_var("SELECT term_id FROM {$wpdb->terms} WHERE name = '".esc_sql($name)."'");
                    kingdom_db_custom_insert(
                    	$wpdb->term_taxonomy,
                    	array(
                    		'values' => array(
                           		'term_id' => $term_id,
                           		'taxonomy' => $taxonomy
							),
							'format' => array(
								'%d', '%s'
							)
                    	),
                    	true
                    );
					$term_taxonomy_id = $wpdb->insert_id;
					$__dbg = compact('taxonomy', 'attribute_value', 'term_id', 'term_taxonomy_id');
                }
            }
        }
    }
    else 
    {
        // get already existing attribute values
        $attribute_values = array();

		$terms = kingdom_load_terms($taxonomy);
        foreach ($terms as $term) {
           	$attribute_values[] = $term->name;
        }
        
        // Check if $attribute_value is not empty
        if( !empty( $attribute_values ) )
        {
            foreach( $values as $attribute_value ) 
            {
            	$attribute_value = (string) $attribute_value;
                if( !in_array( $attribute_value, $attribute_values ) ) 
                {
                    // add new attribute value
                    $__term_and_tax = wp_insert_term($attribute_value, $taxonomy);
					$__dbg = compact('taxonomy', 'attribute_value', '__term_and_tax');
					//var_dump('<pre>1b: ',$__dbg,'</pre>');
                }
            }
        }
    }

    // Add terms
    if( is_array( $value ) )
    {
        foreach( $value as $dm_v )
        {
        	$dm_v = (string) $dm_v;
            if( !is_array($dm_v) && is_string($dm_v)) {
                $__term_and_tax = wp_insert_term( $dm_v, $taxonomy );
				$__dbg = compact('taxonomy', 'dm_v', '__term_and_tax');
				//var_dump('<pre>2: ',$__dbg,'</pre>');
            }
        }
    }
    else
    {
    	$value = (string) $value;
        if( !is_array($value) && is_string($value) ) {
            $__term_and_tax = wp_insert_term( $value, $taxonomy );
			$__dbg = compact('taxonomy', 'value', '__term_and_tax');
			//var_dump('<pre>2b: ',$__dbg,'</pre>');
        }
    }
	
    // link to woocommerce attribute values
    if( !empty( $values ) )
    {
        foreach( $values as $term )
        {
        	
            if( !is_array($term) && !is_object( $term ) )
            { 
                $term = sanitize_title($term);
                
                $term_taxonomy_id = $wpdb->get_var( "SELECT tt.term_taxonomy_id FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} as tt ON tt.term_id = t.term_id WHERE t.slug = '".esc_sql($term)."' AND tt.taxonomy = '".esc_sql($taxonomy)."'" );
  
	                    if( $term_taxonomy_id ) 
	                    {
	                        $checkSql = "SELECT * FROM {$wpdb->term_relationships} WHERE object_id = {$post_id} AND term_taxonomy_id = {$term_taxonomy_id}";
                    if( !$wpdb->get_var($checkSql) ) {
                        $wpdb->insert(
                                $wpdb->term_relationships, array(
	                                'object_id' => $post_id,
	                                'term_taxonomy_id' => $term_taxonomy_id
                                )
                        );
                    }
                }
            }
        }
    }
}
add_action('wp_ajax_kingdom_process_product', 'kingdom_process_product_callback');
function kingdom_process_product_callback( $product_id ) 
{
	global $kingdom, $wpdb;
	
	// load GetMostCommonColors class 
	require_once( $kingdom->cfg['paths']['theme_dir_path'] . 'lib/commoncolors/colors.class.php');
	
	$requestData = array(
		'ID' => isset($_REQUEST['ID']) ? htmlentities($_REQUEST['ID']) : $product_id,
		'debug' => isset($_REQUEST['debug']) ? htmlentities($_REQUEST['debug']) : 0
	);
	 
	$args = array();
	$args['post_type'] = 'product';
	
	// if sent some ID, limit the loop
	if(trim($requestData['ID']) != ""){
		$args['p'] = $requestData['ID'];
	}
	$args['posts_per_page'] = 1;
	
	$loop = new WP_Query( $args );
 
	while ( $loop->have_posts() ) : $loop->the_post();
		global $post;
	
		$post_id = $post->ID;
		
		if ( has_post_thumbnail() ){
			$thumb = wp_get_attachment_url( get_post_thumbnail_id() );
		}
		 
		if( trim( $thumb ) != "" ){
			$checkImage = new wooColorsGetMostCommonColors( $thumb );

			$arrColors = $checkImage->getColors();
			 
			// debug
			if($requestData['debug'] == 1){
				foreach ($arrColors as $key => $value){
					$colorName = $checkImage->convertHexToColorNames( "#" . $key );
					echo '<div style="width:240px; height: 20px; float: left;margin: 0px 10px 0px 0px;background-color: #' . $key . '">' . $key . ' - ' . ( $colorName ) . '</div>';
				}
			}
			if( count( $arrColors ) > 0 ){
				$colors = array();
				foreach($arrColors as $key => $value){
					if(count($colors) >= $checkImage->config["return_colors_nr"]){
						continue;
					}
					
					$colorName = $checkImage->convertHexToColorNames( "#" . $key );
					$colors[$colorName] = $key;
				}
				
				if(count($colors) > 0){
					$toDBColors = array();
					foreach ($colors as $key => $value){
						$rgb = $checkImage->config['named_color'][$key];
						if(count($rgb) > 0){
							$rgb = "rgb(" . ( implode(",", $rgb) ) . ")";
						}
						
						array_push($toDBColors, $key);
						$html[] = '<div style="font-size: 10px; color:#fff; text-shadow: 0.1em 0.1em 0.2em black; line-height: 24px; border: 1px solid #dadada;background-color: ' . ( $rgb ) . ';width: 100px;height: 22px; float: left; margin: 0px 2px 2px 0px;">' . ( $key ) . '</div>';
					}
				}
			}
			 
			if(count($colors) > 0){
				foreach ($toDBColors as $color) {
					kingdom_add_attribute( $post->ID, 'Color', $color );
				}
			}
		}
		
	endwhile;
	
	if( (int) $product_id == 0 ){
		die(json_encode(array(
			'status' 	=> 'valid',
			'html'		=> @implode("\n", $html)
		)));	
	}
}

add_action( 'wwcAmzAff_after_product_import', 'kingdom_bulk_color_extract' );
function kingdom_bulk_color_extract( $lastId ){
	kingdom_process_product_callback( $lastId ); 
}
