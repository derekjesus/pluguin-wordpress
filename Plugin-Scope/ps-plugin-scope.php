<?php
/**
 * Plugin Name: Plugin Scope
 * Description: Plugin Scope
 * Plugin URI: http://example.org/
 * Author: Derek Salazar
 * Author URI: http://devwp.eu/
 * Version: 1.6
 * Text Domain:  Plugin Scope
 * License: GPL2

 Copyright 2020 derek jesus (email : derekjesus@gmail.com)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License, version 2, as
 published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Get some constants ready for paths when your plugin grows 
 * 
 */

define( 'PSP_VERSION', '1.0' );
define( 'PSP_PATH', dirname( __FILE__ ) );
define( 'PSP_PATH_INCLUDES', dirname( __FILE__ ) . '/inc' );
define( 'PSP_FOLDER', basename( PSP_PATH ) );
define( 'PSP_URL', plugins_url() . '/' . PSP_FOLDER );
define( 'PSP_URL_INCLUDES', PSP_URL . '/inc' );


/**
 * 
 * The PS - Plugin Scope class 
 * 
 * @author derek
 *
 */
class PS_Plugin_Base {
	
	/**
	 * 
	 * Assign everything as a call from within the constructor
	 */
	public function __construct() {
		// add script and style calls the WP way 
		// it's a bit confusing as styles are called with a scripts hook
		// @blamenacin - http://make.wordpress.org/core/2011/12/12/use-wp_enqueue_scripts-not-wp_print_styles-to-enqueue-scripts-and-styles-for-the-frontend/
		add_action( 'wp_enqueue_scripts', array( $this, 'ps_add_JS' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'ps_add_CSS' ) );
		
		// add scripts and styles only available in admin
		add_action( 'admin_enqueue_scripts', array( $this, 'ps_add_admin_JS' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'ps_add_admin_CSS' ) );
		
		// register admin pages for the plugin
		add_action( 'admin_menu', array( $this, 'ps_admin_pages_callback' ) );
		
		// register meta boxes for Pages (could be replicated for posts and custom post types)
		add_action( 'add_meta_boxes', array( $this, 'ps_meta_boxes_callback' ) );
		
		// register save_post hooks for saving the custom fields
		add_action( 'save_post', array( $this, 'ps_save_sample_field' ) );
		
		// Register custom post types and taxonomies
		add_action( 'init', array( $this, 'ps_custom_post_types_callback' ), 5 );
		add_action( 'init', array( $this, 'ps_custom_taxonomies_callback' ), 6 );
		
		// Register activation and deactivation hooks
		register_activation_hook( __FILE__, 'ps_on_activate_callback' );
		register_deactivation_hook( __FILE__, 'ps_on_deactivate_callback' );
		
		// Translation-ready
		add_action( 'plugins_loaded', array( $this, 'ps_add_textdomain' ) );
		
		// Add earlier execution as it needs to occur before admin page display
		add_action( 'admin_init', array( $this, 'ps_register_settings' ), 5 );
		
		// Add a sample shortcode
		add_action( 'init', array( $this, 'ps_sample_shortcode' ) );
		
		// Add a sample widget
		add_action( 'widgets_init', array( $this, 'ps_sample_widget' ) );
		
		/*
		 * TODO:
		 * 		template_redirect
		 */
		
		// Add actions for storing value and fetching URL
		// use the wp_ajax_nopriv_ hook for non-logged users (handle guest actions)
 		add_action( 'wp_ajax_store_ajax_value', array( $this, 'store_ajax_value' ) );
 		add_action( 'wp_ajax_fetch_ajax_url_http', array( $this, 'fetch_ajax_url_http' ) );
		
	}	
	
	/**
	 * 
	 * Adding JavaScript scripts
	 * 
	 * Loading existing scripts from wp-includes or adding custom ones
	 * 
	 */
	public function ps_add_JS() {
		wp_enqueue_script( 'jquery' );
		// load custom JSes and put them in footer
		wp_register_script( 'samplescript', plugins_url( '/js/samplescript.js' , __FILE__ ), array('jquery'), '1.0', true );
		wp_enqueue_script( 'samplescript' );
	}
	
	
	/**
	 *
	 * Adding JavaScript scripts for the admin pages only
	 *
	 * Loading existing scripts from wp-includes or adding custom ones
	 *
	 */
	public function ps_add_admin_JS( $hook ) {
		wp_enqueue_script( 'jquery' );
		wp_register_script( 'samplescript-admin', plugins_url( '/js/samplescript-admin.js' , __FILE__ ), array('jquery'), '1.0', true );
		wp_enqueue_script( 'samplescript-admin' );
	}
	
