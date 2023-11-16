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
}