<?php

// Function to handle delete action
function cyberxdc_handle_delete_submission()
{
    if (isset($_GET['delete_id']) && current_user_can('manage_options')) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'cyberxdc_cf7db';
        $delete_id = intval($_GET['delete_id']);
        $form_id_get = intval($_GET['form_id']);
        $wpdb->delete($table_name, array('id' => $delete_id));
        wp_redirect(admin_url('admin.php?page=cyberxdc-cf7-submissions&form_id=' . $form_id_get));
        exit;
    }
}
add_action('admin_init', 'cyberxdc_handle_delete_submission');

// Function to render individual submission details
function cyberxdc_render_individual_submission($submission_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberxdc_cf7db';
    // Fetch the submission details
    $submission = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $submission_id));
    $submission_data = unserialize($submission->submission_data);

    $contact_forms = get_posts(array(
        'post_type' => 'wpcf7_contact_form',
        'posts_per_page' => -1,
    ));

    $contact_form = null;
    foreach ($contact_forms as $form) {
        if ($form->ID == $submission->form_id) {
            $contact_form = $form;
            break;
        }
    }
?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style="max-width: 100%;" class="card">
                <h2>Contact Form name : <?php echo $contact_forms[0]->post_title; ?></h2>
                <p class="text">Submission ID: <?php echo $submission_id; ?></p>
                <p class="desc">Submission Time: <?php echo $submission->submission_time; ?></p>
                <p class="desc">This is single view of contact form data</p>
            </div>
            <div style="max-width: 100%; margin-top: 20px; " class="card">
                <table style="margin-top: 20px;" class="wp-list-table widefat fixed striped">
                    <tbody>
                        <?php foreach ($submission_data as $key => $value) : ?>
                            <tr>
                                <td><strong><?php echo esc_html($key); ?>:</strong></td>
                                <td><?php echo esc_html($value); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td><strong>Submission Time:</strong></td>
                            <td><?php echo esc_html($submission->submission_time); ?></td>
                        </tr>
                    </tbody>
                </table>
                <div style="max-width: 100%; width:100%; margin-top: 20px; " class="actions">
                    <a href="<?php echo admin_url('admin.php?page=cyberxdc-cf7db&delete_id=' . $submission_id . '&form_id=' . $submission->form_id); ?>" class="button">Delete</a>
                    <a href="<?php echo admin_url('admin.php?page=cyberxdc-cf7-submissions&form_id=' . $submission->form_id); ?>" class="button">Back</a>
                </div>
            </div>
        </div>
    </div>
<?php
}

// Adjust the rendering function to handle view action
function cyberxdc_render_cf7db_page()
{
    // Check if CF7DB plugin is active
    if (!function_exists('wpcf7_contact_form')) {
        echo 'Contact Form 7 plugin is not active.';
        return;
    }

    // Check user capability
    if (!current_user_can('manage_options')) {
        return;
    }

    // If view ID is provided in URL
    if (isset($_GET['view_id'])) {
        // Get the submission ID from the URL
        $submission_id = intval($_GET['view_id']);

        // Load the page to display the specific submission details
        cyberxdc_render_individual_submission($submission_id);
        return;
    }

    // If form ID is provided in URL
    if (isset($_GET['form_id'])) {
        // Get the form ID from the URL
        $form_id = intval($_GET['form_id']);

        // Load the page to display submissions for the specific form
        cyberxdc_render_submissions_page($form_id);
        return;
    }

    // Fetch all contact form posts
    $contact_forms = get_posts(array(
        'post_type' => 'wpcf7_contact_form',
        'posts_per_page' => -1,
    ));


    // Display contact forms in a table
?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style="max-width: 100%;" class="card">
                <h1>Contact Form 7 Submissions</h1>
                <p class="desc">Centralize and manage your Contact Form 7 submissions with ease using CyberXDC. Our integrated database solution captures every form entry, ensuring you never miss critical communication from your audience. Access, search, and organize submissions directly from your WordPress dashboard, enhancing your ability to respond swiftly and maintain comprehensive records. Simplify your workflow and improve data handling efficiency, all while keeping valuable information at your fingertips.</p>
            </div>
            <div style="max-width: 100%;margin-top: 20px; " class="card">
                <table style="margin-top: 20px;" class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th scope="col">Form Name</th>
                            <th scope="col">Submissions</th>
                            <th scope="col">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contact_forms as $form) : ?>
                            <tr>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=cyberxdc-cf7-submissions&form_id=' . $form->ID); ?>">
                                        <?php echo esc_html($form->post_title); ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=cyberxdc-cf7-submissions&form_id=' . $form->ID); ?>">
                                        View Submissions
                                    </a>
                                </td>
                                <td>
                                    <?php
                                    global $wpdb;
                                    $table_name = $wpdb->prefix . 'cyberxdc_cf7db';
                                    $submissions_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE form_id = %d", $form->ID));
                                    echo $submissions_count;
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php
}

