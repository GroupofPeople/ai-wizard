<?php

use AI_Wizard\Includes\AI_Wizard_From;
use AI_Wizard\Includes\Licensing\EDD_Integration;

function gofp_wpcf7_editor_panel_chatgpt( $post ) {
	wp_enqueue_script( "chatGPT-wpcf7-admin", plugins_url( "/admin/js/chat-gpt-admin.js", gofpChatGPTFile ), array( 'jquery' ) );
	wp_enqueue_style( "chatGPT-wpcf7-admin", plugins_url( "/admin/css/chat-gpt-admin.css", gofpChatGPTFile ), array(), null );
	wp_enqueue_style( "ai_wizard_bulma_css", plugins_url( "/admin/css/bulma.css", gofpChatGPTFile ), array(), null );

	$id = "gofp-wpcf7-chatgpt";

	$ai_wizard_form    = AI_Wizard_From::getInstance( $post->id() );
	$chat_gpt_settings = $ai_wizard_form->get_chat_gpt_settings();
	$license           = EDD_Integration::get_instance()->is_valid();
	?>
    <fieldset class="enable-container">
        <label for="<?php echo $id; ?>-active"><?php echo esc_html( __( "Enable ChatGPT extension for this form", 'contact-form-7' ) ); ?></label>
        <label class="switch">
            <input type="checkbox" name="<?php echo $id; ?>[active]"
                   id="<?php echo $id; ?>-active" <?php echo $ai_wizard_form->is_enabled() ? "checked" : ""; ?>>
            <span class="slider round"></span>
        </label>
    </fieldset>
    <div class="chat-gpt-page"
         style="<?php echo $ai_wizard_form->is_enabled() ? "" : "display: none"; ?>">
        <div class="separator"></div>
		<?php gofp_wpcf7_editor_panel_introduction(); ?>
        <div class="separator"></div>
        <section class="page">
            <h2>ChatGPTSettings</h2>
            <fieldset>
                <table class="form-table">
                    <tbody>
                    <tr>
                        <th scope="row">
                            <span class="label"><?php echo esc_html( __( 'Used Model', 'contact-form-7-gofp-chatgpt' ) ); ?></span>
                        </th>
                        <td>
                            <div class="half-width">
                                <div class="select is-normal full-width">
                                    <select>
                                        <option>gpt-3.5-turbo</option>
                                    </select>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label class="label"
                                   for="<?php echo $id; ?>-temp"><?php echo esc_html( __( 'Temperature', 'contact-form-7-gofp-chatgpt' ) ); ?></label>
                        </th>
                        <td>
                            <div class="half-width">
                                <div class="temp-container">
                                    <div class="numberslidercontainer">
                                        <input type="range" id="<?php echo $id; ?>-temp" name="<?php echo $id; ?>[temp]"
                                               class="numberslider"
                                               value="<?php echo $chat_gpt_settings['temp'] ?>"
                                               min="0" max="2" step="0.1"
											<?php echo EDD_Integration::get_instance()->is_valid() ? "" : "disabled"; ?>
                                        />
                                        <span class="icon-text has-text-info">
                                            <span class="icon">
                                                <span class="dashicons dashicons-info-outline is-small"></span>
                                            </span>
                                            <span class="help">Controls text creativity and randomness, with higher values being more unpredictable and lower values more conservative. Vary either this value or top_p. Not both.</span>
                                        </span>
                                    </div>
                                    <div class="numbercontainer">
                                        <input class="input" type="number"
                                               value="<?php echo isset( $chat_gpt_settings['temp'] ) ? $chat_gpt_settings['temp'] : 1 ?>"
                                               name="<?php echo $id; ?>[temp]-number" min="0" max="2" step="0.1"
											<?php echo EDD_Integration::get_instance()->is_valid() ? "" : "disabled"; ?>/>
                                        <span class="help"><?php echo esc_html( __( 'Default value', 'contact-form-7-gofp-chatgpt' ) ); ?>: 1</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label class="label"
                                   for="<?php echo $id; ?>-top-p"><?php echo esc_html( __( 'Top-p', 'contact-form-7-gofp-chatgpt' ) ); ?></label>
                        </th>
                        <td>
                            <div class="half-width">
                                <input type="number" id="<?php echo $id; ?>-top-p" name="<?php echo $id; ?>[top-p]"
                                       class="input"
                                       value="<?php echo isset( $chat_gpt_settings['top-p'] ) ? $chat_gpt_settings['top-p'] : 1 ?>"
                                       min="0" max="1" placeholder="1" step="0.01"
									<?php echo EDD_Integration::get_instance()->is_valid() ? "" : "disabled"; ?>
                                />
                                <span class="icon-text has-text-info">
                                            <span class="icon">
                                                <span class="dashicons dashicons-info-outline is-small"></span>
                                            </span>
                                            <span class="help">The parameter controls the diversity of the generated text by limiting the set of next-word candidates to the top p most likely choices.</span>
                                        </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label class="label"
                                   for="<?php echo $id; ?>-max-tokens"><?php echo esc_html( __( 'Maximum Tokens per request', 'contact-form-7-gofp-chatgpt' ) ); ?></label>
                        </th>
                        <td>
                            <div class="half-width childs-centered">
                                <label class="checkbox" for="<?php echo $id; ?>-max-tokens-bool">
                                    <input type="checkbox" id="<?php echo $id; ?>-max-tokens-bool"
                                           name="<?php echo $id; ?>[max-tokens-bool]"
                                           class="" <?php echo ( 'off' == $chat_gpt_settings['max-tokens-bool'] ) ? '' : 'checked' ?>/>
                                    Do not restrict tokens per request
                                </label>
                                <input type="number" id="<?php echo $id; ?>-max-tokens"
                                       name="<?php echo $id; ?>[max-tokens]"
                                       class="input" <?php echo ( 'off' == $chat_gpt_settings['max-tokens-bool'] ) ? '' : 'disabled' ?>
                                       value="<?php echo $chat_gpt_settings['max-tokens'] ?>" min="0" max="4096"
                                       placeholder="infinite" step="0.01"/>

                            </div>
                            <span class="icon-text has-text-info childs-centered">
                                            <span class="icon">
                                                <span class="dashicons dashicons-info-outline is-small"></span>
                                            </span>
                                            <span class="help">Maximum tokens per request limits the number of generated words in a single API request.</span>
                                        </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>

        </section>
        <div class="separator"></div>
        <!--        <div class="content">-->
        <section class="page">
            <h2>Request Settings</h2>
            <fieldset>
                <legend>
					<?php
					echo esc_html( __( "In the following fields, you can use these tags:", 'contact-form-7' ) );
					echo '<br />';
					$post->suggest_mail_tags( 'mail' );
					?>
                </legend>
                <table class="form-table ">
                    <tbody>
					<?php
					gofp_wpcf7_editor_panel_chatgpt_request_settings( $ai_wizard_form, $id );
					?>
                    </tbody>
                </table>
            </fieldset>
        </section>

        <div class="separator"></div>
        <!--        </div>-->
        <section class="page">
			<?php
			gofp_wpcf7_editor_panel_messages_settings( $ai_wizard_form->get_messages(), $id )
			?>
        </section>

        <div class="separator"></div>
        <div>
			<?php
			echo esc_html( __( "To use the answer of ChatGPT you can use the tag ", 'contact-form-7' ) );
			echo "<span class='mailtag code used'>[_chat_gpt_answer]</span>";
			?>
        </div>
    </div>
	<?php
}

