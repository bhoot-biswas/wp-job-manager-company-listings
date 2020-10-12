<?php
/**
 * Template Functions
 *
 * Template functions specifically created for company listings
 *
 * @author      Bengal Studio
 * @category    Core
 * @package     WP_Job_Manager_Companies
 * @version     0.1.0
 */

namespace BengalStudio;

/**
 * Displays the company description for the listing.
 *
 * @since 0.1.0
 * @param int|WP_Post $post
 */
function the_company_description( $post = null ) {
	$company_description = get_the_company_description( $post );
	if ( $company_description ) {
		WP_Job_Manager_Post_Types::output_kses_post( $company_description );
	}
}

/**
 * Gets the company description for the listing.
 *
 * @since 0.1.0
 * @param int|WP_Post $post (default: null).
 * @return string|bool|null
 */
function get_the_company_description( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || 'company_listing' !== $post->post_type ) {
		return null;
	}

	$description = apply_filters( 'the_company_description', wp_kses_post( $post->post_content ) );

	/**
	 * Filter for the company description.
	 *
	 * @since 0.1.0
	 * @param string      $company_description Job description to be filtered.
	 * @param int|WP_Post $post
	 */
	return apply_filters( 'wpjm_the_company_description', $description, $post );
}
