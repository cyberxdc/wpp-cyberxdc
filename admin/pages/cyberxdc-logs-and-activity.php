<?php

// Function to Retrieve Logs

// Include logging functions
require_once plugin_dir_path(__FILE__) . '../../includes/class-cyberxdc-logger.php';

function display_cyberxdc_logs_page()
{
    $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'user-logs';
?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style="max-width: 100%;" class="card">
                <h1>Welcome to CyberXDC Logs & Activity</h1>
                <div class="container">
                    <p>Explore Your Site's Activity and Logs</p>
                    <p>Gain Insight into User Interactions and System Changes</p>
                </div>
                <div class="container">
                    <h2 class="nav-tab-wrapper">
                        <a href="?page=cyberxdc-logs&tab=user-logs" class="nav-tab <?php echo $current_tab === 'user-logs' ? 'nav-tab-active' : ''; ?>">User logs & Activity</a>
                        <a href="?page=cyberxdc-logs&tab=visitor-logs" class="nav-tab <?php echo $current_tab === 'visitor-logs' ? 'nav-tab-active' : ''; ?>">Visitors Logs and Activity</a>
                    </h2>
                </div>
            </div>
            <div style="max-width: 100%;" class="card">
                <?php
                switch ($current_tab) {
                    case 'user-logs':
                        cyberxdc_user_logs();
                        break;
                    case 'visitor-logs':
                        cyberxdc_visitor_logs();
                        break;
                    default:
                        cyberxdc_user_logs();
                        break;
                }
                ?>
            </div>
        </div>
    <?php
}




// Hook into WordPress actions to log activities
add_action('wp_login', 'cyberxdc_log_login', 10, 2);
add_action('wp_logout', 'cyberxdc_log_logout');
add_action('save_post', 'cyberxdc_log_post_changes', 10, 3);
add_action('before_delete_post', 'cyberxdc_log_post_deletion');
add_action('user_register', 'cyberxdc_log_user_registration');
add_action('profile_update', 'cyberxdc_log_profile_update', 10, 2);
add_action('delete_user', 'cyberxdc_log_user_deletion');

// Include Logger Class
require_once plugin_dir_path(__FILE__) . '../../includes/class-cyberxdc-logger.php';

// Function to Retrieve Logs for a Specific Page
function get_cyberxdc_users_logs($page_number, $logs_per_page)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberxdc_users_logs';
    $offset = ($page_number - 1) * $logs_per_page;
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY timestamp DESC LIMIT %d, %d", $offset, $logs_per_page));
}

// Function to Count Total Logs
function count_cyberxdc_users_logs()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberxdc_users_logs';
    return $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
}
// Function to Handle Pagination
// Function to Handle Pagination
function cyberxdc_users_logs_pagination()
{
    $current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;
    $total_logs = count_cyberxdc_users_logs();
    $logs_per_page = 10;
    $total_pages = ceil($total_logs / $logs_per_page);

    if ($total_pages > 1) {
        $base_url = admin_url('admin.php?page=cyberxdc_logs');
        $base_url = remove_query_arg('paged', $base_url); // Remove any existing 'paged' parameter
        if (false === strpos($base_url, '?')) {
            $base_url .= '?paged=%#%';
        } else {
            $base_url .= '&paged=%#%';
        }

        echo '<div class="tablenav-pages">';
        echo '<span class="displaying-num">' . sprintf(_n('%s item', '%s items', $total_logs), number_format_i18n($total_logs)) . '</span>';
        echo paginate_links(array(
            'base' => $base_url,
            'format' => '',
            'current' => $current_page,
            'total' => $total_pages,
            'prev_text' => 'Prev',
            'next_text' => 'Next',
            'before_page_number' => '<span class="screen-reader-text">' . __('Page') . ' </span>',
            'after_page_number' => '',
        ));
        echo '</div>';
    }
}




// Function to Delete Logs
function delete_cyberxdc_users_logs($log_ids)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberxdc_users_logs';
    $ids_placeholder = implode(', ', array_fill(0, count($log_ids), '%d'));
    $sql = $wpdb->prepare("DELETE FROM $table_name WHERE id IN ($ids_placeholder)", $log_ids);
    return $wpdb->query($sql);
}

