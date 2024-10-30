<?php

 
add_filter( 'mpb_meta_boxes', 'mpb_feature_metaboxes' );

function mpb_feature_metaboxes( array $meta_boxes ) {

	$prefix = '_mpb_';
	$meta_boxes['feature_metabox'] = array(
		'id'         => 'feature_metabox',
		'title'      => __( 'Feature Metabox', 'magee-page-builder' ),
		'pages'      => array( 'mpb_feature', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'mpb_styles' => true, // Enqueue the MPB stylesheet on the frontend
		'fields'     => array(

			array(
				'name' => __( 'Icon', 'magee-page-builder' ),
				'desc' => __( 'field description (optional)', 'magee-page-builder' ),
				'id'   => $prefix . 'feature_icon',
				'type' => 'icon_picker',
				'default' =>'fa-archive'
				// 'repeatable' => true,
			),
			array(
				'name' => __( 'Image Icon', 'magee-page-builder' ),
				'desc' => __( 'Upload an image icon.', 'magee-page-builder' ),
				'id'   => $prefix . 'feature_image_icon',
				'type' => 'file',
			),

			array(
				'name'    => __( 'Icon Color', 'magee-page-builder' ),
				'desc'    => '',
				'id'      => $prefix . 'feature_icon_color',
				'type'    => 'colorpicker',
				'default' => '#26b9a3'
			),
			array(
				'name'    => __( 'Title Link', 'magee-page-builder' ),
				'desc'    => '',
				'id'      => $prefix . 'feature_link',
				'type'    => 'text',
				'default' => ''
			),
			array(
						'name' => __( 'Target', 'magee-page-builder' ),
						'id'   => $prefix . 'feature_target',
						'type'    => 'select',
						'options' => array(
							'_blank' => __( 'Blank', 'magee-page-builder' ),
							'_self'   => __( 'Self', 'magee-page-builder' ),
						),

		),
			),
	);
	
	$meta_boxes['testimonial_metabox'] = array(
		'id'         => 'testimonial_metabox',
		'title'      => __( 'Testimonial Metabox', 'magee-page-builder' ),
		'pages'      => array( 'mpb_testimonial', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'mpb_styles' => true, // Enqueue the MPB stylesheet on the frontend
		'fields'     => array(
							  
			array(
				'name' => __( 'Byline', 'magee-page-builder' ),
				'desc' => '',
				'id'   => $prefix . 'testimonial_byline',
				'type' => 'text_small',
				'default' =>''
				// 'repeatable' => true,
			),

			array(
				'name' => __( 'Avatar', 'magee-page-builder' ),
				'desc' => __( 'Upload avatar.', 'magee-page-builder' ),
				'id'   => $prefix . 'testimonial_avatar',
				'type' => 'file',
			),
		),
	);
	
	
	$meta_boxes['team_metabox'] = array(
		'id'         => 'team_metabox',
		'title'      => __( 'Team Metabox', 'magee-page-builder' ),
		'pages'      => array( 'mpb_team', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'mpb_styles' => true, // Enqueue the MPB stylesheet on the frontend
		'fields'     => array(
			
			array(
				'name' => __( 'Avatar', 'magee-page-builder' ),
				'desc' => __( 'Upload avatar.', 'magee-page-builder' ),
				'id'   => $prefix . 'team_avatar',
				'type' => 'file',
			),
			
			array(
				'name' => __( 'Byline', 'magee-page-builder' ),
				'desc' => '',
				'id'   => $prefix . 'team_byline',
				'type' => 'text_small',
				'default' =>''
				// 'repeatable' => true,
			),

		),
	);
	
	/**
	 * Repeatable Field Groups
	 */
	$meta_boxes['team_social_icon'] = array(
		'id'         => 'team_social_icon',
		'title'      => __( 'Social Icons', 'magee-page-builder' ),
		'pages'      => array( 'mpb_team', ),
		'fields'     => array(
			array(
				'id'          => $prefix . 'social_icons',
				'type'        => 'group',
				'description' => __( 'Generates reusable form icons', 'magee-page-builder' ),
				'options'     => array(
					'group_title'   => __( 'Icon {#}', 'magee-page-builder' ), // {#} gets replaced by row number
					'add_button'    => __( 'Add Another Icon', 'magee-page-builder' ),
					'remove_button' => __( 'Remove Icon', 'magee-page-builder' ),
					'sortable'      => true, // beta
				),
				// Fields array works the same, except id's only need to be unique for this group. Prefix is not needed.
				'fields'      => array(
					array(
						'name' => 'Icon',
						'id'   => 'icon',
						'type' => 'icon_picker',
						'default' => 'fa-facebook'
				
					),
					array(
						'name' => __( 'Link', 'magee-page-builder' ),
						'id'   => 'link',
						'type' => 'text',
					),
					array(
						'name' => 'Target',
						'id'   => 'target',
						'type'    => 'select',
						'options' => array(
							'_blank' => __( 'Blank', 'magee-page-builder' ),
							'_self'   => __( 'Self', 'magee-page-builder' ),
						),
					),
				),
			),
		),
	);
	
	
	$meta_boxes['client_metabox'] = array(
		'id'         => 'client_metabox',
		'title'      => __( 'Client Metabox', 'magee-page-builder' ),
		'pages'      => array( 'mpb_client', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'mpb_styles' => true, // Enqueue the MPB stylesheet on the frontend
		'fields'     => array(
			
			array(
				'name' => __( 'Logo', 'magee-page-builder' ),
				'desc' => __( 'Upload logo.', 'magee-page-builder' ),
				'id'   => $prefix . 'client_logo',
				'type' => 'file',
			),

		),
	);
	
	$meta_boxes['portfolio_metabox'] = array(
		'id'         => 'portfolio_metabox',
		'title'      => __( 'Portfolio Metabox', 'magee-page-builder' ),
		'pages'      => array( 'mpb_portfolio', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		// 'mpb_styles' => true, // Enqueue the MPB stylesheet on the frontend
		'fields'     => array(
			array(
				'name' => __( 'Featured Image', 'magee-page-builder' ),
				'desc' => __( 'Upload image.', 'magee-page-builder' ),
				'id'   => $prefix . 'featured_image',
				'type' => 'file',
			),
			array(
				'name' => __( 'Target', 'magee-page-builder' ),
				'desc' => '',
				'id'   => $prefix . 'portfolio_target',
				'type' => 'select',
				'options' => array(
							'_blank' => __( 'Blank', 'magee-page-builder' ),
							'_self'   => __( 'Self', 'magee-page-builder' ),
						),
			),

		),
	);
	
	
	$meta_boxes['slide_metabox'] = array(
		'id'         => 'slide_metabox',
		'title'      => __( 'Slide Metabox', 'magee-page-builder' ),
		'pages'      => array( 'mpb_slide', ), // Post type
		'context'    => 'normal',
		'priority'   => 'high',
		'show_names' => true, // Show field names on the left
		'fields'     => array(
			
			array(
				'name' => __( 'Slide Image', 'magee-page-builder' ),
				'desc' => __( 'Upload image.', 'magee-page-builder' ),
				'id'   => $prefix . 'slide_image',
				'type' => 'file',
			),
			array(
						'name' => __( 'Left Button Text', 'magee-page-builder' ),
						'id'   => $prefix . 'left_btn_text',
						'type' => 'text',
						'default' => 'ALL FEATURES'
					),
			array(
						'name' => __( 'Left Button Link', 'magee-page-builder' ),
						'id'   => $prefix . 'left_btn_link',
						'type' => 'text',
						'default' => '#'
					),
			array(
						'name' => __( 'Link Target', 'magee-page-builder' ),
						'id'   => $prefix . 'left_btn_target',
						'type' => 'select',
						'options' => array(
							'_blank' => __( 'Blank', 'magee-page-builder' ),
							'_self'   => __( 'Self', 'magee-page-builder' ),
						),
						'default' => '_self'
					),
			array(
						'name' => __( 'Right Button Text', 'magee-page-builder' ),
						'id'   => $prefix . 'right_btn_text',
						'type' => 'text',
						'default' => 'BUY NOW'
					),
			array(
						'name' => __( 'Right Button Link', 'magee-page-builder' ),
						'id'   => $prefix . 'right_btn_link',
						'type' => 'text',
						'default' => '#'
					),
			array(
						'name' => __( 'Link Target', 'magee-page-builder' ),
						'id'   => $prefix . 'right_btn_target',
						'type' => 'select',
						'options' => array(
							'_blank' => __( 'Blank', 'magee-page-builder' ),
							'_self'   => __( 'Self', 'magee-page-builder' ),
						),
						'default' => '_self'
					),

		),
	);

	// Add other metaboxes as needed

	return $meta_boxes;
}

add_action( 'init', 'mpb_initialize_mpb_meta_boxes', 9999 );
/**
 * Initialize the metabox class.
 */
function mpb_initialize_mpb_meta_boxes() {

	if ( ! class_exists( 'Mpb_Meta_Box' ) )
		require_once 'init.php';

}
