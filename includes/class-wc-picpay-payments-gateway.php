<?php

namespace PicPayGateway;

/**
 *
 * WC_Gateway Class
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
 * WC_Gateway Class
 *
 * @category Class
 * @version  1.0.0
 * @package  woocommerce-picpay-payments
 *
*/

class WC_Gateway extends \WC_Payment_Gateway
{ 
  
     /**
	 *
     * Initialize gateway
     *
     * @access public
     * @return void
     *
     */   
     
     public function __construct() {
     
          /**
          *
          * Basic gateway settings
          *
          */  

          $this->id = 'woocommerce-picpay-payments';
          $this->icon = WOOCOMMERCE_PICPAY_PAYMENTS_DIR_URL . 'public/assets/img/icon.svg';
          $this->has_fields = false;
          $this->method_title = __( 'PicPay Pagamentos', 'woocommerce-picpay-payments' );
          $this->method_description =  __( 'Para utilizar este meio de pagamento você precisa das credenciais de acesso fornceias pelo PicPay. <br> <a href="https://lojista.picpay.com/dashboard/ecommerce-token">Crie ou entre na sua conta PicPay para conseguirás!</a>', 'woocommerce-picpay-payments' );
          
          $this->init_form_fields();

          /**
          *
          * Get admin gateway optios
          *
          */ 
             
          $this->init_settings();

          $this->title = $this->get_option( 'title' );
          $this->description = $this->get_option( 'description' );
          $this->enabled = $this->get_option( 'enabled' );
          $this->picpay_token = $this->get_option( 'x-picpay-token' );
          $this->saller_token = $this->get_option( 'x-seller-token' );
		  $this->debug = $this->get_option('debug'); 
		  
          /**
          *
          * Set logs
          *
          */ 
		  
		  $this->log = new WC_Log( $this );
		  
          /**
          *
          * Run gateway API
          *
          */ 
		  
		  $this->api = new WC_API( $this );

          /**
          *
          * Run gateway hooks
          *
          */ 

          add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
          add_action( 'woocommerce_order_status_cancelled', array($this, 'cancel_payment') );
		  add_action( 'woocommerce_order_status_refunded', array($this, 'cancel_payment') );
		  add_action( 'woocommerce_api_' . $this->id, array( $this, 'webhook' ) );
      }

     /**
	 *
     * Set gateway forms
     *
     * @access public
     * @return array Admin plugun options
     *
     */  

     public function init_form_fields(){
          
		/**
		 *
		 * Specify the fields to view on the woocommerce configuration page
		 *
		 */ 
          
          $this->form_fields = array(
              'enabled' => array(
                  'title'       => __( 'Ativar/Desativar', 'woocommerce-picpay-payments' ),
                  'label'       => __( 'Marque para ativar essa forma de pagamento', 'woocommerce-picpay-payments' ),
                  'type'        => 'checkbox',
                  'description' => '',
                  'default'     => 'no'
              ),
              'title' => array(
                  'title'       => __( 'Title', 'woocommerce-picpay-payments' ),
                  'type'        => 'text',
                  'description' => __( 'Isso controla o título que o usuário vê durante o checkout.', 'woocommerce-picpay-payments' ),
                  'default'     => 'Simple Redirect',
                  'desc_tip'    => true,
              ),
              'description' => array(
                  'title'       => __( 'Descrição.', 'woocommerce-picpay-payments' ),
                  'type'        => 'textarea',
                  'description' => __( 'Isso controla a descrição que o usuário vê durante o checkout.', 'woocommerce-picpay-payments' ),
                  'desc_tip'    => true,
                  'default'     => __( 'Pague com seu smartphone através do PicPay.', 'woocommerce-picpay-payments' ), 
              ),
              'x-picpay-token' => array(
                  'title'       => 'x-picpay-token',
                  'type'        => 'text'
              ),
              'x-seller-token' => array(
                  'title'       => 'x-seller-token',
                  'type'        => 'text'
              ),
			  'debug'                => array(
				  'title'       => __('Habilitar Log', 'woocommerce-picpay-payments'),
				  'type'        => 'checkbox',
				  'label'       => __('Habilitar Log', 'woocommerce-picpay-payments'),
				  'default'     => 'no',
				  'description' => sprintf(__('Registra os eventos do Gateway, como notificações de pagamento, através do arquivo <code>%s</code>. Observação: isto pode gravar informações pessoais. Nós recomendamos usar isso para apenas para fins de depuração e que delete estes registros após finalizar.', 'woocommerce-picpay-payments'), \WC_Log_Handler_File::get_log_file_path( $this->id )),
			  ),
          );
      }

