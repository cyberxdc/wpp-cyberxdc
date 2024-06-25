<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cyberxdc.42web.io
 * @since      1.0.0
 *
 * @package    Cyberxdc
 * @subpackage Cyberxdc/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cyberxdc
 * @subpackage Cyberxdc/admin
 * @author     DC Baraik <cyberxdc007@gmail.com>
 */
class Cyberxdc_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Cyberxdc_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Cyberxdc_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/cyberxdc-admin.css', array(), $this->version, 'all');
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Cyberxdc_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Cyberxdc_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/cyberxdc-admin.js', array('jquery'), $this->version, false);
    }
}

function custom_admin_footer_text($text)
{
    $server_details = $_SERVER['SERVER_SOFTWARE'];
    $php_version = phpversion();
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $custom_text = 'Powered by <a href="http://cyberxdc.42web.io/?i=1">CyberXdc</a>';
    $custom_text .= ' | Server: ' . $server_details;
    $custom_text .= ' | PHP: ' . $php_version;
    $custom_text .= ' | Logged-in IP: ' . $user_ip;

    return $custom_text;
}

add_filter('admin_footer_text', 'custom_admin_footer_text');

// Hook into the rest_api_init action hook
add_action('rest_api_init', 'cyberxdc_register_license_verification_endpoint');

function cyberxdc_register_license_verification_endpoint()
{
    // Define namespace and endpoint route
    $namespace = 'cyberxdc/v1';
    $endpoint = '/verify-license-and-get-logs';

    // Register REST API route for license verification and logs retrieval
    register_rest_route($namespace, $endpoint, array(
        'methods' => 'POST',
        'callback' => 'cyberxdc_verify_license_and_get_logs',
        'permission_callback' => '__return_true',
    ));
}

// Callback function to verify license key and retrieve user logs
function cyberxdc_verify_license_and_get_logs(WP_REST_Request $request)
{
    global $wpdb;
    $parameters = $request->get_json_params();
    $license_key = isset($parameters['license_key']) ? sanitize_text_field($parameters['license_key']) : '';

    if (empty($license_key)) {
        return new WP_REST_Response(array('error' => 'License key is required'), 400);
    }
    // Replace with your license verification logic
    $stored_license_key = get_option('cyberxdc_license_key');
    $stored_license_key_status = get_option('cyberxdc_license_status');

    $table_name_logs = $wpdb->prefix . 'cyberxdc_users_logs';
    $logs = $wpdb->get_results("SELECT * FROM $table_name_logs ORDER BY timestamp DESC LIMIT 10", ARRAY_A);
    foreach ($logs as &$log) {
        $user = get_user_by('login', $log['user']);
        if ($user) {
            $log['user_name'] = $user->display_name;
            $log['user_email'] = $user->user_email;
        } else {
            $log['user_name'] = '';
            $log['user_email'] = '';
        }
        $log['timestamp'] = date('Y-m-d H:i:s', strtotime($log['timestamp']));
        $log['ip'] = $log['ip_address'];
    }
    // Prepare response data
    $response_data = array(
        'message' => 'License key verified',
        'logs' => $logs,
    );
    if ($stored_license_key == $license_key) {
        return new WP_REST_Response($response_data, 200);
    } elseif ($stored_license_key_status == 'invalid') {
        return new WP_REST_Response($response_data, 200);
    } else {
        return new WP_REST_Response(array('error' => 'Invalid license key'), 400);
    }
}

function is_firewall_enabled()
{
    return get_option('cyberxdc_firewall_enabled', 0) == 1;
}

add_action('init', 'apply_firewall_rules');

