/**
 * Advanced Testimonial Slider - Front-end Scripts
 */
(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        initTestimonialSlider();
    });
    
    /**
     * Initialize testimonial slider functionality
     */
    function initTestimonialSlider() {
        // Elements
        const track = $('.carousel-track');
        const slides = $('.carousel-slide');
        const prevButton = $('.carousel-arrow-prev');
        const nextButton = $('.carousel-arrow-next');
        const dots = $('.pagination-dot');
        const videoOverlay = $('.video-popup-overlay');
        const videoIframe = videoOverlay.find('iframe');
        const closeVideo = $('.close-video');
        
        // Variables
        let currentSlide = 0;
        const slideCount = slides.length;
        let autoplayTimer = null;
        
        // Touch handling variables
        let touchStartX = 0;
        let touchStartY = 0;
        let touchEndX = 0;
        let touchEndY = 0;
        const swipeThreshold = 50;
        let isHorizontalSwipe = false;
        
        /**
         * Update slide position
         */
        function updateSlidePosition() {
            let offset = 0;
            slides.each(function(index) {
                if (index < currentSlide) {
                    offset += $(this).outerWidth(true);
                }
            });
            track.css('transform', `translateX(-${offset}px)`);
            slides.removeClass('active');
            slides.eq(currentSlide).addClass('active');
            dots.removeClass('active');
            dots.eq(currentSlide).addClass('active');
        }
        
        /**
         * Open video popup
         */
        function openVideoPopup(videoUrl) {
            // Extract video ID from URL if it's a YouTube URL
            let videoId = videoUrl.split('youtu.be/')[1];
            if (!videoId) {
                videoId = videoUrl.split('v=')[1];
                if (videoId) {
                    // Remove additional parameters
                    const ampIndex = videoId.indexOf('&');
                    if (ampIndex !== -1) {
                        videoId = videoId.substring(0, ampIndex);
                    }
                }
            }
            
            if (videoId) {
                const embedUrl = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
                videoIframe.attr('src', embedUrl);
            } else {
                // If it's not a YouTube URL, use the URL directly
                videoIframe.attr('src', videoUrl);
            }
            
            videoOverlay.css('display', 'flex').hide().fadeIn(300);
            $('body').css('overflow', 'hidden');
        }
        
        /**
         * Close video popup
         */
        function closeVideoPopup() {
            videoOverlay.fadeOut(300);
            setTimeout(() => {
                videoIframe.attr('src', '');
            }, 300);
            $('body').css('overflow', '');
        }
        
        /**
         * Start autoplay timer
         */
        function startAutoplay() {
            if (atsSettings.autoplay !== 'yes') {
                return;
            }
            
            stopAutoplay();
            
            autoplayTimer = setTimeout(() => {
                currentSlide = (currentSlide + 1) % slideCount;
                updateSlidePosition();
                startAutoplay();
            }, parseInt(atsSettings.autoplaySpeed, 10));
        }
        
        /**
         * Stop autoplay timer
         */
        function stopAutoplay() {
            if (autoplayTimer) {
                clearTimeout(autoplayTimer);
                autoplayTimer = null;
            }
        }
        
        // Initialize touch events if enabled
        if (atsSettings.enableSwipe === 'yes') {
            // Touch event handlers
            track.on('touchstart', function(e) {
                touchStartX = e.originalEvent.touches[0].clientX;
                touchStartY = e.originalEvent.touches[0].clientY;
                isHorizontalSwipe = false;
                
                // Stop autoplay on user interaction
                stopAutoplay();
            });
            
            track.on('touchmove', function(e) {
                if (window.innerWidth > 968) return;
                
                const touchX = e.originalEvent.touches[0].clientX;
                const touchY = e.originalEvent.touches[0].clientY;
                
                // Calculate the difference from start position
                const diffX = touchStartX - touchX;
                const diffY = touchStartY - touchY;
                
                // Determine if this is a horizontal swipe
                if (!isHorizontalSwipe) {
                    isHorizontalSwipe = Math.abs(diffX) > Math.abs(diffY);
                }
                
                // Only prevent default and handle swipe if it's primarily horizontal
                if (isHorizontalSwipe) {
                    e.preventDefault();
                    const currentTransform = -currentSlide * slides.first().outerWidth(true);
                    track.css('transform', `translateX(${currentTransform - diffX}px)`);
                }
            });
            
            track.on('touchend', function(e) {
                if (window.innerWidth > 968 || !isHorizontalSwipe) return;
                
                touchEndX = e.originalEvent.changedTouches[0].clientX;
                const diff = touchStartX - touchEndX;
                
                if (Math.abs(diff) > swipeThreshold) {
                    if (diff > 0 && currentSlide < slideCount - 1) {
                        // Swipe left - next slide
                        currentSlide++;
                    } else if (diff < 0 && currentSlide > 0) {
                        // Swipe right - previous slide
                        currentSlide--;
                    }
                }
                
                updateSlidePosition();
                
                // Restart autoplay after user interaction
                startAutoplay();
            });
        }
        
        // Event handlers
        prevButton.on('click', function() {
            currentSlide = (currentSlide - 1 + slideCount) % slideCount;
            updateSlidePosition();
            
            // Stop and restart autoplay on user interaction
            stopAutoplay();
            startAutoplay();
        });
        
        nextButton.on('click', function() {
            currentSlide = (currentSlide + 1) % slideCount;
            updateSlidePosition();
            
            // Stop and restart autoplay on user interaction
            stopAutoplay();
            startAutoplay();
        });
        
        dots.each(function(index) {
            $(this).on('click', function() {
                currentSlide = index;
                updateSlidePosition();
                
                // Stop and restart autoplay on user interaction
                stopAutoplay();
                startAutoplay();
            });
        });
        
        $(document).on('click', '.video-link', function(e) {
            e.preventDefault();
            const videoUrl = $(this).data('video-url');
            openVideoPopup(videoUrl);
            
            // Stop autoplay when video is opened
            stopAutoplay();
        });
        
        closeVideo.on('click', closeVideoPopup);
        
        videoOverlay.on('click', function(e) {
            if (e.target === this) {
                closeVideoPopup();
            }
        });
        
        $(document).on('keyup', function(e) {
            if (e.key === 'Escape' && videoOverlay.is(':visible')) {
                closeVideoPopup();
            }
        });
        
        // Restart autoplay when video is closed
        videoOverlay.on('hidden', function() {
            startAutoplay();
        });
        
        // Resize handler
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(updateSlidePosition, 250);
        });
        
        // Initialize slider
        updateSlidePosition();
        
        // Start autoplay if enabled
        startAutoplay();
    }
    
})(jQuery);