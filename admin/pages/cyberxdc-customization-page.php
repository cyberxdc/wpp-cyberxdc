<?php
function cyberxdc_customization_page()
{
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'login_page';
?>
    <div class="cyberxdc-wrap">
        <div class="container">
            <div style="max-width: 100%;" class="card">
                <h1>Customization</h1>
                <p class="desc">Personalize your WordPress site with CyberXDC's extensive customization options. Effortlessly modify the login page, inject custom CSS into headers and footers, and integrate scripts for enhanced functionality. Whether you're rebranding or refining user experience, CyberXDC empowers you to showcase your unique identity with ease. Simplify site management and elevate aesthetics with customizable solutions designed to amplify your WordPress presence.</p>
                <h2 class="nav-tab-wrapper">
                    <a href="?page=cyberxdc-customization&tab=login_page" class="nav-tab <?php echo $active_tab == 'login_page' ? 'nav-tab-active' : ''; ?>">Login Page</a>
                    <a href="?page=cyberxdc-customization&tab=custom_style" class="nav-tab <?php echo $active_tab == 'custom_style' ? 'nav-tab-active' : ''; ?>">Custom Style</a>
                    <a href="?page=cyberxdc-customization&tab=custom_script" class="nav-tab <?php echo $active_tab == 'custom_script' ? 'nav-tab-active' : ''; ?>">Custom Script</a>
                </h2>
            </div>
        </div>
        <div class="cyberxdc-wrap">
            <div class="container">
                <?php if ($active_tab == 'login_page') : ?>
                    <?php cyberxdc_login_page_tab(); ?>
                <?php elseif ($active_tab == 'custom_style') : ?>
                    <?php cyberxdc_custom_style_tab(); ?>
                <?php elseif ($active_tab == 'custom_script') : ?>
                    <?php cyberxdc_custom_script_tab(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php
}
function cyberxdc_login_page_tab()
{
    // Retrieve options from database
    $login_page_options = get_option('cyberxdc_login_page_settings');
    $background_color = isset($login_page_options['background_color']) ? $login_page_options['background_color'] : '';
    $text_color = isset($login_page_options['text_color']) ? $login_page_options['text_color'] : '';
    $background_image = isset($login_page_options['background_image']) ? $login_page_options['background_image'] : '';
    $logo_image = isset($login_page_options['logo_image']) ? $login_page_options['logo_image'] : '';
    if (isset($_POST['login_page_submit'])) {
        $background_color = sanitize_hex_color($_POST['background_color']);
        $text_color = sanitize_hex_color($_POST['text_color']);
        $background_image = esc_url($_POST['background_image']);
        $logo_image = esc_url($_POST['logo_image']);
        $logo_url = esc_url($_POST['logo_url']);

        $login_page_options = array(
            'background_color' => $background_color,
            'text_color' => $text_color,
            'background_image' => $background_image,
            'logo_image' => $logo_image,
            'logo_url' => $logo_url
        );

        update_option('cyberxdc_login_page_settings', $login_page_options);
        $notice = 'Settings saved successfully.';
    }
?>
<div class="login-tab-wrapper">
<div style="max-width: 100%;" class="card">
    <form method="post" action="" enctype="multipart/form-data">
        <h3>Login Page Settings</h3>
        <?php if (!empty($notice)) : ?>
            <div style="margin: 0px;" class="notice notice-success is-dismissible">
                <p><?php echo $notice; ?></p>
            </div>
        <?php endif; ?>
        <table class="form-table">
            <!-- Background Color Setting -->
            <tr>
                <th scope="row">
                    <label for="background_color">Background Color</label>
                </th>
                <td>
                    <input type="text" name="background_color" id="background_color" value="<?php echo esc_attr($background_color); ?>" class="color-picker" />
                    <div id="background_color_picker" class="color-picker"></div>
                    <p class="description">Select the background color for the login page. This will apply to the entire page behind the login form.</p>
                </td>
            </tr>
            <!-- Text Color Setting -->
            <tr>
                <th scope="row">
                    <label for="text_color">Text Color</label>
                </th>
                <td>
                    <input type="text" name="text_color" id="text_color" value="<?php echo esc_attr($text_color); ?>" class="color-picker" />
                    <div id="text_color_picker" class="color-picker"></div>
                    <p class="description">Choose the color for the text on the login page, including labels and links.</p>
                </td>
            </tr>
            <!-- Redirect Login Page Logo URL Setting -->
            <tr>
                <th scope="row">
                    <label for="logo_url">Redirect Login Page Logo URL</label>
                </th>
                <td>
                    <input type="text" name="logo_url" id="logo_url" value="<?php echo esc_url($logo_url); ?>" />
                    <p class="description">Set the URL that the login page logo should link to when clicked. Typically, this could be your site's homepage or a custom page.</p>
                </td>
            </tr>
            <!-- Background Image Setting -->
            <tr>
                <th scope="row">
                    <label for="background_image">Background Image</label>
                </th>
                <td>
                    <input type="text" name="background_image" id="background_image" value="<?php echo esc_url($background_image); ?>" />
                    <input type="button" name="upload_background_image_button" id="upload_background_image_button" class="button" value="Upload Image">
                    <div id="background_image_preview" style="margin-top: 10px;">
                        <?php if ($background_image) : ?>
                            <img src="<?php echo esc_url($background_image); ?>" alt="Background Image" style="max-width: 120px; margin-bottom: 10px; display: block;" />
                            <button type="button" id="delete_background_image_button" class="button button-danger">Delete Image</button>
                        <?php endif; ?>
                    </div>
                    <p class="description">Upload or select an image to use as the background for the login page. This image will display behind the login form.</p>
                </td>
            </tr>
            <!-- Logo Image Setting -->
            <tr>
                <th scope="row">
                    <label for="logo_image">Logo Image</label>
                </th>
                <td>
                    <input type="text" name="logo_image" id="logo_image" value="<?php echo esc_url($logo_image); ?>" />
                    <input type="button" name="upload_logo_image_button" id="upload_logo_image_button" class="button" value="Upload Image">
                    <div id="logo_image_preview" style="margin-top: 10px;">
                        <?php if ($logo_image) : ?>
                            <img src="<?php echo esc_url($logo_image); ?>" alt="Logo Image" style="max-width: 120px; margin-bottom: 10px; display: block;" />
                            <button type="button" id="delete_logo_image_button" class="button button-danger">Delete Image</button>
                        <?php endif; ?>
                    </div>
                    <p class="description">Upload or select an image to replace the default WordPress logo on the login page.</p>
                </td>
            </tr>
        </table>
        <!-- Submit Button -->
        <p class="submit">
            <input type="submit" name="login_page_submit" class="button-primary" value="Save Changes">
        </p>
    </form>
</div>
</div>

    <script>
        jQuery(document).ready(function($) {
            $('#upload_background_image_button').click(function() {
                var mediaUploader;
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media({
                    title: 'Choose Background Image',
                    button: {
                        text: 'Choose Image'
                    },
                    multiple: false
                });
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#background_image').val(attachment.url);
                    $('#background_image_preview').html('<img src="' + attachment.url + '" alt="Background Image" style="max-width: 200px; border:1px solid black; display: block;" />' +
                        '<button type="button" id="delete_background_image_button" class="button">Delete Image</button>');
                });
                mediaUploader.open();
            });

            $('#upload_logo_image_button').click(function() {
                var mediaUploader;
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media({
                    title: 'Choose Logo Image',
                    button: {
                        text: 'Choose Image'
                    },
                    multiple: false
                });
                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#logo_image').val(attachment.url);
                    $('#logo_image_preview').html('<img src="' + attachment.url + '" alt="Logo Image" style="max-width: 200px; display: block;" />' +
                        '<button type="button" id="delete_logo_image_button" class="button">Delete Image</button>');
                });
                mediaUploader.open();
            });

            // Delete background image
            $(document).on('click', '#delete_background_image_button', function() {
                $('#background_image').val('');
                $('#background_image_preview').html('');
            });

            // Delete logo image
            $(document).on('click', '#delete_logo_image_button', function() {
                $('#logo_image').val('');
                $('#logo_image_preview').html('');
            });
        });
    </script>
    <style>
        .login-tab-wrapper .wp-picker-container{
            position: relative;
            top: -6px;
        }
    </style>
