<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 */
class Custom_Contact_Form_Core {

    /**
     * The loader that's responsible for maintaining and registering all hooks.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct() {
        if (defined('CUSTOM_CONTACT_FORM_VERSION')) {
            $this->version = CUSTOM_CONTACT_FORM_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'custom-contact-form';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        require_once CUSTOM_CONTACT_FORM_PLUGIN_DIR . 'includes/class-ccf-loader.php';
        require_once CUSTOM_CONTACT_FORM_PLUGIN_DIR . 'includes/class-ccf-i18n.php';
        require_once CUSTOM_CONTACT_FORM_PLUGIN_DIR . 'admin/class-ccf-admin.php';
        require_once CUSTOM_CONTACT_FORM_PLUGIN_DIR . 'public/class-ccf-public.php';

        $this->loader = new Custom_Contact_Form_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     */
    private function set_locale() {
        $plugin_i18n = new Custom_Contact_Form_i18n();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     */
    private function define_admin_hooks() {
        $plugin_admin = new Custom_Contact_Form_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_admin_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'register_settings');
    }

    /**
     * Register all of the hooks related to the public-facing functionality.
     */
    private function define_public_hooks() {
        $plugin_public = new Custom_Contact_Form_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('wp_ajax_ccf_submit_form', $plugin_public, 'handle_form_submission');
        $this->loader->add_action('wp_ajax_nopriv_ccf_submit_form', $plugin_public, 'handle_form_submission');
        $this->loader->add_shortcode('custom_contact_form', $plugin_public, 'display_contact_form');
        $this->loader->add_action('wp_ajax_ccf_submit_form', $plugin_public, 'handle_form_submission');
        $this->loader->add_action('wp_ajax_nopriv_ccf_submit_form', $plugin_public, 'handle_form_submission');
        $this->loader->add_shortcode('custom_contact_form', $plugin_public, 'display_contact_form');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}