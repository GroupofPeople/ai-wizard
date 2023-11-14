<?php

namespace AI_Wizard\Admin;


class Settings_Page {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'api_settings_init' ) );
	}

	public function add_settings_page() {
		add_options_page( 'AIWizard', 'AIWizard', 'manage_options', 'ai-wizard-settings-page', array(
			$this,
			'render_settings_page'
		) );
	}

	public function render_settings_page() {
		global $title;
		?>
        <div class="wrap" id="ai_wizard_settings">

            <h1 class="wp-heading-inline"><?php echo esc_html( $title ); ?></h1>
            <form action='options.php' method='post'>
				<?php settings_fields( 'AI_Wizard_options' ); ?>

                <h2><?php esc_html_e( 'OpenAI Settings', 'AI_Wizard_Settings' ) ?></h2>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th>
                            <label for="none">OpenAI API Path</label>
                        </th>
                        <td>
                            <div style="width:30em; background-color: #f0f0f1; padding: 0 8px; line-height: 2; min-height: 30px;
                                box-shadow: 0 0 0 transparent; border-radius: 4px; border: 1px solid #8c8f94; color: #2c3338;
                                margin: 0 1px; font-size: 14px; display: inline-block; box-sizing: border-box;">
                                https://api.openai.com/v1/chat/completions
                            </div>
                        </td>
                    </tr>
					<?php do_settings_fields( 'AI_Wizard_options', 'AIWizard_OpenAI_settings_section' ); ?>
                    </tbody>
                </table>
				<?php submit_button(); ?>
            </form>
        </div>
		<?php
	}

	public function api_settings_init() {
		register_setting( "AI_Wizard_options", "AI_Wizard_OpenAI_settings" );

		add_settings_section( 'AI_Wizard_OpenAI_settings_section', 'OpenAI Settings', array(
			$this,
			'api_settings_section'
		), 'AI_Wizard_options' );

		add_settings_field( 'AIWizard_OpenAI_settings_api_key', __( 'OpenAI API Key', 'AI_Wizard_Settings' ), array(
			$this,
			'render_api_key_field'
		), 'AI_Wizard_options', 'AIWizard_OpenAI_settings_section' );
	}

	public function render_api_key_field() {
		$options = get_option( 'AI_Wizard_OpenAI_settings' );
		?>
        <input type='text' style="width: 30em" name='AI_Wizard_OpenAI_settings[AIWizard_OpenAI_settings_api_key]'
               value='<?php echo esc_attr($options['AIWizard_OpenAI_settings_api_key']); ?>'/>
		<?php
	}

}