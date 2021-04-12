<?php
/**
 * Class for handling all actions
 *
 *
 * @link              https://finpose.com
 * @since             1.0.0
 * @package           Finpose
 * @author            info@finpose.com
 */
if ( !class_exists( 'prfw_orders' ) ) {
  class prfw_handler extends prfw_app {

    public $v = 'pageOrders';
    public $p = '';

    public $success = false;
    public $message = '';
    public $results = array();
    public $callback = '';

		public $reminder;

    /**
	 * Handler class Constructor
	 */
    public function __construct($v = 'pageOrders') {
      parent::__construct();

			

      // POST verification, before processing
      if($this->post) {
        $validated = $this->validate();
        if($validated) {
          $verified = wp_verify_nonce( $this->post['nonce'], 'prfwpost' );
          $can = current_user_can( 'view_woocommerce_reports' );
          if($verified && $can) {
            if(isset($this->post['process'])) {
              $p = $this->post['process'];
              unset(
                $this->post['process'],
                $this->post['handler'],
                $this->post['action'],
                $this->post['nonce'],
                $this->post['_wp_http_referer']
              );
              $this->$p();
            }
          }
        }
      }

      if($v != 'ajax') { $this->$v(); }

      if($this->ask->errmsg) { $this->view['errmsg'] = $this->ask->errmsg; }
		}

		/**
		 * Validate all inputs before use
		 */
		public function validate($vars = array()) {
			$status = true;

			if(!$vars) { $vars = $this->post; }
			foreach ($vars as $pk => $pv) {
				if($pk == 'year') {
					if(intval($pv)>2030||intval($pv)<2010) {
						$status = false;
						$this->message = esc_html__( 'Year provided is invalid', 'prfw' );
					}
				}
				if($pk == 'month') {
					if(intval($pv)>12||intval($pv)<1) {
						$status = false;
						$this->message = esc_html__( 'Month provided is invalid', 'prfw' );
					}
				}
				if(in_array($pk, array('datestart', 'dateend'))) {
					if($pv) {
						if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $pv)) {
							$status = false;
							$this->message = esc_html__( 'Date format provided is invalid', 'finpose' );
						}
					}
				}
				if($pk == 'totalthan') {
					if(!in_array($pv, array('lower', 'greater'))) {
            $status = false;
            $this->message = esc_html__( 'Total selector can only be lower or greater', 'finpose' );
          }
				}
				if($pk == 'datetype') {
					if(!in_array($pv, array('date_created', 'date_paid', 'date_invoice'))) {
            $status = false;
            $this->message = esc_html__( 'Invalid date type selector', 'finpose' );
          }
				}
				if($pk == 'status') {
					if(!in_array($pv, array('all', 'completed', 'pending', 'processing', 'on-hold', 'cancelled', 'refunded', 'failed'))) {
            $status = false;
            $this->message = esc_html__( 'Invalid date type selector', 'finpose' );
          }
				}
			}

		return $status;
		}

		public function pageOrders() {

		}
		

		public function getPendingOrders() {
			$this->payload['orders'] = $this->pendingOrders();
			$this->success = true;
		}

		function get_custom_email_html( $order, $heading = false, $mailer ) {

			$template = 'emails/wc-customer-pending-payment.php';
			$template_base  = PRFW_PLUGIN_DIR . 'templates/';

			return wc_get_template_html( $template, array(
				'order'         => $order,
				'email_heading' => $heading,
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $mailer
			), '', $template_base );
		
		}

		// TODO : proceed to payment button to email
		public function sendReminder() {
			 // Get woocommerce mailer from instance
			$mailer = WC()->mailer();
			$order = wc_get_order($this->post['oid']);
			$order_data = $order->get_data();
			$customer = new WC_Customer($order_data['customer_id']);
			$reminder = WC()->mailer()->get_emails()['WC_Customer_Pending_Payment'];
			//format the email
			$recipient = $customer->get_email();
			$subject = $reminder->get_subject();
			$content = $this->get_custom_email_html( $order, $subject, $mailer );
			$headers = "Content-Type: text/html\r\n";

			//send the email through wordpress
			$mailer->send( $recipient, $subject, $content, $headers );
		}

		// TODO : option to cancel the order
		public function cancelTheOrder() {
				
		}


		/**
	 * Retrieve pending orders from WooCommerce
	 */
		private function pendingOrders() {
			$query = ( 
					array(
						'orderby' => 'date',
						'order'   => 'DESC',
						'status'  => array( 'wc-pending', 'wc-on-hold' )
					) 
			);
			
			$orders = wc_get_orders( $query );
			
			$list = array();
			foreach ($orders as $order) {
				$order_data = $order->get_data();
				$cus = new WC_Customer($order_data['customer_id']);

				$order_meta = get_post_meta($order->get_meta_data());
				
				$geo = $order_data['billing']['country'];
				$gn = WC()->countries->countries[ $geo ];
				$pm = $order_data['payment_method_title'];

				$od['id'] = $order_data['id'];
				$od['status'] = $order_data['status'];
				$od['editurl'] = $order->get_edit_order_url();
				$oddate = $order->get_date_created();
				$odtime = strtotime($oddate);
				$od['date'] = date('Y-m-d H:i', $odtime);
				$od['pm'] = $pm;
				$od['cus'] = $cus->get_first_name().' '.$cus->get_last_name();
				$od['email'] = $cus->get_email();
				$od['geo'] = $gn;
				$od['total'] = $order_data['total'];
				$od['lastReminder'] = isset($order_meta['lastReminder'])?$order_meta['lastReminder']:0;
				$list[] = $od;
			}
			return $list;
		}

		// TODO : automate reminders
		public function automatedReminders() {
			$pending = $this->pendingOrders();

			// TODO : if last reminder more than 1 week ago send out email
		}


  }
}