	/**
	 * 
	 * Add CSS styles
	 * 
	 */
	public function ps_add_CSS() {
		wp_register_style( 'samplestyle', plugins_url( '/css/samplestyle.css', __FILE__ ), array(), '1.0', 'screen' );
		wp_enqueue_style( 'samplestyle' );
	}
	
	/**
	 *
	 * Add admin CSS styles - available only on admin
	 *
	 */
	public function ps_add_admin_CSS( $hook ) {
		wp_register_style( 'samplestyle-admin', plugins_url( '/css/samplestyle-admin.css', __FILE__ ), array(), '1.0', 'screen' );
		wp_enqueue_style( 'samplestyle-admin' );
		
		if( 'toplevel_page_ps-plugin-scope' === $hook ) {
			wp_register_style('ps_help_page',  plugins_url( '/help-page.css', __FILE__ ) );
			wp_enqueue_style('ps_help_page');
		}
	}
	
	/**
	 * 
	 * Callback for registering pages
	 * 
	 * This demo registers a custom page for the plugin and a subpage
	 *  
	 */
 
	/**
	 * 
	 * The content of the base page
	 * 
	 */
	public function ps_plugin_base() {
		include_once( PSP_PATH_INCLUDES . '/scope-page-template.php' );
	}
	
	public function ps_plugin_side_access_page() {
		include_once( PSP_PATH_INCLUDES . '/remote-page-template.php' );
	}
	
	/**
	 * 
	 * The content of the subpage 
	 * 
	 * Use some default UI from WordPress guidelines echoed here (the sample above is with a template)
	 * 
	 * @see http://www.onextrapixel.com/2009/07/01/how-to-design-and-style-your-wordpress-plugin-admin-panel/
	 *
	 */
	public function ps_plugin_subpage() {
		echo '<div class="wrap">';
		_e( "<h2>PS Plugin Subpage</h2> ", 'psbase' );
		_e( "I'm a subpage and I know it!", 'psbase' );
		echo '</div>';
	}
	
	/**
	 * 
	 *  Adding right and bottom meta boxes to Pages
	 *   
	 */
	public function ps_meta_boxes_callback() {
		// register side box
		add_meta_box( 
		        'ps_side_meta_box',
		        __( "Campos para el Plugin Scope", 'psbase' ),
		        array( $this, 'ps_side_meta_box' ),
		        'building', // leave empty quotes as '' if you want it on all custom post add/edit screens
		        'side',
		        'high'
		    );
		    
		// register bottom box
		/*
		add_meta_box(
		    	'ps_bottom_meta_box',
		    	__( "Campos para el Plugin Scope Bottom", 'psbase' ), 
		    	array( $this, 'ps_bottom_meta_box' ),
		    	'' // leave empty quotes as '' if you want it on all custom post add/edit screens or add a post type slug
		    );
			*/
	}
	