<?php
}

// Enqueue scripts and styles for the color picker
function enqueue_color_picker() {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}

// Hook into admin enqueue scripts
add_action('admin_enqueue_scripts', 'enqueue_color_picker');

// Initialize the color picker on the input field
add_action('admin_footer', 'initialize_color_picker');
function initialize_color_picker() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        $('#background_color_picker').wpColorPicker({
            change: function(event, ui) {
                $('#background_color').val(ui.color.toString());
            },
            clear: function() {
                $('#background_color').val('');
            }
        });

        $('#text_color_picker').wpColorPicker({
            change: function(event, ui) {
                $('#text_color').val(ui.color.toString());
            },
            clear: function() {
                $('#text_color').val('');
            }
        });
    });
    </script>
    <?php
}


function cyberxdc_custom_style_tab()
{
    // Retrieve options from database
    $custom_style_options = get_option('cyberxdc_custom_style_settings');
    $header_css = isset($custom_style_options['header_css']) ? $custom_style_options['header_css'] : '';
    $footer_css = isset($custom_style_options['footer_css']) ? $custom_style_options['footer_css'] : '';

    // Handle form submission
    if (isset($_POST['custom_style_submit'])) {
        // Sanitize and save options
        $header_css = wp_kses_post($_POST['header_css']);
        $footer_css = wp_kses_post($_POST['footer_css']);

        $custom_style_options = array(
            'header_css' => $header_css,
            'footer_css' => $footer_css
        );

        update_option('cyberxdc_custom_style_settings', $custom_style_options);
        $notice = 'Settings saved successfully.';
    }
?>
    <div class="custom_style_wrapper">
        <div style="max-width: 100%;" class="card">
            <form method="post" action="">
                <h3>Custom Style Settings</h3>
                <?php if (!empty($notice)) : ?>
                    <div style="margin: 0px;" class="notice notice-success is-dismissible">
                        <p><?php echo $notice; ?></p>
                    </div>
                <?php endif; ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="header_css">Custom CSS for Header</label></th>
                        <td><textarea name="header_css" id="header_css" class="large-text" rows="5"><?php echo $header_css; ?></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_css">Custom CSS for Footer</label></th>
                        <td><textarea name="footer_css" id="footer_css" class="large-text" rows="5"><?php echo $footer_css; ?></textarea></td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="custom_style_submit" class="button-primary" value="Save Changes">
                </p>
            </form>
        </div>
    </div>
<?php
}

