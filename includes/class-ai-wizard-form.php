<?php

namespace AI_Wizard\Includes;

use ErrorException;

class AI_Wizard_Form {

	private static $METADATA_KEY_ENABLED = 'ai-wizard_is_active';
	private static $METADATA_KEY_SETTINGS = 'ai-wizard_settings';

	private $form_id;

	private $enabled;

	private $messages;

	private $chat_gpt_settings;

	private $prompt;

	private $system_prompt;

	private $response_filter;

	private function __construct() {

	}

	public static function once_enabled() {
		$args = array( 'post_type' => 'wpcf7_contact_form', 'posts_per_page' => - 1 );

		if ( $forms = get_posts( $args ) ) {
			foreach ( $forms as $form ) {
				if ( self::getInstance( $form->ID )->is_enabled() ) {
					return true;
				}
			}
			return false;
		} else {
			return false;
		}
	}

	/**
	 * Returns whether the extension is enabled for the form
	 * @return bool
	 */
	public function is_enabled() {
		return $this->enabled;
	}

	/**
	 * @throws ErrorException
	 */
	public static function getInstance( $form_id = null ) {
		if ( $form_id == null ) {
			$instance = new self();
			$instance->set_defaults();

			return $instance;
		}
		if ( $form_id == - 1 ) {
			global $wpdb;

			$query = "SELECT ID FROM $wpdb->posts ORDER BY ID DESC LIMIT 0,1";

			$result  = $wpdb->get_results( $query );
			$row     = $result[0];
			$id      = $row->ID;
			$post_id = $id + 1;

			$instance = new self();
			$instance->set_defaults();

			return $instance;
		}

		if ( ! get_post_status( $form_id ) ) {
			//ToDo: Exception
			throw new ErrorException( "Not a valid post id" );
		}
		if ( get_post( $form_id )->post_type != 'wpcf7_contact_form' ) {
			//ToDo: Exception
			throw new ErrorException( "Given post is not a cf7 form" );
		}

		$instance          = new self();
		$instance->form_id = $form_id;
		$instance->set_defaults();
		$instance->load_instance();

		return $instance;
	}

	private function set_defaults() {
		$this->enabled           = false;
		$this->messages          = array(
			'msg-error'   => 'An error occurred during your request. Please try again later.',
			'msg-waiting' => 'Your answer is being generated...',
		);
		$this->chat_gpt_settings = array(
			'temp'            => 1,
			'top-p'           => 1,
			'max-tokens-bool' => 'off', //ToDo check if it is useful als on off or bool
			'max-tokens'      => 4096,
		);
		$this->prompt            = '';
		$this->system_prompt     = '';
		$this->response_filter   = array(
			'type'         => 'text',
			'custom-regex' => '',
		);
	}

	/**
	 * Function to load the AI Wizard form data from db and apply the data to the current instance
	 */
	private function load_instance() {
		$enabled = get_post_meta( $this->form_id, self::$METADATA_KEY_ENABLED, true );


		if ( isset( $enabled ) ) {
			$this->enabled = $enabled;
		}

		$form_extensions = get_post_meta( $this->form_id, self::$METADATA_KEY_SETTINGS, true );

		if ( isset( $form_extensions['prompt'] ) ) {
			$this->prompt = $form_extensions['prompt'];
		}
		if ( isset( $form_extensions['system-prompt'] ) ) {
			$this->system_prompt = $form_extensions['system-prompt'];
		}
		if ( isset( $form_extensions['response-filter'] ) ) {
			$this->response_filter = $form_extensions['response-filter'];
		}
		if ( isset( $form_extensions['messages'] ) ) {
			$this->merge_metadata_messages( $form_extensions['messages'] );
		}
		if ( isset( $form_extensions['chat-gpt-settings'] ) ) {
			$this->merge_metadata_chat_gpt_settings( $form_extensions['chat-gpt-settings'] );
		}
	}

	private function merge_metadata_messages( $messages ) {
		foreach ( $this->messages as $msg_type => $msg ) {
			if ( isset( $messages[ $msg_type ] ) ) {
				$this->messages[ $msg_type ] = $messages[ $msg_type ];
			}
		}
	}

