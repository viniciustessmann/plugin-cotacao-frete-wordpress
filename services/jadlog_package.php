<?php 

use Controllers\PackageController;
use Controllers\CotationController;
use Controllers\ProductsController;
use Controllers\TimeController;

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

    add_action( 'woocommerce_shipping_init', 'jadlog_package_shipping_method_init' );
	function jadlog_package_shipping_method_init() {
		if ( ! class_exists( 'WC_Jadlog_Package_Shipping_Method' ) ) {

			class WC_Jadlog_Package_Shipping_Method extends WC_Shipping_Method {

                public $code = '3';
				/**
				 * Constructor for your shipping class
				 *
				 * @access public
				 * @return void
				 */
				public function __construct($instance_id = 0) {
					$this->id                 = "jadlog_package"; 
                    $this->instance_id = absint( $instance_id );
                    $this->method_title       = "Jadlog Package (Melhor envio)"; 
					$this->method_description = 'Serviço Jadlog Package';
					$this->enabled            = "yes"; 
					$this->title              = isset($this->settings['title']) ? $this->settings['title'] : 'Melhor Envio Jadlog Package';
                    $this->supports = array(
                        'shipping-zones',
                        'instance-settings',
                    );
					$this->init_form_fields();
				}
				
				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init() {
					$this->init_form_fields(); 
					$this->init_settings(); 
					add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
				}

				/**
				 * calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package = []) {
					
					global $woocommerce;
					$to = str_replace('-', '', $package['destination']['postcode']);

					$prod = new ProductsController();
					$products = $prod->getProductsCart();
					
					$cotation = new CotationController();					
					$result = $cotation->makeCotationproducts($products, [$this->code], $to);

					if ($result = $cotation->makeCotationproducts($products, [$this->code], $to)) {

						if (isset($result->name) && isset($result->price)) {
							$rate = [
								'id' => 'melhorenvio_jadlog_package',
								'label' => $result->name . (new timeController)->setLabel($result->delivery_range),
								'cost' => $result->price,
								'calc_tax' => 'per_item',
								'meta_data' => [
									'delivery_time' => $result->delivery_time,
									'company' => 'Jadlog'
								]
							]; 
							$this->add_rate($rate);
						}
					}
                }
                
                /**
				 * Initialise Gateway Settings Form Fields
				 */
				function init_form_fields() {

					$this->form_fields = [
						'title' => [
							'title' => 'Titulo',
							'type' => 'text',
							'default' => 'jadlog Package'
						],
						'enabled' => [
							'title' => 'Ativar',
							'type' => 'checkbox',
							'default' => 'yes'
						],
					];
				}   
			}
		}
	}
	
	function add_jadlog_package_shipping_method( $methods ) {
		$methods['jadlog_package'] = 'WC_Jadlog_Package_Shipping_Method';
		return $methods;
	}
	add_filter( 'woocommerce_shipping_methods', 'add_jadlog_package_shipping_method' );

	function jadlog_package_validate_order($posted) {

		// $packages = WC()->shipping->get_packages();
		// $chosen_methods = WC()->session->get('chosen_shipping_methods');
		
        // if (is_array($chosen_methods) && in_array('melhorenvio_jadlog_package', $chosen_methods)) {
        //     foreach ($packages as $i => $package) {
        //         if ($chosen_methods[$i] != "melhorenvio_jadlog_package") {
        //             continue;
        //         }
        //         $jadlog_package_Shipping_Method = new WC_Jadlog_Package_Shipping_Method();
        //         $weightLimit = (int)$jadlog_package_Shipping_Method->settings['weight'];
        //         $weight = 0;
        //         foreach ($package['contents'] as $item_id => $values) {
        //             $_product = $values['data'];
		// 			$weight = $weight + $_product->get_weight() * $values['quantity'];
        //         }
        //         $weight = wc_get_weight($weight, 'kg');
                // if ($weight > $weightLimit) {
                //     $message = sprintf(__('OOPS, %d kg increase the maximum weight of %d kg for %s', 'jadlog_package'), $weight, $weightLimit, $jadlog_package_Shipping_Method->title);
                //     $messageType = "error";
                //     if (!wc_has_notice($message, $messageType)) {
                //         wc_add_notice($message, $messageType);
                //     }
				// }
        //     }
        // }
	}
	
	add_action('woocommerce_review_order_before_cart_contents', 'jadlog_package_validate_order', 10);
	add_action('woocommerce_after_checkout_validation', 'jadlog_package_validate_order', 10);
}
