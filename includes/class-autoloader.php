<?php

namespace AI_Wizard\Includes;

class Autoloader {

	public function __construct() {
		spl_autoload_register( array( $this, 'load_class' ) );
	}

	public function load_class( $className ) {
		$pathArray = explode( '\\', $className );

		if ( 0 == strcmp( $pathArray[0], 'AI_Wizard' ) ) {
			array_shift( $pathArray );

			$classPath = '';
			$fileName  = array_pop( $pathArray );

			foreach ( $pathArray as $value ) {
				$classPath .= DIRECTORY_SEPARATOR;
				$classPath .= strtolower( $value );
			}

			$fileName  = str_replace( '_', '-', strtolower( $fileName ) );
			$classPath .= DIRECTORY_SEPARATOR . 'class-' . $fileName . '.php';


			include_once gofpChatGPTPath . $classPath;
		}
	}
}

$loader = new Autoloader();