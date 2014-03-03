<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * Homepage_Control_Admin Class
 *
 * All functionality pertaining to the homepage control administration interface.
 *
 * @package WordPress
 * @subpackage Homepage_Control
 * @category Plugin
 * @author Matty
 * @since 1.0.0
 */
class Homepage_Control_Admin {
	/**
	 * Constructor function.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct () {
		add_action( 'admin_menu', array( $this, 'register_settings_screen' ), 15 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		// Register necessary scripts and styles, to enable others to enqueue them at will as well.
		add_action( 'admin_init', array( $this, 'register_enqueues' ) );
	} // End __construct()

	/**
	 * Register the admin screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_settings_screen () {
		if ( defined( 'THEME_FRAMEWORK' ) && 'woothemes' == constant( 'THEME_FRAMEWORK' ) ) {
			$parent = 'woothemes';
		} else {
			$parent = 'themes.php';
		}

		$this->_hook = add_submenu_page( $parent, __( 'Homepage', 'homepage-control' ), __( 'Homepage', 'homepage-control' ), 'edit_theme_options', 'homepage-control', array( $this, 'settings_screen' ) );

		// Enqueue our registered scripts and styles on our own admin screen by default.
		add_action( 'admin_print_scripts-' . $this->_hook, array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_print_styles-' . $this->_hook, array( $this, 'enqueue_styles' ) );
	} // End register_settings_screen()

	/**
	 * Output the markup for the settings screen.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function settings_screen () {
?>
		<div class="wrap homepage-control-wrap">
			<h2><?php _e( 'Homepage', 'homepage-control' ); ?></h2>
			<form action="options.php" method="post">
				<?php
					settings_fields( 'homepage_control_settings' );
					do_settings_sections( 'homepage_control' );
					submit_button( __( 'Save Changes', 'homepage-control' ) );
				?>
			</form>
		</div><!--/.wrap-->
<?php
	} // End settings_screen()

	public function register_settings () {
		register_setting( 'homepage_control_settings', 'homepage_control', array( $this, 'validate_settings' ) );
		add_settings_section( 'homepage_components', __( 'Homepage Components', 'homepage-control' ), array( $this, 'render_settings' ), 'homepage_control' );
	}

	public function render_settings () {
		$theme = wp_get_theme();
		$options = get_option( 'homepage_control' );
		$components = $this->_get_hooked_functions();
?>
		<p><?php printf( __( 'Re-order the homepage components in %s.', 'homepage-control' ), $theme->__get( 'Name' ) ); ?></p>
<?php
		if ( 0 < count( $components ) ) {
?>
		<table class="wp-list-table widefat fixed">
			<tbody>
<?php
$count = 0;
foreach ( $components as $k => $v ) {
	$count++;
	$class = 'odd alternate';
	if ( 0 == $count % 2 ) $class = 'even';
?>
			<tr valign="top" id="<?php echo esc_attr( $k ); ?>" class="item-row <?php echo esc_attr( $k ) . ' ' . $class; ?>">
				<td>
					<?php echo esc_html( $v['title'] ); ?>
				</td>
				<td class="actions"><span class="handle"></span><span class="visibility"></span></td>
			</tr>
<?php
}
?>
			</tbody>
			<thead>
				<th><?php _e( 'Component', 'homepage-control' ); ?></th>
				<th>&nbsp;</th>
			</thead>
		</table>
<?php
		} else {
			echo '<div class="message"><p>' . __( 'Your theme doesn\'t include any homepage components which are suitable for re-ordering or disabling.', 'homepage-control' ) . '</p></div>' . "\n";
		}
// TODO
?>
<input type="hidden" id="component_order" name="homepage_control['component_order']" />
<input type="hidden" id="disabled_components" name="homepage_control['disabled_components']" />
<?php
	} // End settings_screen()

	/**
	 * Validate the settings.
	 * @access  public
	 * @since   1.0.0
	 * @param   array $input Inputted data.
	 * @return  array        Validated data.
	 */
	public function validate_settings ( $input ) {
		$input['component_order'] = esc_html( $input['component_order'] );
		$input['disabled_components'] = esc_html( $input['disabled_components'] );
		return $input;
	} // End validate_settings()

	/**
	 * Register scripts and styles, preparing for enqueue.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function register_enqueues () {
		wp_register_script( Homepage_Control()->token . '-sortables', esc_url( Homepage_Control()->plugin_url . 'assets/js/sortables.js' ), array( 'jquery', 'jquery-ui-sortable' ), Homepage_Control()->version );
		wp_register_style( Homepage_Control()->token . '-admin',  esc_url( Homepage_Control()->plugin_url . 'assets/css/settings.css' ), '', Homepage_Control()->version );
	} // End register_enqueues()

	/**
	 * Enqueue JavaScripts for the administration screen.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_scripts () {
		wp_enqueue_script( Homepage_Control()->token . '-sortables' );
	} // End enqueue_scripts()

	/**
	 * Enqueue styles for the administration screen.
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		wp_enqueue_style( Homepage_Control()->token . '-admin' );
	} // End enqueue_styles()

	/**
	 * Retrive the functions hooked on to the "woo_homepage" hook.
	 * @access  private
	 * @since   1.0.0
	 * @return  array An array of the functions, grouped by function name, with a formatted title and their priority.
	 */
	private function _get_hooked_functions () {
		global $wp_filter;

		$response = array();

		if ( isset( $wp_filter['woo_homepage'] ) && 0 < count( $wp_filter['woo_homepage'] ) ) {
			foreach ( $wp_filter['woo_homepage'] as $k => $v ) {
				if ( is_array( $v ) ) {
					foreach ( $v as $i => $j ) {
						$response[$i] = array( 'title' => $this->_maybe_format_title( $i ), 'priority' => $k );
					}
				}
			}
		}

		return $response;
	} // End _get_hooked_functions()

	/**
	 * Format a given key into a title.
	 * @access  private
	 * @since   1.0.0
	 * @return  string A formatted title. If no formatting is possible, return the key.
	 */
	private function _maybe_format_title ( $key ) {
		$prefix = 'woo_display_';
		$title = $key;

		$title = str_replace( $prefix, '', $title );
		$title = str_replace( '_', ' ', $title );
		$title = ucwords( $title );

		return $title;
	} // End _maybe_format_title()
} // End Class
?>