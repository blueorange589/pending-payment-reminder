<?php
/**
 * Application main class
 *
 *
 * @link              https://finpose.com
 * @since             1.0.0
 * @package           Finpose
 * @author            info@finpose.com
 */
if ( !class_exists( 'prfw_app' ) ) {
  class prfw_app {
    public $put;
    public $ask;
    public $get = array();
    public $post = array();
    public $success = false;
    public $message = '';
    public $db;
    public $validated = false;

    public $curyear;
    public $curmonth;

    public $view = array();

    /**
	 * PRFW App Constructor
	 */
    public function __construct() {
      $this->assignInputs();

      $this->curyear = date('Y');
      $this->curmonth = date('m');
      $this->curq = ceil(date("n") / 3);

      $this->view['years'] = $this->getYears();
      
      require 'put.class.php';
      $this->put = new prfw_put($this->db);

      require 'ask.class.php';
      $this->ask = new prfw_ask($this->db);

    }

    /**
	 * All GET, POST variables sanitized as string, additional sanitation will be applied inside methods when necessary
	 */
    public function assignInputs() {
      $this->get = array_map('sanitize_text_field', $_GET);
      $this->post = array_map('sanitize_text_field', $_POST);
    }

    /**
	 * Retrieve list of years available
	 */
    public function getYears() {
      $cy = date('Y');
      $cs = $cy-7;
      $years = array();
      for ($i = $cs; $i <= $cy; $i++) {
        $years[$i] = $i;
      }
    return array_reverse($years, true);
    }

    /**
	 * Get start of each month for given year in unix timestamp
	 */
    public function getMonthsUnix($year) {
      $months = array();
      for ($m = 1; $m < 13; $m++) {
        $mstart = strtotime($year."-".$m."-01");
        $months[$m] = $mstart;
        if($m==12) {
          $months[] = strtotime(($year+1)."-01-01");
        }
      }
    return $months;
    }

    /**
	 * Format given timestamp as date
	 */
    public function dateFormat($unix) {
      return date("M d, Y", $unix);
    }

    /**
	 * Format given timestamp as time
	 */
    public function timeFormat($unix) {
      return date("F d, Y H:i:s", $unix);
    }

    /**
	 * Format monetary values
	 */
    public function format($amount,$commas=true) {
      if(!$amount) return 0;
      $thousandSeperator = ',';
      if(!$commas) $thousandSeperator = '';
      return number_format((float)$amount, 2, '.', $thousandSeperator);
    }

    /**
	 * Format monetary values before insert operation
	 */
    public function moneyToDB($amount) {
      $nocomma = str_replace(",","",$amount);
      return number_format((float)$nocomma, 2, '.', '');
    }

    /**
	 * Add zero when month number below 10
	 */
    public function addZero($month) {
      return $month<10?'0'.$month:$month;
    }

    /**
	 * Format all values in given array recursively
	 */
    public function autoFormat($marr, $commas=true) {
      foreach ($marr as $mk=>$mv) {
        if(is_array($mv)) {
          foreach ($mv as $sk=>$sv) {
            $t = gettype($sv);
            if ($t=='double'||$t=='float') {
              $marr[$mk][$sk] = $this->format($sv, $commas);
            } else if ($t=='integer') {
              if($sk=='qty') { $marr[$mk][$sk] = $sv; } else {
                $marr[$mk][$sk] = $this->format($sv, $commas);
              }
            } else if ($t=='string') {
              $marr[$mk][$sk] = $sv;
            }
          }
        } else {
          $t = gettype($mv);
          if ($t=='double'||$t=='float') {
            $marr[$mk] = $this->format($mv, $commas);
          } else if ($t=='integer') {
            if($mk=='qty') { $marr[$mk] = $mv; } else {
              $marr[$mk] = $this->format($mv, $commas);
            }
          } else if ($t=='string') {
            $marr[$mk] = $mv;
          }
        }
      }
    return $marr;
    }

    /**
	 * Produce random chars
	 */
    public function randomChars($length = 8) {
      $start = rand(0,10);
      return substr(md5(time()), $start, $length);
    }

  }

}
