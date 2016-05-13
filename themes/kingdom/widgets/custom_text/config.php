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
		'custom_text' => array(
			'version' => '1.0',
			'title' => __('Custom Text Widget', 'kingdom'),
			'description' => __("Here you can add custom text, links or images !", 'kingdom'),
			'options' => array(
				'title' 	=> array(
					'title'		=> __('Title', 'kingdom'),
					'type' 		=> 'text',
					'width'		=> '100%',
					'std' 		=> __('Custom Text Widget', 'kingdom')	
				),
				'text' 	=> array(
					'title'		=> __('HTML Code', 'kingdom'),
					'type' 		=> 'textarea',
					'width'		=> '100%',
					'std' 		=> __('You can add any text or message here.', 'kingdom')	
				)
			)
		)
	)
);