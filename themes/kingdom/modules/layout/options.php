<?php
/**
 * Dummy module return as json_encode
 * http://www.aa-team.name
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		0.1 - in development mode
 */
global $kingdom;

echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
		
			/* define the form_sizes  box */
			'config' => array(
				'title' 	=> 'General Settings',
				//'icon' 		=> '{theme_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> false, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				// tabs
				'tabs'	=> array(
					'__tab1'	=> array(__('General SETUP', 'kingdom'), 'logo,favicon,border_radius,home_slider,revolution_slider_select'),
					'__tab2'	=> array(__('Colors', 'kingdom'), 'general-colors-setup,primary_color, secondary_color,menu-colors-setup,menu_normal_state_color,menu_hover_state_color,menu_background_color'),
					'__tab3'	=> array(__('Typography & Spacing', 'kingdom'), 'website_main_font,menu_font,main_menu_font_size,main_menu_submenu_font_size,main_content_font_size,h1-font-size,h2-font-size,content-font-size, main_menu_vertical_spacing,main_menu_horizontal_spacing,main_menu_submenu_top_margin'),
					'__tab4'	=> array(__('Shop', 'kingdom'), 'products_per_page,enable_product_general_description,product_general_description_type,enable_product_description_tab,enable_star_rating,enable_product_in_category,enable_product_tags'),
					'__tab5'	=> array(__('Social SETUP', 'kingdom'), 'facebook_url,linkedin_url,youtube_url,twitter_url,google_url,pinterest_url'),
					'__tab6'	=> array(__('Footer', 'kingdom'), 'enable_partners_module, footer_copyright,phone_support,amazon_url,discover_url,money_url,visa_url,paypal_url,mastercard_url'),
				),
				
				// create the box elements array
				'elements'	=> array(
					'logo' => array(
						'type' 			=> 'upload_image_wp',
						'size' 			=> 'large',
						'force_width'	=> '80',
						'preview_size'	=> 'large',	
						'value' 		=> __('Upload Image', 'kingdom'),
						'title' 		=> __('Logo', 'kingdom'),
						'desc' 			=> __('Upload your Logo using the native media uploader', 'kingdom'),
					),
					
					'favicon' => array(
						'type' 			=> 'upload_image_wp',
						'size' 			=> 'large',
						'force_width'	=> '80',
						'preview_size'	=> 'large',	
						'value' 		=> __('Favicon Image', 'kingdom'),
						'title' 		=> __('Favicon', 'kingdom'),
						'desc' 			=> __('Upload your favicon using the native media uploader', 'kingdom'),
					),
					
					'general-colors-setup' => array(
						'type' => 'html',
						'html' => '<h2 class="kingdom-section-title" id="general-colors-setup">' . __('Main Colors:', 'dnh') . '</h2>',
					),
					
					'primary_color' => array(
						'type' 		=> 'color_picker',
						'std' 		=> '#C5403F',
						'size' 		=> 'large',
						'title'		=> __('Primary Color', 'kingdom'),
						'desc'		=> __('The primary color. Choose from the color picker. The default color is: #C5403F', 'kingdom')
					),
					
					'secondary_color' => array(
						'type' 		=> 'color_picker',
						'std' 		=> '#2980B9',
						'size' 		=> 'large',
						'title'		=> __('Secondary Color', 'kingdom'),
						'desc'		=> __('The secondary color. Choose from the color picker. The default color is: #2980B9', 'kingdom')
					),
					
					'menu-colors-setup' => array(
						'type' => 'html',
						'html' => '<h2 class="kingdom-section-title" id="menu-colors-setup">' . __('Menu Colors:', 'dnh') . '</h2>',
					),
					
					'menu_normal_state_color' => array(
						'type' 		=> 'color_picker',
						'std' 		=> '#2c3e50',
						'size' 		=> 'large',
						'title'		=> __('Menu Normal State Color', 'kingdom'),
						'desc'		=> __('The Menu Normal State color. Choose from the color picker. The default color is: #2c3e50', 'kingdom')
					),
					
					'menu_hover_state_color' => array(
						'type' 		=> 'color_picker',
						'std' 		=> '#c0392b',
						'size' 		=> 'large',
						'title'		=> __('Menu Hover State Color', 'kingdom'),
						'desc'		=> __('The Menu Hover State color. Choose from the color picker. The default color is: #c0392b', 'kingdom')
					),
					
					'menu_background_color' => array(
						'type' 		=> 'color_picker',
						'std' 		=> '#FFFFFF',
						'size' 		=> 'large',
						'title'		=> __('Menu Background Color', 'kingdom'),
						'desc'		=> __('The Menu Background color. Choose from the color picker. The default color is: #FFFFFF', 'kingdom')
					),
					
					'border_radius' => array(
						'type' 		=> 'select',
						'std' 		=> 'yes',
						'size' 		=> 'large',
						'force_width'=> '120',
						'title'		=> __('Rounded border', 'kingdom'),
						'desc'		=> __('Want rounderd border? Default is YES.', 'kingdom'),
						'options'	=> array(
							'yes' => 'YES',
							'no' => 'NO',
						)
					),
					
					'website_main_font' => array(
						'type' 		=> 'google-font-select',
						'std' 		=> 'Open Sans',
						'size' 		=> 'small',
						'title'		=> __('Website Main Font Family', 'kingdom'),
						'desc'		=> __('Choose the primary font for your website. Default is Open Sans', 'kingdom')
					),
					
					'menu_font' => array(
						'type' 		=> 'google-font-select',
						'std' 		=> 'Open Sans',
						'size' 		=> 'small',
						'title'		=> __('Main Menu Font Family', 'kingdom'),
						'desc'		=> __('Choose the font for your main menu. Default is Open Sans', 'kingdom')
					),
					
					'main_menu_font_size' => array(
						'type' 		=> 'range',
						'std' 		=> '18',
						'size' 		=> 'medium',
						'step'		=> '1',
						'min'		=> '1',
						'max'		=> '40',
						'title'		=> __('Main Menu Font Size', 'kingdom'),
						'desc'		=> __('Choose the main menu font size for your website. Default is 18px', 'kingdom')
					),
					
					'main_menu_submenu_font_size' => array(
						'type' 		=> 'range',
						'std' 		=> '14',
						'size' 		=> 'medium',
						'step'		=> '1',
						'min'		=> '1',
						'max'		=> '40',
						'title'		=> __('Main Menu Submenu Font Size', 'kingdom'),
						'desc'		=> __('Main menu submenu font size for your website. Default is 14px', 'kingdom')
					),
					
					'main_menu_vertical_spacing' => array(
						'type' 		=> 'range',
						'std' 		=> '20',
						'size' 		=> 'medium',
						'step'		=> '1',
						'min'		=> '1',
						'max'		=> '60',
						'title'		=> __('Main Menu Vertical Spacing', 'kingdom'),
						'desc'		=> __('Main menu vertical spacing between menu elements in px. Default is 20px', 'kingdom')
					),
					
					'main_menu_horizontal_spacing' => array(
						'type' 		=> 'range',
						'std' 		=> '20',
						'size' 		=> 'medium',
						'step'		=> '1',
						'min'		=> '1',
						'max'		=> '60',
						'title'		=> __('Main Menu Horizontal Spacing', 'kingdom'),
						'desc'		=> __('Main menu horizontal spacing between menu elements in px. Default is 20px', 'kingdom')
					),
					
					'main_menu_submenu_top_margin' => array(
						'type' 		=> 'range',
						'std' 		=> '40',
						'size' 		=> 'medium',
						'step'		=> '1',
						'min'		=> '1',
						'max'		=> '100',
						'title'		=> __('Main Menu Submenu Top Margin', 'kingdom'),
						'desc'		=> __('Main Menu Submenu Top Margin in px. Default is 40px', 'kingdom')
					),
					
					'main_content_font_size' => array(
						'type' 		=> 'range',
						'std' 		=> '14',
						'size' 		=> 'medium',
						'step'		=> '1',
						'min'		=> '1',
						'max'		=> '40',
						'title'		=> __('Main Content Font Size', 'kingdom'),
						'desc'		=> __('Main content font size for your website. Default is 14px', 'kingdom')
					),
					
					'h1-font-size' => array(
						'type' 		=> 'range',
						'std' 		=> '28',
						'size' 		=> 'medium',
						'step'		=> '1',
						'min'		=> '1',
						'max'		=> '30',
						'title'		=> __('H1 Tags Font Size', 'kingdom'),
						'desc'		=> __('Choose the main H1 tags font size for your website. Default is 28px', 'kingdom')
					),
					
					'h2-font-size' => array(
						'type' 		=> 'range',
						'std' 		=> '18',
						'size' 		=> 'medium',
						'step'		=> '1',
						'min'		=> '1',
						'max'		=> '30',
						'title'		=> __('H2 Tags Font Size', 'kingdom'),
						'desc'		=> __('Choose the main H2 tags font size for your website. Default is 18px', 'kingdom')
					),
					
					/*'responsiveness' => array(
						'type' 		=> 'select',
						'std' 		=> 'true',
						'force_width'=> '80',
						'size' 		=> 'large',
						'title'		=> __('Enable Responsiveness', 'kingdom'),
						'desc'		=> __('Activate or deactivate this feature for your theme.', 'kingdom'),
						'options'	=> array(
							'true' => 'Yes',
							'false'=> 'No'
						)
					),*/
					
					'phone_support' => array(
						'type' 		=> 'text',
						'std' 		=> '24/7 Phone support: 0800 556-880',
						'size' 		=> 'large',
						'title'		=> __('Phone Support text', 'kingdom'),
						'desc'		=> __('The text for header Phone Support box', 'kingdom')
					),
					
					'footer_copyright' => array(
						'type' 		=> 'text',
						'std' 		=> '&copy; Copyright 2015 <a href="http://aa-team.com">AA-Team</a>. All rights reserved.',
						'size' 		=> 'large',
						'title'		=> __('The Copyright text', 'kingdom'),
						'desc'		=> __('The text for footer copyright', 'kingdom')
					),
					
					'products_per_page' => array(
						'type' 		=> 'text',
						'std' 		=> '12',
						'size' 		=> 'small',
						'title'		=> __('Numer of products/page on category pages', 'amzStoreTheme'),
						'desc'		=> __('Input the number of products do you want to be displayed per page on category pages. Default is 12', 'amzStoreTheme')
					),
					
					'enable_star_rating' => array(
						'type' 		=> 'select',
						'std' 		=> 'true',
						'force_width'=> '80',
						'size' 		=> 'large',
						'title'		=> __('Show Star Rating', 'kingdom'),
						'desc'		=> __('Activate or deactivate this feature for your theme.', 'kingdom'),
						'options'	=> array(
							'true' => 'Yes',
							'false'=> 'No'
						)
					),
					
					'enable_product_general_description' => array(
						'type' 		=> 'select',
						'std' 		=> 'true',
						'force_width'=> '80',
						'size' 		=> 'large',
						'title'		=> __('Show product details general description', 'kingdom'),
						'desc'		=> __('Activate or deactivate this feature for your theme.', 'kingdom'),
						'options'	=> array(
							'true' => 'Yes',
							'false'=> 'No'
						)
					),
					
                    'product_general_description_type' => array(
                        'type'      => 'select',
                        'std'       => 'first_paragraph',
                        'force_width'=> '250',
                        'size'      => 'large',
                        'title'     => __('Product details general description uses', 'kingdom'),
                        'desc'      => __('Activate or deactivate this feature for your theme.', 'kingdom'),
                        'options'   => array(
                            'first_paragraph'   => 'First paragraph from product content',
                            'short_desc'        => 'Woocommerce short description',
                        )
                    ),
					
					'enable_product_description_tab' => array(
						'type' 		=> 'select',
						'std' 		=> 'true',
						'force_width'=> '80',
						'size' 		=> 'large',
						'title'		=> __('Show product details description tab', 'kingdom'),
						'desc'		=> __('Activate or deactivate this feature for your theme.', 'kingdom'),
						'options'	=> array(
							'true' => 'Yes',
							'false'=> 'No'
						)
					),
					
					'enable_product_in_category' => array(
						'type' 		=> 'select',
						'std' 		=> 'true',
						'force_width'=> '80',
						'size' 		=> 'large',
						'title'		=> __('Show product category', 'kingdom'),
						'desc'		=> __('Activate or deactivate this feature for your theme.', 'kingdom'),
						'options'	=> array(
							'true' => 'Yes',
							'false'=> 'No'
						)
					),
					
					'enable_product_tags' => array(
						'type' 		=> 'select',
						'std' 		=> 'true',
						'force_width'=> '80',
						'size' 		=> 'large',
						'title'		=> __('Show product tags', 'kingdom'),
						'desc'		=> __('Activate or deactivate this feature for your theme.', 'kingdom'),
						'options'	=> array(
							'true' => 'Yes',
							'false'=> 'No'
						)
					),
					
					'enable_partners_module' => array(
						'type' 		=> 'select',
						'std' 		=> 'true',
						'force_width'=> '80',
						'size' 		=> 'large',
						'title'		=> __('Show Partners Module in footer', 'kingdom'),
						'desc'		=> __('Activate or deactivate this feature for your theme.', 'kingdom'),
						'options'	=> array(
							'true' => 'Yes',
							'false'=> 'No'
						)
					),
					
					
					// second tab
					'facebook_url' => array(
						'type' 		=> 'text',
						'std' 		=> '#',
						'size' 		=> 'large',
						'title'		=> __('Facebook URL', 'kingdom'),
						'desc'		=> __('Put you facebook page url. E.g: http://facebook.com/your_page', 'kingdom')
					),
					
					'linkedin_url' => array(
						'type' 		=> 'text',
						'std' 		=> '#',
						'size' 		=> 'large',
						'title'		=> __('Linkedin URL', 'kingdom'),
						'desc'		=> __('Put you linkedin page url. E.g: http://linkedin.com/your_page', 'kingdom')
					),
					
					'youtube_url' => array(
						'type' 		=> 'text',
						'std' 		=> '#',
						'size' 		=> 'large',
						'title'		=> __('Youtube URL', 'kingdom'),
						'desc'		=> __('Put you youtube page url. E.g: http://youtube.com/your_page', 'kingdom')
					),
					
					'twitter_url' => array(
						'type' 		=> 'text',
						'std' 		=> '#',
						'size' 		=> 'large',
						'title'		=> __('Twitter URL', 'kingdom'),
						'desc'		=> __('Put you Twitter page url. E.g: http://twitter.com/your_page', 'kingdom')
					),
					
					'google_url' => array(
						'type' 		=> 'text',
						'std' 		=> '#',
						'size' 		=> 'large',
						'title'		=> __('Google URL', 'kingdom'),
						'desc'		=> __('Put you Google page url. E.g: http://google.com/your_page', 'kingdom')
					),
					
					'pinterest_url' => array(
						'type' 		=> 'text',
						'std' 		=> '#',
						'size' 		=> 'large',
						'title'		=> __('Pinterest URL', 'kingdom'),
						'desc'		=> __('Put you Pinterest page url. E.g: http://pinterest.com/your_page', 'kingdom')
					),
					
					
					// third tab
					'amazon_url' => array(
						'type' 		=> 'text',
						'std' 		=> '#',
						'size' 		=> 'large',
						'title'		=> __('Amazon URL', 'kingdom'),
						'desc'		=> __('Put you amazon page url.', 'kingdom')
					),
					
					'discover_url' => array(
						'type' 		=> 'text',
						'std' 		=> '#',
						'size' 		=> 'large',
						'title'		=> __('Linkedin URL', 'kingdom'),
						'desc'		=> __('Put you discover page url.', 'kingdom')
					),
					
					'money_url' => array(
						'type' 		=> 'text',
						'std' 		=> '#',
						'size' 		=> 'large',
						'title'		=> __('Money URL', 'kingdom'),
						'desc'		=> __('Put you money page url.', 'kingdom')
					),
					
					'visa_url' => array(
						'type' 		=> 'text',
						'std' 		=> '#',
						'size' 		=> 'large',
						'title'		=> __('Visa URL', 'kingdom'),
						'desc'		=> __('Put you visa page url.', 'kingdom')
					),
					
					'paypal_url' => array(
						'type' 		=> 'text',
						'std' 		=> '#',
						'size' 		=> 'large',
						'title'		=> __('Paypal URL', 'kingdom'),
						'desc'		=> __('Put you Paypal page url.', 'kingdom')
					),
					
					'mastercard_url' => array(
						'type' 		=> 'text',
						'std' 		=> '#',
						'size' 		=> 'large',
						'title'		=> __('Mastercard URL', 'kingdom'),
						'desc'		=> __('Put you Mastercard page url. ', 'kingdom')
					),
				)
			)
		)
	)
);