<?php 
echo json_encode(
	array(
		'slideshow' => array(
			'_slideshow_meta' => array(
				'slideshow_image'
			)
		),
		
		'testimonials' => array(
			'_testimonials_meta' => array(
				'before_image',
				'after_image',
				'after_image_homepage'
			)
		),
		
		'partners' => array( '_partner_image' ),
	)
);