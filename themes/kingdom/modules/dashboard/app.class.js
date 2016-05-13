/*
Document   :  Dashboard
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
kingdomDashboard = (function ($) {
    "use strict";

    // public
    var debug = false;
    var maincontainer = null;

	// init function, autoload
	function init()
	{
		maincontainer = $("#kingdom-ajax-response");
		triggers();
	};
	
	function boxLoadAjaxContent( box )
	{
		var allAjaxActions = [];
		box.find('.is_ajax_content').each(function(key, value){
			
			var alias = $(value).text().replace( /\n/g, '').replace("{", "").replace("}", "");
			$(value).attr('id', 'kingdom-row-alias-' + alias);
			allAjaxActions.push( alias );
		}); 
		
		 
		jQuery.post(ajaxurl, {
			'action' 		: 'kingdomDashboardRequest',
			'sub_actions'	: allAjaxActions.join(","),
			'prod_per_page'	: box.find(".kingdom-numer-items-in-top").val(),
			'debug'			: debug
		}, function(response) {
			$.each(response, function(key, value){
				if( value.status == 'valid' ){
					var row = box.find( "#kingdom-row-alias-" + key );
					row.html(value.html);
					
					row.removeClass('is_ajax_content');
					
					tooltip();
				} 
			});
			
		}, 'json');
	}
	
	function tooltip()
	{
		var xOffset = -30,
			yOffset = -300,
			winW 	= $(window).width();
		
		$(".kingdom-aa-products-container ul li a").hover(function(e){
			
			var that = $(this),
				preview = that.data('preview');

			$("body").append("<p id='kingdom-aa-preview'>"+ ( '<img src="' + ( preview ) + '" >' ) +"</p>");
			
			var new_left = e.pageX + yOffset;
			
			if( new_left > (winW - 640) ){
				new_left = (winW - 640)
			}
			$("#kingdom-aa-preview")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(new_left) + "px")
				.fadeIn("fast");
	    },
		function(){
			this.title = this.t;
			$("#kingdom-aa-preview").remove();
	    });
		
	
		$(".kingdom-aa-products-container ul li a").mousemove(function(e){
			
			var new_left = e.pageX + yOffset;
			if( new_left > (winW - 640) ){
				new_left = (winW - 640)
			}
			
			$("#kingdom-aa-preview")
				.css("top",(e.pageY - xOffset) + "px")
				.css("left",(new_left) + "px");
		});
	}

	
	function triggers()
	{
		maincontainer.find(">div").each( function(e){
			var that = $(this);
			// check if box has ajax content
			if( that.find('.is_ajax_content').size() > 0 ){
				boxLoadAjaxContent(that);
			}
		});
		
		maincontainer.find(".kingdom-numer-items-in-top").on('change', function(){
			var that = $(this),
				box = that.parents('.kingdom-dashboard-status-box').eq(0);
			
			box.find('.kingdom-dashboard-status-box-content').addClass('is_ajax_content').html('{products_performances}');
			 
			boxLoadAjaxContent(box);
		});
		
		$(".kingdom-aa-products-tabs").on('click', "li:not(.on) a", function(e){
			e.preventDefault();
			
			var that = $(this),
				alias = that.attr('class').split("items-"),
				alias = alias[1];
			
			$('.kingdom-aa-products-container').hide();
			$("#aa-prod-" + alias).show();
			
			$(".kingdom-aa-products-tabs").find("li.on").removeClass('on');
			that.parent('li').addClass('on');
		});
	}
	
	// external usage
	return {
		"init": init
    }
})(jQuery);
