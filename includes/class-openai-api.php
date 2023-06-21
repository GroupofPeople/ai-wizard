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
		WC()->checkout()->get_posted_data();
		return static::$instance;
	}


	/**
	 * @throws Exception
	 */
	public function call( $prompt, $system, $args = array() ) {
		//define request body
		$body_array = array(
			'model'       => 'gpt-3.5-turbo',
			'messages'    => array(
				(object) array(
					'role'    => 'system',
					'content' => $system
				),
				(object) array(
					'role'    => 'user',
					'content' => $prompt
				)
			),
			'temperature' => 0.3
		);

		if ( isset( $args['temp'] ) ) {
			$body_array['temperature'] = (float) $args['temp'];
		}

		if ( isset( $args['top-p'] ) ) {
			$body_array['top_p'] = (float) $args['top-p'];
		}

		if ( isset( $args['max_tokens'] ) && 'off' != $args['max_tokens'] && isset( $args['max-tokens'] ) ) {
			$body_array['max_tokens'] = $args['max-tokens'];
		}

		$body = (object) $body_array;

		//define request
		$args = array(
			'timeout' => 30,
			'headers' => array(
				'Content-Type'  => 'application/json',
				'Authorization' => 'Bearer ' . get_option( 'CF7ChatGPT_settings' )['CF7ChatGPT_api_key']
			),
			'body'    => json_encode( $body )
		);

		//make request
		$response = wp_remote_post( self::API_URL, $args );

		if ( is_wp_error( $response ) ) {
			error_log( "ERROR wp_remote_post: \n" . print_r( $response, true ) );

			return false;
		}

		$response_body = json_decode( wp_remote_retrieve_body( $response ) );

		if ( isset( $response_body->error ) ) {
			throw new Exception( "ERROR OpenAI API: " . print_r( $response_body->error->message, true ) );
		}

		//extract body of request and write it to temp file -> audio data
		return $response_body->choices[0]->message->content;
	}
}