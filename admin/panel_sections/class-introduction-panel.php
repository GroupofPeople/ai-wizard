<?php

namespace AI_Wizard\Admin\Panel_Sections;

class Introduction_Panel extends Panel_Section {

	public function render_section( $ai_wizard_form, $post ) {
		?>
        <h3><?php esc_html_e('How to use the AI-Wizard:', 'ai-wizard');?></h3>

        <span>
            <?php esc_html_e('To use the generated answer of ChatGPT use the placeholder: ', 'ai-wizard');?>
            <span class="mailtag code used">
                [_chat_gpt_answer]
            </span>
        </span>
        <ol>
            <li>
                <span style="font-weight: bold">CF7 Autoresponder:</span> You can paste the above placeholder in the message field of the cf7 e-mails. An E-Mail will be sent automatically with the Chat GPT answer.
            </li>
            <li>
                <span style="font-weight: bold">Message Field:</span> You can paste the placeholder inside the message fields (e.g. message sent successfully) and the Chat GPT answer will be shown directly below the formula
            </li>
        </ol>
		<?php
	}

	public function save_section( $ai_wizard_form ) {
		// Nothing to do here
	}
}