     /**
	 *
     * Processes the user data after sending the payment request in checkout
     *
     * @access public
     * @param  int Current order number
     * @return array|boolean
     *
     */ 

    public function process_payment( $order_id ) {
		
		/**
		*
		* Set log promise for init payment process
		*
		*/

		$this->log
			->add( sprintf(__('Log do processo de pagamento para o ID do pedido: %s', 'woocommerce-picpay-payments'), $order_id) );
  
		/**
		 *
		 * Get all order data
		 *
		 */ 
          
        $order = wc_get_order( $order_id );
		
		/**
		 *
		 * Get meta URL in order option
		 *
		 */ 

        $url = $order->get_meta('PicPayGateway_Payment_URL');

		/**
		 *
		 * Check if an order payment URL exists
		 *
		 */
		
		if ( filter_var($url, FILTER_VALIDATE_URL) ) {
			
			/**
			*
			* Set log promise for retrieved payment process
			*
			*/

			$this->log
				->add( sprintf(__('URL de pagamento recuperado: %s', 'woocommerce-picpay-payments'), $url) );
			
		   /**
			*
			* Return sucess and redirect for URL payment
			*
			*/ 
	  
			return [ 'result' => 'success', 'redirect' =>  $url ];
		}
		
		/**
		 *
		 * Creates an order payment URL
		 *
		 */
		 
		else {
			
			/**
			 *
			 * Set new order in API instance
			 *
			 */ 
			 
			$this->api->set_order( $order );
			 
			/**
			 *
			 * Process payment in API. 
			 *
			 */ 
			 
			$payment = $this->api->payment_request();
			 
			/**
			 *
			 * Check API response
			 *
			 */ 
			 
			if( $payment['status'] === 'sucess' ) {
		
				/**
				 *
				 * Get an external payment link
				 *
				 */ 
				 
				$url = $payment['body']['payment_url'];
				
			    /**
				 *
				 * Set log promise for sucess API response
				 *
				 */

				$this->log->add( var_export($payment, true) );

				/**
				 *
				 * Validate response and return confirmation
				 *
				 */ 

				if( filter_var($url, FILTER_VALIDATE_URL) ) {
					  
					   /**
						*
						* Add payment URL in order
						*
						*/ 
						
						$order->add_meta_data('PicPayGateway_Payment_URL', $url, true);
						
					  
					   /**
						*
						* Empty user cart
						*
						*/ 
						
						WC()->cart->empty_cart();
					  
					   /**
						*
						* Add note in order
						*
						*/ 
					  
						$order->add_order_note(__( 'O comprador iniciou a transação, mas até agora o PicPay não recebeu nenhuma informação de pagamento.', 'woocommerce-picpay-payments' ));
						
					   /**
						*
						* Save changes in order
						*
						*/ 
						
						$order->save();
						
					   /**
						*
						* Return sucess and redirect for URL payment
						*
						*/ 
					  
						return [ 'result' => 'success', 'redirect' =>  $url ];	
				}
				  
				/**
				 *
				 * API generic error
				 *
				 */ 
				  
				else {
					  
					   /**
						*
						* Message error in checkout page
						*
						*/ 
					   
						wc_add_notice(  __( 'Erro em obter a URL de pagamento do PicPay. Tente novamente!', 'woocommerce-picpay-payments' ), 'error' );
						
					   /**
						*
						* Set log promise for invalid URL payment
						*
						*/

						$this->log
							->add( sprintf(__('Erro de validação do URL do gateway: %s', 'woocommerce-picpay-payments'), $url) );

					   /**
						*
						* Close payment process
						*
						*/ 

						return ['result' => 'fail'];
				}
			 }
			 
			/**
			 *
			 * If the API response is an error, set the error and log
			 *
			 */ 
			 
			else {

			   /**
				*
				* Set frontend generic error
				*
				*/ 
				
				wc_add_notice(  __( 'Parece que o PicPay está fora do ar. Escolha outra forma de pagamento ou tente novamente!', 'woocommerce-picpay-payments' ), 'error' );
				
			   /**
				*
				* Set log promise for fail API response
				*
				*/

				$this->log->add( var_export($payment, true) );

			   /**
				*
				* Close payment process
				*
				*/ 

				return ['result' => 'fail'];			
			}
		}
    }

    /**
	 *
     * Notification request. Callback API for status changes
	 * Does not return the new status
     *
     * @access public
     * @return void
     *
     */ 

