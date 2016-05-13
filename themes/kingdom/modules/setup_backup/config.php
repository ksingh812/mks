<?php
/**
 * Config file, return as json_encode
 * http://www.aa-team.name
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		0.1 - in development mode
 */
$kingdom = $GLOBALS['kingdom'];
 echo json_encode(
	array(
		'setup_backup' => array(
			'version' => '0.1',
			'menu' => array(
				'order' => 20,
				'title' => 'Setup / Backup',
				'icon' => 'assets/menu_icon.png'
			),
			'description' => "Core Modules, can't be deactivate!",
		)
	)
 );