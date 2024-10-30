<?php
class Mpb_Post_Testimonial
{
    function __construct()
    {
        add_action( 'init', array($this,'create_testimonial') );
		add_filter('manage_edit-mpb_testimonial_columns', array($this,'edit_columns'));
		add_action('manage_mpb_testimonial_posts_custom_column',  array($this,'custom_columns'), 10, 2 );

    }
    

    function create_testimonial()
    {
		
		/*  register post type */
        $labels = array(
            'name' =>  __( 'Testimonials', 'magee-page-builder' ),   
            'singular_name' =>  __( 'Testimonials', 'magee-page-builder' ),     
            'add_new' =>  __( 'Add Testimonial', 'magee-page-builder' ),   
            'add_new_item' => __( 'Add a Testimonial', 'magee-page-builder' ),   
            'edit_item' => __( 'Edit Testimonial', 'magee-page-builder' ),   
            'new_item' => __( 'New Testimonial', 'magee-page-builder' ),    
            'not_found' =>  __( 'Testimonial not found.', 'magee-page-builder' ),   
            'parent_item_colon' => '',  
            'menu_name' => __( 'Testimonials', 'magee-page-builder' ),   
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
			'menu_icon'=> 'dashicons-format-chat',
            'supports' => array('title','editor'),
        );

        register_post_type( 'mpb_testimonial', $args);
		
		/*  register taxonomy */
		$category_labels = array(
		  'name'              => __( 'Testimonial Groups', 'magee-page-builder' ),
		  'singular_name'     => __( 'Testimonial Groups', 'magee-page-builder' ),
		  'search_items'      => __( 'Search Testimonial Group', 'magee-page-builder' ),
		  'all_items'         => __( 'All Testimonials', 'magee-page-builder' ),
		  'edit_item'         => __( 'Edit Testimonial Group', 'magee-page-builder' ),
		  'update_item'       => __( 'Update Testimonial Group', 'magee-page-builder' ),
		  'add_new_item'      => __( 'Add New Testimonial Group', 'magee-page-builder' ),
		  'new_item_name'     => __( 'New Testimonial Group', 'magee-page-builder' ),
		  'menu_name'         => __( 'Groups', 'magee-page-builder' ),
		);
		
	    $args = array(
            'labels' => $category_labels,
            'hierarchical' => true,
       );
		
       register_taxonomy( 'mpb_testimonial_group', 'mpb_testimonial', $args );

    }
	
	
	function edit_columns($columns){
	  $columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => __("Title",'magee-page-builder'),
		"mpb_testimonial_group" => __("Group",'magee-page-builder'),
		"date" => __("Date",'magee-page-builder'),
	  );
	 
	  return $columns;
	}
	
	function custom_columns($column, $post_id ){
		global $post;
		
		switch ($column) {
			case 'mpb_testimonial_group' :
			$terms = get_the_term_list( $post_id , 'mpb_testimonial_group' , '' , ',' , '' );
            if ( is_string( $terms ) )
                echo strip_tags($terms);
            else
                _e( 'Unable to get testimonial group(s)', 'magee-page-builder' );
            break;
		}
	}
    
    
}


new Mpb_Post_Testimonial;
