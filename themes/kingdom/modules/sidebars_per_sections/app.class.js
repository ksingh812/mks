/*
Document   :  kingdomSidebarsPerPages
Author     :  AA-Team http://themeforest.net/user/AA-Team
*/
// Initialization and events code for the app
kingdomSidebarsPerPages = (function ($) {
    "use strict";

    // public
    var debug_level = 0;
    var maincontainer = null;

	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $(".kingdom-panel");
			triggers();
		});
	})();
	
	function changeToPanel( elm )
	{
		var section = elm.data('section'),
			section_obj = $("#kingdom-panel-" + section);
		
		$(".kingdom-panel-sections-choose").hide();
		
		section_obj.show();
		
		
		$('#kingdom-sections-choose a.on').removeClass('on');
		elm.addClass('on');
	}
	
	function saveThePanel( elm )
	{
		elm.find("#kingdom-status-box").fadeIn('fast');
	}
	
	function triggers()
	{
		maincontainer.on('click', '#kingdom-sections-choose a', function(e) {
			e.preventDefault();
			changeToPanel( $(this) );
		});
		
		// click on first element
		$('#kingdom-sections-choose a').eq(0).click();
		
		maincontainer.on('submit', '#kingdom_sidebars_per_sections', function(e) {
			e.preventDefault();
			
			saveThePanel( $(this) );
		});
	}

	// external usage
	return {}
})(jQuery);