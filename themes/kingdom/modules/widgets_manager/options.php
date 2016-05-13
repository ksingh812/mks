<?php
/**

* Dummy module return as json_encode

* http://www.aa-team.name

* =======================

*

* @author		Andrei Dinca, AA-Team

* @version		0.1 - in development mode

*/
global $kingdom;
echo json_encode(array(
    $tryed_module['db_alias'] => array(
        /* define the form_messages box */
        'module_box' => ($kingdom->get_theme_status() != 'valid_hash' ? array(
            'title' => 'Unlock - ' . $kingdom->alias,
            'icon' => '{theme_folder_uri}assets/validation_icon.png',
            'size' => 'grid_4', // grid_1|grid_2|grid_3|grid_4
            'header' => true, // true|false
            'toggler' => false, // true|false
            'buttons' => false, // true|false
            'style' => 'panel', // panel|panel-widget
            // create the box elements array
            'elements' => array(
                array(
                    'type' => 'message',
                    'status' => 'info',
                    'html' => 'You need to log into your Themeforest account and go to your “Downloads” page. Locate this plugin you purchased in your “Downloads” list and click on the “License Certificate” link next to the download link. After you have downloaded the certificate you can open it in a text editor such as Notepad and copy the Item Purchase Code.'
                ),
                'productKey' => array(
                    'type' => 'text',
                    'std' => '',
                    'size' => 'small',
                    'title' => 'Item Purchase Code',
                    'desc' => 'Get it from CodeCanyon account and go to your “Downloads” page.'
                ),
                'yourEmail' => array(
                    'type' => 'text',
                    'std' => get_option('admin_email'),
                    'size' => 'small',
                    'title' => 'Your Email',
                    'desc' => 'We will notify you via this email about this product update and bug fix.'
                ),
                'sendActions' => array(
                    'type' => 'buttons',
                    'options' => array(
                        array(
                            'action' => 'kingdom_activate_product',
                            'width' => '100px',
                            'type' => 'submit',
                            'color' => 'green',
                            'pos' => 'left',
                            'value' => 'Activate now'
                        )
                    )
                )
            )
        ) : array(
            'title' => 'Widgets Manager',
            'icon' => '{theme_folder_uri}assets/menu_icon.png',
            'size' => 'grid_4', // grid_1|grid_2|grid_3|grid_4
            'header' => true, // true|false
            'toggler' => false, // true|false
            'buttons' => false, // true|false
            'style' => 'panel', // panel|panel-widget
            // create the box elements array
            'elements' => array(
                array(
                    'type' => 'app',
                    'path' => '{plugin_folder_path}lists.php'
                )
            )
        ))
    )
));