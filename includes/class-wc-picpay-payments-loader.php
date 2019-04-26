<?php

namespace PicPayGateway;

/**
 *
 * WC_Loader Class
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
 * WC_Loader Class
 *
 * @category Class
 * @version  1.0.0
 * @package  woocommerce-picpay-payments
 *
*/

class WC_Loader extends WC_Environment 
{

	/**
	 *
	 * Starts the main settings
	 *
	 * @access public
	 * @return void
	 *
	 */    

    public static function init() {

		/**
		 *
		 * If there are no errors
		 *
		 */ 

		if( WC_Validation::is_valid_environment() ) {

			/**
			 *
			 * Init gateway environment
			 *
			 */ 

			WC_Loader::init_gateway_environment();

			/**
			 *
			 * Valid gateway settings
			 *
			 */ 

			WC_Validation::is_valid_gateway();
		}
	}
}

?>