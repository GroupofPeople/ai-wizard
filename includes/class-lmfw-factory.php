<?php

namespace AI_Wizard\Includes;

//use LMFW\SDK\License;
class LMFW_Factory {

	public static function build_LMFW(){
		require_once gofpChatGPTPath . '/libraries/license-sdk/License.php';
		return new \LMFW\SDK\License(
			'AI Form Wizard',   // The plugin name is used to manage internationalization
			'http://localhost/testsite', //Replace with the URL of your license server (without the trailing slash)
			'ck_4fbb3c67634ea1a5e6b46d8c6023d2c647f9d274', //Customer key created in the license server
			'cs_f95cdf2570ca0f501cd8797121445d31f209a681', //Customer secret created in the license server
			[5494], //Set an array of the products IDs on your license server (if no product validation is needed, send an empty array)
			'ai-form-wizard_license', //Set a unique value to avoid conflict with other plugins
			'ai-form-wizard-is-valid',  //Set a unique value to avoid conflict with other plugins
			0 //How many days the valid object will be used before calling the license server
		);
	}
}