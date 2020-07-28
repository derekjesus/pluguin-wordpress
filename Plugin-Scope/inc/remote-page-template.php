<div class="wrap">
	<div id="icon-edit" class="icon32 icon32-base-template"><br></div>
	<h2><?php _e( "Remote plugin page", 'psbase' ); ?></h2>
	
	<p><?php _e( "Performing side activities - AJAX and HTTP fetch", 'psbase' ); ?></p>
	<div id="ps_page_messages"></div>
	
	<?php
		$ps_ajax_value = get_option( 'ps_option_from_ajax', '' );
	?>
	
	<h3><?php _e( 'Store a Database option with AJAX', 'psbase' ); ?></h3>
	<form id="ps-plugin-scope-ajax-form" action="options.php" method="POST">
			<input type="text" id="ps_option_from_ajax" name="ps_option_from_ajax" value="<?php echo $ps_ajax_value; ?>" />
			
			<input type="submit" value="<?php _e( "Save with AJAX", 'psbase' ); ?>" />
	</form> <!-- end of #ps-plugin-scope-ajax-form -->
	
	<h3><?php _e( 'Fetch a title from URL with HTTP call through AJAX', 'psbase' ); ?></h3>
	<form id="ps-plugin-scope-http-form" action="options.php" method="POST">
			<input type="text" id="ps_url_for_ajax" name="ps_url_for_ajax" value="http://wordpress.org" />
			
			<input type="submit" value="<?php _e( "Fetch URL title with AJAX", 'psbase' ); ?>" />
	</form> <!-- end of #ps-plugin-scope-http-form -->
	
	<div id="resource-window">
	</div>
			
</div>