// Function to render submissions for a specific form
function cyberxdc_render_submissions_page($form_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberxdc_cf7db';

    // Fetch submissions for the specific form ID
    $submissions = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE form_id = %d ORDER BY submission_time DESC", $form_id));

    $contact_forms = get_posts(array(
        'post_type' => 'wpcf7_contact_form',
        'posts_per_page' => -1,
    ));

    $contact_form = null;
    foreach ($contact_forms as $form) {
        if ($form->ID == $form_id) {
            $contact_form = $form;
            break;
        }
    }

    if (!$contact_form) {
        echo '<div class="cyberxdc-wrap"><p>Form not found.</p></div>';
        return;
    }
    ?>
        <div class="cyberxdc-wrap">
            <div class="container">
                <div style="max-width: 100%;" class="card">
                    <h1>Contact Form 7 Submissions</h1>
                    <p class="desc">This is list of all contact form and submissions</p>
                    <h3 class="form-name">Form Name: <?php echo $contact_form->post_title; ?></h3>
                    <p>Submissions for Form ID: <?php echo $form_id; ?></p>
                </div>
                <div style="max-width: 100%; margin-top: 20px; " class="card">
                    <table style="margin-top: 20px;" class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th style="width: 50px;" scope="col">ID</th>
                                <th scope="col">Submission Data</th>
                                <th scope="col">Submission Time</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($submissions as $submission) :
                                $submission_data = unserialize($submission->submission_data);
                                // Concatenate submission data into a single line
                                $submission_data_str = '';
                                foreach ($submission_data as $key => $value) {
                                    $submission_data_str .= '<strong>' . esc_html($key) . ':</strong> ' . esc_html($value) . '; ';
                                }
                            ?>
                                <tr>
                                    <td><?php echo esc_html($submission->id); ?></td>
                                    <td><?php echo $submission_data_str; ?></td>
                                    <td><?php echo esc_html($submission->submission_time); ?></td>
                                    <td>
                                        <a href="<?php echo admin_url('admin.php?page=cyberxdc-cf7-submissions&view_id=' . $submission->id); ?>" class="button button-secondary">View</a>
                                        <a href="<?php echo admin_url('admin.php?page=cyberxdc-cf7-submissions&form_id=' . $form_id . '&delete_id=' . $submission->id); ?>" class="button button-danger" onclick="return confirm('Are you sure you want to delete this submission?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div style="margin-top: 20px;" class="action">
                        <a href="<?php echo admin_url('admin.php?page=cyberxdc-cf7-submissions'); ?>" class="button">Back</a>
                    </div>
                </div>
            </div>
        </div>
    <?php
}

// Hook into CF7 submission process
add_action('wpcf7_before_send_mail', 'cyberxdc_store_cf7_submission', 10, 1);

function cyberxdc_store_cf7_submission($cf7)
{
    // Get the submitted form data
    $submission = WPCF7_Submission::get_instance();
    if ($submission) {
        $posted_data = $submission->get_posted_data();

        // Insert the submitted data into your custom table
        global $wpdb;
        $table_name = $wpdb->prefix . 'cyberxdc_cf7db';
        $wpdb->insert($table_name, array(
            'form_id' => $cf7->id(),
            'submission_data' => serialize($posted_data),
            'submission_time' => current_time('mysql'),
        ));
    }
}

// Function to create the table if it doesn't exist
function cyberxdc_create_submissions_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberxdc_cf7db';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        form_id mediumint(9) NOT NULL,
        submission_data longtext NOT NULL,
        submission_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('admin_init', 'cyberxdc_create_submissions_table');
