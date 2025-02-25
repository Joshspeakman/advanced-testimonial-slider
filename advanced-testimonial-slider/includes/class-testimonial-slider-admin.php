<?php
/**
 * Admin functionalities
 *
 * @package Advanced_Testimonial_Slider
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin class
 */
class Advanced_Testimonial_Slider_Admin {
    
    /**
     * Initialize the admin functionalities
     */
    public function init() {
        // Register custom post type
        add_action('init', array($this, 'register_post_type'));
        
        // Register custom meta boxes
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
        
        // Save post meta
        add_action('save_post_testimonial_slide', array($this, 'save_slide_meta'), 10, 2);
        
        // Add settings page
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Register plugin settings
        add_action('admin_init', array($this, 'register_settings'));
        
        // Enqueue admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    /**
     * Register custom post type
     */
    public function register_post_type() {
        // Post type class should already be loaded by now
        // If not, load it again as a fallback
        if (!class_exists('Advanced_Testimonial_Slider_Post_Type')) {
            require_once ATS_INCLUDES_DIR . 'class-testimonial-slider-post-type.php';
        }
        
        $post_type = new Advanced_Testimonial_Slider_Post_Type();
        $post_type->register_post_type();
    }
    
    /**
     * Register meta boxes
     */
    public function register_meta_boxes() {
        add_meta_box(
            'testimonial_slide_image',
            __('Slide Image', 'advanced-testimonial-slider'),
            array($this, 'render_image_meta_box'),
            'testimonial_slide',
            'side',
            'default'
        );
        
        add_meta_box(
            'testimonial_slide_questions',
            __('Video Questions', 'advanced-testimonial-slider'),
            array($this, 'render_questions_meta_box'),
            'testimonial_slide',
            'normal',
            'high'
        );
    }
    
    /**
     * Render image meta box
     */
    public function render_image_meta_box($post) {
        wp_nonce_field('save_slide_meta', 'slide_meta_nonce');
        
        $image_url = get_post_meta($post->ID, '_slide_image_url', true);
        $image_id = get_post_meta($post->ID, '_slide_image_id', true);
        $image_position = get_post_meta($post->ID, '_slide_image_position', true);
        
        if (empty($image_position)) {
            $image_position = 'center'; // Default position
        }
        
        ?>
        <div class="slide-image-wrapper">
            <div class="slide-image-preview">
                <?php if ($image_url) : ?>
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($post->post_title); ?>" style="max-width: 100%; height: auto;">
                <?php else : ?>
                    <p><?php _e('No image selected', 'advanced-testimonial-slider'); ?></p>
                <?php endif; ?>
            </div>
            
            <input type="hidden" name="slide_image_url" id="slide-image-url" value="<?php echo esc_attr($image_url); ?>">
            <input type="hidden" name="slide_image_id" id="slide-image-id" value="<?php echo esc_attr($image_id); ?>">
            
            <p>
                <button type="button" class="button slide-image-upload">
                    <?php echo $image_url ? __('Change Image', 'advanced-testimonial-slider') : __('Select Image', 'advanced-testimonial-slider'); ?>
                </button>
                
                <?php if ($image_url) : ?>
                    <button type="button" class="button slide-image-remove"><?php _e('Remove Image', 'advanced-testimonial-slider'); ?></button>
                <?php endif; ?>
            </p>
            
            <p>
                <label for="slide_image_position"><?php _e('Image Position:', 'advanced-testimonial-slider'); ?></label>
                <select name="slide_image_position" id="slide_image_position">
                    <option value="left" <?php selected($image_position, 'left'); ?>><?php _e('Left', 'advanced-testimonial-slider'); ?></option>
                    <option value="center" <?php selected($image_position, 'center'); ?>><?php _e('Center', 'advanced-testimonial-slider'); ?></option>
                    <option value="right" <?php selected($image_position, 'right'); ?>><?php _e('Right', 'advanced-testimonial-slider'); ?></option>
                </select>
            </p>
            
            <p class="description"><?php _e('Select an image for this testimonial slide. Recommended size: 600x520px', 'advanced-testimonial-slider'); ?></p>
        </div>
        <?php
    }
    
    /**
     * Render questions meta box
     */
    public function render_questions_meta_box($post) {
        $questions = get_post_meta($post->ID, '_slide_questions', true);
        
        if (!is_array($questions)) {
            $questions = array(
                array(
                    'question' => '',
                    'video_url' => ''
                )
            );
        }
        
        ?>
        <div class="slide-questions-wrapper">
            <div class="slide-questions">
                <?php foreach ($questions as $index => $question) : ?>
                    <div class="slide-question" data-index="<?php echo esc_attr($index); ?>">
                        <div class="question-header">
                            <span class="question-number"><?php echo esc_html($index + 1); ?></span>
                            <button type="button" class="button button-small remove-question"><?php _e('Remove', 'advanced-testimonial-slider'); ?></button>
                        </div>
                        
                        <div class="question-body">
                            <div class="question-row">
                                <label for="question-text-<?php echo esc_attr($index); ?>"><?php _e('Question:', 'advanced-testimonial-slider'); ?></label>
                                <input type="text" id="question-text-<?php echo esc_attr($index); ?>" name="slide_questions[<?php echo esc_attr($index); ?>][question]" value="<?php echo esc_attr($question['question']); ?>" class="widefat">
                            </div>
                            
                            <div class="question-row">
                                <label for="question-video-<?php echo esc_attr($index); ?>"><?php _e('Video URL:', 'advanced-testimonial-slider'); ?></label>
                                <input type="url" id="question-video-<?php echo esc_attr($index); ?>" name="slide_questions[<?php echo esc_attr($index); ?>][video_url]" value="<?php echo esc_attr($question['video_url']); ?>" class="widefat">
                                <p class="description"><?php _e('Enter YouTube video URL (e.g., https://youtu.be/XXXX or https://www.youtube.com/watch?v=XXXX)', 'advanced-testimonial-slider'); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="slide-questions-actions">
                <button type="button" class="button add-question"><?php _e('Add Question', 'advanced-testimonial-slider'); ?></button>
            </div>
            
            <script type="text/html" id="question-template">
                <div class="slide-question" data-index="{{index}}">
                    <div class="question-header">
                        <span class="question-number">{{number}}</span>
                        <button type="button" class="button button-small remove-question"><?php _e('Remove', 'advanced-testimonial-slider'); ?></button>
                    </div>
                    
                    <div class="question-body">
                        <div class="question-row">
                            <label for="question-text-{{index}}"><?php _e('Question:', 'advanced-testimonial-slider'); ?></label>
                            <input type="text" id="question-text-{{index}}" name="slide_questions[{{index}}][question]" class="widefat">
                        </div>
                        
                        <div class="question-row">
                            <label for="question-video-{{index}}"><?php _e('Video URL:', 'advanced-testimonial-slider'); ?></label>
                            <input type="url" id="question-video-{{index}}" name="slide_questions[{{index}}][video_url]" class="widefat">
                            <p class="description"><?php _e('Enter YouTube video URL (e.g., https://youtu.be/XXXX or https://www.youtube.com/watch?v=XXXX)', 'advanced-testimonial-slider'); ?></p>
                        </div>
                    </div>
                </div>
            </script>
        </div>
        <?php
    }
    
    /**
     * Save slide meta data
     */
    public function save_slide_meta($post_id, $post) {
        // Check if our nonce is set
        if (!isset($_POST['slide_meta_nonce'])) {
            return;
        }
        
        // Verify that the nonce is valid
        if (!wp_verify_nonce($_POST['slide_meta_nonce'], 'save_slide_meta')) {
            return;
        }
        
        // If this is an autosave, our form has not been submitted, so we don't want to do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check the user's permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        // Save slide image
        if (isset($_POST['slide_image_url'])) {
            update_post_meta($post_id, '_slide_image_url', sanitize_url($_POST['slide_image_url']));
        }
        
        if (isset($_POST['slide_image_id'])) {
            update_post_meta($post_id, '_slide_image_id', absint($_POST['slide_image_id']));
        }
        
        // Save slide image position
        if (isset($_POST['slide_image_position'])) {
            $position = sanitize_text_field($_POST['slide_image_position']);
            // Only allow these specific values
            if (in_array($position, array('left', 'center', 'right'))) {
                update_post_meta($post_id, '_slide_image_position', $position);
            }
        }
        
        // Save questions
        if (isset($_POST['slide_questions']) && is_array($_POST['slide_questions'])) {
            $questions = array();
            
            foreach ($_POST['slide_questions'] as $question) {
                if (!empty($question['question']) || !empty($question['video_url'])) {
                    $questions[] = array(
                        'question' => sanitize_text_field($question['question']),
                        'video_url' => sanitize_url($question['video_url'])
                    );
                }
            }
            
            update_post_meta($post_id, '_slide_questions', $questions);
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=testimonial_slide',
            __('Settings', 'advanced-testimonial-slider'),
            __('Settings', 'advanced-testimonial-slider'),
            'manage_options',
            'testimonial_slider_settings',
            array($this, 'render_settings_page')
        );
    }
    
    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting(
            'ats_settings',
            'ats_options',
            array($this, 'sanitize_settings')
        );
        
        // General settings section
        add_settings_section(
            'ats_general_section',
            __('General Settings', 'advanced-testimonial-slider'),
            array($this, 'render_general_section'),
            'testimonial_slider_settings'
        );
        
        add_settings_field(
            'button_text',
            __('Button Text', 'advanced-testimonial-slider'),
            array($this, 'render_button_text_field'),
            'testimonial_slider_settings',
            'ats_general_section'
        );
        
        add_settings_field(
            'button_url',
            __('Button URL', 'advanced-testimonial-slider'),
            array($this, 'render_button_url_field'),
            'testimonial_slider_settings',
            'ats_general_section'
        );
        
        add_settings_field(
            'enable_swipe',
            __('Enable Swipe on Mobile', 'advanced-testimonial-slider'),
            array($this, 'render_enable_swipe_field'),
            'testimonial_slider_settings',
            'ats_general_section'
        );
        
        add_settings_field(
            'autoplay',
            __('Autoplay Slider', 'advanced-testimonial-slider'),
            array($this, 'render_autoplay_field'),
            'testimonial_slider_settings',
            'ats_general_section'
        );
        
        add_settings_field(
            'autoplay_speed',
            __('Autoplay Speed (ms)', 'advanced-testimonial-slider'),
            array($this, 'render_autoplay_speed_field'),
            'testimonial_slider_settings',
            'ats_general_section'
        );
        
        // Style settings section
        add_settings_section(
            'ats_style_section',
            __('Style Settings', 'advanced-testimonial-slider'),
            array($this, 'render_style_section'),
            'testimonial_slider_settings'
        );
        
        add_settings_field(
            'title_font_size',
            __('Title Font Size', 'advanced-testimonial-slider'),
            array($this, 'render_title_font_size_field'),
            'testimonial_slider_settings',
            'ats_style_section'
        );
        
        add_settings_field(
            'description_font_size',
            __('Description Font Size', 'advanced-testimonial-slider'),
            array($this, 'render_description_font_size_field'),
            'testimonial_slider_settings',
            'ats_style_section'
        );
        
        add_settings_field(
            'primary_color',
            __('Primary Color', 'advanced-testimonial-slider'),
            array($this, 'render_primary_color_field'),
            'testimonial_slider_settings',
            'ats_style_section'
        );
        
        add_settings_field(
            'secondary_color',
            __('Secondary Color', 'advanced-testimonial-slider'),
            array($this, 'render_secondary_color_field'),
            'testimonial_slider_settings',
            'ats_style_section'
        );
        
        add_settings_field(
            'button_text_color',
            __('Button Text Color', 'advanced-testimonial-slider'),
            array($this, 'render_button_text_color_field'),
            'testimonial_slider_settings',
            'ats_style_section'
        );
        
        add_settings_field(
            'button_hover_effect',
            __('Button Hover Effect', 'advanced-testimonial-slider'),
            array($this, 'render_button_hover_effect_field'),
            'testimonial_slider_settings',
            'ats_style_section'
        );
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $output = array();
        
        // General settings
        $output['button_text'] = isset($input['button_text']) ? sanitize_text_field($input['button_text']) : '';
        $output['button_url'] = isset($input['button_url']) ? sanitize_text_field($input['button_url']) : '';
        $output['enable_swipe'] = isset($input['enable_swipe']) ? 'yes' : 'no';
        $output['autoplay'] = isset($input['autoplay']) ? 'yes' : 'no';
        $output['autoplay_speed'] = isset($input['autoplay_speed']) ? absint($input['autoplay_speed']) : 5000;
        
        // Style settings
        $output['title_font_size'] = isset($input['title_font_size']) ? sanitize_text_field($input['title_font_size']) : '2.5rem';
        $output['description_font_size'] = isset($input['description_font_size']) ? sanitize_text_field($input['description_font_size']) : '1rem';
        $output['primary_color'] = isset($input['primary_color']) ? sanitize_hex_color($input['primary_color']) : '#3498db';
        $output['secondary_color'] = isset($input['secondary_color']) ? sanitize_hex_color($input['secondary_color']) : '#2c3e50';
        $output['button_text_color'] = isset($input['button_text_color']) ? sanitize_hex_color($input['button_text_color']) : '#ffffff';
        $output['button_hover_effect'] = isset($input['button_hover_effect']) ? sanitize_text_field($input['button_hover_effect']) : 'scale-up';
        
        return $output;
    }
    
