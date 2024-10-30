<?php
/*
  Plugin Name: Magee Page Builder
  Plugin URI: http://www.mageewp.com/
  Description: Magee Page Builder.
  Version: 1.0.0
  Author: MageeWP
  Author URI: http://www.mageewp.com
  Text Domain: magee-page-builder
  Domain Path: /languages
  License: GPLv2 or later
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'Mpb_Post' ) ) :

class Mpb_Post{
	
	public function __construct( $args = array() ) {
		
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'admin_enqueue_scripts',  array($this,'admin_scripts' ));
		add_action( 'wp_enqueue_scripts',  array($this,'frontend_scripts' ));
		
		$this->load_post_type();
		$this->includes();
		
	}
	
	
	public static function init() {

	   load_plugin_textdomain( 'magee-page-builder', false,  dirname( plugin_basename(  __FILE__  ) )  . '/languages/'  );
		
    }
	
	public static  function load_post_type(){
		
		foreach( glob( plugin_dir_path( __FILE__ ) . 'inc/post-type/*.php' ) as $filename ) {
				require_once $filename;
			}
		
		}
		
	private function includes() {

		require(  plugin_dir_path( __FILE__ ) . 'inc/metaboxes/metabox-options.php' );
		
	}
	
	
   public static  function admin_scripts() {
	  // wp_enqueue_style('bootstrap',  plugins_url( 'assets/plugins/bootstrap/css/bootstrap.min.css', __FILE__  ), '', '3.3.7', false );
	  wp_enqueue_style('font-awesome',  plugins_url( 'assets/plugins/font-awesome/css/font-awesome.min.css', __FILE__  ), '', '4.6.3', false );
	  wp_enqueue_style( 'fontawesome-iconpicker',  plugins_url( 'assets/plugins/fontawesome-iconpicker/css/fontawesome-iconpicker.min.css',__FILE__ ), '','1.0.0', false );
	  wp_enqueue_style( 'mpb-post-admin',  plugins_url( 'assets/css/admin.css',__FILE__ ), '','1.0.0', false );
	//  wp_enqueue_script( 'bootstrap',  plugins_url( 'assets/plugins/bootstrap/js/bootstrap.min.js',__FILE__ ), array( 'jquery'),'3.3.7', true );
	  wp_enqueue_script( 'fontawesome-iconpicker',  plugins_url( 'assets/plugins/fontawesome-iconpicker/js/fontawesome-iconpicker.js',__FILE__ ), array( 'jquery'),'1.0.0', true );
	  
	   wp_enqueue_script( 'mpb-post-admin',  plugins_url( 'assets/js/admin.js',__FILE__ ), array( 'jquery'),'1.0.0', true );
	  
	  
     }
  
  
   public static  function frontend_scripts() {	
      wp_enqueue_style('bootstrap',  plugins_url( 'assets/plugins/bootstrap/css/bootstrap.min.css', __FILE__  ), '', '3.3.7', false );
      wp_enqueue_style('font-awesome',  plugins_url( 'assets/plugins/font-awesome/css/font-awesome.min.css', __FILE__  ), '', '4.6.3', false );
	  wp_enqueue_script( 'bootstrap',  plugins_url( 'assets/plugins/bootstrap/js/bootstrap.min.js',__FILE__ ), array( 'jquery'),'3.3.7', true );
      wp_enqueue_script( 'mpb-post-main',  plugins_url( 'assets/js/main.js',__FILE__ ), array( 'jquery'),'1.0.0', true );
	  wp_localize_script( 'mpb-post-main', 'mpb_post_params', array(
			  'ajaxurl'    => admin_url('admin-ajax.php'),
			  ) );
	  
	  }

	
	}
	
	new Mpb_Post;

endif;