<?php
add_action('phpmailer_init', 'cyberxdc_phpmailer_init');
function cyberxdc_phpmailer_init($phpmailer)
{
    $smtp_settings = get_option('cyberxdc_smtp_settings');

    if (!empty($smtp_settings['host']) && !empty($smtp_settings['username']) && !empty($smtp_settings['password'])) {
        $phpmailer->isSMTP();
        $phpmailer->Host = $smtp_settings['host'];
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = $smtp_settings['port'];
        $phpmailer->Username = $smtp_settings['username'];
        $phpmailer->Password = $smtp_settings['password'];
        $phpmailer->SMTPSecure = $smtp_settings['encryption'];

        $phpmailer->From = $smtp_settings['from_email'];
        $phpmailer->FromName = $smtp_settings['from_name'];
    }
}
function cyberxdc_smtp_page()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['smtp_settings'])) {
            update_option('cyberxdc_smtp_settings', $_POST['smtp_settings']);
            echo '<div class="updated"><p>SMTP settings saved.</p></div>';
        } elseif (isset($_POST['to_email'])) {
            // Sanitize input values
            $from_email = sanitize_email($_POST['from_email']);
            $to_email = sanitize_email($_POST['to_email']);
            $subject = 'SMTP Test Email';
            $message = 'This is a test email to verify SMTP settings.';
            $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . $from_email);
            // Send test email
            $result = wp_mail($to_email, $subject, $message, $headers);
            // Display result message
            if ($result) {
                echo '<div class="updated"><p>Test email sent successfully.</p></div>';
            } else {
                echo '<div class="error"><p>Failed to send test email. Please check your SMTP settings.</p></div>';
            }
        }
    }

    $smtp_settings = get_option('cyberxdc_smtp_settings', array(
        'host' => '',
        'port' => '',
        'username' => '',
        'password' => '',
        'encryption' => '',
        'from_email' => '',
        'from_name' => ''
    ));

    $smtp_settings = get_option('cyberxdc_smtp_settings');
?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style=" max-width: 100%;" class="cyberxdc-header card">
                <h1>CyberXDC SMTP Settings</h1>
                <p>Welcome to the CyberXDC SMTP Settings page!</p>
            </div>
            <div style="display: flex;" class="row">
                <div class="card col-md-6">
                    <div class="">
                        <div class="card-header">
                            <h2>SMTP Configuration</h2>
                            <p>Enter your SMTP settings.</p>
                        </div>
                        <div class="card-body">
                            <form method="post" action="">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><label for="host">SMTP Host</label></th>
                                        <td><input type="text" name="smtp_settings[host]" id="host" value="<?php echo esc_attr($smtp_settings['host']); ?>" class="regular-text"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="port">SMTP Port</label></th>
                                        <td><input type="text" name="smtp_settings[port]" id="port" value="<?php echo esc_attr($smtp_settings['port']); ?>" class="regular-text"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="username">SMTP Username</label></th>
                                        <td><input type="text" name="smtp_settings[username]" id="username" value="<?php echo esc_attr($smtp_settings['username']); ?>" class="regular-text"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="password">SMTP Password</label></th>
                                        <td><input type="password" name="smtp_settings[password]" id="password" value="<?php echo esc_attr($smtp_settings['password']); ?>" class="regular-text"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="encryption">Encryption</label></th>
                                        <td>
                                            <select name="smtp_settings[encryption]" id="encryption">
                                                <option value="" <?php selected($smtp_settings['encryption'], ''); ?>>None</option>
                                                <option value="ssl" <?php selected($smtp_settings['encryption'], 'ssl'); ?>>SSL</option>
                                                <option value="tls" <?php selected($smtp_settings['encryption'], 'tls'); ?>>TLS</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="from_email">From Email</label></th>
                                        <td><input type="email" name="smtp_settings[from_email]" id="from_email" value="<?php echo esc_attr($smtp_settings['from_email']); ?>" class="regular-text"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="from_name">From Name</label></th>
                                        <td><input type="text" name="smtp_settings[from_name]" id="from_name" value="<?php echo esc_attr($smtp_settings['from_name']); ?>" class="regular-text"></td>
                                    </tr>
                                </table>
                                <?php submit_button('Save SMTP Settings'); ?>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card col-md-6">
                    <div class="">
                        <div class="card-header">
                            <h2>Send Test Email</h2>
                            <p>Send a test email to verify SMTP settings.</p>
                        </div>
                        <div class="card-body">
                            <form method="post" action="">
                                <table class="form-table">
                                    <tr>
                                        <th scope="row"><label for="from_email">From Email</label></th>
                                        <td><input type="email" name="from_email" id="from_email" value="<?php echo esc_attr($smtp_settings['from_email']); ?>" class="regular-text"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="to_email">To Email</label></th>
                                        <td><input type="email" name="to_email" id="to_email" value="" class="regular-text"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row"><label for="test_email_content">Test Email Content</label></th>
                                        <td><textarea name="test_email_content" id="test_email_content" class="regular-text">This is a test email to verify SMTP settings.</textarea></td>
                                    </tr>
                                </table>
                                <?php submit_button('Send Test Email'); ?>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}
