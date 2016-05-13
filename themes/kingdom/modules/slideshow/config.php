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
		'slideshow' => array(
			'version' => '1.0',
			'hide_from_menu' => true,
			'menu' => array(
				'order' => 17,
				'title' => __('Slideshow', 'kingdom'),
				'icon' => 'assets/16_icon.png'
			),
			'in_dashboard' => array(
				'icon' 	=> 'assets/32_advsearch.png',
				'url'	=> admin_url("edit.php?post_type=slideshow")
			),
			'description' => "Slideshow, using this module you can showcase featured content on your homepage and any other pages.",
			'module_init' => 'init.php'
		)
	)
 );