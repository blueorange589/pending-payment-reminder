<?php
/**
 * Handler for AJAX requests
 *
 * @link              https://finpose.com
 * @since             1.0.0
 * @package           Finpose
 * @author            info@finpose.com
 */

class prfw_Ajax {

  function __construct() {

  }

  function run($p) {
    $pname = $p['process'];
    $hname = $p['handler'];
    
    // load app class
    require_once PRFW_PLUGIN_DIR . 'classes/app.class.php';
    require_once PRFW_PLUGIN_DIR . 'classes/'.$hname.'.class.php';

    $className = 'prfw_'.$hname;
    $handler = new $className('ajax');

    $out = array(
      'success' => $handler->success,
      'message' => $handler->message,
      'payload' => $handler->payload,
      'callback'=> $handler->callback
    );

    echo json_encode($out);
  }


}
