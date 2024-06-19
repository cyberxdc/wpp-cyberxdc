<?php

function cyberxdc_two_factor_page()
{
    // Check if the form has been submitted
    if (isset($_POST['cyberxdc_two_factor_settings_submit'])) {
        // Verify nonce for security
        if (!isset($_POST['cyberxdc_two_factor_settings_nonce']) || !wp_verify_nonce($_POST['cyberxdc_two_factor_settings_nonce'], 'cyberxdc_two_factor_settings_nonce')) {
            wp_die('Nonce verification failed');
        }

        // Sanitize and update settings for 2FA
        $enable_two_factor = isset($_POST['enable_two_factor']) ? '1' : '0';

        update_option('cyberxdc_enable_two_factor', $enable_two_factor);

        // Display a notice message
        $notice = 'Two-factor authentication settings saved successfully.';
    }

    // Retrieve current settings for 2FA
    $enable_two_factor = get_option('cyberxdc_enable_two_factor', '0');

?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style="width: 100%; max-width: 100%; " class="card">
                <h2>Two-Factor Authentication Settings</h2>
                <?php if (!empty($notice)) : ?>
                    <div style="margin: 0px;" class="notice notice-success is-dismissible">
                        <p><?php echo $notice; ?></p>
                    </div>
                <?php endif; ?>
                <form method="post" action="">
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Enable Two-Factor Authentication</th>
                            <td>
                                <input type="checkbox" name="enable_two_factor" <?php checked('1', $enable_two_factor); ?> />
                                <p class="description">Enable or disable two-factor authentication.</p>
                            </td>
                        </tr>
                        <?php cyberxdc_display_qr_code(); ?>
                    </table>
                    <?php wp_nonce_field('cyberxdc_two_factor_settings_nonce', 'cyberxdc_two_factor_settings_nonce'); ?>
                    <input type="submit" name="cyberxdc_two_factor_settings_submit" class="button-primary" value="Save Changes">
                </form>
            </div>
        </div>
    </div>

<?php
}




function cyberxdc_display_qr_code()
{
    if (get_option('cyberxdc_enable_two_factor', '0') === '1') {
        $ga = new PHPGangsta_GoogleAuthenticator();
        $secret = get_option('cyberxdc_ga_secret');

        // If the secret is not already generated, generate it
        if (!$secret) {
            $secret = $ga->createSecret();
            update_option('cyberxdc_ga_secret', $secret);
        }

        $qrCodeUrl = $ga->getQRCodeGoogleUrl('YourSite', $secret);
        echo '<tr valign="top">
                <th scope="row">Google Authenticator QR Code</th>
                <td>
                    <img src="' . esc_url($qrCodeUrl) . '" alt="QR Code" />
                    <p class="description">Scan this QR code with your Google Authenticator app.</p>
                </td>
              </tr>';
    }
}


function cyberxdc_verify_otp_on_login($user, $username, $password)
{
    if (get_option('cyberxdc_enable_two_factor', '0') === '1') {
        if (!isset($_POST['otp_code'])) {
            return new WP_Error('otp_required', __('An OTP is required to complete the login.'));
        }

        $otp = $_POST['otp_code'];
        $secret = get_option('cyberxdc_ga_secret');
        $ga = new PHPGangsta_GoogleAuthenticator();

        if (!$ga->verifyCode($secret, $otp, 2)) { // 2 = 2*30sec clock tolerance
            return new WP_Error('invalid_otp', __('Invalid OTP code.'));
        }
    }
    return $user;
}
add_filter('authenticate', 'cyberxdc_verify_otp_on_login', 30, 3);


function cyberxdc_add_otp_field()
{
    if (get_option('cyberxdc_enable_two_factor', '0') === '1') {
        echo '<p>
                <label for="otp_code">OTP Code<br />
                <input type="text" name="otp_code" id="otp_code" class="input" size="20" /></label>
              </p>';
    }
}
add_action('login_form', 'cyberxdc_add_otp_field');
