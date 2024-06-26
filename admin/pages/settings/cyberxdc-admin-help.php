<?php
function cyberxdc_support_page()
{
    global $wpdb;

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cyberxdc_support_nonce']) && wp_verify_nonce($_POST['cyberxdc_support_nonce'], 'cyberxdc_support_form')) {
        // Sanitize and validate input fields
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $subject = sanitize_text_field($_POST['subject']);
        $message = sanitize_textarea_field($_POST['message']);

        // Save the message to the database
        $table_name = $wpdb->prefix . 'cyberxdc_support';
        $wpdb->insert(
            $table_name,
            [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'submitted_at' => current_time('mysql')
            ]
        );

        if ($wpdb->last_error) {
            $notice = 'An error occurred while submitting your support message. Please try again later.';
        }

        request_support_from_plugin_developer($name, $email, $subject, $message);
        // Display a success message
        $notice = 'Your support message has been submitted successfully.';
    }

    // Retrieve support messages from the database
    $table_name = $wpdb->prefix . 'cyberxdc_support';
    $support_messages = $wpdb->get_results("SELECT * FROM $table_name ORDER BY submitted_at DESC", ARRAY_A);

    // Display the support messages

?>
    <div class="container">
        <div style="max-width: 100%; width: 100%;" class="card">
            <h3>CyberXDC Support</h3>

            <?php if (!empty($notice)) : ?>
                <div style="margin: 0px;" class="notice notice-success is-dismissible">
                    <p><?php echo esc_html($notice); ?></p>
                </div>
            <?php endif; ?>
            <hr>
            <h4>Submit a Support Message</h4>
            <div style="display: flex;" class="support-form-wrapper">
                <div style="max-width: 100%; width: 100%;" class="card">
                    <form method="post" action="">
                        <?php wp_nonce_field('cyberxdc_support_form', 'cyberxdc_support_nonce'); ?>
                        <table class=" support-form form-table">
                            <tr>
                                <th scope="row"><label for="name">Name:</label></th>
                                <td><input type="text" id="name" name="name" value="<?php echo esc_attr($_POST['name'] ?? ''); ?>" required></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="email">Email:</label></th>
                                <td><input type="email" id="email" name="email" value="<?php echo esc_attr($_POST['email'] ?? ''); ?>" required></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="subject">Subject:</label></th>
                                <td><input type="text" id="subject" name="subject" value="<?php echo esc_attr($_POST['subject'] ?? ''); ?>" required></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="message">Message:</label></th>
                                <td><textarea id="message" name="message" required><?php echo esc_textarea($_POST['message'] ?? ''); ?></textarea></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="submit">Submit Message</label></th>
                                <td><input class="button" type="submit" value="Submit"></td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div style="max-width: 100%; width: 100%;" class="card">
                    <!-- Developer Details Section -->
                    <h4>Developer Details</h4>
                    <p>Developed by <strong>Dharmendra Chik Baraik</strong>, a Full Stack Developer at CyberXDC. With expertise in various modern web technologies, Dharmendra is dedicated to creating robust and scalable applications. For more details, visit the <a href="https://cyberxdc.com" target="_blank">CyberXDC website</a>.</p>

                    <hr>

                    <!-- Support Messages Section -->
                    <h4>Support Messages</h4>
                    <?php if (!empty($support_messages)) : ?>
                        <ul>
                            <?php foreach ($support_messages as $msg) : ?>
                                <li>
                                    <strong><?php echo esc_html($msg['name']); ?></strong> (<?php echo esc_html($msg['email']); ?>):
                                    <br><?php echo nl2br(esc_html($msg['message'])); ?>
                                    <br><em>Submitted on: <?php echo esc_html(date('F j, Y, g:i a', strtotime($msg['submitted_at']))); ?></em>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else : ?>
                        <p>No support messages available. If you have any issues or questions regarding this plugin, please use the form above to submit a support message. We will get back to you as soon as possible.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
<?php
}

// Remember to create a table for storing support messages, if not already created
function cyberxdc_create_support_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberxdc_support';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        email text NOT NULL,
        subject text NOT NULL,
        message text NOT NULL,
        submitted_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'cyberxdc_create_support_table');

function request_support_from_plugin_developer($name, $email, $subject, $message) {
    // Your API endpoint URL
    $api_url = CYBERXDC_PLUGIN_URL . '/plugin-support';

    // Validate the input fields
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        return false;
    }
    // Prepare the data to send
    $data = array(
        'name' => sanitize_text_field($name),
        'email' => sanitize_email($email),
        'subject' => sanitize_text_field($subject),
        'message' => sanitize_textarea_field($message),
        'website' => home_url(),     
    );


    // Set up the arguments for the POST request
    $args = array(
        'body' => wp_json_encode($data),
        'headers' => array(
            'Content-Type' => 'application/json',
        ),
        'method' => 'POST',
        'timeout' => 45,
        'sslverify' => false
    );

    // Make the API request
    $response = wp_remote_post($api_url, $args);

    // Check for errors in the response
    if (is_wp_error($response)) {
        // Log the error message for debugging
        error_log('Support API request failed: ' . $response->get_error_message());
        return false;
    }

    // Parse the response body
    $response_body = wp_remote_retrieve_body($response);
    $response_data = json_decode($response_body, true);

    // Check if the API returned a success status
    if (isset($response_data['status']) && $response_data['status'] == 'success') {
        return true;
    } else {
        // Log the response body for debugging
        error_log('Support API request error: ' . $response_body);
        return false;
    }
}
