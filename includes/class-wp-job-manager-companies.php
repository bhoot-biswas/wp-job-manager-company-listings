<?php
/**
 * WP_Job_Manager_Companies setup
 *
 * @package WP_Job_Manager_Companies
 */

namespace BengalStudio;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main WP_Job_Manager_Companies Class.
 */
final class WP_Job_Manager_Companies {

	/**
	 * The single instance of the class.
	 *
	 * @var WP_Job_Manager_Companies
	 */
	protected static $_instance = null; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Main WP_Job_Manager_Companies Instance.
	 * Ensures only one instance of WP_Job_Manager_Companies is loaded or can be loaded.
	 *
	 * @return WP_Job_Manager_Companies - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * WP_Job_Manager_Companies Constructor.
	 */
	public function __construct() {
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
