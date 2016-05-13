(function ($) {
    var multiple_asins = [];
    
    function numberWithCommas(x) {
        var parts = x.toString().split(".");
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        return parts.join(".");
    }
    
    function numberFormat(number) {
        return number.replace(',', '###').replace('.', ',').replace('###', '.');
    }

    // cross sell
    $(".cross-sell").on("change", 'input', function(e){
        var that        = $(this),
            row         = that.parents('li').eq(0),
            asin        = that.val()
            the_thumb   = $('#cross-sell-thumb-' + asin).parents('li'),
            buy_block   = $('li.cross-sell-buy-btn');
            
        var price_dec_sep = $('.cross-sell .cross-sell-price-sep').data('price_dec_sep');

        buy_block.fadeOut('fast');
        if( that.is(':checked') ){
            row.attr('class', '');
            the_thumb.fadeIn('fast');
        }
        else{
            row.attr('class', '');
            row.addClass('cross-sale-uncheck');

            the_thumb.fadeOut('fast');
        }

        var _total_price = 0,
            remaining_items = 0;
        $(".cross-sell ul.cross-sell-items li:not(.cross-sale-uncheck)").each(function(){
            var that    = $(this);
            //var price   = that.find('.cross-sell-item-price').text().replace(/[^-\d\.,]/g, '');
            var price   = that.find('.cross-sell-item-price').data('item_price');

            _total_price = _total_price + parseFloat(price);

            remaining_items++;
        });

        if( _total_price > 0 ){
            _total_price = _total_price.toFixed(2);
            if ( ',' == price_dec_sep ) {
                _total_price = numberFormat( _total_price );
            }
            $("#feq-products").show();
            var curr_price = $("#cross-sell-buying-price").text().match(/\d.+/);
            $("#cross-sell-buying-price").text( $("#cross-sell-buying-price").text().replace(curr_price, _total_price) )
        }

        else{
            $("#feq-products").fadeOut('fast');
        }

        buy_block.fadeIn('fast');


    }).on("click", 'a#cross-sell-add-to-cart', function(e){
        e.preventDefault();

        var that = $(this);

        // get all selected products
        var totals_checked  = $(".cross-sell ul.cross-sell-items li:not(.cross-sale-uncheck)").size();
        $(".cross-sell ul.cross-sell-items li:not(.cross-sale-uncheck)").each(function(){
            var that    = $(this),
                q       = 1,
                asin    = that.find('input').val();

            multiple_asins.push(asin);
        });

        if( totals_checked > 0 ){
            window.location = that.attr('href') + '?amz_cross_sell=yes&asins=' + multiple_asins.join(',');
        }
    });
}(jQuery));