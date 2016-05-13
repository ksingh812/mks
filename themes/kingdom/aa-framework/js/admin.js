/*
    Document   :  kingdom
    Created on :  2014
    Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
kingdom = (function ($) {
    "use strict";

	var option = {
		'prefix': "kingdom"
	};
	
    var t = null,
        ajaxBox = null,
        section = 'dashboard',
        topMenu = null,
        formfield, upload_popup_parent;
	
    function init() 
    {
        $(document).ready(function(){
        	
        	t = $("div.wrapper-kingdom");
	        ajaxBox = t.find('#kingdom-ajax-response');
	        topMenu = t.find('#kingdom-topMenu');
	        
	        if (t.size() > 0 ) {
	            fixLayoutHeight();
	        }
	        
	        triggers();
        });
    }
    
    function ajaxLoading(status) 
    {
        var loading = $('<div id="kingdom-ajaxLoadingBox" class="kingdom-panel-widget">loading</div>'); // append loading
        ajaxBox.html(loading);
    }
    
    function moduleWidgetStatus ($btn) 
    {
		var value = $btn.text(), the_status = $btn.hasClass('activate') ? 'true' : 'false';
		// replace the save button value with loading message
		$btn.text('saving setings ...');
		
		
		var data = {
			'action' : 'kingdomWidgetChangeStatus',
			'module' : $btn.attr('rel'),
			'the_status' : the_status
		};
		
		console.log( data ); 
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			if (response.status == 'ok') {
				window.location.reload();
			}
		}, 'json');
	}

    function makeRequest() 
    {
        ajaxLoading();
        var data = {
            'action': 'kingdomLoadSection',
            'section': section
        }; // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function (response) {
            if (response.status == 'ok') {
            	$("h1.kingdom-section-headline").html(response.headline);
                ajaxBox.html(response.html);
                
                makeTabs();
                
                if( typeof kingdomDashboard != "undefined" ){
					kingdomDashboard.init();
				}
				
                // find new open
                var new_open = topMenu.find('li#kingdom-sub-nav-' + section);
                var in_submenu = new_open.parent('.kingdom-sub-menu');
                
                // close current open menu
                var current_open = topMenu.find(">li.active");
                if( current_open != in_submenu.parent('li') ){
					current_open.find(".kingdom-sub-menu").slideUp(250);
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
					topMenu.find(".kingdom-sub-menu").slideUp(250);
					topMenu.find('.active').removeClass('active');
					
					topMenu.find('li#kingdom-nav-' + section).addClass('active');
				}
				
				multiselect_left2right();
				 	
    			$('.kingdom-wp-color-picker').wpColorPicker();
    			
    			font_preview();
				selectByRange();
            }
        },
        'json');
    }
    
    function installDefaultOptions($btn) {
        var theForm = $btn.parents('form').eq(0),
            value = $btn.val(),
            statusBoxHtml = theForm.find('div.kingdom-message'); // replace the save button value with loading message
        $btn.val('installing default settings ...').removeClass('blue').addClass('gray');
        if (theForm.length > 0) { // serialiaze the form and send to saving data
            var data = {
                'action': 'kingdomInstallDefaultOptions',
                'options': theForm.serialize()
            }; // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function (response) {
                if (response.status == 'ok') {
                    statusBoxHtml.addClass('kingdom-success').html(response.html).fadeIn().delay(3000).fadeOut();
                    setTimeout(function () {
                        window.location.reload()
                    },
                    2000);
                } else {
                    statusBoxHtml.addClass('kingdom-error').html(response.html).fadeIn().delay(13000).fadeOut();
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
            statusBoxHtml = theForm.find('div#kingdom-status-box'); // replace the save button value with loading message
        $btn.val('saving setings ...').removeClass('green').addClass('gray');
        
        multiselect_left2right(true);

        if (theForm.length > 0) { // serialiaze the form and send to saving data
            var data = {
                'action': 'kingdomSaveOptions',
                'options': Base64.encode( theForm.serialize() )
            }; // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            jQuery.post(ajaxurl, data, function (response) {
                if (response.status == 'ok') {
                    statusBoxHtml.addClass('kingdom-success').html(response.html).fadeIn().delay(3000).fadeOut();
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
            the_status = $btn.hasClass('activate') ? 'true' : 'false'; // replace the save button value with loading message
        $btn.text('saving setings ...');
        var data = {
            'action': 'kingdomModuleChangeStatus',
            'module': $btn.attr('rel'),
            'the_status': the_status
        }; // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
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
            'action': 'kingdomSyncUpdate'
        }; // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        jQuery.post(ajaxurl, data, function (response) {},
        'json');
    }
    
    function fixLayoutHeight() 
    {
        var win = $(window),
            kingdomWrapper = $("#kingdom-wrapper"),
            minusHeight = 40,
            winHeight = win.height(); // show the freamwork wrapper and fix the height
        kingdomWrapper.css('min-height', parseInt(winHeight - minusHeight)).show();
        $("div#kingdom-ajax-response").css('min-height', parseInt(winHeight - minusHeight - 240)).show();
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
            'action': 'kingdomTryActivate',
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
			var loading = $("#kingdom-main-loading");
			loading.show();
 
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, {
				'action' 		: 'kingdomAjaxList',
				'ajax_id'		: $(".kingdom-table-ajax-list").find('.kingdom-ajax-list-table-id').val(),
				'sub_action'	: action,
				'params'		: params
			}, function(response) {
   
				if( response.status == 'valid' )
				{
					$("#kingdom-table-ajax-response").html( response.html );

					loading.fadeOut('fast');
				}
			}, 'json');
		}

		$(".kingdom-table-ajax-list").on('change', 'select[name=kingdom-post-per-page]', function(e){
			e.preventDefault();

			make_request( 'post_per_page', {
				'post_per_page' : $(this).val()
			} );
		})

		.on('change', 'select[name=kingdom-filter-post_type]', function(e){
			e.preventDefault();

			make_request( 'post_type', {
				'post_type' : $(this).val()
			} );
		})

		.on('click', 'a.kingdom-jump-page', function(e){
			e.preventDefault();

			make_request( 'paged', {
				'paged' : $(this).attr('href').replace('#paged=', '')
			} );
		})

		.on('click', '.kingdom-post_status-list a', function(e){
			e.preventDefault();

			make_request( 'post_status', {
				'post_status' : $(this).attr('href').replace('#post_status=', '')
			} );
		});
	}
	
	function removeHelp()
	{
		$("#kingdom-help-container").remove();	
	}
	
	function showHelp( that )
	{
		removeHelp();
		var help_type = that.data('helptype');
        var html = $('<div class="kingdom-panel-widget" id="kingdom-help-container" />');
        html.append("<a href='#close' class='kingdom-button red' id='kingdom-close-help'>Close HELP</a>")
		if( help_type == 'remote' ){
			var url = that.data('url');
			var content_wrapper = $("#kingdom-content");
			
			html.append( '<iframe src="' + ( url ) + '" style="width:100%; height: 100%;border: 1px solid #d7d7d7;" frameborder="0"></iframe>' )
			
			content_wrapper.append(html);
		}
	}
	
	function multiselect_left2right( autselect ) {
		var $allListBtn = $('.multisel_l2r_btn');
		var autselect = autselect || false;
 
		if ( $allListBtn.length > 0 ) {
			$allListBtn.each(function(i, el) {
 
				var $this = $(el), $multisel_available = $this.prevAll('.kingdom-multiselect-available').find('select.multisel_l2r_available'), $multisel_selected = $this.prevAll('.kingdom-multiselect-selected').find('select.multisel_l2r_selected');
 
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
	
	function showThumbWPImage( media_id, callback )
	{
		var data = {
            'action': 'kingdomGetMediaThumb',
            'media_id': media_id
        }; 
        
        $.post(ajaxurl, data, function (response) {
            if( typeof callback == 'function' ){
            	callback( response );
            }
        },
        'json');
	}
	
	function fixSlidesOrders( items_container )
	{
		items_container.find("li").each(function(i){
			var slide = $(this),
				order = i + 1;
			 
			slide.find('.kingdom-gallery-order').text( "#" + order );
			
			slide.find('input,select,label').each(function(){
				var field = $(this);
				
				if( field.is("label") ){
					var orig_val = field.attr( 'for' ).split("[");
					orig_val = orig_val[0];
					
					field.attr( 'for', orig_val + "[" + order + "]" );
				}
				else{
					var orig_val = field.attr( 'name' ).split("[");
					orig_val = orig_val[0];
					
					field.attr( 'name', orig_val + "[" + order + "]" );
				}
			});
		});
		
		makeGallerySortable( items_container );
	}
	
	function makeGallerySortable(items_container)
	{
		items_container.sortable({
			placeholder: "kingdom-highlight-gallery-item",
			start: function( event, ui ) {
				$(".kingdom-highlight-gallery-item").height( $(ui.item).height() );
			},
			stop: function(){
				fixSlidesOrders(items_container);
			}
		});
		//items_container.disableSelection();
	}
	
	function send_to_editor(){
		if( window.send_to_editor != undefined ) {
			// store old send to editor function
			window.restore_send_to_editor = window.send_to_editor;	
		}
	
		window.send_to_editor = function(html){
			var thumb_id = $(html).attr('class').split('wp-image-');
			// var thumb_id = $('img', html).attr('class').split('wp-image-');
			thumb_id = parseInt(thumb_id[1]);
			
			jQuery.post(ajaxurl, {
				'action' : 'kingdomWPMediaUploadImage',
				'att_id' : thumb_id
			}, function(response) {
				if (response.status == 'valid') {
					
					var upload_box = upload_popup_parent.parents('.kingdom-upload-image-wp-box').eq(0);
					
					upload_box.find('input').val( thumb_id );
					
					var the_preview_box = upload_box.find('.upload_image_preview'),
						the_img = the_preview_box.find('img');
						
					the_img.attr('src', response.thumb );
					the_img.show();
					the_preview_box.show();
					upload_box.find('.kingdom-prev-buttons').show();
					upload_box.find(".upload_image_button_wp").hide();
				
				}
			}, 'json');
			
			tb_remove();
			
			if( window.restore_send_to_editor != undefined ) {
				// store old send to editor function
				window.restore_send_to_editor = window.send_to_editor;	
			}
		}
	}
	
	function removeWpUploadImage( $this )
	{
		var upload_box = $this.parents(".kingdom-upload-image-wp-box").eq(0);
		upload_box.find('input').val('');
		var the_preview_box = upload_box.find('.upload_image_preview'),
			the_img = the_preview_box.find('img');
			
		the_img.attr('src', '');
		the_img.hide();
		the_preview_box.hide();
		upload_box.find('.kingdom-prev-buttons').hide();
		upload_box.find(".upload_image_button_wp").fadeIn('fast');
	}
	
	function sidebarCheckNew( value, that )
	{
		var sidebar_position = $("#kingdom-sidebar-position");
			
		$("#kingdom-sidebar-items").find('tr').hide();
		$("#kingdom-" + ( value ) + "-sidebar-item").show();
		
		$("#kingdom-sidebar-position").find('a').removeClass('on');
		that.addClass('on');
		
		sidebar_position.find('input[type="radio"]').prop('checked', false);
		sidebar_position.find('input[value="' + ( value ) + '"]').prop('checked', true);
	}
	
	function sidebarReplaceRadio()
	{
		var sidebar_position = $("#kingdom-sidebar-position"),
			radios = sidebar_position.find('input');
		
		radios.each(function(){
			var that = $(this);
			that.hide();
			
			var html = $("<a href='#' class='kingdom-radio-img' data-value='" + ( that.val() ) + "' title='" + ( that.data('tooltip') ) + "'><img src='" + ( that.data('replaceimg') ) + "' /></a>");
			
			sidebar_position.append( html );
			if( that.is(':checked') ){
				sidebarCheckNew(that.val(), html); 
			}
		});
		
		sidebar_position.find('a').click(function(e){
			e.preventDefault();
			var that = $(this),
				value = that.data('value');
			sidebarCheckNew(value, that);
		});
		
		$("#kingdom_sidebar").show();
	}
	
	function loadSidebarSelector( that )
	{
		var loading = $('<div id="kingdom-ajaxLoadingBox" class="kingdom-panel-widget">loading</div>'); // append loading
        ajaxBox.append(loading);
        
        jQuery.post(ajaxurl, {
			'action' : 'KingdomGetSidebarConditions',
			'sidebar' : that.val()
		}, function(response) {
			if (response.status == 'valid') {
				$("#kingdom-ajaxLoadingBox").remove();
				$("#kingdom-conditions-ajax").html( response.html );
			}
		}, 'json');
	}
	
	function saveSideBar( sidebar )
	{
		jQuery.post(ajaxurl, {
				'action' : 'kingdomSaveSidebarSettings',
				'settings' : $("#kingdom_sidebars_per_sections").serialize()
			}, function(response) {
				var status_box = $("#wwcAmzAff-status-box");
				if( response.status == 'valid' ){
					status_box
						.addClass('wwcAmzAff-success')
						.text(response.msg)
						.fadeIn('fast');
				}else{
					status_box
						.addClass('wwcAmzAff-error')
						.text("Unable to save!")
						.fadeIn('fast');
				}
				
				setTimeout(function(){
					status_box.fadeOut('fast');
				}, 1500);
		}, 'json');
	}
	
	function saveSidebars()
	{
		jQuery.post(ajaxurl, {
				'action' : 'kingdomSaveSidebars',
				'settings' : $("#kingdom_dynamic_sidebars").serialize()
			}, function(response) {
				var status_box = $("#wwcAmzAff-status-box");
				if( response.status == 'valid' ){
					status_box
						.addClass('wwcAmzAff-success')
						.text(response.msg)
						.fadeIn('fast');
				}else{
					status_box
						.addClass('wwcAmzAff-error')
						.text("Unable to save!")
						.fadeIn('fast');
				}
				
				setTimeout(function(){
					status_box.fadeOut('fast');
				}, 1500);
		}, 'json');
	}
	
	function font_preview()
	{
		function change_font( preview_elm )
		{
			var that = preview_elm,
				pair_element = jQuery("#" + that.attr('id').replace('-font-preview', '')),
				pair_value = pair_element.val(),
				google_font_url = "http://fonts.googleapis.com/css?family=" + pair_value;
			
			// step 1, load into DOM the spreadsheet
			jQuery("head").append("<link href='" + ( google_font_url ) + "' rel='stylesheet' type='text/css'>");
			
			// step 2, print the font name into preview with inline font-family
			
			that.html( "<span style='font-family: " + ( pair_value ) + "'>Grumpy wizards make toxic brew for the evil Queen and Jack.</span>" );
		}
		
		jQuery(".kingdom-font-preview").each(function(){
			
			var that = jQuery(this),
				pair_element = jQuery("#" + that.attr('id').replace('-font-preview', ''));
			
			change_font( that );
				
			pair_element.change(function(e){
				
				var preview = jQuery("#" + jQuery(this).attr('id') + "-font-preview" );
				change_font( preview );
			});
		});	
	}
	
	function selectByRange()
	{
		//console.log( jQuery('.range-wrap input[type="range"]').val() ); 
		jQuery('.range-wrap input[type="range"]').each(function(){
			var that = jQuery(this);
			that.on('change', function(){
				var rangeval = that.val();
				that.parent().find('.range-value').text( rangeval+'px' );
			})
		})
	}
	
    function triggers() 
    {
    	sidebarReplaceRadio();
    	
        $(window).resize(function() {
            fixLayoutHeight();
        });
        
        $('.upload_image_button_wp, .change_image_button_wp').live('click', function(e) {
			e.preventDefault();
			upload_popup_parent = $(this);
			var win = $(window);
			
			send_to_editor();
		
			tb_show('Select image', 'media-upload.php?type=image&amp;height=' + ( parseInt(win.height() / 1.2) ) + '&amp;width=610&amp;post_id=0&amp;from=aaframework&amp;TB_iframe=true');
		});
		
		$('.remove_image_button_wp').live('click', function(e) {
			e.preventDefault();
			
			removeWpUploadImage( $(this) );
		});
		
		if( $('#slideshow_type').val() == 'kingdom-slider' ) {
			$('#revolution_slider_select').parent().parent().hide();	
		} else if( $('#slideshow_type').val() == 'revolution-slider' ) {
			$('#full_page_slideshow').parent().parent().hide();
		}
		
		$('body').on('change', '#home_slider', function (e) {
            e.preventDefault();
            var that = $(this);
           	if( that.val() == 'kingdom-slider' ) {
           		$('#full_page_slideshow').parent().parent().slideDown();
           		$('#revolution_slider_select').parent().parent().slideUp();
           	} else if( that.val() == 'revolution-slider' ) {
           		$('#revolution_slider_select').parent().parent().slideDown();
           		$('#full_page_slideshow').parent().parent().slideUp();
           	}
        });
        
        $("input#kingdom-slideshow-shortcode[type='text']").click(function () {
		   $(this).select();
		});

		$('body').on('click', '.kingdom_activate_product', function (e) {
            e.preventDefault();
            activatePlugin($(this));
        });
		$('body').on('click', '.kingdom-saveOptions', function (e) {
            e.preventDefault();
            saveOptions($(this));
        });
        $('body').on('click', '.kingdom-installDefaultOptions', function (e) {
            e.preventDefault();
            installDefaultOptions($(this));
        });
		
		$('body').on('click', '#' + option.prefix + "-module-manager a", function (e) {
            e.preventDefault();
            moduleChangeStatus($(this));
        }); // Bind the event.
        
        $(window).hashchange(function () { // Alerts every time the hash changes!
            if (location.hash != "") {
                section = location.hash.replace("#!/", '');
                if( t.size() > 0 ) {
                	makeRequest();
                }
            }else{
	            if( t.size() > 0 && location.search == "?page=kingdom" ){
	            	makeRequest();
	            }
            }
        }) // Trigger the event (useful on page load).
        
        $(window).hashchange();
        
        ajax_list();
        
		$("body").on('click', "a.kingdom-show-docs-shortcut", function(e){
        	e.preventDefault();
        	
        	$("a.kingdom-show-docs").click();
        });
        
        $("body").on('click', "a.kingdom-show-docs", function(e){
        	e.preventDefault();
        	
        	showHelp( $(this) );
        });
        
        $("body").on('click', "a#kingdom-close-help", function(e){
        	e.preventDefault();
        	
        	removeHelp();
        });
        
        
        $("body").on('click', ".kingdom-upload-remove-button", function(e){
        	e.preventDefault();
        	
        	var that = $(this),
        		parent = that.parents('.kingdom-gallery-image').eq(0);
        		
        	parent.find('input').val('');
        	parent.find('img').remove();
        	
        	parent.addClass('has_no_image');
        	parent.find(".the_slide_options_no_image").show();
        });
        
        $("body").on('click', ".kingdom-upload-button", function(e){
        	e.preventDefault();
        	
        	var that = $(this);
        	// The input field that will hold the uploaded file url
	        formfield = that.parents('.kingdom-gallery-image').eq(0).find('input');

	        tb_show( '', 'media-upload.php?TB_iframe=true' );
	 		
	        return false;
	    });
	    
	    $(".kingdom-gallery-items").each(function(){
	    	fixSlidesOrders( $(this) );
	    });
	    
	    $("body").on('click', ".kingdom-slide-remove-button", function(e){
        	e.preventDefault();
        	
        	if( confirm("Are you sure you want to delete this slide?") ){
        		var that = $(this),
        			slide = that.parents('.kingdom-gallery-item').eq(0),
        			items_container = that.parents('.kingdom-form').eq(0).find(".kingdom-gallery-items");
        		
        		slide.remove();
        		
        		fixSlidesOrders( items_container ); 
        	}
        	return false;
        });
        	
	    $("body").on('click', ".kingdom-gallery-add-new", function(e){
        	e.preventDefault();
        	
        	var that = $(this),
        		template = $("#kingdom-gallery-item-model").html(),
        		items_container = that.parents('.kingdom-form').eq(0).find(".kingdom-gallery-items"),
        		order = ( parseInt(items_container.find("li").size(), 10) + 1);
        	
        	// add order to template 
        	template = template.replace("#order", "#" + order);
        	
        	// append the template to items container
        	items_container.append(template);
        	
        	fixSlidesOrders( items_container );
	    });
	    
	    //adding my custom function with Thick box close function tb_close() .
	    window.old_tb_remove = window.tb_remove;
	    window.tb_remove = function() {
	        window.old_tb_remove(); // calls the tb_remove() of the Thickbox plugin
	        formfield=null;
	    };
	 
	    // user inserts file into post. only run custom if user started process using the above process
	    // window.send_to_editor(html) is how wp would normally handle the received data
	    window.original_send_to_editor = window.send_to_editor;
	    window.send_to_editor = function(html){
	        if (formfield) { 
	            var fileurl = jQuery('img', html).attr('src');
	            formfield.val(fileurl);
	            
	            var img_container = formfield.parent('.kingdom-gallery-image');
	            
	            var regex = new RegExp("wp-image-([0-9]*)$");
			    var $img = $(html).find('img');
			    $img.each(function(i) {
			        var $this = $(this), cssClass = $this.prop('class');
			        if ( $.trim(cssClass) != '' ) {
			            var val = cssClass.split(' ')[2].replace('wp-image-', '');
			            
			            showThumbWPImage( val, function( response ){
    		
				    		var img = $("<img />");
				    		
				    		img
				    			.attr("src", response.thumb_url )
				    			.attr('width', response.width );
				    			
				    		img_container.find('img').remove();
				    		img_container.append( img );
				    		img_container.removeClass('has_no_image');
				    	});
			        }
			    });
			    
			    tb_remove();
	        } else {
	            window.original_send_to_editor(html);
	        }
	    };
	    
        multiselect_left2right();
        
        $("body").on('change', "select.kingdom_sidebar_selector", function(e){
        	loadSidebarSelector( $(this) );
        });
        
        $("body").on('click', "#kingdom-save-sidebar-settings", function(e){
        	var that = $(this);
        	
        	e.preventDefault();
        	saveSideBar( that.data("sidebar") );
        });
        
        $("body").on('click', '.kingdom_save_sidebars', function(e) {
			e.preventDefault();
			
			saveSidebars();
		});
		
		$("body").on('click', '#kingdom-widgets-manager a:not(.image-preview)', function(e) {
			e.preventDefault();
			moduleWidgetStatus($(this));
		});
    }
	
   	init();
   	
   	/**
	*
	*  Base64 encode / decode
	*  http://www.webtoolkit.info/
	*
	**/
	 
	var Base64 = {
	 
		// private property
		_keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
	 
		// public method for encoding
		encode : function (input) {
			var output = "";
			var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
			var i = 0;
	 
			input = Base64._utf8_encode(input);
	 
			while (i < input.length) {
	 
				chr1 = input.charCodeAt(i++);
				chr2 = input.charCodeAt(i++);
				chr3 = input.charCodeAt(i++);
	 
				enc1 = chr1 >> 2;
				enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
				enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
				enc4 = chr3 & 63;
	 
				if (isNaN(chr2)) {
					enc3 = enc4 = 64;
				} else if (isNaN(chr3)) {
					enc4 = 64;
				}
	 
				output = output +
				this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
				this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);
	 
			}
	 
			return output;
		},
	 
		// public method for decoding
		decode : function (input) {
			var output = "";
			var chr1, chr2, chr3;
			var enc1, enc2, enc3, enc4;
			var i = 0;
	 
			input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");
	 
			while (i < input.length) {
	 
				enc1 = this._keyStr.indexOf(input.charAt(i++));
				enc2 = this._keyStr.indexOf(input.charAt(i++));
				enc3 = this._keyStr.indexOf(input.charAt(i++));
				enc4 = this._keyStr.indexOf(input.charAt(i++));
	 
				chr1 = (enc1 << 2) | (enc2 >> 4);
				chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
				chr3 = ((enc3 & 3) << 6) | enc4;
	 
				output = output + String.fromCharCode(chr1);
	 
				if (enc3 != 64) {
					output = output + String.fromCharCode(chr2);
				}
				if (enc4 != 64) {
					output = output + String.fromCharCode(chr3);
				}
	 
			}
	 
			output = Base64._utf8_decode(output);
	 
			return output;
	 
		},
	 
		// private method for UTF-8 encoding
		_utf8_encode : function (string) {
			string = string.replace(/\r\n/g,"\n");
			var utftext = "";
	 
			for (var n = 0; n < string.length; n++) {
	 
				var c = string.charCodeAt(n);
	 
				if (c < 128) {
					utftext += String.fromCharCode(c);
				}
				else if((c > 127) && (c < 2048)) {
					utftext += String.fromCharCode((c >> 6) | 192);
					utftext += String.fromCharCode((c & 63) | 128);
				}
				else {
					utftext += String.fromCharCode((c >> 12) | 224);
					utftext += String.fromCharCode(((c >> 6) & 63) | 128);
					utftext += String.fromCharCode((c & 63) | 128);
				}
	 
			}
	 
			return utftext;
		},
	 
		// private method for UTF-8 decoding
		_utf8_decode : function (utftext) {
			var string = "";
			var i = 0;
			var c = c1 = c2 = 0;
	 
			while ( i < utftext.length ) {
	 
				c = utftext.charCodeAt(i);
	 
				if (c < 128) {
					string += String.fromCharCode(c);
					i++;
				}
				else if((c > 191) && (c < 224)) {
					c2 = utftext.charCodeAt(i+1);
					string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
					i += 2;
				}
				else {
					c2 = utftext.charCodeAt(i+1);
					c3 = utftext.charCodeAt(i+2);
					string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
					i += 3;
				}
	 
			}
	 
			return string;
		}
	 
	}
   	
   	return {
   		'init'				: init,
   		'makeTabs'			: makeTabs,
   		'replaceRadio'		: sidebarReplaceRadio
   	}
})(jQuery);