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
						
						$to = isset($send_results['contacts']) ? sanitize_text_field($send_results['contacts']) : '';
						$from = isset($send_results['senderid']) ? sanitize_text_field($send_results['senderid']) : '';
						$body = isset($send_results['message']) ? sanitize_text_field($send_results['message']) : '';
					
						
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
                $html = '
					<div class="wrap">
						<h1 class="wp-heading-inline"></h1>
						
						'.$send_results_html.'
						
						<form method="post" style="display:inline-block;">
							<input type="hidden" name="send_sms" value="1" />
							
							
								
							<div style="display:inline-block;">
								<div>
									<div><b>To Phone</b></div>
									<input type="text" name="contacts" placeholder="To Phone" value="" class="regular-text">
								</div>
								
								<br>
								
								<div>
									<div><b>Message</b></div>
									<textarea class="regular-text" name="message" style="height:120px;"></textarea>
								</div>
								
								<br>
								
								<div>
									<button type="submit" class="button button-primary">
										Send Message
									</button>
								</div>
							</div>
							
						</form>
							
					</div>
					
				';
					
				echo $html;
            }
            
            private function SettingsPageHTML() {
				
				add_filter('admin_footer_text', function() {	// Add a footer that links to our website
					$this->AddAdminFooter();
				});
				
				$saved_message = '';
				if (isset($_POST['save_settings'])) {	// If we need to try saving settings...
					$this->SaveSettings();
					$saved_message = '
						<div class="notice notice-success is-dismissible">
							<p>Saved Settings</p>
						</div>
					';
				}
				
				$key = get_option('key', '');
				$senderid = get_option('senderid', '');

                $html = '
                <div class="wrap">
                    <h1 class="wp-heading-inline">TwiSMS Settings</h1>
                    
                    '.$saved_message.'
                    
                    <form method="post">
                        <input type="hidden" name="save_settings" value="1" />
                        
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row">Zamtel key</th>
                                    <td>
                                        <input type="text" name="key" placeholder="zamtel key" value="'.$key.'" class="regular-text">
                                    </td>
                                </tr>
                               
                                <tr>
                                    <th scope="row">Sender ID</th>
                                    <td>
                                        <input type="text" name="senderid" placeholder="cynojine" value="'.$senderid.'" class="regular-text">
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"></th>
                                    <td>
                                        <button type="submit" class="button button-primary">
                                            Save
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Don\'t have a Twilio account?</th>
                                    <td>
                                        Visit Twilio\'s website to <a href="https://www.twilio.com/" target="_blank">Sign Up for your Twilio account</a> and retrieve your <b>Account SID</b>, <b>Auth Token</b>, and <b>Phone Number</b>.
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"></th>
                                    <td>
                                        <p class="description">
                                            Twilio is a cloud communications platform that allows users to send and receive text messages.
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                    </form>
                    
                    
                </div>
            ';
                
            echo $html;
        }

        private function SaveSettings() {
				
            // Get the settings
                $key = isset($_POST['key']) ? sanitize_text_field($_POST['key']) : '';
                $senderid = isset($_POST['senderid']) ? sanitize_text_field($_POST['senderid']) : '';
            
            // Save the settings
                update_option('key', $key);
                update_option('senderid', $senderid);
                
        }

        private function SendSMS() {
            $results = '';
            
            // Get phone and message
                $contacts = isset($_POST['contacts']) ? sanitize_text_field($_POST['contacts']) : '';
                $message = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';
                
            // Get the other relevant data
                $key = get_option('key', '');
                $senderid = get_option('senderid', '');
            
            // Send the SMS
                $results = $this->CURL_SendSMS($key, $twilio_auth_token, $senderid, $contacts, $message);
            
            return ($results);
        }
        private function CURL_SendSMS($key, $senderid, $contacts, $message) {
				
            // URL to send data to
                $url = 'http://bulksms.zamatel.co.zm/api/sms/send/batch?'.$key.'/Messages.json';
            
            
            // Set the data we will post to Twilio
                $data = array(
                    'From' => $senderid,
                    'To' => $contacts,
                    'Body' => $message,
                );
                
            // Set the authorization header
                $basic_auth = 'Basic ' . base64_encode($key);
                $headers = array( 
                    'Authorization' => $basic_auth,
                );
                
                $args = array(
                    'body' => $data,
                    'timeout' => '60',
                    'headers' => $headers,
                );
                
                $response = wp_remote_post($url, $args);
                $json = json_decode($response['body'], true);	// Convert the returned JSON string into an array
            
            return ($json);
        }
        
        
    // Message to output in the WordPress admin footer
        private function AddAdminFooter() {
            echo 'Plain Plugins | Check out our website at <a href="https://cynojine.com" target="_blank">plainplugins.altervista.org</a> for more plugins!';
        }
}

ZamSMS::Instantiate();	// Instantiate an instance of the class