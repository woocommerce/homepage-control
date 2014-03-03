<?php
/**
 * Plugin Name: Homepage Control
 * Plugin URI: http://www.woothemes.com/products/homepage-control/
 * Description: Hi! I'm here to assist you with re-ordering or disabling components of your theme's homepage design.
 * Version: 1.0.0
 * Author: Matty Cohen
 * Author URI: http://matty.co.za/
 * Requires at least: 3.8.1
 * Tested up to: 3.8.1
 *
 * Text Domain: homepage-control
 * Domain Path: /languages/
 *
 * @package Homepage_Control
 * @category Core
 * @author Matty
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of Homepage_Control to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Homepage_Control
 */
function Homepage_Control() {
	return Homepage_Control::instance();
} // End Homepage_Control()

Homepage_Control();

/**
 * Main Homepage_Control Class
 *
 * @class Homepage_Control
 * @version	1.0.0
 * @since 1.0.0
 * @package	Kudos
 * @author Matty
 */
final class Homepage_Control {
	/**
	 * Homepage_Control The single instance of Homepage_Control.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	/**
	 * An instance of the Homepage_Control_Admin class.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct () {
		$this->token 			= 'homepage-control';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.0.0';

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		/* Conditionally load the admin. */
		if ( is_admin() ) {
			require_once( 'classes/class-homepage-control-admin.php' );
			$this->admin = new Homepage_Control_Admin();
		}
	} // End __construct()

	/**
	 * Main Homepage_Control Instance
	 *
	 * Ensures only one instance of Homepage_Control is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Homepage_Control()
	 * @return Main Kudos instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		// Log the version number.
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number()
} // End Class
?>
