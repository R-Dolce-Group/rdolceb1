<?php
/**
 * WooCommerce Admin Custom Order Fields
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Admin Custom Order Fields to newer
 * versions in the future. If you wish to customize WooCommerce Admin Custom Order Fields for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-admin-custom-order-fields/ for more information.
 *
 * @package     WC-Admin-Custom-Order-Fields/Classes
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2017, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Custom Order Fields Export Handler class
 *
 * @since 1.2.0
 */
class WC_Admin_Custom_Order_Fields_Export_Handler {


	/**
	 * Setup class
	 *
	 * @since 1.2.0
	 */
	public function __construct() {

		// Customer / Order CSV Export column headers/data
		add_filter( 'wc_customer_order_csv_export_order_headers', array( $this, 'add_fields_to_csv_export_column_headers' ), 10, 2 );
		add_filter( 'wc_customer_order_csv_export_order_row',     array( $this, 'add_fields_to_csv_export_column_data' ), 10, 4 );

		// Customer / Order XML Export admin custom fields
		add_filter( 'wc_customer_order_xml_export_suite_order_data', array( $this, 'add_fields_to_xml_export_data' ), 10, 2 );
	}


	/**
	 * Adds support for Customer/Order CSV Export by adding a column header for
	 * each registered admin order field
	 *
	 * @since 1.2.0
	 * @param array $headers existing array of header key/names for the CSV export
	 * @return array
	 */
	public function add_fields_to_csv_export_column_headers( $headers, $csv_generator ) {

		$field_headers = array();

		foreach ( wc_admin_custom_order_fields()->get_order_fields() as $field_id => $field ) {
			$field_headers[ 'admin_custom_order_field_' . $field_id ] = 'admin_custom_order_field:' . str_replace( '-', '_', sanitize_title( $field->label ) ) . '_' . $field_id;
		}

		return array_merge( $headers, $field_headers );
	}


	/**
	 * Adds support for Customer/Order CSV Export by adding data for admin order fields
	 *
	 * @since 1.2.0
	 * @param array $order_data generated order data matching the column keys in the header
	 * @param WC_Order $order order being exported
	 * @param \WC_Customer_Order_CSV_Export_Generator $csv_generator instance
	 * @return array
	*/
	public function add_fields_to_csv_export_column_data( $order_data, $order, $csv_generator ) {

		$field_data       = array();
		$new_order_data   = array();
		$one_row_per_item = false;

		foreach ( wc_admin_custom_order_fields()->get_order_fields( SV_WC_Order_Compatibility::get_prop( $order, 'id' ) ) as $field_id => $field ) {
			$field_data[ 'admin_custom_order_field_' . $field_id ] = $field->get_value_formatted();
		}

		// determine if the selected format is "one row per item"
		if ( version_compare( wc_customer_order_csv_export()->get_version(), '4.0.0', '<' ) ) {

			$one_row_per_item = ( 'default_one_row_per_item' === $csv_generator->order_format || 'legacy_one_row_per_item' === $csv_generator->order_format );

		// v4.0.0 - 4.0.2
		} elseif ( ! isset( $csv_generator->format_definition ) ) {

			// get the CSV Export format definition
			$format_definition = wc_customer_order_csv_export()->get_formats_instance()->get_format( $csv_generator->export_type, $csv_generator->export_format );

			$one_row_per_item = isset( $format_definition['row_type'] ) && 'item' === $format_definition['row_type'];

		// v4.0.3+
		} else {

			$one_row_per_item = 'item' === $csv_generator->format_definition['row_type'];
		}

		if ( $one_row_per_item ) {

			foreach ( $order_data as $data ) {
				$new_order_data[] = array_merge( $field_data, (array) $data );
			}

		} else {

			$new_order_data = array_merge( $field_data, $order_data );
		}

		return $new_order_data;
	}


	/**
	 * Adds support for Customer / Order XML Export by adding a dedicated <CustomFields> tag
	 *
	 * @since 1.7.0
	 * @param array $order_data order data for the XML output
	 * @param \WC_Order $order order object
	 * @return array - updated order data
	 */
	public function add_fields_to_xml_export_data( $order_data, $order ) {

		$order_data['CustomFields'] = $this->get_fields_xml_required_format( $order );
		return $order_data;
	}


	/**
	 * Creates array of fields in format required for xml_to_array()
	 *
	 * Filter in method allows modification of individual fields array format
	 *
	 * @since 1.7.0
	 * @param \WC_Order $order order object
	 * @return array|null - fields in array format required by array_to_xml() or null if no fields
	 */
	protected function get_fields_xml_required_format( $order ) {

		$fields       = array();
		$order_fields = wc_admin_custom_order_fields()->get_order_fields( SV_WC_Order_Compatibility::get_prop( $order, 'id' ) );

		foreach( $order_fields as $id => $field ) {

			$field_data = array();

			$field_data['ID']    = $id;
			$field_data['Name']  = $field->label;
			$field_data['Value'] = $field->get_value_formatted();

			$fields['CustomField'][] = apply_filters( 'wc_admin_custom_order_fields_xml_field_data', $field_data, $order, $field );
		}

		return ! empty( $fields ) ? $fields : null;
	}


} // end \WC_Admin_Custom_Order_Fields_Export_Handler class