    /**
     * Render general section
     */
    public function render_general_section() {
        echo '<p>' . __('Configure general settings for the testimonial slider.', 'advanced-testimonial-slider') . '</p>';
    }
    
    /**
     * Render style section
     */
    public function render_style_section() {
        echo '<p>' . __('Customize the appearance of the testimonial slider.', 'advanced-testimonial-slider') . '</p>';
    }
    
    /**
     * Render button text field
     */
    public function render_button_text_field() {
        $options = get_option('ats_options');
        $value = isset($options['button_text']) ? $options['button_text'] : '';
        
        echo '<input type="text" id="button_text" name="ats_options[button_text]" value="' . esc_attr($value) . '" class="regular-text">';
    }
    
    /**
     * Render button URL field
     */
    public function render_button_url_field() {
        $options = get_option('ats_options');
        $value = isset($options['button_url']) ? $options['button_url'] : '';
        
        echo '<input type="text" id="button_url" name="ats_options[button_url]" value="' . esc_attr($value) . '" class="regular-text">';
    }
    
    /**
     * Render enable swipe field
     */
    public function render_enable_swipe_field() {
        $options = get_option('ats_options');
        $checked = isset($options['enable_swipe']) && $options['enable_swipe'] === 'yes' ? 'checked' : '';
        
        echo '<label><input type="checkbox" id="enable_swipe" name="ats_options[enable_swipe]" value="yes" ' . $checked . '> ' . __('Enable touch swipe on mobile devices', 'advanced-testimonial-slider') . '</label>';
    }
    
