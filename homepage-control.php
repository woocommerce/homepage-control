<?php
/**
 * Plugin Name: Homepage Control
 * Plugin URI: http://www.woothemes.com/products/homepage-control/
 * Description: Hi! I'm here to assist you with re-ordering or disabling components of your theme's homepage design.
 * Version: 2.0.2
 * Author: WooThemes
 * Author URI: http://woothemes.com/
 * Requires at least: 3.8.1
 * Tested up to: 4.6.0
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
	 * The name of the hook on which we will be working our magic.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $hook;

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
		$this->version 			= '2.0.2';
		$this->hook 			= (string)apply_filters( 'homepage_control_hook', 'homepage' );

		add_action( 'plugins_loaded', array( $this, 'maybe_migrate_data' ) );

		register_activation_hook( __FILE__, array( $this, 'install' ) );

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		/* Setup Customizer. */
		require_once( 'classes/class-homepage-control-customizer.php' );

		/* Reorder Components. */
		if ( ! is_admin() ) {
			add_action( 'get_header', array( $this, 'maybe_apply_restructuring_filter' ) );
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
	 * Load the localisation file.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'homepage-control', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	} // End load_plugin_textdomain()

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
		update_option( $this->token . '_version', $this->version );
	} // End _log_version_number()

	/**
	 * Migrate data from versions prior to 2.0.0.
	 * @access  public
	 * @since   2.0.0
	 * @return  void
	 */
	public function maybe_migrate_data () {
		$options = get_theme_mod( 'homepage_control' );

		if ( ! isset( $options ) ) {
			return; // Option is empty, probably first time installing the plugin.
		}

		if ( is_array( $options ) ) {
			$order = '';
			$disabled = '';
			$components = array();

			if ( isset( $options['component_order'] ) ) {
				$order = explode( ',', $options['component_order'] );

				if ( isset( $options['disabled_components'] ) ) {
					$disabled = explode( ',', $options['disabled_components'] );
				}

				if ( 0 < count( $order ) ) {
					foreach ( $order as $k => $v ) {
						if ( in_array( $v, $disabled ) ) {
							$components[] = '[disabled]' . $v; // Add disabled tag
						} else {
							$components[] = $v;
						}
					}
				}
			}

			$components = join( ',', $components );

			// Replace old data
			set_theme_mod( 'homepage_control', $components );
		}
	} // End maybe_migrate_data()

	/**
	 * Work through the stored data and display the components in the desired order, without the disabled components.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function maybe_apply_restructuring_filter () {
		$options = get_theme_mod( 'homepage_control' );
		$components = array();

		if ( isset( $options ) && '' != $options ) {
			$components = explode( ',', $options );

			// Remove all existing actions on woo_homepage.
			remove_all_actions( $this->hook );

			// Remove disabled components
			$components = $this->_maybe_remove_disabled_items( $components );

			// Perform the reordering!
			if ( 0 < count( $components ) ) {
				$count = 5;
				foreach ( $components as $k => $v ) {
					if (strpos( $v, '@' ) !== FALSE) {
						$obj_v = explode( '@' , $v );
						if ( class_exists( $obj_v[0] ) && method_exists( $obj_v[0], $obj_v[1] ) ) {
							add_action( $this->hook, array( $obj_v[0], $obj_v[1] ), $count );
						} // End If Statement
					} else {
						if ( function_exists( $v ) ) {
							add_action( $this->hook, esc_attr( $v ), $count );
						}
					} // End If Statement

					$count + 5;
				}
			}
		}
	} // End maybe_apply_restructuring_filter()

	/**
	 * Maybe remove disabled items from the main ordered array.
	 * @access  private
	 * @since   1.0.0
	 * @param   array $components 	Array with components order.
	 * @return  array           	Re-ordered components with disabled components removed.
	 */
	private function _maybe_remove_disabled_items( $components ) {
		if ( 0 < count( $components ) ) {
			foreach ( $components as $k => $v ) {
				if ( false !== strpos( $v, '[disabled]' ) ) {
					unset( $components[ $k ] );
				}
			}
		}
		return $components;
	} // End _maybe_remove_disabled_items()
} // End Class
?>
