<?php
/**
 * Config file, return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0 - in development mode
 */
$kingdom = $GLOBALS['kingdom'];
 echo json_encode(
	array(
		'widgets_manager' => array(
			'version' => '1.0',
			'hide_from_menu' => true,
			'parent_menu'	=> 'layout',
			'menu' => array(
				'title' => 'Widgets manager',
				'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32_advsearch.png',
				'url'	=> admin_url("admin.php?page=kingdom#!/widgets_manager")
			),
			'description' => "You can manage your shop widgets from this section.",
		)
	)
 );