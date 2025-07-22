(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle form submission
        $('#ccf-contact-form').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var submitBtn = form.find('.ccf-submit-btn');
            var messagesContainer = $('#ccf-messages');
            
            // Clear previous errors
            form.find('.ccf-error').removeClass('show').text('');
            form.find('.ccf-field-group').removeClass('error');
            messagesContainer.empty();
            
            // Show loading state
            submitBtn.addClass('loading').prop('disabled', true);
            
            // Prepare form data
            var formData = {
                action: 'ccf_submit_form',
                ccf_nonce: form.find('input[name="ccf_nonce"]').val(),
                ccf_name: form.find('input[name="ccf_name"]').val(),
                ccf_email: form.find('input[name="ccf_email"]').val(),
                ccf_subject: form.find('input[name="ccf_subject"]').val(),
                ccf_message: form.find('textarea[name="ccf_message"]').val()
            };
            
            // Submit form via AJAX
            $.ajax({
                url: ccf_ajax.ajax_url,
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        messagesContainer.html('<div class="ccf-message success">' + response.message + '</div>');
                        
                        // Reset form
                        form[0].reset();
                        
                        // Scroll to success message
                        $('html, body').animate({
                            scrollTop: messagesContainer.offset().top - 20
                        }, 500);
                        
                    } else {
                        if (response.errors) {
                            // Show field-specific errors
                            $.each(response.errors, function(field, message) {
                                var errorElement = $('#ccf-' + field + '-error');
                                var fieldGroup = errorElement.closest('.ccf-field-group');
                                
                                errorElement.text(message).addClass('show');
                                fieldGroup.addClass('error');
                            });
                            
                            // Show general error message
                            messagesContainer.html('<div class="ccf-message error">' + ccf_ajax.error_text + '</div>');
                            
                        } else if (response.message) {
                            // Show general error message
                            messagesContainer.html('<div class="ccf-message error">' + response.message + '</div>');
                        }
                        
                        // Scroll to error message
                        $('html, body').animate({
                            scrollTop: messagesContainer.offset().top - 20
                        }, 500);
                    }
                },
                error: function() {
                    messagesContainer.html('<div class="ccf-message error">An unexpected error occurred. Please try again.</div>');
                },
                complete: function() {
                    // Remove loading state
                    submitBtn.removeClass('loading').prop('disabled', false);
                }
            });
        });
        
        // Real-time validation
        $('#ccf-contact-form input, #ccf-contact-form textarea').on('blur', function() {
            var field = $(this);
            var fieldGroup = field.closest('.ccf-field-group');
            var errorElement = fieldGroup.find('.ccf-error');
            var fieldName = field.attr('name').replace('ccf_', '');
            
            // Clear previous error
            errorElement.removeClass('show').text('');
            fieldGroup.removeClass('error');
            
            // Validate field
            var value = field.val().trim();
            var isRequired = field.prop('required');
            
            if (isRequired && !value) {
                var fieldLabel = fieldGroup.find('.ccf-label').text().replace('*', '').trim();
                errorElement.text(fieldLabel + ' is required.').addClass('show');
                fieldGroup.addClass('error');
                return;
            }
            
            // Email validation
            if (fieldName === 'email' && value) {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    errorElement.text('Please enter a valid email address.').addClass('show');
                    fieldGroup.addClass('error');
                }
            }
        });
        
        // Clear error on input
        $('#ccf-contact-form input, #ccf-contact-form textarea').on('input', function() {
            var field = $(this);
            var fieldGroup = field.closest('.ccf-field-group');
            var errorElement = fieldGroup.find('.ccf-error');
            
            if (fieldGroup.hasClass('error') && field.val().trim()) {
                errorElement.removeClass('show');
                fieldGroup.removeClass('error');
            }
        });
    });

})(jQuery);