<?php
class Mpb_Post_Team
{
    function __construct()
    {
        add_action( 'init', array($this,'create_team') );
		add_filter('manage_edit-mpb_team_columns', array($this,'edit_columns'));
		add_action('manage_mpb_team_posts_custom_column',  array($this,'custom_columns'), 10, 2 );

    }
    

    function create_team()
    {
		
		/*  register post type */
        $labels = array(
            'name' =>  __( 'Team', 'magee-page-builder' ),   
            'singular_name' =>  __( 'Team', 'magee-page-builder' ),     
            'add_new' =>  __( 'Add Member', 'magee-page-builder' ),   
            'add_new_item' => __( 'Add a Member', 'magee-page-builder' ),   
            'edit_item' => __( 'Edit Member', 'magee-page-builder' ),   
            'new_item' => __( 'New Member', 'magee-page-builder' ),    
            'not_found' =>  __( 'Member not found.', 'magee-page-builder' ),   
            'parent_item_colon' => '',  
            'menu_name' => __( 'Team', 'magee-page-builder' ),   
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
			'menu_icon'=> 'dashicons-groups',
            'supports' => array('title','editor'),
        );

        register_post_type( 'mpb_team', $args);
		
		/*  register taxonomy */
		$category_labels = array(
		  'name'              => __( 'Categories', 'magee-page-builder' ),
		  'singular_name'     => __( 'Categories', 'magee-page-builder' ),
		  'search_items'      => __( 'Search Category', 'magee-page-builder' ),
		  'all_items'         => __( 'All Categories', 'magee-page-builder' ),
		  'edit_item'         => __( 'Edit Category', 'magee-page-builder' ),
		  'update_item'       => __( 'Update Category', 'magee-page-builder' ),
		  'add_new_item'      => __( 'Add New Category', 'magee-page-builder' ),
		  'new_item_name'     => __( 'New Category', 'magee-page-builder' ),
		  'menu_name'         => __( 'Categories', 'magee-page-builder' ),
		);
		
	    $args = array(
            'labels' => $category_labels,
            'hierarchical' => true,
       );
		
       register_taxonomy( 'mpb_team_group', 'mpb_team', $args );

    }
	
	
	function edit_columns($columns){
	  $columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => __("Title",'magee-page-builder'),
		"mpb_team_group" => __("Categories",'magee-page-builder'),
		"date" => __("Date",'magee-page-builder'),
	  );
	 
	  return $columns;
	}
	
	function custom_columns($column, $post_id ){
		global $post;
		
		switch ($column) {
			case 'mpb_team_group' :
			$terms = get_the_term_list( $post_id , 'mpb_team_group' , '' , ',' , '' );
            if ( is_string( $terms ) )
                echo strip_tags($terms);
            else
                _e( 'Unable to get team group(s)', 'magee-page-builder' );
            break;
		}
	}
    
    
}


new Mpb_Post_Team;
