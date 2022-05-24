<?php
/**
 *
 * @link              https://sevengits.com
 * @since             1.0.0
 * @package           Phonepe
 *
 * @wordpress-plugin
 * Plugin Name:       Integrate PhonePe with Woocommerce
 * Plugin URI:        https://sevengits.com/plugin/phonepe
 * Description:       Allows customers to use PhonePe payment gateway with the WooCommerce.
 * Version:           1.0.2
 * Author:            Sevengits
 * Author URI:        https://sevengits.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       phonepe
 * Domain Path:       /languages
 * WC requires at least: 3.7
 * WC tested up to: 	 5.9
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SGPPY_VERSION', '1.0.2' );
define( 'SGPPY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-phonepe.php';
require plugin_dir_path( __FILE__ ) . 'plugin-deactivation-survey/deactivate-feedback-form.php';
/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function sgppy_run_phonepe() {

	$plugin = new Phonepe();
	$plugin->run();

}

// Make sure WooCommerce is active
if(in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	sgppy_run_phonepe();
}
add_filter('sgits_deactivate_feedback_form_plugins', function($plugins) {

	$plugins[] = (object)array(
		'slug'		=> 'wc-phonepe',
		'version'	=> '1.0.2'
	);

	return $plugins;

});