<?php
/**
 * Module return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
echo json_encode(
	array(
		$tryed_module['db_alias'] => array(
		
			/* define the form_sizes  box */
			'sidebars_per_sections' => array(
				'title' 	=> 'Choose sidebars per sections',
				'icon' 		=> '{theme_folder_uri}assets/menu_icon.png',
				'size' 		=> 'grid_4', // grid_1|grid_2|grid_3|grid_4
				'header' 	=> false, // true|false
				'toggler' 	=> false, // true|false
				'buttons' 	=> false, // true|false
				'style' 	=> 'panel', // panel|panel-widget
				
				// create the box elements array
				'elements'	=> array(
					
					'app' 	=> array(
						'type' 		=> 'html',
						'html' 		=> apply_filters('kingdom_sidebars_sections_admin_panel', '')
					)
				)
			)
		)
	)
);