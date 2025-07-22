@@ .. @@
     public function sanitize_settings($input) {
         $sanitized_input = array();

         if (isset($input['recipient_email'])) {
             $sanitized_input['recipient_email'] = sanitize_email($input['recipient_email']);
         }

         if (isset($input['subject_prefix'])) {
             $sanitized_input['subject_prefix'] = sanitize_text_field($input['subject_prefix']);
         }

         if (isset($input['success_message'])) {
             $sanitized_input['success_message'] = sanitize_textarea_field($input['success_message']);
         }

         if (isset($input['error_message'])) {
             $sanitized_input['error_message'] = sanitize_textarea_field($input['error_message']);
         }

+        // Preserve existing settings that aren't in the form
+        $existing_options = get_option('ccf_settings', array());
+        $sanitized_input = array_merge($existing_options, $sanitized_input);
+
         return $sanitized_input;
     }