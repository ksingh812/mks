<?php

function __wwcAmzAff_IM_options_cache( $action='default', $istab = '', $is_subtab='' ) {
    global $wwcAmzAff;
    
    $req['action'] = $action;

    //$notifyStatus = get_option('psp_Minify');
    if ( $req['action'] == 'getStatus' ) {
        //if ( $notifyStatus === false || !isset($notifyStatus["cache"]) ) {
            return '';
        //}
        //return $notifyStatus["cache"]["msg_html"];
    }

    $html = array();
    
    ob_start();
?>
<div class="wwcAmzAff-form-row wwcAmzAff-im-cache <?php echo ($istab!='' ? ' '.$istab : ''); ?><?php echo ($is_subtab!='' ? ' '.$is_subtab : ''); ?>">

    <label><?php _e('Cache', 'psp'); ?></label>
    <div class="wwcAmzAff-form-item large">
    <span class="formNote">&nbsp;</span>

    <input type="button" class="wwcAmzAff-button blue" style="width: 160px;" id="wwcAmzAff-im-cache-delete" value="<?php _e('Clear cache', 'psp'); ?>"><br />
    <span style="margin:0px 0px 0px 10px" class="response"><?php echo __wwcAmzAff_IM_options_cache( 'getStatus' ); ?></span>

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
                'action'        : 'wwcAmzAff_import_cache',
                'sub_action'    : 'getStatus'
            }, function(response) {

                var $box = $('.wwcAmzAff-im-cache'), $res = $box.find('.response');
                $res.html( response.msg_html );
                if ( response.status == 'valid' )
                    return true;
                return false;
            }, 'json');
        });

        $("body").on("click", "#wwcAmzAff-im-cache-delete", function(){

            $.post(ajaxurl, {
                'action'        : 'wwcAmzAff_import_cache',
                'sub_action'    : 'cache_delete'
            }, function(response) {

                var $box = $('.wwcAmzAff-im-cache'), $res = $box.find('.response');
                $res.html( response.msg_html );
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

global $wwcAmzAff;
echo json_encode(array(
    $tryed_module['db_alias'] => array(
        
        /* define the form_sizes  box */
        'insane_import' => array(
            'title' => 'Insane Import',
            'icon' => '{plugin_folder_uri}assets/32_icon.png',
            'size' => 'grid_4', // grid_1|grid_2|grid_3|grid_4
            'header' => true, // true|false
            'toggler' => false, // true|false
            'buttons' => true, // true|false
            'style' => 'panel', // panel|panel-widget
            
            // create the box elements array
            'elements' => array(

				'__cache' => array(
					'type' => 'html',
					'html' => __wwcAmzAff_IM_options_cache( 'default', '__tab1', '' )
				),
               
            )
        )
    )
));