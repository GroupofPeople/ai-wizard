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


const aiwzrd_file = __FILE__;
define( "aiwzrd_Path", dirname( __FILE__ ) );
define( "aiwzrd_URL", plugin_dir_url( aiwzrd_file ) );


// Include autoloader.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-autoloader.php';



use aiwzrd\Plugin;
/**
 * Get and initialize the plugin instance.
 *
 * @return Plugin|null Plugin instance
 * @since 1.0.0
 */
function aiwzrd_builder() {
	return Plugin::get_instance();
}

// Initialize plugin.
aiwzrd_builder();