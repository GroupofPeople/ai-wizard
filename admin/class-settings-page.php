<?php

namespace AI_Wizard\Admin;


use AI_Wizard\Includes\Licensing\EDD_Integration;

class Settings_Page {

	private static $MESSAGE;

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
		$status = get_option( 'AI_Wizard_license_status' );
		if ( get_option( 'AI_Wizard_license_key' ) != '' ) {
			EDD_Integration::get_instance()->check_license();
		}
		?>
        <div class="wrap" id="ai_wizard_settings">
            <!--			--><?php
			//			print_r( self::$MESSAGE );
			//			if ( isset( self::$MESSAGE ) ) {
			//				?>
            <!--                <div class="notice notice-info is-dismissible wp-mail-smtp-review-notice">-->
            <!--                    <div>-->
            <!--						--><?php //print_r( self::$MESSAGE ); ?>
            <!--                    </div>-->
            <!--                </div>-->
            <!--				--><?php
			//			}
			//			?>
            <h1 class="wp-heading-inline"><?php echo esc_html( $title ); ?></h1>
            <form action='options.php' method='post'>
				<?php settings_fields( 'AI_Wizard_options' ); ?>

                <h2><?php echo __( 'AIWizard Settings', 'AI_Wizard_Settings' ) ?></h2>
                <table class="form-table license-<?php echo get_option( 'AI_Wizard_license_status' );?>">
                    <tbody>
					<?php do_settings_fields( 'AI_Wizard_options', 'AI_Wizard_license_section' ); ?>
                    <tr>
                        <th>
							<?php if ( $status != EDD_Integration::LICENSE_NONE ) { ?>
                                <input type="submit" class="button button-primary" name="ai_wizard_deactivate"
                                       value="Deactivate License"/>
							<?php } else { ?>
                                <input type="submit" class="button button-primary" name="ai_wizard_activate"
                                       value="Activate License"/>
							<?php } ?>
                        </th>
                        <td>
							<?php
							$this->render_license_status();
							?>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <h2><?php echo __( 'OpenAI Settings', 'AI_Wizard_Settings' ) ?></h2>
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

	private function render_license_status() {
		?>
        <style>
            .button.ai-wizard-status.ai-wizard-status-error, .button.ai-wizard-status.ai-wizard-status-error:hover {
                background: #d63638;
                border-color: #d63638;
                color: #ffffff;
            }

            .button.ai-wizard-status.ai-wizard-status-valid, .button.ai-wizard-status.ai-wizard-status-valid:hover {
                background: #00a32a;
                border-color: #00a32a;
                color: #ffffff;
            }

            .button.ai-wizard-status, .button.ai-wizard-status:hover {
                cursor: default !important;
                font-weight: bold;
                background: #8c8f94;
                border-color: #8c8f94;
                color: #000000;
            }
        </style>
		<?php
		switch ( get_option( 'AI_Wizard_license_status' ) ) {
			case EDD_Integration::LICENSE_VALID:
				?>
                <div class="button ai-wizard-status ai-wizard-status-valid">License is activated and valid</div>
				<?php
				break;
			case EDD_Integration::LICENSE_EXPIRED:
				?>
                <div class="button ai-wizard-status ai-wizard-status-error">License is expired</div>
				<?php
				break;
			case EDD_Integration::LICENSE_DISABLED:
				?>
                <div class="button ai-wizard-status ai-wizard-status-error">License is disabled</div>
				<?php
				break;
			default:
				?>
                <div class="button ai-wizard-status">No license activated</div>
			<?php
		}
		?>
        </div>
		<?php
	}

	public function api_settings_init() {
		$this->openai_settings_init();
		$this->license_settings_init();

	}

