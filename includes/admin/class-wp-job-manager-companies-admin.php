<?php
/**
 * File containing the class WP_Job_Manager_Companies_Admin.
 *
 * @package WP_Job_Manager_Companies
 */

namespace BengalStudio;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles front admin page for WP Job Manager Companies.
 * @var [type]
 */
class WP_Job_Manager_Companies_Admin {

	/**
	 * The single instance of the class.
	 * @var [type]
	 */
	private static $instance = null;

	/**
	 * Ensures only one instance of WP_Job_Manager_Companies_Admin is loaded or can be loaded.
	 * @return [type] [description]
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * WP_Job_Manager_Companies_Admin Constructor.
	 */
	public function __construct() {
		include_once dirname( __FILE__ ) . '/class-wp-job-manager-companies-writepanels.php';
	}

}

WP_Job_Manager_Companies_Admin::instance();
