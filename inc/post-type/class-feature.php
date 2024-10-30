<?php
class Mpb_Post_Feature
{
    function __construct()
    {
        add_action( 'init', array($this,'create_feature') );
        add_filter('manage_edit-mpb_feature_columns', array($this,'edit_columns'));
		add_action('manage_mpb_feature_posts_custom_column',  array($this,'custom_columns'), 10, 2 );
    }
    
    function create_feature()
    {
		
		/*  register post type */
        $labels = array(
            'name' =>  __( 'Features', 'magee-page-builder' ),   
            'singular_name' =>  __( 'Features', 'magee-page-builder' ),     
            'add_new' =>  __( 'Add Feature', 'magee-page-builder' ),   
            'add_new_item' => __( 'Add a Feature', 'magee-page-builder' ),   
            'edit_item' => __( 'Edit Feature', 'magee-page-builder' ),   
            'new_item' => __( 'New Feature', 'magee-page-builder' ),    
            'not_found' =>  __( 'Feature not found.', 'magee-page-builder' ),   
            'parent_item_colon' => '',  
            'menu_name' => __( 'Features', 'magee-page-builder' ),   
            'menu_position' => 5
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
			'menu_icon'=> 'dashicons-star-filled',
            'supports' => array('title','editor'),
        );

        register_post_type( 'mpb_feature', $args);
		
		/*  register taxonomy */
		$category_labels = array(
		  'name'              => __( 'Feature Groups', 'magee-page-builder' ),
		  'singular_name'     => __( 'Feature Groups', 'magee-page-builder' ),
		  'search_items'      => __( 'Search Feature Group', 'magee-page-builder' ),
		  'all_items'         => __( 'All Features', 'magee-page-builder' ),
		  'edit_item'         => __( 'Edit Feature Group', 'magee-page-builder' ),
		  'update_item'       => __( 'Update Feature Group', 'magee-page-builder' ),
		  'add_new_item'      => __( 'Add New Feature Group', 'magee-page-builder' ),
		  'new_item_name'     => __( 'New Feature Group', 'magee-page-builder' ),
		  'menu_name'         => __( 'Groups', 'magee-page-builder' ),
		);
		
	    $args = array(
            'labels' => $category_labels,
            'hierarchical' => true,
       );
		
       register_taxonomy( 'mpb_feature_group', 'mpb_feature', $args );

    }
	
	function edit_columns($columns){
	  $columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => __("Title",'magee-page-builder'),
		"mpb_feature_group" => __("Group",'magee-page-builder'),
		"date" => __("Date",'magee-page-builder'),
	  );
	 
	  return $columns;
	}
	
	function custom_columns($column, $post_id ){
		global $post;
		
		switch ($column) {
			case 'mpb_feature_group' :
			$terms = get_the_term_list( $post_id , 'mpb_feature_group' , '' , ',' , '' );
            if ( is_string( $terms ) )
                echo strip_tags($terms);
            else
                _e( 'Unable to get feature group(s)', 'magee-page-builder' );
            break;
		}
	}
	
    
    
}
	
	

new Mpb_Post_Feature;
