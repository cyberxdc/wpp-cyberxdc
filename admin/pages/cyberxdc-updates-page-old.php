<?php
// Callback function to display content for Updates & Licenses page
function cyberxdc_updates_page()
{
    $plugin_main_file = __DIR__ . '/../../cyberxdc.php';
    if (file_exists($plugin_main_file)) {
        $plugin_data = get_plugin_data($plugin_main_file);
        $plugin_version = $plugin_data['Version'];
    } else {
        $plugin_version = 'Unknown';
    }


?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style="max-width: 100%;" class="card">
                <h1>CyberXDC Updates & Licenses</h1>
                <div class="cyberxdc-content">
                    <div class="cyberxdc-update-info">
                        <h2>Update Plugin</h2>
                        <p>Welcome to the CyberXDC Updates & Licenses page!</p>
                        <p>Plugin Version: <?php echo $plugin_version; ?></p>
                        <p>Click the button below to update the plugin to the latest version.</p>
                        <br>
                        <br>
                        <form method="post">
                            <button type="submit" name="update_plugin" class="button button-primary">Update Plugin to Latest Version</button>
                        </form>
                        <br>
                        <br>
                    </div>
                    <hr>
                    <hr>
                    <div class="cyberxdc-license-info">
                        <h2>Licenses & Agreements</h2>
                        <h3>End User License Agreement (EULA)</h3>
                        <p>This End User License Agreement ("EULA") governs your use of the CyberXDC plugin ("the Software"). By using the Software, you agree to be bound by the terms of this EULA.</p>
                        <ol>
                            <li><strong>License Grant:</strong> CyberXDC grants you a non-exclusive, revocable, non-transferable license to use, copy, and modify the Software for personal and commercial purposes, without any restriction or limitation.</li>
                            <li><strong>Ownership:</strong> You acknowledge that CyberXDC retains all rights, title, and interest in and to the Software. This EULA does not grant you any ownership rights to the Software.</li>
                            <li><strong>Restrictions:</strong> You may not sublicense, sell, or distribute the Software, in whole or in part, without explicit permission from CyberXDC.</li>
                            <li><strong>Support:</strong> CyberXDC may provide support services related to the Software at its discretion, but is not obligated to do so.</li>
                            <li><strong>Warranty:</strong> THE SOFTWARE IS PROVIDED "AS IS," WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED. CYBERXDC DISCLAIMS ALL WARRANTIES, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NONINFRINGEMENT.</li>
                            <li><strong>Limitation of Liability:</strong> IN NO EVENT SHALL CYBERXDC BE LIABLE FOR ANY DAMAGES ARISING FROM THE USE OF THE SOFTWARE, INCLUDING BUT NOT LIMITED TO DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES.</li>
                            <li><strong>Indemnification:</strong> You agree to indemnify and hold harmless CyberXDC from any claims, damages, losses, liabilities, and expenses arising out of your use of the Software.</li>
                            <li><strong>Governing Law:</strong> This EULA shall be governed by and construed in accordance with the laws of [Your Jurisdiction].</li>
                            <li><strong>Entire Agreement:</strong> This EULA constitutes the entire agreement between you and CyberXDC regarding the Software.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
}

// Check if update button is clicked
add_action('admin_init', 'cyberxdc_handle_update_request');

function cyberxdc_handle_update_request()
{
    if (isset($_POST['update_plugin'])) {
        $update_result = cyberxdc_custom_update_functionality();

        if ($update_result === true) {
            add_action('admin_notices', 'cyberxdc_update_success_notice');
        } else {
            add_action('admin_notices', 'cyberxdc_update_failed_notice');
        }
    }
}

// Custom function to perform update functionality
function cyberxdc_custom_update_functionality()
{
    $download_url = 'http://cyberxdc.42web.io/dev-wp-plugin/cyberxdc.zip';
    $plugin_path = WP_PLUGIN_DIR . '/cyberxdc.zip';

    // Download the plugin ZIP file
    $downloaded = wp_remote_get($download_url, array('timeout' => 10));

    if (is_wp_error($downloaded) || $downloaded['response']['code'] !== 200) {
        return false;
    }

    // Save the ZIP file to the plugin directory
    $save_result = file_put_contents($plugin_path, $downloaded['body']);

    if ($save_result === false) {
        return false;
    }

    // Unzip the plugin file
    $unzip_result = unzip_file($plugin_path, WP_PLUGIN_DIR);

    if (is_wp_error($unzip_result)) {
        return false;
    }

    // Plugin updated successfully
    return true;
}

// Display success notice
function cyberxdc_update_success_notice()
{
?>
    <div class="notice notice-success is-dismissible">
        <p>Plugin updated successfully!</p>
    </div>
<?php
}

// Display failure notice
function cyberxdc_update_failed_notice()
{
?>
    <div class="notice notice-error is-dismissible">
        <p>Failed to update plugin. Please try again later.</p>
    </div>
<?php
}