	/**
	 * 
	 * Init right side meta box here 
	 * @param post $post the post object of the given page 
	 * @param metabox $metabox metabox data
	 */
	public function ps_side_meta_box( $post, $metabox) {
		_e(" ", 'psbase');
		
		// Add some test data here - a custom field, that is

		$Building_Address = '';
		$Contruction_Year = '';
		$file = '';
		$Featured = '';
		$range = '';
		$Number_Floors = '';
		$City = '';
		if ( ! empty ( $post ) ) {
			// Read the database record if we've saved that before
			$Building_Address = get_post_meta( $post->ID, 'Building_Address', true );
			$Contruction_Year = get_post_meta( $post->ID, 'Contruction_Year', true );
			$file = get_post_meta( $post->ID, 'file', true );
			$Featured = get_post_meta( $post->ID, 'Featured', true );
			$range = get_post_meta( $post->ID, 'range', true );
			$Number_Floors = get_post_meta( $post->ID, 'Number_Floors', true );
			$City = get_post_meta( $post->ID, 'City', true );
			
		}
		
	 
		 
    $Featured = get_post_meta( $post->ID, 'text4', true );
    $checked="";	
	?>
	 
	 
		<p>
			<label for="ps-test-input"><?php _e( 'Building Address', 'psbase' ); ?></label>
			<input type="text" id="ps-test-input" name="Building_Address" value="<?php echo $Building_Address; ?>" />
		</p>

				
		<p>
			<label for="ps-test-input"><?php _e( 'Contruction Year', 'psbase' ); ?></label>
			<?php
				echo '<input  type="number" name="Contruction_Year" id="'.$Contruction_Year.'"   value="'.$Contruction_Year.'" /><br/> ';
			?>
		</p>
	
	<?php 
    if($Featured){
			 
	 
        $checked = " checked='checked'";
        }
        else{
			 
	 
        $checked = "";
        }
    ?>
    <p>
        <label for="Featured"><?php _e( 'Featured' ); ?></label>
 
        <input type="checkbox" name="Featured" id="dd_city" value="YES" <?php echo ( $Featured === 'YES' ) ? 'checked' : ''; ?>>
		
    </p>

	<?php 
	
	?>
		
		
		<p>
			<label for="ps-test-input"><?php _e( 'Price Range ', 'psbase' ); ?></label><br/>
			<?php
				echo '<input type="range"  min="0" max="10" name="range" id="'.$range.'"   value="'.$range.'" /><br/> ';
			?>
		</p>

						
		<p>
			<label for="ps-test-input"><?php _e( 'Number of Floors', 'psbase' ); ?></label>
			<?php
				echo '<input  type="number" name="Number_Floors" id="'.$Number_Floors.'"   value="'.$Number_Floors.'" /><br/> ';
			?>
		</p>
  	
		<p>
			<label for="ps-test-input"><?php _e( 'City', 'psbase' ); ?></label><br/>
	 
		<select name="City" id="City">
			<option <?php echo ($City === 'Aventura' ) ? 'selected' : '' ?>>Aventura</option>
			<option <?php echo ($City === 'Brickell' ) ? 'selected' : '' ?>>Brickell</option>
			<option <?php echo ($City === 'Coral Gables' ) ? 'selected' : '' ?>>Coral Gables</option>
			<option <?php echo ($City === 'Downtown' ) ? 'selected' : '' ?>>Downtown</option>
			<option <?php echo ($City === 'Doral' ) ? 'selected' : '' ?>>Doral</option>
			<option <?php echo ($City === 'Fort Lauderdale' ) ? 'selected' : '' ?>>Fort Lauderdale</option>
		</select>
	</p> 
			
			<br/>
		</p>
	
		
		
		<?php
	}
	
	/**
	 * Save the custom field from the side metabox
	 * @param $post_id the current post ID
	 * @return post_id the post ID from the input arguments
	 * 
	 */
	public function ps_save_sample_field( $post_id ) {
		// Avoid autosaves
		if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$slug = 'building';
		// If this isn't a 'book' post, don't update it.
		if ( ! isset( $_POST['post_type'] ) || $slug != $_POST['post_type'] ) {
			return;
		}
		
		// If the custom field is found, update the postmeta record
		// Also, filter the HTML just to be safe
		
		if ( isset( $_POST['Building_Address']  ) ) {
			update_post_meta( $post_id, 'Building_Address',  esc_html( $_POST['Building_Address'] ) );
		}
			
		if ( isset( $_POST['Featured']  ) ) {
			update_post_meta( $post_id, 'Featured',  esc_html( $_POST['Featured'] ) );
		}
		if ( isset( $_POST['Contruction_Year']  ) ) {
			update_post_meta( $post_id, 'Contruction_Year',  esc_html( $_POST['Contruction_Year'] ) );
		}
			
		if ( isset( $_POST['file']  ) ) {
			update_post_meta( $post_id, 'file',  esc_html( $_POST['file'] ) );
		}
			
		if ( isset( $_POST['Featured']  ) ) {
			update_post_meta( $post_id, 'Featured',  esc_html( $_POST['Featured'] ) );
		}
			
		if ( isset( $_POST['range']  ) ) {
			update_post_meta( $post_id, 'range',  esc_html( $_POST['range'] ) );
		}
			
		if ( isset( $_POST['Number_Floors']  ) ) {
			update_post_meta( $post_id, 'Number_Floors',  esc_html( $_POST['Number_Floors'] ) );
		}
		
		if ( isset( $_POST['City']  ) ) {
			update_post_meta( $post_id, 'City',  esc_html( $_POST['City'] ) );
		}
		
		
		
		
		
	}
	
