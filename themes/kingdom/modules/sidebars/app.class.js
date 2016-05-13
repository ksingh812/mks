/*
Document   :  kingdomSidebars
Author     :  AA-Team http://themeforest.net/user/AA-Team
*/
// Initialization and events code for the app
kingdomSidebars = (function ($) {
    "use strict";

    // public
    var debug_level = 0;
    var maincontainer = null;
    
    var sidebar_row = 0;

	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $(".kingdom-panel");
			sidebar_row = parseInt($("#kingdom-sidebars-nr").val()) + 1;
    
			triggers();
		});
	})();
	
	
	function deleteSidebar( $btn )
	{
		var contentDom 	= jQuery("#kingdom-panel-content-step"),
			parentRow = $btn.parents('.kingdom-form-row.ui-sortable-handle');
		
		if (confirm('Are you sure to delete this sidebar?')) {
			console.log( parentRow ); 
			parentRow.remove();
			if(contentDom.find('.kingdom-form-row').size() == 0) {
				
				contentDom.find('#kingdom-ingredient-no-items').show();
				
				sidebar_row = 0;
			}
			
			sidebar_row++;
		}
	}
	
	function makeSortableSidebar() 
	{
		jQuery(function() {
			jQuery( "div#kingdom-panel-content-ingredient" ).sortable({
				placeholder: "kingdom-form-row-fake-ingredient",
				stop: function() {
				}
			});
		});
	}
	
	function makeSortableSidebar() 
	{
		jQuery(function() {
			jQuery( "div#kingdom-panel-content-sidebar" ).sortable({
				placeholder: "kingdom-form-row-fake-sidebar",
				stop: function() {
				}
			});
		});
	}
	
	function addNewSidebar() 
	{
		var contentDom 	= jQuery("#kingdom-panel-content-sidebar"),
			lastRow 	= contentDom.find('.kingdom-form-row').last(),
			template_step = maincontainer.find("#kingdom-template-sidebar"),
			clone = template_step.clone();
			
		clone.find('input,textarea').each(function(){
			var that = $(this),
				name = that.data('name');
			that.attr('name', "sidebar[" + ( sidebar_row ) + "][" + ( name ) + "]");
		});
		 
		// append new posible step
		if(contentDom.find('.kingdom-form-row').size() > 0){
			lastRow.after( clone.html() );
		}else{
			contentDom.append( clone.html() );
			contentDom.find('#kingdom-sidebar-no-items').hide();
		}
		makeSortableSidebar();
		sidebar_row++;
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
	
	function triggers()
	{
		maincontainer.on('click', '#kingdom-add-new-sidebar', function(e) {
			e.preventDefault();
			addNewSidebar();
		});
		
		maincontainer.on('click', 'a.sidebar-delete-btn', function(e) {
			e.preventDefault();
			deleteSidebar(jQuery(this));
		});
		
		makeSortableSidebar();
	}

	// external usage
	return {}
})(jQuery);