<?php

function cyberxdc_page_settings()
{
    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        update_option('cyberxdc_default_parent_page', isset($_POST['cyberxdc_default_parent_page']) ? $_POST['cyberxdc_default_parent_page'] : '');
        update_option('cyberxdc_default_page_template', isset($_POST['cyberxdc_default_page_template']) ? $_POST['cyberxdc_default_page_template'] : 'default');
        update_option('cyberxdc_page_disable_comments', isset($_POST['cyberxdc_page_disable_comments']) ? '1' : '0');
        update_option('cyberxdc_default_page_author', isset($_POST['cyberxdc_default_page_author']) ? $_POST['cyberxdc_default_page_author'] : '');

        // Display a notice
        $notice = 'Settings saved successfully.';
    }

    // Get current settings
    $default_parent_page = get_option('cyberxdc_default_parent_page', '');
    $default_page_template = get_option('cyberxdc_default_page_template', 'default');
    $page_disable_comments = get_option('cyberxdc_page_disable_comments', '0');
    $default_page_author = get_option('cyberxdc_default_page_author', '');
    $pages = get_pages();
    $templates = wp_get_theme()->get_page_templates();
    $users = get_users(array('who' => 'authors'));
?>
    <div class="container">
        <div style="max-width: 100%; width: 100%;" class="card">
            <h3>CyberXDC Page Settings</h3>
            <?php if (!empty($notice)) : ?>
                <div style="margin: 0px;" class="notice notice-success is-dismissible">
                    <p><?php echo $notice; ?></p>
                </div>
            <?php endif; ?>
            <hr>
            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="cyberxdc_default_parent_page">Default Parent Page:</label></th>
                        <td>
                            <select id="cyberxdc_default_parent_page" name="cyberxdc_default_parent_page">
                                <option value="">Select Parent Page</option>
                                <?php foreach ($pages as $page) : ?>
                                    <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($default_parent_page, $page->ID); ?>>
                                        <?php echo esc_html($page->post_title); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cyberxdc_default_page_template">Default Page Template:</label></th>
                        <td>
                            <select id="cyberxdc_default_page_template" name="cyberxdc_default_page_template">
                                <option value="default" <?php selected($default_page_template, 'default'); ?>>Default Template</option>
                                <?php foreach ($templates as $template_name => $template_filename) : ?>
                                    <option value="<?php echo esc_attr($template_filename); ?>" <?php selected($default_page_template, $template_filename); ?>>
                                        <?php echo esc_html($template_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cyberxdc_page_disable_comments">Disable Comments:</label></th>
                        <td><input type="checkbox" id="cyberxdc_page_disable_comments" name="cyberxdc_page_disable_comments" value="1" <?php checked($page_disable_comments, '1'); ?>></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="cyberxdc_default_page_author">Default Author:</label></th>
                        <td>
                            <select id="cyberxdc_default_page_author" name="cyberxdc_default_page_author">
                                <option value="">Select Author</option>
                                <?php foreach ($users as $user) : ?>
                                    <option value="<?php echo esc_attr($user->ID); ?>" <?php selected($default_page_author, $user->ID); ?>>
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
add_action('save_post', 'cyberxdc_apply_page_settings', 10, 2);

function cyberxdc_apply_page_settings($post_id, $post)
{
    if ($post->post_type != 'page') {
        return;
    }

    // Set default parent page
    $default_parent_page = get_option('cyberxdc_default_parent_page', '');
    if ($default_parent_page && empty($post->post_parent)) {
        wp_update_post(array(
            'ID' => $post_id,
            'post_parent' => $default_parent_page
        ));
    }

    // Set default page template
    $default_page_template = get_option('cyberxdc_default_page_template', 'default');
    if ($default_page_template && get_post_meta($post_id, '_wp_page_template', true) == 'default') {
        update_post_meta($post_id, '_wp_page_template', $default_page_template);
    }

    // Disable comments
    if (get_option('cyberxdc_page_disable_comments', '0') === '1') {
        remove_post_type_support('page', 'comments');
    }

    // Set default author
    $default_page_author = get_option('cyberxdc_default_page_author', '');
    if ($default_page_author) {
        wp_update_post(array(
            'ID' => $post_id,
            'post_author' => $default_page_author
        ));
    }
}
?>
