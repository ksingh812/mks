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
		'bulk_products_colors_check' => array(
			'version' => '0.1',
			'menu' => array(
				'order' => 4,
				'title' => 'Bulk Products Color Checker',
				'icon' => 'assets/menu_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32_advsearch.png',
				'url'	=> admin_url("admin.php?page=kingdom#!/bulk_products_colors_check")
			),
			'desciption' => "With this feature you can check the main colors for all your products images.",
			'module_init' => 'ajax-request.php'
		)
	)
 );