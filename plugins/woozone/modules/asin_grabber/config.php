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
		'asin_grabber' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 4,
				'show_in_menu' => false,
				'title' => 'ASIN Grabber',
				'icon' => 'assets/16_assets.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32_assetsdwl.png',
				'url'	=> admin_url("admin.php?page=wwcAmzAff_asin_grabber")
			),
			'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/woocommerce-amazon-affiliates/documentation/amazon-asin-grabber/'
			),
			'description' => "With this module you can import hundreds of ASIN codes from Amazon pages like: Best Sellers, Most Wished, etc.",
			'module_init' => 'init.php',
			'load_in' => array(
				'backend' => array(
					'admin.php?page=wwcAmzAff_assets_download',
					'admin.php?page=wwcAmzAff_asin_grabber',
					'admin-ajax.php'
				),
				'frontend' => false
			),
			'javascript' => array(
				'admin',
				'download_asset',
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