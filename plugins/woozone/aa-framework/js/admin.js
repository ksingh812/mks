/*
    Document   :  wwcAmzAff
    Created on :  2014
    Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
wwcAmzAff = (function ($) {
    "use strict";

	var option = {
		'prefix': "wwcAmzAff"
	};
	
    var t = null,
        ajaxBox = null,
        section = 'dashboard',
        subsection	= '',
        in_loading_section = null,
        topMenu = null;

    function init() 
    {
        $(document).ready(function(){
        	
        	t = $("div.wrapper-wwcAmzAff");
	        ajaxBox = t.find('#wwcAmzAff-ajax-response');
	        topMenu = t.find('#wwcAmzAff-topMenu');
	        
	        if (t.size() > 0 ) {
	            fixLayoutHeight();
	        }
	        
	        // plugin depedencies if default!
	        if ( $("li#wwcAmzAff-nav-depedencies").length > 0 ) {
	        	section = 'depedencies';
	        }
	        
	        triggers();
        });
    }
    
    function ajaxLoading(status) 
    {
        var loading = $('<div id="wwcAmzAff-ajaxLoadingBox" class="wwcAmzAff-panel-widget">loading</div>'); // append loading
        ajaxBox.html(loading);
    }
    
    function makeRequest() 
    {
		// fix for duble loading of js function
		if( in_loading_section == section ){
			return false;
		}
		in_loading_section = section;
		
		// do not exect the request if we are not into our ajax request pages
		if( ajaxBox.size() == 0 ) return false;

        ajaxLoading();
        var data = {
            'action': 'wwcAmzAffLoadSection',
            'section': section
        }; 
        
        jQuery.post(ajaxurl, data, function (response) {
        	
        	if( response.status == 'redirect' ){
        		window.location = response.url;
        		return;
        	}
        	
            if (response.status == 'ok') {
            	$("h1.wwcAmzAff-section-headline").html(response.headline);
                ajaxBox.html(response.html);
                
                makeTabs();
                
                if( typeof wwcAmzAffDashboard != "undefined" ){
					wwcAmzAffDashboard.init();
				}
				
                // find new open
                var new_open = topMenu.find('li#wwcAmzAff-sub-nav-' + section);
                var in_submenu = new_open.parent('.wwcAmzAff-sub-menu');
                
                // close current open menu
                var current_open = topMenu.find(">li.active");
                if( current_open != in_submenu.parent('li') ){
					current_open.find(".wwcAmzAff-sub-menu").slideUp(250);
					current_open.removeClass("active");
				}
				
				// open current menu
				in_submenu.find('.active').removeClass('active');
				new_open.addClass('active');
				
				// check if is into a submenu
				if( in_submenu.size() > 0 ){
					if( !in_submenu.parent('li').hasClass('active') ){
						in_submenu.slideDown(100);
					}
					in_submenu.parent('li').addClass('active');
				}
				
				if( section == 'dashboard' ){
					topMenu.find(".wwcAmzAff-sub-menu").slideUp(250);
					topMenu.find('.active').removeClass('active');
					
					topMenu.find('li#wwcAmzAff-nav-' + section).addClass('active');
				}
				
				multiselect_left2right();
            }
        },
        'json');
    }
    
    function installDefaultOptions($btn) {
        var theForm = $btn.parents('form').eq(0),
            value = $btn.val(),
            statusBoxHtml = theForm.find('div.wwcAmzAff-message'); // replace the save button value with loading message
        $btn.val('installing default settings ...').removeClass('blue').addClass('gray');
        if (theForm.length > 0) { // serialiaze the form and send to saving data
            var data = {
                'action': 'wwcAmzAffInstallDefaultOptions',
                'options': theForm.serialize()
            }; // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function (response) {
                if (response.status == 'ok') {
                    statusBoxHtml.addClass('wwcAmzAff-success').html(response.html).fadeIn().delay(3000).fadeOut();
                    setTimeout(function () {
                        window.location.reload()
                    },
                    2000);
                } else {
                    statusBoxHtml.addClass('wwcAmzAff-error').html(response.html).fadeIn().delay(13000).fadeOut();
                } // replace the save button value with default message
                $btn.val(value).removeClass('gray').addClass('blue');
            },
            'json');
        }
    }
    
    function saveOptions ($btn, callback) 
    {
        var theForm = $btn.parents('form').eq(0),
            value = $btn.val(),
            statusBoxHtml = theForm.find('div#wwcAmzAff-status-box'); // replace the save button value with loading message
        $btn.val('saving setings ...').removeClass('green').addClass('gray');
        
        multiselect_left2right(true);

        if (theForm.length > 0) { // serialiaze the form and send to saving data
            var data = {
                'action': 'wwcAmzAffSaveOptions',
                'options': theForm.serialize()
            }; // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function (response) {
                if (response.status == 'ok') {
                    statusBoxHtml.addClass('wwcAmzAff-success').html(response.html).fadeIn().delay(3000).fadeOut();
                    if (section == 'synchronization') {
                        updateCron();
                    }
                    
                } // replace the save button value with default message
                $btn.val(value).removeClass('gray').addClass('green');
                
                if( typeof callback == 'function' ){
                	callback.call();
                }
            },
            'json');
        }
    }
    
    function moduleChangeStatus($btn) 
    {
        var value = $btn.text(),
            the_status = $btn.hasClass('activate') ? 'true' : 'false';
        // replace the save button value with loading message
        $btn.text('saving setings ...');
        var data = {
            'action': 'wwcAmzAffModuleChangeStatus',
            'module': $btn.attr('rel'),
            'the_status': the_status
        };
        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function (response) {
            if (response.status == 'ok') {
                window.location.reload();
            }
        },
        'json');
    }
    
    function updateCron() 
    {
        var data = {
            'action': 'wwcAmzAffSyncUpdate'
        }; // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function (response) {},
        'json');
    }
    
    function fixLayoutHeight() 
    {
        var win = $(window),
            wwcAmzAffWrapper = $("#wwcAmzAff-wrapper"),
            minusHeight = 40,
            winHeight = win.height(); // show the freamwork wrapper and fix the height
        wwcAmzAffWrapper.css('min-height', parseInt(winHeight - minusHeight)).show();
        $("div#wwcAmzAff-ajax-response").css('min-height', parseInt(winHeight - minusHeight - 240)).show();
    }
    
    function activatePlugin( $that ) 
    {
        var requestData = {
            'ipc': $('#productKey').val(),
            'email': $('#yourEmail').val()
        };
        if (requestData.ipc == "") {
            alert('Please type your Item Purchase Code!');
            return false;
        }
        $that.replaceWith('Validating your IPC <em>( ' + (requestData.ipc) + ' )</em>  and activating  Please be patient! (this action can take about <strong>10 seconds</strong>)');
        var data = {
            'action': 'wwcAmzAffTryActivate',
            'ipc': requestData.ipc,
            'email': requestData.email
        }; // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function (response) {
            if (response.status == 'OK') {
                window.location.reload();
            } else {
                alert(response.msg);
                return false;
            }
        },
        'json');
    }
    
    function ajax_list()
	{
		var make_request = function( action, params, callback ){
			var loading = $("#wwcAmzAff-main-loading");
			loading.show();
 
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, {
				'action' 		: 'wwcAmzAffAjaxList',
				'ajax_id'		: $(".wwcAmzAff-table-ajax-list").find('.wwcAmzAff-ajax-list-table-id').val(),
				'sub_action'	: action,
				'params'		: params
			}, function(response) {
   
				if( response.status == 'valid' )
				{
					$("#wwcAmzAff-table-ajax-response").html( response.html );

					loading.fadeOut('fast');
				}
			}, 'json');
		}

		$(".wwcAmzAff-table-ajax-list").on('change', 'select[name=wwcAmzAff-post-per-page]', function(e){
			e.preventDefault();

			make_request( 'post_per_page', {
				'post_per_page' : $(this).val()
			} );
		})

		.on('change', 'select[name=wwcAmzAff-filter-post_type]', function(e){
			e.preventDefault();

			make_request( 'post_type', {
				'post_type' : $(this).val()
			} );
		})
		
		.on('change', 'select[name=wwcAmzAff-filter-post_parent]', function(e){
			e.preventDefault();

			make_request( 'post_parent', {
				'post_parent' : $(this).val()
			} );
		})

		.on('click', 'a.wwcAmzAff-jump-page', function(e){
			e.preventDefault();

			make_request( 'paged', {
				'paged' : $(this).attr('href').replace('#paged=', '')
			} );
		})

		.on('click', '.wwcAmzAff-post_status-list a', function(e){
			e.preventDefault();

			make_request( 'post_status', {
				'post_status' : $(this).attr('href').replace('#post_status=', '')
			} );
		});
	}
	
	function amzCheckAWS()
	{
		$('body').on('click', '.wwcAmzAffCheckAmzKeys', function (e) {
            e.preventDefault();
            $('#AccessKeyID').val( $.trim( $('#AccessKeyID').val() ) );
            $('.wwcAmzAff-aff-ids input').each(function(){
            	$(this).val( $.trim( $(this).val() ) );
            });

            var that = $(this),
            	old_value = that.val(),
            	submit_btn = that.parents('form').eq(0).find('input[type=submit]');
            
            that.removeClass('blue').addClass('gray');
            that.val('Checking your keys ...');	
            
            saveOptions(submit_btn, function(){
            	
            	jQuery.post(ajaxurl, {
					'action' : 'wwcAmzAffCheckAmzKeys'
				}, function(response) {
						if( response.status == 'valid' ){
							alert('WooCommerce Amazon Affiliates was able to connect to Amazon with the specified AWS Key Pair and Associate ID');
						}
						else{
							var msg = 'WooCommerce Amazon Affiliates was not able to connect to Amazon with the specified AWS Key Pair and Associate ID. Please triple-check your AWS Keys and Associate ID.';
							
							msg += "\n" + response.msg;
							alert( msg );
							
						}
						that.val( old_value ).removeClass('gray').addClass('blue');
				}, 'json');
            });
        });
	}
	
	function removeHelp()
	{
		$("#wwcAmzAff-help-container").remove();	
	}
	
	function showHelp( that )
	{
		removeHelp();

		var help_type = that.data('helptype');
        var operation = that.data('operation');
        var html = $('<div class="wwcAmzAff-panel-widget" id="wwcAmzAff-help-container" />');
        
        var btn_close_text = ( operation == 'help' ? 'Close HELP' : 'Close Feedback' );
        html.append("<a href='#close' class='wwcAmzAff-button red' id='wwcAmzAff-close-help'>" + btn_close_text + "</a>")
		if( help_type == 'remote' ){
			var url = that.data('url');
			var content_wrapper = $("#wwcAmzAff-content");
			
			html.append( '<iframe src="' + ( url ) + '" style="width:100%; height: 100%;border: 1px solid #d7d7d7;" frameborder="0" id="wwcAmzAff-iframe-docs"></iframe>' )
			
			content_wrapper.append(html);
			
			// feedback iframe related!
			//var $iframe = $('#wwcAmzAff-iframe-docs'),
		}
	}
	
	function hashChange()
	{
		if ( location.href.indexOf("wwcAmzAff#") != -1 ) {
			// Alerts every time the hash changes!
			if(location.hash != "") {
				section = location.hash.replace("#", '');
				
				var __tmp = section.indexOf('#');
				if ( __tmp == -1 ) subsection = '';
				else { // found subsection block!
						subsection = section.substr( __tmp+1 );
						section = section.slice( 0, __tmp );
					}
				} 
	 
				if ( subsection != '' )
				makeRequest([
					function (s) { scrollToElement( s ) },
					'#'+subsection
				]);
			else 
				makeRequest();
			return false;
		}
		if ( location.href.indexOf("=wwcAmzAff") != -1 ) {
			makeRequest();
			return false;
		}
	}
	
	function multiselect_left2right( autselect ) {
		var $allListBtn = $('.multisel_l2r_btn');
		var autselect = autselect || false;
 
		if ( $allListBtn.length > 0 ) {
			$allListBtn.each(function(i, el) {
 
				var $this = $(el), $multisel_available = $this.prevAll('.wwcAmzAff-multiselect-available').find('select.multisel_l2r_available'), $multisel_selected = $this.prevAll('.wwcAmzAff-multiselect-selected').find('select.multisel_l2r_selected');
 
				if ( autselect ) {
					$multisel_selected.find('option').each(function() {
						$(this).prop('selected', true);
					});
					$multisel_available.find('option').each(function() {
						$(this).prop('selected', false);
					});
				} else {

				$this.on('click', '.moveright', function(e) {
					e.preventDefault();
					$multisel_available.find('option:selected').appendTo($multisel_selected);
				});
				$this.on('click', '.moverightall', function(e) {
					e.preventDefault();
					$multisel_available.find('option').appendTo($multisel_selected);
				});
				$this.on('click', '.moveleft', function(e) {
					e.preventDefault();
					$multisel_selected.find('option:selected').appendTo($multisel_available);
				});
				$this.on('click', '.moveleftall', function(e) {
					e.preventDefault();
					$multisel_selected.find('option').appendTo($multisel_available);
				});
				
				}
			});
		}
	}
	
	function makeTabs()
	{
		$('ul.tabsHeader').each(function() {
			// For each set of tabs, we want to keep track of
			// which tab is active and it's associated content
			var $active, $content, $links = $(this).find('a');

			// If the location.hash matches one of the links, use that as the active tab.
			// If no match is found, use the first link as the initial active tab.
			var __tabsWrapper = $(this), __currentTab = $(this).find('li#tabsCurrent').attr('title');
			$active = $( $links.filter('[title="'+__currentTab+'"]')[0] || $links[0] );
			$active.addClass('active');
			$content = $( '.'+($active.attr('title')) );

			// Hide the remaining content
			$links.not($active).each(function () {
				$( '.'+($(this).attr('title')) ).hide();
			});

			// Bind the click event handler
			$(this).on('click', 'a', function(e){
				// Make the old tab inactive.
				$active.removeClass('active');
				$content.hide();

				// Update the variables with the new link and content
				__currentTab = $(this).attr('title');
				__tabsWrapper.find('li#tabsCurrent').attr('title', __currentTab);
				$active = $(this);
				$content = $( '.'+($(this).attr('title')) );

				// Make the tab active.
				$active.addClass('active');
				$content.show();

				// Prevent the anchor's default click action
				e.preventDefault();
			});
		});
	}
	
    function triggers() 
    {
    	amzCheckAWS();
    	
        $(window).resize(function () {
            fixLayoutHeight();
        });
         
		$('body').on('click', '.wwcAmzAff_activate_product', function (e) {
            e.preventDefault();
            activatePlugin($(this));
        });
		$('body').on('click', '.wwcAmzAff-saveOptions', function (e) {
            e.preventDefault();
            saveOptions($(this));
        });
        $('body').on('click', '.wwcAmzAff-installDefaultOptions', function (e) {
            e.preventDefault();
            installDefaultOptions($(this));
        });
		
		$('body').on('click', '#' + option.prefix + "-module-manager a", function (e) {
            e.preventDefault();
            moduleChangeStatus($(this));
        }); // Bind the event.
        
        $('body').on('click', 'input#psp-item-check-all', function(){
            var that = $(this),
                checkboxes = $('#psp-list-table-posts input.psp-item-checkbox');

            if( that.is(':checked') ){
                checkboxes.prop('checked', true);
            }
            else{
                checkboxes.prop('checked', false);
            }
        });

		// Bind the hashchange event.
		/*$(window).on('hashchange', function(){
			hashChange();
		});
		hashChange();*/
        $(window).hashchange(function () { // Alerts every time the hash changes!
            if (location.hash != "") {
                section = location.hash.replace("#!/", '');
                if( t.size() > 0 ) {
                	makeRequest();
                }
            }else{
	            if( t.size() > 0 && location.search == "?page=wwcAmzAff" ){
	            	makeRequest();
	            }
            }
        }) // Trigger the event (useful on page load).
        $(window).hashchange();
        
        ajax_list();
        
        $("body").on('click', "a.wwcAmzAff-show-feedback", function(e){
        	e.preventDefault();
        	
        	showHelp( $(this) );
        });
        
		$("body").on('click', "a.wwcAmzAff-show-docs-shortcut", function(e){
        	e.preventDefault();
        	
        	$("a.wwcAmzAff-show-docs").click();
        });
        
        $("body").on('click', "a.wwcAmzAff-show-docs", function(e){
        	e.preventDefault();
        	
        	showHelp( $(this) );
        });
        
         $("body").on('click', "a#wwcAmzAff-close-help", function(e){
        	e.preventDefault();
        	
        	removeHelp();
        });
        
        multiselect_left2right();
    }
	
   	init();
   	
   	return {
   		'init'				: init,
   		'makeTabs'			: makeTabs,
   	}
})(jQuery);