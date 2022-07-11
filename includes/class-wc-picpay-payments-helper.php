<?php

namespace PicPayGateway;

/**
 *
 * WC_Helper Class
 *
 * This file is part of <santanamic/woo-picpay-payments>
 * Created by WILLIAN SANTANA <https://github.com/santanamic>
 *
 * For the information of copyright and license you should read the file
 * LICENSE which is distributed with this source code.
 *
 * Para a informaçao dos direitos autorais e de licença voce deve ler o arquivo
 * LICENSE que é distribuído com este código-fonte.
 *
 * Para obtener la información de los derechos de autor y la licencia debe leer
 * el archivo LICENSE que se distribuye con el código fuente.
 *
 * @package woo-picpay-payments
 * @author @santanamic
 *
 */

defined('ABSPATH') || exit; // Exit if accessed directly

/**
 *
 * WC_Helper Class
 *
 * @category Class
 * @version  1.0.0
 * @package  woo-picpay-payments
 *
 */

class WC_Helper
{

    /**
     *
     * Alert admin message. The plugin can not function
     * 
     * @access public
     * @param  array WP default plugin links
     * @return array WP updated plugin links
     *
     */

    public static function admin_plugin_links($links)
    {

        /**
         *
         * Add link shortcut config to page plugins
         *
         */

        $links[] = '<a href="' . esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=woo-picpay-payments')) . '">' . __('Configurações', 'woo-picpay-payments') . '</a>';

        /**
         *
         * Add link shortcut support to page plugins
         *
         */

        $links[] = '<a href="http://bit.ly/picpay-support-gateway">' . __('Suporte', 'woo-picpay-payments') . '</a>';

        /**
         *
         * Add link shortcut docs to page plugins
         *
         */

        $links[] = '<a href="http://bit.ly/picpay-gateway-docs">' . __('Documentação', 'woo-picpay-payments') . '</a>';

        /**
         *
         * WordPress links array updated.
         *
         */

        return $links;
    }

    /**
     *
     * Set admin scripts
     * 
     * @access public
     * @return void
     *
     */

    public static function admin_plugin_scripts()
    {

        /**
         *
         * Set CSS core
         *
         */

        wp_enqueue_style('woo-picpay-payments-admin-style', WOOCOMMERCE_PICPAY_PAYMENTS_DIR_URL . 'admin/assets/css/style.css');

        /**
         *
         * Set CSS fancybox
         *
         */

        wp_enqueue_style('woo-picpay-payments-admin-style-fancybox',  WOOCOMMERCE_PICPAY_PAYMENTS_DIR_URL . 'admin/assets/css/jquery.fancybox.min.css');

        /**
         *
         * Set javascript fancybox
         *
         */

        wp_enqueue_script('woo-picpay-payments-admin-script-fancybox', WOOCOMMERCE_PICPAY_PAYMENTS_DIR_URL . 'admin/assets/js/jquery.fancybox.min.js');


        /**
         *
         * Set javascript core
         *
         */

        wp_enqueue_script('woo-picpay-payments-admin-script', WOOCOMMERCE_PICPAY_PAYMENTS_DIR_URL . 'admin/assets/js/script.js');
    }

    /**
     *
     * Get plugin options
     * 
     * @access public
     * @param  string An option of the plugin configuration form
     * @return mixed
     *
     */

    public static function plugin_settings($option)
    {

        /**
         *
         * Get WordPress array data
         *
         */

        $data = get_option('woocommerce_woo-picpay-payments_settings');

        /**
         *
         * Valid array data
         *
         */

        if (is_array($data) && $data != null) {

            /**
             *
             * Check if option exist in data
             *
             */

            if (array_key_exists($option, $data)) {

                /**
                 *
                 * Return value option
                 *
                 */

                return $data[$option];
            } else {

                /**
                 *
                 * Here it is important that the return is false
                 *
                 */

                return false;
            }
        } else {

            /**
             *
             * Return false for invalid data
             *
             */

            return false;
        }
    }
}
