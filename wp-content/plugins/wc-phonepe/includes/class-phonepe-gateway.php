<?php

/**
 * Payment Gateway for PhonePe.
 *
 * This class defines all code necessary for phonepe payment gateway.
 *
 * @since      1.0.0
 * @author     Sevengits <sevengits@gmail.com>
 */

class WC_PhonePe_Gateway extends WC_Payment_Gateway {
 
    /**
     * Class constructor
     */
    public function __construct() {

        $this->id = 'sg-phonepe'; // payment gateway plugin ID
        $this->icon= apply_filters('phonepe_icon', plugin_dir_url( dirname( __FILE__ ) ) . 'assets/phonepe.svg'); // URL of the icon that will be displayed on checkout page near your gateway name
        $this->has_fields = false; // in case you need a custom credit card form
        $this->method_title = 'PhonePe';
        $this->method_description = 'PhonePe UPI payment gateway'; // will be displayed on the options page
     
        
        $this->supports = array(
            'products'
        );
     
        // Method with all the options fields
        $this->init_form_fields();
     
        // Load the settings.
        $this->init_settings();
        $this->title = $this->get_option( 'title' );
        $this->description = $this->get_option( 'description' );
        $this->enabled = $this->get_option( 'enabled' );
        $this->mode = $this->get_option( 'testmode' );
        $this->merchant_id =  $this->get_option( 'merchant_id' );
        $this->salt_key =  $this->get_option( 'phonepe_salt_key' );
        $this->salt_key_index =  $this->get_option( 'phonepe_salt_key_index' );
     
        // This action hook saves the settings
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
     
        // We need custom JavaScript to obtain a token
        add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
     
        // You can also register a webhook here
         add_action( 'woocommerce_api_phonepe-payment-complete', array( $this, 'webhook' ) );

    }

   /**
     * Plugin options, we deal 
     */
    public function init_form_fields(){

        $this->form_fields = array(
            'enabled' => array(
                'title'       => 'Enable/Disable',
                'label'       => 'Enable PhonePe',
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'no'
            ),
            'title' => array(
                'title'       => __('Title','phonepe'),
                'type'        => 'text',
                'description' => __('This controls the title which the user sees during checkout.','phonepe'),
                'default'     => 'PhonePe UPI',
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => __('Description','phonepe'),
                'type'        => 'textarea',
                'description' => __('This controls the description which the user sees during checkout.','phonepe'),
                'default'     => 'Pay with your UPI.',
            ),
            'merchant_id' => array(
                'title'       => __('Merchant Id','phonepe'),
                'type'        => 'text'
            ),
            'phonepe_salt_key' => array(
                'title'       => __('SaltKey','phonepe'),
                'type'        => 'text',
                
            ),
            'phonepe_salt_key_index' => array(
                'title'       => __('SaltKey Index','phonepe'),
                'type'        => 'text',
            ),
            
            'phonepe_environment' => array(
                'title'       => __('Mode','phonepe'),
                'type'        => 'select',
                'options'		=> array(
                    'test' => __('Test Mode','phonepe'),
                    'live' => __('Live Mode','phonepe'),
                ),
            ),
            
        );

    }

   /**
    * You will need it if you want your custom credit card form 
    */
   public function payment_fields() {

   }

   /*
    * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
    */
    public function payment_scripts() {

    }

   /*
     * Fields validation
    */
   public function validate_fields() {

   }

