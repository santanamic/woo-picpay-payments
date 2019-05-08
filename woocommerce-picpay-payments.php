<?php

/**
 *
 * Plugin Name: Woocommerce PicPay Pagamentos
 * Plugin URI: https://github.com/santanamic/woocommerce-picpay-payments
 * Description: Receba Pagamentos com PicPay no WooCommerce
 * Author: WILLIAN SANTANA
 * Author URI: https://github.com/santanamic
 * Version: 1.2.0
 * License: GPLv2
 * Tested up to: 5.1.1
 * WC requires at least: 3.0
 * WC tested up to: 3.5
 * Domain Path: /languages
 *
 * This file is part of <santanamic/woocommerce-picpay-payments>
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
 * @package woocommerce-picpay-payments
 * @author @santanamic
 *
 */
 
defined( 'ABSPATH' ) || exit; // Exit if accessed directly

define('WOOCOMMERCE_PICPAY_PAYMENTS_VERSION', '1.2.0');
define('WOOCOMMERCE_PICPAY_PAYMENTS_DIR_PATH', plugin_dir_path(__FILE__));
define('WOOCOMMERCE_PICPAY_PAYMENTS_DIR_URL', plugin_dir_url(__FILE__));
define('WOOCOMMERCE_PICPAY_PAYMENTS_BASENAME', plugin_basename(__FILE__));
define('WOOCOMMERCE_PICPAY_PAYMENTS_SLUG', 'woocommerce-picpay-payments');

require_once( WOOCOMMERCE_PICPAY_PAYMENTS_DIR_PATH . 'vendor/autoload.php' );

add_action( 'plugins_loaded', array('\PicPayGateway\WC_Loader', 'init' ) );

add_action( 'admin_enqueue_scripts', array('\PicPayGateway\WC_Helper', 'admin_plugin_scripts') );

?>