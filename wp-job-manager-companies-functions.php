<?php
/**
 * Global WP Job Manager Companies functions.
 *
 * New global functions are discouraged whenever possible.
 *
 * @package WP_Job_Manager_Companies
 */

namespace BengalStudio;

/**
 * [dropdown_companies description]
 * @param  string $args [description]
 * @return [type]       [description]
 */
function dropdown_companies( $args = [] ) {
	$defaults = array(
		'post_type'   => 'company_listing',
		'post_status' => 'publish',
		'numberposts' => -1,
	);

	// Parse incoming $args into an array and merge it with $defaults
	$args = wp_parse_args( $args, $defaults );

	$dropdown_companies = array( '' => __( 'Select a company', 'wp-job-manager-companies' ) );
	$companies          = get_posts( $args );

	if ( $companies ) {
		foreach ( $companies as $company ) {
			$dropdown_companies[ $company->ID ] = $company->post_title;
		}
	}

	return $dropdown_companies;
}
