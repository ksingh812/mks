<?php
/**
 * Config file, return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
echo json_encode(
	array(
		'content_spinner' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 4,
				'show_in_menu' => false,
				'title' => 'Amazon Content Spinner',
				'icon' => 'assets/16_spinner.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32_spinner.png',
				'url'	=> admin_url("admin.php?page=wwcAmzAff_content_spinner")
			),
			'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/woocommerce-amazon-affiliates/documentation/content-spinner/'
			),
			'description' => "Excelent On Page Optimization for Amazon Products",
			'module_init' => 'init.php',
			'load_in' => array(
				'backend' => array(
					'admin.php?page=wwcAmzAff_content_spinner',
					'admin-ajax.php'
				),
				'frontend' => false
			),
			'javascript' => array(
				'admin',
				'hashchange',
				'tipsy'
			),
			'css' => array(
				'admin',
				'tipsy'
			)
		)
	)
);