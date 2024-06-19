<?php
function cyberxdc_files_security_page()
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if iFrame protection is enabled
        $iframe_protection = isset($_POST['iframe_protection']) ? 1 : 0;
        update_option('cyberxdc_iframe_protection', $iframe_protection);

        // Check if copy protection is enabled
        $copy_protection = isset($_POST['copy_protection']) ? 1 : 0;
        update_option('cyberxdc_copy_protection', $copy_protection);

        // Check if file installation is enabled
        $file_installation = isset($_POST['file_installation']) ? 1 : 0;
        update_option('cyberxdc_file_installation', $file_installation);

        // Check if files should be deleted after WP core update
        $delete_files_after_update = isset($_POST['delete_files_after_update']) ? 1 : 0;
        update_option('cyberxdc_delete_files_after_update', $delete_files_after_update);

        // Check if ability to edit PHP files should be disabled
        $disable_php_editing = isset($_POST['disable_php_editing']) ? 1 : 0;
        update_option('cyberxdc_disable_php_editing', $disable_php_editing);

        // Display success message
        $notice = 'Settings updated successfully.';
    }
?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style="max-width: 100%; min-width: 600px; width: 100%;" class="card">
                <h2>File Permissions</h2>
                <?php if (!empty($notice)) : ?>
                    <div class="notice notice-success is-dismissible ">
                        <p><?php echo $notice; ?></p>
                    </div>
                <?php endif; ?>
                <form method="post" action="">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Enable iFrame protection:</th>
                            <td><input type="checkbox" name="iframe_protection" <?php echo get_option('cyberxdc_iframe_protection') ? 'checked' : ''; ?>></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Enable copy protection:</th>
                            <td><input type="checkbox" name="copy_protection" <?php echo get_option('cyberxdc_copy_protection') ? 'checked' : ''; ?>></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Install files:</th>
                            <td><input type="checkbox" name="file_installation" <?php echo get_option('cyberxdc_file_installation') ? 'checked' : ''; ?>></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Delete readme.html and wp-config-sample.php:</th>
                            <td><input type="checkbox" name="delete_files_after_update" <?php echo get_option('cyberxdc_delete_files_after_update') ? 'checked' : ''; ?>></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Disable ability to edit PHP files:</th>
                            <td><input type="checkbox" name="disable_php_editing" <?php echo get_option('cyberxdc_disable_php_editing') ? 'checked' : ''; ?>></td>
                        </tr>
                    </table>
                    <?php wp_nonce_field('cyberxdc_file_permissions_nonce', 'cyberxdc_file_permissions_nonce'); ?>
                    <input type="submit" name="submit" class="button-primary" value="Save Changes">
                </form>
            </div>
        </div>
    </div>
<?php
}
// Enable iFrame protection
add_action('update_option_cyberxdc_iframe_protection', 'cyberxdc_enable_iframe_protection', 10, 2);

function cyberxdc_enable_iframe_protection($old_value, $new_value)
{
    if ($new_value) {
        // Enable iFrame protection logic here
        add_filter('wp_headers', 'cyberxdc_add_x_frame_options');
    } else {
        // Disable iFrame protection logic here
        remove_filter('wp_headers', 'cyberxdc_add_x_frame_options');
    }
}

function cyberxdc_add_x_frame_options($headers)
{
    $headers['X-Frame-Options'] = 'DENY';
    return $headers;
}

// Enable copy protection
add_action('update_option_cyberxdc_copy_protection', 'cyberxdc_enable_copy_protection', 10, 2);

function cyberxdc_enable_copy_protection($old_value, $new_value)
{
    if ($new_value) {
        // Enable copy protection logic here
        add_action('wp_enqueue_scripts', 'cyberxdc_enqueue_scripts');
    } else {
        // Disable copy protection logic here
        remove_action('wp_enqueue_scripts', 'cyberxdc_enqueue_scripts');
    }
}

function cyberxdc_enqueue_scripts()
{
    wp_enqueue_script('cyberxdc-copy-protection', plugin_dir_url(__FILE__) . 'js/copy-protection.js', array('jquery'), null, true);
}

// Install files
add_action('admin_init', 'cyberxdc_install_files');

function cyberxdc_install_files()
{
    $file_installation = get_option('cyberxdc_file_installation', 0);
    if ($file_installation) {
        // Install files logic here
        cyberxdc_install_files_function();
    }
}

function cyberxdc_install_files_function()
{
    // Logic to install files
}

// Delete readme.html and wp-config-sample.php after update
add_action('upgrader_process_complete', 'cyberxdc_delete_files_after_update', 10, 2);

function cyberxdc_delete_files_after_update($upgrader_object, $options)
{
    $delete_files_after_update = get_option('cyberxdc_delete_files_after_update', 0);
    if ($delete_files_after_update) {
        // Delete files logic here
        cyberxdc_delete_files_function();
    }
}

function cyberxdc_delete_files_function()
{
    // Logic to delete files
}

// Disable ability to edit PHP files
add_action('admin_init', 'cyberxdc_disable_php_editing');

function cyberxdc_disable_php_editing()
{
    $disable_php_editing = get_option('cyberxdc_disable_php_editing', 0);
    if ($disable_php_editing) {
        // Disable PHP editing logic here
        remove_submenu_page('themes.php', 'theme-editor.php');
    }
}
?>