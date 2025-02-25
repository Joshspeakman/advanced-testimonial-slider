<?php
/**
 * Shortcode functionality
 *
 * @package Advanced_Testimonial_Slider
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shortcode class
 */
class Advanced_Testimonial_Slider_Shortcode {
    
    /**
     * Initialize the shortcode functionality
     */
    public function init() {
        // Enqueue front-end scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Add shortcode
        add_shortcode('advanced_testimonial_slider', array($this, 'render_testimonial_slider'));
    }
    
    /**
     * Enqueue front-end scripts and styles
     */
    public function enqueue_scripts() {
        // Enqueue jQuery
        wp_enqueue_script('jquery');
        
        // Enqueue our styles
        wp_enqueue_style(
            'ats-styles',
            ATS_ASSETS_URL . 'css/testimonial-slider.css',
            array(),
            ATS_VERSION
        );
        
        // Enqueue our scripts
        wp_enqueue_script(
            'ats-scripts',
            ATS_ASSETS_URL . 'js/testimonial-slider.js',
            array('jquery'),
            ATS_VERSION,
            true
        );
        
        // Get plugin options
        $options = get_option('ats_options');
        
        // Localize script
        wp_localize_script(
            'ats-scripts',
            'atsSettings',
            array(
                'enableSwipe' => isset($options['enable_swipe']) ? $options['enable_swipe'] : 'yes',
                'autoplay' => isset($options['autoplay']) ? $options['autoplay'] : 'no',
                'autoplaySpeed' => isset($options['autoplay_speed']) ? intval($options['autoplay_speed']) : 5000
            )
        );
        
        // Add inline CSS with custom styles
        $custom_css = $this->generate_custom_css($options);
        wp_add_inline_style('ats-styles', $custom_css);
    }
    
    /**
     * Generate custom CSS based on options
     */
    private function generate_custom_css($options) {
        $css = '';
        
        // Title font size
        if (!empty($options['title_font_size'])) {
            $css .= '.testimonial-name { font-size: ' . esc_attr($options['title_font_size']) . ' !important; }';
        }
        
        // Description font size
        if (!empty($options['description_font_size'])) {
            $css .= '.testimonial-description { font-size: ' . esc_attr($options['description_font_size']) . ' !important; }';
        }
        
        // Primary color
        if (!empty($options['primary_color'])) {
            $css .= '.testimonial-name { color: ' . esc_attr($options['primary_color']) . ' !important; }';
            $css .= '.cta-button { background: linear-gradient(to right, ' . esc_attr($options['primary_color']) . ', ' . esc_attr($options['secondary_color']) . ') !important; }';
            $css .= '.pagination-dot.active { background: ' . esc_attr($options['primary_color']) . ' !important; }';
        }
        
        // Button text color
        if (!empty($options['button_text_color'])) {
            $css .= '.cta-button { color: ' . esc_attr($options['button_text_color']) . ' !important; }';
        }
        
        // Button hover effect
        if (!empty($options['button_hover_effect'])) {
            switch ($options['button_hover_effect']) {
                case 'scale-up':
                    $css .= '.cta-button:hover { transform: translate(0, -3px) !important; }';
                    break;
                    
                case 'opacity':
                    $css .= '.cta-button:hover { opacity: 0.8 !important; transform: none !important; }';
                    break;
                    
                case 'background-shift':
                    $css .= '.cta-button:hover { background: linear-gradient(to left, ' . esc_attr($options['primary_color']) . ', ' . esc_attr($options['secondary_color']) . ') !important; transform: none !important; }';
                    break;
                    
                case 'none':
                    $css .= '.cta-button:hover { transform: none !important; opacity: 1 !important; }';
                    break;
            }
        }
        
        // Add media queries if needed
        $css .= '@media (max-width: 768px) {';
        $css .= '  .testimonial-name { font-size: calc(' . esc_attr($options['title_font_size']) . ' * 0.8) !important; }';
        $css .= '  .testimonial-description { font-size: calc(' . esc_attr($options['description_font_size']) . ' * 0.9) !important; }';
        $css .= '}';
        
        return $css;
    }
    
