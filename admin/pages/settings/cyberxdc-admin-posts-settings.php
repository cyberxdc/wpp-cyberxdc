<?php

function cyberxdc_post_settings()
{
    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        update_option('cyberxdc_default_post_category', isset($_POST['cyberxdc_default_post_category']) ? $_POST['cyberxdc_default_post_category'] : '');
        update_option('cyberxdc_default_post_format', isset($_POST['cyberxdc_default_post_format']) ? $_POST['cyberxdc_default_post_format'] : 'standard');
        update_option('cyberxdc_disable_comments', isset($_POST['cyberxdc_disable_comments']) ? '1' : '0');
        update_option('cyberxdc_default_author', isset($_POST['cyberxdc_default_author']) ? $_POST['cyberxdc_default_author'] : '');

        // Display a notice
        $notice = 'Settings saved successfully.';
    }

    // Get current settings
    $default_post_category = get_option('cyberxdc_default_post_category', '');
    $default_post_format = get_option('cyberxdc_default_post_format', 'standard');
    $disable_comments = get_option('cyberxdc_disable_comments', '0');
    $default_author = get_option('cyberxdc_default_author', '');
    $categories = get_categories(array('hide_empty' => false));
    $post_formats = get_theme_support('post-formats');
    $users = get_users(array('who' => 'authors'));
?>
    <div class="container">
        <div style="max-width: 100%; width: 100%;" class="card">
            <h3>CyberXDC Post Settings</h3>
            <?php if (!empty($notice)) : ?>
                <div style="margin: 0px;" class="notice notice-success is-dismissible">
                    <p><?php echo $notice; ?></p>
                </div>
            <?php endif; ?>
            <hr>
            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="cyberxdc_default_post_category">Default Post Category:</label></th>
                        <td>
                            <select id="cyberxdc_default_post_category" name="cyberxdc_default_post_category">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category) : ?>
                                    <option value="<?php echo esc_attr($category->term_id); ?>" <?php selected($default_post_category, $category->term_id); ?>>
                                        <?php echo esc_html($category->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cyberxdc_default_post_format">Default Post Format:</label></th>
                        <td>
                            <select id="cyberxdc_default_post_format" name="cyberxdc_default_post_format">
                                <option value="standard" <?php selected($default_post_format, 'standard'); ?>>Standard</option>
                                <?php if (isset($post_formats[0])) : ?>
                                    <?php foreach ($post_formats[0] as $format) : ?>
                                        <option value="<?php echo esc_attr($format); ?>" <?php selected($default_post_format, $format); ?>>
                                            <?php echo ucfirst($format); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cyberxdc_disable_comments">Disable Comments:</label></th>
                        <td><input type="checkbox" id="cyberxdc_disable_comments" name="cyberxdc_disable_comments" value="1" <?php checked($disable_comments, '1'); ?>></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cyberxdc_default_author">Default Author:</label></th>
                        <td>
                            <select id="cyberxdc_default_author" name="cyberxdc_default_author">
                                <option value="">Select Author</option>
                                <?php foreach ($users as $user) : ?>
                                    <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($default_author, $user->ID); ?>>
                                        <?php echo esc_html($user->display_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
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

// Apply the settings
add_action('save_post', 'cyberxdc_apply_post_settings', 10, 2);

function cyberxdc_apply_post_settings($post_id, $post)
{
    if ($post->post_type != 'post') {
        return;
    }

    // Set default post category
    $default_category = get_option('cyberxdc_default_post_category', '');
    if ($default_category && !has_term('', 'category', $post_id)) {
        wp_set_post_categories($post_id, array($default_category));
    }

    // Set default post format
    $default_format = get_option('cyberxdc_default_post_format', 'standard');
    set_post_format($post_id, $default_format);

    // Disable comments
    if (get_option('cyberxdc_disable_comments', '0') === '1') {
        remove_post_type_support('post', 'comments');
    }

    // Set default author
    $default_author = get_option('cyberxdc_default_author', '');
    if ($default_author) {
        wp_update_post(array(
            'ID' => $post_id,
            'post_author' => $default_author
        ));
    }
}
?>
