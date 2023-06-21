<?php

/*
Plugin Name: AI-Wizard
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 0.9.5
Author: alex
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

// Exit if accessed directly.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


const gofpChatGPTFile = __FILE__;
define( "gofpChatGPTPath", dirname( __FILE__ ) );
define( "gofpChatGPTURL", plugin_dir_url( gofpChatGPTFile ) );


// Include autoloader.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-autoloader.php';


use AI_Wizard\Plugin;

/**
 * Get and initialize the plugin instance.
 *
 * @return Singleton|Plugin|null Plugin instance
 * @since 1.0.0
 */
function wp_chatGPT_builder() {
	// To prevent parse error for PHP prior to 5.3.0.
//    $class = '\GofP_Grid\Plugin';
	return Plugin::get_instance();
}

// Initialize plugin.
wp_chatGPT_builder();