    /**
     * Render testimonial slider
     */
    public function render_testimonial_slider($atts) {
        // Parse shortcode attributes
        $atts = shortcode_atts(
            array(
                'limit' => -1,
                'orderby' => 'menu_order',
                'order' => 'ASC',
            ),
            $atts,
            'advanced_testimonial_slider'
        );
        
        // Get slides
        $slides = get_posts(array(
            'post_type' => 'testimonial_slide',
            'posts_per_page' => $atts['limit'],
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
        ));
        
        if (empty($slides)) {
            return '<p>' . __('No testimonial slides found.', 'advanced-testimonial-slider') . '</p>';
        }
        
        // Get options
        $options = get_option('ats_options');
        $button_text = isset($options['button_text']) ? $options['button_text'] : 'LEARN MORE';
        $button_url = isset($options['button_url']) ? $options['button_url'] : '/contact-us/';
        
        // Start output buffer
        ob_start();
        
        // Video Popup Overlay
        ?>
        <!-- Video Popup Overlay -->
        <div class="video-popup-overlay">
            <div class="video-popup-container">
                <button class="close-video" aria-label="Close video">&times;</button>
                <iframe src="" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>

        <!-- Main Content -->
        <div class="testimonials-outer-wrapper">
            <div class="carousel-container">
                <div class="carousel-wrapper">
                    <div class="carousel-track">
                        <?php foreach ($slides as $index => $slide) : 
                            $slide_id = $slide->ID;
                            $image_url = get_post_meta($slide_id, '_slide_image_url', true);
                            $image_position = get_post_meta($slide_id, '_slide_image_position', true);
                            if (empty($image_position)) {
                                $image_position = 'center'; // Default position
                            }
                            $questions = get_post_meta($slide_id, '_slide_questions', true);
                            
                            if (!is_array($questions)) {
                                $questions = array();
                            }
                        ?>
                            <!-- Testimonial Slide -->
                            <div class="carousel-slide slide-<?php echo esc_attr($index + 1); ?>" data-slide-index="<?php echo esc_attr($index); ?>">
                                <div class="testimonial-section">
                                    <div class="testimonial-image-column" style="<?php echo $image_url ? 'background-image: url(' . esc_url($image_url) . '); background-position: ' . esc_attr($image_position) . ' center;' : ''; ?>"></div>
                                    <div class="testimonial-content-column">
                                        <h3 class="testimonial-name"><?php echo esc_html($slide->post_title); ?></h3>
                                        <div class="testimonial-description">
                                            <?php echo wp_kses_post($slide->post_content); ?>
                                        </div>
                                        
                                        <div class="video-questions" id="slide-<?php echo esc_attr($index + 1); ?>-questions">
                                            <?php foreach ($questions as $question) : 
                                                if (empty($question['question']) || empty($question['video_url'])) {
                                                    continue;
                                                }
                                            ?>
                                                <a href="<?php echo esc_url($question['video_url']); ?>" class="video-link" data-video-url="<?php echo esc_url($question['video_url']); ?>" title="<?php echo esc_attr($question['question']); ?>" aria-label="<?php echo esc_attr($question['question']); ?>">
                                                    <span class="video-link-question"><?php echo esc_html($question['question']); ?></span>
                                                    <span class="watch-video-text"><?php _e('WATCH VIDEO', 'advanced-testimonial-slider'); ?></span>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>

                                        <a href="<?php echo esc_url($button_url); ?>" class="cta-button"><?php echo esc_html($button_text); ?></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Carousel Navigation -->
                    <div class="carousel-nav">
                        <button class="carousel-arrow carousel-arrow-prev" aria-label="Previous slide">
                            <svg viewBox="0 0 24 24">
                                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
                            </svg>
                        </button>
                        
                        <button class="carousel-arrow carousel-arrow-next" aria-label="Next slide">
                            <svg viewBox="0 0 24 24">
                                <path d="M8.59 16.59L10 18l6-6-6-6-1.41 1.41L13.17 12z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="carousel-pagination">
                    <?php foreach ($slides as $index => $slide) : ?>
                        <button class="pagination-dot<?php echo $index === 0 ? ' active' : ''; ?>" aria-label="Go to slide <?php echo esc_attr($index + 1); ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
        
        // Return the output
        return ob_get_clean();
    }
}