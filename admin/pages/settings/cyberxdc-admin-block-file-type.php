<?php
// Define the function to render the file upload settings page
function cyberxdc_block_file_type()
{
?>
    <div class="card">
        <div class="card-header">
            <h3 class="wp-heading-inline">Block File Upload Type</h3>
            <p>Enter file extensions below to block file upload in WordPress library</p>
        </div>
        <div class="card-body">
            <form action="options.php" method="post">
                <?php settings_fields('cyberxdc_file_upload_settings'); ?>
                <?php do_settings_sections('cyberxdc_file_upload_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="blocked_extensions">Blocked Extensions</label>
                        </th>
                        <td>
                            <input type="text" id="blocked_extensions" name="blocked_extensions" value="<?php echo esc_attr(get_option('blocked_extensions')); ?>" class="regular-text" placeholder=".exe, .bat, .php">
                            <p class="description">Enter comma-separated file extensions to block (e.g., .exe, .bat, .php).</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button('Save Settings'); ?>
            </form>
        </div>
    </div>
<?php
}

// Register settings and sections
function cyberxdc_file_upload_block_settings_init()
{
    register_setting('cyberxdc_file_upload_settings', 'blocked_extensions');
}
add_action('admin_init', 'cyberxdc_file_upload_block_settings_init');

function block_custom_upload_mimes($mimes)
{
    // Get blocked extensions from the options table
    $blocked_extensions = get_option('blocked_extensions');

    // Check if blocked_extensions is empty or not set
    if (!empty($blocked_extensions)) {
        // Split blocked extensions into an array
        $extensions = explode(',', $blocked_extensions);

        // Loop through each extension and unset its corresponding MIME type
        foreach ($extensions as $extension) {
            if (isset($mimes[$extension])) {
                unset($mimes[$extension]);
            }
        }
    }

    // Return the modified MIME types array
    return $mimes;
}
// Hook the function to modify upload MIME types
add_filter('upload_mimes', 'block_custom_upload_mimes');
