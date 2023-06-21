<?php

namespace AI_Wizard\Includes;

class Scripts {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'custom_shortcode_scripts' ) );
		$this->register_scripts();
		$this->register_styles();
	}

	private function register_scripts() {
		wp_register_script( "ai_wizard_form_js", false );
		wp_register_script( "ai_wizard_error_form_js", false );
	}

	private function register_styles() {
		wp_register_style( "ai_wizard_form_css", plugins_url( "/includes/css/chatgpt-form.css", gofpChatGPTFile ), array(), null );
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
				if ( get_post_meta( $atts['id'], 'chatgpt_is_active', true ) ) {
					wp_enqueue_script( 'ai_wizard_form_js' );
					$script = $this->generate_form_scripts($atts['id']);
					wp_add_inline_script( "ai_wizard_form_js", $script);
					$script = $this->generate_error_message_script($atts['id']);
					wp_add_inline_script( "ai_wizard_error_form_js", $script);
					wp_enqueue_style( 'ai_wizard_form_css' );
				}
			}
		}
	}

	private function generate_form_scripts($form_id){
		$waiting_message_script = $this->generate_waiting_message_script($form_id);
		$error_message_script = $this->generate_error_message_script($form_id);

		return "
			(function ($) {
				$( document ).ready(function() {
					$waiting_message_script
					$error_message_script
				});
			})(jQuery)";
	}

	private function generate_waiting_message_script($form_id){
		$data = get_post_meta( $form_id, 'request_settings', true );
		$waiting_message = esc_html($data['messages']['msg-waiting']);

		return "$('<span id=\"ai-wizard-waiting-message\">$waiting_message</span>').insertAfter('span.wpcf7-spinner');
				";

	}

	private function generate_error_message_script($form_id){
		$data = get_post_meta( $form_id, 'request_settings', true );
		$error_message = esc_html($data['messages']['msg-error']);

		return "const { fetch: originalFetch } = window;
window.fetch = async (...args) => {
  let [resource, config] = args;
  let response = await originalFetch(resource, config);
  if (!response.ok && response.status === 500) {
    // 404 error handling
    const collection = document.getElementsByClassName('wpcf7-form');
    if( typeof collection[0] !== 'undefined' ){
        collection[0].classList.remove('submitting');
        collection[0].classList.add('failed');
        collection[0].setAttribute('data-status', 'failed');
    }
    const responseOutput = document.getElementsByClassName('wpcf7-response-output');
    if( typeof responseOutput[0] !== 'undefined' ){
        responseOutput[0].innerHTML = '$error_message' ;
    }
  }
  return response;
};";

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

// Return the array
		return $attributes;
	}
}