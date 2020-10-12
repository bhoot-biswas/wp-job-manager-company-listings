<?php
/**
 * Single company listing.
 *
 * This template can be overridden by copying it to yourtheme/job_manager/content-single-company_listing.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Bengal Studio
 * @package     WP_Job_Manager_Companies
 * @category    Template
 * @since       0.1.0
 * @version     0.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post;
?>
<div class="single_company_listing">
	<?php
		/**
		 * single_company_listing_start hook
		 */
		do_action( 'single_company_listing_start' );
	?>

	<div class="company_description">
		<?php wpjm_the_company_description(); ?>
	</div>

	<?php
		/**
		 * single_company_listing_end hook
		 */
		do_action( 'single_company_listing_end' );
	?>
</div>
