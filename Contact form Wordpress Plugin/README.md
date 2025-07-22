# Custom Contact Form WordPress Plugin

A lightweight, secure, and customizable contact form plugin for WordPress that provides a complete solution for collecting and managing user inquiries without relying on third-party services.

## Features

- **Easy to Use**: Simple shortcode implementation `[custom_contact_form]`
- **Responsive Design**: Mobile-friendly contact forms that work on all devices
- **Secure**: Built-in nonce verification, data sanitization, and validation
- **AJAX Powered**: Smooth form submission without page reloads
- **Customizable**: Flexible settings for messages, required fields, and styling
- **Database Storage**: Optional storage of form submissions in WordPress database
- **Email Notifications**: Automatic email notifications to administrators
- **Admin Interface**: Easy-to-use admin panel for managing settings and submissions
- **Translation Ready**: Full internationalization support
- **No Dependencies**: Works without external libraries or services

## Installation

### Manual Installation

1. Download the plugin files
2. Upload the `custom-contact-form` folder to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure the plugin settings under 'Settings' > 'Contact Form'

### WordPress Admin Installation

1. Go to 'Plugins' > 'Add New' in your WordPress admin
2. Upload the plugin zip file
3. Activate the plugin
4. Configure the settings

## Usage

### Basic Usage

Add the contact form to any post, page, or widget using the shortcode:

```
[custom_contact_form]
```

### Shortcode Parameters

Customize the form appearance with these parameters:

```
[custom_contact_form title="Get In Touch" show_title="true"]
```

**Available Parameters:**
- `title` - Custom form title (default: "Contact Us")
- `show_title` - Show/hide the form title (default: "true")

### Configuration

1. Go to **Settings > Contact Form** in your WordPress admin
2. Configure the following options:
   - **Recipient Email**: Where form submissions will be sent
   - **Subject Prefix**: Prefix for email subjects
   - **Success Message**: Message shown after successful submission
   - **Error Message**: Message shown when submission fails

### Managing Submissions

1. Go to **Form Submissions** in your WordPress admin
2. View all form submissions with details
3. Use bulk actions to manage multiple submissions
4. Export or delete submissions as needed

## File Structure

```
custom-contact-form/
├── custom-contact-form.php          # Main plugin file
├── README.md                        # Documentation
├── includes/                        # Core plugin classes
│   ├── class-ccf-core.php          # Main plugin class
│   ├── class-ccf-loader.php        # Hook loader
│   ├── class-ccf-activator.php     # Plugin activation
│   ├── class-ccf-deactivator.php   # Plugin deactivation
│   └── class-ccf-i18n.php          # Internationalization
├── admin/                           # Admin functionality
│   ├── class-ccf-admin.php         # Admin interface
│   ├── css/admin.css                # Admin styles
│   └── js/admin.js                  # Admin JavaScript
└── public/                          # Public functionality
    ├── class-ccf-public.php         # Public interface
    ├── css/style.css                # Frontend styles
    └── js/script.js                 # Frontend JavaScript
```

## Customization

### Styling

The plugin includes responsive CSS that works with most themes. You can customize the appearance by:

1. **Override CSS**: Add custom CSS to your theme's style.css
2. **Custom Stylesheet**: Enqueue your own stylesheet that loads after the plugin's CSS
3. **Theme Integration**: Copy and modify the plugin's CSS classes

### Hooks and Filters

The plugin provides several hooks for developers:

```php
// Modify form fields before display
add_filter('ccf_form_fields', 'your_custom_fields_function');

// Process form data before saving
add_action('ccf_before_save_submission', 'your_processing_function');

// Modify email content
add_filter('ccf_email_message', 'your_email_customization');
```

### Database Schema

Form submissions are stored in the `wp_ccf_submissions` table with the following structure:

- `id` - Unique submission ID
- `name` - Sender's name
- `email` - Sender's email
- `subject` - Message subject
- `message` - Message content
- `submitted_at` - Submission timestamp
- `user_ip` - Sender's IP address
- `user_agent` - Browser information
- `status` - Submission status (unread/read)

## Security Features

- **Nonce Verification**: All form submissions are verified with WordPress nonces
- **Data Sanitization**: All input data is properly sanitized before processing
- **SQL Injection Protection**: Uses WordPress prepared statements
- **XSS Prevention**: All output is properly escaped
- **Rate Limiting**: Built-in protection against spam submissions

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Internet Explorer 11+

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Changelog

### Version 1.0.0
- Initial release
- Basic contact form functionality
- Admin interface for settings and submissions
- AJAX form submission
- Email notifications
- Database storage
- Responsive design
- Security features

## Support

For support, feature requests, or bug reports, please visit the plugin's GitHub repository or contact the developer.

## License

This plugin is licensed under the GPL v2 or later.

## Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues on the GitHub repository.

---

**Custom Contact Form** - A professional WordPress contact form solution.


---

## 📄 License

This plugin is licensed under the **GPL v2 or later**. See [LICENSE](./LICENSE) for full terms.

---

## 🤝 Contributing

We welcome contributions via PR or issues.

---

## 📧 Support

For bugs, questions, or features, open an issue or contact the developer on GitHub.
