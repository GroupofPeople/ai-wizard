<?php

/*
 * Plugin Name:     AI-Wizard
 * Plugin URI:      https://ai-wizard.groupofpeople.net/
 * Description:     AI-Wizard is a powerful WordPress plugin that seamlessly integrates OpenAI's ChatGPT technology with Contact Form 7 (CF7) forms.
 * Version:         1.0.0
 * Author:          GroupOfPeople-Alex
 * License:         GPL3
 * Text Domain:     ai-wizard
 * Domain Path:     /languages
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