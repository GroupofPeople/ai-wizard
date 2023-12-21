<?php

namespace aiwzrd\includes;

use Exception;

class OpenAI_API {
	const API_URL = "https://api.openai.com/v1/chat/completions";

	private static $instance = null;

	final public static function get_instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * @throws Exception
	 */
	public function call( $prompt, $system, $args = [] ) {
		$defaultArgs = [
			'temp' => 0.3,
		];

		$args = array_merge( $defaultArgs, $args );

		$messages = [
			(object) [
				'role'    => 'system',
				'content' => $system,
			],
			(object) [
				'role'    => 'user',
				'content' => $prompt,
			],
		];

		$body = (object) [
			'model'       => 'gpt-3.5-turbo',
			'messages'    => $messages,
			'temperature' => (float) $args['temp'],
		];

		if ( isset( $args['top-p'] ) ) {
			$body->top_p = (float) $args['top-p'];
		}

		if ( isset( $args['max_tokens'] ) && $args['max_tokens'] !== 'off' && isset( $args['max-tokens'] ) ) {
			$body->max_tokens = $args['max-tokens'];
		}

		$apiUrl = self::API_URL;
		$apiKey = get_option( 'AI_Wizard_OpenAI_settings' )['AIWizard_OpenAI_settings_api_key'];

		$headers = array(
			'content-type' => 'application/json',
			'Authorization' => ' Bearer ' . $apiKey,
		);

		$args = array(
			'body'    => wp_json_encode( $body ),
			'timeout' => 300,
			'headers' => $headers,
		);

		$response = wp_remote_post( $apiUrl, $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			throw new Exception( $error_message );
		}

		$responseJson = json_decode( wp_remote_retrieve_body( $response ) );

		if ( isset( $responseJson->error ) ) {
			throw new Exception( "ERROR OpenAI API: " . $responseJson->error->message );
		}

		return $responseJson->choices[0]->message->content;
	}
}