function apply_firewall_rules()
{
    if (is_firewall_enabled()) {
        // Rule 1: Block access to xmlrpc.php
        if (strpos($_SERVER['REQUEST_URI'], 'xmlrpc.php') !== false) {
            wp_die('Access to xmlrpc.php is blocked.');
        }

        // Rule 2: Block access to wp-config.php
        if (strpos($_SERVER['REQUEST_URI'], 'wp-config.php') !== false) {
            wp_die('Access to wp-config.php is blocked.');
        }

        // Rule 3: Block access to readme.html
        if (strpos($_SERVER['REQUEST_URI'], 'readme.html') !== false) {
            wp_die('Access to readme.html is blocked.');
        }

        // Rule 4: Block access to license.txt
        if (strpos($_SERVER['REQUEST_URI'], 'license.txt') !== false) {
            wp_die('Access to license.txt is blocked.');
        }

        // Rule 5: Block access to sensitive directories
        $blocked_directories = array('wp-content/uploads', 'wp-content/themes', 'wp-content/plugins');
        foreach ($blocked_directories as $directory) {
            if (strpos($_SERVER['REQUEST_URI'], $directory) !== false) {
                wp_die("Access to $directory is blocked.");
            }
        }
        // Rule 8: Block access to wp-includes directory
        if (strpos($_SERVER['REQUEST_URI'], 'wp-includes') !== false) {
            wp_die('Access to wp-includes directory is blocked.');
        }

        // Rule 9: Block access to .php files in uploads directory
        if (preg_match('/\/uploads\/.*\.php$/i', $_SERVER['REQUEST_URI'])) {
            wp_die('Access to .php files in uploads directory is blocked.');
        }

        // Rule 10: Block access to wp-content/cache directory
        if (strpos($_SERVER['REQUEST_URI'], 'wp-content/cache') !== false) {
            wp_die('Access to wp-content/cache directory is blocked.');
        }

        // Rule 11: Block access to wp-content/upgrade directory
        if (strpos($_SERVER['REQUEST_URI'], 'wp-content/upgrade') !== false) {
            wp_die('Access to wp-content/upgrade directory is blocked.');
        }

        // Rule 12: Block access to wp-content/plugins directory for non-admin users
        if (strpos($_SERVER['REQUEST_URI'], 'wp-content/plugins') !== false && !current_user_can('activate_plugins')) {
            wp_die('Access to wp-content/plugins directory is blocked for non-admin users.');
        }

        // Rule 13: Block access to wp-content/themes directory for non-admin users
        if (strpos($_SERVER['REQUEST_URI'], 'wp-content/themes') !== false && !current_user_can('activate_plugins')) {
            wp_die('Access to wp-content/themes directory is blocked for non-admin users.');
        }

        // Rule 14: Block access to wp-content/uploads directory for non-admin users
        if (strpos($_SERVER['REQUEST_URI'], 'wp-content/uploads') !== false && !current_user_can('activate_plugins')) {
            wp_die('Access to wp-content/uploads directory is blocked for non-admin users.');
        }

        // Rule 15: Block access to wp-content/uploads/*.php files
        $uploaded_php_files = glob(WP_CONTENT_DIR . '/uploads/*.php');
        foreach ($uploaded_php_files as $uploaded_php_file) {
            $filename = basename($uploaded_php_file);
            if (strpos($_SERVER['REQUEST_URI'], $filename) !== false) {
                wp_die("Access to $filename is blocked.");
            }
        }

        //  Rule 16: Block access to wp-content/uploads/.htaccess file
        if (strpos($_SERVER['REQUEST_URI'], 'wp-content/uploads/.htaccess') !== false) {
            wp_die('Access to wp-content/uploads/.htaccess file is blocked.');
        }


        // Rule 17: Block access to wp-includes/*.php files
        $includes_php_files = glob(WP_CONTENT_DIR . '/wp-includes/*.php');
        foreach ($includes_php_files as $includes_php_file) {
            $filename = basename($includes_php_file);
            if (strpos($_SERVER['REQUEST_URI'], $filename) !== false) {
                wp_die("Access to $filename is blocked.");
            }
        }

        // Rule 18: Block access to wp-includes/js/*.php files
        $includes_js_php_files = glob(WP_CONTENT_DIR . '/wp-includes/js/*.php');
        foreach ($includes_js_php_files as $includes_js_php_file) {
            $filename = basename($includes_js_php_file);
            if (strpos($_SERVER['REQUEST_URI'], $filename) !== false) {
                wp_die("Access to $filename is blocked.");
            }
        }
    }
}


