/* sample script here */

jQuery(document).ready(function($) {
	
	// Handle the AJAX field save action
	$('#ps-plugin-scope-ajax-form').on('submit', function(e) {
		e.preventDefault();
		
		var ajax_field_value = $('#ps_option_from_ajax').val();
		
		 $.post(ajaxurl, {
			 	data: { 'ps_option_from_ajax': ajax_field_value },
		             action: 'store_ajax_value'
				 }, function(status) {
					 	 $('#ps_page_messages').html('Value updated successfully');
		           }
		);
	});
	
	// Handle the AJAX URL fetcher
	$('#ps-plugin-scope-http-form').on('submit', function(e) {
		e.preventDefault();
		
		var ajax_field_value = $('#ps_url_for_ajax').val();
		
		 $.post(ajaxurl, {
			 	data: { 'ps_url_for_ajax': ajax_field_value },
		             action: 'fetch_ajax_url_http'
				 }, function(status) {
					 	 $('#ps_page_messages').html('The URL title is fetching in the frame below');
					 	 $('#resource-window').html( '<p>Site title: ' + status + '</p>');
		           }
		);
	});
});