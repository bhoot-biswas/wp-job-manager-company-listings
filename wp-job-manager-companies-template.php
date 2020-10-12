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
