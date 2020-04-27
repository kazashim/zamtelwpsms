<?php
    if (!defined('WP_UNINSTALL_PLUGIN')) {
		exit();
	}
	
	// Delete the settings options
		delete_option('key');
		delete_option('senderid');
?>
