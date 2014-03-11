<?php
/**
 *
 * @package   AWP_Reviews
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 *
 * @wordpress-plugin
 * Plugin Name: AWP Plugin and Theme Reviews
 * Plugin URI: https://github.com/AJZane/awp-reviews
 * Description: A plugin and theme review plugin for the Advanced WordPress group
 * Version: 0.1
 * Author: Advanced WP
 * Author URI: http://AdvancedWP.org
 * Text Domain:       awp-reviews-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/AJZane/awp-reviews
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-awp-reviews.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'AWP_Reviews', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'AWP_Reviews', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'AWP_Reviews', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-awp-reviews-admin.php' );
	add_action( 'plugins_loaded', array( 'AWP_Reviews_Admin', 'get_instance' ) );

}

function AWP() {
	return AWP_Reviews::get_instance();
}