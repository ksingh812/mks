<?php
/**
 * Dummy module return as json_encode
 * http://www.aa-team.name
 * =======================
 *
 * @author		Andrei Dinca, AA-Team
 * @version		0.1 - in development mode
 */
echo json_encode(array(
	$tryed_module['db_alias'] => array(
		/* define the form_messages box */
		'html_box1' => array(
			'title' => 'Support',
			'icon' => '{plugin_folder_uri}assets/16_icon.png',
			'size' => 'grid_2', // grid_1|grid_2|grid_3|grid_4
			'header' => false, // true|false
			'toggler' => false, // true|false
			'buttons' => false, // true|false
			'style' => 'panel-widget', // panel|panel-widget

			// create the box elements array

			'elements' => array(
				array(
					'type' => 'html',
					'html' => '<h1>Need help?</h1>
						<img src="{theme_folder_uri}assets/support.png"/>
						<p> If you encounter any problems please open a ticket on our support center : <a href="http://support.aa-team.com">support.aa-team.com</a>',
				)
			)
		)
	)
));