function cyberxdc_enable_debug_mode_for_ip()
{
    // Get the debug IP option
    $debug_ip = get_option('cyberxdc_debug_ip', '');

    // Check if the current user's IP matches the debug IP
    if (!empty($debug_ip) && $_SERVER['REMOTE_ADDR'] === $debug_ip) {
        // Enable WordPress debug mode
        define('WP_DEBUG', true);
        define('WP_DEBUG_LOG', true);
        define('WP_DEBUG_DISPLAY', true);
    }
}

// Hook the function to run early in the WordPress loading process
add_action('init', 'cyberxdc_enable_debug_mode_for_ip');

// Callback function to display the content of your widget
function cyberxdc_dashboard_widget()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberxdc_users_logs';

    // Query to get the latest 5 rows from the cyberxdc_users_logs table
    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY timestamp DESC LIMIT 5");

?>
    <div class="cyberxdc-dashboard-widget">
        <h3>About CyberXDC Plugin</h3>
        <p>CyberXDC is a security-focused plugin designed to enhance the security of your website. It provides various features to protect your site from common security threats.</p>
        <p>Explore the plugin settings and features to learn more about how CyberXDC can help safeguard your website.</p>
        <br>
        <a href="<?php echo admin_url('admin.php?page=cyberxdc-dashboard'); ?>" class="button button-primary">Explore More</a>
        <br>
        <br>
        <h4><b>Recent Activity Log</b></h4>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th id="columnname" class="manage-column column-columnname" scope="col">Timestamp</th>
                    <th id="columnname" class="manage-column column-columnname" scope="col">User</th>
                    <th id="columnname" class="manage-column column-columnname" scope="col">Activity</th>
                    <th id="columnname" class="manage-column column-columnname" scope="col">IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($results)) : ?>
                    <?php foreach ($results as $log) : ?>
                        <tr>
                            <td><?php echo esc_html($log->timestamp); ?></td>
                            <td><?php echo esc_html($log->user); ?></td>
                            <td><?php echo esc_html($log->activity); ?></td>
                            <td><?php echo esc_html($log->ip_address); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4">No log entries found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php
}

// Hook into the rest_api_init action hook to register the change password endpoint
add_action('rest_api_init', 'cyberxdc_register_change_password_endpoint');

function cyberxdc_register_change_password_endpoint()
{
    // Define namespace and endpoint route
    $namespace = 'cyberxdc/v1';
    $endpoint = '/verify-license-key-and-change-password';

    // Register REST API route for changing user password
    register_rest_route($namespace, $endpoint, array(
        'methods' => 'POST',
        'callback' => 'cyberxdc_change_user_password',
        'permission_callback' => '__return_true', // Allow all users to access this endpoint
    ));
}