	/**
	 * 
	 * Init bottom meta box here 
	 * @param post $post the post object of the given page 
	 * @param metabox $metabox metabox data
	 */
	public function ps_bottom_meta_boxps_bottom_meta_box( $post, $metabox) {
		_e( "<p>Bottom meta content here</p>", 'psbase' );
	}
	
	/**
	 * Register custom post types
     *
	 */
	public function ps_custom_post_types_callback() {
			$rewrite = array(
		'slug' => 'building',
		'with_front' => true,
		'pages' => true,
		'feeds' => true,
	);
		
		register_post_type( 'building', array(
			'labels' => array(
				'name' => _x( 'Scope ', 'Post Type General Name', 'psbase' ),
		'singular_name' => _x( 'Scope', 'Post Type Singular Name', 'psbase' ),
		'menu_name' => _x( 'Scope ', 'Admin Menu text', 'psbase' ),
		'name_admin_bar' => _x( 'Scope', 'Add New on Toolbar', 'psbase' ),
		'archives' => __( 'Archivos Scope', 'psbase' ),
		'attributes' => __( 'Atributos Scope', 'psbase' ),
		'parent_item_colon' => __( 'Padres Scope:', 'psbase' ),
		'all_items' => __( 'Todas Scope ', 'psbase' ),
		'add_new_item' => __( 'Añadir nueva Scope', 'psbase' ),
		'add_new' => __( 'Añadir nueva', 'psbase' ),
		'new_item' => __( 'Nueva Scope', 'psbase' ),
		'edit_item' => __( 'Editar Scope', 'psbase' ),
		'update_item' => __( 'Actualizar Scope', 'psbase' ),
		'view_item' => __( 'Ver Scope', 'psbase' ),
		'view_items' => __( 'Ver Scope ', 'psbase' ),
		'search_items' => __( 'Buscar Scope', 'psbase' ),
		'not_found' => __( 'No se encontraron Scope .', 'psbase' ),
		'not_found_in_trash' => __( 'Ningún Scope encontrado en la papelera.', 'psbase' ),
		'featured_image' => __( 'Imagen destacada', 'psbase' ),
		'set_featured_image' => __( 'Establecer imagen destacada', 'psbase' ),
		'remove_featured_image' => __( 'Borrar imagen destacada', 'psbase' ),
		'use_featured_image' => __( 'Usar como imagen destacada', 'psbase' ),
		'insert_into_item' => __( 'Insertar en la Scope', 'psbase' ),
		'uploaded_to_this_item' => __( 'Subido a esta Scope', 'psbase' ),
		'items_list' => __( 'Lista de Scope ', 'psbase' ),
		'items_list_navigation' => __( 'Navegación por el listado de Scope ', 'psbase' ),
		'filter_items_list' => __( 'Lista de Scope  filtradas', 'psbase' ),
			),

		'menu_icon' => '',
		'supports' => array('title', 'editor', 'thumbnail', 'author', 'page-attributes', 'post-formats'),
	 
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'has_archive' => 'building',
		'hierarchical' => false,
		'exclude_from_search' => false,
		'show_in_rest' => true,
		'publicly_queryable' => true,
		'capability_type' => 'post',
		'rewrite' => $rewrite,
	     'taxonomies' => array( 'category'), 
 
		 
		));	
		
	}
	
	/**
	 * Register custom taxonomies
     *
	 */
	 /*
	public function ps_custom_taxonomies_callback() {
	    
	    
		$rewrite = array(
		'slug' => 'building_taxonomy',
		'with_front' => true,
		'pages' => true,
		'feeds' => true,
	);
	
		register_taxonomy( 'building_taxonomy', 'building', array(
			'hierarchical' => true,
			'labels' => array(
				'name' => _x( "Plugin Scope Item Taxonomies", 'taxonomy general name', 'psbase' ),
				'singular_name' => _x( "Plugin Scope Item Taxonomy", 'taxonomy singular name', 'psbase' ),
				'search_items' =>  __( "Search Taxonomies", 'psbase' ),
				'popular_items' => __( "Popular Taxonomies", 'psbase' ),
				'all_items' => __( "All Taxonomies", 'psbase' ),
				'parent_item' => null,
				'parent_item_colon' => null,
				'edit_item' => __( "Edit Plugin Scope Item Taxonomy", 'psbase' ), 
				'update_item' => __( "Update Plugin Scope Item Taxonomy", 'psbase' ),
				'add_new_item' => __( "Add New Plugin Scope Item Taxonomy", 'psbase' ),
				'new_item_name' => __( "New Plugin Scope Item Taxonomy Name", 'psbase' ),
				'separate_items_with_commas' => __( "Separate Scope Item taxonomies with commas", 'psbase' ),
				'add_or_remove_items' => __( "Add or remove Plugin Scope Item taxonomy", 'psbase' ),
				'choose_from_most_used' => __( "Choose from the most used Plugin Scope Item taxonomies", 'psbase' )
			),
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => $rewrite,
		));
		
		register_taxonomy_for_object_type( 'building_taxonomy', 'building' );
	}
*/	
	/**
	 * Initialize the Settings class
	 * 
	 * Register a settings section with a field for a secure WordPress admin option creation.
	 * 
	 */
	public function ps_register_settings() {
		require_once( PSP_PATH . '/ps-plugin-settings.class.php' );
		new PS_Plugin_Settings();
	}
	
