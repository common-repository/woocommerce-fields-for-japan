<?php
/**
 * Plugin Name: WooCommerce Fields for Japan
 * Plugin URI: http://little.ws
 * Description: WooCommerceを日本向けに住所の並びなどを変更します。Woocommerce Ver2以降にも対応しています。
 * Author: Little.ws
 * Author URI: http://little.ws
 * Version: 1
 * License: GPLv2 or later
 */

/**
 * WC_JapaneseCheckoutFields class.
 */
class WC_JapaneseCheckoutFields {

    /**
     * Construct.
     */
    public function __construct() {

        // New checkout fields.
        add_filter( 'woocommerce_billing_fields', array( &$this, 'checkout_billing_fields' ) );
        add_filter( 'woocommerce_shipping_fields', array( &$this, 'checkout_shipping_fields' ) );

        // Custom shop_order details.
        add_filter( 'woocommerce_admin_billing_fields', array( &$this, 'admin_billing_fields' ) );
        add_filter( 'woocommerce_admin_shipping_fields', array( &$this, 'admin_shipping_fields' ) );
        add_action( 'woocommerce_admin_order_data_after_billing_address', array( &$this, 'custom_admin_billing_fields' ) );
        add_action( 'woocommerce_admin_order_data_after_shipping_address', array( &$this, 'custom_admin_shipping_fields' ) );
        add_action( 'admin_enqueue_scripts', array( &$this, 'admin_enqueue_scripts' ) );
        add_action( 'save_post', array( &$this, 'save_custom_fields' ) );

        // Custom address format.
        add_filter( 'woocommerce_localisation_address_formats', array( &$this, 'localisation_address_formats' ) );

        // Custom user edit fields.
        if ( version_compare( WOOCOMMERCE_VERSION, '2.0.6', '>=' ) ) {
            add_filter( 'woocommerce_customer_meta_fields', array( &$this, 'user_edit_fields' ) );
        }

    }

