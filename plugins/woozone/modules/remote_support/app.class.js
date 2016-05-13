/*
Document   :  404 Monitor
Author     :  Andrei Dinca, AA-Team http://codecanyon.net/user/AA-Team
*/

// Initialization and events code for the app
wwcAmzAffRemoteSupport = (function ($) {
    "use strict";

    // public
    var debug_level = 0;
    var maincontainer = null;
    var loading = null;
    var loaded_page = 0;
    var token = null;

	// init function, autoload
	(function init() {
		// load the triggers
		$(document).ready(function(){
			maincontainer = $("#wwcAmzAff-wrapper");
			loading = maincontainer.find("#wwcAmzAff-main-loading");

			triggers();
		});
	})();
	
	function remote_register_and_login( that )
	{
		loading.show();
		
		jQuery.post(ajaxurl, {
			'action' 		: 'wwcAmzAffRemoteSupportRequest',
			'sub_actions'	: 'remote_register_and_login',
			'params'		: that.serialize(),
			'debug_level'	: debug_level
		}, function(response) {
			
			if( response.status == 'valid' ){
				token = response.token;
				$("#wwcAmzAff-token").val(token);
				$("#wwcAmzAff-boxid-login").fadeOut(100);
				$("#wwcAmzAff-boxid-register").fadeOut(100);
				
				var box_info_message = $("#wwcAmzAff-boxid-logininfo .wwcAmzAff-message");
				box_info_message.removeClass("wwcAmzAff-info");
				box_info_message.addClass("wwcAmzAff-success");
				
				box_info_message.html("You have successfully login into http://support.aa-team.com . Now you can open a ticket for our AA-Team support team.");
				
				$("#wwcAmzAff-boxid-ticket").fadeIn(100);
			}else{
				var status_block = that.find(".wwcAmzAff-message");
				status_block.html( "<strong>" + ( response.error_code ) + ": </strong>" + response.msg );
				
				status_block.fadeIn('fast'); 
			}
			
			loading.hide();
		}, 'json'); 
	}
	
	function remote_login( that )
	{
		loading.show();
		
		jQuery.post(ajaxurl, {
			'action' 		: 'wwcAmzAffRemoteSupportRequest',
			'sub_actions'	: 'remote_login',
			'params'		: that.serialize(),
			'debug_level'	: debug_level
		}, function(response) {
			
			if( response.status == 'valid' ){
				token = response.token;
				$("#wwcAmzAff-token").val(token);
				$("#wwcAmzAff-boxid-login").fadeOut(100);
				$("#wwcAmzAff-boxid-register").fadeOut(100);
				
				var box_info_message = $("#wwcAmzAff-boxid-logininfo .wwcAmzAff-message");
				box_info_message.removeClass("wwcAmzAff-info");
				box_info_message.addClass("wwcAmzAff-success");
				
				box_info_message.html("You have successfully login into http://support.aa-team.com . Now you can open a ticket for our AA-Team support team.");
				
				$("#wwcAmzAff-boxid-ticket").fadeIn(100);
			}else{
				var status_block = that.find(".wwcAmzAff-message");
				status_block.html( "<strong>" + ( response.error_code ) + ": </strong>" + response.msg );
				
				status_block.fadeIn('fast'); 
			}
			
			loading.hide();
		}, 'json'); 
	}
	
	function open_ticket( that )
	{
		loading.show();
		
		$("#wwcAmzAff-wp_password").val( $("#wwcAmzAff-password").val() );
		$("#wwcAmzAff-access_key").val( $("#wwcAmzAff-key").val() );
		
		jQuery.post(ajaxurl, {
			'action' 		: 'wwcAmzAffRemoteSupportRequest',
			'sub_actions'	: 'open_ticket',
			'params'		: that.serialize(),
			'token'			: $("#wwcAmzAff-token").val(),
			'debug_level'	: debug_level
		}, function(response) {
			
			if( response.status == 'valid' ){
				that.find(".wwcAmzAff-message").html( "The ticket has been open. New ticket ID: <strong>" + response.new_ticket_id + "</strong>" );
				that.find(".wwcAmzAff-message").show();
			}
			 
			loading.hide();
			
		}, 'json'); 
	}
	
	function access_details( that )
	{
		loading.show();
		
		jQuery.post(ajaxurl, {
			'action' 		: 'wwcAmzAffRemoteSupportRequest',
			'sub_actions'	: 'access_details',
			'params'		: that.serialize(),
			'debug_level'	: debug_level
		}, function(response) {
			
			loading.hide();
		}, 'json'); 
	}
	
	function checkAuth( token )
	{
		var loading = $("#wwcAmzAff-main-loading");
		loading.show(); 
		
		jQuery.post(ajaxurl, {
			'action' 		: 'wwcAmzAffRemoteSupportRequest',
			'sub_actions'	: 'check_auth',
			'params'		: {
				'token': token
			},
			'debug_level'	: debug_level
		}, function(response) {
			// if has a valid token
			if( response.status == 'valid' ){
				$("#wwcAmzAff-boxid-ticket").show();
				$("#wwcAmzAff-boxid-logininfo").hide();
			}
			
			// show the auth box
			else{
				$("#wwcAmzAff-boxid-ticket").hide();
				$("#wwcAmzAff-boxid-logininfo .wwcAmzAff-message").html( 'In order to contact AA-Team support team you need to login into support.aa-team.com' );
				$("#wwcAmzAff-boxid-login").show();
				$("#wwcAmzAff-boxid-register").show();
			}
			loading.hide();
		}, 'json'); 
	}

	function triggers()
	{
		maincontainer.on('submit', '#wwcAmzAff-form-login', function(e){
			e.preventDefault();

			remote_login( $(this) );
		});
		
		maincontainer.on('submit', '#wwcAmzAff-form-register', function(e){
			e.preventDefault();

			remote_register_and_login( $(this) );
		});
		
		maincontainer.on('submit', '#wwcAmzAff_access_details', function(e){
			e.preventDefault();

			access_details( $(this) );
		});
		
		maincontainer.on('change', '#wwcAmzAff-create_wp_credential', function(e){
			e.preventDefault();

			var that = $(this);
			
			if( that.val() == 'yes' ){
				$(".wwcAmzAff-wp-credential").show();
			}else{
				$(".wwcAmzAff-wp-credential").hide();
			}
		});
		
		maincontainer.on('change', '#wwcAmzAff-allow_file_remote', function(e){
			e.preventDefault();

			var that = $(this);
			
			if( that.val() == 'yes' ){
				$(".wwcAmzAff-file-access-credential").show();
			}else{
				$(".wwcAmzAff-file-access-credential").hide();
			}
		});
		
		maincontainer.on('submit', '#wwcAmzAff_add_ticket', function(e){
			e.preventDefault();

			open_ticket( $(this) );
		});
	}

	// external usage
	return {
		'checkAuth': checkAuth,
		'token' : token
    }
})(jQuery);
