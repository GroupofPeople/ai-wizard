<?php

namespace AI_Wizard\Admin\Panel_Sections;

class Introduction_Panel extends Panel_Section {

	public function render_section( $ai_wizard_form, $post ) {
		?>
        <span><?php echo __('How to use the AI-Wizard Introduction', 'ai-wizard');?></span>
        <p>
            <?php echo __('A video with the basic usage of AI Wizard can be found', 'ai-wizard');?>
            <a href="https://ai-wizard.groupofpeople.net/"> <?php echo __('here', 'ai-wizard');?></a>.
        </p>
        <p>
            <?php echo __('To use the generated answer of ChatGPT in the Form use:', 'ai-wizard');;?> [_chat_gpt_answer]
        </p>
		<?php
	}

	public function save_section( $ai_wizard_form, $request_args ) {
		// Nothing to do here
	}
}