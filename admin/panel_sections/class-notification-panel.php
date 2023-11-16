<?php

namespace aiwzrd\admin\panel_sections;

class Notification_Panel extends Panel_Section {

	private $descriptions;
	private $tool_tips;

	public function __construct() {
		$this->descriptions();
		$this->tool_tips();
	}

	private function descriptions() {
		$this->descriptions['msg-error']   = __( 'An error occurred during OpenAI request', 'ai-wizard' );
		$this->descriptions['msg-waiting'] = __( 'Message while the response is being generated', 'ai-wizard' );
	}

	private function tool_tips() {
		$this->tool_tips['msg-error']   = __( 'Individual text that is shown to the user when an error occurs while generating an answer.', 'ai-wizard' );
		$this->tool_tips['msg-waiting'] = __( 'Individual text that is shown to the user when a response is generated.', 'ai-wizard' );
	}

	public function render_section( $ai_wizard_form, $post ) {
		$messages = $ai_wizard_form->get_messages();
		?>

        <section class="page">
            <h2><?php esc_html_e( 'Notifications', 'ai-wizard' );?></h2>
            <br>
            <fieldset>
				<?php foreach ( $messages as $key => $message ) { ?>
                    <div class="field ">
                        <label class="label" for="<?php echo esc_html(self::PREFIX . '-' . $key); ?>">
							<?php echo esc_html( $this->descriptions[ $key ] ); ?>
                        </label>
                        <div class="control">
                            <input id="<?php echo esc_html(self::PREFIX . '-' . $key); ?>"
                                   name="<?php echo esc_html(self::PREFIX . '[' . $key . ']'); ?>"
                                   class="input" value="<?php echo esc_attr($message); ?>">
                        </div>
						<?php $this->tool_tip( $this->tool_tips[ $key ] ); ?>
                    </div>
				<?php } ?>
            </fieldset>
        </section>
		<?php
	}

	public function save_section( $ai_wizard_form ) {
		$request_args = wp_parse_args( $_POST[ self::PREFIX ], array(
			'msg-error'   => '',
			'msg-waiting' => '',
		) );

		$ai_wizard_form->set_messages( array(
			'msg-error'   => sanitize_text_field($request_args['msg-error']),
			'msg-waiting' => sanitize_text_field($request_args['msg-waiting']),
		) );
	}
}