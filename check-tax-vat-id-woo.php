<?php
/*

 * Plugin Name: VAT / UST ID Checker - Validator EU for WooCommerce

 * Version: 1.0.0

 * Plugin URI: http://www.mlfactory.de

 * Description: Allows you to check if the vat / ust id is valid and apply Reverse Charge.

 * Author: Michael Leithold

 * Author URI: https://profiles.wordpress.org/mlfactory/

 * Requires at least: 4.0

 * Tested up to: 5.5

 * License: GPLv2 or later

 * Text Domain: ctviwoo
 
 * Domain Path: /languages
 *

*/
 
if ( ! defined( 'WPINC' ) ) {
	die;
}


require_once( trailingslashit( dirname( __FILE__ ) ) . 'core/lib/autoloader.php' );

use PH7\Eu\Vat\Exception as EuVatExcept;
use PH7\Eu\Vat\Provider\Europa;
use PH7\Eu\Vat\Validator;
//*************************//
//*****B A C K E N D*****//
//*************************//

class ctviwoo_backend {
	
	public static function init() {

		add_action('wpi_after_formatted_billing_address', __CLASS__ . '::ctviwoo_wpi_add_vat');

		add_action( 'woocommerce_admin_order_data_after_billing_address', __CLASS__ . '::ctviwoo_woo_checkout_page', 10, 1 );
	 
	}

	static public function ctviwoo_woo_checkout_page($order){
		global $post_id;
		$order = new WC_Order( $post_id );
		$private_or_company = get_post_meta($order->get_id(), '_ctviwoo_private_company', true );
		$vat_id = get_post_meta($order->get_id(), '_ctviwoo_vat_id', true );
		
		if (empty($private_or_company)) {
			$private_or_company = __('Privatperson');
		}
		echo '<p><strong>'.__('Unternehmen/Privatperson').':</strong><br/> ' . $private_or_company . '</p>';

		if ($private_or_company == "Company" && !empty($vat_id)) {
			echo '<p><strong>'.__('UST ID', 'ctviwoo').':</strong><br/> ' . $vat_id . '</p>';
		}
	}


	static public function ctviwoo_wpi_add_vat($invoice){
		$invoice_data = array();
		$order_data = array();
		$order_id = '';

		foreach ( $invoice->get_invoice_info() as $info_id => $info ) {
			$invoice_data = $invoice->get_invoice_info();			
		}

		$order_data = $invoice_data['order_number'];
		$order_id = $order_data['value'];

		if(!empty($order_id)){
			
			$isvatempty = get_post_meta( $order_id, 'is_vat_exempt', true );
			$vatid = get_post_meta( $order_id, '_ctviwoo_vat_id', true );
			if ($isvatempty == 'yes' && isset($vatid) && !empty($vatid)) {
				$html .= '<p>'.$vatid.'</p>';	
				$html .= '<p><strong>'.__( 'Umkehr der Steuerschuld<br/>§ 13b Abs. 2 UStG', 'ctviwoo' ).'</strong></p>';	
			}
			

			if($html){
				echo $html;	
			}
		}
	}


}


ctviwoo_backend::init();	 



//*************************//
//*****F R O N T E N D*****//
//*************************//

class ctviwoo_frontend {
	
