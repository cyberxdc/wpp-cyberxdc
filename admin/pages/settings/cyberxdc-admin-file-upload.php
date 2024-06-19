<?php
// Define the function to render the file upload settings page
function cyberxdc_file_upload_type()
{
?>
    <div class="card">
        <div class="card-header">
            <h3 class="wp-heading-inline">Allow File Upload Type</h3>
            <p>Enter file extensions below to allow file upload in WordPress library</p>
        </div>
        <div class="card-body">
            <form action="options.php" method="post">
                <?php settings_fields('cyberxdc_file_upload_settings'); ?>
                <?php do_settings_sections('cyberxdc_file_upload_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="allowed_extensions">Allowed Extensions</label>
                        </th>
                        <td>
                            <input type="text" id="allowed_extensions" name="allowed_extensions" value="<?php echo esc_attr(get_option('allowed_extensions')); ?>" class="regular-text" placeholder=".svg, .jpg, .png">
                            <p class="description">Enter comma-separated file extensions (e.g., .svg, .jpg, .png).</p>
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
function cyberxdc_file_upload_settings_init()
{
    register_setting('cyberxdc_file_upload_settings', 'allowed_extensions');

    // Check if user has submitted allowed extensions
    if (isset($_POST['allowed_extensions'])) {
        // Get user input and sanitize
        $allowed_extensions = sanitize_text_field($_POST['allowed_extensions']);

        // Update allowed MIME types based on user input
        $allowed_mimes = array();
        $extensions = explode(',', $allowed_extensions);
        foreach ($extensions as $extension) {
            $mime_type = wp_ext2type($extension);
            if ($mime_type) {
                $allowed_mimes[$extension] = $mime_type;
            }
        }

        // Update option with allowed MIME types
        update_option('upload_filetypes', implode(' ', array_values($allowed_mimes)));
    }
}
add_action('admin_init', 'cyberxdc_file_upload_settings_init');

function allow_custom_upload_mimes($mimes)
{
    // Check if $mimes is an array
    if (!is_array($mimes)) {
        // Log an error or handle it gracefully
        error_log('MIME types array is missing or invalid.');
        return $mimes; // Return original $mimes array
    }

    // Get allowed extensions from the options table
    $allowed_extensions = get_option('allowed_extensions');

    // Check if allowed_extensions is empty or not set
    if (empty($allowed_extensions)) {
        // Log an error or handle it gracefully
        error_log('Allowed extensions are missing or empty.');
        return $mimes; // Return original $mimes array
    }

    // Split allowed extensions into an array
    $extensions = explode(',', $allowed_extensions);

    // Loop through each extension and add its corresponding MIME type
    foreach ($extensions as $extension) {
        // Determine MIME type based on extension
        switch ($extension) {
            case 'exe':
                $mimes['exe'] = 'application/octet-stream';
                break;
            case 'bat':
                $mimes['bat'] = 'application/octet-stream';
                break;
            case 'svg':
                $mimes['svg'] = 'image/svg+xml';
                break;
            case 'jpg':
                $mimes['jpg'] = 'image/jpeg';
                break;
            case 'jpeg':
                $mimes['jpeg'] = 'image/jpeg';
                break;
            case 'png':
                $mimes['png'] = 'image/png';
                break;
            case 'gif':
                $mimes['gif'] = 'image/gif';
                break;
            case 'bmp':
                $mimes['bmp'] = 'image/bmp';
                break;
            case 'tiff':
                $mimes['tiff'] = 'image/tiff';
                break;
            case 'webp':
                $mimes['webp'] = 'image/webp';
                break;
            case 'avif':
                $mimes['avif'] = 'image/avif';
                break;
            default:
                // Log an error or handle unsupported extension gracefully
                error_log('Unsupported extension: ' . $extension);
                break;
        }
    }

    // Return the modified MIME types array
    return $mimes;
}

// Hook the function to modify upload MIME types
add_filter('upload_mimes', 'allow_custom_upload_mimes');
