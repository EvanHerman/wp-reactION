<?php
	
class react_ang_theme {
	
	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, '__react_ang_scripts' ) );
		add_action( 'print_scripts', array( $this, '_react_ang_print_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, '__react_ang_admin_scripts' ) ); // icon picker menu chooser
	}
	
	function __react_ang_scripts() {
		
		wp_enqueue_script( 'react_ang_main', get_template_directory_uri().'/build/js/react_angular.min.js', array( 'jquery' ), null, true );
		
		wp_localize_script( 'react_ang_main', 'ajaxInfo', 
			array( 
				'json_url' => esc_url_raw( trailingslashit( rest_url() ) ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
				'template_directory' => get_template_directory_uri(),
				'site_url' => get_bloginfo('wpurl')
			) 
		);
	
		wp_enqueue_script( 'react_app', get_template_directory_uri().'/build/js/react_app_js.js', array( 'jquery', 'react_ang_main' ), null, true );
		
		wp_localize_script( 'react_app', 'ajax_data', 
			array( 
				'ajax_url' => admin_url('admin-ajax.php'),
				'preloader_gif' => admin_url( 'images/wpspin_light.gif' )
			) 
		);
		
		wp_enqueue_script( 'bootstrap', get_template_directory_uri().'/build/js/bootstrap.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'scripts', get_template_directory_uri().'/build/js/scripts.js', array( 'react_app', 'bootstrap' ), null, true );
		wp_enqueue_script( 'nav-menu', get_template_directory_uri().'/build/js/nav-menu-script.min.js', array( 'scripts' ), null, true );
		// enqueue main styles
		wp_enqueue_style( 'styles', get_template_directory_uri().'/build/css/styles.css', array(), null, 'all' );
		
	}
	
	public function __react_ang_admin_scripts( $hook ) {
		if( 'nav-menus.php' == $hook ) {
			wp_enqueue_style( 'fa-styles', get_template_directory_uri() . '/assets/fonts/font-awesome-4.4.0/css/font-awesome.min.css', array(), null, 'all' );
			wp_enqueue_style( 'icon-picker-styles', get_template_directory_uri().'/build/css/admin-iconpicker-styles.min.css', array( 'fa-styles' ), null, 'all' );
			wp_enqueue_script( 'icon-picker-scripts', get_template_directory_uri().'/build/js/admin-iconpicker-script.min.js', array( 'jquery' ), null, true );
		}
	}
	
	function _react_ang_print_scripts() {
		wp_enqueue_script( 'tiny_mce' );	
	}
	
}


new react_ang_theme();


/**
*	Enqueue scripts & styles in our header
*
*	
**/
function euqueue_script_styles_header() {
	// styles
		// font awesome
		wp_enqueue_style( 'fa-styles', get_template_directory_uri() . '/assets/fonts/font-awesome-4.4.0/css/font-awesome.min.css', array( 'styles' ), null, 'all' );
		// normalize
		wp_enqueue_style( 'normalize', get_template_directory_uri() . '/assets/css/nav-menu/normalize.css', array(), null, 'all' );
		// example
		wp_enqueue_style( 'demo', get_template_directory_uri() . '/assets/css/nav-menu/demo.css', array( 'styles', 'fa-styles' ), null, 'all' );
		// example
		wp_enqueue_style( 'menu-elastic', get_template_directory_uri() . '/assets/css/nav-menu/menu_sideslide.css', array( 'styles', 'fa-styles' ), null, 'all' );
		// wp_enqueue_script( 'classie.menu', get_template_directory_uri().'/assets/js/nav-menu/main3.js', array( 'scripts' ), null, true );
	// scripts
	
}
add_action( 'wp_enqueue_scripts', 'euqueue_script_styles_header' );


/**
*	AJAX Handlers to return all sorts of data in various locations throughout the theme!
*	
*	@Compiled by Evan Herman / Yikes Inc.
**/

/*
* User Logged In check
*/
add_action('wp_ajax_user_check', 'UserCheck');
add_action('wp_ajax_nopriv_user_check', 'UserCheck');
function UserCheck(){
	if(is_user_logged_in()){
		echo 'true';
	}
die();
}

/*
*	Get post attachment URL
*/
add_action( 'wp_ajax_get_attachment_URL', 'get_attachment_URL_callback' );
function get_attachment_URL_callback() {
	global $wpdb;
	$attachment_ID = intval( $_POST['attachment_id'] );
    $attachment_src = wp_get_attachment_image_src( $attachment_ID, 'full' );
	wp_send_json( esc_url( urlencode( $attachment_src[0] ) ) );
	wp_die();
}

/*
*	Get nav menu items
*	Ajax handler
*/	
add_action( 'wp_ajax_get_main_nav', 'get_main_nav_callback' );
add_action( 'wp_ajax_no_priv_get_main_nav', 'get_main_nav_callback' );
function get_main_nav_callback() {
	if( has_nav_menu( 'main_nav' ) ) {
		$nav_args = array(
			'theme_location' => 'main_nav',
			'walker' => new wp_reaction_walker_nav_menu,
			'items_wrap' => '%3$s'
		);
		$nav_menu = wp_nav_menu( $nav_args );
	} else {
		if( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$nav_menu = '<a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '">' . __( 'Register a menu to get started', 'wp-reaction' ) . '</a>';
	}
	echo 'test';
	wp_die();
}

if ( ! function_exists( '_react_theme_setup' ) ) :
	
	function _react_theme_setup() {
	
		// include our custom main menu nav walker
		require_once get_template_directory() . '/lib/main-menu-nav-walker.php';
		// include our custom edit menu walker 
		require_once get_template_directory() . '/lib/edit_custom_walker.php';
		
		// register default navigation items
		register_nav_menus( array(
			'main_nav' => 'Main Navigation (slideout menu)',
		) );
				
	}
		
endif;
add_action( 'after_setup_theme', '_react_theme_setup' );

/**
*	Extend the WP API with customendpoints
*	@since 0.1
*	Help Resource: http://v2.wp-api.org/extending/adding/
**/

/* 
*	Include menu endpoint extension class
*	@ /menus/
*	@ /menu/<id>
*	@ /menu-locations/
*	@ /menu-location/<menu-location> (eg: main_nav)
*/
include_once dirname( __FILE__ ) . '/lib/api-endpoint-extensions/menu-extension.php';

/** End WP API Endpoint Extensions **/

/***
*	Custom Nav Walker/Edit Menu Walker Functions	
*	@since 0.1
***/

/**
* Define new Walker edit menu walker (adds our icon class field to the edit menu page)
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function _reaction_edit_walker($walker,$menu_id) {

    return 'Reaction_Walker_Nav_Menu_Edit';
    
}
add_filter( 'wp_edit_nav_menu_walker', '_reaction_edit_walker', 10, 2 );

/**
 * Add custom fields to $item nav object
 * in order to be used in custom Walker
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function reaction_add_custom_nav_fields( $menu_item ) {

    $menu_item->icon_class = get_post_meta( $menu_item->ID, '_menu_item_icon_class', true );
    return $menu_item;
    
}
// add custom menu fields to menu
add_filter( 'wp_setup_nav_menu_item', 'reaction_add_custom_nav_fields' );	

/**
 * Save menu custom fields
 *
 * @access      public
 * @since       1.0 
 * @return      void
*/
function reaction_update_custom_nav_fields( $menu_id, $menu_item_db_id, $args ) {
	
    // Check if element is properly sent
    if ( is_array( $_REQUEST['menu-item-icon-class']) ) {
        $subtitle_value = $_REQUEST['menu-item-icon-class'][$menu_item_db_id];
        update_post_meta( $menu_item_db_id, '_menu_item_icon_class', $subtitle_value );
    }
	    
}
// save menu custom fields
add_action( 'wp_update_nav_menu_item', 'reaction_update_custom_nav_fields', 10, 3 );	

/***
*	End Custom Nav Walker/Edit Menu Walker Functions
*	
***/

?>