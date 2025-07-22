<?php
/**
 * Plugin Name: Custom Contact Form
 * Plugin URI: https://github.com/yourusername/custom-contact-form
 * Description: A lightweight, secure, and customizable contact form plugin for WordPress.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: custom-contact-form
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Network: false
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Current plugin version.
 */
define('CUSTOM_CONTACT_FORM_VERSION', '1.0.0');
define('CUSTOM_CONTACT_FORM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CUSTOM_CONTACT_FORM_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_custom_contact_form() {
    require_once CUSTOM_CONTACT_FORM_PLUGIN_DIR . 'includes/class-ccf-activator.php';
    Custom_Contact_Form_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_custom_contact_form() {
    require_once CUSTOM_CONTACT_FORM_PLUGIN_DIR . 'includes/class-ccf-deactivator.php';
    Custom_Contact_Form_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_custom_contact_form');
register_deactivation_hook(__FILE__, 'deactivate_custom_contact_form');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require CUSTOM_CONTACT_FORM_PLUGIN_DIR . 'includes/class-ccf-core.php';

/**
 * Begins execution of the plugin.
 */
function run_custom_contact_form() {
    $plugin = new Custom_Contact_Form_Core();
    $plugin->run();
}
run_custom_contact_form();