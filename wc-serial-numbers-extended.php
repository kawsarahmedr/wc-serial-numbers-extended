<?php
/**
 * Plugin Name: WC Serial Numbers Extended
 * Plugin URI:  https://www.pluginever.com/plugins/wocommerce-serial-numbers-pro/
 * Description: The plugin "WC Serial Numbers Extended" will add a feature to copy the serial key or license key to the clipboard.
 * Version:     1.0.1
 * Author:      PluginEver
 * Author URI:  http://pluginever.com
 * License:     GPLv2+
 * Text Domain: wc-serial-numbers-extended
 * Domain Path: /languages
 * Tested up to: 6.3
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 *
 * @package WooCommerceSerialNumbersExtended
 */

// Don't call the file directly.
defined( 'ABSPATH' ) || exit();


if ( ! class_exists('WCSNEX' ) ) {

    /**
     * Class WCSNEX
     *
     * This class will extend the WC Serial Number plugin feature
     *
     * @since 1.0.0
     * @package WooCommerceSerialNumbersExtended
     */
    class WCSNEX {
        /**
         * WCSNEX constructor.
         *
         * @since 1.0.0
         */
        public function __construct() {
            add_action('init', array( $this, 'init' ) );
            add_filter( 'wc_serial_numbers_display_key_props_html', array( __CLASS__, 'control_key_props_html' ) );
            add_action( 'wp_footer', array( __CLASS__, 'footer_inline_code' ) );
        }

	    /**
	     * Init
	     *
	     * @since 1.0.0
	     * @return void
	     */
        public function init() {
	        remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );
	        add_action( 'woocommerce_order_details_after_order_table', array( $this, 'order_again_button' ) );
        }

	    /**
	     * Order again button on order details page
	     *
	     * @param object $order The order object
	     *
	     * @since 1.0.0
	     * @return void
	     */
        public function order_again_button( $order ) {
            ob_start();
	        ?>
            <p class="order-again wcsn-order-again">
                <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'order_again', $order->get_id(), wc_get_cart_url() ), 'woocommerce-order_again' ) ); ?>" class="button">
                    <?php esc_html_e( 'Order again', 'wc-serial-numbers-extended' ); ?>
                </a>
                <a id="copy_license_key" class="button">
                    <?php _e( 'Copy to Clipboard', 'wc-serial-numbers-extended' ); ?>
                </a>
            </p>
            <?php
            echo ob_get_clean();
        }

        /**
         * Control key props html.
         *
         * @param string $html The key props html.
         *
         * @since 1.0.0
         * @return string $html
         */
        public static function control_key_props_html( $html ) {
            return str_replace("<code>","<code class='wcsn_license_key'>", $html );
        }

        /**
         * Footer inline scripts
         *
         * @since 1.0.0
         * @return void
         */
        public static function footer_inline_code() {
            ob_start();
            ?>
            <style>
                .order-again {
                    display: flex;
                    justify-content: space-between;
                }
                #copy_license_key {
                    /*background: #38A9FF;*/
                    /*color: #fff;*/
                    margin-left: 10px;
                }
                #copy_license_key:hover{
                    cursor: pointer;
                    opacity: 0.9;
                }
                #copy_license_key::after{
                    display: none;
                }
            </style>
            <script>
                (function ($) {
                    // Copy License key, on click event
                    var copyLicenseKey = function () {

                        $( "#copy_license_key" ).on( "click", async function() {

                            let wcsn_license_key = '';
                            $( ".wcsn_license_key" ).each(function() {
                                wcsn_license_key += $( this ).html() + ' ';
                            });

                            if (window.isSecureContext && navigator.clipboard) {
                                try {
                                    await navigator.clipboard.writeText( wcsn_license_key );
                                    $( '#copy_license_key' ).html( 'Copied' );
                                } catch (err) {
                                    $( '#copy_license_key' ).html( 'Failed to copy' );
                                }
                            } else {
                                $( '#copy_license_key' ).html( 'Failed! Server isn\'t secured' );
                            }
                            setTimeout( function () {
                                $( '#copy_license_key' ).html( 'Copy to Clipboard' );
                            }, 600 )
                        });
                    }
                    // Dom Ready
                    $(function () {
                        copyLicenseKey();
                    });
                })(jQuery);
            </script>
            <?php
            echo ob_get_clean();
        }
    }
}

// Initialize the plugin.
new WCSNEX();