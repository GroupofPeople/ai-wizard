<?php

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_post_meta_by_key( 'ai-wizard_settings' );
delete_post_meta_by_key('ai-wizard_is_active');

unregister_setting( "AI_Wizard_options", "AI_Wizard_OpenAI_settings" );
unregister_setting( "AI_Wizard_options", "AI_Wizard_OpenAI_settings" );

delete_option( 'AI_Wizard_OpenAI_settings' );
delete_option( 'AI_Wizard_OpenAI_settings' );
