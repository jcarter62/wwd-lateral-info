<?php
/**
 * User: jcarter
 * Date: 4/20/18
 */

class wwd_auth {
	private $PLUGIN_ROLE = 'WWDVIEW';
	private $isAuthenticated = false;
	private $username = '';

	/**
	 * wwd_auth constructor.
	 */
	public function __construct( ) {
		$this->isAuthenticated = false;
	}

	public function isIsAuthenticated() {
		$this->checkAuth();
		return $this->isAuthenticated;
	}

	/**
	 * Determine if currently logged in user is considered "authenticated"
	 */
	private function checkAuth() {
		$this -> isAuthenticated = false;
		$isLoggedIn              = is_user_logged_in();
		if ( $isLoggedIn ) {
			$this->username = wp_get_current_user()->user_login;
			$id           = wp_get_current_user()->ID;
			$meta         = get_user_meta( $id, 'wp_capabilities', false );
			foreach ( $meta[0] as $key => $value ) {
			    $k = strtoupper($key);
				if (( $k == $this->PLUGIN_ROLE ) or ( $k == 'ADMINISTRATOR' ) ) {
					$this->isAuthenticated = true;
				}
			}
		}
	}

	/**
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}
}