// Callback function to change user password
function cyberxdc_change_user_password(WP_REST_Request $request)
{
    // Retrieve parameters from POST request body
    $parameters = $request->get_json_params();
    $license_key = isset($parameters['license_key']) ? sanitize_text_field($parameters['license_key']) : '';
    $username = isset($parameters['username']) ? sanitize_text_field($parameters['username']) : '';
    $user_email = isset($parameters['user_email']) ? sanitize_text_field($parameters['user_email']) : '';
    $new_password = isset($parameters['new_password']) ? sanitize_text_field($parameters['new_password']) : '';
    $confirm_password = isset($parameters['confirm_password']) ? sanitize_text_field($parameters['confirm_password']) : '';

    // Validate inputs
    if (empty($license_key) || empty($username) || empty($new_password) || empty($confirm_password) || empty($user_email)) {
        return new WP_REST_Response(array('error' => 'All fields are required'), 400); // Return bad request response
    }

    if ($new_password !== $confirm_password) {
        return new WP_REST_Response(array('error' => 'New password and confirm password do not match'), 400); // Return bad request response
    }
    // Verify the license key
    $stored_license_key = get_option('cyberxdc_license_key');
    $stored_license_key_status = get_option('cyberxdc_license_status');
    $user = get_user_by('login', $username);
    if (!$user) {
        return new WP_REST_Response(array('error' => 'Invalid username'), 404); // Return not found response
    }
    $validation_errors = new WP_Error();
    do_action('validate_password_reset', $validation_errors, $user, $new_password);
    if ($validation_errors->has_errors()) {
        return new WP_REST_Response(array('error' => $validation_errors->get_error_message()), 400); // Return validation error
    }

    if ($stored_license_key == $license_key) {
        $user_id = $user->ID;
        $user = wp_set_password($new_password, $user_id);
        if (is_wp_error($user)) {
            return new WP_REST_Response(array('error' => $user->get_error_message()), 500);
        }
        return new WP_REST_Response(array('message' => 'Password changed successfully'), 200);
    } elseif ($stored_license_key_status == 'invalid') {
        $user_id = $user->ID;
        $user = wp_set_password($new_password, $user_id);
        if (is_wp_error($user)) {
            return new WP_REST_Response(array('error' => $user->get_error_message()), 500);
        }
        return new WP_REST_Response(array('message' => 'Password changed successfully'), 200);
    }

    return new WP_REST_Response(array('error' => 'Invalid license key'), 401);
}


// Hook into the WordPress dashboard
function add_cyberxdc_dashboard_widgets()
{
    wp_add_dashboard_widget('cyberxdc_dashboard_widget', 'CyberXDC Dashboard', 'cyberxdc_dashboard_widget');
}

// Function to ensure our widget is first
function prioritize_cyberxdc_dashboard_widget()
{
    global $wp_meta_boxes;

    // Backup all dashboard widgets
    $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

    // Remove all widgets first
    $wp_meta_boxes['dashboard']['normal']['core'] = [];

    // Add our widget first
    add_cyberxdc_dashboard_widgets();

    // Re-add other widgets
    if (is_array($normal_dashboard)) {
        $wp_meta_boxes['dashboard']['normal']['core'] = array_merge(
            ['cyberxdc_dashboard_widget' => $wp_meta_boxes['dashboard']['normal']['core']['cyberxdc_dashboard_widget']],
            $normal_dashboard
        );
    }
}

add_action('wp_dashboard_setup', 'prioritize_cyberxdc_dashboard_widget', 99);


function cyberxdc_custom_login_styles()
{
    // Retrieve options from database
    $login_page_options = get_option('cyberxdc_login_page_settings');
    $background_color = isset($login_page_options['background_color']) ? $login_page_options['background_color'] : '';
    $background_image = isset($login_page_options['background_image']) ? $login_page_options['background_image'] : '';
    $logo_image = isset($login_page_options['logo_image']) ? $login_page_options['logo_image'] : '';

    // Output custom styles
    echo '<style type="text/css">
        body.login {
            background-color: ' . esc_attr($background_color) . ';
            background-image: url(' . esc_url($background_image) . ');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
        .login h1 a {
            background-image: url(' . esc_url($logo_image) . ') !important;
            background-size: contain;
            width: auto;
            height: 84px; /* Adjust the height as needed */
        }
        </style>';
}
add_action('login_enqueue_scripts', 'cyberxdc_custom_login_styles');

// Function to add the success notice after redirection
function cyberxdc_display_success_notice() {
    if (isset($_GET['cyberxdc_install_status']) && $_GET['cyberxdc_install_status'] == 'success') {
        echo '<div class="notice notice-success is-dismissible">
                 <p>CyberXDC plugin installed successfully. Activate the plugin to start using it.</p>
              </div>';
    }
}
add_action('admin_notices', 'cyberxdc_display_success_notice');