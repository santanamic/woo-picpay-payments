<?php

namespace PicPayGateway;

/**
 *
 * WC_Notices Class
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
 * WC_Notices Class
 *
 * @category Class
 * @version  1.0.0
 * @package  woo-picpay-payments
 *
 */

class WC_Notices
{

    /**
     *
     * Alert admin error. The plugin can not function
     *
     * @access public
     * @return boolean
     *
     */

    public static function __callStatic($method, $arguments)
    {

        /**
         *
         * Woocommerce and ExtraCheckoutFieldsForBrazil required
         *
         */

        add_action('admin_notices', array('\PicPayGateway\WC_Notices_Builder', $method));

        /**
         *
         * Close method
         *
         */

        return false;
    }
}
