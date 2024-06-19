<?php
function cyberxdc_firewalls_page()
{
    // Check if form is submitted
    if (isset($_POST['submit_firewalls'])) {
        // Check if firewall is enabled
        $firewall_enabled = isset($_POST['firewall_enabled']) ? 1 : 0;
        update_option('cyberxdc_firewall_enabled', $firewall_enabled);

        $notice = 'Firewall settings saved successfully.';
    }

    // Get current firewall status
    $firewall_enabled = get_option('cyberxdc_firewall_enabled', 0);
?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style="max-width: 100%;" class="wp-header card">
                <h2>Firewall Settings</h2>
                <p>Enable or disable the firewall for added security.</p>
                <hr>
                <?php if (isset($notice)) : ?>
                    <div style="margin: 0px;" class="notice notice-success is-dismissible ">
                        <p><strong>Success:</strong> <?php echo $notice; ?></p>
                    </div>
                <?php endif; ?>
                <br>
                <br>
                <form method="post" action="">
                    <label for="firewall_enabled">
                        <input type="checkbox" id="firewall_enabled" name="firewall_enabled" <?php checked($firewall_enabled, 1); ?>>
                        Enable Firewall
                    </label><br>
                    <br>
                    <br>
                    <?php if ($firewall_enabled) : ?>
                        <div style="margin: 0px;" class="notice notice-info">
                            <p><strong>Warning:</strong> Enabling the firewall may affect certain functionalities on your site. Make sure to thoroughly test after enabling.</p>
                        </div>
                    <?php endif; ?>
                    <br>
                    <br>
                    <input type="submit" name="submit_firewalls" class="button-primary" value="Save Changes">
                </form>
            </div>
        </div>
    </div>
<?php
}
?>