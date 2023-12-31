<?php

namespace aiwzrd\admin;

use aiwzrd\includes\AI_Wizard_Form;

class AI_Wizard_Panel {

	private $sections = array();

	public function __construct() {
		add_filter( 'wpcf7_editor_panels', array( $this, 'add_editor_panel' ) );
		add_action( 'wpcf7_after_save', array( $this, 'save_contact_form' ),1);
		add_action( 'deleted_post', array( $this, 'delete_form' ), 1, 2 );
		add_action( 'admin_enqueue_scripts', array($this, 'enqueue'));
		$this->enqueue_sections();
	}

	private function enqueue_sections() {
		$this->sections[] = new Panel_Sections\Enable_Section();
		$this->sections[] = new Panel_Sections\Introduction_Panel();
		$this->sections[] = new Panel_Sections\Request_Settings_Section();
		$this->sections[] = new Panel_Sections\Notification_Panel();
	}

	public function delete_form( $postid, $post ) {
		if ( $post->post_type == 'wpcf7_contact_form' ) {
			AI_Wizard_Form::getInstance( $postid )->delete();
		}
	}


	public function save_contact_form( $cf7_form ) {
		$post_id = $cf7_form->id();

		$ai_wizard_form = AI_Wizard_Form::getInstance( $post_id );

		foreach ( $this->sections as $section ) {
			$section->save_section( $ai_wizard_form );
		}
	}


	public function enqueue() {
		global $pagenow;

		if($pagenow === 'admin.php' && isset($_GET['page']) && ($_GET['page'] === 'wpcf7' || $_GET['page'] === 'wpcf7-new')){
			wp_enqueue_script( "ai-wizard-cf7-admin", plugins_url( "/admin/js/ai-wizard-cf7-admin.js", aiwzrd_file ), array( 'jquery' ));
			wp_enqueue_style( "ai-wizard-cf7-admin", plugins_url( "/admin/css/ai-wizard-cf7-admin.css", aiwzrd_file ), array() );
			wp_enqueue_style( "ai_wizard_bulma_css", plugins_url( "/admin/css/bulma.css", aiwzrd_file ), array() );
		}
	}

	public function add_editor_panel( $panels ) {

		$insertIndex = count( $panels ) - 1;

		$newPanels = array();
		$current   = 0;

		foreach ( $panels as $key => $value ) {
			if ( $current === $insertIndex ) {
				$newPanels['ai-wizard-panel'] = array(
					'title'    => 'AI-Wizard',
					'callback' => array( $this, 'panel_callback' ),
				);
			}

			$newPanels[ $key ] = $value;

			$current ++;
		}

		return $newPanels;
	}

	public function panel_callback( $post ) {

		$ai_wizard_form = AI_Wizard_Form::getInstance( $post->id() );

        $this->enqueue();
		ob_start();
		foreach ( $this->sections as $section ) {
			$section->render_section( $ai_wizard_form , $post);

			if($section == $this->sections[0]){
				echo '<div class="ai-wizard-content" style="';
				echo $ai_wizard_form->is_enabled() ? "" : "display: none";
				echo '">';
			}

			if($section !== end($this->sections)){
				echo '<div class="ai-wizard-separator"></div>';
			}

			if($section == end($this->sections)){
				echo '</div>';
			}
		}

		$allowed_tags = array(
			'fieldset' => array(
				'class' => array()
			),
			'label' => array(
				'for' => array(),
				'class' => array(),
			),
			'input' => array(
				'type' => array(),
				'name' => array(),
				'id' => array(),
				'checked' => array(),
				'value' => array(),
				'class' => array(),
			),
			'span' => array(
				'class' => array(),
				'style' => array(),
			),
			'div' => array(
				'class' => array(),
				'style' => array(),
			),
			'h3' => array(),
			'h2' => array(),
			'ol' => array(),
			'li' => array(),
			'section' => array(
				'class' => array()
			),
			'table' => array(
				'class' => array()
			),
			'tbody' => array(),
			'tr' => array(),
			'th' => array(
				'scope' => array()
			),
			'td' => array(),
			'textarea' => array(
				'id' => array(),
				'name' => array(),
				'class' => array()
			),
			'br' => array(),
			'strong' => array(),
			'legend' => array(),
			'select' => array(
				'name' => array(),
				'id' => array()
			),
			'option' => array(
				'value' => array()
			)
			// Add more tags as needed
		) ;
		$html = $this->minify_html( ob_get_clean());

		echo wp_kses( $html , $allowed_tags);

	}

	private function minify_html( $html ) {
		// Remove HTML comments
		$html = preg_replace( '/<!--(.|\s)*?-->/', '', $html );

		// Remove white spaces around HTML tags
		$html = preg_replace( '/>\s+</', '><', $html );

		// Remove white spaces and line breaks
		$html = preg_replace( '/\s+/', ' ', $html );

		return str_replace( [ "\r", "\n" ], '', $html );
	}


}