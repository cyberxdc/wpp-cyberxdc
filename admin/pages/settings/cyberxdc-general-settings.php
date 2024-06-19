<?php
function cyberxdc_general_settings()
{
    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Update maintenance mode setting
        update_option('cyberxdc_maintenance_mode', isset($_POST['cyberxdc_maintenance_mode']) ? '1' : '0');

        // Update auto-update settings
        update_option('cyberxdc_auto_update_core', isset($_POST['cyberxdc_auto_update_core']) ? '1' : '0');
        update_option('cyberxdc_auto_update_plugins', isset($_POST['cyberxdc_auto_update_plugins']) ? '1' : '0');
        update_option('cyberxdc_auto_update_themes', isset($_POST['cyberxdc_auto_update_themes']) ? '1' : '0');

        // Update excluded premium plugins
        $excluded_plugins = isset($_POST['cyberxdc_exclude_premium_plugins']) ? $_POST['cyberxdc_exclude_premium_plugins'] : '';
        update_option('cyberxdc_exclude_premium_plugins', $excluded_plugins);

        // Update excluded premium themes
        $excluded_themes = isset($_POST['cyberxdc_exclude_premium_themes']) ? $_POST['cyberxdc_exclude_premium_themes'] : '';
        update_option('cyberxdc_exclude_premium_themes', $excluded_themes);

        // debug ip
        update_option('cyberxdc_debug_ip', sanitize_text_field($_POST['cyberxdc_debug_ip']));


        // Display a notice
        $notice = 'Settings saved successfully.';
    }

    // Get current settings
    $maintenance_mode = get_option('cyberxdc_maintenance_mode', '0');
    $auto_update_core = get_option('cyberxdc_auto_update_core', '0');
    $auto_update_plugins = get_option('cyberxdc_auto_update_plugins', '0');
    $auto_update_themes = get_option('cyberxdc_auto_update_themes', '0');
    $excluded_plugins = get_option('cyberxdc_exclude_premium_plugins', '');
    $excluded_themes = get_option('cyberxdc_exclude_premium_themes', '');

?>
    <div class="container">
        <div style="max-width: 100%; width: 100%;" class="card">
            <h3>CyberXDC General Settings</h3>
            <?php if (!empty($notice)) : ?>
                <div style="margin: 0px;" class="notice notice-success is-dismissible">
                    <p><?php echo $notice; ?></p>
                </div>
            <?php endif; ?>
            <hr>
            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label class="label" for="cyberxdc_maintenance_mode">Maintenance Mode:</label></th>
                        <td><input class="checkbox" type="checkbox" id="cyberxdc_maintenance_mode" name="cyberxdc_maintenance_mode" value="1" <?php checked($maintenance_mode, '1'); ?>></td>
                    </tr>
                    <tr>
                        <th scope="row"><label class="label" for="cyberxdc_auto_update_core">Enable Auto WP Core Update:</label></th>
                        <td><input class="checkbox" type="checkbox" id="cyberxdc_auto_update_core" name="cyberxdc_auto_update_core" value="1" <?php checked($auto_update_core, '1'); ?>></td>
                    </tr>
                    <tr>
                        <th scope="row"><label class="label" for="cyberxdc_auto_update_plugins">Enable Auto Plugin Update:</label></th>
                        <td><input class="checkbox" type="checkbox" id="cyberxdc_auto_update_plugins" name="cyberxdc_auto_update_plugins" value="1" <?php checked($auto_update_plugins, '1'); ?>></td>
                    </tr>
                    <tr>
                        <th scope="row"><label class="label" for="cyberxdc_exclude_premium_plugins">Exclude Premium Plugins (Comma separated list of plugin slugs):</label></th>
                        <td><input type="text" id="cyberxdc_exclude_premium_plugins" name="cyberxdc_exclude_premium_plugins" value="<?php echo esc_attr($excluded_plugins); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label class="label" for="cyberxdc_auto_update_themes">Enable Auto Themes Update:</label></th>
                        <td><input class="checkbox" type="checkbox" id="cyberxdc_auto_update_themes" name="cyberxdc_auto_update_themes" value="1" <?php checked($auto_update_themes, '1'); ?>></td>
                    </tr>
                    <tr>
                        <th scope="row"><label class="label" for="cyberxdc_exclude_premium_themes">Exclude Premium Themes (Comma separated list of theme slugs):</label></th>
                        <td><input type="text" id="cyberxdc_exclude_premium_themes" name="cyberxdc_exclude_premium_themes" value="<?php echo esc_attr($excluded_themes); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label class="label" for="cyberxdc_debug_ip">Enable Debug Mode for Your IP:</label></th>
                        <td><input type="text" id="cyberxdc_debug_ip" name="cyberxdc_debug_ip" value="<?php echo esc_attr(get_option('cyberxdc_debug_ip', '')); ?>" placeholder="Enter your IP address"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="submit">Save Settings</label></th>
                        <td><input class="button" type="submit" class="button-primary" value="Save Changes"></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
    <style>
        .form-table th {
            width: 420px;
            max-width: 420px;
        }
    </style>
    <?php
}

// Hook to enable maintenance mode if the setting is enabled
function cyberxdc_enable_maintenance_mode()
{
    // Get the maintenance mode option
    $maintenance_mode = get_option('cyberxdc_maintenance_mode', '0');

    // Check if maintenance mode is enabled
    if ($maintenance_mode === '1') {
        // Display maintenance message
    ?>
        <div style="max-width: 600px; margin: 50px auto; padding: 40px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); text-align: center;">
            <h1 style="color: #333333; font-size: 32px; margin-bottom: 20px;">CyberXDC</h1>
            <h2 style="color: #333333; font-size: 24px; margin-bottom: 10px;">Site Maintenance</h2>
            <p style="color: #666666; font-size: 16px;">This site is currently undergoing maintenance. Please check back later.</p>
        </div>
<?php
        // Prevent further execution of WordPress
        exit;
    }
}
add_action('template_redirect', 'cyberxdc_enable_maintenance_mode');

// Hook to enable auto-updates
define('WP_AUTO_UPDATE_CORE', true);
add_filter('auto_update_plugin', '__return_true');
add_filter('auto_update_theme', '__return_true');

// Exclude premium themes from auto-updates
add_filter('auto_update_theme', 'cyberxdc_exclude_premium_themes');
function cyberxdc_exclude_premium_themes($update)
{
    try {
        $excluded_themes = get_option('cyberxdc_exclude_premium_themes', '');
        $premium_themes = array_map('trim', explode(',', $excluded_themes));

        if (isset($update->theme) && in_array($update->theme, $premium_themes)) {
            return false;
        }
        return $update;
    } catch (Exception $e) {
        error_log('Error: ' . $e->getMessage());
    }
}

// Exclude premium plugins from auto-updates
add_filter('auto_update_plugin', 'cyberxdc_exclude_premium_plugins', 10, 2);
function cyberxdc_exclude_premium_plugins($update, $item)
{
    try {
        $excluded_plugins = get_option('cyberxdc_exclude_premium_plugins', '');
        $premium_plugins = array_map('trim', explode(',', $excluded_plugins));

        if (isset($item->plugin) && array_intersect($premium_plugins, explode('/', $item->plugin))) {
            return false;
        }
        return $update;
    } catch (Exception $e) {
        error_log('Error: ' . $e->getMessage());
    }
}
