jQuery(document).ready(function($) {

	var wwcAmzAff_asins_arr = [];

	var addASINtoQueue = function() {
		var asins_str = jQuery.trim(jQuery('#wwcAmzAff-csv-asin').val()),
			delimiter = jQuery("input[name=wwcAmzAff-csv-delimiter]:checked").attr('id').split('radio-'),
			delimiter = delimiter[1];

			if( delimiter == 'newline' ){
				delimiter = "\n";
			}else if ( delimiter == 'comma' ){
				delimiter = ",";
			}else{
				delimiter = "\t";
			}

		if(asins_str == ""){
			alert('Please first add some ASINs!');
			return false;
		}


		jQuery.each( asins_str.split( delimiter ), function(key, val) {
			if(jQuery.trim( val ) != ""){
				wwcAmzAff_asins_arr.push( jQuery.trim( val ) );
			}
		});

		if(wwcAmzAff_asins_arr.length > 0){
			printASINtoQueue();
		}else{
			alert('No ASIN can be added to Queue!');
		}
	};

	var printASINtoQueue = function() {
		jQuery("#wwcAmzAff-no-ASIN").hide();
		jQuery("#wwcAmzAff-csvBulkImport-queue-response").show();

		var print = '';
		jQuery.each( wwcAmzAff_asins_arr, function(key, val) {
			print += '<tr>';
			print += 	'<td>' + ( val ) + '</td>';
			print += 	'<td id="wwcAmzAff-asin-' + key + '"><div class="wwcAmzAff-message wwcAmzAff-error" style="display:none;">Error:!</div><div class="wwcAmzAff-message wwcAmzAff-success" style="display:none;">Ready!</div><div class="wwcAmzAff-message wwcAmzAff-info">Ready for import</div></td>';
			print += '</tr>';
		});

		jQuery("#wwcAmzAff-print-response").html( print );
	};

	jQuery("a#wwcAmzAff-addASINtoQueue").die().on('click', function(e) {
		e.preventDefault();
		addASINtoQueue();
	});

	jQuery('body').on('click', 'a#wwcAmzAff-startImportASIN', function (e) {
		e.preventDefault();

		var numberOfItems = wwcAmzAff_asins_arr.length,
			loaded = 0,
			labelCurr = jQuery('#wwcAmzAff-status-ready'),
			labelTotal = jQuery('#wwcAmzAff-status-remaining');

		jQuery(this).hide();
		jQuery('.wwcAmzAff-status-block').show();
		// update totals
		labelCurr.text(loaded);
		labelTotal.text(numberOfItems);

		if(numberOfItems == 0) alert('Please first select some products from list!');

		var wwcAmzAff_insert_new_product = function(curr_step) {

			// stop if not valid wwcAmzAff_asins_arr
			if(typeof wwcAmzAff_asins_arr[curr_step] == 'undefined') return false;

			var data = {
				'action': 'wwcAmzAffImportProduct',
				'asin':  wwcAmzAff_asins_arr[curr_step],
				'category': 'All',
				'to-category': $("#wwcAmzAff-to-category").val()
			};

			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: data,
				success: function(response) {
					if( typeof response.status != 'undefined' && response.status == 'valid' ) {
						// show the download assets lightbox
						if( response.show_download_lightbox == true ) {
							$("#wwcAmzAff-wrapper").append( response.download_lightbox_html );
					
							wwcAmzAffAssetDownload.download_asset( $('.wwcAmzAff-images-tail').find('li').eq(0), undefined, 100, function(){
						
								$(".wwcAmzAff-asset-download-lightbox").remove();
								
								jQuery("#wwcAmzAff-asin-" + loaded).find('.wwcAmzAff-success').show();
								jQuery("#wwcAmzAff-asin-" + loaded).find('.wwcAmzAff-info').hide();
								++loaded;
		
								labelCurr.text(loaded);
								labelTotal.text(numberOfItems - loaded);
		
								// continue insert the rest of ASIN
								if(numberOfItems > curr_step) {
									wwcAmzAff_insert_new_product(++curr_step);
								}
		
								if( numberOfItems == curr_step){
									jQuery('.wwcAmzAff-status-block').html('<div class="wwcAmzAff-message wwcAmzAff-success">All products import successful! </div>');
								}
							} );
						} else {
						
							jQuery("#wwcAmzAff-asin-" + loaded).find('.wwcAmzAff-success').show();
							jQuery("#wwcAmzAff-asin-" + loaded).find('.wwcAmzAff-info').hide();
							++loaded;
	
							labelCurr.text(loaded);
							labelTotal.text(numberOfItems - loaded);
	
							// continue insert the rest of ASIN
							if(numberOfItems > curr_step) {
								wwcAmzAff_insert_new_product(++curr_step);
							}
	
							if( numberOfItems == curr_step){
								jQuery('.wwcAmzAff-status-block').html('<div class="wwcAmzAff-message wwcAmzAff-success">All products import successful! </div>');
							}
						}

					}else{
						
						var errMsg = '';
						if ( typeof response.status != 'undefined' )
							errMsg = response.msg;
						else
							errMsg = 'unknown error occured: could be related to max_execution_time, memory_limit server settings!';

						jQuery("#wwcAmzAff-asin-" + loaded).find('.wwcAmzAff-error').text("Error: " + errMsg);
						jQuery("#wwcAmzAff-asin-" + loaded).find('.wwcAmzAff-error').show();
						jQuery("#wwcAmzAff-asin-" + loaded).find('.wwcAmzAff-info').hide();
						++loaded;

						labelCurr.text(loaded);
						labelTotal.text(numberOfItems - loaded);

						// continue insert the rest of ASIN
						if(numberOfItems > curr_step) {
							wwcAmzAff_insert_new_product(++curr_step);
						}
					}
				}
			});
		}

		// run for first
		if(numberOfItems > 0) wwcAmzAff_insert_new_product(0);
	});
});