<?php 

add_action('wp_ajax_nopriv_wwcAmzAff_load_bulk_product', 'wwcAmzAff_load_bulk_product_callback');
add_action('wp_ajax_wwcAmzAff_load_bulk_product', 'wwcAmzAff_load_bulk_product_callback');
function wwcAmzAff_load_bulk_product_callback() {
	global $wwcAmzAff;
	
	$amazon_settings = $wwcAmzAff->getAllSettings('array', 'amazon');
	$plugin_uri = $wwcAmzAff->cfg['paths']['plugin_dir_url'] . 'modules/amazon/';
	
	$requestData = array(
		'ASIN' => isset($_REQUEST['ASIN']) ? htmlentities($_REQUEST['ASIN']) : '',
		'to-category' => isset($_REQUEST['to-category']) ? htmlentities($_REQUEST['to-category']) : 'amz',
		'category' => isset($_REQUEST['category']) ? htmlentities($_REQUEST['category']) : ''
	);
	
	// load the amazon webservices client class 
	require_once( $wwcAmzAff->cfg['paths']['plugin_dir_path'] . '/lib/scripts/amazon/aaAmazonWS.class.php');
	
	// create new amazon instance
	$aaAmazonWS = new aaAmazonWS( 
		$amazon_settings['AccessKeyID'], 
		$amazon_settings['SecretAccessKey'], 
		$amazon_settings['country'], 
		$amazon_settings['AffiliateId']
	);
    $aaAmazonWS->set_the_plugin( $wwcAmzAff );
	
	// create request by ASIN
	$product = $aaAmazonWS->responseGroup('Large' . ( $requestData['category'] == 'Apparel' ? ',Variations' : ''))->optionalParameters(array('MerchantId' => 'All'))->lookup($requestData['ASIN']);
  
	if($product['Items']["Request"]["IsValid"] == "True"){
		$thisProd = $product['Items']['Item'];
		if(count($product['Items']['Item']) > 0){
			// start creating return array 
			$retProd = $retProd['images'] = array();
			
			// product large image
			$retProd['images'][] = $thisProd['LargeImage']['URL'];
			
			$retProd['ASIN'] = $thisProd['ASIN'];
			
			// get gallery images 
			if(count($thisProd['ImageSets']) > 0){
				$count = 0;
				foreach ($thisProd['ImageSets']["ImageSet"] as $key => $value){
					if($count > 5) continue;
					if( isset($value['LargeImage']['URL']) && $count > 0 ){
						$retProd['images'][] = $value['LargeImage']['URL'];
					}
					$count++;
				}
			}
			
			// set other ItemAttributes
			
			// CustomerReviews url 
			if($thisProd['CustomerReviews']['HasReviews']){
				$retProd['CustomerReviewsURL'] = $thisProd['CustomerReviews']['IFrameURL'];
			}
			
			// DetailPageURL
			$retProd['DetailPageURL'] = $thisProd['DetailPageURL'];
			
			// ItemLinks
			$retProd['ItemLinks'] = $thisProd['ItemLinks'];
			
			// product title 
			$retProd['Title'] = $thisProd['ItemAttributes']['Title'];
			
			// Binding
			$retProd['Binding'] = $thisProd['ItemAttributes']['Binding'];
			
			// ProductGroup
			$retProd['ProductGroup'] = $thisProd['ItemAttributes']['ProductGroup'];
			
			// SKU
			$retProd['SKU'] = $thisProd['ItemAttributes']['SKU'];
			
			// Feature
			$retProd['Feature'] = $thisProd['ItemAttributes']['Feature'];
			
			// price (OfferSummary) //['Offers']
			$retProd['price'] = array(
				'Amount' => $thisProd['Offers']['Offer']['OfferListing']['Price']['Amount'],
				'FormattedPrice' => preg_replace( "/[^0-9,.]/", "", $thisProd['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'] )
			);
			
			if($requestData['category'] == 'Apparel'){
				$retProd['price'] = array(
					'Amount' => $thisProd['ItemAttributes']['ListPrice']['Amount'],
					'FormattedPrice' => preg_replace( "/[^0-9,.]/", "", $thisProd['ItemAttributes']['ListPrice']['FormattedPrice'] )
				);
			}
			
			// check if product have some offers
			if(isset($thisProd['OfferSummary']['LowestNewPrice'])){
				$retProd['price_offer'] = array(
					'Amount' => $thisProd['OfferSummary']['LowestNewPrice']['Amount'],
					'FormattedPrice' => preg_replace( "/[^0-9,.]/", "", $thisProd['OfferSummary']['LowestNewPrice']['FormattedPrice'] )
				);
			}
			
			// EditorialReviews
			$retProd['EditorialReviews'] = $thisProd['EditorialReviews']['EditorialReview']['Content'];
			
			if($_REQUEST['dump'] == '1'){
				var_dump('<pre>', $retProd ,'</pre>'); die; 
			}
			
			$wwcAmzAff->addNewWooProduct($retProd);

			// now return everythink as json 
			die(json_encode(array(
				'status' 		=> 'valid'
			)));
		}
	}else{
		die(json_encode(array(
			'status' => 'invalid',
			'msg' => "Can't get product by given ASIN: " . $requestData['ASIN']
		)));
	}
}

