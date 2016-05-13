jQuery(document).ready(function($) {

	// very global
	var kingdom_ID	= [];
	var kingdom_last_key = '';
	
	var kingdom_updateID = function() {
		jQuery(".kingdom-elements").each( function() {
			var elm = jQuery(this);
			if(elm.is(':checked') == true){
				var asin = elm.attr('id').replace('kingdom-check-', '');
				
				// if not in array
				if(jQuery.inArray(asin, kingdom_ID) == -1) kingdom_ID.push(asin);
			}else{
				if(jQuery.inArray(asin, kingdom_ID) > -1 ) kingdom_ID.pop(asin);
			}
		});
		
		jQuery('#kingdom-status-remaining').text(kingdom_ID.length);
	}
	
	var kingdom_reset_div = function () {
		
		var bulkimport_div 	= jQuery("#kingdom-bulkimport .kingdom-product-box"),
			win_h	 		= jQuery(window).height(),
			div_height 		= win_h - 300;
			
		bulkimport_div.height( div_height );	
	}

	var doit;
	jQuery(window).on('resize', function() {
		clearTimeout(doit);
		doit = setTimeout(function(){ kingdom_reset_div(); }, 100);
	});
	
	var kingdom_launch_search = function (data) {
		
		// delete all array documents
		kingdom_ID = null; kingdom_ID = [];
		
		var searchAjaxLoader 	= jQuery("#kingdom-ajax-loader"),
			searchBtn 			= jQuery("#kingdom-search-link");
			
		searchBtn.hide();	
		searchAjaxLoader.show();
		
		var data = {
			action: 'kingdom_bulk_products_request',
			category: jQuery('#dropdown_product_cat').val()
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			jQuery("#kingdom-ajax-results").html(response);
			
			searchBtn.show();	
			searchAjaxLoader.hide();
			
			kingdom_updateID();
			jQuery(".kingdom-import-bar").show();
			
			kingdom_reset_div();
			jQuery(".kingdom-product-box").scrollTop(0);
		});
	};
	
	
	jQuery("#kingdom-page").live('change', function() {
		// Hide the label at start
		jQuery('#progress_bar .ui-progress .ui-label').hide();

		// Set initial value
		jQuery('#progress_bar .ui-progress').css('width', '0%');
		
		jQuery('#kingdom-status-ready').text('0');
		
		kingdom_launch_search();
	});
	
	jQuery("#kingdom-check-all").live('change', function() {
		var allChecks = jQuery(".kingdom-elements");

		if(jQuery(this).is(':checked') == true){
			allChecks.each( function() {
				jQuery(this).attr("checked", true);
				kingdom_ID.pop(jQuery(this).attr('id').replace('kingdom-check-', ''));
			});
		}else{
			allChecks.each( function() {
				jQuery(this).attr("checked", false);
				kingdom_ID.pop(jQuery(this).attr('id').replace('kingdom-check-', ''));
			});
		}
		
		kingdom_updateID();
	});
	
	jQuery(".kingdom-elements").live('change', function() {
		if(jQuery(this).is(':checked') == false){
			kingdom_ID.pop(jQuery(this).attr('id').replace('kingdom-check-', ''));
		}else{
			kingdom_ID.push(jQuery(this).attr('id').replace('kingdom-check-', ''));
		}
		
		jQuery('#kingdom-status-remaining').text(kingdom_ID.length);
	});
	
	jQuery("#kingdom-search-form").submit(function(e) {
		kingdom_launch_search();
		return false;
	});
	
	jQuery("a.kingdom-load-product").live('click', function(e) {
		e.preventDefault();
		
		var data = {
			'action': 'kingdom_load_product',
			'ID':  jQuery(this).attr('rel')
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: data,
			success: function(response) {
				if(response.status == 'valid'){
					window.location = response.redirect_url;
					return true;
				}else{
					alert(response.msg);
					return false
				}
			}
		});
	});
	
	jQuery("#kingdom-category").live('change', function() {
		var $that 	= jQuery(this),
			val 	= $that.val();
		
		if(val != "All"){
			var data = {
				'action': 'kingdom_load_sort_by_categ',
				'cat': val
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function(response) {
					if(response.status == 'valid'){
						jQuery('#kingdom-sort-box').find('select').html(response.select_html);
						jQuery('#kingdom-sort-info').html(response.info_html);
					}
				}
			});
		}else{
			jQuery('#kingdom-sort-info').html('<tr><td><strong>You cannot use any sort parameters with the All search index.</strong></td></tr>');
		}
	});
	
	jQuery("a#kingdom-import-btn").live('click', function(e) {
		e.preventDefault();
		
		var numberOfItems = kingdom_ID.length,
			loaded = 0,
			labelCurr = jQuery('#kingdom-status-ready'),
			labelTotal = jQuery('#kingdom-status-remaining');
		
		// update totals
		labelCurr.text(loaded);	
		labelTotal.text(numberOfItems);	
		
		if(numberOfItems == 0) alert('Please first select some products from list!');
		
		// Hide the label at start
		jQuery('#progress_bar .ui-progress .ui-label').hide();

		// Set initial value
		jQuery('#progress_bar .ui-progress').css('width', '0%');
		
		
		var kingdom_insert_new_product = function(curr_step) {
		
			// stop if not valid kingdom_ID
			if(typeof kingdom_ID[curr_step] == 'undefined') return false;
			
			var data = {
				'action': 'kingdom_process_product',
				'ID':  kingdom_ID[curr_step],
				'to-category': jQuery('#kingdom-to-category').val()
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function(response) {
					if(response.status == 'valid'){
						++loaded;
						
						labelCurr.text(loaded);	
						
						// status bar 
						var progressCount = parseInt((loaded / (numberOfItems))  * 100);
						jQuery('#progress_bar .ui-progress').animateProgress({
							progress : progressCount,
							duration : 300,
							easing   : 'swing'
						});
						
						// continue insert the rest of ID
						if(numberOfItems > curr_step) kingdom_insert_new_product(++curr_step);
						
						jQuery('#pcp-response-' + data.ID ).html( response.html );
						
					}else{
						alert(response.msg);
					}
				}
			});
		}
		
		// run for first 
		if(numberOfItems > 0) kingdom_insert_new_product(0);
	});
});


(function( $ ){
    // Simple wrapper around jQuery animate to simplify animating options.progress from your app
    // Inputs: options.progress as a percent, Callback
    // TODO: Add options and jQuery UI support.
    $.fn.animateProgress = function(options, callback) { 
        
        return this.each(function() {
            
            var progress = options.progress;
            $(this).animate({
                width: options.progress + '%'
            }, {
                duration: options.duration, 
        
                // swing or linear
                easing: options.easing,

                // this gets called every step of the animation, and updates the label
                step: function( progress ){
                    var labelEl = $('.ui-label'),
                    valueEl = labelEl.find('.value');
          
                    if (Math.ceil(progress) < 20 && $('.ui-label', this).is(":visible")) {
                        labelEl.hide();
                    }else{
                        if (labelEl.is(":hidden")) {
                            labelEl.fadeIn();
                        };
                    }
                    valueEl.text((progress.toFixed(1)) + '%');
                    
                },
                complete: function(scope, i, elem) {
                    if (callback) {
                        callback.call(this, i, elem );
                    };
                }
            });
        });
    };
})( jQuery );