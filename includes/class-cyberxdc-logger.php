<?php

class Cyberxdc_Logger
{

    public static function log_activity($user, $activity)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'cyberxdc_users_logs';
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $location = '';

        // Check if location is available in session
        if (isset($_SESSION['user_location'])) {
            $location = $_SESSION['user_location'];
        } else {
            // If location is not available in session, fetch it
            if (empty($ip_address) || $ip_address === '127.0.0.1' || $ip_address === '::1') {
                $ip_address = '49.206.201.42';
            }
            $location = self::get_ip_location($ip_address);

            // Store location in session
            $_SESSION['user_location'] = $location;
        }

        // Insert activity into database
        $wpdb->insert(
            $table_name,
            [
                'timestamp' => current_time('mysql'),
                'user' => $user,
                'activity' => $activity,
                'ip_address' => $ip_address,
                'location' => $location,
            ]
        );
    }

    public static function get_ip_location($ip)
    {
        // Your API token from ipinfo.io
        $token = '400a1d917f8378';

        // Check if the IP address is valid
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return 'Unknown';
        }
        // URL for the ipinfo.io API
        $url = "http://ipinfo.io/{$ip}/json?token={$token}";

        // Send the request to the API
        $response = wp_remote_get($url);

        // Check for errors in the response
        if (is_wp_error($response)) {
            return 'Unknown';
        }

        // Decode the JSON response
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);

        // Return the location if available
        if (isset($data->city) && isset($data->region) && isset($data->country)) {
            return "{$data->city}, {$data->region}, {$data->country}";
        }

        return 'Unknown';
    }
}

// Log login activity
function cyberxdc_log_login($user_login, $user)
{
    Cyberxdc_Logger::log_activity($user_login, 'User logged in');
}

// Log logout activity
function cyberxdc_log_logout()
{
    $user = wp_get_current_user();
    if ($user->exists()) {
        Cyberxdc_Logger::log_activity($user->user_login, 'User logged out');
    }
}

// Log post changes (add/edit)
function cyberxdc_log_post_changes($post_id, $post, $update)
{
    $user = wp_get_current_user();
    if ($update) {
        $activity = 'Post updated: ' . $post->post_title;
    } else {
        $activity = 'Post added: ' . $post->post_title;
    }
    Cyberxdc_Logger::log_activity($user->user_login, $activity);
}

// Log post deletion
function cyberxdc_log_post_deletion($post_id)
{
    $user = wp_get_current_user();
    $post = get_post($post_id);
    $activity = 'Post deleted: ' . $post->post_title;
    Cyberxdc_Logger::log_activity($user->user_login, $activity);
}

// Log user registration
function cyberxdc_log_user_registration($user_id)
{
    $user = get_user_by('id', $user_id);
    Cyberxdc_Logger::log_activity($user->user_login, 'User registered');
}

// Log profile update
function cyberxdc_log_profile_update($user_id, $old_user_data)
{
    $user = get_user_by('id', $user_id);
    Cyberxdc_Logger::log_activity($user->user_login, 'User profile updated');
}

// Log user deletion
function cyberxdc_log_user_deletion($user_id)
{
    $user = get_user_by('id', $user_id);
    Cyberxdc_Logger::log_activity($user->user_login, 'User deleted');
}