add_action('wp_ajax_wwcAmzAff_bulk_amazon_request', 'wwcAmzAff_bulk_amazon_request_callback');
function wwcAmzAff_bulk_amazon_request_callback() {
	global $wwcAmzAff; 
		
	$amazon_settings = $wwcAmzAff->getAllSettings('array', 'amazon');
	$plugin_uri = $wwcAmzAff->cfg['paths']['plugin_dir_url'] . 'modules/amazon/';
	
	$requestData = array(
		'search' => isset($_REQUEST['search']) ? htmlentities($_REQUEST['search']) : '',
		'category' => isset($_REQUEST['category']) ? htmlentities($_REQUEST['category']) : '',
		'sort' => isset($_REQUEST['sort']) ? htmlentities($_REQUEST['sort']) : '',
		'page' => isset($_REQUEST['page']) ? (int)($_REQUEST['page']) : ''
	);
	
	// load the amazon webservices client class 
	require_once( $wwcAmzAff->cfg['paths']['plugin_dir_path'] . '/lib/scripts/amazon/aaAmazonWS.class.php');
	
	// create new amazon instance
	$aaAmazonWS = new aaAmazonWS( 
		$amazon_settings['AccessKeyID'], 
		$amazon_settings['SecretAccessKey'], 
		$amazon_settings['country'], 
		$amazon_settings['AffiliateId']
	);
    $aaAmazonWS->set_the_plugin( $wwcAmzAff );
	
	if($requestData['sort'] != 'none') {
		
		// changing the category to {$requestData['category']} and the response to only images and looking for some matrix stuff.
		$response = $aaAmazonWS->optionalParameters(array('Sort' => $requestData['sort']))->category($requestData['category'])->page($requestData['page'])->responseGroup('Large' . ( $requestData['category'] == 'Apparel' ? ',Variations' : ''))->search($requestData['search']);
	}else{	
		// changing the category to {$requestData['category']} and the response to only images and looking for some matrix stuff.
		$response = $aaAmazonWS->category($requestData['category'])->page($requestData['page'])->responseGroup('Large' . ( $requestData['category'] == 'Apparel' ? ',Variations' : ''))->search($requestData['search']);
	}
	
	
	// print some debug if requested
	$_GET['dump'] = isset($_GET['dump']) ? $_GET['dump'] : 0;
	if($_GET['dump'] == 1 && is_admin()) {
		var_dump('<pre>', $requestData, $response ,'</pre>'); die; 
	}
  
	if($response['Items']['Request']['IsValid'] == 'False') {
	
		die('<div class="error" style="float: left;margin: 10px;padding: 6px;">Amazon error id: <bold>' . ( $response['Items']['Request']['Errors']['Error']['Code'] ) . '</bold>: <br /> ' . ( $response['Items']['Request']['Errors']['Error']['Message'] ) . '</div>');
	}
	elseif(count($response['Items']) > 0){
	
		if($response['Items']['TotalResults'] > 1) {
	?>
			<div class="resultsTopBar">
				<h2>
					Showing <?php echo $requestData['page'];?> - <?php echo $response['Items']["TotalPages"];?> of <span id="wwcAmzAff-totalPages"><?php echo $response['Items']["TotalResults"];?></span> Results
				</h2>
				
				<div class="wwcAmzAff-pagination">
					<span>View page:</span>
					<select id="wwcAmzAff-page">
						<?php
						for( $p = 1; $p <= 5; $p++ ){
							echo '<option value="' . ( $p ) . '" ' . ( $p == $requestData['page'] ? 'selected' : '' ) . '> ' . ( $p ) . ' </option>';
						}
						?>
					</select>
				</div>
			</div>
	
		<?php
		}	// don't show paging if total results it's not bigget than 1	
			if(count($response['Items']['Item']) > 0){
				echo '<div class="wwcAmzAff-product-box">';
				echo '<table class="product">
					<thead>
						<tr class="wwcAmzAff-tabel-title">
							<td style="text-align: center;border-right: 1px solid #DADADA;">Nr.</td>
							<td style="text-align: center;border-right: 1px solid #DADADA;"><input type="checkbox" checked id="wwcAmzAff-check-all" /></td>
							<td style="text-align: center;border-right: 1px solid #DADADA;">&nbsp;Image</td>
							<td style="border-right: 1px solid #DADADA;">&nbsp;Info</td>
							<td>Extra</td>
						</tr>
					</thead>
					<tbody>
				';	
				$cc = 0;
				
				foreach ($response['Items']['Item'] as $key => $value){
				 
					if($response['Items']['TotalResults'] == 1) {
						$value = $response['Items']['Item'];
						if($_REQUEST['dump'] == 1){
							var_dump('<pre>',$value ,'</pre>'); die; 
						}
					}
					if(($cc + 1) > $response['Items']['TotalResults']) continue;
					
					$thumb = $value['SmallImage']['URL'];
					if(trim($thumb) == ""){
						// try to find image as first image from image sets
						$thumb = $value['ImageSets']['ImageSet'][0]['SmallImage']['URL'];
					}
		?>
					<tr>
						<td class="product-number"><?php echo ++$cc;?>.</td>
						<td class="product-check" valign="top"><input style="margin-top: 10px;" type="checkbox" checked id="wwcAmzAff-check-<?php echo $value['ASIN'];?>" class="wwcAmzAff-elements" /></td>
						<td class="product-image">
							<a href="<?php echo $value['DetailPageURL'];?>" target="_blank">
								<img class="productImage" src="<?php echo $thumb;?>">
							</a>
						</td>
						<td class="product-data">
							<h4 class="product-title">
								<a href="<?php echo $value['DetailPageURL'];?>" target="_blank"><?php echo $value['ItemAttributes']['Title'];?></a>
							</h4>
							<div class="newPrice">
								<span class="price"><?php 
									if($requestData['category'] == 'Apparel'){
										echo $value['VariationSummary']['LowestPrice']['FormattedPrice'];
									}else{
										echo $value['Offers']['Offer']['OfferListing']['Price']['FormattedPrice'];
									}
									?></span>
							</div>
							<div class="product-description"><?php echo $value['EditorialReviews']['EditorialReview']['Content'];?></div>
						</td>
						<td class="product-options">
							<?php 
								if($value['CustomerReviews']['HasReviews'] == true){
									echo '<a target="_blank" class="wwcAmzAff-option-btn" href="' . ( $value['CustomerReviews']['IFrameURL'] ) . '"> <img src="'. ( $plugin_uri ) .'assets/comments.png" /> Customer Reviews</a>';
								}
							?>
							<a class="wwcAmzAff-option-btn wwcAmzAff-load-product" href="#" rel="<?php echo $value['ASIN'];?>"><img src="<?php echo $plugin_uri;?>assets/update.png" /> Load this product </a>
						<td>
					</tr>	
		<?php
				} // end foreach
				echo '</tbody></table></div>'; // close the table
		} // end if have products
	}
	die(); // this is required to return a proper result
}

