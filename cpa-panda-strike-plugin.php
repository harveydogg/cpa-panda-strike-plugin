<?php

/**
* Plugin Name:       Panda Strike
* Plugin URI:        https://github.com/pandastrike/cpa-panda-strike-plugin
* Description:       Misc tweaks and functionality for the Cessna Pilots Association.
* Version:           0.0.3
* Author:            Panda
* Author URI:        https://pandastrike.com
* Text Domain:       cpa-panda-strike-plugin
* Domain Path:       /languages
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class PandaStrike {

	private static $instance;

	private $export_headers = array(
		'CPA Number' => 'CPA Number',
		'first_name' => 'first_name',
		'last_name' => 'last_name',
		'billing_first_name' => 'billing_first_name',
		'billing_last_name' => 'billing_last_name',
		'billing_company' => 'billing_company',
		'billing_address_1' => 'billing_address_1',
		'billing_address_2' => 'billing_address_2',
		'billing_city' => 'billing_city',
		'billing_postcode' => 'billing_postcode',
		'billing_country' => 'billing_country',
		'billing_state' => 'billing_state',
		'billing_email' => 'billing_email',
		'billing_phone' => 'billing_phone',
		'shipping_first_name' => 'shipping_first_name',
		'shipping_last_name' => 'shipping_last_name',
		'shipping_company' => 'shipping_company',
		'shipping_address_1' => 'shipping_address_1',
		'shipping_address_2' => 'shipping_address_2',
		'shipping_city' => 'shipping_city',
		'shipping_postcode' => 'shipping_postcode',
		'shipping_country' => 'shipping_country',
		'shipping_state' => 'shipping_state',
		'shipping_email' => 'shipping_email',
		'shipping_phone' => 'shipping_phone',
	);


	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			$className = __CLASS__;
			self::$instance = new $className;
		}
		return self::$instance;
	}

	protected function __construct() {

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		add_action( 'profile_update', array( $this, 'update_display_name' ), 10, 1 );
		add_action( 'user_register', array( $this, 'update_display_name' ), 10, 1 );

		add_filter( 'wc_membership_plan_members_area_sections', array( $this, 'wc_remove_member_area_sections' ) );

		add_filter( 'wc_memberships_members_area_my-membership-details_actions', array( $this, 'wc_memberships_members_area_my_membership_details_actions' ), 10, 2 );

		// export related
		add_filter( 'wc_memberships_csv_export_user_memberships_headers', array( $this, 'wc_memberships_export_headers' ), 10, 2 );
		add_filter( 'wc_memberships_csv_export_user_memberships_row', array( $this, 'wc_memberships_row' ), 10, 3 );

	}

	private function __clone() {}

	private function __wakeup() {}

	public function load_textdomain() {
		load_plugin_textdomain( 'panda-strike', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	// Force the display name to First and Last name
	public function update_display_name( $user_id ) {

		$user_info = get_userdata( $user_id );

		if ( $user_info instanceof WP_User ) {

			$display_name = array();

			if ( $user_info->user_firstname ) {
				array_push( $display_name, $user_info->user_firstname );
			}

			if ( $user_info->user_lastname ) {
				array_push( $display_name, $user_info->user_lastname );
			}

			if ( ! empty( $display_name ) ) {

				// don't want no infinite loop aye
				remove_action( 'profile_update', array( $this, 'update_display_name' ), 10, 1 );

				wp_update_user( array(
					'ID' => $user_id,
					'display_name' => implode( ' ', $display_name ),
				));
			}
		}
	}

	// Remove unused woocommerce member area navigation items
	function wc_remove_member_area_sections( $sections ) {

		if ( ! empty( $sections ) ) {
			if ( $sections['my-membership-content'] ) {
				unset( $sections['my-membership-content'] );
			}

			if ( $sections['my-membership-discounts'] ) {
				unset( $sections['my-membership-discounts'] );
			}

			if ( $sections['my-membership-notes'] ) {
				unset( $sections['my-membership-notes'] );
			}

			// Reversing lands the user on the Manage page instead of Products
			$sections = array_reverse( $sections );
		}

		return $sections;
	}

	// rename Cancel to be more obvious
	function wc_memberships_members_area_my_membership_details_actions( $actions, WC_Memberships_User_Membership $user_membership ) {

		if ( $actions['cancel'] ) {
			$name = $user_membership->get_plan()->name;
			$text = isset( $name ) ? $name : 'Membership';
			$actions['cancel']['name'] = sprintf( __('Cancel %s', 'woocommerce-memberships'), $text );
		}

		return $actions;
	}

	function wc_memberships_export_headers( $headers, $class_instance ) {
		foreach( $this->export_headers AS $k => $v) {
			$headers[ $k ] = $v;
		}
		return $headers;
	}

	function wc_memberships_row( $row, $user_membership, $class_instance ) {
		$user_id = $user_membership->get_user_id();
		$user_meta = array_map( function( $a ) { return $a[0]; }, get_user_meta( $user_id ) );

		foreach ( $this->export_headers as $k => $v ) {
			$row[ $k ] = empty( $user_meta[ $k ] ) ? '' : $user_meta[ $k ];
		}

		return $row;
	}

}

/**
* Return the singleton
*/
function panda_strike() {
	return PandaStrike::instance();
}

panda_strike();
