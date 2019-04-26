<?php

namespace PicPayGateway;

/**
 *
 * WC_Gateway_Register Class
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
 
/**
 *
 * WC_Gateway_Register Class
 *
 * @category Class
 * @version  1.0.0
 * @package  woocommerce-picpay-payments
 *
*/

class WC_Gateway_Register 
{ 

	/**
	 *
	 * Gateway Registration on woocommerce
	 * 
	 * @access private
	 * @param  array Woocommerce all registered gateway
	 * @return array all gateway list
	 *
	 */ 
	
    public static function add_gateway($data) {
		
          /**
           *
           * Set plugin gateway class
           *
           */ 
          
          $data[] = '\PicPayGateway\WC_Gateway';

          /**
           *
           * all gateway
           *
           */ 
          
          return $data;
     }
}

?>