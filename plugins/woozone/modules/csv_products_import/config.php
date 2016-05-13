<?php
/**
 * Config file, return as json_encode
 * http://www.aa-team.com
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		1.0
 */
 global $wwcAmzAff;
 echo json_encode(
	array(
		'csv_products_import' => array(
			'version' => '1.0',
			'menu' => array(
				'order' => 4,
				'title' => 'CSV Bulk Products Import',
				'icon' => 'assets/16_csv.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32_csvimport.png',
				'url'	=> admin_url("admin.php?page=wwcAmzAff#!/csv_products_import")
			),
			'help' => array(
				'type' => 'remote',
				'url' => 'http://docs.aa-team.com/woocommerce-amazon-affiliates/documentation/csv-bulk-import/'
			),
			//'module_init' => 'ajax-request.php'
			'description' => "With this module you can import hundred of products using ASIN lists in no time!",
			'load_in' => array(
				'backend' => array(
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
			),
			'errors' => array(
				1 => __('
					You configured CSV Bulk Import incorrectly. See 
					' . ( $wwcAmzAff->convert_to_button ( array(
						'color' => 'white_blue wwcAmzAff-show-docs-shortcut',
						'url' => 'javascript: void(0)',
						'title' => 'here'
					) ) ) . ' for more details on fixing it. <br />
					Setup the Amazon config mandatory settings ( Access Key ID, Secret Access Key, Main Affiliate ID ) 
					' . ( $wwcAmzAff->convert_to_button ( array(
						'color' => 'white_blue',
						'url' => admin_url( 'admin.php?page=wwcAmzAff#!/amazon' ),
						'title' => 'here'
					) ) ) . '
					', $wwcAmzAff->localizationName),
				2 => __('
					You don\'t have WooCommerce installed/activated! Please activate it:
					' . ( $wwcAmzAff->convert_to_button ( array(
						'color' => 'white_blue',
						'url' => admin_url('plugin-install.php?tab=search&s=woocommerce&plugin-search-input=Search+Plugins'),
						'title' => 'NOW'
					) ) ) . '
					', $wwcAmzAff->localizationName),
				3 => __('
					You don\'t have the SOAP library installed! Please activate it!
					', $wwcAmzAff->localizationName),
				4 => __('
					You don\'t have the cURL library installed! Please activate it!
					', $wwcAmzAff->localizationName)
			)
		)
	)
 );