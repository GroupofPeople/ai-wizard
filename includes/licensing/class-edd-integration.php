<?php

namespace AI_Wizard\Includes\Licensing;

use WP_Error;

class EDD_Integration {
	const LICENSE_NONE = 'none';
	const LICENSE_VALID = 'valid';
	const LICENSE_INVALID = 'invalid';
	const LICENSE_EXPIRED = 'expired';
	const LICENSE_DISABLED = 'disabled';


	const LICENSE_SITE_INACTIVE = 'site_inactive';
	const LICENSE_NO_ACTIVATION_LEFT = 'no_activations_left';
	const LICENSE_MISSING = 'missing';
	const LICENSE_MISMATCH = 'mismatch';

	private static $instance;

	private function __construct() {
	}

	public static function get_instance() {
		return new self();
	}

	private function get_license_key(){
		return get_option('AI_Wizard_license_status');
	}

	public function activate_license($license) {
		$current_status = get_option( 'AI_Wizard_license_status' );

		if ( $current_status === self::LICENSE_VALID ) {
			return new WP_Error( 'AI_Wizard_a_license_is_active', 'There is already a license active' );
		}

		$response = $this->call_api( 'activate_license', $license );

		if( $response->success ){
			update_option('AI_Wizard_license_status', $response->license);

			return 'valid';
		}
		return $response->error;
	}

	private function call_api( $action, $license ) {
		$base_url = 'https://ai-wizard.groupofpeople.net';

		$api_params = array(
			'edd_action' => $action,
			'license'    => $license,
			'item_id'    => 714, // the name of our product in EDD
			'url'        => home_url()
		);

		$response = wp_remote_post( $base_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

		return json_decode( wp_remote_retrieve_body( $response ) );
	}

	public function deactivate_license() {
		$response = $this->call_api( 'deactivate_license', get_option('AI_Wizard_license_key') );
		error_log("Deactivation response: ".print_r($response, true));
	}

	public function check_license() {
		$response = $this->call_api( 'check_license', get_option('AI_Wizard_license_key') );
		if( $response->success ){
			update_option('AI_Wizard_license_status', $response->license);
		}else{
			update_option('AI_Wizard_license_status', $response->error);
		}
	}

	public function get_version() {
		$this->call_api( 'get_version', get_option() );
	}

//	function edd_sample_activate_license() {
//
//		// listen for our activate button to be clicked
//		if ( isset( $_POST['edd_license_activate'] ) ) {
//
//			// run a quick security check
//			if ( ! check_admin_referer( 'edd_sample_nonce', 'edd_sample_nonce' ) ) {
//				return;
//			} // get out if we didn't click the Activate button
//
//			// retrieve the license from the database
//			$license = trim( get_option( 'edd_sample_license_key' ) );
//
//
//			// data to send in our API request
//
//
//			// Call the custom API.
//			$response = wp_remote_post( EDD_SAMPLE_STORE_URL, array(
//				'timeout'   => 15,
//				'sslverify' => false,
//				'body'      => $api_params
//			) );
//
//			// make sure the response came back okay
//			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
//
//				$message = ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.' );
//
//			} else {
//
//				$license_data = json_decode( wp_remote_retrieve_body( $response ) );
//
//				if ( false === $license_data->success ) {
//
//					switch ( $license_data->error ) {
//
//						case 'expired' :
//
//							$message = sprintf( __( 'Your license key expired on %s.' ), date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) ) );
//							break;
//
//						case 'revoked' :
//
//							$message = __( 'Your license key has been disabled.' );
//							break;
//
//						case 'missing' :
//
//							$message = __( 'Invalid license.' );
//							break;
//
//						case 'invalid' :
//						case 'site_inactive' :
//
//							$message = __( 'Your license is not active for this URL.' );
//							break;
//
//						case 'item_name_mismatch' :
//
//							$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), EDD_SAMPLE_ITEM_NAME );
//							break;
//
//						case 'no_activations_left':
//
//							$message = __( 'Your license key has reached its activation limit.' );
//							break;
//
//						default :
//
//							$message = __( 'An error occurred, please try again.' );
//							break;
//					}
//
//				}
//
//			}
//
//			// Check if anything passed on a message constituting a failure
//			if ( ! empty( $message ) ) {
//				$base_url = admin_url( 'plugins.php?page=' . EDD_SAMPLE_PLUGIN_LICENSE_PAGE );
//				$redirect = add_query_arg( array(
//					'sl_activation' => 'false',
//					'message'       => urlencode( $message )
//				), $base_url );
//
//				wp_redirect( $redirect );
//				exit();
//			}
//
//			// $license_data->license will be either "valid" or "invalid"
//
//			update_option( 'edd_sample_license_status', $license_data->license );
//			wp_redirect( admin_url( 'plugins.php?page=' . EDD_SAMPLE_PLUGIN_LICENSE_PAGE ) );
//			exit();
//		}
//	}

	public function test() {
	}

	public function is_valid(){
		$current_status = get_option( 'AI_Wizard_license_status' );
		return $current_status === self::LICENSE_VALID;
	}
}