	public static function init() {
		 

		add_action( 'woocommerce_after_order_notes',  __CLASS__ . '::ctviwoo_ustid_inputfield' );
		add_action( 'woocommerce_checkout_update_order_review',  __CLASS__ . '::ctviwoo_validate_ustid' );
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::ctviwoo_load_scripts' );
		add_action( 'woocommerce_thankyou', __CLASS__ . '::ctviwoo_woo_thankyou', 99, 2 );
		add_action('woocommerce_checkout_update_user_meta',  __CLASS__ . '::ctviwoo_update_user_meta');
		add_action('woocommerce_checkout_update_order_meta', __CLASS__ . '::ctviwoo_update_order_meta');
		add_action( 'wp_footer', __CLASS__ . '::ctviwoo_woo_add_update_cart' );
		add_action('wp_ajax_ctviwoo_update_session',  __CLASS__ . '::ctviwoo_update_session');
		add_action('wp_ajax_nopriv_ctviwoo_update_session', __CLASS__ . '::ctviwoo_update_session');
		add_action( 'woocommerce_before_checkout_billing_form', __CLASS__ . '::ctviwoo_checkout_fields', 1 );
	 

		function ctviwoo_wooformfield( $key, $args, $value = '' ) {
					global $woocommerce;
						$defaults = array(
										 'type' => 'radio',
										'label' => '',
										'placeholder' => '',
										'required' => false,
										'class' => array( ),
										'label_class' => array( ),
										'return' => false,
										'options' => array( )
						);
						$args     = wp_parse_args( $args, $defaults );
						if ( ( isset( $args[ 'clear' ] ) && $args[ 'clear' ] ) )
										$after = '<div class="clear"></div>';
						else
										$after = '';
						$required = ( $args[ 'required' ] ) ? ' <abbr class="required" title="' . esc_attr__( 'required', 'woocommerce' ) . '">*</abbr>' : '';
						switch ( $args[ 'type' ] ) {
										case "select":
														$options = '';
														if ( !empty( $args[ 'options' ] ) )
																		foreach ( $args[ 'options' ] as $option_key => $option_text )
																						$options .= '<input type="radio" name="' . $key . '" id="' . $key . '" value="' . $option_key . '" ' . selected( $value, $option_key, false ) . 'class="select">' . $option_text . '' . "\r\n";
														$field = '<p class="form-row ' . implode( ' ', $args[ 'class' ] ) . '" id="' . $key . '_field">
		<label for="' . $key . '" class="' . implode( ' ', $args[ 'label_class' ] ) . '">' . $args[ 'label' ] . $required . '</label>
		' . $options . '
		</p>' . $after;
														break;
						}
						if ( $args[ 'return' ] )
										return $field;
						else
										echo $field;
		}

	
	 
	}

	static public function ctviwoo_checkout_fields( $checkout ) {
					echo '<div id="ctviwoo_checkout_fields">';
					ctviwoo_wooformfield( 'ctviwoo_corp', array(
									 'type' => 'select',
									'class' => array(
													 'ctvidwoo_private_company form-row-wide'
									),
									'label' => __( '' ),
									'placeholder' => __( '' ),
									'required' => true,
									'options' => array(
													'Private' => __( 'Privatperson', 'ctviwoo' ).'<br/>',
													'Company' => __( 'Unternehmen', 'ctviwoo' ).'<br/>'

													
									)
					), $checkout->get_value( 'ctviwoo_corp' ) );
					echo '</div>';
	}	


	static public function ctviwoo_update_session() {
		
	$value = sanitize_text_field($_REQUEST['value']);

		if (isset($value)) {
			global $woocommerce;
			WC()->session->set( 'ctviwoo_companyorprivate', $value );
			
		}
		wp_die();
	}
	


	static public function ctviwoo_woo_add_update_cart() {
		if (function_exists('is_checkout')) {
			if( ! is_checkout() ) {
				return;
			} else {
			?>
			<script type="text/javascript">
			jQuery(function($){
				$(document).on('change','#ctviwoo_ust_input',function(){
					$(document.body).trigger("update_checkout");
				});
				$(document).on('change','input[name=\'ctviwoo_corp\']',function(){
				  $.ajax({
					 type : "post",
					 url : "<?php echo admin_url( 'admin-ajax.php' ) ?>",
					 data : {action: "ctviwoo_update_session", value: this.value},
					 success: function(response) {
						$(document.body).trigger("update_checkout");
					 }	
					})   		 
				});			
				<?php if (WC()->session->get( 'ctviwoo_companyorprivate') == "Company") { ?>
					$("input[name='ctviwoo_corp'][value='Company']").prop("checked", true);
					$("#ctviwoo_ust_wrapper").show(300);
					
				<?php } else { ?>
					$("input[name='ctviwoo_corp'][value='Private']").prop("checked", true);
					$("#ctviwoo_ust_wrapper").hide(300);
				<?php } ?>			
			});
			</script>
			<?php
			}
		} else {
			return;
		}
	}

	public static function ctviwoo_update_order_meta( $order_id ) {
		if (isset($_POST['ctviwoo_corp']) && $_POST['ctviwoo_corp'] != ""){
			update_post_meta( $order_id, '_ctviwoo_private_company', sanitize_text_field($_POST['ctviwoo_corp']));
			if (sanitize_text_field($_POST['ctviwoo_corp']) == "Company") {
				update_post_meta( $order_id, '_ctviwoo_vat_id', sanitize_text_field($_POST['ctviwoo_ust_input']));
				update_post_meta( $order_id, '_vat_number_is_valid', 'yes' );
				update_post_meta( $order_id, '_vat_number_validated', 'yes' );
			}
		}
	}

