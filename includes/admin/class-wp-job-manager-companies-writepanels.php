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
		add_action( 'job_manager_save_company_listing', [ $this, 'save_company_listing_data' ], 20, 2 );
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
	 * Handles `save_post` action.
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 */
	public function save_post( $post_id, $post ) {
		if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( is_int( wp_is_post_revision( $post ) ) ) {
			return;
		}
		if ( is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}
		if (
			empty( $_POST['job_manager_nonce'] )
			|| ! wp_verify_nonce( wp_unslash( $_POST['job_manager_nonce'] ), 'save_meta_data' ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Nonce should not be modified.
		) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( 'company_listing' !== $post->post_type ) {
			return;
		}

		do_action( 'job_manager_save_company_listing', $post_id, $post );
	}

	/**
	 * Handles the actual saving of company listing data fields.
	 *
	 * @param int     $post_id
	 * @param WP_Post $post (Unused).
	 */
	public function save_company_listing_data( $post_id, $post ) {
		global $wpdb;

		// These need to exist.
		add_post_meta( $post_id, '_featured', 0, true );

		// Save fields.
		foreach ( $this->company_listing_fields() as $key => $field ) {
			if ( isset( $field['type'] ) && 'info' === $field['type'] ) {
				continue;
			}

			// Checkboxes that aren't sent are unchecked.
			if ( 'checkbox' === $field['type'] ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce check handled by WP core.
				if ( ! empty( $_POST[ $key ] ) ) {
					$_POST[ $key ] = 1;
				} else {
					$_POST[ $key ] = 0;
				}
			}

			// Expirey date.
			if ( '_company_author' === $key ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce check handled by WP core.
				if ( empty( $_POST[ $key ] ) ) {
					$_POST[ $key ] = 0;
				}

				// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce check handled by WP core.
				$input_post_author = $_POST[ $key ] > 0 ? intval( $_POST[ $key ] ) : 0;

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- Avoid update post within `save_post` action.
				$wpdb->update( $wpdb->posts, [ 'post_author' => $input_post_author ], [ 'ID' => $post_id ] );
			} elseif ( isset( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce check handled by WP core.
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing -- Input sanitized in registered post meta config; see WP_Job_Manager_Post_Types::register_meta_fields() and WP_Job_Manager_Post_Types::get_job_listing_fields() methods.
				update_post_meta( $post_id, $key, wp_unslash( $_POST[ $key ] ) );
			}
		}
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
			} elseif ( method_exists( 'WP_Job_Manager_Writepanels', 'input_' . $type ) ) {
				call_user_func( [ 'WP_Job_Manager_Writepanels', 'input_' . $type ], $key, $field );
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

	/**
	 * Returns configuration for custom fields on Company Listing posts.
	 *
	 * @return array
	 */
	public function company_listing_fields() {
		global $post_id;

		$current_user = wp_get_current_user();
		$fields_raw   = WP_Job_Manager_Companies_Post_Types::get_company_listing_fields();
		$fields       = [];

		if ( $current_user->has_cap( 'edit_others_job_listings' ) ) {
			$fields['_company_author'] = [
				'label'    => __( 'Posted by', 'wp-job-manager-companies' ),
				'type'     => 'author',
				'priority' => 0,
			];
		}

		foreach ( $fields_raw as $meta_key => $field ) {
			$show_in_admin = $field['show_in_admin'];
			if ( is_callable( $show_in_admin ) ) {
				$show_in_admin = (bool) call_user_func( $show_in_admin, true, $meta_key, $post_id, $current_user->ID );
			}

			if ( ! $show_in_admin ) {
				continue;
			}

			/**
			 * Check auth callback. Mirrors first 4 params of WordPress core's `auth_{$object_type}_meta_{$meta_key}` filter.
			 *
			 * @param bool   $allowed   Whether the user can edit the company listing meta. Default false.
			 * @param string $meta_key  The meta key.
			 * @param int    $object_id Object ID.
			 * @param int    $user_id   User ID.
			 */
			if ( ! call_user_func( $field['auth_edit_callback'], false, $meta_key, $post_id, $current_user->ID ) ) {
				continue;
			}

			$fields[ $meta_key ] = $field;
		}

		/**
		 * Filters company listing data fields shown in WP admin.
		 *
		 * To add company listing data fields, use the `job_manager_company_listing_data_fields` found in `includes/class-wp-job-manager-companies-post-types.php`.
		 *
		 * @since 0.1.0
		 *
		 * @param array    $fields  Company listing fields for WP admin. See `job_manager_company_listing_data_fields` filter for more information.
		 * @param int|null $post_id Post ID to get fields for. May be null.
		 */
		$fields = apply_filters( 'job_manager_company_listing_wp_admin_fields', $fields, $post_id );

		uasort( $fields, [ __CLASS__, 'sort_by_priority' ] );

		return $fields;
	}

	/**
	 * Sorts array of custom fields by priority value.
	 *
	 * @param array $a
	 * @param array $b
	 * @return int
	 */
	protected static function sort_by_priority( $a, $b ) {
		if ( ! isset( $a['priority'] ) || ! isset( $b['priority'] ) || $a['priority'] === $b['priority'] ) {
			return 0;
		}

		return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
	}

}

WP_Job_Manager_Companies_Writepanels::instance();