    /**
     * Admin Enqueue scripts.
     *
     * @return void
     */
    public function admin_enqueue_scripts() {
        global $post_type;

        if ( 'shop_order' == $post_type ) {

            wp_register_style( 'wcfjp-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), null );
            wp_enqueue_style( 'wcfjp-admin-styles' );

            wp_register_script( 'wcfjp-write-panels', plugins_url( 'js/jquery.write-panels.js', __FILE__ ), array( 'jquery' ), null, true );
            wp_enqueue_script( 'wcfjp-write-panels' );
        }
    }

    /**
     * New checkout billing fields
     *
     * @param  array $fields Default fields.
     *
     * @return array         New fields.
     */
    public function checkout_billing_fields( $fields ) {
        $new_fields = array();

        // Billing First Name.
        $new_fields['billing_first_name'] = array(
            'label'       => '姓',
            'class'       => array( 'form-row-first' ),
            'required'    => true
        );

        // Billing Last Name.
        $new_fields['billing_last_name'] = array(
            'label'       => '名',
            'class'       => array( 'form-row-last' ),
            'clear'       => true,
            'required'    => true
        );

        // Billing Post Code.
        $new_fields['billing_postcode'] = array(
            'label'       => '郵便番号',
            'placeholder' => '000-0000',
            'class'       => array( 'form-row-first', 'update_totals_on_change', 'address-field' ),
            'clear'       => true,
            'required'    => true
        );
        
        // Billing State.
        $new_fields['billing_state'] = array(
            'type'        => 'state',
            'label'       => '都道府県',
            'class'       => array( 'form-row-first', 'address-field' ),
            'required'    => true
        );
        
        // Billing City.
        $new_fields['billing_city'] = array(
            'label'       => '市町村',
            'class'       => array( 'form-row-last', 'address-field' ),
            'clear'       => true,
            'required'    => true
        );
        
        // Billing Anddress 01.
        $new_fields['billing_address_1'] = array(
            'label'       => '番地',
            'class'       => array( 'form-row-first', 'address-field' ),
            'required'    => true
        );

        // Billing Anddress 02.
        $new_fields['billing_address_2'] = array(
            'label'       => '建物名',
            'class'       => array( 'form-row-last', 'address-field' )
        );

        // Billing Phone.
        $new_fields['billing_phone'] = array(
            'label'       => '電話番号',
            'placeholder' => '000-1234-5678',
            'class'       => array( 'form-row-first' ),
            'required'    => true
        );

        // Billing Email.
        $new_fields['billing_email'] = array(
            'label'       => 'メールアドレス',
            'class'       => array( 'form-row-wide' ),
            'clear'       => true,
            'required'    => true
        );

        return apply_filters( 'wcfjp_billing_fields', $new_fields );
    }

    /**
     * New checkout shipping fields
     *
     * @param  array $fields Default fields.
     *
     * @return array         New fields.
     */
    public function checkout_shipping_fields( $fields ) {
        $new_fields = array();

        // Shipping First Name.
        $new_fields['shipping_first_name'] = array(
            'label'       => '姓',
            'class'       => array( 'form-row-first' ),
            'required'    => true
        );

        // Shipping Last Name.
        $new_fields['shipping_last_name'] = array(
            'label'       => '名',
            'class'       => array( 'form-row-last' ),
            'clear'       => true,
            'required'    => true
        );

        // Shipping Company.
        $new_fields['shipping_company'] = array(
            'label'       => '会社名',
            'class'       => array( 'form-row-wide' )
        );

        // Shipping Post Code.
        $new_fields['shipping_postcode'] = array(
            'label'       => '郵便番号',
            'placeholder' => '000-0000',
            'class'       => array( 'form-row-first', 'update_totals_on_change', 'address-field' ),
            'clear'       => true,
            'required'    => true
        );
        
        // Shipping State.
        $new_fields['shipping_state'] = array(
            'type'        => 'state',
            'label'       => '都道府県',
            'class'       => array( 'form-row-first', 'address-field' ),
            'required'    => true
        );
        
        // Shipping City.
        $new_fields['shipping_city'] = array(
            'label'       => '市町村',
            'class'       => array( 'form-row-last', 'address-field' ),
            'clear'       => true,
            'required'    => true
        );
        
        // Shipping Anddress 01.
        $new_fields['shipping_address_1'] = array(
            'label'       => '番地',
            'class'       => array( 'form-row-first', 'address-field' ),
            'required'    => true
        );

        // Shipping Anddress 02.
        $new_fields['shipping_address_2'] = array(
            'label'       => '建物名',
            'class'       => array( 'form-row-last', 'address-field' ),
            'clear'       => true
        );

        return apply_filters( 'wcfjp_shipping_fields', $new_fields );
    }

    /**
     * Custom billing admin edit fields.
     *
     * @param  array $data Default WC_Order data.
     *
     * @return array       Custom WC_Order data.
     */
    public function admin_billing_fields( $data ) {
        global $woocommerce;

        $billing_data['first_name'] = array(
            'label' => '姓',
            'show'  => false
        );
        $billing_data['last_name'] = array(
            'label' => '名',
            'show'  => false
        );
        $billing_data['postcode'] = array(
            'label' => '郵便番号',
            'show'  => false
        );
        $billing_data['state'] = array(
            'label' => '都道府県',
            'show'  => false
        );
        $billing_data['city'] = array(
            'label' => '市町村',
            'show'  => false
        );
        $billing_data['address_1'] = array(
            'label' => '番地',
            'show'  => false
        );
        $billing_data['address_2'] = array(
            'label' => '建物名',
            'show'  => false
        );

        $billing_data['phone'] = array(
            'label' => '電話番号',
        );

        $billing_data['email'] = array(
            'label' => 'メールアドレス',
        );

        return apply_filters( 'wcfjp_admin_billing_fields', $billing_data );
    }

    /**
     * Custom shipping admin edit fields.
     *
     * @param  array $data Default WC_Order data.
     *
     * @return array       Custom WC_Order data.
     */
    public function admin_shipping_fields( $data ) {
        global $woocommerce;

        $shipping_data['first_name'] = array(
            'label' => __( 'First Name', 'wcfjp' ),
            'show'  => false
        );
        $shipping_data['last_name'] = array(
            'label' => __( 'Last Name', 'wcfjp' ),
            'show'  => false
        );
        $shipping_data['company'] = array(
            'label' => '会社名',
            'show'  => false
        );
        $shipping_data['postcode'] = array(
            'label' => '郵便番号',
            'show'  => false
        );
        $shipping_data['state'] = array(
            'label' => '都道府県',
            'show'  => false
        );
        $shipping_data['city'] = array(
            'label' => '市町村',
            'show'  => false
        );
        $shipping_data['address_1'] = array(
            'label' => '番地',
            'show'  => false
        );
        $shipping_data['address_2'] = array(
            'label' => '建物名',
            'show'  => false
        );

        return apply_filters( 'wcfjp_admin_shipping_fields', $shipping_data );
    }

    /**
     * Custom billing admin fields.
     *
     * @param  object $order Order data.
     *
     * @return string        Custom information.
     */
    public function custom_admin_billing_fields( $order ) {
        global $woocommerce;

        // Use nonce for verification.
        wp_nonce_field( basename( __FILE__ ), 'wcfjp_meta_fields' );

        $html = '<div class="wcfjp-address">';

        if ( ! $order->get_formatted_billing_address() ) {
            $html .= '<p class="none_set"><strong>住所:</strong> 請求先住所がありません。</p>';
        } else {

            $html .= '<p><strong>住所:</strong><br />';
            if ( version_compare( WOOCOMMERCE_VERSION, '2.0.5', '<=' ) ) {
                $html .= $order->billing_first_name . ' ' . $order->billing_last_name . '<br />';
                $html .= $order->billing_postcode . '<br />';
                $html .= $order->billing_state . '<br />';
                $html .= $order->billing_city . ' '. $order->billing_address_1 . '<br />';
                $html .= $order->billing_address_2 . '<br />';
            } else {
                $html .= $order->billing_postcode . '<br />';
                $html .= $order->get_formatted_billing_address();
            }

            $html .= '</p>';
        }

        $html .= '<h4>顧客データ</h4>';

        $html .= '<p>';

        $html .= '<strong>電話番号: </strong>' . $order->billing_phone . '<br />';

        $html .= '<strong>メールアドレス: </strong>' . $order->billing_email . '<br />';

        $html .= '</p>';

        $html .= '</div>';

        echo $html;
    }

    /**
     * Custom billing admin fields.
     *
     * @param  object $order Order data.
     *
     * @return string        Custom information.
     */
    public function custom_admin_shipping_fields( $order ) {
        global $woocommerce;

        $html = '<div class="wcfjp-address">';

        if ( ! $order->get_formatted_shipping_address() ) {
            $html .= '<p class="none_set"><strong>住所:</strong> 配送先住所は入力されていません。</p>';
        } else {

            $html .= '<p><strong>住所:</strong><br />';
            if ( version_compare( WOOCOMMERCE_VERSION, '2.0.5', '<=' ) ) {
                $html .= $order->billing_first_name . ' ' . $order->billing_last_name . '<br />';
                $html .= $order->billing_postcode . '<br />';
                $html .= $order->billing_state . '<br />';
                $html .= $order->billing_city . ' ' . $order->billing_address_1 . '<br />';
                $html .= $order->billing_address_2 . '<br />';
            } else {
                $html .= $order->billing_postcode . '<br />';
                $html .= $order->get_formatted_shipping_address();
            }

            $html .= '</p>';
        }

        $html .= '</div>';

        echo $html;
    }

    /**
     * Save custom fields.
     *
     * @param  int  $post_id Post ID.
     *
     * @return mixed
     */
    public function save_custom_fields( $post_id ) {
        global $post_type;

        if ( 'shop_order' == $post_type ) {

            // Verify nonce.
            if ( ! isset( $_POST['wcfjp_meta_fields'] ) || ! wp_verify_nonce( $_POST['wcfjp_meta_fields'], basename( __FILE__ ) ) ) {
                return $post_id;
            }

            // Verify if this is an auto save routine.
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return $post_id;
            }

            // Verify current user.
            if ( ! current_user_can( 'edit_pages', $post_id ) ) {
                return $post_id;
            }

        }

        return $post_id;
    }

