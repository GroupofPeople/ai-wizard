<?php

namespace aiwzrd\includes;

use WPCF7_Submission;

class Post_Handler {
	public function __construct() {
		add_filter( 'wpcf7_mail_tag_replaced', array( $this, "handle_mail_tag_replaced" ), 10, 4 );
	}

	public function handle_mail_tag_replaced( $replaced, $submitted, $html, $mail_tag ) {
		if ( $mail_tag->tag_name() != "_chat_gpt_answer" ) {
			return $replaced;
		}

		$submission = WPCF7_Submission::get_instance();
		$form = $submission->get_contact_form();
		$form_id = $form->id();
		$ai_wizard_form = AI_Wizard_Form::getInstance($form_id);

		if ( ! $ai_wizard_form->is_enabled() ) {
			return $replaced;
		}


		$prompt = $ai_wizard_form->get_prompt();
		$prompt = wpcf7_mail_replace_tags( $prompt );

		$response = '';
		try {
			$response = OpenAI_API::get_instance()->call( $prompt, $ai_wizard_form->get_system_prompt(), $ai_wizard_form->get_chat_gpt_settings() );
		}catch (\Exception $e){
			$submission->set_status('aborted');
			$messages = AI_Wizard_Form::getInstance($form_id)->get_messages();
			return $messages['msg-error'];
		}

		return $this->filter_response( $form_id, $response );
	}

	public function filter_response( $form_id, $response ) {
		$filterSettings = AI_Wizard_Form::getInstance($form_id)->get_response_filter();

		switch ($filterSettings['type']) {
			case 'number':
				preg_match('/\d+\.?\d*/', $response, $matches);
				return isset($matches[0]) ? $matches[0] : '';
			default:
				return $response;
		}
	}

	/**
	 * Special mail tag for site url
	 *
	 * @param string
	 * @param string
	 * @param string
	 *
	 * @return string
	 */
	public function special_mail_tag( $output, $name, $html ) {
		// For backwards compatibility
//		error_log("Name: ".$name);
		$name = preg_replace( '/^wpcf7\./', '_', $name );

		if ( '_chat_gpt_answer' == $name ) {
//			error_log("$output");
//			$form_id = WPCF7_Submission::get_instance()->get_contact_form()->id();
//
//			if ( ! get_post_meta( $form_id, 'chatgpt_is_active', true ) ) {
//				return $output;
//			}
//
//			$ai_wizard_form = AI_Wizard_Form::getInstance($form_id);
//
//			$prompt = $ai_wizard_form->get_prompt();
//			$prompt = wpcf7_mail_replace_tags( $prompt );
//
//			$response = OpenAI_API::get_instance()->call( $prompt, $ai_wizard_form->get_system_prompt(), $ai_wizard_form->get_chat_gpt_settings() );
//
//			$filtered_response = $this->filter_response( $form_id, $response );
//
//			return apply_filters( 'open_ai_chatgpt_formular_response', $filtered_response, $form_id );
//			$output = "test";
		}

		return $output;
	}
}