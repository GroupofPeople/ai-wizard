<?php

namespace aiwzrd;

use aiwzrd\admin\AI_Wizard_Panel;
use aiwzrd\admin\Settings_Page;
use aiwzrd\includes\Post_Handler;
use aiwzrd\includes\Scripts;

class Plugin {

	private static $instance = null;

	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ], 0 );
	}

	final public static function get_instance() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	public function init() {
		$this->init_admin();
		$this->init_frontend();
	}

	public function init_admin() {
		if ( ! is_admin() ) {
			return;
		}

		new AI_Wizard_Panel();
		new Settings_Page();

	}

	public function init_frontend() {
		new Post_Handler();
		new Scripts();
	}
}