/*
Document   :  Asset Download
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
wwcAmzAffASINGrabber = (function($) {
	"use strict";

	// public
	var debug_level = 0;
	var maincontainer = null;
	var loading = null;

	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function() {
			maincontainer = $(".wwcAmzAff-asin-grabber");
			loading = maincontainer.find("#wwcAmzAff-main-loading");
			triggers();
		});
	})();

	function add_to_queue(form) {
		$.post(ajaxurl, {
			'action': 'wwcAmzAffLoadSection',
			'section': 'csv_products_import',
			'debug_level': debug_level
		}, function(response) {
			if (response.status == 'ok') {

				var response_html = $(response.html);
				response_html.attr('id', 'wwcAmzAff-import-queue-html');
				$("#wwcAmzAff-import-queue-html").remove();
				response_html.find("#wwcAmzAff-csv-asin").val(form.find("textarea").val());

				maincontainer.find("#wwcAmzAff-content").find("#wwcAmzAff-content-area").append(response_html);
				response_html.find("#wwcAmzAff-addASINtoQueue").click();
			}

			loading.hide();
		}, 'json');
	}

	function grabb_asins(form) {
		var nb_asins = form.find(".wwcAmzAff-number-of-results").val();

		if( nb_asins == 0 ){
			nb_asins = parseInt( $(".wwcAmzAff-custom-nr-pages").val() );
		}

		if( nb_asins == 0 ){
			alert("Please select a number of pages greater than 0!");
			return;
		}

		var original_url = form.find("input[name='wwcAmzAff[grabb-url]']").val();
		var cc = 1;

		function grab_asin_page(form, cc) 
		{
			//form.find("input[name='wwcAmzAff[grabb-url]']").val(original_url + "&pg=" + cc)
			form.find("input[name='wwcAmzAff[grabb-url]']").val(original_url)

			$.post(ajaxurl, {
				'action': 'wwcAmzAff_grabb_asins',
				'params': form.serialize(),
				'debug_level': debug_level
			}, function(response) {
				$("#wwcAmzAff-grabb-asins").find("#wwcAmzAff-grabb-error").remove();

				if (response.status == 'valid') {
					var old_value = maincontainer.find("#wwcAmzAff-asin-codes textarea").val();

					if (old_value != "") {
						old_value = old_value + "\n";
					}

					maincontainer.find("#wwcAmzAff-asin-codes textarea").val(old_value + response.asins.join('\n'));
					$("#wwcAmzAff-asin-codes").show();
				}

				if (response.status == 'invalid') {
					$("#wwcAmzAff-asin-codes").hide();

					$("#wwcAmzAff-grabb-asins").append("<div id='wwcAmzAff-grabb-error' class='wwcAmzAff-message wwcAmzAff-error'>" + ( response.msg ) + "</div>");
				}

				

				if ((cc * 1) < nb_asins) {
					cc++;

					grab_asin_page(form, cc);
				} else {
					form.find("input[name='wwcAmzAff[grabb-url]']").val(original_url);
					loading.hide();
				}

			}, 'json');
		}
		
		grab_asin_page(form, cc);

	}

	function triggers() {
		maincontainer.on("click", '#wwcAmzAff-grabb-asins #wwcAmzAff-grabb-button', function(e) {
			e.preventDefault();
			var that = $(this)
			loading.show();
			grabb_asins(maincontainer.find('#wwcAmzAff-grabb-asins'));
		});

		maincontainer.on("click", '#wwcAmzAff-import-to-queue', function(e) {
			e.preventDefault();
			var that = $(this)
			loading.show();
			add_to_queue(maincontainer.find('#wwcAmzAff-asin-codes'));
		});

		maincontainer.on("change", '.wwcAmzAff-number-of-results', function(e) {
			var that = $(this),
					val = that.val();

			if( val == 0 ){
				$(".wwcAmzAffCustomNrPages").show();
			}else{
				$(".wwcAmzAffCustomNrPages").hide();
			}
		});
		
	}

	// external usage
	return {
		//"asin_grabber": asin_grabber
	}
})(jQuery);