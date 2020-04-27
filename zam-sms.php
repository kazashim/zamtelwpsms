<?php
	/*
		Plugin Name: ZamtelSMS
		Description: Use ZamtalBulkSMS to send SMS from your website!
		Version: 1.0.0
		Author: Cynojine Technologu
    */
    
    if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
    }
    

    class ZamSMS {
// - Get the static instance variable
private static $_instance = null;
		
		
public static function Instantiate() {
    if (is_null(self::$_instance)) {
        self::$_instance = new self();
    }
    return self::$_instance;

    }

    private function __construct() {
			
        // - Side Menu (which loads the back-end)
            add_action('admin_menu', function() {
                $this->AdminMenu();
            });
    }


    // Main Admin Page Menu

			private function AdminMenu() {
				
				// Output the main menu item in the left menu				
					add_menu_page(
						'ZamSMS', 
						'ZamSMS', 
						'manage_options', 
						'zam-sms', 
						function() {
							$this->MainPageHTML();
						}, 
						'dashicons-admin-comments'
					);
					
				// Output the second menu item
					add_submenu_page(
						'zam-sms', 
						'ZamSMS Settings',
						'Settings',
						'manage_options',
						'zam-sms-settings',
						function() {
							$this->SettingsPageHTML();
						},
						1
					);
					
			}
}