<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_post_meta_by_key( 'request_settings' );
delete_post_meta_by_key('chatgpt_is_active');

unregister_setting( "CF7ChatGPT", "CF7ChatGPT_settings" );

delete_option( 'CF7ChatGPT_settings' );
