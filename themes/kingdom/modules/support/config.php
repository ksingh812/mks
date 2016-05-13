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

		'support' => array(

			'version' => '0.1',

			'menu' => array(

				'order' => 100,

				'title' => 'Contact / Support',

				'icon' => 'assets/menu_icon.png'

			),

			'description' => "Core Modules, can't be deactivated!",

		)

	)

 );