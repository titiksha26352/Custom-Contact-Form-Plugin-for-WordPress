<?php
/**
 * Fired during plugin activation
 */
class Custom_Contact_Form_Activator {

    /**
     * Code to run during plugin activation.
     */
    public static function activate() {
        // Set default options
        $default_options = array(
            'recipient_email' => get_option('admin_email'),
            'subject_prefix' => '[Contact Form]',
            'success_message' => __('Thank you for your message. We will get back to you soon!', 'custom-contact-form'),
            'error_message' => __('Sorry, there was an error sending your message. Please try again.', 'custom-contact-form'),
            'required_fields' => array('name', 'email', 'message'),
            'enable_captcha' => false,
            'store_submissions' => true
        );

        add_option('ccf_settings', $default_options);

        // Create submissions table if storing submissions is enabled
        self::create_submissions_table();

        // Clear rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Create submissions table
     */
    private static function create_submissions_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'ccf_submissions';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            email varchar(100) NOT NULL,
            subject varchar(200),
            message text NOT NULL,
            submitted_at datetime DEFAULT CURRENT_TIMESTAMP,
            user_ip varchar(45),
            user_agent text,
            status varchar(20) DEFAULT 'unread',
            PRIMARY KEY (id),
            KEY submitted_at (submitted_at),
            KEY status (status)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}