    /**
     * Render autoplay field
     */
    public function render_autoplay_field() {
        $options = get_option('ats_options');
        $checked = isset($options['autoplay']) && $options['autoplay'] === 'yes' ? 'checked' : '';
        
        echo '<label><input type="checkbox" id="autoplay" name="ats_options[autoplay]" value="yes" ' . $checked . '> ' . __('Automatically cycle through slides', 'advanced-testimonial-slider') . '</label>';
    }
    
    /**
     * Render autoplay speed field
     */
    public function render_autoplay_speed_field() {
        $options = get_option('ats_options');
        $value = isset($options['autoplay_speed']) ? $options['autoplay_speed'] : 5000;
        
        echo '<input type="number" id="autoplay_speed" name="ats_options[autoplay_speed]" value="' . esc_attr($value) . '" min="1000" step="500" class="small-text"> ' . __('milliseconds', 'advanced-testimonial-slider');
    }
    
    /**
     * Render title font size field
     */
    public function render_title_font_size_field() {
        $options = get_option('ats_options');
        $value = isset($options['title_font_size']) ? $options['title_font_size'] : '2.5rem';
        
        echo '<input type="text" id="title_font_size" name="ats_options[title_font_size]" value="' . esc_attr($value) . '" class="small-text"> ' . __('(e.g., 2.5rem, 24px)', 'advanced-testimonial-slider');
    }
    
