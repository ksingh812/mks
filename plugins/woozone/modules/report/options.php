<?php

global $wwcAmzAff;

function __wwcAmzAff_report_recurrency() {
    global $wwcAmzAff;
    $recurrency = array(
        12      => __('Every 12 hours', $wwcAmzAff->localizationName),
        24      => __('Every single day', $wwcAmzAff->localizationName),
        48      => __('Every 2 days', $wwcAmzAff->localizationName),
        72      => __('Every 3 days', $wwcAmzAff->localizationName),
        96      => __('Every 4 days', $wwcAmzAff->localizationName),
        120     => __('Every 5 days', $wwcAmzAff->localizationName),
        144     => __('Every 6 days', $wwcAmzAff->localizationName),
        168     => __('Every 1 week', $wwcAmzAff->localizationName),
        336     => __('Every 2 weeks', $wwcAmzAff->localizationName),
        504     => __('Every 3 weeks', $wwcAmzAff->localizationName),
        720     => __('Every 1 month', $wwcAmzAff->localizationName), // ~ 4 weeks + 2 days
    );
    return $recurrency;
}

function __wwcAmzAff_report_recurrency_html( $action='default', $istab = '', $is_subtab='' ) {
    global $wwcAmzAff;
    
    $req['action'] = $action;

    $notifyStatus = get_option('wwcAmzAff_report_act', array());
    $recurrency_list = __wwcAmzAff_report_recurrency();

    if ( $req['action'] == 'getStatus' ) {
        if ( $notifyStatus === false || !isset($notifyStatus["report"]) ) {
            return '';
        }
        return $notifyStatus["report"]["msg_html"];
    }

    $html = array();
    
    $vals = array('recurrency' => '24');
    foreach ( $vals as $key => $val ) {
        if ( isset($notifyStatus["$key"]) && !empty($notifyStatus["$key"]) ) {
            $vals["$key"] = $notifyStatus["$key"]; // get from db
        }
    }
    
    ob_start();
?>
<div class="wwcAmzAff-form-row wwcAmzAff-report-container <?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">

    <label><?php _e('Report', 'wwcAmzAff'); ?></label>
    <div class="wwcAmzAff-form-item large">
    <span class="formNote"><?php _e('report sending recurrency', 'wwcAmzAff'); ?></span>

    <span><?php _e('Recurrency:', 'wwcAmzAff'); ?></span>&nbsp;
    <select id="recurrency" name="recurrency" style="width: 180px;">
        <?php
            foreach ($recurrency_list as $kk => $vv){
                $vv = (string) $vv;
                echo '<option value="' . ( $kk ) . '" ' . ( $vals["recurrency"] == $kk ? 'selected="true"' : '' ) . '>' . ( $vv ) . '</option>';
            } 
        ?>
    </select>&nbsp;&nbsp;
    
    <input type="button" class="wwcAmzAff-button blue" style="width: 160px;" id="wwcAmzAff-report-now" value="<?php _e('Send Report NOW', 'wwcAmzAff'); ?>">
    <img id="ajaxLoading" src="<?php echo $wwcAmzAff->cfg['modules']['report']['folder_uri']; ?>/assets/ajax-loader.gif" width="16" height="11" style="display:none; width:auto;"/>
    <span style="margin:0px 0px 0px 10px" class="response"><?php echo __wwcAmzAff_report_recurrency_html( 'getStatus' ); ?></span>

    </div>
</div>
<?php
    $htmlRow = ob_get_contents();
    ob_end_clean();
    $html[] = $htmlRow;
    
    // view page button
    ob_start();
?>
    <script>
    (function($) {
        var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';
        
        $(document).ready(function() {
            $.post(ajaxurl, {
                'action'        : 'wwcAmzAff_report_settings',
                'subaction'    : 'getStatus'
            }, function(response) {

                var $box = $('.wwcAmzAff-report-container'), $res = $box.find('.response');
                $res.html( response.html );
                if ( response.status == 'valid' )
                    return true;
                return false;
            }, 'json');
        });

        $("body").on("click", "#wwcAmzAff-report-now", function(){
			$(this).hide();
			$('#ajaxLoading').show();
			
            $.post(ajaxurl, {
                'action'        : 'wwcAmzAff_report_settings',
                'subaction'    : 'send_report'
            }, function(response) {
				$('#ajaxLoading').hide();
				$('#wwcAmzAff-report-now').show();
				
                var $box = $('.wwcAmzAff-report-container'), $res = $box.find('.response');
                $res.html( response.html );
                if ( response.status == 'valid' )
                    return true;
                return false;
            }, 'json');
        });
    })(jQuery);
    </script>
<?php
    $__js = ob_get_contents();
    ob_end_clean();
    $html[] = $__js;

    return implode( "\n", $html );
}

echo json_encode(array(
    $tryed_module['db_alias'] => array(
        
        /* define the form_sizes  box */
        'report' => array(
            'title' => 'Woozone Report',
            'icon' => '{plugin_folder_uri}assets/amazon.png',
            'size' => 'grid_4', // grid_1|grid_2|grid_3|grid_4
            'header' => true, // true|false
            'toggler' => false, // true|false
            'buttons' => true, // true|false
            'style' => 'panel', // panel|panel-widget
            
            // create the box elements array
            'elements' => array(

                /*'recurrency'   => array(
                    'type'      => 'select',
                    'std'       => 90,
                    'size'      => 'large',
                    'title'     => 'Recurrency',
                    'force_width'=> '200',
                    'desc'      => 'report sending recurrency',
                    'options'   => __wwcAmzAff_report_recurrency() //$wwcAmzAff->doRange( range(10, 100, 5) )
                ),*/
                
                '__report' => array(
                    'type' => 'html',
                    'html' => __wwcAmzAff_report_recurrency_html( 'default', '__tab1', '' )
                ),

                'email_to' => array(
                    'type' => 'text',
                    'std' => '',
                    'size' => 'large',
                    'force_width' => '300',
                    'title' => 'Email To',
                    'desc' => 'email to address'
                ),
                
                'email_subject' => array(
                    'type' => 'text',
                    'std' => 'WooZone Report',
                    'size' => 'large',
                    'force_width' => '500',
                    'title' => 'Email Subject',
                    'desc' => 'email subject'
                ),
                
            )
        )
    )
));