   /*
    * We're processing the payments here
    */
   public function process_payment( $order_id ) {

    global $woocommerce;
 
	// we need it to get any order detailes
	$order = wc_get_order( $order_id );
    if($this->mode=='live'){
        //live mode
        $endpoint = 'https://mercury-t2.phonepe.com/v4/debit'; 
    }else{
        //test mode
        $endpoint = 'https://mercury-uat.phonepe.com/v4/debit'; 
    }
    // get phonepe varibles from settings
    $merchant_id         = $this->merchant_id;
    $salt_key            = $this->salt_key;
    $salt_key_index      = $this->salt_key_index;
    $transaction_id      = time();
   
	/*
 	 * Array with parameters for API interaction
	 */
	$data = array(

        "merchantId" 	    =>  $merchant_id,
		"transactionId"	    =>  $transaction_id  ,
        "amount"            =>  $order->get_total() * 100,// in paisa
        "email"             =>  $order->get_billing_email(),
        "merchantUserId"    =>  $order->get_customer_id() 
	);
    $encrypted_data= base64_encode(json_encode($data));

    $xvarify = hash('sha256',$encrypted_data ."/v4/debit".$salt_key)."###".$salt_key_index  ; 
    $post_data = json_encode(array("request" => $encrypted_data));
    /*
	 *  API interaction could be built with wp_remote_post()
 	 */
      
     
      $options = array(
        'body'        =>    $post_data,
        'method'      =>    'POST',
        'sslverify'   =>    false,
        'data_format' =>    'body',
        'user-agent'  =>    'woo-plugin',
        'cookies'     => array(),
        'headers'     => array(
            'Content-Type'          => 'application/json',
            'Content-Length'        =>  strlen($post_data),
            'X-VERIFY'              =>  $xvarify,
            'X-REDIRECT-URL'        => $this->get_return_url( $order ),
            'X-CALLBACK-URL'        => get_site_url().'/wc-api/phonepe-payment-complete/?order_id='.$order_id.'&transaction_id='.$transaction_id
            
        ),

      );
    
	 $response = wp_remote_post( $endpoint,$options );

    
	 if( !is_wp_error( $response ) ) {
 
		 $body = json_decode( $response['body'], true );
        
		 // it could be different depending on your payment processor
		 if ( $body['code'] == 'SUCCESS' ) {
            $redirect_url=$body['data']['redirectURL'];
			$order->update_status( 'pending', '', true );
			 
			// some notes to customer (replace true with false to make it private)
		    $order->add_order_note( 'PhonePe transaction id is '.$transaction_id." and transaction status".$body['code']);
        
			// Empty cart
			$woocommerce->cart->empty_cart();
            
			// Redirect to the thank you page
			return array(
				'result' => 'success',
				'redirect' =>  $redirect_url
			);
 
		 } else {
			wc_add_notice(  $body['message'], 'error' );
			return;
		}
 
	} else {
		wc_add_notice(  'Connection error.', 'error' );
		return;
	}
 

    }

   /*
    * In case you need a webhook
    */
   public function webhook() {
    
    $order_id = isset($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : null;
    if (is_null($order_id)) return;
    $transaction_id = isset($_REQUEST['transaction_id']) ? intval($_REQUEST['transaction_id']) : null;
    if (is_null($transaction_id)) return;

    $salt_key            = $this->salt_key;
    $salt_key_index      = $this->salt_key_index;
    $merchant_id         = $this->merchant_id;
    if($this->mode=='live'){
        //live mode
        $endpoint = 'https://mercury-t2.phonepe.com/v3/transaction/'.$merchant_id."/".$transaction_id."/status"; 
    }else{
        //test mode
        $endpoint = 'https://mercury-uat.phonepe.com/v3/transaction/'.$merchant_id."/".$transaction_id."/status"; 
    }
    $xvarify = hash('sha256', "/v3/transaction/" . $merchant_id  . "/" . $transaction_id . "/status" .$salt_key)."###" . $salt_key_index; 
    
    $options = array(
        'method'      =>    'GET',
        'sslverify'   =>    false,
        'user-agent'  =>    'woo-plugin',
        'cookies'     => array(),
        'headers'     => array(
            'Content-Type'          => 'application/json',
           'X-VERIFY'              => $xvarify,
        ),

      );
    
	 $response = wp_remote_get( $endpoint,$options );
     $body = json_decode( $response['body'], true );
      if($body['success']==true){
        // Payment transaction is successfull.
        if ( $body['code'] == 'PAYMENT_SUCCESS' ) {
            $order = wc_get_order($order_id);
            $order->payment_complete();
            wc_reduce_stock_levels($order_id);
        }
        }else{
        $order = wc_get_order($order_id);
        // add response message in as note.
		$order->add_order_note( 'PhonePe :'.$body['message']);
      }  



    }
}
?>