	public function webhook() {
	   
	   /**
		*
		* Clean PHP buffer
		*
		*/ 

		@ob_clean();
		
	   /**
		*
		* Check if isset header token in request
		*
		*/ 
		
		if( isset( $_SERVER['HTTP_X_SELLER_TOKEN'] ) ) {

		   /**
			*
			* Check if header token is equal to token admin option 
			*
			*/

			if( $_SERVER['HTTP_X_SELLER_TOKEN'] === $this->saller_token ) {
			   
			   /**
				*
				* Get request body
				*
				*/
				
				$payment = file_get_contents("php://input");
				
			   /**
				*
				* Convert to array PHP
				*
				*/
				
				$payment = json_decode( $payment, true );
			   
			   /**
				*
				* Set log promise for URL notification received 
				*
				*/

				$this->log
					->add( sprintf(__('PicPay Gateway recebeu uma notificação de URL: %s', 'woocommerce-picpay-payments'), var_export($payment, true)) );

			   /**
				*
				* Check if not exist erros
				*
				*/
				
				if( json_last_error() == JSON_ERROR_NONE ) {

					/**
					*
					* Gets the order status from the gateway API
					*
					*/
					
					$order_status = $this->status_order( $payment['referenceId'] );
				   
				   /**
					*
					* Combine status in array payment
					*
					*/
					
					$payment['status'] =  $order_status;
					
				   /**
					*
					* Get all order data
					*
					*/

					$order = wc_get_order( $payment['referenceId'] );
					
				   /**
					*
					* Add note in order
					*
					*/ 
				  
					$order->add_order_note(__( 'PicPay: Uma atualização de status foi recebida. O status da order no PicPay está como: ' . $payment['status'] ));
			  
				   /**
					*
					* Set authorizationId in order option
					*
					*/
					
					$order->add_meta_data( 'PicPayGateway_authorizationId_' . $payment['status'] , $payment['authorizationId'], true );
					
				   /**
					*
					* Save changes in order
					*
					*/ 

					$order->save();
					
				   /**
					*
					* Update order in woocommerce
					*
					*/
					
					$this->update_order( $payment );					
				}
				
			   /**
				*
				* Set if exist erros
				*
				*/
				
				else {

				   /**
					*
					* Set log promise for URL notification body json error 
					*
					*/

					$this->log
						->add( sprintf(__('PicPay Gateway recebeu uma notificação de URL, mas o corpo da mensagem é invalido : %s', 'woocommerce-picpay-payments'), var_export($payment, true)) );
				}
			}
		}
		exit;
	}

    /**
	 *
     * Get order status
     *
     * @access public
     * @return void
     *
     */ 

    public function status_order( $order_id ) {
		
        /**
		 *
		 * Set log promise for get status order
		 *
		 */

		$this->log
			->add( sprintf(__('Obtendo o status da order: %s', 'woocommerce-picpay-payments'), $order_id) );

        /**
		 *
		 * Get all order data
		 *
		 */

        $order = wc_get_order( $order_id );

		/**
		 *
		 * Set new order in API instance
		 *
		 */ 
	 
		$this->api->set_order( $order );
		
		/**
		 *
		 * Get order status
		 *
		 */ 

		$response = $this->api->payment_status();
		
			/**
			 *
			 * Check API response
			 *
			 */ 
			 
		if( $response['status'] === 'sucess' ) {
			
		    /**
			 *
			 * Set log promise for sucess API response
			 *
			 */

			$this->log->add( var_export($response, true) );
		
			/**
			 *
			 * Return succes and status array
			 *
			 */ 
		 
			return $response['body']['status'];
		 }

		/**
		 *
		 * Return and add log
		 *
		 */ 
		 
		 else {

			/**
			 *
			 * Set log promise for fail API response
			 *
			 */

			$this->log->add( var_export($response, true) );
			
			/**
			 *
			 * Return fail and error
			 *
			 */
			 
			return $response;
		}
	}

    /**
	 *
     * Update order status in Woocommerce
     *
     * @access public
     * @return void
     *
     */ 

