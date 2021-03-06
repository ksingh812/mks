<?php

global $wwcAmzAff;

echo json_encode(array(
    $tryed_module['db_alias'] => array(
        
        /* define the form_sizes  box */
        'sync_options' => array(
            'title' => 'Synchronization log Settings',
            'icon' => '{plugin_folder_uri}assets/amazon.png',
            'size' => 'grid_4', // grid_1|grid_2|grid_3|grid_4
            'header' => true, // true|false
            'toggler' => false, // true|false
            'buttons' => true, // true|false
            'style' => 'panel', // panel|panel-widget
            
            // create the box elements array
            'elements' => array(

                'interface_max_products' => array(
                    'type' => 'text',
                    'std' => '50',
                    'size' => 'large',
                    'force_width' => '100',
                    'title' => 'Products per page',
                    'desc' => 'Number of products per page to show for pagination in the interface: all = all products are displayed; >0 = number of products to be displayed'
                    //'desc' => 'Maximum number of products to show in the interface (usefull when you have too many products and the interface breaks): all = all products are displayed; 0 = no products is displayed; >0 = number of products to be displayed'
                ),
                
            )
        )
    )
));