/**
 * Advanced Testimonial Slider - Admin Scripts
 */
(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        // Initialize color pickers
        $('.ats-color-picker').wpColorPicker();
        
        // Image upload handling
        initImageUpload();
        
        // Question management
        initQuestionManagement();
        
        // Shortcode copy functionality
        initShortcodeCopy();
    });
    
    /**
     * Initialize image upload functionality
     */
    function initImageUpload() {
        // Image upload button
        $('.slide-image-upload').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var imageContainer = button.closest('.slide-image-wrapper');
            var imagePreview = imageContainer.find('.slide-image-preview');
            var imageUrlInput = imageContainer.find('#slide-image-url');
            var imageIdInput = imageContainer.find('#slide-image-id');
            
            // Create media frame
            var frame = wp.media({
                title: atsAdmin.mediaTitle,
                multiple: false,
                library: {
                    type: 'image'
                },
                button: {
                    text: atsAdmin.mediaButton
                }
            });
            
            // When an image is selected
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                
                // Update preview
                imagePreview.html('<img src="' + attachment.url + '" alt="" style="max-width: 100%; height: auto;">');
                
                // Update fields
                imageUrlInput.val(attachment.url);
                imageIdInput.val(attachment.id);
                
                // Update button text
                button.text('Change Image');
                
                // Show remove button
                if (!imageContainer.find('.slide-image-remove').length) {
                    button.after('<button type="button" class="button slide-image-remove">Remove Image</button>');
                    initImageRemove();
                }
            });
            
            // Open media frame
            frame.open();
        });
        
        // Initialize remove button if it exists
        initImageRemove();
    }
    
    /**
     * Initialize image remove functionality
     */
    function initImageRemove() {
        $('.slide-image-remove').off('click').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var imageContainer = button.closest('.slide-image-wrapper');
            var imagePreview = imageContainer.find('.slide-image-preview');
            var imageUrlInput = imageContainer.find('#slide-image-url');
            var imageIdInput = imageContainer.find('#slide-image-id');
            
            // Clear preview
            imagePreview.html('<p>No image selected</p>');
            
            // Clear fields
            imageUrlInput.val('');
            imageIdInput.val('');
            
            // Update button text
            imageContainer.find('.slide-image-upload').text('Select Image');
            
            // Hide remove button
            button.remove();
        });
    }
    
    /**
     * Initialize question management functionality
     */
    function initQuestionManagement() {
        // Add question button
        $('.add-question').on('click', function(e) {
            e.preventDefault();
            
            var questionsContainer = $('.slide-questions');
            var questionTemplate = $('#question-template').html();
            var nextIndex = $('.slide-question').length;
            
            // Replace placeholders in the template
            var newQuestion = questionTemplate
                .replace(/\{\{index\}\}/g, nextIndex)
                .replace(/\{\{number\}\}/g, nextIndex + 1);
            
            // Append new question
            questionsContainer.append(newQuestion);
            
            // Initialize remove functionality for the new question
            initQuestionRemove();
        });
        
        // Initialize remove functionality for existing questions
        initQuestionRemove();
    }
    
    /**
     * Initialize question remove functionality
     */
    function initQuestionRemove() {
        $('.remove-question').off('click').on('click', function(e) {
            e.preventDefault();
            
            var button = $(this);
            var question = button.closest('.slide-question');
            
            // Don't remove if it's the only question
            if ($('.slide-question').length <= 1) {
                return;
            }
            
            // Remove the question
            question.remove();
            
            // Renumber remaining questions
            renumberQuestions();
        });
    }
    
    /**
     * Renumber questions after removal
     */
    function renumberQuestions() {
        $('.slide-question').each(function(index) {
            var question = $(this);
            
            // Update index attribute
            question.attr('data-index', index);
            
            // Update number display
            question.find('.question-number').text(index + 1);
            
            // Update field names and IDs
            question.find('[name^="slide_questions["]').each(function() {
                var field = $(this);
                var name = field.attr('name').replace(/slide_questions\[\d+\]/, 'slide_questions[' + index + ']');
                field.attr('name', name);
            });
            
            question.find('[id^="question-"]').each(function() {
                var field = $(this);
                var id = field.attr('id').replace(/question-(text|video)-\d+/, 'question-$1-' + index);
                field.attr('id', id);
            });
        });
    }
    
    /**
     * Initialize shortcode copy functionality
     */
    function initShortcodeCopy() {
        $('.ats-copy-shortcode').on('click', function() {
            var shortcodeText = $(this).prev('code').text();
            
            // Create temporary input
            var tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(shortcodeText).select();
            
            // Copy text
            document.execCommand('copy');
            
            // Remove temporary input
            tempInput.remove();
            
            // Show confirmation
            var button = $(this);
            var originalText = button.text();
            
            button.text(atsAdmin.copied);
            
            setTimeout(function() {
                button.text(originalText);
            }, 1000);
        });
    }
    
})(jQuery);