function cyberxdc_user_logs()
{
    $current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;

    // Number of logs per page
    $logs_per_page = 10;

    // Retrieve logs for the current page
    $logs = get_cyberxdc_users_logs($current_page, $logs_per_page);

    // Total number of logs
    $total_logs = count_cyberxdc_users_logs();

    // Total number of pages
    $total_pages = ceil($total_logs / $logs_per_page);

    // Process log deletion
    if (isset($_POST['delete_users_logs'])) {
        $log_ids = isset($_POST['log']) ? $_POST['log'] : array();
        if (!empty($log_ids)) {
            $deleted = delete_cyberxdc_users_logs($log_ids);
            if ($deleted) {
                $user_success_message = 'Users Logs deleted successfully.';
            } else {
                $user_error_message = 'Failed to delete logs. Please try again.';
            }
        }
    }
    ?>
        <div class="users-logs">
            <div class="card-header">
                <h2>User Logs and Activity</h2>
                <p>These are logs and activity reported by CyberXDC.</p>
            </div>
            <?php if (isset($user_success_message)) : ?>
                <div style="margin: 0px;" class="notice notice-success is-dismissible">
                    <p><?php echo $user_success_message; ?></p>
                </div>
                <br>
            <?php endif; ?>
            <?php if (isset($user_error_message)) : ?>
                <div style="margin: 0px;" class="notice notice-error is-dismissible">
                    <p><?php echo $user_error_message; ?></p>
                </div>
                <br>
            <?php endif; ?>
        </div>
        <form method="post">
            <div style="margin-bottom: 8px;" class="cyberxdc-logs-actions">
                <button type="submit" class="button" name="delete_users_logs">Delete Selected</button>
                <br>
            </div>
            <!-- Add table to display logs -->
            <table class="wp-list-table widefat fixed striped table-view-list">
                <thead>
                    <tr>
                        <th scope="col" id="cb" class="manage-column column-cb check-column">
                            <input type="checkbox" id="cb-select-all">
                        </th>
                        <th scope="col" id="id" class="manage-column column-id column-primary">ID</th>
                        <th scope="col" id="timestamp" class="manage-column column-timestamp">Timestamp</th>
                        <th scope="col" id="user" class="manage-column column-user">User</th>
                        <th scope="col" id="activity" class="manage-column column-activity">Activity</th>
                        <th scope="col" id="ip" class="manage-column column-ip">IP Address</th>
                        <th scope="col" id="location" class="manage-column column-location">Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)) : ?>
                        <?php foreach ($logs as $log) : ?>
                            <tr>
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="log[]" value="<?php echo esc_attr($log->id); ?>">
                                </th>
                                <td><?php echo esc_html($log->id); ?></td>
                                <td><?php echo esc_html($log->timestamp); ?></td>
                                <td><?php echo esc_html($log->user); ?></td>
                                <td><?php echo esc_html($log->activity); ?></td>
                                <td><?php echo esc_html($log->ip_address); ?></td>
                                <td><?php echo esc_html($log->location); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7">No logs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <!-- Pagination -->
            <!-- Pagination -->
            <?php if ($total_pages > 1) : ?>
                <div class="tablenav">
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo sprintf(_n('%s log', '%s logs', $total_logs), number_format_i18n($total_logs)); ?></span>
                        <span class="pagination-links">
                            <?php if ($current_page > 1) : ?>
                                <a class="prev-page button" href="<?php echo esc_url(add_query_arg('paged', $current_page - 1)); ?>">‹</a>
                            <?php endif; ?>

                            <span class="screen-reader-text">Current Page</span>
                            <span id="table-paging" class="paging-input">
                                <span class="tablenav-paging-text"><?php echo $current_page; ?> of <span class="total-pages"><?php echo $total_pages; ?></span></span>
                            </span>

                            <?php if ($current_page < $total_pages) : ?>
                                <a class="next-page button" href="<?php echo esc_url(add_query_arg('paged', $current_page + 1)); ?>">›</a>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>


        </form>
    <?php
}

function get_cyberxdc_visitor_logs($page, $logs_per_page)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberxdc_visitor_logs';
    $offset = ($page - 1) * $logs_per_page;

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name ORDER BY time DESC LIMIT %d OFFSET %d",
        $logs_per_page,
        $offset
    ));

    return $results;
}

function count_cyberxdc_visitor_logs()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberxdc_visitor_logs';

    return $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
}

function delete_cyberxdc_visitor_logs($log_ids)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberxdc_visitor_logs';
    $ids = implode(',', array_map('intval', $log_ids));

    $wpdb->query("DELETE FROM $table_name WHERE id IN ($ids)");
}

