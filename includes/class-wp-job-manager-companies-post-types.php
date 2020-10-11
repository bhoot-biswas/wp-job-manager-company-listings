<?php
/**
 * File containing the class WP_Job_Manager_Companies_Post_Types.
 *
 * @package WP_Job_Manager_Companies
 */

namespace BengalStudio;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * HHandles displays and hooks for the Company Listing custom post type.
 * @var [type]
 */
class WP_Job_Manager_Companies_Post_Types {

	/**
	 * The single instance of the class.
	 * @var [type]
	 */
	private static $instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
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
		add_action( 'init', [ $this, 'register_post_types' ], 0 );
	}

	/**
	 * Registers the custom post type and taxonomies.
	 */
	public function register_post_types() {
		if ( post_type_exists( 'company_listing' ) ) {
			return;
		}

		/**
		 * Post types
		 */
		$singular            = __( 'Company', 'wp-job-manager-companies' );
		$plural              = __( 'Companies', 'wp-job-manager-companies' );
		$permalink_structure = [
			'company_rewrite_slug'            => _x( 'company', 'Company permalink - resave permalinks after changing this', 'wp-job-manager-companies' ),
			'companiess_archive_rewrite_slug' => 'company-listings',
		];

		/**
		 * Set whether to add archive page support when registering the company listing post type.
		 * @var [type]
		 */
		if ( apply_filters( 'job_manager_companies_enable_company_archive_page', current_theme_supports( 'job-manager-templates' ) ) ) {
			$has_archive = $permalink_structure['companiess_archive_rewrite_slug'];
		} else {
			$has_archive = false;
		}

		$rewrite = [
			'slug'       => $permalink_structure['company_rewrite_slug'],
			'with_front' => false,
			'feeds'      => true,
			'pages'      => false,
		];

		register_post_type(
			'company_listing',
			apply_filters(
				'register_post_type_company_listing',
				[
					'labels'                => [
						'name'                  => $plural,
						'singular_name'         => $singular,
						'menu_name'             => __( 'Company Listings', 'wp-job-manager-companies' ),
						// translators: Placeholder %s is the plural label of the job listing post type.
						'all_items'             => sprintf( __( 'All %s', 'wp-job-manager-companies' ), $plural ),
						'add_new'               => __( 'Add New', 'wp-job-manager-companies' ),
						// translators: Placeholder %s is the singular label of the job listing post type.
						'add_new_item'          => sprintf( __( 'Add %s', 'wp-job-manager-companies' ), $singular ),
						'edit'                  => __( 'Edit', 'wp-job-manager-companies' ),
						// translators: Placeholder %s is the singular label of the job listing post type.
						'edit_item'             => sprintf( __( 'Edit %s', 'wp-job-manager-companies' ), $singular ),
						// translators: Placeholder %s is the singular label of the job listing post type.
						'new_item'              => sprintf( __( 'New %s', 'wp-job-manager-companies' ), $singular ),
						// translators: Placeholder %s is the singular label of the job listing post type.
						'view'                  => sprintf( __( 'View %s', 'wp-job-manager-companies' ), $singular ),
						// translators: Placeholder %s is the singular label of the job listing post type.
						'view_item'             => sprintf( __( 'View %s', 'wp-job-manager-companies' ), $singular ),
						// translators: Placeholder %s is the singular label of the job listing post type.
						'search_items'          => sprintf( __( 'Search %s', 'wp-job-manager-companies' ), $plural ),
						// translators: Placeholder %s is the singular label of the job listing post type.
						'not_found'             => sprintf( __( 'No %s found', 'wp-job-manager-companies' ), $plural ),
						// translators: Placeholder %s is the plural label of the job listing post type.
						'not_found_in_trash'    => sprintf( __( 'No %s found in trash', 'wp-job-manager-companies' ), $plural ),
						// translators: Placeholder %s is the singular label of the job listing post type.
						'parent'                => sprintf( __( 'Parent %s', 'wp-job-manager-companies' ), $singular ),
						'featured_image'        => __( 'Company Logo', 'wp-job-manager-companies' ),
						'set_featured_image'    => __( 'Set company logo', 'wp-job-manager-companies' ),
						'remove_featured_image' => __( 'Remove company logo', 'wp-job-manager-companies' ),
						'use_featured_image'    => __( 'Use as company logo', 'wp-job-manager-companies' ),
					],
					// translators: Placeholder %s is the plural label of the job listing post type.
					'description'           => sprintf( __( 'This is where you can create and manage %s.', 'wp-job-manager-companies' ), $plural ),
					'public'                => true,
					'show_ui'               => true,
					'capability_type'       => 'job_listing',
					'map_meta_cap'          => true,
					'publicly_queryable'    => true,
					'exclude_from_search'   => false,
					'hierarchical'          => false,
					'rewrite'               => $rewrite,
					'query_var'             => true,
					'supports'              => [ 'title', 'editor', 'custom-fields', 'publicize', 'thumbnail', 'author' ],
					'has_archive'           => $has_archive,
					'show_in_nav_menus'     => false,
					'delete_with_user'      => true,
					'show_in_rest'          => true,
					'rest_base'             => 'company-listings',
					'rest_controller_class' => 'WP_REST_Posts_Controller',
					'template'              => [ [ 'core/freeform' ] ],
					'template_lock'         => 'all',
					'menu_position'         => 30,
					'menu_icon'             => 'dashicons-building',
				]
			)
		);
	}

	/**
	 * Returns configuration for custom fields on Company Listing posts.
	 *
	 * @return array See `job_manager_company_listing_data_fields` filter for more documentation.
	 */
	public static function get_job_listing_fields() {
		$default_field = [
			'label'              => null,
			'placeholder'        => null,
			'description'        => null,
			'priority'           => 10,
			'value'              => null,
			'default'            => null,
			'classes'            => [],
			'type'               => 'text',
			'data_type'          => 'string',
			'show_in_admin'      => true,
			'show_in_rest'       => false,
			'auth_edit_callback' => [ __CLASS__, 'auth_check_can_edit_job_listings' ],
			'auth_view_callback' => null,
			'sanitize_callback'  => [ __CLASS__, 'sanitize_meta_field_based_on_input_type' ],
		];

		$fields = [
			'_company_location' => [
				'label'         => __( 'Location', 'wp-job-manager-companies' ),
				'placeholder'   => __( 'e.g. "London"', 'wp-job-manager-companies' ),
				'description'   => __( 'Leave this blank if the location is not important.', 'wp-job-manager-companies' ),
				'priority'      => 1,
				'data_type'     => 'string',
				'show_in_admin' => true,
				'show_in_rest'  => true,
			],
			'_company_name'     => [
				'label'         => __( 'Company Name', 'wp-job-manager-companies' ),
				'placeholder'   => '',
				'priority'      => 3,
				'data_type'     => 'string',
				'show_in_admin' => true,
				'show_in_rest'  => true,
			],
			'_company_website'  => [
				'label'             => __( 'Company Website', 'wp-job-manager-companies' ),
				'placeholder'       => '',
				'priority'          => 4,
				'data_type'         => 'string',
				'show_in_admin'     => true,
				'show_in_rest'      => true,
				'sanitize_callback' => [ __CLASS__, 'sanitize_meta_field_url' ],
			],
			'_company_tagline'  => [
				'label'         => __( 'Company Tagline', 'wp-job-manager-companies' ),
				'placeholder'   => __( 'Brief description about the company', 'wp-job-manager-companies' ),
				'priority'      => 5,
				'data_type'     => 'string',
				'show_in_admin' => true,
				'show_in_rest'  => true,
			],
			'_company_twitter'  => [
				'label'         => __( 'Company Twitter', 'wp-job-manager-companies' ),
				'placeholder'   => '@yourcompany',
				'priority'      => 6,
				'data_type'     => 'string',
				'show_in_admin' => true,
				'show_in_rest'  => true,
			],
			'_company_video'    => [
				'label'             => __( 'Company Video', 'wp-job-manager-companies' ),
				'placeholder'       => __( 'URL to the company video', 'wp-job-manager-companies' ),
				'type'              => 'file',
				'priority'          => 8,
				'data_type'         => 'string',
				'show_in_admin'     => true,
				'show_in_rest'      => true,
				'sanitize_callback' => [ __CLASS__, 'sanitize_meta_field_url' ],
			],
			'_featured'         => [
				'label'              => __( 'Featured Listing', 'wp-job-manager-companies' ),
				'type'               => 'checkbox',
				'description'        => __( 'Featured listings will be sticky during searches, and can be styled differently.', 'wp-job-manager-companies' ),
				'priority'           => 10,
				'data_type'          => 'integer',
				'show_in_admin'      => true,
				'show_in_rest'       => true,
				'auth_edit_callback' => [ __CLASS__, 'auth_check_can_manage_job_listings' ],
			],
		];

		/**
		 * Filters company listing data fields.
		 * @var [type]
		 */
		$fields = apply_filters( 'job_manager_company_listing_data_fields', $fields );

		// Ensure default fields are set.
		foreach ( $fields as $key => $field ) {
			$fields[ $key ] = array_merge( $default_field, $field );
		}

		return $fields;
	}

}
