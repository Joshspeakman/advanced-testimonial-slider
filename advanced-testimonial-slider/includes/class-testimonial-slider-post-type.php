<?php
/**
 * Post Type Registration
 *
 * @package Advanced_Testimonial_Slider
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Custom post type class
 */
class Advanced_Testimonial_Slider_Post_Type {
    
    /**
     * Register custom post type
     */
    public function register_post_type() {
        $labels = array(
            'name'               => _x('Testimonial Slides', 'post type general name', 'advanced-testimonial-slider'),
            'singular_name'      => _x('Testimonial Slide', 'post type singular name', 'advanced-testimonial-slider'),
            'menu_name'          => _x('Testimonial Slider', 'admin menu', 'advanced-testimonial-slider'),
            'name_admin_bar'     => _x('Testimonial Slide', 'add new on admin bar', 'advanced-testimonial-slider'),
            'add_new'            => _x('Add New', 'testimonial slide', 'advanced-testimonial-slider'),
            'add_new_item'       => __('Add New Testimonial Slide', 'advanced-testimonial-slider'),
            'new_item'           => __('New Testimonial Slide', 'advanced-testimonial-slider'),
            'edit_item'          => __('Edit Testimonial Slide', 'advanced-testimonial-slider'),
            'view_item'          => __('View Testimonial Slide', 'advanced-testimonial-slider'),
            'all_items'          => __('All Testimonial Slides', 'advanced-testimonial-slider'),
            'search_items'       => __('Search Testimonial Slides', 'advanced-testimonial-slider'),
            'parent_item_colon'  => __('Parent Testimonial Slides:', 'advanced-testimonial-slider'),
            'not_found'          => __('No testimonial slides found.', 'advanced-testimonial-slider'),
            'not_found_in_trash' => __('No testimonial slides found in Trash.', 'advanced-testimonial-slider')
        );
        
        $args = array(
            'labels'             => $labels,
            'description'        => __('Testimonial slides for the slider', 'advanced-testimonial-slider'),
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => false,
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => 'dashicons-format-gallery',
            'supports'           => array('title', 'editor', 'page-attributes')
        );
        
        register_post_type('testimonial_slide', $args);
    }
}