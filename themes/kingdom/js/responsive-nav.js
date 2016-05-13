/*!
 * jQuery ResponsiveNav plugin
 * ---------------------------
 * Author: AA-Team
 */

;(function ( $, window, document, undefined ) {
   "use strict";
   
    // Create the defaults once
    var pluginName = 'ResponsiveNav',
        defaults = {
        	minScreenWidth: 600,
        	menuClass: 'main-menu-mobile',
            wrapAfter: ''
        };
 
    // The plugin constructor
    function Plugin( element, options ) {
    	var self = this;
        this.element = $(element);
        this.elementClone = this.element.clone();
        this.options = $.extend( {}, defaults, options);
        this.wrapElm = '';
        this.selectList = null;
        
        this._defaults = defaults;
        this._name = pluginName;
        
        self.init();
        $(window).resize(function() {
        	self.init();
        });
    }

    Plugin.prototype.init = function () {
    	this.wrapElm = $(this.options.wrapAfter);
    	
    	if( this.checkWrapperWidth() === true ){
    		this.generateSelectList();
    		this.replaceUlWithSelect();
    		
    		// Go to selected page
    		this.selectList.change(function(){
    			window.location = $(this).val();
    		});
    	}
    	else{
    		this.RollBackOriginal();	
    	}
    };
    
    Plugin.prototype.replaceUlWithSelect = function () {
    	var self = this;
		this.element.html( this.selectList );
    }
    
    Plugin.prototype.RollBackOriginal = function () {
    	var self = this;
		this.element.html( this.elementClone );
    }
    
    Plugin.prototype.generateSelectList = function () {
    	var self = this;
    	this.selectList = $("<select />");
    	
    	this.selectList.addClass(this.options.menuClass);
    	
    	/* Navigate to populate options */
    	this.elementClone.children('ul').children('li').each(function () {
    		/* Get top-level link and text */
            var href = $(this).children('a').attr('href');
            var text = $(this).children('a').text();
            
            /* Append this option to our "select" */
            if ($(this).is(".on") && href != '#') {
                self.selectList.append('<option value="'+ ( href ) +'" selected>'+ ( text ) +'</option>');
            } 
            else if (href == '#') {
				self.selectList.append('<option value="'+ ( href ) +'" disabled="disabled">'+ ( text ) +'</option>');
            } 
            else {
            	self.selectList.append('<option value="'+ ( href ) +'">'+ ( text ) +'</option>');
            }
            
	        /* Check for "children" and navigate for more options if they exist */
            if ($(this).children('ul').length > 0) {
                $(this).children('ul').children('li').each(function() {
		            /* Get child-level link and text */
	                var href = $(this).children('a').attr('href');
	                var text = $(this).children('a').text();
	
	                /* Append this option to our "select" */
	                if ($(this).is(".current-menu-item") && href != '#') {
	                   self.selectList.append('<option value="'+ ( href ) +'" selected> - '+ ( text ) +'</option>');
	                }
	                else if (href == '#') {
	                    self.selectList.append('<option value="'+ ( href ) +'" disabled="disabled"># '+ ( text ) +'</option>');
	                }
	                else {
	                    self.selectList.append('<option value="'+ ( href ) +'"> - '+ ( text ) +'</option>');
	                }
           		});
           	}
    	});
    }
    
    Plugin.prototype.checkWrapperWidth = function () {
    	var wrapSize = this.wrapElm.width(); 
    	if( wrapSize > 0 && wrapSize < this.options.minScreenWidth ){
    		return true;
    	}
    	
    	return false;
    };

    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            new Plugin( this, options );
        });
    }

})( jQuery, window, document );