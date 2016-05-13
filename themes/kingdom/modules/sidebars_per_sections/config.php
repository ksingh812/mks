<?php
/**
 * Config file, return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
$kingdom = $GLOBALS['kingdom'];
 echo json_encode(
	array(
		'sidebars_per_sections' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 4,
				'title' => 'Sidebars per sections'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32_advsearch.png',
				'url'	=> admin_url("admin.php?page=kingdom#!/sidebars_per_sections")
			),
			'description' => "Choose what sidebars to have on each section",
			'module_init' => 'init.php'
		)
	)
 );