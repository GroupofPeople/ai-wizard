<?php

namespace aiwzrd\includes;

class Scripts {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'custom_shortcode_scripts' ) );
		$this->register_scripts();
		$this->register_styles();
	}

	private function register_scripts() {
		wp_register_script( "ai_wizard_form_js", false , array('jquery'));
		wp_register_script( "ai_wizard_error_form_js", false );
	}

	private function register_styles() {
		wp_register_style( "ai_wizard_form_css", plugins_url( "/includes/css/chatgpt-form.css", aiwzrd_file ), array(), null );
	}

	public function custom_shortcode_scripts() {
		global $post;

		$shortcode_regex = '/\[contact-form-7\s.*?]/';

		if ( is_a( $post, "WP_Post" ) && has_shortcode( $post->post_content, 'contact-form-7' ) ) {
			preg_match_all( $shortcode_regex, $post->post_content, $shortcodes );
			foreach ( $shortcodes[0] as $shortcode ) {
				$atts = $this->extract_atts_form_shortcode( $shortcode );
				if ( ! isset( $atts['id'] ) ) {
					continue;
				}
				$aiwzrd_form = AI_Wizard_Form::getInstance($atts['id']);
				if( $aiwzrd_form->is_enabled()){
					wp_enqueue_script( 'ai_wizard_form_js' );
					$script = $this->generate_form_scripts($aiwzrd_form);
					wp_add_inline_script( "ai_wizard_form_js", $script);
					wp_enqueue_style( 'ai_wizard_form_css' );
				}
			}
		}
	}

	private function generate_form_scripts($form){
		$waiting_message_script = $this->generate_waiting_message_script($form);
		return "
			(function ($) {
				$( document ).ready(function() {
					console.log('Hello');
					$waiting_message_script
				});
			})(jQuery)";
	}

	private function generate_waiting_message_script($form){
		$data = $form->get_messages();
		$waiting_message = esc_html($data['msg-waiting']);

		return "$('<span id=\"ai-wizard-waiting-message\">$waiting_message</span>').insertAfter('span.wpcf7-spinner');";
	}

	private function extract_atts_form_shortcode( $shortcode ) {
		// Store the shortcode attributes in an array here
		$attributes = [];

		// Get all attributes
		if ( preg_match_all( '/\w+\=\".*?\"/', $shortcode, $atts ) ) {

			// Now split up the key value pairs
			foreach ( $atts[0] as $att ) {
				$striped_att                = str_replace( '"', '', $att );
				$att_pair                   = explode( '=', $striped_att );
				$attributes[ $att_pair[0] ] = $att_pair[1];
			}
		}

		return $attributes;
	}
}