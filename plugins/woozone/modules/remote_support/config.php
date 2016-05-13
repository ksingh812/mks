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
		'remote_support' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 4,
				'show_in_menu' => false,
				'title' => 'Remote Support',
				'icon' => 'assets/16_remotesupport.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32_remotesupport.png',
				'url'	=> admin_url("admin.php?page=wwcAmzAff_remote_support")
			),
			'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/woocommerce-amazon-affiliates/documentation/remote-support/'
			),
			'description' => 'Using the remote support module you can give secured access to your wordpress install directly to AA-Team support.',
			'module_init' => 'init.php',
			'load_in' => array(
				'backend' => array(
					'admin.php?page=wwcAmzAff_remote_support',
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