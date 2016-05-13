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
		'auto_import' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 4,
				'show_in_menu' => false,
				'title' => 'Auto Import Products',
				'icon' => 'assets/16_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32_icon.png',
				'url'	=> 'admin.php?page=wwcAmzAff_auto_import' //admin_url("admin.php?page=wwcAmzAff#!/auto_import")
			),
			'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/woocommerce-amazon-affiliates/documentation/amazon-asin-grabber/'
			),
			'description' => "With this module you can schedule automatic products fetch on certain intervals",
			'module_init' => 'init.php',
			'load_in' => array(
				'backend' => array(
					'admin.php?page=wwcAmzAff_auto_import',
					'admin.php?page=wwcAmzAff_insane_import',
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
			),
            'errors' => array()
		)
	)
);