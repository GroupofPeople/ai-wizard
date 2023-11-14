<?php

namespace AI_Wizard\Admin\Panel_Sections;

use AI_Wizard\Includes\AI_Wizard_Form;

class Enable_Section extends Panel_Section {

	public function render_section( $ai_wizard_form, $post ) {
		?>
        <fieldset class="enable-container <?php echo AI_Wizard_Form::once_enabled() && !$ai_wizard_form->is_enabled() ? "enable-container-disabled" : ""; ?>">
            <label for="<?php echo esc_html(self::PREFIX); ?>-active"><?php echo esc_html__( "Enable ChatGPT extension for this form", 'ai-wizard' ); ?></label>
            <label class="switch">
                <input type="checkbox" name="<?php echo esc_html(self::PREFIX); ?>[active]"
                       id="<?php echo esc_html(self::PREFIX); ?>-active" <?php echo esc_html($ai_wizard_form->is_enabled() ? "checked" : ""); ?>
	                <?php echo esc_html(AI_Wizard_Form::once_enabled() && !$ai_wizard_form->is_enabled() ? "disabled" : ""); ?>>
                <span class="slider round"></span>
            </label>
            <?php
            if(AI_Wizard_Form::once_enabled() && !$ai_wizard_form->is_enabled()){
                $this->tool_tip(esc_html__('Please note that AI Wizard can only be used for one CF7 form in total on your WordPress site. Visit the developer page to receive more information.', 'ai-wizard'));
            }
            ?>
        </fieldset>
		<?php
	}

	public function save_section( $ai_wizard_form ) {
		$request_args = wp_parse_args( $_POST[ self::PREFIX ], array(
			'active' => 'off',
		) );

		if ( $request_args['active'] == 'on' ) {
			$ai_wizard_form->enable();
		} else {
			$ai_wizard_form->disable();
		}
	}
}