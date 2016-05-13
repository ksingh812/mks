/*
Document   :  Auto Import
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
wwcAmzAffAutoImport = (function($) {
	"use strict";

	// public
    var debug_level                     = 0,
        maincontainer                   = null,
        loading                         = null,
        lang                            = null;

	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function() {

			maincontainer = $("#wwcAmzAff-insane-import");
			loading = maincontainer.find("#wwcAmzAff-main-loading");
			
            // language messages
            lang = maincontainer.find('#wwcAmzAff-lang-translation').html();
            //lang = JSON.stringify(lang);
            lang = typeof lang != 'undefined'
                ? JSON && JSON.parse(lang) || $.parseJSON(lang) : lang;
                
            //triggers();
		});
	})();
	

    // :: LOADING
    function row_loading( row, status, extra )
    {
        var extra = extra || {};
        var isextra = ( typeof extra != 'undefined' && misc.size(extra) == 1 ? true : false );
  
        if( status == 'show' ){
            if( row.size() > 0 ){
                if( row.find('> .wwcAmzAff-row-loading-marker').size() == 0 ){
                    //<div class="wwcAmzAff-loading-text">Loading</div><div class="wwcAmzAff-meter wwcAmzAff-animate" style="width:30%; margin: 10px 0px 0px 30%;"><span style="width:100%"></span></div>
                    var html = '<div class="wwcAmzAff-meter wwcAmzAff-animate" style="width:30%; margin: 15% auto 10px;"><span style="width:100%"></span></div><div class="wwcAmzAff-loading-text">' + lang.loading + '</div>';
                    //if ( isextra ) {
                    //    html = html + extra.html;
                    //}
                    var row_loading_box = $('<div class="wwcAmzAff-row-loading-marker"><div class="wwcAmzAff-row-loading">' + html + '</div></div>');
                    row_loading_box.find('div.wwcAmzAff-row-loading').css({
                        'width'     : parseInt( row.outerWidth() ),
                        'height'    : parseInt( row.outerHeight() + 40 ),
                        'top'       : '10px' //'-40px'
                    });
                    row.prepend(row_loading_box);
                }
                if ( isextra && $.trim( extra.html ) != '' ) {
                    row.find('> .wwcAmzAff-row-loading-marker')
                    .find('div.wwcAmzAff-row-loading')
                    .find('div.wwcAmzAff-loading-text')
                    .html( extra.html );
                }
                row.find('> .wwcAmzAff-row-loading-marker').find('div.wwcAmzAff-row-loading').css({
                    'width'     : parseInt( row.outerWidth() ),
                    'height'    : parseInt( row.outerHeight() + 40 ),
                    'top'       : '10px' //'-40px'
                });
                //text loading!
                row.find('> .wwcAmzAff-row-loading-marker').find('div.wwcAmzAff-loading-text').css({
                    'height'    : parseInt( row.outerHeight() - 10 )
                });
                row.find('> .wwcAmzAff-row-loading-marker').fadeIn('fast');
            }
        } else {
            row.find('> .wwcAmzAff-row-loading-marker').fadeOut('slow');
        }
    }
    
    
    // :: MESSAGES
    function set_status_msg_generic( status, msg, op, from ) {
        var from        = from || '';
    };
    
	
    // :: Insane Import Page
    var insane = (function() {
        
        var DEBUG                   = false,
            TEST                    = 0;
        var debug_level             = 0,
            maincontainer           = $("#wwcAmzAff-insane-import"),
            mainloading             = maincontainer.find("#wwcAmzAff-main-loading"),
            lightbox                = null;

        // Test!
        function __() {};
        
        // get public vars
        function get_vars() {
            return $.extend( {}, {} );
        };
        
        // init function, autoload
        (function init() {
            // load the triggers
            $(document).ready(function() {
    
                // add lightbox container
                $("#wwcAmzAff-wrapper").prepend( $('<div class="wwcAmzAff-big-overlay-lightbox"/>') );
                lightbox = $('.wwcAmzAff-big-overlay-lightbox');

                triggers();
            });
        })();
        
        // Triggers
        function triggers() {
            var box         = maincontainer.find('#wwcAmzAff-content-scroll'),
                box_import  = maincontainer.find('#wwcAmzAff-insane-import-parameters');

            // checkboxes with readonly attribute
            lightbox.on("click", 'input[type="checkbox"][readonly]', function(e){
                e.preventDefault();
                //$(this).prop('checked', true);
            });
            //lightbox.find('input[type="checkbox"][readonly]').css("opacity", "0.5");
            
            // checkboxes remove readonly attribute (become editable again)
            //lightbox.find('input[type="checkbox"]').off('.readonly').removeAttr("readonly").css("opacity", "1");

            // add search to schedule box
            box.on('click', 'form#wwcAmzAff-search-products .wwcAmzAff-add-to-schedule', function(e) {
                e.preventDefault();

                var form = $(this).parents('form');
                get_search_params( { 'box' : box, 'form' : form, 'box_import' : box_import } );
            });
            
            // close lightbox
            lightbox.on("click", 'a#wwcAmzAff-close-btn', function(e){
                e.preventDefault();
                var that = $(this);
                
                boxstatus( 'close' );
            });
 
            // save search to schedule table
            lightbox.on('click', 'form#wwcAmzAff-search-add-schedule input[type="submit"]', function(e) {
                e.preventDefault();

                var form = $(this).parents('form');
                save_search_params( { 'form' : form } );
            });
        };
        
        // get search parameters
        function get_search_params( pms ) {
            boxstatus( 'show' );
            //loading( 'show', lang.loading );
            
            var pms             = typeof pms == 'object' ? pms : {},
                box             = misc.hasOwnProperty(pms, 'box') ? pms.box
                    : maincontainer.find('#wwcAmzAff-content-scroll'),
                form            = misc.hasOwnProperty(pms, 'form') ? pms.form
                    : box.find('form#wwcAmzAff-search-products'),
                box_import      = misc.hasOwnProperty(pms, 'box_import') ? pms.box_import
                    : maincontainer.find('#wwcAmzAff-insane-import-parameters');

            // Search Parameters
            /*
            var nodename        = null, 
                nodeid          = null;
  
            var data            = [],
                form_params     = form.serializeArray();

            // get last BrowseNode value
            if ( $.isArray(form_params) ) {
                for (var i = 0, len = form_params.length; i < len; i++) {
                    var obj = form_params[i];
                    if ( typeof(obj) != 'undefined' 
                        && misc.hasOwnProperty(obj, 'name') && misc.hasOwnProperty(obj, 'value') ) {

                        if ( obj.name.search(/BrowseNode/gi) > 0 ) {
                            if ( obj.value != '' ) {
                                nodename = obj.name;
                                nodeid   = obj.value;
                            }
                            form_params.splice(i, 1);
                            --i;
                        }
                    }
                }
                if ( nodeid ) {
                    form_params.push(
                        {name: nodename, value: nodeid}
                    );
                }
            }
            */
           
            var data            = [],
                form_params     = [];

            data.push(
                {name: 'debug_level',       value: debug_level},
                {name: 'action',            value: 'wwcAmzAff_AutoImportAjax'},
                {name: 'sub_action',        value: 'search_get_params'}
            );

            //loop through wwcAmzAff-search: input, select
            var browsenode       = [],
                browsenode_list  = [],
                browsenode_cc    = 0;

            form.find('input[name^="wwcAmzAff-search"], select[name^="wwcAmzAff-search"]').each(function (i) {
                var $this       = $(this),
                    type        = $this.prop('type'), //$this.prop('tagName').toLowerCase()
                    name        = $this.prop('name'),
                    _name       = name.replace('wwcAmzAff-search[', '').replace(']', ''),
                    value       = $this.val();
                    
                var add         = true;
                if ( 'select-one' == type ) {
                    var opt_sel = $this.find('option:selected'),
                        text    = $.trim( opt_sel.text() );

                    if ( 'category' == _name ) {
                        var nodeid  = opt_sel.data('nodeid');

                        form_params.push( { 'name': 'wwcAmzAff-search[category_id]', 'value': nodeid } );
                    }
                    else if ( 'BrowseNode' == _name ) {
                        if ( value != '' ) {
                            browsenode[0] = { 'name': 'wwcAmzAff-search['+_name+']', 'value': value };
                            browsenode[1] = { 'name': 'wwcAmzAff-search[_'+_name+']', 'value': text };
                            
                            browsenode_list[browsenode_cc] = [];
                            browsenode_list[browsenode_cc][0] = { 'name': 'wwcAmzAff-search['+_name+'_list]', 'value': value };
                            browsenode_list[browsenode_cc][1] = { 'name': 'wwcAmzAff-search[_'+_name+'_list]', 'value': text };
                            browsenode_cc++;
                        }
                        add = false; // insertion is made outside this loop
                    }
                    
                    if ( add ) {
                        form_params.push( { 'name': 'wwcAmzAff-search[_'+_name+']', 'value': text } );
                    }
                }

                if ( add ) {
                    form_params.push( { 'name': name, 'value': value } );
                }
            });
  
            // BrowseNode
            if (browsenode.length > 0) {
                for (var ii in [0, 1]) {
                    form_params.push( { 'name': browsenode[ii].name, 'value': browsenode[ii].value } );
                }
                
                for (var ii in browsenode_list) {
                    for (var ii2 in [0, 1]) {
                        form_params.push( {
                            'name'      : browsenode_list[ii][ii2].name+'['+ii+']',
                            'value'     : browsenode_list[ii][ii2].value
                        });
                    }
                }
            }

            form_params = $.param( form_params ); // turn the result into a query string
            data.push(
                {name: 'params', value: form_params}
            );
            
            // Import Parameters
            var import_params = get_parameters_import( { 'box' : box_import } );
            import_params = $.param( import_params ); // turn the result into a query string
            data.push(
                {name: 'import_params', value: import_params}
            );
            
            data = $.param( data ); // turn the result into a query string
            //console.log( data ); return false;

            $.post(ajaxurl, data, function(response) {
                if (1) {
                    //set_status_msg( response.status, response.msg, 'search' );

                    loading( 'close' );
                    if ( misc.hasOwnProperty(response, 'html') ) {
                        boxstatus( 'add_content', { 'html' : response.html } );
                    }
                }

            }, 'json')
            .fail(function() {})
            .done(function() {})
            .always(function() {});
        }
        
        // get import parameters
        function get_parameters_import( pms ) {
            var pms          = typeof pms == 'object' ? pms : {},
                box          = misc.hasOwnProperty(pms, 'box') ? pms.box : null,
                params       = misc.hasOwnProperty(pms, 'params') ? pms.params : [];
            
            // use cached params
            if ( $.isArray(params) && params.length > 0 ) {
                //import_params = params;
                return params;
            }
 
            //import-parameters[import_type]: input, output
            box.find('input[name^="import-parameters"]').each(function (i) {
                var $this   = $(this),
                    type    = $this.prop('type'),
                    name    = $this.prop('name').replace('import-parameters[', '').replace(']', ''),
                    value   = $this.val(),
                    param   = {};

                var add = true;
                if ( type == 'radio' || type == 'checkbox' ) {
                    if ( !$this.prop('checked') ) add = false;
                } else if ( type == 'range' ) {
                    if ( value >= 100 ) value = 'all';
                }

                param = { 'name': name, 'value': value };
                if ( add ) {
                    params.push( param );
                }
            });

            // import in
            params.push( { 'name': 'to-category', 'value': box.find('select#wwcAmzAff-to-category').val() } );
            var __ = box.find('select#wwcAmzAff-to-category option:selected').text();
            __ = $.trim( __ );
            params.push( { 'name': '_to-category', 'value': __ } );

            //console.log( params );
            //import_params = params;
            return params;
        }
        
        // save search parameters
        function save_search_params( pms ) {
            loading( 'show', lang.loading );
            
            var pms             = typeof pms == 'object' ? pms : {},
                form            = misc.hasOwnProperty(pms, 'form') ? pms.form
                    : lightbox.find('form#wwcAmzAff-search-add-schedule');
                    
            var data            = [],
                form_params     = form.serializeArray();
                
            data.push(
                {name: 'debug_level',       value: debug_level},
                {name: 'action',            value: 'wwcAmzAff_AutoImportAjax'},
                {name: 'sub_action',        value: 'search_save_params'}
            );
            
            form_params = $.param( form_params ); // turn the result into a query string
            data.push(
                {name: 'allparams', value: form_params}
            );
            
            data = $.param( data ); // turn the result into a query string
            //console.log( data ); return false;

            $.post(ajaxurl, data, function(response) {
                if (1) {
                    //set_status_msg( response.status, response.msg, 'search' );

                    loading( 'close' );
                    if ( misc.hasOwnProperty(response, 'html') ) {
                        //boxstatus( 'add_content', { 'html' : response.html } );
                        console.log( 'gimi' ); 
                    }
                }

            }, 'json')
            .fail(function() {})
            .done(function() {})
            .always(function() {});
        }
        
        // Loading
        function boxstatus( status, pms ) {
            var status       = status || 'show',
                pms          = typeof pms == 'object' ? pms : {};
            
            if ( 'show' == status ) {
                lightbox.show();
                loading( 'show', lang.loading );
            }
            else if ( 'close' == status ) {
                loading( 'close' );
                lightbox.find('.wwcAmzAff-donwload-in-progress-box').remove();
                lightbox.hide();
            }
            else if ( 'add_content' == status ) {
                var html = misc.hasOwnProperty(pms, 'html') ? pms.html : '';
                //lightbox.html( html );
                lightbox.find('.wwcAmzAff-donwload-in-progress-box').remove();
                lightbox.append( html );
                lightbox.find('input[type="checkbox"][readonly]').css("opacity", "0.5");
            }
        }

        function loading( status, msg, from ) {
            var msg         = msg || '',
                from        = from || '',
                container   = lightbox; //$("#wwcAmzAff-wrapper #wwcAmzAff-content");

            //if (status == 'close') return false; //debug!
            row_loading( container, status, {html: msg} );
        };

        function set_status_msg( status, msg, op ) {
            set_status_msg_generic( status, msg, op );
        };

        // external usage
        return {
            // attributes
            'v'                     : get_vars,
            
            // methods
            '__'                    : __
        };
    })();
    
    
    // :: MISC
    var misc = {

        hasOwnProperty: function(obj, prop) {
            var proto = obj.__proto__ || obj.constructor.prototype;
            return (prop in obj) &&
            (!(prop in proto) || proto[prop] !== obj[prop]);
        },

        arrayHasOwnIndex: function(array, prop) {
            return array.hasOwnProperty(prop) && /^0$|^[1-9]\d*$/.test(prop) && prop <= 4294967294; // 2^32 - 2
        },

        size: function(obj) {
            var size = 0;
            for (var key in obj) {
                if (misc.hasOwnProperty(obj, key)) size++;
            }
            return size;
        }
    }
	
	// external usage
	return {
		//"background_loading": background_loading
	}
})(jQuery);

