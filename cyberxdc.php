<?php

/**
 * @link              http://localhost/cyberxdc
 * @since             1.0.1
 * @package           Cyberxdc
 *
 * @wordpress-plugin
 * Plugin Name:       CyberXDC
 * Plugin URI:        http://localhost/cyberxdc
 * Description:       "CyberXDC" is a WordPress plugin to manage basic functionslities of wordpress website and giving essential features to users, it is developed by DC Baraik and also customized for security purpose.
 * Version:           1.0.1
 * Author:            DC Baraik
 * Author URI:        http://localhost/cyberxdc
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cyberxdc
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if (!defined('CYBERXDC_VERSION')) {
    define('CYBERXDC_VERSION', '1.0.1');
}
if (!defined('CYBERXDC_PATH')) {
    define('CYBERXDC_PATH', plugin_dir_path(__FILE__));
}
if (!defined('CYBERXDC_AUTHOR_URI')) {
    define('CYBERXDC_AUTHOR_URI', 'http://localhost/cyberxdc');
}
define('CYBERXDC_PLUGIN_URL', 'https://cyberxdc.online');
if (!defined('CYBERXDC_PLUGIN_URL')) {
    define('CYBERXDC_PLUGIN_URL', 'https://cyberxdc.online');
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cyberxdc-activator.php
 */
function activate_cyberxdc()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-cyberxdc-activator.php';
    Cyberxdc_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cyberxdc-deactivator.php
 */
function deactivate_cyberxdc()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-cyberxdc-deactivator.php';
    Cyberxdc_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_cyberxdc');
register_deactivation_hook(__FILE__, 'deactivate_cyberxdc');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-cyberxdc.php';




/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cyberxdc()
{

    $plugin = new Cyberxdc();
    $plugin->run();
}
run_cyberxdc();

// cyberxdc.php

require_once plugin_dir_path(__FILE__) . 'admin/class-cyberxdc-admin.php';
require_once plugin_dir_path(__FILE__) . 'admin/partials/cyberxdc-admin-menus.php';
require_once plugin_dir_path(__FILE__) . 'admin/partials/cyberxdc-admin-display.php';
require_once plugin_dir_path(__FILE__) . 'admin/pages/cyberxdc-updates-page.php';
require_once plugin_dir_path(__FILE__) . 'admin/pages/cyberxdc-settings-page.php';
require_once plugin_dir_path(__FILE__) . 'admin/pages/cyberxdc-logs-and-activity.php';
require_once plugin_dir_path(__FILE__) . 'admin/pages/cyberxdc-shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'admin/pages/cyberxdc-settings-smtp-page.php';
require_once plugin_dir_path(__FILE__) . 'admin/pages/cyberxdc-security-page.php';
require_once plugin_dir_path(__FILE__) . 'admin/pages/cyberxdc-cf7db-page.php';
require_once plugin_dir_path(__FILE__) . 'admin/pages/cyberxdc-customization-page.php';

// Initialize admin menus functionality
function run_cyberxdc_admin_menus()
{
    $admin_menus = new Cyberxdc_Admin_Menus();
}
run_cyberxdc_admin_menus();

// Function to Check and Create Table if it Doesn't Exist
function cyberxdc_check_and_create_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'cyberxdc_users_logs';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            user varchar(50) NOT NULL,
            activity text NOT NULL,
            ip_address varchar(100) NOT NULL,
            location varchar(100) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . 'cyberxdc_cf7db';
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();

        $sql_cf7db = "CREATE TABLE $table_name (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    form_id mediumint(9) NOT NULL,
                    submission_data text NOT NULL,
                    submission_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                    PRIMARY KEY  (id)
            ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_cf7db);
    }
}
add_action('init', 'cyberxdc_check_and_create_table');

function add_custom_css_to_admin_head()
{
?>
    <style type="text/css">
        .cyberxdc-admin #wpcontents {
            background-color: white !important;
        }
    </style>
<?php
}
add_action('admin_head', 'add_custom_css_to_admin_head');