    public function update_order( $payment ) {
		
		/**
		 *
		 * Get all order data
		 *
		 */ 
          
        $order = wc_get_order( $payment['referenceId'] );
		
		/**
		 *
		 * Actions for each status change
		 *
		 */ 
		 
		switch( $payment['status'] ) {
			
			/**
			 *
			 * update order status for cancelled
			 *
			 */
			 
			case 'expired':
				$order->update_status( 'cancelled', __('PicPay: Pagamento expirado.', 'woocommerce-picpay-payments' ));
				break;
				
			/**
			 *
			 * update order status for in analysis and reduce stock
			 *
			 */
			 
			case 'analysis':
				$order->update_status( 'on-hold', __('PicPay: Pagamento sob revisão.', 'woocommerce-picpay-payments' ));
				wc_reduce_stock_levels( $payment['referenceId'] );
				break;
				
			/**
			 *
			 * Check last status, if it is the same created reduce stock
			 * Update order status for processing
			 *
			 */
			 
			case 'paid':
				if($order->get_status() == 'created') wc_reduce_stock_levels( $payment['referenceId'] );
				$order->update_status('processing', __( 'PicPay: Pagamento aprovado.', 'woocommerce-picpay-payments' ));
				break;
				
			/**
			 *
			 * Add order note for payment completed
			 *
			 */
			 
			case 'completed':
				$order->add_order_note(__( 'PicPay: Pagamento concluído e creditado em sua conta.', 'woocommerce-picpay-payments' ));
				break;
				
			/**
			 *
			 * Check last status, if different from refunded Update and add order note for status refunded
			 * Increase stock.
			 * Send email
			 *
			 */
			 
			case 'refunded':
				if($order->get_status() != 'refunded') {
					$order->update_status('refunded', __( 'PicPay: Pagamento reembolsado.', 'woocommerce-picpay-payments' ));
					wc_increase_stock_levels( $payment['referenceId'] );
					$order->add_order_note(__( 'PicPay: Pagamento reembolsado.', 'woocommerce-picpay-payments' ));
				}
			
			/**
			 *
			 * Update order status for chargeback
			 *
			 */

			 case 'chargeback':
				$order->update_status( 'refunded', __('PicPay: Pagamento chargeback.', 'woocommerce-picpay-payments' ));		
				break;
				
			/**
			 *
			 * Empty for default
			 *
			 */

			 default:
				break;
		}
		
        /**
		 *
		 * Set log promise for update order
		 *
		 */

		$this->log
			->add( sprintf(__('Alterção de status da order: %s', 'woocommerce-picpay-payments'), var_export($payment, true)) );
	}
	
    /**
	 *
     * Check the requirements for run the gateway in checkout
     *
     * @access public
     * @param string
     * @return boolean
     *
     */ 
	 
	public function cancel_payment( $order_id ) {
		
        /**
		 *
		 * Set log promise for init cancel payment
		 *
		 */

		$this->log
			->add( sprintf(__('Processo para cancelamento da order: %s', 'woocommerce-picpay-payments'), $order_id) );
		
		/**
		 *
		 * Get all order data
		 *
		 */
		 
		$order = wc_get_order( $order_id );
		
		/**
		 *
		 * Gets the order status from the gateway API
		 *
		 */
		
		$order_status = $this->status_order( $order_id );
		
		/**
		 *
		 * If authorizationId is not empty or false
		 *
		 */
		
		if( $order_status == 'analysis' || $order_status == 'paid' ||
			$order_status == 'completed' ) {

			/**
			 *
			 * Send the cancellation request to API
			 *
			 */
			 
			 $response = $this->api->payment_cancel( $order_status );
			 
			/**
			 *
			 * Get API response status
			 *
			 */

			if( $response['status'] === 'sucess' ) {
				
				/**
				 *
				 * Save cancellationId in order
				 *
				 */
				 
				$order->add_meta_data( 'PicPayGateway_cancellationId', $response['body']['cancellationId'], true );
				
				/**
				 *
				 * Save changes in order
				 *
				 */
			
				$order->save();
				
				/**
				 *
				 * Set log promise for sucess API response
				 *
				 */

				$this->log->add( var_export($response, true) );
			 }

			/**
			 *
			 * Fail add log
			 *
			 */ 
			 
			 else {

				/**
				 *
				 * Set log promise for fail API response
				 *
				 */

				$this->log->add( var_export($response, true) );
			}
		}
		
		/**
		 *
		 * Fail add log
		 *
		 */ 
		 
		 else {

			/**
			 *
			 * Set log promise for empty or false authorizationId
			 *
			 */

			$this->log
				->add( sprintf(__('API não processada. A solicitação foi feita para um status de pedido não necessário ou com dados inválidos: %s', 'woocommerce-picpay-payments'), $order_id) );
		}
	}

    /**
	 *
     * Check the requirements for run the gateway in checkout
     *
     * @access public
     * @return boolean
     *
     */ 

    public function is_available() {

        /**
		 *
		 * Verify that the gateway configuration is valid
		 *
		 */

          return WC_Validation::is_valid_gateway();
    }
}

?>