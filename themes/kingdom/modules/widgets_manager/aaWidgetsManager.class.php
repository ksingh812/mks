<?php
/*

* Define class Modules Manager List

* Make sure you skip down to the end of this file, as there are a few

* lines of code that are very important.

*/
!defined('ABSPATH') and exit;
if (class_exists('aaWidgetsManager') != true) {
    class aaWidgetsManager
    {
        /*
        * Some required plugin information
        */
        const VERSION = '1.0';
		
        /*        
        * Store some helpers config
        * 
        */
        public $cfg = array();
		
        /*
        * Required __construct() function that initalizes the AA-Team Framework
        */
        public function __construct($cfg)
        {
            $this->cfg = $cfg;
 
			echo $this->printListInterface();
        }
        public function printListInterface()
        {
        	global $kingdom;
            $html   = array();
            $html[] = '<table class="kingdom-table" id="' . ($this->cfg['default']['alias']) . '-widgets-manager" style="border-collapse: collapse;border-spacing: 0;">';
            $html[] = '<thead>
						<tr>
							<th width="10">Version</th>
							<th width="100">Preview</th>
							<th width="350" align="left">Name</th>
							<th align="left">About</th>
						</tr>
					</thead>';
            $html[] = '<tbody>';
            $cc     = 0;
            foreach ($this->cfg['widgets'] as $key => $value) {
                
				$preview = '';
				if( is_file( $value["folder_path"] . 'screenshot.png' )){
					$preview = $value["folder_uri"] . 'screenshot.png';
				}
                $html[] = '<tr class="' . ($cc % 2 ? 'odd' : 'even') . '">
					<td align="center" style="vertical-align:middle">' . ($value[$key]['version']) . '</td>
					<td align="center">' . ( trim($preview) != "" ? '<a class="image-preview" href="' . ( $value["folder_uri"] . 'screenshot.png' ) . '" target="_blank"><img src=" ' . ( $preview ) . '" width="120" /></a>' : '') . '</td>
					<td style="vertical-align:middle">';
					
                // activate / deactivate widget button
                if ($value['status'] == true) {
                    $html[] = '<a href="#deactivate" class="deactivate" rel="' . ( $key ) . '">Deactivate</a>';
                } else {
                    $html[] = '<a href="#activate" class="activate" rel="' . ( $key ) . '">Activate</a>';
                }
                $html[] = "&nbsp; | &nbsp;" . $value[$key]['title'];
                $html[] = '</td>
					<td style="vertical-align:middle">' . ($value[$key]['description']) . '</td>
				</tr>';
                $cc++;
            }
            $html[] = '</tbody>';
            $html[] = '</table>';
            return implode("\n", $html);
        }
    }
}