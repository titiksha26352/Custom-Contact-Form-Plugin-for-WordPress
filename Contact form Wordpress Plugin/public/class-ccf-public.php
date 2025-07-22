<?php
/**
 * The public-facing functionality of the plugin.
 */
class Custom_Contact_Form_Public {

    /**
     * The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     */
    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, CUSTOM_CONTACT_FORM_PLUGIN_URL . 'public/css/style.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, CUSTOM_CONTACT_FORM_PLUGIN_URL . 'public/js/script.js', array('jquery'), $this->version, false);
        
        // Localize script for AJAX
        wp_localize_script($this->plugin_name, 'ccf_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ccf_nonce'),
            'loading_text' => __('Sending...', 'custom-contact-form'),
            'error_text' => __('Please fix the errors below.', 'custom-contact-form')
        ));
    }

    /**
     * Display the contact form via shortcode
     */
    public function display_contact_form($atts) {
        $atts = shortcode_atts(array(
            'title' => __('Contact Us', 'custom-contact-form'),
            'show_title' => 'true'
        ), $atts, 'custom_contact_form');

        ob_start();
        
        $options = get_option('ccf_settings', array());
        $required_fields = isset($options['required_fields']) ? $options['required_fields'] : array('name', 'email', 'message');
        
        ?>
        <div class="ccf-form-container">
            <?php if ($atts['show_title'] === 'true'): ?>
                <h3 class="ccf-form-title"><?php echo esc_html($atts['title']); ?></h3>
            <?php endif; ?>
            
            <div id="ccf-messages"></div>
            
            <form id="ccf-contact-form" class="ccf-form" method="post">
                <?php wp_nonce_field('ccf_submit_form', 'ccf_nonce'); ?>
                
                <div class="ccf-field-group">
                    <label for="ccf-name" class="ccf-label">
                        <?php _e('Name', 'custom-contact-form'); ?>
                        <?php if (in_array('name', $required_fields)): ?>
                            <span class="ccf-required">*</span>
                        <?php endif; ?>
                    </label>
                    <input type="text" id="ccf-name" name="ccf_name" class="ccf-input" 
                           <?php echo in_array('name', $required_fields) ? 'required' : ''; ?>>
                    <div class="ccf-error" id="ccf-name-error"></div>
                </div>

                <div class="ccf-field-group">
                    <label for="ccf-email" class="ccf-label">
                        <?php _e('Email', 'custom-contact-form'); ?>
                        <?php if (in_array('email', $required_fields)): ?>
                            <span class="ccf-required">*</span>
                        <?php endif; ?>
                    </label>
                    <input type="email" id="ccf-email" name="ccf_email" class="ccf-input" 
                           <?php echo in_array('email', $required_fields) ? 'required' : ''; ?>>
                    <div class="ccf-error" id="ccf-email-error"></div>
                </div>

                <div class="ccf-field-group">
                    <label for="ccf-subject" class="ccf-label">
                        <?php _e('Subject', 'custom-contact-form'); ?>
                        <?php if (in_array('subject', $required_fields)): ?>
                            <span class="ccf-required">*</span>
                        <?php endif; ?>
                    </label>
                    <input type="text" id="ccf-subject" name="ccf_subject" class="ccf-input" 
                           <?php echo in_array('subject', $required_fields) ? 'required' : ''; ?>>
                    <div class="ccf-error" id="ccf-subject-error"></div>
                </div>

                <div class="ccf-field-group">
                    <label for="ccf-message" class="ccf-label">
                        <?php _e('Message', 'custom-contact-form'); ?>
                        <?php if (in_array('message', $required_fields)): ?>
                            <span class="ccf-required">*</span>
                        <?php endif; ?>
                    </label>
                    <textarea id="ccf-message" name="ccf_message" class="ccf-textarea" rows="5" 
                              <?php echo in_array('message', $required_fields) ? 'required' : ''; ?>></textarea>
                    <div class="ccf-error" id="ccf-message-error"></div>
                </div>

                <div class="ccf-field-group">
                    <button type="submit" class="ccf-submit-btn">
                        <?php _e('Send Message', 'custom-contact-form'); ?>
                    </button>
                </div>
            </form>
        </div>
        <?php
        
        return ob_get_clean();
    }

    /**
     * Handle form submission via AJAX
     */
    public function handle_form_submission() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['ccf_nonce'], 'ccf_submit_form')) {
            wp_die(__('Security check failed.', 'custom-contact-form'));
        }

        $response = array();
        $errors = array();

        // Get form data
        $name = sanitize_text_field($_POST['ccf_name']);
        $email = sanitize_email($_POST['ccf_email']);
        $subject = sanitize_text_field($_POST['ccf_subject']);
        $message = sanitize_textarea_field($_POST['ccf_message']);

        // Get plugin settings
        $options = get_option('ccf_settings', array());
        $required_fields = isset($options['required_fields']) ? $options['required_fields'] : array('name', 'email', 'message');

        // Validate required fields
        if (in_array('name', $required_fields) && empty($name)) {
            $errors['name'] = __('Name is required.', 'custom-contact-form');
        }

        if (in_array('email', $required_fields) && empty($email)) {
            $errors['email'] = __('Email is required.', 'custom-contact-form');
        } elseif (!empty($email) && !is_email($email)) {
            $errors['email'] = __('Please enter a valid email address.', 'custom-contact-form');
        }

        if (in_array('subject', $required_fields) && empty($subject)) {
            $errors['subject'] = __('Subject is required.', 'custom-contact-form');
        }

        if (in_array('message', $required_fields) && empty($message)) {
            $errors['message'] = __('Message is required.', 'custom-contact-form');
        }

        // If there are validation errors, return them
        if (!empty($errors)) {
            $response['success'] = false;
            $response['errors'] = $errors;
            wp_send_json($response);
        }

        // Store submission in database if enabled
        if (isset($options['store_submissions']) && $options['store_submissions']) {
            $this->store_submission($name, $email, $subject, $message);
        }

        // Send email
        $email_sent = $this->send_email($name, $email, $subject, $message);

        if ($email_sent) {
            $success_message = isset($options['success_message']) ? $options['success_message'] : 
                __('Thank you for your message. We will get back to you soon!', 'custom-contact-form');
            
            $response['success'] = true;
            $response['message'] = $success_message;
        } else {
            $error_message = isset($options['error_message']) ? $options['error_message'] : 
                __('Sorry, there was an error sending your message. Please try again.', 'custom-contact-form');
            
            $response['success'] = false;
            $response['message'] = $error_message;
        }

        wp_send_json($response);
    }

    /**
     * Send email notification
     */
    private function send_email($name, $email, $subject, $message) {
        $options = get_option('ccf_settings', array());
        
        $to = isset($options['recipient_email']) ? $options['recipient_email'] : get_option('admin_email');
        $subject_prefix = isset($options['subject_prefix']) ? $options['subject_prefix'] : '[Contact Form]';
        
        $email_subject = $subject_prefix . ' ' . $subject;
        
        $email_message = sprintf(
            __("You have received a new message from your website contact form.\n\nName: %s\nEmail: %s\nSubject: %s\n\nMessage:\n%s\n\n---\nThis email was sent from your website contact form.", 'custom-contact-form'),
            $name,
            $email,
            $subject,
            $message
        );

        $headers = array(
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
            'Reply-To: ' . $name . ' <' . $email . '>'
        );

        return wp_mail($to, $email_subject, $email_message, $headers);
    }

    /**
     * Store submission in database
     */
    private function store_submission($name, $email, $subject, $message) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'ccf_submissions';
        
        $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'submitted_at' => current_time('mysql'),
                'user_ip' => $this->get_user_ip(),
                'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT']),
                'status' => 'unread'
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
    }

    /**
     * Get user IP address
     */
    private function get_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);
        } else {
            return sanitize_text_field($_SERVER['REMOTE_ADDR']);
        }
    }
}