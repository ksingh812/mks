/*
Document   :  Cronjobs
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
wwcAmzAffCronjobs = (function($) {
    "use strict";
    
    // public
    var debug_level = 0;
    var maincontainer = null;
    var loading = null;
    var loaded_page = 0;
    
    var mainsync = null;
    var synctable = null;


    // init function, autoload
    (function init() {
        // load the triggers
        $(document).ready(function() {
            
            maincontainer = $("#wwcAmzAff-wrapper");
            loading = maincontainer.find("#wwcAmzAff-main-loading");
            
            mainsync = maincontainer.find("form#wwcAmzAff_cronjobs");
            synctable = mainsync.find('#wwcAmzAff-cj-table');
            
            triggers();
        });
    })();
    
    // reload cronjobs list
    function load_cronjobs( callback ) {
        var data = [];

        loading.show();

        data.push({name: 'action', value: 'wwcAmzAff_cronjobs'});
        data.push({name: 'subaction', value: 'load_cronjobs'});
        data.push({name: 'debug_level', value: debug_level});
        
        data = $.param( data ); // turn the result into a query string
        
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {

            if( response.status == 'valid' ){
                synctable.find('> tbody').html( response.html );
            }
            
            loading.hide();
            
        }, 'json');
    }
    
    // activate/clear action for single cronjob
    function cron_activate( row )
    {
        var data = [];

        //row_loading( row, 'show' );
        loading.show();
        
        data.push({name: 'action', value: 'wwcAmzAff_cronjobs'});
        data.push({name: 'subaction', value: 'cron_activate'});
        data.push({name: 'debug_level', value: debug_level});
        
        data.push({name: 'cron_id', value: row.data('cron_id')});
        data.push({name: 'new_status', value: row.data('new_status')});
        
        data = $.param( data ); // turn the result into a query string
 
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {

            if( response.status == 'valid' ){
                //synctable.find('> tbody').html( response.html );
                
                // it needs an new page access through ajax so the new event schedules become active!
                load_cronjobs(); // default page load
            }
            
            //row_loading( row, 'hide' );
            //loading.hide();
        }, 'json');
    }
    
    function row_loading( row, status, pms )
    {
        var pms = pms || {};

        if( status == 'show' ){
            if( row.size() > 0 ){
                if( row.find('.wwcAmzAff-row-loading-marker').size() == 0 ){
                    var row_loading_box = $('<div class="wwcAmzAff-row-loading-marker"><div class="wwcAmzAff-row-loading"><div class="wwcAmzAff-meter wwcAmzAff-animate" style="width:30%; margin: 8px 0px 0px 30%;"><span style="width:100%"></span></div></div></div>')
                    row_loading_box.find('div.wwcAmzAff-row-loading').css({
                        'width': row.width(),
                        'height': row.height(),
                        'top': '-16px'
                    });

                    row.find('td').eq(0).append(row_loading_box);
                }
                row.find('.wwcAmzAff-row-loading-marker').fadeIn('fast');
            }
        }else{
            row.find('.wwcAmzAff-row-loading-marker').fadeOut('fast');
        }
    }
    
    function triggers()
    {
        // reload cronjobs
        maincontainer.on('click', '#wwcAmzAff-cj-reload input', function(e){
            e.preventDefault();

            load_cronjobs();
        });
        load_cronjobs(); // default page load
        
        // activate/clear action for single cronjob
        synctable.on('click', 'td input.wwcAmzAff-button', function(e){
            e.preventDefault();
 
            var that    = $(this),
                row     = that.parents("tr").eq(0);
     
            cron_activate( row );
        });
    }
    
    var misc = {
    
        hasOwnProperty: function(obj, prop) {
            var proto = obj.__proto__ || obj.constructor.prototype;
            return (prop in obj) &&
            (!(prop in proto) || proto[prop] !== obj[prop]);
        },
    
        size: function(obj) {
            var size = 0;
            for (var key in obj) {
                if (misc.hasOwnProperty(obj, key)) size++;
            }
            return size;
        }
    
    };
                
    // external usage
    return {
    }
})(jQuery);