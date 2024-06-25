<?php

/**
 * Fired during plugin activation
 *
 * @link       https://cyberxdc.42web.io
 * @since      1.0.0
 *
 * @package    Cyberxdc
 * @subpackage Cyberxdc/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cyberxdc
 * @subpackage Cyberxdc/includes
 * @author     DC Baraik <cyberxdc007@gmail.com>
 */
class Cyberxdc_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		global $wpdb;

		// Create table for cyberxdc_users_logs
		$table_name_logs = $wpdb->prefix . 'cyberxdc_users_logs';
		$charset_collate = $wpdb->get_charset_collate();

		$sql_logs = "CREATE TABLE $table_name_logs (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			timestamp datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			user varchar(50) NOT NULL,
			activity text NOT NULL,
			ip_address varchar(100) NOT NULL,
			location varchar(100) NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		// Create table for cf7_submissions
		$table_name_submissions = $wpdb->prefix . 'cf7_submissions';

		$sql_submissions = "CREATE TABLE $table_name_submissions (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			form_id mediumint(9) NOT NULL,
			submission_data text NOT NULL,
			submission_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		$table_name = $wpdb->prefix . 'cyberxdc_visitor_logs';

		$sql_cyberxdc_visitor_logs = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			ip4 varchar(45) NOT NULL,
			ip6 varchar(45),
			country varchar(100),
			browser text NOT NULL,
			device varchar(100) NOT NULL,
			time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			page_visited text NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		// Include necessary WordPress functions
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		// Create the tables
		dbDelta($sql_logs);
		dbDelta($sql_submissions);
		dbDelta($sql_cyberxdc_visitor_logs);

		// Add options
		if (get_option('cyberxdc_license_key') === false) {
			add_option('cyberxdc_license_key', '');
		}
		if (get_option('cyberxdc_license_status') === false) {
			add_option('cyberxdc_license_status', 'inactive');
		} else {
			cyberxdc_validate_license();
			error_log("License status on plugin activation: " . get_option('cyberxdc_license_status'));
		}
		$plugin_download_url = CYBERXDC_PLUGIN_URL . '/plugin-download-url';
		$plugin_download_url_response = wp_remote_get($plugin_download_url);

		if (is_wp_error($plugin_download_url_response)) {
			$error_message = $plugin_download_url_response->get_error_message();
			echo "Error: $error_message";
			return;
		}

		$plugin_download_url_data = json_decode($plugin_download_url_response['body'], true);

		if (empty($plugin_download_url_data)) {
			echo "Error: Invalid plugin download URL data";
			return;
		}

		$plugin_repo_name = $plugin_download_url_data['repo_name'];
		$plugin_repo_owner = $plugin_download_url_data['repo_owner'];
		$plugin_repo_tagname = $plugin_download_url_data['tag'];

		if (empty($plugin_repo_name) || empty($plugin_repo_owner) || empty($plugin_repo_tagname)) {
			echo "Error: Invalid plugin repository data";
			return;
		}
		if (get_option('cyberxdc_plugin_repo_name')) {
			update_option('cyberxdc_plugin_repo_name', $plugin_repo_name);
		} else {
			add_option('cyberxdc_plugin_repo_name', $plugin_repo_name);
		}
		if (get_option('cyberxdc_plugin_repo_owner')) {
			update_option('cyberxdc_plugin_repo_owner', $plugin_repo_owner);
		} else {
			add_option('cyberxdc_plugin_repo_owner', $plugin_repo_owner);
		}
		if (get_option('cyberxdc_plugin_repo_tagname')) {
			update_option('cyberxdc_plugin_repo_tagname', $plugin_repo_tagname);
		} else {
			add_option('cyberxdc_plugin_repo_tagname', $plugin_repo_tagname);
		}
		if(get_option('cyberxdc_license_validation_failed_date' === false)) {
			add_option('cyberxdc_license_validation_failed_date', current_time('timestamp'));
		}
	}
}
