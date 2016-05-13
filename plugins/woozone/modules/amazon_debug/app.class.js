// Initialization and events code for the app
wwcAmzAffAmazonDebug = (function($) {
	"use strict";

	// public
	var debug_level = 0;
	var loading = $('<div id="wwcAmzAff-ajaxLoadingBox" class="wwcAmzAff-panel-widget">loading</div>'); // append loading
	
	// init function, autoload
	function init() {
		// load the triggers
		$(document).ready(function() {
			triggers();
		});
	};
	
	// check all!
	var checkall_notify = {
			default: function( wrapper ) {
				var self = this;
				
				var wrapper = wrapper + ' ' || '';
				var per_one = wrapper + "li input[name='wwcAmzAff-amzdbg-rg[]']",
					per_all = wrapper + "input[name='wwcAmzAff-amzdbg-rg[all]']";
 
				// select all checkbox status based on item checkboxes
				if ( $(per_one+":checked").length == $(per_one).length ) {
					$(per_all).prop('checked', true);
				} else {
					$(per_all).prop('checked', false);
				}
			},
			
			triggers: function( wrapper ) {
				var self = this;
				
				var wrapper = wrapper + ' ' || '';
				var per_one = wrapper + "li input[name='wwcAmzAff-amzdbg-rg[]']",
					per_all = wrapper + "input[name='wwcAmzAff-amzdbg-rg[all]']";
					
				self.default( wrapper );
				
				// select all checkbox - click
				$('body').on('click', per_all + ', ' + per_all+' ~ label', function () {
					var that = $(this), elType = that.prop('tagName').toUpperCase();
	
					var allStatus = that.prop('checked');
					$(per_all).prop('checked', allStatus);
					$(per_one).prop('checked', allStatus);
					
					if ( allStatus ) {
						$(per_one).parent().find('a').addClass('on');
					} else {
						$(per_one).parent().find('a').removeClass('on');
					}
				});
				
				// select item checkbox - click
				$('body').on('click', per_one + ', ' + per_one+' ~ label', function () {
					var that = $(this), elType = that.prop('tagName').toUpperCase();
					
					var status = that.prop('checked');
					
					// select all checkbox status based on individul checkboxes
					if ( $(per_one+":checked").length == $(per_one).length ) {
						$(per_all).prop('checked', true);
					} else {
						$(per_all).prop('checked', false);
					}
					
					if ( status ) {
						that.parent().find('a').addClass('on');
					} else {
						that.parent().find('a').removeClass('on');
					}
				});
			}
	};
	
	function make_request() {
		ajaxLoading( 'show' );

		var data = [];
		
		// action
		data.push({
			name	: 'action',
			value	: 'wwcAmzAffAmazonDebugGetResponse'
		});
		
		// asin
		data.push({
			name	: 'asin',
			value	: $('#wwcAmzAff-amzdbg-asin').val()
		});
		
		// response groups
		var rg = [];
		var per_one = "#wwcAmzAff-amazonDebug li input[name='wwcAmzAff-amzdbg-rg[]']";
		$(per_one + ':checked').each(function (i, el) {
			rg.push( $(el).val() );
		});
		data.push({
			name	: 'rg',
			value	: rg
		});
		
		// turn the result into a query string
		//console.log( data ); return false;
		data = $.param( data );
 
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: data,
			dataType: 'json',
			success: function(response) {
				if( typeof response.status != 'undefined' && response.status == 'valid' ) {
					var jsonStr = response.html;
					//jsonStr = jsonStr.toString();
					//jsonStr = JSON.parse(jsonStr);
					//jsonStr = JSON.stringify(jsonStr, null, '\t');

 					/*					
					jsonStr = '<pre><code class="json">' + jsonStr + '</code></pre>';
					var $respWrap = $('#wwcAmzAff-amzdbg-amazonResponse').html( jsonStr );
					// highlight.js
					hljs.configure({
						tabReplace		: '    ' // 4 spaces
					});
					//hljs.initHighlightingOnLoad();
					$('pre code').each(function(i, block) {
  						hljs.highlightBlock(block);
					});
					*/
					
					var $respWrap = $('#wwcAmzAff-amzdbg-amazonResponse #RawJson').html( jsonStr );
					$('#wwcAmzAff-amzdbg-amazonResponse #GoFormatJson').trigger('click');
 
					ajaxLoading( 'remove' );
				}
			}
		});
	}
	
	function ajaxLoading(status) 
    {
    	if( status == 'show' ){
        	$('#wwcAmzAff-amzdbg-amazonResponse').append( loading );
       	}
       	else{
       		$('#wwcAmzAff-amzdbg-amazonResponse #wwcAmzAff-ajaxLoadingBox').remove();
       	}
    }
	
	function triggers() {
		// check all
		checkall_notify.triggers('#wwcAmzAff-amazonDebug');
		
		// get response
		$('#wwcAmzAff-amazonDebug #wwcAmzAff-amzdbg-getAmzResponse').click(function(e) {
			e.preventDefault();
			
			make_request();	
		});
		
		// restore to default response groups
		$('#wwcAmzAff-amazonDebug #wwcAmzAff-amzdbg-rg-godefault').click(function(e) {
			e.preventDefault();
			
			var groupsDefault = $('#wwcAmzAff-amazonDebug #wwcAmzAff-amzdbg-default').val();
			groupsDefault = groupsDefault.split(',');
			
			var per_one = "#wwcAmzAff-amazonDebug li input[name='wwcAmzAff-amzdbg-rg[]']",
				per_all = "#wwcAmzAff-amazonDebug input[name='wwcAmzAff-amzdbg-rg[all]']";

			$(per_all).prop('checked', false);
			$(per_one).prop('checked', false);
			$(per_one).each(function(i, el) {
				var that = $(el),
					group = that.val();

				if ( $.inArray(group, groupsDefault) > -1 ) {
					that.prop('checked', true);
				}
				
				var status = that.prop('checked');
				if ( status ) {
					that.parent().find('a').addClass('on');
				} else {
					that.parent().find('a').removeClass('on');
				}
			});
		});
		
		console.log( $("#wwcAmzAff-amazonDebug #wwcAmzAff-amzdbg-amazonResponse") ); 
	};
	
	init();

	// external usage
	return {
		"ajaxLoading": ajaxLoading
	};
})(jQuery);