    /**
     * Render description font size field
     */
    public function render_description_font_size_field() {
        $options = get_option('ats_options');
        $value = isset($options['description_font_size']) ? $options['description_font_size'] : '1rem';
        
        echo '<input type="text" id="description_font_size" name="ats_options[description_font_size]" value="' . esc_attr($value) . '" class="small-text"> ' . __('(e.g., 1rem, 16px)', 'advanced-testimonial-slider');
    }
    
    /**
     * Render primary color field
     */
    public function render_primary_color_field() {
        $options = get_option('ats_options');
        $value = isset($options['primary_color']) ? $options['primary_color'] : '#3498db';
        
        echo '<input type="text" id="primary_color" name="ats_options[primary_color]" value="' . esc_attr($value) . '" class="ats-color-picker">';
    }
    
    /**
     * Render secondary color field
     */
    public function render_secondary_color_field() {
        $options = get_option('ats_options');
        $value = isset($options['secondary_color']) ? $options['secondary_color'] : '#2c3e50';
        
        echo '<input type="text" id="secondary_color" name="ats_options[secondary_color]" value="' . esc_attr($value) . '" class="ats-color-picker">';
    }
    
    /**
     * Render button text color field
     */
    public function render_button_text_color_field() {
        $options = get_option('ats_options');
        $value = isset($options['button_text_color']) ? $options['button_text_color'] : '#ffffff';
        
        echo '<input type="text" id="button_text_color" name="ats_options[button_text_color]" value="' . esc_attr($value) . '" class="ats-color-picker">';
    }
    