function cyberxdc_visitor_logs()
{
    // Get current tab
    $current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'user-logs';

    // Get current page number
    $current_page = isset($_GET['paged']) ? absint($_GET['paged']) : 1;

    // Number of logs per page
    $logs_per_page = 10;

    // Handle form submission
    if (isset($_POST['delete_visitor_logs'])) {
        $log_ids = isset($_POST['log']) ? array_map('intval', $_POST['log']) : [];
        if (!empty($log_ids)) {
            delete_cyberxdc_visitor_logs($log_ids);
            $visitor_suceess_message = 'Visitor Logs deleted successfully.';
        } else {
            $visitor_error_message = 'Please select at least one log to delete.';
        }
    }

    // Retrieve logs for the current page based on the current tab
    if ($current_tab === 'visitor-logs') {
        $logs = get_cyberxdc_visitor_logs($current_page, $logs_per_page);
        $total_logs = count_cyberxdc_visitor_logs();
    } else {
        $logs = get_cyberxdc_visitor_logs($current_page, $logs_per_page);
        $total_logs = count_cyberxdc_visitor_logs();
    }

    // Total number of pages
    $total_pages = ceil($total_logs / $logs_per_page);
    ?>
        <div class="visitor-logs">
            <div class="card-header">
                <h2>Visitors Logs and Activity</h2>
                <p>These are logs and activity reported by CyberXDC.</p>

            </div>
            <?php if (isset($visitor_suceess_message)) : ?>
                <div style="margin: 0px;" class="notice notice-success is-dismissible">
                    <p><?php echo $visitor_suceess_message; ?></p>
                </div>
                <br>
            <?php endif; ?>
            <?php if (isset($visitor_error_message)) : ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php echo $visitor_error_message; ?></p>
                </div>
                <br>
            <?php endif; ?>
        </div>
        <form method="post">
            <div style="margin-bottom: 8px;" class="cyberxdc-logs-actions">
                <button type="submit" class="button" name="delete_visitor_logs">Delete Selected</button>
                <br>
            </div>
            <!-- Add table to display logs -->
            <table class="wp-list-table widefat fixed striped table-view-list">
                <thead>
                    <tr>
                        <th scope="col" id="cb" class="manage-column column-cb check-column">
                            <input type="checkbox" id="cb-select-all">
                        </th>
                        <th scope="col" id="id" class="manage-column column-id column-primary">ID</th>
                        <th scope="col" id="timestamp" class="manage-column column-timestamp">Timestamp</th>
                        <th scope="col" id="ip4" class="manage-column column-ip">IPv4 Address</th>
                        <th scope="col" id="ip6" class="manage-column column-ip">IPv6 Address</th>
                        <th scope="col" id="country" class="manage-column column-country">Country</th>
                        <th scope="col" id="browser" class="manage-column column-browser">Browser</th>
                        <th scope="col" id="device" class="manage-column column-device">Device</th>
                        <th scope="col" id="page_visited" class="manage-column column-page-visited">Page Visited</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)) : ?>
                        <?php foreach ($logs as $log) : ?>
                            <tr>
                                <th scope="row" class="check-column">
                                    <input type="checkbox" name="log[]" value="<?php echo esc_attr($log->id); ?>">
                                </th>
                                <td><?php echo esc_html($log->id); ?></td>
                                <td><?php echo esc_html($log->time); ?></td>
                                <td><?php echo esc_html($log->ip4); ?></td>
                                <td><?php echo esc_html($log->ip6); ?></td>
                                <td><?php echo esc_html($log->country); ?></td>
                                <td><?php echo esc_html($log->browser); ?></td>
                                <td><?php echo esc_html($log->device); ?></td>
                                <td><?php echo esc_html($log->page_visited); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="9">No logs found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($total_pages > 1) : ?>
                <div class="tablenav">
                    <div class="tablenav-pages">
                        <span class="displaying-num"><?php echo sprintf(_n('%s log', '%s logs', $total_logs), number_format_i18n($total_logs)); ?></span>
                        <span class="pagination-links">
                            <?php if ($current_page > 1) : ?>
                                <a class="prev-page button" href="<?php echo esc_url(add_query_arg('paged', $current_page - 1)); ?>">‹</a>
                            <?php endif; ?>

                            <span class="screen-reader-text">Current Page</span>
                            <span id="table-paging" class="paging-input">
                                <span class="tablenav-paging-text"><?php echo $current_page; ?> of <span class="total-pages"><?php echo $total_pages; ?></span></span>
                            </span>

                            <?php if ($current_page < $total_pages) : ?>
                                <a class="next-page button" href="<?php echo esc_url(add_query_arg('paged', $current_page + 1)); ?>">›</a>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            <?php endif; ?>
        </form>
    </div>
    </div>
<?php
}
