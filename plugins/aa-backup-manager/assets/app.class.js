/*
Document   :  diet_nutrition_theme Install
Author     :  AA-Team - http://themeforest.net/user/AA-Team
*/
// Initialization and events code for the app
diet_nutrition_themeInstall = (function ($) {
    "use strict";
    // public
    var debug_level = 0;
    var maincontainer = null;
	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $(".BKM_iw");
			triggers();
		});
	})();
	
	function fix_content_width()
	{
		maincontainer.css({
			'width': ( $("#wpbody").width() + 20 ),
			'height': ( $("#wpwrap").height() - 40 )
		});
	}
	
	function auto_refresh( nb_seconds )
	{
		setTimeout(function(){
			window.location = window.location;
			return;
		}, nb_seconds * 1000 );
	}
	
	function triggers()
	{
		$(window).resize(fix_content_width);
		fix_content_width();
		
		maincontainer.find("#export-iw-export").on('change', "input[name='all']", function(){
			var that = $(this),
				post_types = maincontainer.find("#export-iw-export").find("#iw-export-post-types").find("input");
				
			if( that.is(':checked') ){
				post_types.each(function(){
					$(this).attr("checked", "checked");
				});
			}else{
				post_types.each(function(){
					$(this).removeAttr("checked");
				});
			}
		});
		
		maincontainer.on('change', 'form.BKM_iw-existent-file select', function(e){
			
			var that = $(this),
				val  = that.val();

			if( val === 'none' ){
				maincontainer.find('form.BKM_iw-existent-file input').attr('disabled', true);
			}else{
				maincontainer.find('form.BKM_iw-existent-file input').attr('disabled', false);
			}
		});
		
		
		maincontainer.on('submit', '#export-iw-export', function(e){
			e.preventDefault();
			
			var that = $(this);
			maincontainer.find(".BKM_iw-loader").show();
			
			$.post(ajaxurl, {
				'action': 'BKM_export_content',
				'params': that.serialize()
			}, function(response) {
				maincontainer.find("#export-iw-export").html(response.html);
				maincontainer.find(".BKM_iw-loader").hide();
			}, 'json');
		
		});
	}
	// external usage
	return {
		'auto_refresh': auto_refresh
	}
})(jQuery);
