<?php
/**
 * Bulk Products Colors Checker - return as json_encode
 * http://www.aa-team.name
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		0.1 - in development mode
 */ 
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
		
			'color_config ' => array(
				'title' 	=> 'Color Filter Settings',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> true, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				// create the box elements array
				'elements'	=> array(
					
					'return_colors_nr' => array(
						'type' 		=> 'select',
						'std' 		=> '1',
						'width'		=> '60px',
						'size' 		=> 'small',
						'title' 	=> 'Number of colors',
						'desc' 		=> 'How many colors to be extracted from the product image.',
						'options'	=> range(1, 5)
					)
					
					,'reduce_brightness' => array(
						'type' 		=> 'select',
						'std' 		=> true,
						'size' 		=> 'small',
						'width'		=> '100px',
						'title' 	=> 'Reduce Brightness',
						'desc' 		=> 'If the image has brightness, select yes/no if you want it to be reduced.',
						'options'	=> array(
							true => "YES",
							false => "NO"
						)
					)
					
					,'reduce_gradients' => array(
						'type' 		=> 'select',
						'std' 		=> true,
						'size' 		=> 'small',
						'width'		=> '100px',
						'title' 	=> 'Reduce Gradients',
						'desc' 		=> 'If the image has gradients, select yes/no if you want it to be reduced.',
						'options'	=> array(
							true => "YES",
							false => "NO"
						)
					)
					
					,'delta' => array(
						'type' 		=> 'select',
						'std' 		=> 5,
						'width'		=> '60px',
						'size' 		=> 'small',
						'title' 	=> 'Delta',
						'desc' 		=> 'Delta is the image color perimeter. ',
						'options'	=> array(
							1 => 1,
							5 => 5,
							10 => 10,
							15 => 15,
							20 => 20,
							25 => 25,
							30 => 30
						)
					)
					
					,'check_on' => array(
						'type' 		=> 'select',
						'std' 		=> '200X200',
						'size' 		=> 'small',
						'width'		=> '100px',
						'title' 	=> 'Resize image before check at size:',
						'desc' 		=> 'We obtain the colors by croping the images in the process.Smaller means faster.' ,
						'options'	=> array(
							'30X30' => "30px X 30px",
							'70X70' => "70px X 70px",
							'100X100' => "100px X 100px",
							'100X100' => "100px X 100px",
							'200X200' => "200px X 200px",
							'300X300' => "300px X 300px",
							'400X400' => "400px X 400px",
							'500X500' => "500px X 500px",
						)
					
					)
					
					,'colors_name' => array(
						'type' 		=> 'textarea',
						'std' 		=> "Beige => 182,174,148\r\nBlack => 0,0,0\r\nBlue =>\t31,83,182\r\nBrown => 88,61,33\r\nGray => 119,119,119\r\nGreen => 86,159,42\r\nOrange => 207,121,7\r\nPink => 213,3,110\r\nPurple => 123,41,168\r\nRed => 178,29,15\r\nTurquoise => 24,197,196\r\nYellow => 200,165,0",
						'size' 		=> 'large',
						'rows' 		=> '15',
						'title' 	=> 'Colors names',
						'desc' 		=> 'One per line. Format: "color name => rgb". E.g: red => 255, 0, 0',
					)
				)
			),
			
			/* define the form_messages box */
			'bulk_products_colors_check' => array(
				'title' 	=> 'With this feature you extract colors from multiple products at once.',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> false, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				// create the box elements array
				'elements'	=> array(
					array(
						'type' 		=> 'app',
						'path' 		=> '{plugin_folder_path}panel.php',
					)
				)
			)
		)
	)
);