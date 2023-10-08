<?php
/**
 * Plugin Name: WC Serial Numbers Extended
 * Plugin URI:  https://www.pluginever.com/plugins/wocommerce-serial-numbers-pro/
 * Description: Sell and manage license keys/ serial numbers/ secret keys easily within your WooCommerce store.
 * Version:     1.0.0
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
            add_filter( 'wc_serial_numbers_display_key_props_html', array( $this, 'control_key_props_html' ) );
            add_action( 'wp_footer', array( __CLASS__, 'footer_inline_code' ) );
        }

        /**
         * Control key props html.
         *
         * @param string $html The key props html.
         *
         * @since 1.0.0
         * @return string $html
         */
        public function control_key_props_html( $html ) {
            $copy_btn_html = '<span id="copy_license_key">' . __( 'Copy Key', 'wc-serial-numbers-extended' ) . '</span>';
            $html = substr_replace( $html, $copy_btn_html, strripos($html, '</code>')+7, 0 );
            $html = str_replace("<code>","<code id='wcsn_license_key'>", $html );
            return $html;
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
                #copy_license_key {
                    background: #38A9FF;
                    color: #fff;
                    border-radius: 2px;
                    margin-left: 10px;
                    padding: 4px 10px;
                    font-size: 14px;
                }
                #copy_license_key:hover{
                    cursor: pointer;
                    opacity: 0.9;
                }
            </style>
            <script>
                (function ($) {
                    // Copy License key, on click event
                    var copyLicenseKey = function () {
                        $( '#copy_license_key' ).on( 'click', async function() {
                            let wcsn_license_key = $( '#wcsn_license_key' ).html();
                            try {
                                await navigator.clipboard.writeText( wcsn_license_key );
                                $( '#copy_license_key' ).html( 'Copied' );
                            } catch (err) {
                                $( '#copy_license_key' ).html( 'Failed to copy the key' );
                            }
                            setTimeout( function () {
                                $( '#copy_license_key' ).html( 'Copy Key' );
                            }, 600)
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