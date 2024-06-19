<?php

require_once plugin_dir_path(__FILE__) . '/security/cyberxdc-general-security.php';
require_once plugin_dir_path(__FILE__) . '/security/cyberxdc-two-factor-login.php';
require_once plugin_dir_path(__FILE__) . '/security/cyberxdc-user-security-page.php';
require_once plugin_dir_path(__FILE__) . '/security/cyberxdc-database-security.php';
require_once plugin_dir_path(__FILE__) . '/security/cyberxdc-file-security.php';
require_once plugin_dir_path(__FILE__) . '/security/cyberxdc-firewalls-page.php';
function cyberxdc_security_page() {
    $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
    ?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style="max-width: 100%;" class="wp-header card">
                <div class="header-section">
                    <div class="">
                        <h1 class="wp-heading-inline">CyberXDC Security</h1>
                        <p>Welcome to the CyberXDC Settings page!</p>
                    </div>
                    <div class="container">
                        <h2 class="nav-tab-wrapper">
                            <a href="?page=cyberxdc-security&tab=general" class="nav-tab <?php echo $current_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
                            <a href="?page=cyberxdc-security&tab=user_security" class="nav-tab <?php echo $current_tab == 'user_security' ? 'nav-tab-active' : ''; ?>">User Security</a>
                            <a href="?page=cyberxdc-security&tab=database_security" class="nav-tab <?php echo $current_tab == 'database_security' ? 'nav-tab-active' : ''; ?>">Database Security</a>
                            <a href="?page=cyberxdc-security&tab=files_security" class="nav-tab <?php echo $current_tab == 'files_security' ? 'nav-tab-active' : ''; ?>">Files Security</a>
                            <a href="?page=cyberxdc-security&tab=firewalls" class="nav-tab <?php echo $current_tab == 'firewalls' ? 'nav-tab-active' : ''; ?>">Firewalls</a>
                            <a href="?page=cyberxdc-security&tab=two_factor" class="nav-tab <?php echo $current_tab == 'two_factor' ? 'nav-tab-active' : ''; ?>">Two Factor Authentication</a>
                        </h2>
                    </div>
                </div>
                <br>
                <?php
                // Check if settings have been updated and display success notice
                if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) {
                ?>
                    <div style="margin: 0px;" id="message" class="updated notice is-dismissible">
                        <p><strong><?php _e('Settings updated successfully.'); ?></strong></p>
                        <button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php _e('Dismiss this notice.'); ?></span></button>
                    </div>
                    <script>
                        jQuery(document).ready(function($) {
                            $('#message.notice.is-dismissible').on('click', '.notice-dismiss', function() {
                                $('#message').fadeOut();
                            });
                        });
                    </script>
                <?php
                }
                ?>
            </div>
            <div class="container">
                <?php
                switch ( $current_tab ) {
                    case 'general':
                        cyberxdc_general_security_page();
                        break;
                    case 'user_security':
                        cyberxdc_user_security_page();
                        break;
                    case 'database_security':
                        cyberxdc_database_security_page();
                        break;
                    case 'files_security':
                        cyberxdc_files_security_page();
                        break;
                    case 'firewalls':
                        cyberxdc_firewalls_page();
                        break;
                    case 'two_factor':
                        cyberxdc_two_factor_page();
                        break;
                    default:
                        cyberxdc_general_security_page();
                        break;
                }
                ?>
            </div>
        </div>

    </div>
    <?php
}