	private function merge_metadata_chat_gpt_settings( $chat_gpt_settings ) {
		foreach ( $this->chat_gpt_settings as $setting_type => $setting ) {
			if ( isset( $chat_gpt_settings[ $setting_type ] ) ) {
				$this->chat_gpt_settings[ $setting_type ] = $chat_gpt_settings[ $setting_type ];
			}
		}
	}

	/**
	 * Returns the ID of the associated form
	 * @return int form_id
	 */
	public function get_form_id() {
		return $this->form_id;
	}

	/**
	 * Function to activate the AI Wizard form extension for the corresponding form
	 */
	public function enable() {
		//No need to check if it is already enabled
		if ( $this->enabled ) {
			return;
		}
		if (self::once_enabled()) {
			return;
		}
		$this->enabled = true;
		$this->update_enable();
	}

	/**
	 * Internal function to update the metadata field enabled
	 */
	private function update_enable() {
		update_post_meta( $this->form_id, self::$METADATA_KEY_ENABLED, $this->enabled );
	}

	/**
	 * Function to deactivate the AI Wizard form extension for the corresponding form
	 */
	public function disable() {
		if ( ! $this->enabled ) {
			return;
		}
		$this->enabled = false;
		$this->update_enable();
	}

	/**
	 * Returns the messages
	 * @return array
	 */
	public function get_messages() {
		return $this->messages;
	}

	/**
	 * @param mixed $messages
	 */
	public function set_messages( $messages ) {
		$this->messages = $messages;
		$this->update_metadata_settings();
	}

	private function update_metadata_settings() {
		$settings       = array(
			'chat-gpt-settings' => $this->chat_gpt_settings,
			'messages'          => $this->messages,
			'prompt'            => $this->prompt,
			'system-prompt'     => $this->system_prompt,
			'response-filter'   => $this->response_filter,
		);
		$caller         = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 2 )[1];
		$callerFunction = $caller['function'];
		$callerClass    = isset( $caller['class'] ) ? $caller['class'] : '';
		$callerFile     = $caller['file'];
		$callerLine     = $caller['line'];

		error_log( "Caller function: {$callerFunction}" );
		error_log( "Caller class: {$callerClass}" );
		error_log( "Caller file: {$callerFile}" );
		error_log( "Caller line: {$callerLine}" );
		$startTime = microtime( true );

		update_post_meta( $this->form_id, self::$METADATA_KEY_SETTINGS, $settings );

		$endTime       = microtime( true );
		$executionTime = $endTime - $startTime;

		error_log( 'update_post_meta time: ' . $executionTime );
	}

	/**
	 * Returns the Settings used for ChatGPT
	 * @return array
	 */
	public function get_chat_gpt_settings() {
		return $this->chat_gpt_settings;
	}

	/**
	 * @param mixed $chat_gpt_settings
	 */
	public function set_chat_gpt_settings( $chat_gpt_settings ) {
		$this->chat_gpt_settings = $chat_gpt_settings;
		$this->update_metadata_settings();
	}

	/**
	 * @return mixed
	 */
	public function get_prompt() {
		return $this->prompt;
	}

	/**
	 * @param mixed $prompt
	 */
	public function set_prompt( $prompt ) {
		$this->prompt = $prompt;
		$this->update_metadata_settings();
	}

	/**
	 * @return mixed
	 */
	public function get_system_prompt() {
		return $this->system_prompt;
	}

	/**
	 * @param mixed $system_prompt
	 */
	public function set_system_prompt( $system_prompt ) {
		$this->system_prompt = $system_prompt;
		$this->update_metadata_settings();
	}

	/**
	 * @return mixed
	 */
	public function get_response_filter() {
		return $this->response_filter;
	}

	/**
	 * Delete the metadata for this instance
	 */
	public function delete() {
		delete_post_meta( $this->form_id, self::$METADATA_KEY_ENABLED );
		delete_post_meta( $this->form_id, self::$METADATA_KEY_SETTINGS );
	}

	/**
	 * @param mixed $response_filter_type text|number|regex
	 */
	public function set_response_filter_type( $response_filter_type ) {
		$this->response_filter['type'] = $response_filter_type;
		$this->update_metadata_settings();
	}

	/**
	 * @param mixed $regex
	 */
	public function set_response_filter_regex( $regex ) {
		$this->response_filter['custom-regex'] = $regex;
		$this->update_metadata_settings();
	}
}