function gofp_wpcf7_editor_panel_messages_settings( $messages, $id ) {
	?>
    <h2>Notifications</h2>
    <br>
    <fieldset>
        <div class="field ">
            <label class="label" for="<?php echo $id; ?>-msg-error">
				<?php echo esc_html( __( 'An error occurred during OpenAI request', 'contact-form-7-gofp-chatgpt' ) ); ?>
            </label>
            <div class="control">
                <input id="<?php echo $id; ?>-msg-error" name="<?php echo $id; ?>[msg-error]"
                       class="input" value="<?php echo $messages["msg-error"] ?>">
            </div>
            <span class="icon-text has-text-info">
                <span class="icon">
                    <span class="dashicons dashicons-info-outline is-small"></span>
                </span>
                <span class="help">Individual text that is shown to the user when an error occurs while generating an answer.</span>
            </span>
        </div>
        <div class="field">
            <label class="label" for="<?php echo $id; ?>-msg-waiting">
				<?php echo esc_html( __( 'Message while the response is being generated', 'contact-form-7-gofp-chatgpt' ) ); ?>
            </label>
            <div class="control">
                <input id="<?php echo $id; ?>-msg-error" name="<?php echo $id; ?>[msg-waiting]"
                       class="input" value="<?php echo $messages["msg-waiting"] ?>">
            </div>
            <span class="icon-text has-text-info">
                <span class="icon">
                    <span class="dashicons dashicons-info-outline is-small"></span>
                </span>
                <span class="help">Individual text that is shown to the user when a response is generated.</span>
            </span>
        </div>
        <!--            <p class="description">-->
        <!--                <label for="--><?php //echo $id; ?><!---msg-waiting">-->
        <!--					--><?php //echo esc_html( __( 'Answer is generating', 'contact-form-7-gofp-chatgpt' ) ); ?>
        <!--                    <br>-->
        <!--                    <input id="--><?php //echo $id; ?><!---msg-waiting" name="-->
		<?php //echo $id; ?><!--[msg-waiting]"-->
        <!--                           class="large-text code"-->
        <!--                           value="--><?php //echo $messages["msg-waiting"] ?><!--">-->
        <!--                </label>-->
        <!--            </p>-->
		<?php
		//		}
		?>
    </fieldset>
	<?php

}

