<?php

function cyberxdc_user_security_page()
{
    global $wpdb;
    $notice = '';

    // Check if the form has been submitted
    if (isset($_POST['cyberxdc_user_security_submit'])) {
        // Verify nonce for security
        if (!isset($_POST['cyberxdc_user_security_nonce']) || !wp_verify_nonce($_POST['cyberxdc_user_security_nonce'], 'cyberxdc_user_security_nonce')) {
            wp_die('Nonce verification failed');
        }

        // Sanitize and update username
        if (isset($_POST['new_username']) && isset($_POST['user_id'])) {
            $new_username = sanitize_user($_POST['new_username']);
            $user_id = intval($_POST['user_id']);

            if (username_exists($new_username)) {
                $notice = 'Username already exists.';
            } elseif (empty($new_username)) {
                $notice = 'Username cannot be empty.';
            } else {
                $result = $wpdb->update(
                    $wpdb->users,
                    array('user_login' => $new_username),
                    array('ID' => $user_id)
                );

                if ($result === false) {
                    $notice = 'Error updating username: ' . $wpdb->last_error;
                } else {
                    $notice = 'Username changed successfully.';
                }
            }
        } else {
            $notice = 'Invalid input.';
        }
    }

    // Get administrators
    $admins = get_users(array(
        'role' => 'administrator'
    ));

?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style="width: 100%; max-width: 100%; " class="card">
                <h2>User Security Settings</h2>
                <?php if (!empty($notice)) : ?>
                    <div style="margin: 0px;" class="notice notice-success is-dismissible">
                        <p><?php echo $notice; ?></p>
                    </div>
                <?php endif; ?>
                <form method="post" action="">
                    <h3>Administrator Accounts</h3>
                    <p>These are the accounts that have administrator privileges.</p>
                    <style>
                        #admin-table .id-column {
                            width: 50px;
                        }
                    </style>
                    <table id="admin-table" class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th width="50px" scope="col" class="id-column" class="manage-column">ID</th>
                                <th scope="col" class="manage-column">Username</th>
                                <th scope="col" class="manage-column">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $admin) : ?>
                                <?php
                                $username_warning = '';
                                if ($admin->user_login === 'admin') {
                                    $username_warning = '<span style="color: red;"> - <strong>Danger!</strong> It is recommended to change the "admin" username for security reasons.</span>';
                                }
                                ?>
                                <tr>
                                    <td width="50px"><?php echo esc_html($admin->ID); ?></td>
                                    <td><?php echo esc_html($admin->user_login) . $username_warning; ?></td>
                                    <td><?php echo esc_html($admin->user_email); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div style="max-width: 100%; width: 100%;" class="card">
                        <h3>Change Your Username</h3>
                        <?php if (is_user_logged_in()) : ?>
                            <?php $current_user = wp_get_current_user(); ?>
                            <p>
                                Current Username: <strong><?php echo esc_html($current_user->user_login); ?></strong>
                            </p>
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row">New Username</th>
                                    <td>
                                        <input type="text" name="new_username" value="" />
                                        <input type="hidden" name="user_id" value="<?php echo esc_attr($current_user->ID); ?>" />
                                        <p class="description">Enter a new username.</p>
                                    </td>
                                </tr>
                            </table>
                            <div  style="margin: 0px;" class="notice notice-info cyberxdc-custom-info-box">
                                <p style="">
                                    NOTE: If you are currently logged in as "admin" you will be automatically logged out after changing your username and will be required to log back in.
                                </p>
                            </div>
                            <br>
                            <?php wp_nonce_field('cyberxdc_user_security_nonce', 'cyberxdc_user_security_nonce'); ?>
                            <input type="submit" name="cyberxdc_user_security_submit" class="button-primary" value="Save Changes">
                        <?php else : ?>
                            <p>You need to be logged in to change your username.</p>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
}
