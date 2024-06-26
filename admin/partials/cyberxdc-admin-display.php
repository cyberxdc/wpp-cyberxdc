<?php
/**
 * Callback function to display content for CyberXDC dashboard page
 */

// Fetch the latest 5 visitors logs

require_once plugin_dir_path(__FILE__) . '../../admin/pages/cyberxdc-license-page.php';
function get_latest_visitors_logs($limit = 5)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberxdc_visitor_logs'; // Correct table name
    $query = $wpdb->prepare("SELECT * FROM $table_name ORDER BY time DESC LIMIT %d", $limit); // Correct column name
    return $wpdb->get_results($query);
}

// Fetch the latest 5 users logs
function get_latest_users_logs($limit = 5)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'cyberxdc_users_logs'; // Correct table name
    $query = $wpdb->prepare("SELECT * FROM $table_name ORDER BY timestamp DESC LIMIT %d", $limit); // Correct column name
    return $wpdb->get_results($query);
}

function cyberxdc_dashboard_page()
{
    // Function to get geolocation based on IP address
    function get_geolocation($ip)
    {
        $api_key = 'YOUR_API_KEY'; // Your geolocation service API key
        $url = "https://api.ipgeolocation.io/ipgeo?apiKey=$api_key&ip=$ip";

        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return $data;
    }

    // Check if the user is logged in
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $logged_in_ip = $_SERVER['REMOTE_ADDR'];
        $logged_in_username = $current_user->user_login;

        // Get geolocation for logged-in IP
        $geo_location = get_geolocation($logged_in_ip);
        if ($geo_location && isset($geo_location['city']) && isset($geo_location['country_name'])) {
            $logged_in_city = $geo_location['city'];
            $logged_in_country = $geo_location['country_name'];
        } else {
            $logged_in_city = 'Unknown';
            $logged_in_country = 'Unknown';
        }
    } else {
        $logged_in_ip = 'Not logged in';
        $logged_in_username = 'Not logged in';
        $logged_in_city = 'Not applicable';
        $logged_in_country = 'Not applicable';
    }

    // Server Information
    global $server_details;

    $server_details = $server_details = array(
        'Hosted Server' => gethostname(),
        'IP Address' => $_SERVER['SERVER_ADDR'],
        'City' => $logged_in_city,
        'Country' => $logged_in_country,
        'Logged-in IP' => $logged_in_ip,
        'Logged-in Username' => $logged_in_username,
        'PHP Version' => phpversion(),
        'MySQL Version' => $GLOBALS['wpdb']->db_version(),
    );

    $visitors_logs = get_latest_visitors_logs();
    $users_logs = get_latest_users_logs();
    
    // Important features and their links
    $notice = '';
?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style="max-width: 100%;" class="cyberxdc-header card">
                <h1>CyberXDC Dashboard</h1>
                <h2>Empowering your online presence with CyberXDC</h2>
                <p>Transform your WordPress management experience with the CyberXDC Dashboard. Gain unparalleled insights and control over your site's performance, security, and user interactions from a centralized, intuitive interface. Monitor server health, track user activities, and analyze visitor interactions seamlessly to ensure peak performance and safeguard your online presence. Stay ahead with real-time data and actionable insights, empowering you to make informed decisions and optimize your WordPress site with ease.</p>
                <br>
                <?php if (get_option('cyberxdc_license_key') == '' || get_option('cyberxdc_license_key')) {
                    cyberxdc_generate_license_page();
                }
                ?>
            </div>
            <div style="display: flex; flex-direction:column; width:100%; max-width: 100%; flex-wrap: wrap" class="row">
                <div style="max-width: 100%; width:100%" class="card col-sm-6">
                    <h2>Server Information</h2>
                    <table class="table wp-list-table widefat fixed striped table-view-list">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($server_details as $key => $value) : ?>
                                <tr>
                                    <td><strong><?php echo $key; ?></strong></td>
                                    <td><?php echo $value; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <br>
                    <a href="admin.php?page=server_details" class="button">View All Server Details</a>
                </div>
                <div style="max-width: 100%; width:100%" class="card col-sm-6">
                    <h2>Visitors Logs</h2>
                    <table class="table wp-list-table widefat fixed striped table-view-list">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>IP v4</th>
                                <th>Country</th>
                                <th>Device</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($visitors_logs as $log) : ?>
                                <tr>
                                    <td><?php echo $log->time; ?></td>
                                    <td><?php echo $log->ip4; ?></td>
                                    <td><?php echo $log->country; ?></td>
                                    <td><?php echo $log->device; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <br>
                    <a href="admin.php?page=cyberxdc-logs&tab=visitor-logs" class="button">View All Visitors Logs</a>
                </div>
                <div style="max-width: 100%; width:100%" class="card col-sm-6 ">
                    <h2>Users Logs</h2>
                    <table class="table wp-list-table widefat fixed striped table-view-list">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>User</th>
                                <th>Activity</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users_logs as $log) : ?>
                                <tr>
                                    <td><?php echo $log->timestamp; ?></td>
                                    <td><?php echo $log->user; ?></td>
                                    <td><?php echo $log->activity; ?></td>
                                    <td><?php echo $log->ip_address; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <br>
                    <a href="admin.php?page=cyberxdc-logs&tab=user-logs" class="button">View All Users Logs</a>
                </div>
            </div>
        </div>
    </div>
<?php
}
