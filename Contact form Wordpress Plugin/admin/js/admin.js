(function($) {
    'use strict';

    $(document).ready(function() {
        // Handle select all checkbox
        $('#cb-select-all-1').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('input[name="submission_ids[]"]').prop('checked', isChecked);
        });
        
        // Handle individual checkboxes
        $('input[name="submission_ids[]"]').on('change', function() {
            var totalCheckboxes = $('input[name="submission_ids[]"]').length;
            var checkedCheckboxes = $('input[name="submission_ids[]"]:checked').length;
            
            $('#cb-select-all-1').prop('checked', totalCheckboxes === checkedCheckboxes);
        });
        
        // Confirm bulk delete action
        $('form').on('submit', function(e) {
            var action = $('select[name="action"]').val();
            var checkedItems = $('input[name="submission_ids[]"]:checked').length;
            
            if (action === 'delete_selected' && checkedItems > 0) {
                if (!confirm('Are you sure you want to delete the selected submissions? This action cannot be undone.')) {
                    e.preventDefault();
                }
            }
        });
        
        // Copy shortcode to clipboard
        $('.ccf-shortcode-info code').on('click', function() {
            var text = $(this).text();
            
            // Create temporary textarea to copy text
            var temp = $('<textarea>');
            $('body').append(temp);
            temp.val(text).select();
            document.execCommand('copy');
            temp.remove();
            
            // Show feedback
            var originalText = $(this).text();
            $(this).text('Copied!').css('background', '#46b450');
            
            setTimeout(function() {
                $('.ccf-shortcode-info code').text(originalText).css('background', '#f1f1f1');
            }, 2000);
        });
    });

})(jQuery);