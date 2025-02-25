<?php
/**
 * Plugin Name: Advanced Testimonial Slider
 * Plugin URI: https://wordpress.org/
 * Description: A responsive testimonial carousel with video popup functionality, mobile swipe, and customization options.
 * Version: 1.0.0
 * Author: Website Admin
 * Author URI: https://wordpress.org/
 * Text Domain: advanced-testimonial-slider
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin class
 */
class Advanced_Testimonial_Slider {
    
    /**
     * Plugin version
     */
    const VERSION = '1.0.0';
    
    /**
     * Constructor
     */
    public function __construct() {
        // Define constants
        $this->define_constants();
        
        // Register activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Load required files
        $this->load_dependencies();
        
        // Initialize the plugin
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    /**
     * Define constants
     */
    private function define_constants() {
        define('ATS_VERSION', self::VERSION);
        define('ATS_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('ATS_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('ATS_PLUGIN_BASENAME', plugin_basename(__FILE__));
        define('ATS_ASSETS_URL', ATS_PLUGIN_URL . 'assets/');
        define('ATS_INCLUDES_DIR', ATS_PLUGIN_DIR . 'includes/');
    }
    
    /**
     * Load dependencies
     */
    private function load_dependencies() {
        // Include post type class first
        require_once ATS_INCLUDES_DIR . 'class-testimonial-slider-post-type.php';
        
        // Include admin class
        require_once ATS_INCLUDES_DIR . 'class-testimonial-slider-admin.php';
        
        // Include shortcode class
        require_once ATS_INCLUDES_DIR . 'class-testimonial-slider-shortcode.php';
    }
    
    /**
     * Activation hook
     */
    public function activate() {
        // Register custom post type (post type class is already loaded)
        $post_type = new Advanced_Testimonial_Slider_Post_Type();
        $post_type->register_post_type();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set default options
        $this->set_default_options();
    }
    
    /**
     * Deactivation hook
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Set default plugin options
     */
    private function set_default_options() {
        $options = array(
            'title_font_size' => '2.5rem',
            'description_font_size' => '1rem',
            'primary_color' => '#3498db',
            'secondary_color' => '#2c3e50',
            'button_text_color' => '#ffffff',
            'button_hover_effect' => 'scale-up',
            'button_text' => 'LEARN MORE',
            'button_url' => '/contact-us/',
            'enable_swipe' => 'yes',
            'autoplay' => 'no',
            'autoplay_speed' => 5000,
        );
        
        add_option('ats_options', $options);
        
        // Import example slides if no posts exist
        if (!get_posts(array('post_type' => 'testimonial_slide', 'numberposts' => 1))) {
            $this->import_default_slides();
        }
    }
    
    /**
     * Import default slides
     */
    private function import_default_slides() {
        // First slide
        $slide1 = array(
            'post_title' => 'John Smith',
            'post_content' => 'One of our top clients, John Smith shares his experience with our company and how it has impacted him personally, professionally, and financially.',
            'post_status' => 'publish',
            'post_type' => 'testimonial_slide',
            'menu_order' => 1
        );
        
        $slide1_id = wp_insert_post($slide1);
        
        if (!is_wp_error($slide1_id)) {
            update_post_meta($slide1_id, '_slide_image_url', '');
            
            $questions = array(
                array(
                    'question' => 'What did you do before using our services?',
                    'video_url' => 'https://youtu.be/example1'
                ),
                array(
                    'question' => 'What was life like before discovering our company?',
                    'video_url' => 'https://youtu.be/example2'
                ),
                array(
                    'question' => 'How did our services change your life?',
                    'video_url' => 'https://youtu.be/example3'
                ),
                array(
                    'question' => 'What does our company mean to you?',
                    'video_url' => 'https://youtu.be/example4'
                )
            );
            
            update_post_meta($slide1_id, '_slide_questions', $questions);
        }
        
        // Second slide
        $slide2 = array(
            'post_title' => 'Client Compilation',
            'post_content' => 'Hear directly from our satisfied clients about their experiences, the impact our services have made in their lives, and how they have achieved success while working with us.',
            'post_status' => 'publish',
            'post_type' => 'testimonial_slide',
            'menu_order' => 2
        );
        
        $slide2_id = wp_insert_post($slide2);
        
        if (!is_wp_error($slide2_id)) {
            update_post_meta($slide2_id, '_slide_image_url', '');
            
            $questions = array(
                array(
                    'question' => 'What is the biggest benefit of working with us?',
                    'video_url' => 'https://youtu.be/example5'
                ),
                array(
                    'question' => 'What impact have our services had on your life?',
                    'video_url' => 'https://youtu.be/example6'
                ),
                array(
                    'question' => 'What is the main reason you chose us?',
                    'video_url' => 'https://youtu.be/example7'
                ),
                array(
                    'question' => 'How have our services impacted your career?',
                    'video_url' => 'https://youtu.be/example8'
                )
            );
            
            update_post_meta($slide2_id, '_slide_questions', $questions);
        }
        
        // Third slide
        $slide3 = array(
            'post_title' => 'Jane Johnson',
            'post_content' => 'Hear directly from our successful client about her experience with our company, the impact we have made in her community, and how she has achieved work-life balance while building a thriving business.',
            'post_status' => 'publish',
            'post_type' => 'testimonial_slide',
            'menu_order' => 3
        );
        
        $slide3_id = wp_insert_post($slide3);
        
        if (!is_wp_error($slide3_id)) {
            update_post_meta($slide3_id, '_slide_image_url', '');
            
            $questions = array(
                array(
                    'question' => 'What is it like working with our company?',
                    'video_url' => 'https://youtu.be/example9'
                ),
                array(
                    'question' => 'How are our services different from others?',
                    'video_url' => 'https://youtu.be/example10'
                ),
                array(
                    'question' => 'Why did you choose our company?',
                    'video_url' => 'https://youtu.be/example11'
                ),
                array(
                    'question' => 'How do our services help you find meaning in your work?',
                    'video_url' => 'https://youtu.be/example12'
                )
            );
            
            update_post_meta($slide3_id, '_slide_questions', $questions);
        }
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        // Initialize admin
        $admin = new Advanced_Testimonial_Slider_Admin();
        $admin->init();
        
        // Initialize shortcode
        $shortcode = new Advanced_Testimonial_Slider_Shortcode();
        $shortcode->init();
    }
}

// Initialize the plugin
new Advanced_Testimonial_Slider();