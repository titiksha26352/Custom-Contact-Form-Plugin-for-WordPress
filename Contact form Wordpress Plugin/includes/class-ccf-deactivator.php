<?php
/**
 * Fired during plugin deactivation
 */
class Custom_Contact_Form_Deactivator {

    /**
     * Code to run during plugin deactivation.
     */
    public static function deactivate() {
        // Clear any scheduled events
        wp_clear_scheduled_hook('ccf_cleanup_submissions');
        
        // Clear rewrite rules
        flush_rewrite_rules();
    }
}