<?php

namespace aiwzrd\includes;

class Autoloader {

	public function __construct() {
		spl_autoload_register( array( $this, 'load_class' ) );
	}

	public function load_class( $className ) {
		if ( strpos( $className, 'aiwzrd\\' ) !== 0 ) {
			return; // Class does not belong to the AI_Wizard namespace, exit early
		}

		$namespaceParts = explode( '\\', $className );
		$classFileName  = 'class-' . str_replace( '_', '-', strtolower( end( $namespaceParts ) ) ) . '.php';
		$relativePath   = implode( DIRECTORY_SEPARATOR, array_slice( $namespaceParts, 1, - 1 ) );
		$filePath       = aiwzrd_Path . DIRECTORY_SEPARATOR . ( $relativePath ? strtolower( $relativePath ) . DIRECTORY_SEPARATOR : '' ) . $classFileName;

		if ( file_exists( $filePath ) ) {
			include_once $filePath;
		}
	}
}

$loader = new Autoloader();