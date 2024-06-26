<?php 

/**
 * CyberXDC Admin Menus Class File
 * This file contains the CyberXDC admin menus.
 */ 


 class Cyberxdc_Admin_Menus {
    public function __construct() {
        add_action('admin_menu', array($this, 'cyberxdc_add_menu'));
    }

    // Add top-level CyberXDC menu item
    public function cyberxdc_add_menu() {
        add_menu_page(
            'CyberXDC',
            'CyberXDC',
            'manage_options',
            'cyberxdc',
            array($this, 'cyberxdc_dashboard_page'),
            'dashicons-admin-generic',   
            // plugins_url( 'cyberxdc/admin/images/cyberxdc-logo.svg' ),
            80
        );

        // Add submenus

        add_submenu_page(
            'cyberxdc',
            'SMTP Settings',
            'SMTP Settings',
            'manage_options',
            'cyberxdc-smtp',
            array($this, 'cyberxdc_smtp_page')
        );
        add_submenu_page(
            'cyberxdc',
            'Shortcodes',
            'Shortcodes',
            'manage_options',
            'cyberxdc-shortcodes',
            array($this, 'cyberxdc_shortcodes_page')
        );

        add_submenu_page(
            'cyberxdc',
            'Security',
            'Security',
            'manage_options',
            'cyberxdc-security',
            array($this, 'cyberxdc_security_page')
        );
        add_submenu_page(
            'cyberxdc',
            'Customization',
            'Customization',
            'manage_options',
            'cyberxdc-customization',
            array($this, 'cyberxdc_customization_page')
        );

        add_submenu_page(
            'cyberxdc',
            'Settings',
            'Settings',
            'manage_options',
            'cyberxdc-settings',
            array($this, 'cyberxdc_settings_page')
        );

        add_submenu_page(
            'cyberxdc',                        // Parent menu slug
            'Contact Form 7 Submissions',      // Page title
            'CF7 Submissions',                 // Menu title
            'manage_options',                  // Capability
            'cyberxdc-cf7-submissions',        // Menu slug
            'cyberxdc_render_cf7db_page'       // Callback function
        );
        
        add_submenu_page(
            'cyberxdc',
            'Logs & Activity',
            'Logs & Activity',
            'manage_options',
            'cyberxdc-logs',
            array($this, 'cyberxdc_logs_page')
        );        
        add_submenu_page(
            'cyberxdc',
            'Updates & Licenses',
            'Updates & Licenses',
            'manage_options',
            'cyberxdc-updates',
            array($this, 'cyberxdc_updates_page')
        );
        
    }

    // Callback function to display content for CyberXDC dashboard page
    public function cyberxdc_dashboard_page() {
        cyberxdc_dashboard_page();
    }

    // Callback function to display content for CyberXDC SMTP settings page
    public function cyberxdc_smtp_page() {
        cyberxdc_smtp_page();
    }
    // Callback functions for each submenu
    public function cyberxdc_settings_page() {
        cyberxdc_settings_page();
    }

    public function cyberxdc_customization_page() {
        cyberxdc_customization_page();
    }

    public function cyberxdc_shortcodes_page() {
        // Add your shortcodes page content here
        cyberxdc_shortcodes_page();
    }

    public function cyberxdc_logs_page() {
        // Add your logs page content here
        display_cyberxdc_logs_page();
    }

    public function cyberxdc_security_page() {
        // Add your security page content here
        cyberxdc_security_page();
    }

    public function cyberxdc_updates_page() {
        // Add your updates page content here
        cyberxdc_updates_page();
    }
    
}
