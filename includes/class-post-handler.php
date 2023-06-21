<?php

namespace AI_Wizard\Includes;

use WPCF7_Submission;

class Post_Handler {
	public function __construct() {
		add_filter( 'wpcf7_mail_tag_replaced', array( $this, "handle_mail_tag_replaced" ), 10, 4 );
		add_filter( 'wpcf7_special_mail_tags', array( $this, "special_mail_tag" ), 20, 3 );
		add_filter("wpcf7_feedback_response", function ($response, $result) {

			error_log('$response: '. print_r($response, true));
//			$response['status'] = "error";
//			$response["message"] = "a custom message";


			return $response;

		}, 10 , 2);
	}

	public function handle_mail_tag_replaced( $replaced, $submitted, $html, $mail_tag ) {
		if ( $mail_tag->tag_name() != "_chat_gpt_answer" ) {
			return $replaced;
		}
//		error_log( "Tags: " . print_r( $mail_tag->tag_name(), true ) );

		$form_id = WPCF7_Submission::get_instance()->get_contact_form()->id();

		if ( ! get_post_meta( $form_id, 'chatgpt_is_active', true ) ) {
			return $replaced;
		}


		global $wpdb;

		$request_settings = get_post_meta( $form_id, 'request_settings', true );
		$ai_wizard_form = AI_Wizard_From::getInstance($form_id);

		$prompt = $ai_wizard_form->get_prompt();
		$prompt = wpcf7_mail_replace_tags( $prompt );


		$response = OpenAI_API::get_instance()->call( $prompt, $ai_wizard_form->get_system_prompt(), $ai_wizard_form->get_chat_gpt_settings() );

		$filtered_response = $this->filter_response( $form_id, $response );
		$filtered_response = apply_filters( 'gofp_chatgpt_formular_response', $filtered_response, $form_id );

		/**
		 * Save new Value in DB
		 */
		if ( $this->is_db_active() ) {
			$cfdb                       = apply_filters( 'cfdb7_database', $wpdb );
			$table_name                 = $cfdb->prefix . 'db7_forms';
			$result                     = $wpdb->get_results( "SELECT * FROM $table_name WHERE form_post_id = $form_id ORDER BY form_id DESC LIMIT 0, 1" );
			$entry                      = $result[0];
			$form_data                  = unserialize( $entry->form_value );
			$form_data[ '_chat_gpt_answer' ] = $filtered_response;

			$serialized_form_data = serialize( $form_data );

			$wpdb->update( $table_name, array( 'form_value' => $serialized_form_data ), array( "form_id" => $entry->form_id ) );
		}
		return $filtered_response;
	}

	public function filter_response( $form_id, $response ) {
		$filter_method = AI_Wizard_From::getInstance($form_id)->get_response_filter()['type'];

		switch ( $filter_method ) {
			case 'number':
				$regex = '/\d+\.?\d*/';
				break;
			case 'regex':
				$regex = AI_Wizard_From::getInstance($form_id)->get_response_filter()['custom-regex'];
				break;
			default:
				return $response;
		}

		preg_match( $regex, $response, $matches );

		return $matches[0];
	}

	private function is_db_active() {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

		foreach ( $active_plugins as $plugin ) {
			if ( strpos( $plugin, 'contact-form-cfdb-7' ) ) {
				return true;
			}
		}

		return false;
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
		$name = preg_replace( '/^wpcf7\./', '_', $name );

		if ( '_chat_gpt_answer' == $name ) {
			// Get the site url
			$output = "test";
		}

		return $output;
	}
}