<?php namespace Rep;
/**
 * @package WordPress
 * @subpackage Template
 *
 * Persistence Singleton
 *
 * @todo Add encryption
 * @todo Add Cookie Chaining
 * @todo Add File system storage
 *
 */

class Persistence {

	/**
	 * Key value store
	 *
	 * @var array
	 **/
	private static $store = array();

	/**
	 * Wheather to encrypt or not, requred mcrypt
	 *
	 * @var string
	 **/
	private static $encrypt = true;

	/**
	 * Call this method to get singleton
	 *
	 * @return Persistence
	 */
	public static function get_instance() {
		static $instance = null;
		if ($instance === null) {
				$instance = new Persistence();
		}
		return $instance;
	}

	/**
	 * Private constuctor so nobody else can instance it
	 *
	 */
	private function __construct() {}

	/**
	 * set a key value pair
	 *
	 * @return Bool
	 * @author
	 **/
	public static function set($k = null, $v = null)	{

		if ( !$k || !$v ) return false;

		// Default to cookie storage
		if ( !defined( 'PERSISTENCE_METHOD' ) ) define( 'PERSISTENCE_METHOD', 'COOKIE' );

		// We first store the value in memory
		self::$store[$k] = $v;

		// Store using selected method
		switch ( PERSISTENCE_METHOD ) {
			case 'COOKIE':
				return self::set_cookie( $k, $v );
				break;
			case 'SESSION':
				return self::set_session( $k, $v );
				break;
		}

		return false;

	}

	/**
	 * Get a value from the store
	 *
	 * @return Mixed
	 * @author
	 **/
	public static function get($k = null) {

		if ( !$k ) return null;

		// Defaul to cookie storage
		if ( !defined( 'PERSISTENCE_METHOD' ) ) define( 'PERSISTENCE_METHOD', 'COOKIE' );

		// Check if we have the key in memory, if so return the value
		if ( self::store_has_key( $k ) ) return self::store_get_value( $k );

		// Check the chosen store and return
		switch ( PERSISTENCE_METHOD ) {
			case 'COOKIE':
				return self::get_cookie( $k );
				break;
			case 'SESSION':
				return self::get_session( $k );
				break;
		}

		return null;

	}

	/**
	 * Checks the in memory store for specified key
	 *
	 * @return Bool
	 * @author
	 **/
	private static function store_has_key($key = null) {

		return array_key_exists( $key, self::$store );

	}

	/**
	 * Return the in memory value for supplied key
	 *
	 * @return Mixed
	 * @author
	 **/
	private static function store_get_value($k) {

		if ( self::store_has_key( $k ) ) {
			return self::$store[$k];
		}

		return null;

	}

	/**
	 * Stores key value pair in cookie
	 *
	 * @todo Add encrypt
	 * @todo Add multi cookie chaining
	 * @todo Ability to set expiry
	 *
	 * @return Bool
	 * @author
	 **/
	private static function set_cookie($k = null, $v = null) {

		if ( !$k || !$v ) return false;

		return setcookie( $k, $v, ( time() + (60*60*24) ), '/' );

	}

	/**
	 * Get value for supplied key
	 *
	 * @todo Add decryption
	 * @todo Add multi cookie chaining
	 *
	 * @return Mixed
	 * @author
	 **/
	private static function get_cookie($k = null) {

		if ( !$k ) return null;

		if ( isset( $_COOKIE[$k] ) ) return $_COOKIE[$k];

		return null;

	}

	/**
	 * Stores supplied key value pair in session
	 *
	 * @return void
	 * @author
	 **/
	public static function set_session( $k = null, $v = null ) {

		if ( !session_id() ) return false;

		$_SESSION[$k] = $v;

		return true;

	}

	/**
	 * Get value for supplied key from session
	 *
	 * @return void
	 * @author
	 **/
	private static function get_session( $k = null ) {

		if ( !$k ) return null;

		if ( isset( $_SESSION[$k] ) ) return $_SESSION[$k];

		return null;

	}

	/**
	 * Encrypt supplied data
	 *
	 * @return String
	 * @author
	 **/
	private static function encrypt($data, $key) {

		$key = md5($key);

		$encrypted = array();

		$m = mcrypt_module_open('rijndael-256', '', 'cbc', '');

		$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($m), MCRYPT_RAND);

		mcrypt_generic_init($m, $key, $iv);

		$data = mcrypt_generic($m, $data);

		mcrypt_generic_deinit($m);

		mcrypt_module_close($m);

		$encrypted = array('data' => $data, 'thingybob' => $iv);

		return $encrypted;

	}

	/**
	 * Decrypt supplied data
	 *
	 * @return Array
	 * @author
	 **/
	private static function decrypt($data, $key) {

		$key = md5($key);

		$m = mcrypt_module_open('rijndael-256', '', 'cbc', '');

		$iv = $data['thingybob'];

		mcrypt_generic_init($m, $key, $iv);

		$data = mdecrypt_generic($m, $data['data']);

		mcrypt_generic_deinit($m);

		mcrypt_module_close($m);

		return trim($data);
	}

}

?>