    /**
     * Render button hover effect field
     */
    public function render_button_hover_effect_field() {
        $options = get_option('ats_options');
        $selected = isset($options['button_hover_effect']) ? $options['button_hover_effect'] : 'scale-up';
        
        $effects = array(
            'scale-up' => __('Scale Up', 'advanced-testimonial-slider'),
            'opacity' => __('Opacity Change', 'advanced-testimonial-slider'),
            'background-shift' => __('Background Shift', 'advanced-testimonial-slider'),
            'none' => __('None', 'advanced-testimonial-slider')
        );
        
        echo '<select id="button_hover_effect" name="ats_options[button_hover_effect]">';
        foreach ($effects as $value => $label) {
            echo '<option value="' . esc_attr($value) . '" ' . selected($selected, $value, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="ats-settings-wrapper">
                <div class="ats-settings-content">
                    <form action="options.php" method="post">
                        <?php
                        settings_fields('ats_settings');
                        do_settings_sections('testimonial_slider_settings');
                        submit_button();
                        ?>
                    </form>
                </div>
                
                <div class="ats-settings-sidebar">
                    <div class="ats-settings-box">
                        <h3><?php _e('Shortcode', 'advanced-testimonial-slider'); ?></h3>
                        <p><?php _e('Use this shortcode to display the testimonial slider:', 'advanced-testimonial-slider'); ?></p>
                        <div class="ats-shortcode-display">
                            <code>[advanced_testimonial_slider]</code>
                            <button type="button" class="button button-small ats-copy-shortcode">
                                <?php _e('Copy', 'advanced-testimonial-slider'); ?>
                            </button>
                        </div>
                    </div>
                    
                    <div class="ats-settings-box">
                        <h3><?php _e('Need Help?', 'advanced-testimonial-slider'); ?></h3>
                        <p><?php _e('For support or suggestions, please contact the plugin developer.', 'advanced-testimonial-slider'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_scripts($hook) {
        // Only load on our settings page or testimonial slide edit page
        if ($hook !== 'testimonial_slide_page_testimonial_slider_settings' && 
            $hook !== 'post.php' && $hook !== 'post-new.php') {
            return;
        }
        
        // Check if we're on a testimonial slide edit page
        global $post;
        if (($hook === 'post.php' || $hook === 'post-new.php') && 
            (empty($post) || $post->post_type !== 'testimonial_slide')) {
            return;
        }
        
        // Enqueue WordPress media scripts
        wp_enqueue_media();
        
        // Enqueue WordPress color picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        // Enqueue our admin styles
        wp_enqueue_style(
            'ats-admin-styles',
            ATS_ASSETS_URL . 'css/admin.css',
            array(),
            ATS_VERSION
        );
        
        // Enqueue our admin scripts
        wp_enqueue_script(
            'ats-admin-scripts',
            ATS_ASSETS_URL . 'js/admin.js',
            array('jquery', 'wp-color-picker'),
            ATS_VERSION,
            true
        );
        
        // Localize admin script
        wp_localize_script(
            'ats-admin-scripts',
            'atsAdmin',
            array(
                'mediaTitle' => __('Select or Upload Testimonial Image', 'advanced-testimonial-slider'),
                'mediaButton' => __('Use this image', 'advanced-testimonial-slider'),
                'copied' => __('Copied!', 'advanced-testimonial-slider')
            )
        );
    }
}