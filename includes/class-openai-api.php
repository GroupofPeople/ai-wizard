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

		$headers = [
			'Content-Type' => 'application/json',
			'Authorization' => 'Bearer ' . get_option('AI_Wizard_OpenAI_settings')['AIWizard_OpenAI_settings_api_key'],
		];

		$apiUrl = self::API_URL;
		$apiKey = get_option('AI_Wizard_OpenAI_settings')['AIWizard_OpenAI_settings_api_key'];

		$headers = array(
			'Content-Type: application/json',
			'Authorization: Bearer ' . $apiKey,
		);

		//debug
		ob_start();

		$ch1 = curl_init();
		curl_setopt($ch1, CURLOPT_URL, 'https://api.openai.com/v1/models');
		curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch1, CURLOPT_HTTPHEADER, array(
			'Authorization: Bearer ' . $apiKey,
		));
		curl_setopt($ch1, CURLOPT_TIMEOUT, 3);


		curl_setopt($ch1, CURLOPT_VERBOSE, true);
		$out1 = fopen('php://output', 'w');
		curl_setopt($ch1, CURLOPT_STDERR, $out1);

		fclose($out1);
		$debug1 = ob_get_clean();
		error_log("Debug GET: \n".$debug1);

		curl_close($ch1);


		//debug
		ob_start();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_TIMEOUT, 3);


		curl_setopt($ch, CURLOPT_VERBOSE, true);
		$out = fopen('php://output', 'w');
		curl_setopt($ch, CURLOPT_STDERR, $out);

		$response = curl_exec($ch);
		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		fclose($out);
		$debug = ob_get_clean();
		error_log("Debug: ".$debug);

		curl_close($ch);

		$my_curl = curl_init(); //new cURL handler

		$my_array=array(
			CURLOPT_URL =>'https://www.example.com/my_script.php',
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_POSTFIELDS    => array(
				'f_name' => 'Alex',
				'l_name' => 'John',
			)
		);
		curl_setopt_array($my_curl, $my_array); // use the array

		$return_str= curl_exec($my_curl); // Execute and get data
		curl_close($my_curl); // close the handler

		echo $return_str;

		if ($responseCode !== 200) {
			error_log("ERROR API response code: {$responseCode}");
			error_log("ERROR API response body: {$response}");
			return false;
		}

		$responseJson = json_decode($response);

		if (isset($responseJson->error)) {
			throw new Exception("ERROR OpenAI API: " . $responseJson->error->message);
		}

		return $responseJson->choices[0]->message->content;
	}
}