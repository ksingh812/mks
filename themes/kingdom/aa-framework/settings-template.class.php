<?php
/*
* Define class aaInterfaceTemplates
* Make sure you skip down to the end of this file, as there are a few
* lines of code that are very important.
*/
! defined( 'ABSPATH' ) and exit;

if(class_exists('aaInterfaceTemplates') != true) {

	class aaInterfaceTemplates {

		/*
		* Some required plugin information
		*/
		const VERSION = '1.0';
		
		/*
		* Store some helpers config
		* 
		*/
		public $cfg	= array();

		/*
		* Required __construct() function that initalizes the AA-Team Framework
		*/
		public function __construct($cfg) 
		{
			$this->cfg = $cfg;   
		}
		
		
		/*
		* bildThePage, method
		* -------------------
		*
		* @params $options = array (requiered)
		* @params $alias = string (requiered)
		* this will create you interface via options array elements
		*/
		public function bildThePage ( $options = array(), $alias='', $module=array(), $showForm=true, $gallery_items=array() ) 
		{
			global $kingdom;
			
			// reset as array, this will stock all the html content, and at the end return it
			$html = array();
  
			if(count($options) == 0) {
				return 'Please fill whit some options content first!';
			}
			
			$noRowElements = array('message', 'app', 'images_gallery');
			
			foreach ( $options as $theBoxs ) {
				
				// loop the all the boxs
				foreach ( $theBoxs as $box_id => $box ){
					
					$box_id = $alias . "_" . $box_id;
					$settings = array();
					
					// get the values from DB
					$settings = get_option($box_id);
					
					// check if isset and string have content
					if(isset($dbValues) && @trim($dbValues) != ""){
						$settings = maybe_unserialize($settings);
					}
					
					// create defalt setup for each header, prevent php notices
					if(!isset($box['header'])) $box['header']= false;
					if(!isset($box['toggler'])) $box['toggler']= false;
					if(!isset($box['buttons'])) $box['buttons']= false;
					if(!isset($box['style'])) $box['style']= 'panel';
					
					$box_show_wrappers = true;
					if ( !isset($box['panel_setup_verification']) )
						$box['panel_setup_verification'] = false;
					
					if ( $box['panel_setup_verification'] ) {

						$tryLoadInterface = str_replace("{plugin_folder_path}", $module["folder_path"], $box['elements'][0]['path']);
									
						if(is_file($tryLoadInterface)) {
							// Turn on output buffering
							ob_start();
										
							require( $tryLoadInterface  );
  
							if ( isset($__module_is_setup_valid) && $__module_is_setup_valid !==true ) {
								$box_show_wrappers = false;
							}
									
							//copy current buffer contents into $message variable and delete current output buffer
							$__error_msg_panel = ob_get_clean();
						}
					}
  
					if ( $box_show_wrappers ) {
					// container setup
					$html[] = '<div class="kingdom-' . ( $box['size'] ) . '">
                        	<div class="kingdom-' . ( $box['style'] ) . '">';
							
					// hide panel header only if it's requested
					if( $box['header'] == true ) {
						$html[] = '<div class="kingdom-panel-header">
							<span class="kingdom-panel-title">
								' . ( isset($box['icon']) ? '<img src="' . ( $box['icon'] ) . '" />' : '' ) . '
								' . ( $box['title'] ) . '
							</span>
							 ' . ( $box['toggler'] == true ? '<span class="kingdom-panel-toggler"></span>' : '' ) . '
						</div>';
					}
						
					$html[] = '<div class="kingdom-panel-content">';
					if($showForm){
						$html[] = '<form class="kingdom-form" id="' . ( $box_id ) . '" action="#save_with_ajax">';
					}
					
					// create a hidden input for sending the prefix
					$html[] = '<input type="hidden" id="box_id" name="box_id" value="' . ( $box_id ) . '" />';
					
					$html[] = '<input type="hidden" id="box_nonce" name="box_nonce" value="' . ( wp_create_nonce( $box_id . '-nonce') ) . '" />';
					} // end if show box wrappers

					$html[] = $this->tabsHeader($box); // tabs html header

					// loop the box elements
					if(count($box['elements']) > 0){
					
						// loop the box elements now
						foreach ( $box['elements'] as $elm_id => $value ){

							// some helpers. Reset an each loop, prevent collision
							$val = '';
							$select_value = '';
							$checked = '';
							$option_name = isset($option_name) ? $option_name : '';
							
							// Set default value to $val
							if ( isset( $value['std']) ) {
								$val = $value['std'];
							}
							
							// If the option is already saved, ovveride $val
							if ( ( $value['type'] != 'info' ) ) {
								/*if ( isset($settings[($elm_id)] ) ) {
										$val = $settings[( $elm_id )];
										
										// Striping slashes of non-array options
										if ( !is_array($val) ) {
											$val = stripslashes( $val );
											if($val == '') $val = true;
										}
								}*/
                                if ( isset($settings[($elm_id)] )
                                    && (
                                        ( !is_array($settings[($elm_id)]) && @trim($settings[($elm_id)]) != "" )
                                        ||
                                        ( is_array($settings[($elm_id)]) /*&& !empty($settings[($elm_id)])*/ )
                                    )
                                ) {
                                        $val = $settings[( $elm_id )];

                                        // Striping slashes of non-array options
                                        if ( !is_array($val) ) {
                                            $val = stripslashes( $val );
                                            //if($val == '') $val = true;
                                        }
                                }
							}
							
							// If there is a description save it for labels
							$explain_value = '';
							if ( isset( $value['desc'] ) ) {
								$explain_value = $value['desc'];
							}
							
							if(!in_array( $value['type'], $noRowElements)){
								
								// the row and the label 
								$html[] = '<div class="' . ( $value['type'] == 'html' ? 'kingdom-form-nomargin' : '') . ' kingdom-form-row' . ($this->tabsElements($box, $elm_id)) . '">
									   <label for="' . ( $elm_id ) . '">' . ( isset($value['title']) ? $value['title'] : '' ) . '</label>
									   <div class="kingdom-form-item'. ( isset($value['size']) ? " " . $value['size'] : '' ) .'">';
							}
							
							// the element description
							if(isset($value['desc'])) $html[]	= '<span class="formNote">' . ( $value['desc'] ) . '</span>';
							
							switch ( $value['type'] ) {
								
								// Basic text input
								case 'text':
									$html[] = '<input ' . ( isset($value['force_width']) ? "style='width:" . ( $value['force_width'] ) . "px;'" : '' ) . ' id="' . esc_attr( $elm_id ) . '" name="' . esc_attr( $option_name . $elm_id ) . '" type="text" value="' . esc_attr( $val ) . '" />';
								break;
								
								// Wordpress color picker input
								case 'color_picker':
									$html[] = '<input class="kingdom-wp-color-picker" ' . ( isset($value['force_width']) ? "style='width:" . ( $value['force_width'] ) . "px;'" : '' ) . ' id="' . esc_attr( $elm_id ) . '" name="' . esc_attr( $option_name . $elm_id ) . '" type="text" value="' . esc_attr( $val ) . '" />';
								break;
								
								
								// Basic checkbox input
								case 'checkbox':
									$html[] = '<input ' . ( isset($value['force_width']) ? "style='width:" . ( $value['force_width'] ) . "px;'" : '' ) . ' ' . ( $val == true ? 'checked' : '' ). ' id="' . esc_attr( $elm_id ) . '" name="' . esc_attr( $option_name . $elm_id ) . '" type="checkbox" value="" />';
								break;
								
								// Basic html5 range input
								case 'range':
									$html[] = '<div class="range-wrap"><div class="range-value">'. esc_attr( $val ) . 'px</div><input ' . ( isset($value['force_width']) ? "style='width:" . ( $value['force_width'] ) . "px;'" : '' ) . ' ' . ( $val == true ? 'checked' : '' ). ' id="' . esc_attr( $elm_id ) . '" name="' . esc_attr( $option_name . $elm_id ) . '" type="range" min="'. esc_attr( $value['min'] ) .'" max="'. esc_attr( $value['max'] ) .'" step="'. esc_attr( $value['step'] ) .'" value="' . esc_attr( $val ) . '" /></div>';
								break;
								
								// Google Font Select Box
								case 'google-font-select':
									
									$value['options'] = $kingdom->coreFunctions->getAllGfonts();
									 
									$html[] = '<select ' . ( isset($value['force_width']) ? "style='float: left; width:" . ( $value['force_width'] ) . "px;'" : '' ) . ' name="' . esc_attr( $elm_id ) . '" id="' . esc_attr( $elm_id ) . '">';
									
									foreach ($value['options'] as $key => $option ) {
										$selected = '';
										if( $val != '' ) {
											if ( $val == $key ) { $selected = ' selected="selected"';} 
										}
										$html[] = '<option'. $selected .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
									 } 
									$html[] = '</select>';
									
									$html[] = '<p>Font Preview:</p><div class="kingdom-font-preview" id="' . esc_attr( $elm_id ) . '-font-preview">';
									$html[] = '</div>';
								break;
								
								// Basic upload_image
								case 'upload_image':
									$html[] = '<table border="0">';
									$html[] = '<tr>';
									$html[] = 	'<td>';
									$html[] = 		'<input class="upload-input-text" name="' . ( $elm_id ) . '" id="' . ( $elm_id ) . '_upload" type="text" value="' . ( $val ) . '" />';
									
									$html[] = 		'<script type="text/javascript">
										jQuery("#' . ( $elm_id ) . '_upload").data({
											"w": ' . ( $value['thumbSize']['w'] ) . ',
											"h": ' . ( $value['thumbSize']['h'] ) . ',
											"zc": ' . ( $value['thumbSize']['zc'] ) . '
										});
									</script>';
									
									$html[] = 	'</td>';
									$html[] = '<td>';
									$html[] = 		'<a href="#" class="button upload_button" id="' . ( $elm_id ) . '">' . ( $value['value'] ) . '</a> ';
									$html[] = 		'<a href="#" class="button reset_button ' . $hide . '" id="reset_' . ( $elm_id ) . '" title="' . ( $elm_id ) . '">Remove</a> ';
									$html[] = '</td>';
									$html[] = '</tr>';
									$html[] = '</table>';
									
									$html[] = '<a class="thickbox" id="uploaded_image_' . ( $elm_id ) . '" href="' . ( $val ) . '" target="_blank">';
									
									if(!empty($val)){
										$imgSrc = $kingdom->image_resize( $val, $value['thumbSize']['w'], $value['thumbSize']['h'], $value['thumbSize']['zc'] );
										$html[] = '<img style="border: 1px solid #dadada;" id="image_' . ( $elm_id ) . '" src="' . ( $imgSrc ) . '" />';
									}
									$html[] = '</a>';
									
								break;
								
								// Basic upload_image
								case 'upload_image_wp':
									$preview_size = (isset($value['preview_size']) ? $value['preview_size'] : 'thumbnail');
									if( (int) $val > 0 ){
										$image = wp_get_attachment_image_src( $val, $preview_size );
										$image_full = wp_get_attachment_image_src( $val, 'full' );
										if( count($image) > 0 ){
											$image = $image[0];
										}
										
										if( count($image_full) > 0 ){
											$image_full = $image_full[0];
										}
									}
									
									$html[] = '<div class="kingdom-upload-image-wp-box">';
									$html[] = 	'<a data-previewsize="' . ( $preview_size ) . '" class="upload_image_button_wp kingdom-button blue" ' . ( isset($value['force_width']) ? "style='" . ( trim($val) != "" ? 'display:none;' : '' ) . "width:" . ( $value['force_width'] ) . "px;'" : '' ) . ' href="#">' . ( $value['value'] ) . '</a>';
									$html[] = 	'<input type="hidden" name="' . ( $elm_id ) . '" value="' . ( $val ) . '">';
									if( trim($image_full) != "" ){
										$html[] = 	'<a href="' . ( $image_full ) . '" target="_blank" class="upload_image_preview" style="display: ' . ( trim($val) == "" ? 'none' : 'block' ). '">';
										$html[] = 		'<img src="' . ( $image ) . '" style="display: ' . ( trim($val) == "" ? 'none' : 'inline-block' ). '">';	
										$html[] = 	'</a>';
									}
									$html[] =	'<div class="kingdom-prev-buttons" style="display: ' . ( trim($val) == "" ? 'none' : 'inline-block' ). '">';
									$html[] = 		'<span class="change_image_button_wp kingdom-button green">Change Image</span>';
									$html[] = 		'<span class="remove_image_button_wp kingdom-button red">Remove Image</span>';
									$html[] =	'</div>';
									$html[] = '</div>';
								break;
								
								// Basic textarea
								case 'textarea':
									$cols = "120";
									if(isset($value['cols'])) {
										$cols = $value['cols'];
									}
									$height = "style='height:120px;'";
									if(isset($value['height'])) {
										$height = "style='height:{$value['height']};'";
									}
									
									$html[] = '<textarea id="' . esc_attr( $elm_id ) . '" ' . $height . ' cols="' . ( $cols ) . '" name="' . esc_attr( $option_name . $elm_id ) . '">' . esc_attr( $val ) . '</textarea>';
								break;
								
								// Basic textarea
								case 'images_gallery':
									$html[] = '<ul class="kingdom-gallery-items">';
									
									if( count($gallery_items) > 0 ){
										foreach ($gallery_items as $key_item => $value_item) {
											 
											$has_image = (isset($value_item['kingdom-gallery-images']) && trim($value_item['kingdom-gallery-images']) != "") ? true : false;
											
											$html[] = 	'<li class="kingdom-gallery-item">';
											$html[] = 		'<table>';
											$html[] = 			'<tr>';
											$html[] = 				'<td class="kingdom-gallery-order">';
											$html[] = 					'#' . $key_item;
											$html[] = 				'</td>';
											$html[] = 				'<td width="200">';
											$html[] = 					'<div class="kingdom-gallery-image ' . ( ($has_image === true) ? '' : 'has_no_image' ) . '">
																			<input type="hidden" name="kingdom-gallery-images" value="' . ( $value_item['kingdom-gallery-images'] ) . '" />
																			' . ( ($has_image === true) ? "<img src='" . ( $value_item['kingdom-gallery-images'] ) . "' />" : '') . '
																			<div class="the_slide_options">
																				<a href="#" class="kingdom-button green kingdom-upload-button">Change the image</a>
																				<a href="#" class="kingdom-button red kingdom-upload-remove-button">Remove the image</a>
																			</div>
																			
																			<div class="the_slide_options_no_image">
																				<a href="#" class="kingdom-button green kingdom-upload-button">Add new image</a>
																			</div>
																		</div>';
											$html[] = 				'</td>';
											
											if( isset($value['options']) && count($value['options']) > 0 ){
												
												// refactoring the default settings
												if( count($value['options']) > 0 ){
													foreach ($value['options'] as $key_sub_item => $value_sub_item) {														
														foreach ($value_sub_item["details"]['elements'] as $key_sub_item_default => $value_sub_item_default) {
															$value['options'][$key_sub_item]["details"]['elements'][$key_sub_item_default]['std'] = $value_item[$key_sub_item_default];
														}  
													}
												}
												$html[] = '<td>';
												$html[] =	$this->bildThePage( $value['options'] , $kingdom->alias, array(), false, false);
												$html[] = '</td>';
											}
											
											$html[] = 				'<td width="150">';
											$html[] = 					'<a href="#" class="kingdom-button red kingdom-slide-remove-button">&nbsp;Remove this slide&nbsp;</a>';
											$html[] = 					'<a href="#" class="kingdom-button orange kingdom-slide-duplicate-button">Duplicate this slide</a>';
											$html[] = 				'</td>';
											$html[] = 			'</tr>';
											$html[] = 		'</table>';
											$html[] = 	'</li>';
										}
									}

									$html[] = '</ul>';

									// print the template
									$html[] = 	'<script type="template" id="kingdom-gallery-item-model">';
									
									$html[] = 	'<li class="kingdom-gallery-item">';
									$html[] = 		'<table>';
									$html[] = 			'<tr>';
									$html[] = 				'<td class="kingdom-gallery-order">';
									$html[] = 					'#';
									$html[] = 				'</td>';
									$html[] = 				'<td width="200">';
									$html[] = 					'<div class="kingdom-gallery-image has_no_image">
																	<input type="hidden" name="kingdom-gallery-images" />
																	<div class="the_slide_options">
																		<a href="#" class="kingdom-button green kingdom-upload-button">Change the image</a>
																		<a href="#" class="kingdom-button red kingdom-upload-remove-button">Remove the image</a>
																	</div>
																	
																	<div class="the_slide_options_no_image">
																		<a href="#" class="kingdom-button green kingdom-upload-button">Add new image</a>
																	</div>
																</div>';
									$html[] = 				'</td>';
									
									if( isset($value['options']) && count($value['options']) > 0 ){
										 
										$html[] = '<td>';
										$html[] =	$this->bildThePage( $value['options'] , $kingdom->alias, array(), false, false);
										$html[] = '</td>';
									}
									
									$html[] = 				'<td width="150">';
									$html[] = 					'<a href="#" class="kingdom-button red kingdom-slide-remove-button">&nbsp;Remove this slide&nbsp;</a>';
									$html[] = 					'<a href="#" class="kingdom-button orange kingdom-slide-duplicate-button">Duplicate this slide</a>';
									$html[] = 				'</td>';
									$html[] = 			'</tr>';
									$html[] = 		'</table>';
									$html[] = 	'</li>';
									
									$html[] = '</script>';
									
									$html[] = '<a href="#" class="kingdom-button blue kingdom-gallery-add-new">&nbsp;Add new slide&nbsp;</a>';
								break;
								
								// Basic html/text message
								case 'message':
									$html[] = '<div class="kingdom-message kingdom-' . ( $value['status'] ) . ' ' . ($this->tabsElements($box, $elm_id)) . '">' . ( $value['html'] ) . '</div>';
								break;
								
								// buttons
								case 'buttons':
								
									// buttons for each box
									
									if(count($value['options']) > 0){
										foreach ($value['options'] as $key => $value){
											$html[] = '<input 
												type="' . ( $value['type'] ) . '" 
												style="width:' . ( $value['width'] ) . '" 
												value="' . ( $value['value'] ) . '" 
												class="kingdom-button ' . ( $value['color'] ) . ' ' . ( isset($value['pos']) ? $value['pos'] : '' ) . ' ' . ( $value['action'] ) . '" 
											/>';
										}
									}
									
								break;
								
								
								// Basic html/text message
								case 'html':
									$html[] = $value['html'];
								break;
								
								// Basic app, load the path of this file
								case 'app':
									
									$tryLoadInterface = str_replace("{plugin_folder_path}", $module["folder_path"], $value['path']);
									
									if(is_file($tryLoadInterface)) {
										// Turn on output buffering
										ob_start();
										
										require( $tryLoadInterface  );
										
										//copy current buffer contents into $message variable and delete current output buffer
										$html[] = ob_get_clean();
									}
								break;
								
								// Select Box
								case 'select':
									$html[] = '<select ' . ( isset($value['force_width']) ? "style='width:" . ( $value['force_width'] ) . "px;'" : '' ) . ' name="' . esc_attr( $elm_id ) . '" id="' . esc_attr( $elm_id ) . '">';
									
									foreach ($value['options'] as $key => $option ) {
										$selected = '';
										if( $val != '' ) {
											if ( $val == $key ) { $selected = ' selected="selected"';} 
										}
										$html[] = '<option'. $selected .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
									 } 
									$html[] = '</select>';
								break;
								
								// multiselect Box
								case 'multiselect':
									$html[] = '<select multiple="multiple" size="3" name="' . esc_attr( $elm_id ) . '[]" id="' . esc_attr( $elm_id ) . '">';
									
									if(count($option) > 1){
										foreach ($value['options'] as $key => $option ) {
											$selected = '';
											if( $val != '' ) {
												if ( in_array($key, $val) ) { $selected = ' selected="selected"';} 
											}
											$html[] = '<option'. $selected .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
										} 
									}
									$html[] = '</select>';
								break;
								
								// multiselect Box
								case 'multiselect_left2right':

									$available = array(); $selected = array();
									foreach ($value['options'] as $key => $option ) {
										if( $val != '' ) {
											if ( in_array($key, $val) ) { $selected[] = $key; } 
										}
									}
									$available = array_diff(array_keys($value['options']), $selected);
									
									$html[] = '<div class="kingdom-multiselect-half kingdom-multiselect-available" style="margin-right: 2%;">';
									if( isset($value['info']['left']) ){
										$html[] = '<h5>' . ( $value['info']['left'] ) . '</h5>';
									}
									$html[] = '<select multiple="multiple" size="' . (isset($value['rows_visible']) ? $value['rows_visible'] : 5) . '" name="' . esc_attr( $elm_id ) . '-available[]" id="' . esc_attr( $elm_id ) . '-available" class="multisel_l2r_available">';
									
									if(count($available) > 0){
										foreach ($value['options'] as $key => $option ) {
											if ( !in_array($key, $available) ) continue 1;
											$html[] = '<option value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
										} 
									}
									$html[] = '</select>';
									
									$html[] = '</div>';
									
									$html[] = '<div class="kingdom-multiselect-half kingdom-multiselect-selected">';
									if( isset($value['info']['right']) ){
										$html[] = '<h5>' . ( $value['info']['right'] ) . '</h5>';
									}
									$html[] = '<select multiple="multiple" size="' . (isset($value['rows_visible']) ? $value['rows_visible'] : 5) . '" name="' . esc_attr( $elm_id ) . '[]" id="' . esc_attr( $elm_id ) . '" class="multisel_l2r_selected">';
									
									if(count($selected) > 0){
										foreach ($value['options'] as $key => $option ) {
											if ( !in_array($key, $selected) ) continue 1;
											$isselected = ' selected="selected"'; 
											$html[] = '<option'. $isselected .' value="' . esc_attr( $key ) . '">' . esc_html( $option ) . '</option>';
										} 
									}
									$html[] = '</select>';
									$html[] = '</div>';
									$html[] = '<div style="clear:both"></div>';
									$html[] = '<div class="multisel_l2r_btn" style="">';
									$html[] = '<span style="display: inline-block; width: 24.1%; text-align: center;"><input id="' . esc_attr( $elm_id ) . '-moveright" type="button" value="Move Right" class="moveright kingdom-button gray"></span>';
									$html[] = '<span style="display: inline-block; width: 24.1%; text-align: center;"><input id="' . esc_attr( $elm_id ) . '-moverightall" type="button" value="Move Right All" class="moverightall kingdom-button gray"></span>';
									$html[] = '<span style="display: inline-block; width: 24.1%; text-align: center;"><input id="' . esc_attr( $elm_id ) . '-moveleft" type="button" value="Move Left" class="moveleft kingdom-button gray"></span>';
									$html[] = '<span style="display: inline-block; width: 24.1%; text-align: center;"><input id="' . esc_attr( $elm_id ) . '-moveleftall" type="button" value="Move Left All" class="moveleftall kingdom-button gray"></span>';
									$html[] = '</div>';
								break;
								
							}
							
							if(!in_array( $value['type'], $noRowElements)){
								// close: .kingdom-form-row
								$html[] = '</div>';
								
								// close: .kingdom-form-item
								$html[] = '</div>';
							}
							
						}
					}
					
					// kingdom-message use for status message, default it's hidden
					$html[] = '<div class="kingdom-message" id="kingdom-status-box" style="display:none;"></div>';
					
					if( $box['buttons'] == true && !is_array($box['buttons']) ) {
						// buttons for each box
						$html[] = '<div class="kingdom-button-row">
							<input type="reset" value="Reset to default value" class="kingdom-button gray left" />
							<input type="submit" value="Save the settings" class="kingdom-button green kingdom-saveOptions" />
						</div>';
					}
					elseif( is_array($box['buttons']) ){
						// buttons for each box
						$html[] = '<div class="kingdom-button-row">';
						
						foreach ( $box['buttons'] as $key => $value ){
							$html[] = '<input type="submit" value="' . ( $value['value'] ) . '" class="kingdom-button ' . ( $value['color'] ) . ' ' . ( $value['action'] ) . '" />';
						}
						
						$html[] = '</div>';
					}
					
					if ( $box_show_wrappers ) {
						
					if($showForm){
						// close: form
						$html[] = '</form>';
					}
					
					// close: .kingdom-panel-content
					$html[] = '</div>';
					
					// close: box style  div (.kingdom-panel)
					$html[] = '</div>';
					
					// close: box size div
					$html[] = '</div>';
					
					} // end if show box wrappers
				}
			}
			
			// return the $html
			return implode("\n", $html);
		}

		/*
		* printBaseInterface, method
		* --------------------------
		*
		* this will add the base DOM code for you options interface
		*/
		public function printBaseInterface() 
		{ 
?>
		<div id="kingdom-wrapper" class="fluid wrapper-kingdom">
    
			<!-- Header -->
			<?php
			// show the top menu
			kingdomAdminMenu::getInstance()->show_menu();
			?>
		
			<!-- Content -->
			<div id="kingdom-content">
				
				<h1 class="kingdom-section-headline">
				</h1>
				
				<!-- Container -->
				<div class="kingdom-container clearfix">
				
					<!-- Main Content Wrapper -->
					<div id="kingdom-content-wrap" class="clearfix">
					
						<!-- Content Area -->
						<div id="kingdom-content-area">
							<!-- Content Area -->
							<div id="kingdom-ajax-response"></div>
							
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
		</div>

<?php
		}
		
		//make Tabs!
		private function tabsHeader($box) {
			$html = array();

			// get tabs
			$__tabs = isset($box['tabs']) ? $box['tabs'] : array();

			$__ret = '';
			if (is_array($__tabs) && count($__tabs)>0) {
				$html[] = '<ul class="tabsHeader">';
				$html[] = '<li style="display:none;" id="tabsCurrent" title=""></li>'; //fake li with the current tab value!
				foreach ($__tabs as $tabClass=>$tabElements) {
					$html[] = '<li><a href="javascript:void(0);" title="'.$tabClass.'">'.$tabElements[0].'</a></li>';
				}
				$html[] = '</ul>';
				$__ret = implode('', $html);
				
			}
			return $__ret;
		}
		
		private function tabsElements($box, $elemKey) {
			// get tabs
			$__tabs = isset($box['tabs']) ? $box['tabs'] : array();

			$__ret = '';
			if (is_array($__tabs) && count($__tabs)>0) {
				foreach ($__tabs as $tabClass=>$tabElements) {

					$tabElements = $tabElements[1];
					$tabElements = trim($tabElements);
					$tabElements = array_map('trim', explode(',', $tabElements));
					if (in_array($elemKey, $tabElements)) 
						$__ret .= ($tabClass.' '); //support element on multiple tabs!
				}
			}
			return ' '.trim($__ret).' ';
		}
	}
}