    /**
     * Custom user edit fields.
     *
     * @param  array $fields Default fields.
     *
     * @return array         Custom fields.
     */
    public function user_edit_fields( $fields ) {
        unset( $fields );

        // Billing fields.
        $fields['billing']['title'] = 'お客様請求先情報';
        $fields['billing']['fields']['billing_first_name'] = array(
            'label' => __( 'First name', 'wcfjp' ),
            'description' => ''
        );
        $fields['billing']['fields']['billing_last_name'] = array(
            'label' => __( 'Last name', 'wcfjp' ),
            'description' => ''
        );
        $fields['billing']['fields']['billing_postcode'] = array(
            'label' => '郵便番号',
            'description' => ''
        );
        $fields['billing']['fields']['billing_state'] = array(
            'label' => '都道府県',
            'description' => __( 'State code', 'wcfjp' )
        );
        $fields['billing']['fields']['billing_city'] = array(
            'label' => '市町村',
            'description' => ''
        );
        $fields['billing']['fields']['billing_address_1'] = array(
            'label' => '番地',
            'description' => ''
        );
        $fields['billing']['fields']['billing_address_2'] = array(
            'label' => '建物名',
            'description' => ''
        );
        $fields['billing']['fields']['billing_phone'] = array(
            'label' => '電話番号',
            'description' => ''
        );
        $fields['billing']['fields']['billing_email'] = array(
            'label' => 'メールアドレス',
            'description' => ''
        );

        // Shipping fields.
        $fields['shipping']['title'] = 'お客様配送先住所';
        $fields['shipping']['fields']['shipping_first_name'] = array(
            'label' => __( 'First name', 'wcfjp' ),
            'description' => ''
        );
        $fields['shipping']['fields']['shipping_last_name'] = array(
            'label' => __( 'Last name', 'wcfjp' ),
            'description' => ''
        );
        $fields['shipping']['fields']['shipping_company'] = array(
            'label' => '会社名',
            'description' => ''
        );
        $fields['shipping']['fields']['shipping_postcode'] = array(
            'label' => '郵便番号',
            'description' => ''
        );
        $fields['shipping']['fields']['shipping_state'] = array(
            'label' => '都道府県',
            'description' => ''
        );
        $fields['shipping']['fields']['shipping_city'] = array(
            'label' => '市町村',
            'description' => ''
        );
        $fields['shipping']['fields']['shipping_address_1'] = array(
            'label' => '番地',
            'description' => ''
        );
        $fields['shipping']['fields']['shipping_address_2'] = array(
            'label' => '建物名',
            'description' => ''
        );
        $new_fields = apply_filters( 'wcfjp_customer_meta_fields', $fields );

        return $new_fields;
    }

    /**
     * Custom country address formats.
     *
     * @param  array $formats Defaul formats.
     *
     * @return array          New ja format.
     */
    function localisation_address_formats( $formats ) {

        $formats['ja'] = "{name}\n{country}\n{postcode}\n{state}\n{city} {address_1}\n{address_2}";

        return $formats;
    }
}

/**
 * Load plugin functions.
 */
add_action( 'plugins_loaded', 'wcfjp_plugin', 0 );

function wcfjp_plugin() {
    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        $wcJapaneseCheckoutFields = new WC_JapaneseCheckoutFields();
    } else {
        add_action( 'admin_notices', 'wcfjp_fallback_notice' );
    }
}
