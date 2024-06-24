<?php

function cyberxdc_media_settings()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        update_option('cyberxdc_custom_image_sizes', isset($_POST['cyberxdc_custom_image_sizes']) ? '1' : '0');
        update_option('cyberxdc_jpeg_quality', isset($_POST['cyberxdc_jpeg_quality']) ? $_POST['cyberxdc_jpeg_quality'] : '82');
        update_option('cyberxdc_restrict_upload_types', isset($_POST['cyberxdc_restrict_upload_types']) ? '1' : '0');
        update_option('cyberxdc_disable_image_compression', isset($_POST['cyberxdc_disable_image_compression']) ? '1' : '0');
        update_option('cyberxdc_generate_media_metadata', isset($_POST['cyberxdc_generate_media_metadata']) ? '1' : '0');

        // Display a notice
        $notice = 'Settings saved successfully.';
    }

    $custom_image_sizes = get_option('cyberxdc_custom_image_sizes', '0');
    $jpeg_quality = get_option('cyberxdc_jpeg_quality', '82');
    $restrict_upload_types = get_option('cyberxdc_restrict_upload_types', '0');
    $disable_image_compression = get_option('cyberxdc_disable_image_compression', '0');
    $generate_media_metadata = get_option('cyberxdc_generate_media_metadata', '0');
?>
    <div class="container">
        <div style="max-width: 100%; width: 100%;" class="card">
            <h3>CyberXDC Media Settings</h3>
            <?php if (!empty($notice)) : ?>
                <div style="margin: 0px;" class="notice notice-success is-dismissible">
                    <p><?php echo $notice; ?></p>
                </div>
            <?php endif; ?>
            <hr>
            <form method="post" action="">
                <table class="form-table">
                    <!-- Custom Image Sizes Option -->
                    <tr>
                        <th scope="row"><label for="cyberxdc_custom_image_sizes">Enable Custom Image Sizes:</label></th>
                        <td>
                            <input type="checkbox" id="cyberxdc_custom_image_sizes" name="cyberxdc_custom_image_sizes" value="1" <?php checked($custom_image_sizes, '1'); ?>>
                            <p class="description">
                                <strong>Note:</strong> Enable this option to add custom image sizes for your uploaded images. Custom sizes help in optimizing the display of images at different resolutions and contexts.
                            </p>
                        </td>
                    </tr>

                    <!-- JPEG Quality Option -->
                    <tr>
                        <th scope="row"><label for="cyberxdc_jpeg_quality">JPEG Quality (1-100):</label></th>
                        <td>
                            <input type="number" id="cyberxdc_jpeg_quality" name="cyberxdc_jpeg_quality" value="<?php echo esc_attr($jpeg_quality); ?>" min="1" max="100">
                            <p class="description">
                                <strong>Note:</strong> Set the quality of JPEG images on a scale of 1 to 100. Higher values result in better image quality but larger file sizes. The default WordPress quality is 82.
                            </p>
                        </td>
                    </tr>

                    <!-- Restrict Upload Types Option -->
                    <tr>
                        <th scope="row"><label for="cyberxdc_restrict_upload_types">Restrict Upload Types:</label></th>
                        <td>
                            <input type="checkbox" id="cyberxdc_restrict_upload_types" name="cyberxdc_restrict_upload_types" value="1" <?php checked($restrict_upload_types, '1'); ?>>
                            <p class="description">
                                <strong>Note:</strong> Enable this option to restrict the types of files that can be uploaded. This can enhance security by limiting uploads to safer file types like JPEG, PNG, and GIF.
                            </p>
                        </td>
                    </tr>

                    <!-- Disable Image Compression Option -->
                    <tr>
                        <th scope="row"><label for="cyberxdc_disable_image_compression">Disable Image Compression:</label></th>
                        <td>
                            <input type="checkbox" id="cyberxdc_disable_image_compression" name="cyberxdc_disable_image_compression" value="1" <?php checked($disable_image_compression, '1'); ?>>
                            <p class="description">
                                <strong>Note:</strong> Enable this option to prevent WordPress from compressing images during upload. This will keep your images at their original quality, but may increase file sizes.
                            </p>
                        </td>
                    </tr>

                    <!-- Generate Media Metadata Option -->
                    <tr valign="top">
                        <th scope="row">Generate Media Metadata</th>
                        <td>
                            <label for="cyberxdc_generate_media_metadata">
                                <input type="checkbox" id="cyberxdc_generate_media_metadata" name="cyberxdc_generate_media_metadata" value="1" <?php checked($generate_media_metadata, '1'); ?>>
                                Enable automatic generation of media metadata
                            </label>
                            <p class="description">
                                <strong>Note:</strong> This option will automatically generate metadata (title, descriptions, captions, and alt text) for your media files based on your website's name and slogan.
                            </p>
                        </td>
                    </tr>

                    <!-- Save Settings Button -->
                    <tr>
                        <th scope="row"><label for="submit">Save Settings</label></th>
                        <td><input class="button" type="submit" class="button-primary" value="Save Changes"></td>
                    </tr>
                </table>
            </form>
        </div>
    </div>
<?php
}

// Apply settings if custom image sizes is enabled
if (get_option('cyberxdc_custom_image_sizes', '0') === '1') {
    add_image_size('custom_size_1', 400, 400, true);
    add_image_size('custom_size_2', 800, 800, false);
}

// Set JPEG quality
add_filter('jpeg_quality', function ($arg) {
    return get_option('cyberxdc_jpeg_quality', 82);
});

// Restrict upload types
if (get_option('cyberxdc_restrict_upload_types', '0') === '1') {
    add_filter('upload_mimes', function ($mimes) {
        return array(
            'jpg|jpeg|jpe' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        );
    });
}

// Disable image compression
if (get_option('cyberxdc_disable_image_compression', '0') === '1') {
    add_filter('wp_editor_set_quality', function ($quality) {
        return 100;
    });
    add_filter('big_image_size_threshold', '__return_false');
}

// Generate media metadata
if (get_option('cyberxdc_generate_media_metadata', '0') === '1') {
    add_action('admin_init', 'update_existing_media_metadata');
}
function update_existing_media_metadata()
{

    $attachments = get_posts(array(
        'post_type' => 'attachment',
        'numberposts' => -1,
    ));

    foreach ($attachments as $attachment) {
        if (strpos($attachment->post_mime_type, 'image') !== false) {
            $original_title = $attachment->post_title;
            $formatted_title = ucwords(str_replace('-', ' ', $original_title));
            update_post_meta($attachment->ID, '_wp_attachment_image_alt', $formatted_title);
            update_post_meta($attachment->ID, '_wp_attachment_metadata', array(
                'image_meta' => array(
                    'caption' => $formatted_title,
                    'title' => $formatted_title,
                    'description' => $formatted_title,
                ),
            ));
        }
    }
}

?>
