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
		'layout' => array(
			'version' => '1.0',
			'hide_from_menu' => false,
			'menu' => array(
				'order' => 2,
				'title' => __('Layout', 'kingdom'),
				'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32_advsearch.png',
				'url'	=> admin_url("admin.php?page=kingdom#!/layout")
			),
			'description' => "Customize your shop like you want!",
			'module_init' => 'init.php'
		)
	)
 );