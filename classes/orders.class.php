<?php
/**
 * Class for Sales
 *
 *
 * @link              https://finpose.com
 * @since             1.1.0
 * @package           Finpose
 * @author            info@finpose.com
 */
if ( !class_exists( 'prfw_orders' ) ) {
  class prfw_orders extends prfw_app {

    public $v = 'pageOrders';
    public $p = '';

    public $selyear;
		public $selmonth;
		public $selq;

    public $success = false;
    public $message = '';
    public $results = array();
    public $callback = '';

    /**
	 * Reporting Constructor
	 */
    public function __construct($v = 'pageOrders') {
      parent::__construct();
      $this->selyear = $this->curyear;
			$this->selmonth = $this->curmonth;
			$this->selq = $this->curq;

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


  }
}
