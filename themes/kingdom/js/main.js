var $ = jQuery;
var kingdom_sidebar_menu = function( $elm ){
	
	// layout fix
	$elm.each(function(){
		var that = $(this);
		
		that.find(' > li > .children').each(function(){
			var submenu = $(this),
				parent = submenu.parent('li').eq(0);
			
			parent.append('<span class="accordsuffix">+</span>');
		}); 
	});
	
	$elm.find('> li').on('click', '.accordsuffix', function(){
		var that = $(this),
			parent = that.parent('li').eq(0),
			submenu = parent.children('ul.children');
		
		if( ! parent.hasClass('open') ){
			
			// close last open menu
			//var last_open = $elm.find(".open");
			//last_open.children('ul.children').slideUp('fast');
			
			// open current submenu
			submenu.slideDown('fast');
			parent.addClass("open");
			that.text('-');
		} else {
			submenu.slideUp('fast');
			parent.removeClass("open");
			that.text('+');
		}
	});
};

var kdAfterUpdateEffect = function( slider )
{
	slider.find(".item").each(function(i){
		var that = $(this),
			speed = (i + 1) * 240;
		
		if( speed > 1200 ) speed = 1200;
		 
		that.animate({
			opacity: 1
		}, speed);
	});
	
	slider.css('background', 'none');
	slider.css('overflow', 'visible');
	
	if( slider.attr('id') == 'kd_footer_partners' ){
		slider.css('background', '#fafafa');
	}
	//background-color: ;
	
}

