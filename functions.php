<?php
	
class react_ang_theme {
	
	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, '__react_ang_scripts' ) );
		add_action( 'print_scripts', array( $this, '_react_ang_print_scripts' ) );
	}
	
	function __react_ang_scripts() {
		
		wp_enqueue_script( 'react_ang_main', get_template_directory_uri().'/build/js/react_angular.min.js', array( 'jquery' ), null, true );
		
		wp_localize_script( 'react_ang_main', 'ajaxInfo', 
			array( 
				'json_url' => get_bloginfo('wpurl').'/wp-json/',
				'nonce' => wp_create_nonce( 'wp_json' ),
				'template_directory' => get_template_directory_uri(),
				'site_url' => get_bloginfo('wpurl')
			) 
		);
		
		// Enqueue Ract > Date Time Formatter
		wp_enqueue_script( 'moment.js', get_template_directory_uri().'/build/js/moment.js', array( 'jquery', 'react_ang_main' ), null, true );
		
		wp_enqueue_script( 'react_app', get_template_directory_uri().'/build/js/react_app_js.js', array( 'jquery', 'react_ang_main', 'moment.js' ), null, true );
		
		wp_localize_script( 'react_app', 'ajax_data', 
			array( 
				'ajax_url' => admin_url('admin-ajax.php'),
				'preloader_gif' => admin_url( 'images/wpspin_light.gif' )
			) 
		);
		
		wp_enqueue_script( 'bootstrap', get_template_directory_uri().'/build/js/bootstrap.js', array( 'jquery' ), null, true );
		wp_enqueue_script( 'scripts', get_template_directory_uri().'/build/js/scripts.js', array( 'bootstrap' ), null, true );
		
		// enqueue main styles
		wp_enqueue_style( 'styles', get_template_directory_uri().'/build/css/styles.css', array(), null, 'all' );
		
	}
	
	function _react_ang_print_scripts() {
		wp_enqueue_script( 'tiny_mce' );	
	}
}


new react_ang_theme();






/**
*	AJAX Handlers to return all sorts of data in various locations throughout the theme!
*	
*	@Compiled by Evan Herman / Yikes Inc.
**/



// Same handler function...
add_action( 'wp_ajax_get_attachment_URL', 'get_attachment_URL_callback' );
function get_attachment_URL_callback() {
	global $wpdb;
	$attachment_ID = intval( $_POST['attachment_id'] );
    $attachment_src = wp_get_attachment_image_src( $attachment_ID, 'full' );
	wp_send_json( esc_url( urlencode( $attachment_src[0] ) ) );
	wp_die();
}
	
?>