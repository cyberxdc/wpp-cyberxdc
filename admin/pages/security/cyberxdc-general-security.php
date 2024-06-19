<?php
require_once plugin_dir_path( __FILE__ ) . '../../../includes/GoogleAuthenticator.php';

use PHPGangsta\GoogleAuthenticator;
function cyberxdc_general_security_page()
{
    // Check if the form has been submitted
    if (isset($_POST['cyberxdc_general_settings_submit'])) {
        // Verify nonce for security
        if (!isset($_POST['cyberxdc_general_settings_nonce']) || !wp_verify_nonce($_POST['cyberxdc_general_settings_nonce'], 'cyberxdc_general_settings_nonce')) {
            wp_die('Nonce verification failed');
        }

        // Sanitize and update settings
        $disable_file_editor = isset($_POST['disable_file_editor']) ? '1' : '0';
        $disable_xml_rpc = isset($_POST['disable_xml_rpc']) ? '1' : '0';

        update_option('cyberxdc_disable_file_editor', $disable_file_editor);
        update_option('cyberxdc_disable_xml_rpc', $disable_xml_rpc);

        // Sanitize and update settings
        $max_login_attempts = isset($_POST['max_login_attempts']) ? intval($_POST['max_login_attempts']) : 5;
        $lockout_duration = isset($_POST['lockout_duration']) ? intval($_POST['lockout_duration']) : 60;

        update_option('cyberxdc_max_login_attempts', $max_login_attempts);
        update_option('cyberxdc_lockout_duration', $lockout_duration);

        // Display a notice message
        $notice = 'Settings saved successfully.';
    }

    // Retrieve current settings
    $disable_file_editor = get_option('cyberxdc_disable_file_editor', '0');
    $disable_xml_rpc = get_option('cyberxdc_disable_xml_rpc', '0');
    $secret_key = get_option('cyberxdc_secret_key', '');
    // Retrieve current settings
    $max_login_attempts = get_option('cyberxdc_max_login_attempts', 5);
    $lockout_duration = get_option('cyberxdc_lockout_duration', 60);

?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style="width: 100%; max-width: 100%; " class="card">
                <h2>General Security Settings</h2>
                <?php if (!empty($notice)) : ?>
                    <div style="margin: 0px;" class="notice notice-success is-dismissible">
                        <p><?php echo $notice; ?></p>
                    </div>
                <?php endif; ?>
                <form method="post" action="">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Disable File Editor</th>
                            <td>
                                <input type="checkbox" name="disable_file_editor" <?php checked('1', $disable_file_editor); ?> />
                                <p class="description">Disable the theme and plugin file editor in the WordPress admin area.</p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Disable XML-RPC</th>
                            <td>
                                <input type="checkbox" name="disable_xml_rpc" <?php checked('1', $disable_xml_rpc); ?> />
                                <p class="description">Disable the XML-RPC feature for security reasons.</p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Max Login Attempts</th>
                            <td>
                                <input type="number" name="max_login_attempts" min="1" value="<?php echo esc_attr($max_login_attempts); ?>" />
                                <p class="description">Maximum number of login attempts allowed per day.</p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Lockout Duration (seconds)</th>
                            <td>
                                <input type="number" name="lockout_duration" min="1" value="<?php echo esc_attr($lockout_duration); ?>" />
                                <p class="description">Duration of lockout in seconds after reaching maximum login attempts.</p>
                            </td>
                        </tr>
                    </table>
                    <?php wp_nonce_field('cyberxdc_general_settings_nonce', 'cyberxdc_general_settings_nonce'); ?>
                    <input type="submit" name="cyberxdc_general_settings_submit" class="button-primary" value="Save Changes">
                </form>
            </div>
        </div>
    </div>
<?php
}

// Disable File Editor
if (get_option('cyberxdc_disable_file_editor', '0') === '1') {
    define('DISALLOW_FILE_EDIT', true);
}

// Disable XML-RPC
function cyberxdc_disable_xml_rpc($methods)
{
    if (get_option('cyberxdc_disable_xml_rpc', '0') === '1') {
        unset($methods['pingback.ping']);
        unset($methods['pingback.extensions.getPingbacks']);
    }
    return $methods;
}
add_filter('xmlrpc_methods', 'cyberxdc_disable_xml_rpc');

function cyberxdc_flush_rewrite_rules()
{
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'cyberxdc_flush_rewrite_rules');
register_deactivation_hook(__FILE__, 'cyberxdc_flush_rewrite_rules');

// Add this code to your theme's functions.php or a custom plugin

// Hook to track login attempts
add_action('wp_login_failed', 'cyberxdc_track_login_attempts');

function cyberxdc_track_login_attempts($username)
{
    $current_date = date('Y-m-d');
    $user_attempts_key = 'cyberxdc_login_attempts_' . $username . '_' . $current_date;
    $login_attempts = get_option($user_attempts_key, 0);
    $login_attempts++;
    update_option($user_attempts_key, $login_attempts);

    $max_login_attempts = get_option('cyberxdc_max_login_attempts', 5);
    if ($login_attempts >= $max_login_attempts) {
        cyberxdc_lockout_user($username);
    }
}

// Lockout user function
function cyberxdc_lockout_user($username)
{
    $lockout_duration = get_option('cyberxdc_lockout_duration', 3600); // Default to 1 hour
    $lockout_expiration = time() + $lockout_duration;
    update_option('cyberxdc_lockout_' . $username, $lockout_expiration);
}

// Hook to check if user is locked out
add_action('authenticate', 'cyberxdc_check_lockout_status', 30, 1);

function cyberxdc_check_lockout_status($user)
{
    if (isset($_POST['log'])) {
        $username = $_POST['log'];
        $lockout_expiration = get_option('cyberxdc_lockout_' . $username, 0);

        if (time() < $lockout_expiration) {
            $error_msg = sprintf(__('You have been locked out due to multiple failed login attempts. Please try again after %s minutes.', 'text-domain'), ceil(($lockout_expiration - time()) / 60));
            return new WP_Error('cyberxdc_lockout', $error_msg);
        }
    }
    return $user;
}

// Hook to reset login attempts on successful login
add_action('wp_login', 'cyberxdc_reset_login_attempts', 10, 2);

function cyberxdc_reset_login_attempts($username, $user)
{
    $current_date = date('Y-m-d');
    $user_attempts_key = 'cyberxdc_login_attempts_' . $username . '_' . $current_date;
    delete_option($user_attempts_key);
    delete_option('cyberxdc_lockout_' . $username);
}
