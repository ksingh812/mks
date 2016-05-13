/*
Document   :  Amazon Advanced Search
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/
// Initialization and events code for the app
wwcAmzAffAdvancedSearch = (function ($) {
    "use strict";

    // public
    var ASINs = [];
    var loaded_products = 0;
    var debug_level = 0;

	// init function, autoload
	(function init() {
		// init the tooltip
		tooltip();

		// load the triggers
		$(document).ready(function(){
			var loading = $("#wwcAmzAff-advanced-search #main-loading");

			triggers();
 
			load_categ_parameters( $(".wwcAmzAff-categories-list li.on a") );

			// show debug hint
			console.log( '// want some debug?' );
			console.log( 'wwcAmzAffAdvancedSearch.setDegubLevel(1);' );
		});
	})();

	function load_categ_parameters( that )
	{
		var loading = $("#wwcAmzAff-advanced-search #main-loading");
		loading.css('display', 'block');
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, {
			'action' 		: 'wwcAmzAffCategParameters',
			'categ'			: that.data('categ'),
			'nodeid'		: that.data('nodeid'),
			'debug_level'	: debug_level
		}, function(response) {
			if( response.status == 'valid' ){
				$('#wwcAmzAff-parameters-container').html( response.html );

				// clear the products from right panel
				$(".wwcAmzAff-product-list").html('');
			}

			loading.css('display', 'none');
		}, 'json');
	}

	function updateExecutionQueue()
	{
		var queue_list = $("input.wwcAmzAff-items-select");
		if( queue_list.length > 0 ){
			$.each( queue_list, function(){
				var that = $(this),
					asin = that.val();

				if( that.is(':checked') ){
					// if not in global asins storage than push to array
					if( $.inArray( asin, ASINs) == -1 ){
						ASINs.push( asin );
					}
				}

				if( that.is(':checked') == false ){
					// if not in global asins storage than push to array
					if( $.inArray( asin, ASINs) > -1){
						// remove array key by value
						ASINs.splice( ASINs.indexOf(asin), 1 );
					}
				}
			});

		}else{
			// refresh the array list
			ASINs = [];
		}

		// update the queue list DOM
		if( ASINs.length > 0 ){
			var newHtml = [];
			$.each( ASINs, function( key, value ){
				var original_img = $("img#wwcAmzAff-item-img-" + value);

				if( original_img.length > 0 ){
					newHtml.push( '<a href="#' + ( value ) + '" class="removeFromQueue" title="Remove from Queue">' );
					newHtml.push( 	'<img src="' + ( original_img.attr('src') ) + '" width="30" height="30">' );
					newHtml.push( 	'<span></span>' );
					newHtml.push( '</a>' );
				}
			});

			// append the new html DOM elements to queue container
			$("#wwcAmzAff-execution-queue-list").html( newHtml.join( "\n" ) );
		}

		// clear the execution queue if not ASIN(s)
		else{
			$("#wwcAmzAff-execution-queue-list").html( 'No item(s) yet' );

			// uncheck "select all" if need
			if( jQuery("#wwcAmzAff-items-select-all").is(':checked') ){
				jQuery("#wwcAmzAff-items-select-all").removeAttr('checked');
			}
		}
	}

	function launchSearch( that, reset_page )
	{
		var loading = $("#wwcAmzAff-advanced-search #main-loading");
		loading.css('display', 'block');

		// get the current browse node
		var current_node = '';
		jQuery("#wwcAmzAffGetChildrens select").each(function(){
		    var that_select = jQuery(this);

		    if( that_select.val() != "" ){
		        current_node = that_select.val();
		    }
		});

		var page = $("select#wwcAmzAff-page").val() > 0 ? parseInt($("select#wwcAmzAff-page").val(), 10) : 1;
		if( reset_page == true ){
			page = 1;
		}
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, {
			'action' 		: 'wwcAmzAffLaunchSearch',
			'params'		: that.serialize(),
			'page'			: page,
			'node'			: current_node,
			'debug_level'	: debug_level
		}, function(response) {

			ASINs = [];
			$(".wwcAmzAff-product-list").html( response );
			jQuery("#wwcAmzAff-items-select-all").click();
			loading.css('display', 'none');
		}, 'html');
	}

	function tailProductImport( import_step, callback )
	{
		//console.log( import_step ); 
		// stop if not valid ASINs key
		if(typeof ASINs[import_step] == 'undefined') return false;

		var asin = ASINs[import_step];

		// increse the loaded products marker
		++loaded_products;
		
		// make the import
		jQuery.post(ajaxurl, {
			'action' 		: 'wwcAmzAffImportProduct',
			'asin'			: asin,
			'category'		: $(".wwcAmzAff-categories-list li.on a").data('categ'),
			'to-category'	: $("#wwcAmzAff-to-category").val(),
			'debug_level'	: debug_level
		}, function(response) {

			if( typeof response.status != 'undefined' && response.status == 'valid' ) {
				// show the download assets lightbox
				if( response.show_download_lightbox == true ){
					$("#wwcAmzAff-wrapper").append( response.download_lightbox_html );
					
					wwcAmzAffAssetDownload.download_asset( $('.wwcAmzAff-images-tail').find('li').eq(0), undefined, 100, function(){
						
						$(".wwcAmzAff-asset-download-lightbox").remove();
				
						jQuery('a.removeFromQueue[href$="#' + ( asin ) + '"]').html( '<span class="success"></span>' );

						// continue insert the rest of ASINs
						if( ASINs.length > import_step ) tailProductImport( ++import_step, callback );
		
						// execute the callback at the end of loop
						if( ASINs.length == import_step ){
							callback( loaded_products );
						}
					} );
				}
				else{
					jQuery('a.removeFromQueue[href$="#' + ( asin ) + '"]').html( '<span class="success"></span>' );

					// continue insert the rest of ASINs
					if( ASINs.length > import_step ) tailProductImport( ++import_step, callback );
	
					// execute the callback at the end of loop
					if( ASINs.length == import_step ){
						callback( loaded_products );
					}
				}
			} else {
				// alert('Unable to import product: ' + asin );
				// return false;
				
				var errMsg = '';
				if ( typeof response.status != 'undefined' )
					errMsg = response.msg;
				else
					errMsg = 'unknown error occured: could be related to max_execution_time, memory_limit server settings!';
 
				jQuery('a.removeFromQueue[href$="#' + ( asin ) + '"]').html( '<span class="error"></span>' );
				jQuery('.wwcAmzAff-queue-table').find('tbody:last').append('<tr><td colspan=3>' + errMsg + '</td></tr>');

				// continue insert the rest of ASINs
				if( ASINs.length > import_step ) tailProductImport( ++import_step, callback );
	
				// execute the callback at the end of loop
				if( ASINs.length == import_step ){
					callback( loaded_products );
				}
			}

		}, 'json');
	}

	// public method
	function launchImport( that )
	{
		var loading = $("#wwcAmzAff-advanced-search #main-loading");
		loading.css('display', 'block');
		if( ASINs.length == 0 ){
			alert( 'First please select products from the list!' );
			loading.css('display', 'none');
			return false;
		}
  
		tailProductImport( 0, function( loaded_products ){
			//console.log( 'done', loaded_products ) ;

			jQuery('body').find('#wwcAmzAff-advanced-search .wwcAmzAff-items-list tr.on').remove();
			loading.css('display', 'none');

			return true;
		});
	}

	function getChildNodes( that )
	{
		var loading = $("#wwcAmzAff-advanced-search #main-loading");
		loading.css('display', 'block');

		// prev element valud
		var ascensor_value = that.val(),
			that_index = that.index();

		// max 3 deep
		if ( that_index > 10 ){
			loading.css('display', 'none');
			return false;
		}

		var container = $('#wwcAmzAffGetChildrens');
		var remove = false;
		// remove items prev of current selected
		container.find('select').each( function(i){
			if( remove == true ) $(this).remove();
			if( $(this).index() == that_index ){
				remove = true;
			}
		});

		// store current childrens into array
		if( ascensor_value != "" ){
			// make the import
			jQuery.post(ajaxurl, {
				'action' 		: 'wwcAmzAffGetChildNodes',
				'ascensor'		: ascensor_value,
				'debug_level'	: debug_level
			}, function(response) {
				if( response.status == 'valid' ){
					$('#wwcAmzAffGetChildrens').append( response.html );

					loading.css('display', 'none');
				}
			}, 'json');

		}else{
			loading.css('display', 'none');
		}
	}

	function setDegubLevel( new_level )
	{
		debug_level = new_level;
		return "new debug level: " + debug_level;
	}

	function tooltip()
	{
		/* CONFIG */
		var xOffset = -40,
			yOffset = -250;

		/* END CONFIG */
		jQuery('body').on('mouseover', '.wwcAmzAff-tooltip', function (e) {
			var img_src = $(this).data('img');
			console.log( $(this), img_src ); 
			$("body").append("<img id='wwcAmzAff-tooltip' src="+ img_src +">");
			$("#wwcAmzAff-tooltip")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(e.pageX + yOffset) + "px")
				.show();
	  	});
		
		jQuery('body').on('mouseout', '.wwcAmzAff-tooltip', function (e) {
			$("#wwcAmzAff-tooltip").remove();
	    });
		jQuery('body').on('mousemove', '.wwcAmzAff-tooltip', function (e) {
			$("#wwcAmzAff-tooltip")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(e.pageX + yOffset) + "px");
		});
	}

	function triggers()
	{
		jQuery('body').on('click', '.wwcAmzAff-categories-list a', function (e) {
			e.preventDefault();

			var that = $(this),
				that_p = that.parent('li');

			// escape if is the same block
			if( that.parent('li').hasClass('on') ) return true;

			// get current clicked category paramertes
			load_categ_parameters(that);

			$(".wwcAmzAff-categories-list li.on").removeClass('on');
			that_p.addClass('on');
		});
		
		jQuery('body').on('change', 'select.wwcAmzAffParameter-sort', function (e) {
		    var that = $(this),
		        val = that.val(),
		        opt = that.find("[value=" + ( val ) + "]"),
		        desc = opt.data('desc');

		    $("p#wwcAmzAffOrderDesc").html( "<strong>" + ( val ) + ":</strong> " + desc );
		});

		// check / uncheck all
		jQuery('body').on('change', '#wwcAmzAff-items-select-all', function (e)
		{
			var that = $(this),
				selectors = $("input.wwcAmzAff-items-select");

			if( that.is(':checked') == true){

				selectors.each(function(){
					var sub_that = $(this),
						tr_parent = sub_that.parents('tr').eq(0);
					sub_that.attr('checked', 'true');
					tr_parent.addClass('on');
				});
			}else{
				selectors.each(function(){
					var sub_that = $(this),
						tr_parent = sub_that.parents('tr').eq(0);
					sub_that.removeAttr('checked');
					tr_parent.removeClass('on');
				});
			}

			// update the execution queue
			updateExecutionQueue();
		})

		// temp
		.click();
		
		jQuery('body').on('change', 'input.wwcAmzAff-items-select', function (e)
		{
			var that = $(this),
				tr_parent = that.parents('tr').eq(0);
			if( that.is(':checked') == false){
				tr_parent.removeClass('on');
			}else{
				tr_parent.addClass('on');
			}

			// update the execution queue
			updateExecutionQueue();
		});
		
		jQuery('body').on('click', '#wwcAmzAff-advanced-search .wwcAmzAff-items-list tr td:not(:last-child, :first-child)', function (e)
		{
			var that = $(this),
				tr_parent = that.parent('tr'),
				input = tr_parent.find('input');
			input.click();
		});
		
		jQuery('body').on('click', '#wwcAmzAff-advanced-search a.removeFromQueue', function (e) 
		{
			e.preventDefault();

			var that = $(this),
				href = that.attr('href').replace("#", ''),
				tr_parent = $('tr#wwcAmzAff-item-row-' + href),
				input = tr_parent.find('input');

			input.click();
		});
		
		jQuery('body').on('submit', '#wwcAmzAff_import_panel', function (e) {
			e.preventDefault();

			launchSearch( $(this), true );
		});
		
		jQuery('body').on('change', 'select#wwcAmzAff-page', function (e) {
			e.preventDefault();

			launchSearch( $("#wwcAmzAff_import_panel"), false );
		});

		jQuery('body').on('click', 'a#wwcAmzAff-advance-import-btn', function (e) {
			e.preventDefault();

			launchImport();
		});
		
		jQuery('body').on('change', '#wwcAmzAffGetChildrens select', function (e) {
			e.preventDefault();

			getChildNodes( $(this) );
		});
	}

	// external usage
	return {
		"setDegubLevel": setDegubLevel,
        "ASINs": ASINs,
        "launchImport": launchImport
    }
})(jQuery);