<div class="wrap">
	<div id="icon-edit" class="icon32 icon32-base-template"><br></div>
	<h2><?php _e( "Base plugin page", 'psbase' ); ?></h2>
	
	<p><?php _e( "Sample base plugin page", 'psbase' ); ?></p>
	
	<form id="ps-plugin-scope-form" action="options.php" method="POST">
		
			<?php settings_fields( 'ps_setting' ) ?>
			<?php do_settings_sections( 'ps-plugin-scope' ) ?>
			
			<input type="submit" value="<?php _e( "Save", 'psbase' ); ?>" />
	</form> <!-- end of #pstemplate-form -->
</div>