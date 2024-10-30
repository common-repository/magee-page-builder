<?php
class Mpb_Post_Portfolio
{
    function __construct()
    {
        add_action( 'init', array($this,'create_portfolio') );
		add_filter('manage_edit-mpb_portfolio_columns', array($this,'edit_columns'));
		add_action('manage_mpb_portfolio_posts_custom_column',  array($this,'custom_columns'), 10, 2 );

    }
    

    function create_portfolio()
    {
		
		/*  register post type */
        $labels = array(
            'name' =>  __( 'Portfolios', 'magee-page-builder' ),   
            'singular_name' =>  __( 'Portfolios', 'magee-page-builder' ),     
            'add_new' =>  __( 'Add Portfolio', 'magee-page-builder' ),   
            'add_new_item' => __( 'Add a Portfolio', 'magee-page-builder' ),   
            'edit_item' => __( 'Edit Portfolio', 'magee-page-builder' ),   
            'new_item' => __( 'New Portfolio', 'magee-page-builder' ),    
            'not_found' =>  __( 'Portfolio not found.', 'magee-page-builder' ),   
            'parent_item_colon' => '',  
            'menu_name' => __( 'Portfolios', 'magee-page-builder' ),   
            'menu_position' => 6
        );   
        $args = array(   
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
			'menu_icon'=> 'dashicons-portfolio',
            'supports' => array('title','editor','excerpt','page-attributes')
        );

        register_post_type( 'mpb_portfolio', $args);
		
		/*  register taxonomy */
		$category_labels = array(
		  'name'              => __( 'Portfolio Categories', 'magee-page-builder' ),
		  'singular_name'     => __( 'Portfolio Categories', 'magee-page-builder' ),
		  'search_items'      => __( 'Search Portfolio Category', 'magee-page-builder' ),
		  'all_items'         => __( 'All Portfolios', 'magee-page-builder' ),
		  'edit_item'         => __( 'Edit Portfolio Category', 'magee-page-builder' ),
		  'update_item'       => __( 'Update Portfolio Category', 'magee-page-builder' ),
		  'add_new_item'      => __( 'Add New Portfolio Category', 'magee-page-builder' ),
		  'new_item_name'     => __( 'New Portfolio Category', 'magee-page-builder' ),
		  'menu_name'         => __( 'Categories', 'magee-page-builder' ),
		);
		
	    $args = array(
            'labels' => $category_labels,
            'hierarchical' => true,
       );
		
       register_taxonomy( 'mpb_portfolio_category', 'mpb_portfolio', $args );

    }
	
	
	function edit_columns($columns){
	  $columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => __("Title",'magee-page-builder'),
		"mpb_portfolio_category" => __("Category",'magee-page-builder'),
		"date" => __("Date",'magee-page-builder'),
	  );
	 
	  return $columns;
	}
	
	function custom_columns($column, $post_id ){
		global $post;
		
		switch ($column) {
			case 'mpb_portfolio_category' :
			$terms = get_the_term_list( $post_id , 'mpb_portfolio_category' , '' , ',' , '' );
            if ( is_string( $terms ) )
                echo strip_tags($terms);
            else
                _e( 'Unable to get portfolio category.', 'magee-page-builder' );
            break;
		}
	}
    
    
}


new Mpb_Post_Portfolio;
