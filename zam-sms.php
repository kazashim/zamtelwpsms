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