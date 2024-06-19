<?php
function cyberxdc_shortcodes_page()
{
    $all_shortcodes = array();

    // Get all posts
    $posts = get_posts(array('post_type' => 'any', 'posts_per_page' => -1));

    // Loop through each post and extract shortcodes
    foreach ($posts as $post) {
        $post_content = $post->post_content;

        // Regular expression to match shortcodes
        preg_match_all('/\[(\w+)[^\]]*\]/', $post_content, $matches);

        // Check if any matches are found
        if (!empty($matches[1])) {
            foreach ($matches[1] as $shortcode) {
                // Check if the shortcode exists in the list of default WordPress shortcodes
                if (shortcode_exists($shortcode)) {
                    $all_shortcodes[] = array(
                        'shortcode' => $shortcode,
                        'post_id' => $post->ID,
                        'post_title' => get_the_title($post),
                        'post_type' => get_post_type($post),
                        'permalink' => get_permalink($post),
                        'description' => '', // Placeholder for shortcode description
                        'type' => 'Default',
                    );
                }
            }
        }
    }

    // Output the shortcode list
?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style="max-width: 100%;" class="cyberxdc-header card">
                <h1>CyberXDC Shortcodes</h1>
                <p>Welcome to the CyberXDC Shortcodes page!</p>
            </div>
            <div style="display: flex;" class="row">
                <div class="card">
                    <div class="card-header">
                        <h2>Custom Shortcodes</h2>
                        <p>These are custom shortcodes that you can use in your posts or pages.</p>
                        <br>
                    </div>
                    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f2f2f2;">
                                <th>Title</th>
                                <th>Description</th>
                                <th>Shortcode</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Login Form</td>
                                <td>Use this shortcode to display the WordPress login form on a custom page.</td>
                                <td><code>[custom_login_form]</code></td>
                            </tr>
                            <tr>
                                <td>Registration Form</td>
                                <td>Use this shortcode to provide a link to the WordPress registration page.</td>
                                <td><code>[custom_registration_form]</code></td>
                            </tr>
                            <tr>
                                <td>Forgot Password Form</td>
                                <td>Use this shortcode to provide a link to the WordPress forgot password page.</td>
                                <td><code>[custom_forgot_password_form]</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div style="max-width: 100%; width: 100%;" class="card">
                    <?php
                    if (!empty($all_shortcodes)) {
                        echo '<h2>List of WordPress Shortcodes in Posts:</h2>';
                        echo '<table style="width: 100%; border-collapse: collapse;">';
                        echo '<tr>';
                        echo '<th style="border: 1px solid #ddd; padding: 8px;">Shortcode</th>';
                        echo '<th style="border: 1px solid #ddd; padding: 8px;">Type</th>';
                        echo '<th style="border: 1px solid #ddd; padding: 8px;">Post ID</th>';
                        echo '<th style="border: 1px solid #ddd; padding: 8px;">Post Title</th>';
                        echo '<th style="border: 1px solid #ddd; padding: 8px;">Shortcode Description</th>';
                        echo '<th style="border: 1px solid #ddd; padding: 8px;">Post Type</th>';
                        echo '<th style="border: 1px solid #ddd; padding: 8px;">Permalink</th>';
                        echo '</tr>';

                        foreach ($all_shortcodes as $shortcode_info) {
                            echo '<tr>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px;">[' . $shortcode_info['shortcode'] . ']</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px;">' . $shortcode_info['type'] . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px;">' . $shortcode_info['post_id'] . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px;">' . $shortcode_info['post_title'] . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px;">' . $shortcode_info['description'] . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px;">' . $shortcode_info['post_type'] . '</td>';
                            echo '<td style="border: 1px solid #ddd; padding: 8px;"><a href="' . $shortcode_info['permalink'] . '">View Post</a></td>';
                            echo '</tr>';
                        }

                        echo '</table>';
                    } else {
                        echo '<p>No default WordPress shortcodes found in posts.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php
}

// Login Form Shortcode
function custom_login_form_shortcode()
{
    if (is_user_logged_in()) {
        return '<p>You are already logged in.</p>';
    } else {
        ob_start();
        wp_login_form();
        return ob_get_clean();
    }
}
add_shortcode('cyberxdc_login_form', 'custom_login_form_shortcode');

// Registration Page Shortcode
function custom_registration_form_shortcode()
{
    if (is_user_logged_in()) {
        return '<p>You are already logged in.</p>';
    }

    if (!get_option('users_can_register')) {
        return '<p>Registration is currently disabled.</p>';
    }

    return '<a href="' . esc_url(wp_registration_url()) . '">Register</a>';
}
add_shortcode('cyberxdc_registration_form', 'custom_registration_form_shortcode');

// Forgot Password Page Shortcode
function custom_forgot_password_form_shortcode()
{
    if (is_user_logged_in()) {
        return '<p>You are already logged in.</p>';
    }

    return '<a href="' . esc_url(wp_lostpassword_url()) . '">Forgot Password</a>';
}
add_shortcode('cyberxdc_forgot_password_form', 'custom_forgot_password_form_shortcode');