	private function openai_settings_init() {
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

	private function license_settings_init() {
		register_setting( 'AI_Wizard_options', 'AI_Wizard_license_key', array(
			'sanitize_callback' => array(
				$this,
				'sanitize_license'
			)
		) );

		add_settings_section( 'AI_Wizard_license_section', 'License Settings', array(
			$this,
			'api_license_settings_section'
		), 'AI_Wizard_options' );


		add_settings_field( 'AI_Wizard_license_key_field', __( 'License Key', 'AI_Wizard_Settings' ), array(
			$this,
			'render_license_key_field'
		), 'AI_Wizard_options', 'AI_Wizard_license_section' );
	}

	public function sanitize_license( $input ) {
		$old_license = get_option( 'AI_Wizard_license_key' );

		$new_license = $input['AI_Wizard_license_key_field'];

		if ( isset( $_POST['submit'] ) ) {
			return $old_license;
		}

		if ( isset( $_POST['ai_wizard_deactivate'] ) ) {
			EDD_Integration::get_instance()->deactivate_license();
			update_option( 'AI_Wizard_license_status', EDD_Integration::LICENSE_NONE );
		}

//		if ( $old_license == $new_license ) {
//			return $old_license;
//		}

		//pressed submit button not license


//        EDD_Integration::get_instance()->check_license();
		$current_status = get_option( 'AI_Wizard_license_status' );

		if ( isset( $_POST['ai_wizard_activate'] ) ) {
			$response = EDD_Integration::get_instance()->activate_license( $new_license );
//            error_log('Response ai_wizard_activate: '.print_r($response, true));

			if ( $response != EDD_Integration::LICENSE_VALID ) {
				$message = '';
				switch ( $response ) {
					case EDD_Integration::LICENSE_MISSING :
						$message = __( "Or Wrong Key No License key was inserted. Please provide an License Key in order to perform the activation ", "AI_Wizard_Settings" );
						break;
					case EDD_Integration::LICENSE_NO_ACTIVATION_LEFT :
						$message = __( "No Activations left", "AI_Wizard_Settings" );
						break;
					case EDD_Integration::LICENSE_EXPIRED :
						$message = __( "Expired", "AI_Wizard_Settings" );
						break;
					case EDD_Integration::LICENSE_DISABLED :
						$message = __( "Disabled", "AI_Wizard_Settings" );
						break;


					//Maybe not needed
					case EDD_Integration::LICENSE_INVALID :
						$message = __( "Invalid", "AI_Wizard_Settings" );
						break;
					case EDD_Integration::LICENSE_MISMATCH :
						$message = __( "Mismatch", "AI_Wizard_Settings" );
						break;

					default:
						$message = __( "An error occurred during your activation request. Please try again later.", "AI_Wizard_Settings" );

				}
				add_settings_error( 'AI_Wizard_license_key', 'AI_Wizard_license_error', $message, 'error' );
			}
		}

		return $new_license;
	}

	public function render_api_key_field() {
		$options = get_option( 'AI_Wizard_OpenAI_settings' );
		?>
        <input type='text' style="width: 30em" name='AI_Wizard_OpenAI_settings[AIWizard_OpenAI_settings_api_key]'
               value='<?php echo $options['AIWizard_OpenAI_settings_api_key']; ?>'/>
		<?php
	}

	public function api_settings_section() {
		?>
        <h2>Section</h2>
		<?php
	}

	public function render_license_key_field() {
		$license_key = get_option( 'AI_Wizard_license_key' );
		$status      = get_option( 'AI_Wizard_license_status' );
		?>
        <style>
            .license-disabled input[name='AI_Wizard_license_key[AI_Wizard_license_key_field]']{
                background: #8c8f94;
            }
        </style>
        <input type='text' style="width: 30em" name='AI_Wizard_license_key[AI_Wizard_license_key_field]'
               value='<?php echo $license_key; ?>'
			<?php echo $status === EDD_Integration::LICENSE_VALID ? 'disabled' : ''; ?>
        >
		<?php
	}

	public function api_license_settings_section() {

	}
}