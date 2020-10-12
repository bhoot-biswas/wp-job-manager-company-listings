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
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );
	}

	/**
	 * Enqueues CSS and JS assets.
	 */
	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		if ( in_array( $screen->id, apply_filters( 'job_manager_admin_screen_ids', [ 'edit-company_listing', 'company_listing' ] ), true ) ) {
			wp_enqueue_style( 'job_manager_admin_css', JOB_MANAGER_PLUGIN_URL . '/assets/css/admin.css', [], JOB_MANAGER_VERSION );
		}
	}

}

WP_Job_Manager_Companies_Admin::instance();