function cyberxdc_enqueue_custom_css()
{
    // Retrieve custom CSS options
    $custom_style_options = get_option('cyberxdc_custom_style_settings');
    $header_css = isset($custom_style_options['header_css']) ? $custom_style_options['header_css'] : '';
    $footer_css = isset($custom_style_options['footer_css']) ? $custom_style_options['footer_css'] : '';

    // Enqueue header CSS
    if (!empty($header_css)) {
        add_action('wp_head', function () use ($header_css) {
            echo '<style>' . $header_css . '</style>';
        });
    }

    // Enqueue footer CSS
    if (!empty($footer_css)) {
        add_action('wp_footer', function () use ($footer_css) {
            echo '<style>' . $footer_css . '</style>';
        });
    }
}

add_action('wp_enqueue_scripts', 'cyberxdc_enqueue_custom_css');


function cyberxdc_custom_script_tab()
{
    // Retrieve options from database
    $custom_script_options = get_option('cyberxdc_custom_script_settings');
    $header_script = isset($custom_script_options['header_script']) ? stripslashes($custom_script_options['header_script']) : '';
    $footer_script = isset($custom_script_options['footer_script']) ? stripslashes($custom_script_options['footer_script']) : '';


    // Handle form submission
    if (isset($_POST['custom_script_submit'])) {
        // Sanitize and save options
        $header_script = wp_kses_post($_POST['header_script']);
        $footer_script = wp_kses_post($_POST['footer_script']);

        $custom_script_options = array(
            'header_script' => $header_script,
            'footer_script' => $footer_script
        );

        update_option('cyberxdc_custom_script_settings', $custom_script_options);
        $notice = 'Settings saved successfully.';
    }
?>
    <div class="custom_script_wrapper">
        <div style="max-width: 100%;" class="card">
            <form method="post" action="">
                <h3>Custom Script Settings</h3>
                <?php if (!empty($notice)) : ?>
                    <div style="margin: 0px;" class="notice notice-success is-dismissible">
                        <p><?php echo $notice; ?></p>
                    </div>
                <?php endif; ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="header_script">Custom JavaScript for Header</label></th>
                        <td><textarea name="header_script" id="header_script" class="large-text" rows="5"><?php echo $header_script; ?></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="footer_script">Custom JavaScript for Footer</label></th>
                        <td><textarea name="footer_script" id="footer_script" class="large-text" rows="5"><?php echo $footer_script; ?></textarea></td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="custom_script_submit" class="button-primary" value="Save Changes">
                </p>
            </form>
        </div>
    </div>
<?php
}


function cyberxdc_enqueue_custom_scripts()
{
    $custom_script_options = get_option('cyberxdc_custom_script_settings');

    // Check if custom script options exist and if they are in the expected format
    if (is_array($custom_script_options) && isset($custom_script_options['header_script']) && isset($custom_script_options['footer_script'])) {
        $header_script = $custom_script_options['header_script'];
        $footer_script = $custom_script_options['footer_script'];

        // Enqueue header script
        if (!empty($header_script)) {
            wp_add_inline_script('jquery', $header_script);
        }

        // Enqueue footer script
        if (!empty($footer_script)) {
            wp_add_inline_script('jquery', $footer_script);
        }
    }
}

add_action('wp_enqueue_scripts', 'cyberxdc_enqueue_custom_scripts');
