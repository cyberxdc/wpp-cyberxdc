<?php
function cyberxdc_generate_license_page() {
    // Initialize notice variable
    $notice = '';

    // Check if the license key generation form was submitted
    if (isset($_POST['generate_license'])) {
        // Verify nonce for security
        check_admin_referer('cyberxdc_generate_license_nonce');
        
        // API endpoint to generate the license key
        $api_url = CYBERXDC_PLUGIN_URL . '/licenses/generate';

        // Prepare the data to send to the API
        $post_data = array(
            'domain' => get_bloginfo('siteurl'),
        );

        // Send POST request to the API to generate the license key
        $response = wp_remote_post($api_url, array(
            'body' => $post_data,
        ));

        // Check for errors in the API request
        if (is_wp_error($response)) {
            $notice = '<div class="notice notice-error is-dismissible"><p>Error generating license key: ' . $response->get_error_message() . '</p></div>';
        } else {
            // Decode the response
            $response_body = wp_remote_retrieve_body($response);
            $response_data = json_decode($response_body, true);

            // Check if the API returned a license key
            if (isset($response_data['license_key']) && !empty($response_data['license_key'])) {
                $license_key = $response_data['license_key'];
                // Save the generated key to the options table
                update_option('cyberxdc_license_key', $license_key);
                update_option('cyberxdc_license_status', 'inactive');

                // Show success message
                $notice = '<div class="notice notice-success is-dismissible"><p>License key generated successfully. Please activate your license.</p></div>';
            } else {
                // Show error message if the license key is not found in the response
                $notice = '<div class="notice notice-error is-dismissible"><p>Error generating license key: License key not found in response.</p></div>';
            }
        }
    }

    // Retrieve the saved license key
    $license_key = get_option('cyberxdc_license_key');

    // Check if the license activation form was submitted
    if (isset($_POST['activate_license'])) {
        // Verify nonce for security
        check_admin_referer('cyberxdc_activate_license_nonce');

        // API endpoint for license activation
        $activation_api_url = CYBERXDC_PLUGIN_URL . '/licenses/activate';

        // Prepare data for the activation request
        $activation_data = array(
            'license_key' => $license_key,
            'domain' => get_bloginfo('siteurl'),
            'admin_email' => get_option('admin_email'),
            'server_ip' => $_SERVER['SERVER_ADDR'],
            'user_ip' => $_SERVER['REMOTE_ADDR'],
        );

        // Send activation request
        $activation_response = wp_remote_post($activation_api_url, array(
            'body' => $activation_data,
        ));

        // Check for errors in the activation request
        if (is_wp_error($activation_response)) {
            $notice = '<div class="notice notice-error is-dismissible"><p>Error activating license: ' . $activation_response->get_error_message() . '</p></div>';
        } else {
            $response_code = wp_remote_retrieve_response_code($activation_response);
            $response_body = wp_remote_retrieve_body($activation_response);
            $response_data = json_decode($response_body, true);
            if ($response_code == 200) {
                // Assuming HTTP status 200 indicates success
                update_option('cyberxdc_license_status', 'active');
                $notice = '<div class="notice notice-success is-dismissible"><p>License activated successfully.</p></div>';
            } else {
                // Handle other status codes or error responses
                $error_message = isset($response_data['message']) ? $response_data['message'] : 'Unknown error.';
                $notice = '<div class="notice notice-error is-dismissible"><p>Error activating license: ' . $error_message . '</p></div>';
            }
        }
    }

    $important_features = array(
        'Login Page Customization' => 'admin.php?page=cyberxdc-customization',
        'Header and Footer Scripts' => 'admin.php?page=cyberxdc-customization&tab=custom_style',
        'Two Factor Authentication' => 'admin.php?page=cyberxdc-security&tab=two_factor',
        'Firewall Rules' => 'admin.php?page=cyberxdc-security&tab=firewalls',
        'Database Security' => 'admin.php?page=cyberxdc-security&tab=database_security',
        'Contact Form 7 Database' => 'admin.php?page=cyberxdc-cf7-submissions',
        'Logs and Activities' => 'admin.php?page=cyberxdc-logs',
    );

    ?>

    <div class="cyberxdc-wrap">
        <div class="container">
            <?php if (get_option('cyberxdc_license_key') === '') : ?>
                <div class="card">
                    <h2>Generate License Key</h2>
                    <?php echo $notice; ?>
                    <form method="post">
                        <?php wp_nonce_field('cyberxdc_generate_license_nonce'); ?>
                        <input type="submit" class="button button-primary" name="generate_license" value="Generate License Key">
                    </form>
                </div>
            <?php elseif (get_option('cyberxdc_license_status') != 'active') : ?>
                <div class="card">
                    <h2>Activate License Key</h2>
                    <?php echo $notice; ?>
                    <p>License Key: <strong><?php echo esc_html($license_key); ?></strong></p>
                    <form method="post">
                        <?php wp_nonce_field('cyberxdc_activate_license_nonce'); ?>
                        <input type="submit" class="button button-primary" name="activate_license" value="Activate License">
                    </form>
                </div>
            <?php else : ?>
                <div class="card">
                    <h2>License Status</h2>
                    <?php echo $notice; ?>
                    <p>License Key: <strong><?php echo esc_html($license_key); ?></strong></p>
                    <p>License Status: <strong><?php echo esc_html(get_option('cyberxdc_license_status')); ?></strong></p>
                </div>
            <?php endif; ?>
            <div style="width: 100%; max-width: 75%;" class="card">
                <h2>Importants Features</h2>
                <ul style="display: flex; flex-wrap: wrap;" >
                    <?php foreach ($important_features as $feature => $link) : ?>
                        <li style="margin: 4px;" ><a href="<?php echo $link; ?>" class="button"><?php echo $feature; ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php
}
?>
