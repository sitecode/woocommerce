<?php
/**
 * WooCommerce Auth
 *
 * Handles wc-auth endpoint requests
 *
 * @author   WooThemes
 * @category API
 * @package  WooCommerce/API
 * @since    2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Auth' ) ) :

class WC_Auth {

	/**
	 * Version
	 */
	const VERSION = 1;

	/**
	 * Setup class
	 *
	 * @since 2.4.0
	 */
	public function __construct() {
		// Register auth endpoint
		add_action( 'init', array( __CLASS__, 'add_endpoint' ), 0 );

		// Handle auth requests
		add_action( 'parse_request', array( $this, 'handle_auth_requests' ), 0 );
	}

	/**
	 * Add auth endpoint
	 *
	 * @since 2.4.0
	 */
	public static function add_endpoint() {
		add_rewrite_endpoint( 'wc-auth', EP_ROOT );
	}

	/**
	 * Handle auth requests
	 *
	 * @since 2.4.0
	 */
	public function handle_auth_requests() {
		global $wp;

		if ( ! empty( $_GET['wc-auth'] ) ) {
			$wp->query_vars['wc-auth'] = $_GET['wc-auth'];
		}

		// wc-auth endpoint requests
		if ( ! empty( $wp->query_vars['wc-auth'] ) ) {
			ob_start();

			$method = strtolower( wc_clean( $wp->query_vars['wc-auth'] ) );

			if ( is_user_logged_in() ) {
				$method = 'grant_access';
			}

			if ( 'login' == $method && ! is_user_logged_in() ) { // Login endpoint
				wc_get_template( 'auth/form-login.php' );

				exit;
			} else if ( 'grant_access' == $method && current_user_can( 'manage_woocommerce' ) ) {
				wc_get_template( 'auth/form-login.php' );

				exit;
			}

			wp_die( __( 'You do not have permissions to access this page!' ), __( 'Access Denied', 'woocommerce' ), array( 'response' => 401 ) );
		}
	}
}

endif;

return new WC_Auth();