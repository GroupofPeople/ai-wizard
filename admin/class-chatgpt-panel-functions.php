<?php

namespace AI_Wizard\Admin;

use AI_Wizard\Includes\AI_Wizard_From;

class ChatGPT_Panel_Functions {

	function __construct() {
		add_filter( 'wpcf7_editor_panels', array( $this, 'editor_panels' ) );
		add_action( 'wpcf7_save_contact_form', array( $this, 'save_contact_form' ), 1, 3 );
		add_action( 'deleted_post', array( $this, 'delete_form' ), 1, 2 );
	}

	public function delete_form( $postid, $post ) {
		if ( $post->post_type == 'wpcf7_contact_form' ) {
			AI_Wizard_From::getInstance($post->ID)->delete();
		}
	}

	public function save_contact_form( $contact_form, $args, $context ) {
		$post_id = $args['id'];
		$args    = wp_parse_args( $args['gofp-wpcf7-chatgpt'], array(
			'prompt-template'       => "",
			'system-prompt'         => "",
			'response-filter'       => "",
			'response-filter-regex' => "",
			'temp'                  => 1,
			'top-p'                 => 1,
			'max-tokens-bool'       => 'off',
			'max-tokens'            => null,
			'active'                => 'off',
		) );

		if ( $post_id == - 1 ) {
			global $wpdb;

			$query = "SELECT ID FROM $wpdb->posts ORDER BY ID DESC LIMIT 0,1";

			$result  = $wpdb->get_results( $query );
			$row     = $result[0];
			$id      = $row->ID;
			$post_id = $id + 1;
		}

		$ai_wizard_form = AI_Wizard_From::getInstance($post_id);

		$chat_gpt_settings = array(
			'temp'            => $args['temp'],
			'top-p'           => $args['top-p'],
			'max-tokens-bool' => 'off',
			'max-tokens'      => '',
		);

		if ( isset( $args['max-tokens-bool'] ) ) {
			$chat_gpt_settings['max-tokens']      = $args['max-tokens'];
			$chat_gpt_settings['max-tokens-bool'] = $args['max-tokens'];
		}

		$ai_wizard_form->set_chat_gpt_settings($chat_gpt_settings);

		$ai_wizard_form->set_messages(array(
			'msg-error'   => $args['msg-error'],
			'msg-waiting' => $args['msg-waiting'],
		));

		$ai_wizard_form->set_prompt($args['prompt-template']);
		$ai_wizard_form->set_system_prompt($args['system-prompt']);
		$ai_wizard_form->set_response_filter_type($args['response-filter']);
		$ai_wizard_form->set_response_filter_regex($args['response-filter-regex']);

		if($args['active'] == 'off'){
			$ai_wizard_form->disable();
		}else{
			$ai_wizard_form->enable();
		}
	}

	public function editor_panels( $panels ) {

		require_once "cf7-chatgpt-panel.php";

		$length = count( $panels );
		$return = array_slice( $panels, 0, $length - 1, true ) + array(
				'chatGPT-panel' => array(
					'title'    => 'ChatGPT',
					'callback' => 'gofp_wpcf7_editor_panel_chatgpt'
				)
			) + array_slice( $panels, $length - 1, 1, true );

		return $return;
	}
}