	static public function ctviwoo_update_user_meta( $user_id ) {
		if (isset($_POST['ctviwoo_corp']) && $_POST['ctviwoo_corp'] != ""){
			update_user_meta( $user_id, '_ctviwoo_private_company', sanitize_text_field($_POST['ctviwoo_corp']));
			if (sanitize_text_field($_POST['ctviwoo_corp']) == "Company") {
				update_user_meta( $user_id, '_ctviwoo_vat_id', sanitize_text_field($_POST['ctviwoo_ust_input']));
				update_post_meta( $order_id, '_vat_number_is_valid', 'yes' );
				update_post_meta( $order_id, '_vat_number_validated', 'yes' );
			}
		}
	}

	static public function ctviwoo_woo_thankyou( $order_id ) {
		$vat_id = get_post_meta( $order_id, '_ctviwoo_vat_id', true);
		if (isset($vat_id) && !empty($vat_id)) {
		echo '<p>'. __('UST ID:', 'ctviwoo').'&nbsp;'.$vat_id.'</p>';
		}
	}

	
	public static function ctviwoo_load_scripts($hook) {
		
		wp_enqueue_script('ctviwoo_js', plugins_url('core/js/ctviwoo.js',__FILE__ ), array('jquery'));	
	
	}
	
	
		
	static public function ctviwoo_validate_ustid( $checkout ) {
		
		if ( isset( $checkout ) ) {
			
			parse_str( $checkout, $post_data );
			
			array_map( 'sanitize_text_field', $post_data );	
			
			if (isset($post_data['ctviwoo_ust_input']) && isset($post_data['ctviwoo_corp']) && $post_data['ctviwoo_corp'] == "Company" && $post_data['ctviwoo_ust_input'] != "") {
					
				$ustid = $post_data['ctviwoo_ust_input'];	
				
				$cop = $post_data['ctviwoo_corp'];
				
				$countrycode = substr($ustid, 0, 2);
				
				$number = substr($ustid, 2);

				$europe_country_codes = array( 'AD', 'AL', 'AT', 'AX', 'BA', 'BE', 'BG', 'BY', 'CH', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FO', 'FR', 'FX', 'GB', 'GG', 'GI', 'GR', 'HR', 'HU', 'IE', 'IM', 'IS', 'IT', 'JE', 'LI', 'LT', 'LU', 'LV', 'MC', 'MD', 'ME', 'MK', 'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'RS', 'RU', 'SE', 'SI', 'SJ', 'SK', 'SM', 'TR', 'UA', 'VA' );

				$base_country_code = WC_Countries::get_base_country();

				global $woocommerce;

				if(in_array( $countrycode, $europe_country_codes ) && $countrycode != $base_country_code) {

					try {
						
						$oVatValidator = new Validator(new Europa, $number, $countrycode);
						
						if ($oVatValidator->check()) {
							
							$sRequestDate = $oVatValidator->getRequestDate();
							
							$sFormattedRequestDate = (new DateTime)->format('d-m-Y');
							
							$woocommerce->customer->set_is_vat_exempt( true );
							
							wc_add_notice( __("UST ID gültig.<br />Umkehr der Steuerschuld wird angewendet.", "ctviwoo"), "notice" );
							
							WC()->session->set( 'ctviwoo_ustid', $ustid );
							
							WC()->session->set( 'ctviwoo_companyorprivate', $cop );

						} else {
										
							$woocommerce->customer->set_is_vat_exempt( false );	
							
							wc_add_notice( __("Für diese UST ID ist keine Umkehr der Steuerschuld möglich.", "ctviwoo"), "notice" );
							
						}
						
					} catch(EuVatExcept $oExcept) {
							
							wc_add_notice( __("Für diese UST ID ist keine Umkehr der Steuerschuld möglich.", "ctviwoo"), "notice" );
					
					}
				
				} else {
					
					$woocommerce->customer->set_is_vat_exempt( false );
					
				}
				

			} else {
					
				global $woocommerce;
					
				$woocommerce->customer->set_is_vat_exempt( false );
					
			}			
			
		} else {
			
			global $woocommerce;
					
			$woocommerce->customer->set_is_vat_exempt( false );
			
		}	

	}		

	static public function ctviwoo_ustid_inputfield( $checkout ) {

		echo '<div id="ctviwoo_ust_wrapper"><h2>' . __('Ust. ID') . '</h2>';

		woocommerce_form_field( 'ctviwoo_ust_input', array(
			'type'          => 'text',
			'class'         => array('my-field-class form-row-wide'),
			'label'         => __('UST ID'),
			'placeholder'   => __('Geben Sie hier Ihre UST ID ein'),
			), WC()->session->get( 'ctviwoo_ustid'));

		echo '</div>';

	}	

}


ctviwoo_frontend::init();	 
		 
?>