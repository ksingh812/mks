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
		'partners' => array(
			'version' => '1.0',
			'hide_from_menu' => true,
			'menu' => array(
				'order' => 16,
				'title' => __('Partners', $kingdom->localizationName),
				'icon' => 'assets/16_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32_advsearch.png',
				'url'	=> admin_url("edit.php?post_type=partners")
			),
			'description' => "Partners Carousel from Footer Area.",
			'module_init' => 'init.php'
		)
	)
 );