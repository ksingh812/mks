<?php
/**
 * Config file, return as json_encode
 * http://www.aa-team.name
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		0.1 - in development mode
 */
 echo json_encode(
	array(
		'modules_manager' => array(
			'version' => '0.1',
			'menu' => array(
				'order' => 29,
				'title' => 'Modules manager',
				'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32_advsearch.png',
				'url'	=> admin_url("admin.php?page=kingdom#!/modules_manager")
			),
			'description' => "Core Modules, can't be deactivated!",
		)
	)
 );