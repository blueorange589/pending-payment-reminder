<?php
/**
 * Class for reminder emails
 *
 *
 * @link              https://finpose.com
 * @since             1.0.0
 * @package           Finpose
 * @author            info@finpose.com
 */
if ( !class_exists( 'WC_Customer_Pending_Payment' ) ) {
  class WC_Customer_Pending_Payment extends WC_Email {
		/**
	 * Create an instance of the class.
	 *
	 * @access public
	 * @return void
	 */
		function __construct() {
 			// Email slug we can use to filter other data.
			$this->id          = 'wc_customer_pending_payment';
			$this->title       = __( 'Pending Payment Reminder', 'prfw' );
			$this->description = __( 'Payment reminder email sent to the customer, after send reminder button is clicked.', 'prfw' );
			// For admin area to let the user know we are sending this email to customers.
			$this->customer_email = true;
			$this->heading     = __( 'Order Pending Payment', 'prfw' );
			// translators: placeholder is {blogname}, a variable that will be substituted when email is sent out
			$this->subject     = sprintf( _x( '[%s] Order Pending Payment', 'default email subject for cancelled emails sent to the customer', 'prfw' ), '{blogname}' );
			
			// Template paths.
			$this->template_html  = 'emails/wc-customer-pending-payment.php';
			$this->template_plain = 'emails/plain/wc-customer-pending-payment.php';
			$this->template_base  = PRFW_PLUGIN_DIR . 'templates/';

			add_action( 'prfw_trigger_pending_payment_email', array( $this, 'trigger' ) );

			parent::__construct();
		}
	}

	  /**
	 * Trigger Function that will send this email to the customer.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $order_id ) {
		$this->object = wc_get_order( $order_id );

		if ( version_compare( '3.0.0', WC()->version, '>' ) ) {
			$order_email = $this->object->billing_email;
		} else {
			$order_email = $this->object->get_billing_email();
		}

		$this->recipient = $order_email;

		echo $this->recipient;
		exit;

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}


	  /**
	 * Get content html.
	 *
	 * @access public
	 * @return string
	 */
		function get_content_html() {
			return wc_get_template_html( $this->template_html, array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'			=> $this
			), '', $this->template_base );
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		function get_content_plain() {
			return wc_get_template_html( $this->template_plain, array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'			=> $this
			), '', $this->template_base );
		}

}