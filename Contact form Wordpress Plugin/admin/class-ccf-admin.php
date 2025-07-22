<?php
/**
 * The admin-specific functionality of the plugin.
 */
class Custom_Contact_Form_Admin {

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
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, CUSTOM_CONTACT_FORM_PLUGIN_URL . 'admin/css/admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, CUSTOM_CONTACT_FORM_PLUGIN_URL . 'admin/js/admin.js', array('jquery'), $this->version, false);
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            __('Custom Contact Form Settings', 'custom-contact-form'),
            __('Contact Form', 'custom-contact-form'),
            'manage_options',
            'custom-contact-form',
            array($this, 'admin_page')
        );

        add_menu_page(
            __('Contact Form Submissions', 'custom-contact-form'),
            __('Form Submissions', 'custom-contact-form'),
            'manage_options',
            'ccf-submissions',
            array($this, 'submissions_page'),
            'dashicons-email-alt',
            30
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('ccf_settings_group', 'ccf_settings', array($this, 'sanitize_settings'));

        add_settings_section(
            'ccf_general_section',
            __('General Settings', 'custom-contact-form'),
            array($this, 'general_section_callback'),
            'custom-contact-form'
        );

        add_settings_field(
            'recipient_email',
            __('Recipient Email', 'custom-contact-form'),
            array($this, 'recipient_email_callback'),
            'custom-contact-form',
            'ccf_general_section'
        );

        add_settings_field(
            'subject_prefix',
            __('Subject Prefix', 'custom-contact-form'),
            array($this, 'subject_prefix_callback'),
            'custom-contact-form',
            'ccf_general_section'
        );

        add_settings_field(
            'success_message',
            __('Success Message', 'custom-contact-form'),
            array($this, 'success_message_callback'),
            'custom-contact-form',
            'ccf_general_section'
        );

        add_settings_field(
            'error_message',
            __('Error Message', 'custom-contact-form'),
            array($this, 'error_message_callback'),
            'custom-contact-form',
            'ccf_general_section'
        );

        add_settings_field(
            'required_fields',
            __('Required Fields', 'custom-contact-form'),
            array($this, 'required_fields_callback'),
            'custom-contact-form',
            'ccf_general_section'
        );

        add_settings_field(
            'store_submissions',
            __('Store Submissions', 'custom-contact-form'),
            array($this, 'store_submissions_callback'),
            'custom-contact-form',
            'ccf_general_section'
        );
    }

    /**
     * Sanitize settings
     */
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

        if (isset($input['required_fields'])) {
            $sanitized_input['required_fields'] = array_map('sanitize_text_field', $input['required_fields']);
        }

        if (isset($input['store_submissions'])) {
            $sanitized_input['store_submissions'] = (bool) $input['store_submissions'];
        }

        return $sanitized_input;
    }

    /**
     * Admin page callback
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('ccf_settings_group');
                do_settings_sections('custom-contact-form');
                submit_button();
                ?>
            </form>
            
            <div class="ccf-shortcode-info">
                <h3><?php _e('Usage', 'custom-contact-form'); ?></h3>
                <p><?php _e('Use the following shortcode to display the contact form:', 'custom-contact-form'); ?></p>
                <code>[custom_contact_form]</code>
            </div>
        </div>
        <?php
    }

    /**
     * Submissions page callback
     */
    public function submissions_page() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'ccf_submissions';

        // Handle bulk actions
        if (isset($_POST['action']) && $_POST['action'] === 'delete_selected' && !empty($_POST['submission_ids'])) {
            check_admin_referer('ccf_bulk_action');
            $ids = array_map('intval', $_POST['submission_ids']);
            $placeholders = implode(',', array_fill(0, count($ids), '%d'));
            $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id IN ($placeholders)", $ids));
            echo '<div class="notice notice-success"><p>' . __('Selected submissions deleted.', 'custom-contact-form') . '</p></div>';
        }

        // Get submissions
        $submissions = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submitted_at DESC LIMIT 50");

        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php if (empty($submissions)): ?>
                <p><?php _e('No submissions found.', 'custom-contact-form'); ?></p>
            <?php else: ?>
                <form method="post">
                    <?php wp_nonce_field('ccf_bulk_action'); ?>
                    <div class="tablenav top">
                        <div class="alignleft actions bulkactions">
                            <select name="action">
                                <option value=""><?php _e('Bulk Actions', 'custom-contact-form'); ?></option>
                                <option value="delete_selected"><?php _e('Delete', 'custom-contact-form'); ?></option>
                            </select>
                            <input type="submit" class="button action" value="<?php _e('Apply', 'custom-contact-form'); ?>">
                        </div>
                    </div>
                    
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <td class="manage-column column-cb check-column">
                                    <input type="checkbox" id="cb-select-all-1">
                                </td>
                                <th><?php _e('Name', 'custom-contact-form'); ?></th>
                                <th><?php _e('Email', 'custom-contact-form'); ?></th>
                                <th><?php _e('Subject', 'custom-contact-form'); ?></th>
                                <th><?php _e('Message', 'custom-contact-form'); ?></th>
                                <th><?php _e('Date', 'custom-contact-form'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($submissions as $submission): ?>
                                <tr>
                                    <th scope="row" class="check-column">
                                        <input type="checkbox" name="submission_ids[]" value="<?php echo esc_attr($submission->id); ?>">
                                    </th>
                                    <td><?php echo esc_html($submission->name); ?></td>
                                    <td><?php echo esc_html($submission->email); ?></td>
                                    <td><?php echo esc_html($submission->subject); ?></td>
                                    <td><?php echo esc_html(wp_trim_words($submission->message, 10)); ?></td>
                                    <td><?php echo esc_html(mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $submission->submitted_at)); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Settings callbacks
     */
    public function general_section_callback() {
        echo '<p>' . __('Configure the basic settings for your contact form.', 'custom-contact-form') . '</p>';
    }

    public function recipient_email_callback() {
        $options = get_option('ccf_settings');
        $value = isset($options['recipient_email']) ? $options['recipient_email'] : get_option('admin_email');
        echo '<input type="email" name="ccf_settings[recipient_email]" value="' . esc_attr($value) . '" class="regular-text" required>';
    }

    public function subject_prefix_callback() {
        $options = get_option('ccf_settings');
        $value = isset($options['subject_prefix']) ? $options['subject_prefix'] : '[Contact Form]';
        echo '<input type="text" name="ccf_settings[subject_prefix]" value="' . esc_attr($value) . '" class="regular-text">';
    }

    public function success_message_callback() {
        $options = get_option('ccf_settings');
        $value = isset($options['success_message']) ? $options['success_message'] : __('Thank you for your message. We will get back to you soon!', 'custom-contact-form');
        echo '<textarea name="ccf_settings[success_message]" rows="3" class="large-text">' . esc_textarea($value) . '</textarea>';
    }

    public function error_message_callback() {
        $options = get_option('ccf_settings');
        $value = isset($options['error_message']) ? $options['error_message'] : __('Sorry, there was an error sending your message. Please try again.', 'custom-contact-form');
        echo '<textarea name="ccf_settings[error_message]" rows="3" class="large-text">' . esc_textarea($value) . '</textarea>';
    }

    public function required_fields_callback() {
        $options = get_option('ccf_settings');
        $required_fields = isset($options['required_fields']) ? $options['required_fields'] : array('name', 'email', 'message');
        $all_fields = array('name', 'email', 'subject', 'message');
        
        echo '<fieldset>';
        foreach ($all_fields as $field) {
            $checked = in_array($field, $required_fields) ? 'checked="checked"' : '';
            echo '<label><input type="checkbox" name="ccf_settings[required_fields][]" value="' . esc_attr($field) . '" ' . $checked . '> ' . ucfirst($field) . '</label><br>';
        }
        echo '</fieldset>';
        echo '<p class="description">' . __('Select which fields should be required.', 'custom-contact-form') . '</p>';
    }

    public function store_submissions_callback() {
        $options = get_option('ccf_settings');
        $value = isset($options['store_submissions']) ? $options['store_submissions'] : true;
        echo '<input type="checkbox" name="ccf_settings[store_submissions]" value="1" ' . checked(1, $value, false) . '>';
        echo '<p class="description">' . __('Store form submissions in the database for review.', 'custom-contact-form') . '</p>';
    }
}