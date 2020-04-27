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
            private function MainPageHTML() {
				add_filter('admin_footer_text', function() {	// Add a footer that links to our website
					$this->AddAdminFooter();
				});
				
				$send_results_html = '';
				$send_results_msg_bubble = '';
				if (isset($_POST['send_sms'])) {
					$send_results = $this->SendSMS();
					
					//$send_results_html = '
					//	<pre>
					//		'.print_r($send_results, true).'
					//	</pre>
					//';
					
					$status = isset($send_results['status']) ? sanitize_text_field($send_results['status']) : '';
					
					if ($status == 'queued') {
						
						$contacts = isset($send_results['contacts']) ? sanitize_text_field($send_results['contacts']) : '';
						$senderid = isset($send_results['senderid']) ? sanitize_text_field($send_results['senderid']) : '';
						$message = isset($send_results['message']) ? sanitize_text_field($send_results['message']) : '';
					
						
						$send_results_html .= '
							<div class="notice notice-success is-dismissible">
								<p>Your message has been queued for delivery.</p>
							</div>
						';
						
						$send_results_msg_bubble .= '<div class="msg-bubble">';
							$send_results_msg_bubble .= '<div style="margin-bottom:5px;">';
								$send_results_msg_bubble .= '<b style="font-size:0.8em; font-weight:700;">Sent</b>';
								$send_results_msg_bubble .= '<b style="float:right; font-size:0.8em; font-weight:700;">'.$to.'</b>';
							$send_results_msg_bubble .= '</div>';
							$send_results_msg_bubble .= $body;
						$send_results_msg_bubble .= '</div>';
					}
					else {
						$send_results_html .= '
							<div class="notice notice-error is-dismissible">
								<p>There was an error sending your message.</p>
							</div>
							<div class="notice notice-error is-dismissible">
								<p>Please check and confirm that your settings are correct and that your recipient\'s phone number was entered correctly.</p>
							</div>
						';
					}
				}
