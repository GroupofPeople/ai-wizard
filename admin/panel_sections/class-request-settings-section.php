<?php

namespace aiwzrd\admin\panel_sections;

class Request_Settings_Section extends Panel_Section {

	public function render_section( $ai_wizard_form, $post ) {
		?>
        <section class="page">
            <h2><?php esc_html_e('Request Settings', 'ai-wizard');?></h2>
            <fieldset>
                <legend>
					<?php
                    esc_html_e( "In the following fields, you can use these tags:", 'ai-wizard' );
					echo '<br />';
					$post->suggest_mail_tags( 'mail' );
					?>
                </legend>
                <table class="form-table">
                    <tbody>
					<?php
					$this->render_prompt( $ai_wizard_form );
					$this->render_system_prompt( $ai_wizard_form );
					$this->render_filter( $ai_wizard_form );
					?>
                    </tbody>
                </table>
            </fieldset>
        </section>
		<?php

	}

	private function render_prompt( $ai_wizard_form ) {
		?>
        <tr>
            <th scope="row">
                <label class="label" for="<?php echo esc_html(self::PREFIX); ?>-prompt"><?php esc_html_e('Prompt', 'ai-wizard');?></label>
            </th>
            <td>
                <textarea id="<?php echo esc_html(self::PREFIX); ?>-prompt" name="<?php echo esc_html(self::PREFIX); ?>[prompt-template]"
                          class="textarea pretty-tags">
                    <?php echo esc_html($ai_wizard_form->get_prompt()); ?>
                </textarea>
                <div>
					<?php $this->tool_tip( __('Enter your user prompt using custom tags to embed user input via tags for a personalized response.', 'ai-wizard') ); ?>
                </div>
            </td>
        </tr>
		<?php
	}

	private function render_system_prompt( $ai_wizard_form ) {
		?>
        <tr>
            <th scope="row">
                <label class="label"
                       for="<?php echo esc_html(self::PREFIX); ?>-system-prompt"><?php esc_html_e( 'System Prompt', 'ai-wizard' ); ?></label>
            </th>
            <td>
            <textarea id="<?php echo esc_html(self::PREFIX); ?>-system-prompt" name="<?php echo esc_html(self::PREFIX); ?>[system-prompt]"
                      class="textarea"><?php echo esc_html($ai_wizard_form->get_system_prompt()); ?></textarea>
                <div>
                    <?php $this->tool_tip( __('Provide additional context information for the creation of the response by Chat GPT.', 'ai-wizard'));?>
                </div>
            </td>
        </tr>
		<?php
	}

	private function render_filter( $ai_wizard_form ) {
		$current_response_filter = $ai_wizard_form->get_response_filter()['type'];
		?>
        <tr>
            <th scope="row">
                <label class="label" for="<?php echo esc_html(self::PREFIX); ?>-response-filter">
					<?php esc_html_e( 'Filter API Response', 'ai-wizard' ); ?>
                </label>
            </th>
            <td>
                <div class="control">
                    <label class="radio" for="<?php echo esc_html(self::PREFIX); ?>[response-filter]-text">
                        <input type="radio" name="<?php echo esc_html(self::PREFIX); ?>[response-filter]"
                               id="<?php echo esc_html(self::PREFIX); ?>[response-filter]-text"
                               value="text" <?php echo $current_response_filter == "text" ? "checked" : "" ?>/>
                        <?php esc_html_e('Use complete Text','ai-wizard');?>
                    </label>
                </div>
                <div class="control">
                    <label class="radio" for="<?php echo esc_html(self::PREFIX); ?>[response-filter]-number">
                        <input type="radio" name="<?php echo esc_html(self::PREFIX); ?>[response-filter]"
                               id="<?php echo esc_html(self::PREFIX); ?>[response-filter]-number"
                               value="number" <?php echo esc_html($current_response_filter == "number" ? "checked" : ""); ?>/>
                        <?php esc_html_e('Use First number of Response','ai-wizard');?>
                    </label>
                </div>
            </td>
        </tr>
		<?php
	}

	public function save_section( $ai_wizard_form ) {
		$request_args = array(
			'prompt-template' => "",
			'system-prompt'   => "",
			'response-filter' => "",
		);

        foreach ($request_args as $key => $value){
            if(isset($_POST[ self::PREFIX ][$key])){
                $request_args[$key] = sanitize_text_field($_POST[ self::PREFIX ][$key]);
            }
        }

		$ai_wizard_form->set_prompt( sanitize_text_field($request_args['prompt-template']) );
		$ai_wizard_form->set_system_prompt( sanitize_text_field($request_args['system-prompt']) );
        //validate response-filter
        $filtered = (in_array($request_args['response-filter'] , array('number', 'text')))? $request_args['response-filter'] : 'text';
		$ai_wizard_form->set_response_filter_type( $filtered );
	}

}