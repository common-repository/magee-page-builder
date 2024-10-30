<?php
class Mpb_Post_Client
{
    function __construct()
    {
        add_action( 'init', array($this,'create_client') );
		add_filter('manage_edit-mpb_client_columns', array($this,'edit_columns'));
		add_action('manage_mpb_client_posts_custom_column',  array($this,'custom_columns'), 10, 2 );

    }
    

    function create_client()
    {
		
		/*  register post type */
        $labels = array(
            'name' =>  __( 'Clients', 'magee-page-builder' ),   
            'singular_name' =>  __( 'Clients', 'magee-page-builder' ),     
            'add_new' =>  __( 'Add Client', 'magee-page-builder' ),   
            'add_new_item' => __( 'Add a Client', 'magee-page-builder' ),   
            'edit_item' => __( 'Edit Client', 'magee-page-builder' ),   
            'new_item' => __( 'New Client', 'magee-page-builder' ),    
            'not_found' =>  __( 'Client not found.', 'magee-page-builder' ),   
            'parent_item_colon' => '',  
            'menu_name' => __( 'Clients', 'magee-page-builder' ),   
            'menu_position' => 6
        );   
        $args = array(   
            'labels' => $labels,
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
			'menu_icon'=> 'dashicons-businessman',
            'supports' => array('title'),
        );

        register_post_type( 'mpb_client', $args);
		
		/*  register taxonomy */
		$category_labels = array(
		  'name'              => __( 'Client Groups', 'magee-page-builder' ),
		  'singular_name'     => __( 'Client Groups', 'magee-page-builder' ),
		  'search_items'      => __( 'Search Client Group', 'magee-page-builder' ),
		  'all_items'         => __( 'All Clients', 'magee-page-builder' ),
		  'edit_item'         => __( 'Edit Client Group', 'magee-page-builder' ),
		  'update_item'       => __( 'Update Client Group', 'magee-page-builder' ),
		  'add_new_item'      => __( 'Add New Client Group', 'magee-page-builder' ),
		  'new_item_name'     => __( 'New Client Group', 'magee-page-builder' ),
		  'menu_name'         => __( 'Groups', 'magee-page-builder' ),
		);
		
	    $args = array(
            'labels' => $category_labels,
            'hierarchical' => true,
       );
		
       register_taxonomy( 'mpb_client_group', 'mpb_client', $args );

    }
	
	
	function edit_columns($columns){
	  $columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => __("Title",'magee-page-builder'),
		"mpb_client_group" => __("Group",'magee-page-builder'),
		"date" => __("Date",'magee-page-builder'),
	  );
	 
	  return $columns;
	}
	
	function custom_columns($column, $post_id ){
		global $post;
		
		switch ($column) {
			case 'mpb_client_group' :
			$terms = get_the_term_list( $post_id , 'mpb_client_group' , '' , ',' , '' );
            if ( is_string( $terms ) )
                echo strip_tags($terms);
            else
                _e( 'Unable to get client group(s)', 'magee-page-builder' );
            break;
		}
	}
    
    
}


new Mpb_Post_Client;
