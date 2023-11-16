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

		$apiUrl = self::API_URL;
		$apiKey = get_option('AI_Wizard_OpenAI_settings')['AIWizard_OpenAI_settings_api_key'];

		$headers = array(
			'Content-Type: application/json',
			'Authorization: Bearer ' . $apiKey,
		);

		//debug

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);

		$response = curl_exec($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		if (curl_error($ch) !== '') {
			throw new Exception(curl_error($ch));
		}

		curl_close($ch);

		if ($responseCode !== 200) {
			throw new Exception();
		}

		$responseJson = json_decode($response);

		if (isset($responseJson->error)) {
			throw new Exception("ERROR OpenAI API: " . $responseJson->error->message);
		}

		return $responseJson->choices[0]->message->content;
	}
}