$(document).ready(function() {
	var choose = $('.variations select option').first().text();
	$('.kd_variations').click(function(){
		$('.kd_custom_select .btn .current_value').text( choose );
	});
	
	//$(".menu-main-menu-container").ResponsiveNav({
	//	'wrapAfter': '.navigationbar',
	//	'menuClass': 'kd_main_menu_mobile'
	//});
	var w = window.innerWidth;
	if( w <= 520 ) {
		$('.kd-mobilemenunav').click(function(e){
			e.preventDefault();
			$(this).parent().find('.menu-main-menu-container').slideToggle();
		});
	}
	
	var large_gallery = $("#kd_image_large_gallery");
	
	$('.kd_list_type .kd_grid').click(function(e) {
		e.preventDefault();
		$('#kd_products_listitems').removeClass('kd_product_list_list').addClass('kd_product_list_grid');

		$('.kd_list_type .kd_list').removeClass('on');
		$(this).addClass('on');

	});

	$('.kd_list_type .kd_list').click(function(e) {
		e.preventDefault();
		$('#kd_products_listitems').removeClass('kd_product_list_grid').addClass('kd_product_list_list');

		$('.kd_list_type .kd_grid').removeClass('on');
		$(this).addClass('on');
	});

	$("a.prettyPhoto, a.woocommerce-main-image").prettyPhoto({
		social_tools : null,
		theme: 'facebook',
		show_title: false
	});
	

	$("#kd_product_gallery").on('click', '.owl-item', function(e) {
		e.preventDefault();

		var that = $(this),
			pos = that.index();
			
		large_gallery.trigger('owl.goTo', pos);
	});

	function kdChangeThumbs() {
		var position = this.owl.currentItem;
		$("#kd_product_gallery").trigger('owl.goTo', position);
	};
	
	$('#myTab a').click(function(e) {
		e.preventDefault()
		$(this).tab('show')
	})

	$(".home .the-content .woocommerce").each(function(){
		var that = $(this);
		that.owlCarousel({
			navigation : true,
			pagination : false,
			items : 4,
			itemsDesktop : [1199,4],
			itemsDesktopSmall : [981,3],
			itemsTablet: [768,2],
			itemsTabletSmall: false,
			itemsMobile : [479,1],
			afterUpdate: kdAfterUpdateEffect(that)
		});
	});

	$("#kd_related_products").owlCarousel({
		navigation : true,
		pagination : false,
		items : 3,
		itemsDesktop : [1199,2],
		itemsDesktopSmall : [981,2],
		itemsTablet: [768,3],
		itemsTabletSmall: [600,1],
		itemsMobile : [479,1],
		itemsScaleUp: true,
		afterUpdate: kdAfterUpdateEffect( $("#kd_related_products") )
	});

	$("#kd_product_gallery").owlCarousel({
		navigation : false,
		pagination : false,
		items : 2,
	});
	
	large_gallery.owlCarousel({
		navigation : true,
		pagination : false,
		singleItem	: true,
		items : 1,
		navigationText: ["<span></span>", "<span></span>"],
		afterAction : kdChangeThumbs,
	});

	$("#kd_blog_slider").owlCarousel({
		navigation : true,
		pagination : false,
		items : 1,
		autoHeight: true,
		itemsDesktop : [1199,1],
		itemsDesktopSmall : [981,1],
		itemsTablet: [768,1],
		itemsTabletSmall: [600,1],
		itemsMobile : [479,1],
		
		afterUpdate: kdAfterUpdateEffect( $("#kd_blog_slider") )
	});

	$("#kd_testimonial_slider").owlCarousel({
		navigation : true,
		pagination : false,
		items : 1,
	});

	$("#kd_footer_partners").owlCarousel({
		navigation : true,
		pagination : false,
		items : 5,
		navigationText: ["<span></span>", "<span></span>"],
		afterUpdate: kdAfterUpdateEffect( $("#kd_footer_partners") )
	});

	$('a.kd_cart_item-close-btn, .product-remove a').tooltip({
		'placement' : 'bottom'
	});

	$("#kd_slider_range").slider({
		range : true,
		min : 0,
		max : 1800,
		values : [0, 900],
		slide : function(event, ui) {
			$("#kd_amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
		}
	});
	$("#kd_amount").val("$" + $("#kd_slider_range").slider("values", 0) + " - $" + $("#kd_slider_range").slider("values", 1));
	
	$(".nav-tabs").on('click', 'li a', function(e){
		e.preventDefault();
		
		var that = $(this);
		
		if( !that.parent("li").hasClass('active') ){
			
			$(".nav-tabs").find('li.active').removeClass('active');
			that.parent('li').addClass("active");
			
			$(".entry-content.active").removeClass('active');
			$("#" + that.attr('href').replace("#", "")).addClass("active")
		}
	});
	
	// Save Rating
	$('.rating-input').on('click', 'span', function(e)
	{
		var that = $(this),
			value = that.data('value'),
			parent = that.parent('.rating-input').find("input");
			
		jQuery.post(ajaxurl, {
			'action': 'kingdom_save_stars',
			'value': value,
			'productid': parent.data('productid')
		}, function() {
		}, 'json');
	});
	
	if( kingdom_isMobile.any ) {
		
		$('.kd_custom_select select').css('cssText', 'display:inline;');
		$('.kd_custom_select .btn.dropdown-toggle').hide();
		
	}else{
		
		var customSelect = function() { 
			$(".kd_custom_select").each(function(){
				var that = $(this),
					select_box = that.find("select"),
					list = that.find('ul'),
					options = select_box.find('option'),
					options_size = options.size();
				
				// check if don't have any selected item
				var has_selected = true;
				if( options.find('li:selected').size() == 0 ){
					has_selected = false;
				}
				
				options.each(function(i){
					var li = $(this);
					list.append('<li><a href="#' + ( li.val() ) + '">' + ( li.text() ) + '</a></li>');
					
					if( i < (options_size - 1) )
						list.append('<li class="divider"></li>');
					
					if( li.attr('selected') == 'selected' || ( has_selected == false && i == 0 ) ){
						that.find("span.current_value").text( li.text() );
					} 
				});
			});
		}
		customSelect();
		
		//$(".kd_custom_select").on('click', "a", function(e){
		$(".kd_custom_select").on('click touchstart', "a", function(e){
			e.preventDefault();
			// alert('ceva');
			var that = $(this),
				alias = that.attr("href").replace("#", ''),
				container = that.parents('.kd_custom_select').eq(0),
				select_box = container.find("select");
			
			var selected_option = select_box.find('option[value="' + ( alias ) + '"]');
			
			container.find("span.current_value").text( selected_option.text() );
			selected_option.prop('selected', true);
			//select_box.val(alias);
			select_box.trigger( "change" );
			
			if( select_box.hasClass('orderby') ){
				select_box.parent('.woocommerce-ordering').submit();
				console.log( selected_option ); 
			}
			
			// if( selected_option.length == 0 ) {
				// var choose = $('.variations select option').first().text();
				// container.find("span.current_value").text( choose );
			// }
			
			if( selected_option.length == 0 ) {
                var choose = select_box.find('option[value=""]');
                container.find("span.current_value").text( choose.text() );
                choose.prop('selected', true);
                select_box.trigger( "change" );
            }
			
		});
	}
	
	// woocommerce widget filters
	$('.product_list_widget > li > a > img').each(function() {
		$(this).parent().before(this);
		$(this).wrap('<div class="product_list_widget_img_wrapper" />');
	});
	
	$('.product_list_widget > li > a').each(function() {
		if ($.trim($(this).text()).length > 30 ) { $(this).text($.trim($(this).text()).substr(0, 30) + "..."); }
	});
	
	$('.product_list_widget > li > .product_list_widget_img_wrapper').each(function() {
		$(this).parent().children('a').prepend(this);
	});
	
	// woocommerce product categoies, create accordition
	kingdom_sidebar_menu( $(".widget_product_categories .product-categories") );
	
	$(".kd_small-cart .cart-details-wrapper").each(function(){
		var that = $(this);
		
		that.height( (that.find(".kd_small_cart_items li").size() * 94) + 110 );
		
		if( that.find(".kd_small_cart_items .empty").size() > 0 ){
			that.height( 32 );
		}
	});
	
	$("#top_nav .sub-menu").parent('li').append( '<i class="icon icon_arrow-menu"></i>' );
	
	$("input[type=number]")
		.attr("type", "text")
		.show();
		
	// Google maps
	$('div.kingdom-map').each(function(){
		var that = $(this);
		that.gMap({ address: that.data('address'), zoom: that.data('zoom') });
	}); 
	
	if( $('.dropdown-menu').has('li').length == 0 ) {
  		$('.kd_loop_orderby').hide();
	}
	
	$(".kd_hovereffect img").each(function(i, img) {	
	    $(img).css({
	        position: "relative",
	        left: ($(img).parent().width()/2) - ($(img).width()/2)
	    });
	});
	
});
