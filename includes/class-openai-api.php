<?php

namespace AI_Wizard\Includes;

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
	public function call($prompt, $system, $args = []) {
		$defaultArgs = [
			'temp' => 0.3,
		];

		$args = array_merge($defaultArgs, $args);

		$messages = [
			(object) [
				'role' => 'system',
				'content' => $system,
			],
			(object) [
				'role' => 'user',
				'content' => $prompt,
			],
		];

		$body = (object) [
			'model' => 'gpt-3.5-turbo',
			'messages' => $messages,
			'temperature' => (float) $args['temp'],
		];

		if (isset($args['top-p'])) {
			$body->top_p = (float) $args['top-p'];
		}

		if (isset($args['max_tokens']) && $args['max_tokens'] !== 'off' && isset($args['max-tokens'])) {
			$body->max_tokens = $args['max-tokens'];
		}

		$headers = [
			'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . get_option('AI_Wizard_OpenAI_settings')['AIWizard_OpenAI_settings_api_key'],
		];

		$requestArgs = [
			'timeout' => 30,
			'headers' => $headers,
			'body' => wp_json_encode($body),
		];

		$response = wp_remote_post(self::API_URL, $requestArgs);

		if (is_wp_error($response)) {
			error_log("ERROR wp_remote_post: \n" . print_r($response, true));
			return false;
		}

		$responseCode = wp_remote_retrieve_response_code($response);
		$responseBody = wp_remote_retrieve_body($response);

		if ($responseCode !== 200) {
			error_log("ERROR API response code: {$responseCode}");
			error_log("ERROR API response body: {$responseBody}");
			return false;
		}

		$responseJson = json_decode($responseBody);

		if (isset($responseJson->error)) {
			throw new Exception("ERROR OpenAI API: " . $responseJson->error->message);
		}

		return $responseJson->choices[0]->message->content;
	}
}