	/**
	 * Register a sample shortcode to be used
	 * 
	 * First parameter is the shortcode name, would be used like: [pssampcode]
	 * 
	 */
	public function ps_sample_shortcode() {
		add_shortcode( 'pssampcode', array( $this, 'ps_sample_shortcode_body' ) );
	}
	
	/**
	 * Returns the content of the sample shortcode, like [pssamplcode]
	 * @param array $attr arguments passed to array, like [pssamcode attr1="one" attr2="two"]
	 * @param string $content optional, could be used for a content to be wrapped, such as [pssamcode]somecontnet[/pssamcode]
	 */
	public function ps_sample_shortcode_body( $attr, $content = null ) {
		/*
		 * Manage the attributes and the content as per your request and return the result
		 */
		return __( 'Sample Output', 'psbase');
	}
	
	/**
	 * Hook for including a sample widget with options
	 */
	public function ps_sample_widget() {
		include_once PSP_PATH_INCLUDES . '/ps-sample-widget.class.php';
	}
	
	/**
	 * Add textdomain for plugin
	 */
	public function ps_add_textdomain() {
		load_plugin_textdomain( 'psbase', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}
	
	/**
	 * Callback for saving a simple AJAX option with no page reload
	 */
	public function store_ajax_value() {
		if( isset( $_POST['data'] ) && isset( $_POST['data']['ps_option_from_ajax'] ) ) {
			update_option( 'ps_option_from_ajax' , $_POST['data']['ps_option_from_ajax'] );
		}	
		die();
	}
	
	/**
	 * Callback for getting a URL and fetching it's content in the admin page
	 */
	public function fetch_ajax_url_http() {
		if( isset( $_POST['data'] ) && isset( $_POST['data']['ps_url_for_ajax'] ) ) {
			$ajax_url = $_POST['data']['ps_url_for_ajax'];
			
			$response = wp_remote_get( $ajax_url );
			
			if( is_wp_error( $response ) ) {
				echo json_encode( __( 'Invalid HTTP resource', 'psbase' ) );
				die();
			}
			
			if( isset( $response['body'] ) ) {
				if( preg_match( '/<title>(.*)<\/title>/', $response['body'], $matches ) ) {
					echo json_encode( $matches[1] );
					die();
				}
			}
		}
		echo json_encode( __( 'No title found or site was not fetched properly', 'psbase' ) );
		die();
	}
	
}
function _CreateCategory(){
$my_cat = array(
    'taxonomy'             => 'category',
    'cat_name' => 'Preconstruction', 
    'category_description' => '',
    'category_parent' => '');

// Create the category
wp_insert_category($my_cat);
}
add_action('admin_init','_CreateCategory');
function _CreateCategory1(){
$my_cat = array(
    'taxonomy'             => 'category',
    'cat_name' => 'Luxury Condo', 
    'category_description' => '',
    'category_parent' => '');

// Create the category
wp_insert_category($my_cat);
}
add_action('admin_init','_CreateCategory1');
/**
 * Register activation hook
 *
 */
function ps_on_activate_callback() {
	// do something on activation
}

/**
 * Register deactivation hook
 *
 */
function ps_on_deactivate_callback() {
	// do something when deactivated
}
function reset_permalinks() {
    global $wp_rewrite;
    $wp_rewrite->set_permalink_structure( '/%postname%/' );
}
add_action( 'init', 'reset_permalinks' );
// Initialize everything
$ps_plugin_base = new PS_Plugin_Base();
