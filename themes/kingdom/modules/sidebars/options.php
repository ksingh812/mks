<?php
/**
 * Dummy module return as json_encode
 * http://www.aa-team.name
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		0.1 - in development mode
 */
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
		
			/* define the form_sizes  box */
			'dynamic_sidebars' => array(
				'title' 	=> 'Dynamic Sidebars',
				'icon' 		=> '{theme_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> true, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> array(
					'save' => array(
						'width' => '100px',
						'value' => 'Save sidebars',
						'color' => 'green',
						'action' => 'kingdom_save_sidebars'
					)
				), // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				// create the box elements array
				'elements'	=> array(
					
					'app' 	=> array(
						'type' 		=> 'html',
						'html' 		=> apply_filters('kingdom_widgets_admin_panel', '')
					)
				)
			)
		)
	)
);