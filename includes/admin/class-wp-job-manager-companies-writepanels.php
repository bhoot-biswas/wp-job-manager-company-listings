<?php
/**
 * File containing the class WP_Job_Manager_Companies_Writepanels.
 *
 * @package WP_Job_Manager_Companies
 */

namespace BengalStudio;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles the management of Company Listing meta fields.
 * @var [type]
 */
class WP_Job_Manager_Companies_Writepanels {

	/**
	 * The single instance of the class.
	 * @var [type]
	 */
	private static $instance = null;

	/**
	 * Ensures only one instance of WP_Job_Manager_Companies_Writepanels is loaded or can be loaded.
	 * @return [type] [description]
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );
		add_action( 'save_post', [ $this, 'save_post' ], 1, 2 );
		add_action( 'job_manager_companies_save_company_listing', [ $this, 'save_company_listing_data' ], 20, 2 );
	}

	/**
	 * Handles the hooks to add custom field meta boxes.
	 */
	public function add_meta_boxes() {
		global $wp_post_types;

		// translators: Placeholder %s is the singular name for a company listing post type.
		add_meta_box( 'company_listing_data', sprintf( __( '%s Data', 'wp-job-manager-companies' ), $wp_post_types['company_listing']->labels->singular_name ), [ $this, 'company_listing_data' ], 'company_listing', 'normal', 'high' );
	}

}

WP_Job_Manager_Companies_Writepanels::instance();
