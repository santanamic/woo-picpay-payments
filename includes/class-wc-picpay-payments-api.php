<?php

namespace PicPayGateway;

/**
 *
 * WC_API Class
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
 * WC_API Class
 *
 * @category Class
 * @version  1.0.0
 * @package  woocommerce-picpay-payments
 *
*/

class WC_API 
{

    /**
    *
    * The gateway
    *
    * @var WC_Gateway 
    *
    */ 	
	
	private $gateway;
	
    /**
    *
    * The data order
    *
    * @var \WC_Order 
    *
    */ 

    protected $order;

    /**
     *
     * Relation between API and Order data
     * 
     * @access public
     * @param  WC_Gateway $gateway
     *
     */ 
	
    public function __construct( WC_Gateway $gateway ) {

        /**
         *
         * Set order in data
         *
         */ 
        
        $this->gateway = $gateway;
    }   
	
	/**
     *
     * Current order
     * 
     * @access public
     * @param \WC_Order
     * @return void
     *
     */ 
	
    public function set_order( \WC_Order $order ) {

        /**
         *
         * All data order
         *
         */ 
		 
		 $this->order = $order;
    }
	
    /**
     *
     * For acess token and more gateway options
     * 
     * @access public
     * @return $settings
     *
     */ 
	
    public function get_gateway_settings( $option ) {

        /**
         *
         * Return all admin options if option param not exist
         *
         */          

		 return $option ? $this->gateway->settings[$option] : $this->gateway->settings;
    }
	
	/**
     *
     * Body request
     * 
     * @access public
     * @return array
     *
     */ 
	
    public function get_order_options() {

        /**
         *
         * Options for API request
         *
         */ 
		 
		 return [
			'reference_id' => $this->order->get_order_number(),
			'callback_url' => \WC()->api_request_url( $this->gateway->id ),
			'return_url' => $this->order->get_checkout_order_received_url(),
			'value' => $this->order->get_total(),
			'buyer' => [
				'firstName' => $this->order->get_billing_first_name(),
				'lastName'  => $this->order->get_billing_last_name(),
				'document' => ( $this->order->get_meta('_billing_persontype') == '2' ) ? $this->order->get_meta('_billing_cnpj') : $this->order->get_meta('_billing_cpf'),
				'email' => $this->order->get_billing_email(),
				'phone' => $this->order->get_meta('_billing_cellphone') ? '' : $this->order->get_billing_phone() 
			]
		];
    }
	
    /**
     *
     * Credential Instance
     * 
     * @access public
     * @return \PicPay\Configuration
     *
     */ 
	
    public function api_credential() {
		
        /**
         *
		 * Get all admin options
         * Set token option in credential instance
         *
         */ 
		 
		 return \PicPay\Configuration::getDefaultConfiguration()
			->setApiKey( 'x-picpay-token', $this->get_gateway_settings('gateway_token') );
    }
	
    /**
     *
     * Credential Instance
     * 
     * @access public
     * @return \PicPay\Configuration
     *
     */ 
	
    public function api_client( $guzzleHttp=null ) {
		
        /**
         *
         * Set HttpClient
         *
         */ 

        if( $guzzleHttp ) {

            /**
             *
             * Using custom client
             *
             */ 

            return $guzzleHttp;
        }
        else {

            /**
             *
             * Using default client
             *
             */
            
             return new \GuzzleHttp\Client([
							'verify'  => false,
							'headers' => [
								'Content-Type'    => 'application/javascript; charset=UTF-8',
								'Cache-Control'   => 'no-cache',
								'Accept-Encoding' => 'none'
							]
						]);
        }
    }
	
    /**
     *
     * Get payment URL
     * 
     * @access public
     * @return Array
     *
     */ 
	
    public function payment_request() {
		
        /**
         *
         * Set api Instance for payment Request
         *
         */ 

		$apiInstance = new \PicPay\SDK\RequisioDePagamentoApi( 
								$this->api_client(), 
								$this->api_credential());

        /**
         *
         * Set Request body orders params
         *
         */ 
		 
		 $body = new \PicPay\modelPackage\PaymentRequest( 
								$this->get_order_options());
				
        /**
         *
         * Init the Request
         *
         */ 
		 
		try {

			/**
			 *
			 * Run communication with the API
			 *
			 */
			 
			$result = $apiInstance->postPayments($body);
			
			/**
			 *
			 * On success the response returns
			 *
			 */ 
		 
			return  [ 'status' => 'sucess', 'body' => $result ];
		} 
	
		/**
		 *
		 * Error handling
		 *
		 */ 
			 
		catch ( \Exception $e ) {

			/**
			 *
			 * Get and return error mesage for log file
			 *
			 */ 
		 
			return  [ 'status' => 'fail', 'body' => 'Exception when calling RequisioDePagamentoApi->postPayments: ' . $e->getMessage() ];
		}
	}
	
    /**
     *
     * Credential Instance
     * 
     * @access public
     * @return \PicPay\Configuration
     *
     */ 
	
    public function payment_status() {
		
       /**
         *
         * Set api Instance for payment status
         *
         */ 

		$apiInstance = new \PicPay\SDK\StatusApi( 
								$this->api_client(), 
								$this->api_credential());
								
       /**
         *
         * Get order ID
         *
         */ 
		 
		$order_id = $this->order->get_order_number();
	
        /**
         *
         * Init the Request
         *
         */ 
		 
		try {

			/**
			 *
			 * Run communication with the API
			 *
			 */
			 
			$result = $apiInstance->getStatus( $order_id );
			
			/**
			 *
			 * On success the response returns
			 *
			 */ 
		 
			return  [ 'status' => 'sucess', 'body' => $result ];
		}
		
		/**
		 *
		 * Error handling
		 *
		 */ 
			 
		catch ( \Exception $e ) {

			/**
			 *
			 * Get and return error mesage for log file
			 *
			 */ 

			return  [ 'status' => 'fail', 'body' => 'Exception when calling StatusApi->getStatus: ' . $e->getMessage() ];
		}
	}
	
    /**
     *
     * Cancel Request
     * 
     * @access public
     * @return void
     *
     */ 
	
    public function payment_cancel( $order_id ) {
		
       /**
         *
         * Set api Instance for payment cancel
         *
         */ 

		$apiInstance = new \PicPay\SDK\CancelamentoApi( 
								$this->api_client(), 
								$this->api_credential());

	   /**
         *
         * Body request
         *
         */
		 
		$body = new \PicPay\modelPackage\CancelRequest();

	
        /**
         *
         * Init the Request
         *
         */ 
		 
		try {

			/**
			 *
			 * Run communication with the API
			 *
			 */
			 
			$result = $apiInstance->postCancellations( $body, $order_id );
			
			/**
			 *
			 * On success the response returns
			 *
			 */ 
		 
			return  [ 'status' => 'sucess', 'body' => $result ];
		}
		
		/**
		 *
		 * Error handling
		 *
		 */ 
			 
		catch ( \Exception $e ) {

			/**
			 *
			 * Get and return error mesage for log file
			 *
			 */ 

			return  [ 'status' => 'fail', 'body' => 'Exception when calling StatusApi->getStatus: ' . $e->getMessage() ];
		}
	}
}

?>