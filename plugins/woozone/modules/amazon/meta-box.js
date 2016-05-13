jQuery(document).ready(function($) {

	var wwcAmzAff_launch_search = function (data) {
		var searchAjaxLoader 	= jQuery("#wwcAmzAff-ajax-loader"),
			searchBtn 			= jQuery("#wwcAmzAff-search-link");
			
		searchBtn.hide();	
		searchAjaxLoader.show();
		
		var data = {
			action: 'amazon_request',
			search: jQuery('#wwcAmzAff-search').val(),
			category: jQuery('#wwcAmzAff-category').val(),
			page: ( parseInt(jQuery('#wwcAmzAff-page').val(), 10) > 0 ? parseInt(jQuery('#wwcAmzAff-page').val(), 10) : 1 )
		};
		
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			jQuery("#wwcAmzAff-ajax-results").html(response);
			
			searchBtn.show();	
			searchAjaxLoader.hide();
		});
	};
	
	jQuery('body').on('change', '#wwcAmzAff-page', function (e) {
		wwcAmzAff_launch_search();
	});
	
	jQuery("#wwcAmzAff-search-form").submit(function(e) {
		wwcAmzAff_launch_search();
		return false;
	});
	
	jQuery('body').on('click', 'a.wwcAmzAff-load-product', function (e) {
		e.preventDefault();
		
		var data = {
			'action': 'wwcAmzAff_load_product',
			'ASIN':  jQuery(this).attr('rel')
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
});