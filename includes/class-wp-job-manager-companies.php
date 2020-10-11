<?php
/**
 * File containing the class WP_Job_Manager_Companies.
 *
 * @package WP_Job_Manager_Companies
 */

namespace BengalStudio;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles core plugin hooks and action setup.
 * @var [type]
 */
final class WP_Job_Manager_Companies {

	/**
	 * The single instance of the class.
	 * @var [type]
	 */
	private static $instance = null;

	/**
	 * Ensures only one instance of WP_Job_Manager_Companies is loaded or can be loaded.
	 * @return [type] [description]
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * WP_Job_Manager_Companies Constructor.
	 */
	public function __construct() {
		// Includes.
		include_once WP_JOB_MANAGER_COMPANIES_PLUGIN_DIR . '/includes/class-wp-job-manager-companies-post-types.php';

		if ( is_admin() ) {
			include_once WP_JOB_MANAGER_COMPANIES_PLUGIN_DIR . '/includes/admin/class-wp-job-manager-companies-admin.php';
		}

		// Init classes.
		$this->post_types = WP_Job_Manager_Companies_Post_Types::instance();

		// Actions.
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
	}

	/**
	 * [register_scripts description]
	 * @return [type] [description]
	 */
	public function register_scripts() {
		$asset_file = include( WP_JOB_MANAGER_COMPANIES_PLUGIN_DIR . '/build/index.asset.php' );

		wp_register_script(
			'wp-job-manager-companies',
			WP_JOB_MANAGER_COMPANIES_PLUGIN_URL . '/build/index.js',
			$asset_file['dependencies'],
			$asset_file['version']
		);

		wp_register_style(
			'wp-job-manager-companies',
			WP_JOB_MANAGER_COMPANIES_PLUGIN_URL . '/build/index.css',
			[],
			$asset_file['version']
		);
	}

}

WP_Job_Manager_Companies::instance();
