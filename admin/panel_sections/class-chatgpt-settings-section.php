<?php

namespace AI_Wizard\Admin\Panel_Sections;

class ChatGPT_Settings_Section extends Panel_Section {

	public function render_section( $ai_wizard_form, $post ) {
		?>
        <section class="page">
            <h2>ChatGPTSettings</h2>
            <fieldset>
                <table class="form-table">
                    <tbody>
					<?php
					$this->render_model();
					$this->render_temp();
					$this->render_t_top();
					$this->render_max_token();
					?>
                    </tbody>
                </table>
            </fieldset>
        </section>
		<?php
	}

	private function render_model() {
		?>
        <tr>
            <th scope="row">
                <span class="label"><?php echo esc_html( __( 'Used Model', 'ai-wizard' ) ); ?></span>
            </th>
            <td>
                <div class="half-width">
                    <div class="select is-normal full-width">
                        <label>
                            <select>
                                <option>gpt-3.5-turbo</option>
                            </select>
                        </label>
                    </div>
                </div>
            </td>
            <td>
		        <?php $this->pop_up( __( 'Model', 'ai-wizard' ), __( 'To use more models you can visit our website', 'ai-wizard' ) ); ?>
            </td>
        </tr>
		<?php
	}

	private function render_temp() {
		?>
        <tr>
            <th scope="row">
                <label class="label"
                       for="<?php echo self::PREFIX; ?>-temp"><?php echo esc_html( __( 'Temperature', 'ai-wizard' ) ); ?></label>
            </th>
            <td>
                <div class="half-width">
                    <div class="temp-container">
                        <div class="numberslidercontainer">
                            <input type="range" id="<?php echo self::PREFIX; ?>-temp"
                                   name="<?php echo self::PREFIX; ?>[temp]" class="numberslider"
                                   value="1" min="0" max="2" step="0.1" disabled
                            />
							<?php $this->tool_tip( __( 'Controls text creativity and randomness, with higher values being more unpredictable and lower values more conservative. Vary either this value or top_p. Not both.', 'ai-wizard' ) ) ?>
                        </div>
                        <div class="numbercontainer">
                            <input class="input" type="number"
                                   value="1"
                                   name="<?php echo self::PREFIX; ?>[temp]-number" min="0" max="2"
                                   step="0.1" disabled/>
                        </div>
                    </div>
                </div>
            </td>
            <td>
				<?php $this->pop_up( __( 'Temperature', 'ai-wizard' ), __( 'To adjust the temperature you can visit our website', 'ai-wizard' ) ); ?>
            </td>
        </tr>
		<?php
	}

	private function render_t_top() {
		?>
        <tr>
            <th scope="row">
                <label class="label"
                       for="<?php echo self::PREFIX; ?>-top-p"><?php echo esc_html( __( 'Top-p', 'ai-wizard' ) ); ?></label>
            </th>
            <td>
                <div class="half-width">
                    <input type="number" id="<?php echo self::PREFIX; ?>-top-p"
                           name="<?php echo self::PREFIX; ?>[top-p]" class="input"
                           value="1" min="0" max="1" placeholder="1" step="0.01" disabled
                    />
                    <div>
		                <?php $this->tool_tip( __( 'The parameter controls the diversity of the generated text by limiting the set of next-word candidates to the top p most likely choices.', 'ai-wizard' ) ); ?>
                    </div>
                </div>
            </td>
            <td>
		        <?php $this->pop_up( __( 'top-p', 'ai-wizard' ), __( 'To adjust top-p you can visit our website', 'ai-wizard' ) ); ?>
            </td>
        </tr>
		<?php
	}

	private function render_max_token() {
		?>
        <tr>
            <th scope="row">
                <label class="label"
                       for="<?php echo self::PREFIX; ?>-max-tokens"><?php echo esc_html( __( 'Maximum Tokens per request', 'ai-wizard' ) ); ?></label>
            </th>
            <td>
                <div class="half-width childs-centered">
                    <label class="checkbox" for="<?php echo self::PREFIX; ?>-max-tokens-bool">
                        <input type="checkbox" id="<?php echo self::PREFIX; ?>-max-tokens-bool"
                               name="<?php echo self::PREFIX; ?>[max-tokens-bool]"
                               class="" checked disabled/>
                        Do not restrict tokens per request
                    </label>
                    <input type="number" id="<?php echo self::PREFIX; ?>-max-tokens"
                           name="<?php echo self::PREFIX; ?>[max-tokens]" class="input" disabled
                           value="4096" min="0" max="4096" placeholder="infinite" step="0.01"/>

                </div>
                <?php $this->tool_tip(__('Maximum tokens per request limits the number of generated words in a single API request.', 'ai-wizard'));?>

            </td>
            <td>
		        <?php $this->pop_up( __( 'Max Tokens per Request', 'ai-wizard' ), __( 'To adjust the Max tokens per Request you can visit our website', 'ai-wizard' ) ); ?>
            </td>
        </tr>
		<?php
	}

	public function save_section( $ai_wizard_form, $request_args ) {
		//Nothing to doo here, cause just a display
	}
}