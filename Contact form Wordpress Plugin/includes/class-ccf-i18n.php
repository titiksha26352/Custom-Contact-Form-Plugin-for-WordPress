<?php
/**
 * Define the internationalization functionality
 */
class Custom_Contact_Form_i18n {

    /**
     * Load the plugin text domain for translation.
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'custom-contact-form',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}