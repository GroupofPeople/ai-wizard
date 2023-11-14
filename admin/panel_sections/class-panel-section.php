<?php

namespace AI_Wizard\Admin\Panel_Sections;

abstract class Panel_Section {
    const PREFIX = "ai-wizard";

	public abstract function render_section($ai_wizard_form, $post);

	public abstract function save_section( $ai_wizard_form );

	protected function pop_up( $title, $text ) {
		?>
		<div class="ai-wizard-popup">
        <span class="popup-text">Upgrade
            <span class="popup-content">
                <h3><?php echo esc_html($title) ?></h3>
                <p><?php echo esc_html($text) ?></p>
                <a href="https://ai-wizard.groupofpeople.net/">Click here to go to your website</a>
            </span>
        </span>
		</div>

		<?php
	}

	protected function tool_tip( $text = '' ) {
		?>
		<span class="icon-text has-text-info">
            <span class="icon">
                <span class="dashicons dashicons-info-outline is-small"></span>
            </span>
            <span class="help"><?php echo esc_html($text) ?></span>
        </span>
		<?php
	}
}