/*
Document   :  Report
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
wwcAmzAffReport = (function ($) {
    "use strict";

    // public
    var debug_level = 0;
    var maincontainer = null;
    var loading = null;
    var loaded_page = 0;
    
    var lang = null;
    
    var mainsync = null;
    var synctable = null;
    var $form_settings = null;
    var module = 'report';
    
    var load_is_running = false; // load rows is already running;

    
    // init function, autoload
    (function init() {
        // load the triggers
        $(document).ready(function(){
            
            maincontainer = $("#wwcAmzAff-wrapper");
            loading = maincontainer.find("#wwcAmzAff-main-loading");

            lang = maincontainer.find('#wwcAmzAff-lang-translation').html();
            //lang = JSON.stringify(lang);
            lang = JSON && JSON.parse(lang) || $.parseJSON(lang);

            mainsync = maincontainer.find("#wwcAmzAff-sync-log");
            synctable = mainsync.find('.wwcAmzAff-sync-table');
            $form_settings = mainsync.find('form#wwcAmzAff-sync-settings');
            module = mainsync.data('module');
            
            triggers();
            
            jQuery('i, a').tipsy({live: true, gravity: 'w'});
        });
    })();
    
    // Load list
    function loadRows( callback ) {
        var data = [];

        // already loading...
        if ( load_is_running ) {
            if ( typeof callback != 'undefined' && $.isFunction(callback) ) {
                callback();
            }
            return false;
        }
        load_is_running = true;

        loading.show();

        data.push({name: 'action', value: 'wwcAmzAff_report'});
        data.push({name: 'subaction', value: 'load_logs'});
        data.push({name: 'module', value: module});
        data.push({name: 'debug_level', value: debug_level});
        
        data = $.param( data ); // turn the result into a query string
        
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {

            if( response.status == 'valid' ){
                synctable.find('> table > tbody').html( response.html );
                mainsync.find('.wwcAmzAff-sync-filters > span span.count').html( response.nb );
            }
            
            loading.hide();
            
            if ( typeof callback != 'undefined' && $.isFunction(callback) ) {
                callback();
            }
            load_is_running = false;
        }, 'json');
    }
    
    // Sync single product
    function viewLog( row )
    {
        var data = [];
        
        row_loading( row, 'show' );
  
        data.push({name: 'action', value: 'wwcAmzAff_report'});
        data.push({name: 'subaction', value: 'view_log'});
        data.push({name: 'debug_level', value: debug_level});
        
        data.push({name: 'id', value: row.data('id')});
        
        data = $.param( data ); // turn the result into a query string
 
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function(response) {

            if( response.status == 'valid' ){
                $(".wwcAmzAff-report-log").append( response.html );
            }
            
            row_loading( row, 'hide' );
        }, 'json');
    }
    
    function row_loading( row, status, pms )
    {
        var pms = pms || {};

        if( status == 'show' ){
            if( row.size() > 0 ){
                if( row.find('.wwcAmzAff-row-loading-marker').size() == 0 ){
                    var row_loading_box = $('<div class="wwcAmzAff-row-loading-marker"><div class="wwcAmzAff-row-loading"><div class="wwcAmzAff-meter wwcAmzAff-animate" style="width:30%; margin: 2px 0px 0px 30%;"><span style="width:100%"></span></div></div></div>')
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
        maincontainer.on("click", 'a#wwcAmzAff-close-btn', function(e){
            e.preventDefault();
            var that = $(this)
            
            $(".wwcAmzAff-report-log-lightbox").remove();
        });

        // load rows
        maincontainer.on('click', '.wwcAmzAff-sync-filters span.right button.load_rows', function(e){
            e.preventDefault();

            loadRows();
        });
        loadRows(); // default page load
        
        // view log
        synctable.on('click', 'td.wwcAmzAff-sync-now button', function(e){
            e.preventDefault();
 
            var that    = $(this),
                row     = that.parents("tr").eq(0);
     
            viewLog( row );
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
        },
        
        format: function() {
            // The string containing the format items (e.g. "{field}")
            // will and always has to be the first argument.
            var args = arguments,
                str = args[0];
 
            return str.replace(/{(\d+)}/g, function(match, number) {
                return typeof args[number] !== 'undefined' ? args[number] : match;
            });
        },
        
        is_browser: function() {
            if(/chrom(e|ium)/.test(navigator.userAgent.toLowerCase())){
                return 'chrome';
            }
            return 'default';
        },
        
        // preserve = choose yes if you want to preserve html tags
        decodeEntities: (function() {
            var preserve = false;
   
            // create a new html document (doesn't execute script tags in child elements)
            // this also prevents any overhead from creating the object each time
            var doc = document.implementation.createHTMLDocument("");
            var element = doc.createElement('div');
                
            // regular expression matching HTML entities
            var entity = /&(?:#x[a-f0-9]+|#[0-9]+|[a-z0-9]+);?/ig;
        
            function getText(str) {
                if ( preserve ) {
                    // find and replace all the html entities
                    str = str.replace(entity, function(m) {
                        element.innerHTML = m;
                        return element.textContent;
                    });
                } else {
                    element.innerHTML = str;
                    str = element.textContent;
                }
                element.textContent = ''; // reset the value
                return str;
            }
        
            function decodeHTMLEntities(str, _preserve) {
                preserve = _preserve || false;
                if (str && typeof str === 'string') {

                    str = getText(str);
                    if ( preserve ) {
                        return str;
                    } else {
                        // called twice because initially text might be encoded like this: &lt;img src=fake onerror=&quot;prompt(1)&quot;&gt;
                        return getText(str);
                    }
                }
            }
            return decodeHTMLEntities;
        })(),
        decodeEntities2: function(str, preserve) {
            var preserve = preserve || false;

            if ( preserve )
                return $("<textarea/>").html(str).text();
            else
                return $("<div/>").html(str).text();
        }
    
    };

    // external usage
    return {
    }
})(jQuery);