add_action('wp_ajax_wwcAmzAff_load_sort_by_categ', 'wwcAmzAff_load_sort_by_categ_callback');
function wwcAmzAff_load_sort_by_categ_callback() {
 
	$alls = array(
		'Apparel' => array(
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'salesrank' => 'Bestselling',
			'pricerank' => 'Price: low to high',
			'inverseprice' => 'Price: high to low',
			'-launch-date' => 'Newest arrivals',
			'sale-flag' => 'On sale'
		),
		'Appliances' => array(
			'salesrank' => 'Bestselling',
			'pmrank' => 'Featured items',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'relevancerank' => 'Items ranked according to the following criteria: how often the keyword appears in 
				  the description, where the keyword appears, for example, the ranking is higher when keywords are found 
				  in titles, and, if there are multiple keywords, how closely they occur in descriptions, and, finally, how 
				  often customers purchased the products they found using the keyword.',
			'reviewrank' => 'Average customer review: high to low'
		),
		'ArtsAndCrafts' => array(
			'pmrank' => 'Featured items',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'relevancerank' => 'Items ranked according to the following criteria: how often the keyword appears in the description, where the keyword appears, for example, the ranking is higher when keywords are found in titles, and, if there are multiple keywords, how closely they occur in descriptions, and, finally, how often customers purchased the products they found using the keyword.',
			'reviewrank' => 'Highest to lowest ratings in customer reviews.'
		),
		'Automotive' => array(
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A'
		),
		'Baby' => array(
			'psrank' => 'Bestseller ranking taking into consideration projected sales.The
				  lower the value, the better the sales.',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z'
		),
		'Beauty' => array(
			'pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'-launch-date' => 'Newest arrivals',
			'sale-flag' => 'On sale'
		),
		'Books' => array(
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'salesrank' => 'Bestselling',
			'reviewrank' => 'Average customer review: high to low',
			'pricerank' => 'Price: low to high',
			'inverse-pricerank' => 'Price: high to low',
			'daterank' => 'Publication date: newer to older',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A'
		),
		'Classical' => array(
			'psrank' => 'Bestseller ranking taking into consideration projected sales.The
				  lower the value, the better the sales.',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A',
			'orig-rel-date' => 'Release date: newer to older',
			'-orig-rel-date' => 'Release date: older to newer',
			'releasedate' => 'Release date: newer to older',
			'-releasedate' => 'Release date: older to newer'
		),
		'Collectibles' => array(
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur 
				  in descriptions (if there are multiple keywords), and how often customers
				  purchased the products they found using the keyword.',
			'reviewrank' => 'Average customer review: high to low',
			'salesrank' => 'Bestselling'
		),
		'DigitalMusic' => array(
			'songtitlerank' => 'Most popular',
			'uploaddaterank' => 'Date added'
		),
		'DVD' => array(
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-video-release-date' => 'Release date: newer to older',
			'releasedate' => 'Release date: newer to older'
		),
		'Electronics' => array(
			'pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'reviewrank' => 'Average customer review: high to low',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z'
		),
		'Grocery' => array(
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'salesrank' => 'Bestselling',
			'pricerank' => 'Price: low to high',
			'inverseprice' => 'Price: high to low',
			'launch-date' => 'Newest launches first',
			'sale-flag' => 'On sale'
		),
		'HealthPersonalCare' => array(
			'pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'pricerank' => 'Price: low to high',
			'inverseprice' => 'Price: high to low',
			'launch-date' => 'Newest arrivals',
			'sale-flag' => 'On sale'
		),
		'HomeImprovement' => array(
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A'
		),
		'SearchIndex:Industrial' => array(
			'pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A'
		),
		'Jewelry' => array(
			'pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'pricerank' => 'Price: low to high',
			'inverseprice' => 'Price: high to low',
			'launch-date' => 'Newest arrivals'
		),
		'KindleStore' => array(
			'daterank' => 'Publication date: newer to older',
			'-edition-sales-velocity' => 'Quickest to slowest selling products.',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'reviewrank' => 'Average customer review: high to low'
		),
		'Kitchen' => array(
			'pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A'
		),
		'LawnAndGarden' => array(
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'reviewrank' => 'Average customer review: high to low',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low'
		),
		'Magazines' => array(
			'subslot-salesrank' => 'Bestselling',
			'reviewrank' => 'Average customer review: high to low',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'daterank' => 'Publication date: newer to older',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A'
		),
		'Marketplace' => array(
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A',
			'-launch-date' => 'Newest arrivals first'
		),
		'Merchants' => array(
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'salesrank' => 'Bestselling',
			'pricerank' => 'Price: low to high',
			'inverseprice' => 'Price: high to low',
			'-launch-date' => 'Newest arrivals',
			'sale-flag' => 'On sale'
		),
		'Miscellaneous' => array(
			'pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A'
		),
		'MobileApps' => array(
			'pmrank' => 'Featured items',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'relevancerank' => 'Items ranked according to the following criteria: how often the keyword appears in the description, where the keyword appears, for example, the ranking is higher when keywords are found in titles, and, if there are multiple keywords, how closely they occur in descriptions, and, finally, how often customers purchased the products they found using the keyword.',
			'reviewrank' => 'Highest to lowest ratings in customer reviews.'
		),
		'MP3Downloads' => array(
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'-releasedate' => 'Release date: most recent to oldest',
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'salesrank' => 'Bestselling'
		),
		'Music' => array(
			'psrank' => 'Bestseller ranking taking into consideration projected sales.The
				  lower the value, the better the sales.',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A',
			'artistrank' => 'Artist name: A to Z',
			'orig-rel-date' => 'Original release date of the item listed from newer to older. See
				  release-date, which sorts by the latest release date.',
			'release-date' => 'Sorts by the latest release date from newer to older. See
				  orig-rel-date, which sorts by the original release date.',
			'releasedate' => 'Release date: most recent to oldest',
			'-releasedate' => 'Release date: oldest to most recent',
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.'
		),
		'MusicalInstruments' => array(
			'pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'-launch-date' => 'Newest arrivals',
			'sale-flag' => 'On sale'
		),
		'MusicTracks' => array(
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A'
		),
		'OfficeProducts' => array(
			'pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'reviewrank' => 'Average customer review: high to low',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z'
		),
		'OutdoorLiving' => array(
			'psrank' => 'Bestseller ranking taking into consideration projected sales.The
				  lower the value, the better the sales.',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A'
		),
		'PCHardware' => array(
			'psrank' => 'Bestseller ranking taking into consideration projected sales.The
				  lower the value, the better the sales.',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z'
		),
		'PetSupplies' => array(
			'+pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A',
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'reviewrank' => 'Average customer review: high to low'
		),
		'Photo' => array(
			'pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A'
		),
		'Shoes' => array(
			'-launch-date' => 'Newest arrivals',
			'pmrank' => 'Featured items',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'reviewrank' => 'Average customer review: high to low'
		),
		'Software' => array(
			'pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z'
		),
		'SportingGoods' => array(
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'salesrank' => 'Bestselling',
			'pricerank' => 'Price: low to high',
			'inverseprice' => 'Price: high to low',
			'launch-date' => 'Newest arrivals',
			'sale-flag' => 'On sale'
		),
		'Tools' => array(
			'pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A'
		),
		'Toys' => array(
			'pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-age-min' => 'Age: high to low'
		),
		'UnboxVideo' => array(
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-video-release-date' => 'Release date: newer to older'
		),
		'VHS' => array(
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-video-release-date' => 'Release date: newer to older',
			'-releasedate' => 'Release date: newer to older'
		),
		'Video' => array(
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z',
			'-video-release-date' => 'Release date: newer to older',
			'-releasedate' => 'Release date: newer to older'
		),
		'VideoGames' => array(
			'pmrank' => 'Featured items',
			'salesrank' => 'Bestselling',
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'titlerank' => 'Alphabetical: A to Z'
		),
		'Watches' => array(
			'price' => 'Price: low to high',
			'-price' => 'Price: high to low',
			'relevancerank' => 'Items ranked according to the following criteria: how often the
				  keyword appears in the description, where the keyword appears (for example, the
				  ranking is higher when keywords are found in titles), how closely they occur in
				  descriptions (if there are multiple keywords), and how often customers purchased the
				  products they found using the keyword.',
			'reviewrank' => 'Average customer review: high to low',
			'salesrank' => 'Bestselling to worst selling'
		),
		'Wireless' => array(
			'daterank' => 'Publication date: newer to older',
			'pricerank' => 'Price: low to high',
			'inverse-pricerank' => 'Price: high to low',
			'reviewrank' => 'Average customer review: high to low',
			'salesrank' => 'Bestselling',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A'
		),
		'WirelessAccessories' => array(
			'psrank' => 'Bestseller ranking taking into consideration projected sales.The
				  lower the value, the better the sales.',
			'salesrank' => 'Bestselling',
			'titlerank' => 'Alphabetical: A to Z',
			'-titlerank' => 'Alphabetical: Z to A'
		)
	);
	
	
	$requestData = array(
		'cat' => isset($_REQUEST['cat']) ? htmlentities($_REQUEST['cat']) : ''
	);

	if(in_array($requestData['cat'], array_keys($alls))){
		if(count($alls[$requestData['cat']]) > 0){
			$selectHtml = array();
			$detailsHtml = array();
			
			foreach ($alls[$requestData['cat']] as $key => $value){
				$selectHtml[] = '<option value="' . ( $key ) . '">' . ( $key ) . '</option>';
				
				$detailsHtml[] = '<tr><td valign="top"><strong>' . ( $key ) . '</strong></td><td>' . ( $value ) . '</td></tr>';
			}
			
			die(json_encode(array(
				'status' => 'valid',
				'select_html' => implode("\n", $selectHtml),
				'info_html' => implode("\n", $detailsHtml)
			)));
		}
	}
}