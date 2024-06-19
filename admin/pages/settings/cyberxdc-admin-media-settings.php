<?php

function cyberxdc_media_settings()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        update_option('cyberxdc_custom_image_sizes', isset($_POST['cyberxdc_custom_image_sizes']) ? '1' : '0');
        update_option('cyberxdc_jpeg_quality', isset($_POST['cyberxdc_jpeg_quality']) ? $_POST['cyberxdc_jpeg_quality'] : '82');
        update_option('cyberxdc_restrict_upload_types', isset($_POST['cyberxdc_restrict_upload_types']) ? '1' : '0');
        update_option('cyberxdc_disable_image_compression', isset($_POST['cyberxdc_disable_image_compression']) ? '1' : '0');

        // Display a notice
        $notice = 'Settings saved successfully.';
    }

    $custom_image_sizes = get_option('cyberxdc_custom_image_sizes', '0');
    $jpeg_quality = get_option('cyberxdc_jpeg_quality', '82');
    $restrict_upload_types = get_option('cyberxdc_restrict_upload_types', '0');
    $disable_image_compression = get_option('cyberxdc_disable_image_compression', '0');
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
                    <tr>
                        <th scope="row"><label for="cyberxdc_custom_image_sizes">Enable Custom Image Sizes:</label></th>
                        <td><input type="checkbox" id="cyberxdc_custom_image_sizes" name="cyberxdc_custom_image_sizes" value="1" <?php checked($custom_image_sizes, '1'); ?>></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cyberxdc_jpeg_quality">JPEG Quality (1-100):</label></th>
                        <td><input type="number" id="cyberxdc_jpeg_quality" name="cyberxdc_jpeg_quality" value="<?php echo esc_attr($jpeg_quality); ?>" min="1" max="100"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cyberxdc_restrict_upload_types">Restrict Upload Types:</label></th>
                        <td><input type="checkbox" id="cyberxdc_restrict_upload_types" name="cyberxdc_restrict_upload_types" value="1" <?php checked($restrict_upload_types, '1'); ?>></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cyberxdc_disable_image_compression">Disable Image Compression:</label></th>
                        <td><input type="checkbox" id="cyberxdc_disable_image_compression" name="cyberxdc_disable_image_compression" value="1" <?php checked($disable_image_compression, '1'); ?>></td>
                    </tr>
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
add_filter('jpeg_quality', function($arg) {
    return get_option('cyberxdc_jpeg_quality', 82);
});

// Restrict upload types
if (get_option('cyberxdc_restrict_upload_types', '0') === '1') {
    add_filter('upload_mimes', function($mimes) {
        return array(
            'jpg|jpeg|jpe' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        );
    });
}

// Disable image compression
if (get_option('cyberxdc_disable_image_compression', '0') === '1') {
    add_filter('wp_editor_set_quality', function($quality) {
        return 100;
    });
    add_filter('big_image_size_threshold', '__return_false');
}
?>
