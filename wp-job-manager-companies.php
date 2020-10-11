<?php
/**
 * Plugin Name:     Company Listings for WP Job Manager
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          Bengal Studio
 * Author URI:      https://bengal-studio.com/
 * Text Domain:     wp-job-manager-companies
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         WP_Job_Manager_Companies
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define constants.
define( 'WP_JOB_MANAGER_COMPANIES_VERSION', '0.1.0' );
define( 'WP_JOB_MANAGER_COMPANIES_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'WP_JOB_MANAGER_COMPANIES_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
define( 'WP_JOB_MANAGER_COMPANIES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Require the main WP_Job_Manager_Companies class.
require_once dirname( __FILE__ ) . '/includes/class-wp-job-manager-companies.php';