function add_custom_class_to_admin_body($classes)
{
    // Check if the current URL contains "cyberxdc"
    if (strpos($_SERVER['REQUEST_URI'], 'cyberxdc') !== false) {
        $classes .= ' cyberxdc-admin';
    }
    return $classes;
}
add_filter('admin_body_class', 'add_custom_class_to_admin_body');


add_action('admin_enqueue_scripts', function () {
    if (is_admin())
        wp_enqueue_media();
});

// Hook into the init action to track visitor logs
add_action('init', 'cyberxdc_track_visitor_logs');

function cyberxdc_track_visitor_logs()
{
    // Do not track admin users
    if (is_user_logged_in() && current_user_can('manage_options')) {
        return;
    }

    // Get visitor IPs
    $ip4 = $_SERVER['REMOTE_ADDR'] ?? '';
    $ip6 = $_SERVER['HTTP_CLIENT_IP'] ?? '';

    // Get country (using a third-party service like ipinfo.io)
    $country = 'Unknown';
    if ($ip4 && filter_var($ip4, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        $response = wp_remote_get("http://ipinfo.io/{$ip4}/country");
        if (is_array($response) && !is_wp_error($response)) {
            $country_data = json_decode(wp_remote_retrieve_body($response), true);
            if (isset($country_data['country'])) {
                $country = trim($country_data['country']);
                if (strlen($country) > 255) {
                    $country = substr($country, 0, 255); // Truncate if too long
                }
            } else {
                error_log('Error fetching country info: ' . print_r($response, true));
            }
        } else {
            error_log('Error fetching country info: ' . print_r($response, true));
        }
    }

    // Get browser and device
    $browser = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $device = 'Unknown Device';

    // Use a library like Mobile Detect to determine the device type
    require_once 'includes/Mobile_Detect.php'; // Include the Mobile Detect library
    $detect = new Mobile_Detect;
    if ($detect->isMobile()) {
        $device = 'Mobile';
    } elseif ($detect->isTablet()) {
        $device = 'Tablet';
    } else {
        $device = 'Desktop';
    }

    // Get current time
    $time = current_time('mysql');

    // Get current page URL
    $page_visited = $_SERVER['REQUEST_URI'];


    // Store the data
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberxdc_visitor_logs';
    $result = $wpdb->insert($table_name, [
        'ip4' => $ip4,
        'ip6' => $ip6,
        'country' => $country,
        'browser' => $browser,
        'device' => $device,
        'time' => $time,
        'page_visited' => $page_visited,
    ]);

    // Check for errors

    if ($result === false) {
        error_log('Error inserting visitor log: ' . $wpdb->last_error);
    }
}


// Schedule a daily event to validate the license
function cyberxdc_schedule_daily_license_validation()
{
    if (!wp_next_scheduled('cyberxdc_validate_license_event')) {
        wp_schedule_event(time(), 'daily', 'cyberxdc_validate_license_event');
    }
}
add_action('wp', 'cyberxdc_schedule_daily_license_validation');


// validate license on wp login
add_action('wp_login', 'cyberxdc_validate_license');

function cyberxdc_validate_license() {
    $stored_license_key = get_option('cyberxdc_license_key');
    
    if (!empty($stored_license_key)) {
        $validation_api_url = CYBERXDC_PLUGIN_URL . '/licenses/validate';

        $validation_data = array(
            'license_key' => $stored_license_key,
            'domain' => home_url(),
        );

        $validation_response = wp_remote_post($validation_api_url, array(
            'body' => $validation_data,
        ));

        if (is_wp_error($validation_response)) {
            error_log('License validation error: ' . $validation_response->get_error_message());
            return;
        }

        $response_body = wp_remote_retrieve_body($validation_response);
        $response_data = json_decode($response_body, true);
        error_log('License validation response: ' . print_r($response_data, true));

        if (isset($response_data['status']) && $response_data['status'] === 'active') {
            // License is valid
            update_option('cyberxdc_license_status', 'active');
            // Delete scheduled deletion event if it exists
            if (wp_next_scheduled('cyberxdc_delete_plugin_event')) {
                wp_clear_scheduled_hook('cyberxdc_delete_plugin_event');
            }
        }elseif (isset($response_data['status']) && $response_data['status'] === 'invalid') {
            // License is not valid
            error_log('License validation failed: ' . print_r($response_data, true));
            update_option('cyberxdc_license_status', 'invalid');

            // Schedule deletion after 30 days if not already scheduled
            if (!wp_next_scheduled('cyberxdc_delete_plugin_event')) {
                wp_schedule_single_event(time() + 30 * DAY_IN_SECONDS, 'cyberxdc_delete_plugin_event');
                // Update or add option cyberxdc_license_validation_failed_date
                $failed_date = get_option('cyberxdc_license_validation_failed_date');
                if (!$failed_date) {
                    add_option('cyberxdc_license_validation_failed_date', current_time('timestamp'));
                } else {
                    update_option('cyberxdc_license_validation_failed_date', current_time('timestamp'));
                }
            }
        }elseif (isset($response_data['status']) && $response_data['status'] === 'inactive') {
            error_log('Error validating license: ' . print_r($response_data, true));
            update_option('cyberxdc_license_status', 'inactive');
        }
    } else {
        error_log('No license key found');
        update_option('cyberxdc_license_status', 'invalid');
        // Schedule deletion after 30 days if not already scheduled
        if (!wp_next_scheduled('cyberxdc_delete_plugin_event')) {
            wp_schedule_single_event(time() + 30 * DAY_IN_SECONDS, 'cyberxdc_delete_plugin_event');
            // Update or add option cyberxdc_license_validation_failed_date
            $failed_date = get_option('cyberxdc_license_validation_failed_date');
            if (!$failed_date) {
                add_option('cyberxdc_license_validation_failed_date', current_time('timestamp'));
            } else {
                update_option('cyberxdc_license_validation_failed_date', current_time('timestamp'));
            }
        }
    }
}

add_action('cyberxdc_validate_license_event', 'cyberxdc_validate_license');
add_action('cyberxdc_delete_plugin_event', 'cyberxdc_delete_plugin_directory');

function cyberxdc_delete_plugin_directory()
{
    deactivate_plugins(plugin_basename(__FILE__));
    delete_option('cyberxdc_license_key');
    delete_option('cyberxdc_license_status');
    $plugin_path = plugin_dir_path(__FILE__);
    if (file_exists($plugin_path)) {
        $deleted = cyberxdc_recursive_remove_directory($plugin_path);
        if ($deleted) {
            error_log('Plugin directory deleted successfully.');
        }
    }
}
function cyberxdc_recursive_remove_directory($dir)
{
    if (!is_dir($dir)) {
        return false;
    }
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? cyberxdc_recursive_remove_directory("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

// Add the "Settings" link to the plugin's action links in plugins.php
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'cyberxdc_add_settings_and_update_links', 10, 2);

// Enqueue scripts and styles
add_action('admin_enqueue_scripts', 'cyberxdc_enqueue_thickbox');

function cyberxdc_enqueue_thickbox($hook_suffix) {
    // Only enqueue scripts on plugin admin pages where Thickbox is used
    if ($hook_suffix === 'plugins.php' || $hook_suffix === 'plugin-install.php') {
        wp_enqueue_script('jquery');
        wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');
        
        // Inline script for Thickbox initialization
        wp_add_inline_script('jquery', 'jQuery(document).ready(function($){
            $(document).on("click", ".cyberxdc-thickbox", function(e) {
                e.preventDefault();
                var url = $(this).attr("href");
                tb_show("CyberXDC Readme Contents", url + "&TB_iframe=true&width=600&height=800");
            });
        });');
    }
}

// Function to add settings and update links
function cyberxdc_add_settings_and_update_links($links, $file) {
    // URL to the plugin's settings page
    $settings_url = admin_url('options-general.php?page=cyberxdc');

    // Check for updates and add the update notice if applicable
    $update_info = cyberxdc_compare_versions();
    if ($update_info['has_update']) {
        // Construct the update link with a nonce for security
        $update_url = wp_nonce_url(admin_url('plugins.php?action=cyberxdc_update_plugin'), 'cyberxdc_update_nonce');

        // Construct the update notice message with Thickbox modal for readme.txt contents
        $update_message = sprintf(
            '<div class="update-message notice inline notice-warning notice-alt" style="margin-top: 10px;">
                <p>There is a new version of CyberXDC available. 
                    <a href="%1$s" class="cyberxdc-thickbox" aria-label="View CyberXDC version %2$s details">View version %2$s details</a> 
                    or <a href="%3$s" class="update-link" aria-label="Update CyberXDC now">Update now</a>.
                </p>
            </div>',
            admin_url('admin-ajax.php?action=cyberxdc_get_readme_contents&version=' . urlencode($update_info['latest_version'])),
            esc_html($update_info['latest_version']),
            esc_url($update_url)
        );

        // Add the update message to the existing action links array
        $links[] = $update_message;
    }

    return $links;
}


// Ajax handler to fetch readme contents
add_action('wp_ajax_cyberxdc_get_readme_contents', 'cyberxdc_ajax_get_readme_contents');

function cyberxdc_ajax_get_readme_contents() {
    $version = isset($_GET['version']) ? sanitize_text_field($_GET['version']) : '';
    
    if ($version) {
        // Replace PLUGIN_URL with your actual plugin URL
        $plugin_url = CYBERXDC_PLUGIN_URL;

        $readme_contents = 
        "
        <h2>CyberXDC Readme Contents</h1>
        <p>The readme.txt file is not available for this version. Please update to the latest version.</p>
        <p>Version: $version</p>
        <p>URL: <a href='https://wordpress.org/plugins/wpp-cyberxdc-main/'>https://wordpress.org/plugins/wpp-cyberxdc-main/</a></p>
        <p>Readme: <a href='$plugin_url/readme.md'>$plugin_url/readme.md</a></p>
        <p>Support: <a href='https://wordpress.org/support/plugin/wpp-cyberxdc-main'>https://wordpress.org/support/plugin/wpp-cyberxdc-main</a></p>
        <p>Changelog: <a href='$plugin_url/CHANGELOG.md'>$plugin_url/CHANGELOG.md</a></p>
        <p>Documentation: <a href='$plugin_url/docs/README.md'>$plugin_url/docs/README.md</a></p>
        ";
        wp_die($readme_contents);
    } else {
        wp_die();
    }
}


function cyberxdc_get_readme_contents() {
    $readme_file = plugin_dir_path(__FILE__) . 'readme.md'; // Adjust path to your plugin's readme.txt
    $readme_contents = '';

    // Check if readme.txt exists and readable
    if (file_exists($readme_file) && is_readable($readme_file)) {
        // Read contents of readme.txt
        $readme_contents = file_get_contents($readme_file);
        // Sanitize the contents for safe display
        $readme_contents = esc_html($readme_contents);
        error_log('Readme contents: ' . $readme_contents);
    } else {
        // Error handling if readme.txt is not accessible
        $readme_contents = 'Error: Readme file not found or inaccessible.';
    }

    return $readme_contents;
}




// Hook into the admin_init action to handle the update process
add_action('admin_init', 'cyberxdc_handle_plugin_update');

function cyberxdc_handle_plugin_update()
{
    // Check if the action and nonce are set and valid
    if (isset($_GET['action']) && $_GET['action'] === 'cyberxdc_update_plugin' && check_admin_referer('cyberxdc_update_nonce')) {
        // Check user capabilities
        if (!current_user_can('update_plugins')) {
            wp_die('You do not have sufficient permissions to update plugins for this site.');
        }

        // Perform the update
        $update_result = cyberxdc_custom_update_functionality();

        // Redirect back to the plugins page with a message
        if ($update_result === true) {
            add_action('admin_notices', 'cyberxdc_update_success_notice');
            wp_redirect(admin_url('plugins.php?cyberxdc_update=success'));
            exit;
        } else {
            add_action('admin_notices', 'cyberxdc_update_failed_notice');
            wp_redirect(admin_url('plugins.php?cyberxdc_update=failed'));
            exit;
        }
    }
}
