<?php
/**
 * Config file, return as json_encode
 * http://www.aa-team.com
 * ======================
 *
 * @author		AA-Team
 * @version		1.0
 */
$kingdom = $GLOBALS['kingdom']; 
echo json_encode(
	array(
		'color_filter' => array(
			'version' => '1.0',
			'title' => __('Color filter', 'kingdom'),
			'description' => __("Here you can filter your products based on color", 'kingdom'),
			'options' => array(
				'title' 	=> array(
					'title'		=> __('Title', 'kingdom'),
					'type' 		=> 'text',
					'width'		=> '100%',
					'std' 		=> __('Color filter title', 'kingdom')	
				)
			)
		)
	)
);