<?php
require_once plugin_dir_path(__FILE__) . '/settings/cyberxdc-general-settings.php';
require_once plugin_dir_path(__FILE__) . '/settings/cyberxdc-admin-file-upload.php';
require_once plugin_dir_path(__FILE__) . '/settings/cyberxdc-admin-block-file-type.php';
require_once plugin_dir_path(__FILE__) . '/settings/cyberxdc-admin-media-settings.php';
require_once plugin_dir_path(__FILE__) . '/settings/cyberxdc-admin-posts-settings.php';
require_once plugin_dir_path(__FILE__) . '/settings/cyberxdc-admin-pages-settings.php';
require_once plugin_dir_path(__FILE__) . '/settings/cyberxdc-admin-help.php';

// Callback function to display content for Settings page
function cyberxdc_settings_page()
{
    // Determine the current tab
    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'tools';
?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style="max-width: 100%;" class="wp-header card ">
                <div class="header-section">
                    <div class="">
                        <h1 class="wp-heading-inline">Settings</h1>
                        <p>Fine-tune your WordPress site with CyberXDC's comprehensive settings management. From global preferences to specific page, post, media, and file upload configurations, CyberXDC simplifies site optimization. Streamline workflow and enhance user experience with intuitive controls designed to maximize performance and efficiency. Empower your site with tailored settings that cater to your unique needs, ensuring seamless operation and scalability in your WordPress journey.</p>
                    </div>
                    <div class="container">
                        <h2 class="nav-tab-wrapper">
                            <a href="?page=cyberxdc-settings&tab=general" class="nav-tab <?php echo $tab == 'general' ? 'nav-tab-active' : ''; ?>">General Settings</a>
                            <a href="?page=cyberxdc-settings&tab=file-upload" class="nav-tab <?php echo $tab == 'file-upload' ? 'nav-tab-active' : ''; ?>">File Upload Settings</a>
                            <a href="?page=cyberxdc-settings&tab=media" class="nav-tab <?php echo $tab == 'media' ? 'nav-tab-active' : ''; ?>">Media Settings</a>
                            <a href="?page=cyberxdc-settings&tab=posts" class="nav-tab <?php echo $tab == 'posts' ? 'nav-tab-active' : ''; ?>">Posts Settings</a>
                            <a href="?page=cyberxdc-settings&tab=pages" class="nav-tab <?php echo $tab == 'pages' ? 'nav-tab-active' : ''; ?>">Pages Settings</a>
                            <a href="?page=cyberxdc-settings&tab=support" class="nav-tab <?php echo $tab == 'support' ? 'nav-tab-active' : ''; ?>">Help & Support</a>
                        </h2>
                    </div>
                </div>
                <br>
                <?php
                // Check if settings have been updated and display success notice
                if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
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
                switch ($tab) {
                    case 'file-upload':
                        cyberxdc_file_upload_type();
                        cyberxdc_block_file_type();
                        break;
                    case 'media':
                        cyberxdc_media_settings();
                        break;
                    case 'posts':
                        cyberxdc_post_settings();
                        break;
                    case 'pages':
                        cyberxdc_page_settings();
                        break;
                    case 'support':
                        cyberxdc_support_page();
                        break;
                    case 'general':
                    default:
                        cyberxdc_general_settings();
                        break;
                }
                ?>
            </div>
        </div>

    </div>
<?php
}
?>
