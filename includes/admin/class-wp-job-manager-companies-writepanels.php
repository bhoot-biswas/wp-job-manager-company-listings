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

	/**
	 * Displays metadata fields for Company Listings.
	 *
	 * @param int|WP_Post $post
	 */
	public function company_listing_data( $post ) {
		global $post, $thepostid, $wp_post_types;

		$thepostid = $post->ID;

		echo '<div class="wp_job_manager_meta_data">';

		wp_nonce_field( 'save_meta_data', 'job_manager_nonce' );

		do_action( 'job_manager_company_listing_data_start', $thepostid );

		foreach ( $this->company_listing_fields() as $key => $field ) {
			$type = ! empty( $field['type'] ) ? $field['type'] : 'text';

			if ( ! isset( $field['value'] ) && metadata_exists( 'post', $thepostid, $key ) ) {
				$field['value'] = get_post_meta( $thepostid, $key, true );
			}

			if ( ! isset( $field['value'] ) && isset( $field['default'] ) ) {
				$field['value'] = $field['default'];
			}

			if ( has_action( 'job_manager_input_' . $type ) ) {
				do_action( 'job_manager_input_' . $type, $key, $field );
			} elseif ( method_exists( $this, 'input_' . $type ) ) {
				call_user_func( [ $this, 'input_' . $type ], $key, $field );
			}
		}

		$user_edited_date = get_post_meta( $post->ID, '_company_edited', true );
		if ( $user_edited_date ) {
			echo '<p class="form-field">';
			// translators: %1$s is placeholder for singular name of the job listing post type; %2$s is the intl formatted date the listing was last modified.
			echo '<em>' . sprintf( esc_html__( '%1$s was last modified by the user on %2$s.', 'wp-job-manager-companies' ), esc_html( $wp_post_types['company_listing']->labels->singular_name ), esc_html( date_i18n( get_option( 'date_format' ), $user_edited_date ) ) ) . '</em>';
			echo '</p>';
		}

		do_action( 'job_manager_company_listing_data_end', $thepostid );

		echo '</div>';
	}

}

WP_Job_Manager_Companies_Writepanels::instance();
