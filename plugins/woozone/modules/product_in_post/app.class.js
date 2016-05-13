/*
Document   :  Asset Download
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
wwcAmzAffProductInPost = (function($) {
	"use strict";

	// public
	var debug_level = 0;
	var loading = $('<div id="wwcAmzAff-ajaxLoadingBox" class="wwcAmzAff-panel-widget">loading</div>'); // append loading
	var _editor = null;
	
	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function() {
			//triggers();
		});
		
		if( typeof ajaxurl != "undefined" ){
			addButton(); 
		}
	})();

	function addButton()
	{
		
		tinymce.PluginManager.add( 'product_in_post', function( editor, url ) {
	        // Add a button that opens a window
	        editor.addButton( 'product_in_post', {
	            text: 'Add Products',
	            icon: 'product_in_post',
	            onclick: function() {
	            	createLightbox( editor );
	            }
	        } );
	    });
	}
	
	function createLightbox( editor )
	{
		_editor = editor;
		tb_show( 'Amazon Product to post/page', '#TB_inline?inlineId=wwcAmzAffAddProductInline' );
		//tb_position();
		tb_resize();
		triggers_load_products();
	}
	
	function tb_resize()
	{
		function resize(){
			var tbWindow = $('#TB_window'),
				tb_width = tbWindow.width(),
				tb_height = tbWindow.height();
			
			$('#TB_ajaxContent').css({
				'width': (tb_width - 40) + "px",
				'height': (tb_height - 50) + "px"
			});
			
			$(".wwcAmzAffAllProducts").css({
				'height': (tb_height - 80) + "px"
			});
		}
		resize();
		
		$(window).on('resize', function(){
			resize();
		});
	}
	
	function ajaxLoading(status) 
    {
    	if( status == 'show' ){
        	$("#wwcAmzAffAddProduct").append( loading );
       	}
       	else{
       		$("#wwcAmzAff-ajaxLoadingBox").remove();
       	}
    }
    
    function trigger_select_products()
    {
    	$("#wwcAmzAffListOfProducts .list-of-products").on('click', 'a', function(e){
    		e.preventDefault();
    		
    		var that = $(this),
    			postid = that.data('postid');
    		
    		if( that.hasClass('added') ){
    			alert('Already added!');
    			return;
    		}
    		that.addClass('added');
    		var chosedProducts = $(".wwcAmzAffChosenProducts ul");
    		
    		chosedProducts.find("li.product-note").hide();
    		chosedProducts.append('<li data-prodid="' + ( postid ) + '"><a href="#"><span><img src="' + ( that.find('img').attr('src') ) + '" class="product-post-image" /></span><div class="product-mask">' + ( that.find('h3').text() ) + '</div><span class="product-delete-box"><em>delete</em></span></a></li>');
    		
    		that.find(".product-tick-box").remove();
    		that.append('<div class="product-tick-box"><em>tick</em><div>');
    		
    		
    		chosedProducts.sortable();
    	});
    	
    	$(".wwcAmzAffChosenProducts ul").on('click', "a em", function(e){
    		e.preventDefault();
    		
    		var that = $(this),
    			parent = that.parents('li').eq(0);
    			
    		parent.remove();
    		
    		$('#list-product-' + ( parent.data('prodid') ))
    		.removeClass('added')
    		.find('.product-tick-box').remove(); 
    		
    		var left_prod = $('#list-product-' + ( parent.data('prodid') ));
    		
    		if( $(".wwcAmzAffChosenProducts ul li").size() <= 1 ){
    			$(".wwcAmzAffChosenProducts ul li.product-note").show();
    		}
    	});
    	
    	$(".wwcAmzAffChosenProducts").on('click', 'input.button', function(e){
    		e.preventDefault();
    		
    		var asins = [];
    		
    		$(".wwcAmzAffChosenProducts").find('li:not(.product-note)').each(function(){
    			asins.push($(this).find('.product-mask').text());
    		});
    		
    		_editor.insertContent( '[wwcAmzAffProducts asin="' + ( asins.join(",")) + '"][/wwcAmzAffProducts]' );
    		
    		tb_remove();
    		
    		$("#wwcAmzAffAddImportedProducts").html('');
    	});
    }
    
    function triggers_load_products()
    {
    	/*$("#wwcAmzAffAddProduct").on('click', ".wwcAmzAffChooseMenu a", function(e){
    		e.preventDefault();
    		
    		var that = $(this),
    			rel = that.attr('rel'),
    			rel_elm = $( "#" + rel );
    		
    		ajaxLoading( 'show' );
    		
    		$(".wwcAmzAffChooseMenu a.on").removeClass('on');
    		that.addClass('on');
    		
    		if( rel == 'wwcAmzAffAddImportedProducts' ){*/
    			$.post(ajaxurl, {
    				'action': 'wwcAmzAffProductInPost',
    				'subaction': 'load-products',
    				'categ': 'all'
    			}, function(response) {
    				
    				if( response.status == 'valid' ){
    					$("#wwcAmzAffAddImportedProducts").html( response.html );
    					
    					var tbWindow = $('#TB_window'),
							tb_width = tbWindow.width(),
							tb_height = tbWindow.height();
				
    					$(".wwcAmzAffAllProducts").css({
							'height': (tb_height - 80) + "px"
						});
    				}
    				
    				ajaxLoading( 'remove' );
				}, 
				'json');
				
				$("#wwcAmzAffAddAsinsCode").hide();
    			$("#wwcAmzAffAddImportedProducts").show();
    		/*}
    		
    		if( rel == 'wwcAmzAffAddAsinsCode' ){
    			$("#wwcAmzAffAddAsinsCode").show();
    			$("#wwcAmzAffAddImportedProducts").hide();
    			
    			ajaxLoading( 'remove' );
    		}
    	});*/
    }
    

	// external usage
	return {
		"trigger_select_products": trigger_select_products,
		"ajaxLoading": ajaxLoading
	}
})(jQuery);