function gofp_wpcf7_editor_panel_chatgpt_request_settings( $ai_wizard_form, $id ) {

	?>
    <tr>
        <th scope="row">
            <label class="label"
                   for="<?php echo $id; ?>-prompt"><?php echo esc_html( __( 'Prompt', 'contact-form-7-gofp-chatgpt' ) ); ?></label>
        </th>
        <td>
            <textarea id="<?php echo $id; ?>-prompt" name="<?php echo $id; ?>[prompt-template]"
                      class="textarea pretty-tags"
            ><?php echo $ai_wizard_form->get_prompt() ?></textarea>
            <div>
                <span class="icon-text has-text-info">
                    <span class="icon">
                        <span class="dashicons dashicons-info-outline is-small"></span>
                    </span>
                    <span class="help">Enter your user prompt using custom tags to embed user input via tags for a personalized response.</span>
                </span>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label class="label"
                   for="<?php echo $id; ?>-system-prompt"><?php echo esc_html( __( 'System Prompt', 'contact-form-7-gofp-chatgpt' ) ); ?></label>
        </th>
        <td>
            <textarea id="<?php echo $id; ?>-system-prompt" name="<?php echo $id; ?>[system-prompt]"
                      class="textarea"><?php echo $ai_wizard_form->get_system_prompt() ?></textarea>
            <div>
                <span class="icon-text has-text-info">
                    <span class="icon">
                        <span class="dashicons dashicons-info-outline is-small"></span>
                    </span>
                    <span class="help">Provide additional context information for the creation of the response by Chat GPT.</span>
                </span>
            </div>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label class="label"
                   for="<?php echo $id; ?>-response-filter"><?php echo esc_html( __( 'Filter API Response by', 'contact-form-7-gofp-chatgpt' ) ); ?></label>
        </th>
        <td>
			<?php
			$current_response_filter = $ai_wizard_form->get_response_filter()['type'];
			?>
            <div class="control">
                <label class="radio" for="<?php echo $id; ?>[response-filter]-text">
                    <input type="radio" name="<?php echo $id; ?>[response-filter]"
                           id="<?php echo $id; ?>[response-filter]-text"
                           value="text" <?php echo $current_response_filter == "text" ? "checked" : "" ?>/>
                    Use complete Text
                </label>
            </div>
            <div class="control">
                <label class="radio" for="<?php echo $id; ?>[response-filter]-number">
                    <input type="radio" name="<?php echo $id; ?>[response-filter]"
                           id="<?php echo $id; ?>[response-filter]-number" value="number"
						<?php
						echo $current_response_filter == "number" ? "checked" : "";
						echo EDD_Integration::get_instance()->is_valid() ? "" : "disabled";
						?>/>
                    Use First number of Response
                </label>
            </div>
            <div class="control">
                <label class="radio" for="<?php echo $id; ?>[response-filter]-number">
                    <input type="radio" name="<?php echo $id; ?>[response-filter]"
                           id="<?php echo $id; ?>[response-filter]-regex" value="regex"
						<?php
						echo $current_response_filter == "regex" ? "checked" : "";
						echo EDD_Integration::get_instance()->is_valid() ? "" : "disabled";
						?>/>
                    Use custom Regex
                </label>
            </div>
            <div id="container-response-filter-regex" style="display: none">
                <label for="<?php echo $id; ?>-response-filter-regex">
                    <input class="input" placeholder="Your custom Regex" type="text"
                           id="<?php echo $id; ?>-response-filter-regex"
                           name="<?php echo $id; ?>[response-filter-regex]" class="large-text code" size="70"
                           value="<?php echo $ai_wizard_form->get_response_filter()['custom-regex'] ?>"/>
                </label>
                <div>
                <span class="icon-text has-text-info">
                    <span class="icon">
                        <span class="dashicons dashicons-info-outline is-small"></span>
                    </span>
                    <span class="help">Use a custom regex to adjust the output.</span>
                </span>
                </div>
            </div>
        </td>
    </tr>
	<?php

}

function gofp_wpcf7_editor_panel_introduction() {
	?>
    <span>How to use the AI-Wizard Introduction</span>
    <p>
        A video with the basic usage of AI Wizard can be found
        <a>here</a>.
    </p>
	<?php
}