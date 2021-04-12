<?php
/*
Pending Payment Reminder for WooCommerce is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Pending Payment Reminder for WooCommerce is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Pending Payment Reminder for WooCommerce. If not, see {URI to Plugin License}.
*/

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://finpose.com
 * @since             1.0.0
 * @package           finpose
 *
 * @wordpress-plugin
 * Plugin Name:       Pending Payment Reminder for WooCommerce
 * Description:       List orders with pending payment status and send out a reminder email on a button click.
 * Version:           1.0.0
 * WC requires at least:  3.0.0
 * WC tested up to:       5.0.0
 * Author:            Finpose
 * Author URI:        https://finpose.com
 * Text Domain:       prfw
 * Domain Path:       /language
 *
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) { die; }


define( 'PRFW_VERSION', '1.0.0' );
define( 'PRFW_DBVERSION', '1.0.0' );
define( 'PRFW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PRFW_BASE_URL', plugin_dir_url( __FILE__ ) );
define( 'PRFW_ENV', 'production' );
define( 'PRFW_WP_URL', get_site_url() );
define( 'PRFW_WPADMIN_URL', get_admin_url() );


/**
 * Check if WooCommerce is installed & activated
 */
function prfw_is_woocommerce_activated() {
  $blog_plugins = get_option( 'active_plugins', array() );
  $site_plugins = is_multisite() ? (array) maybe_unserialize( get_site_option('active_sitewide_plugins' ) ) : array();

  if ( in_array( 'woocommerce/woocommerce.php', $blog_plugins ) || isset( $site_plugins['woocommerce/woocommerce.php'] ) ) {
      return true;
  } else {
      return false;
  }
}

/**
 * Generate error message if WooCommerce is not active
 */
function prfw_need_woocommerce() {
  $plugin_name = "Pending Payment Reminder for WooCommerce";
  printf(
    '<div class="notice error"><p><strong>%s</strong></p></div>',
    sprintf(
        esc_html__( '%s requires WooCommerce 3.0 or greater to be installed & activated!', 'prfw' ),
        $plugin_name
    )
  );
}

/**
 * Return error if WooCommerce is not active
 */
if (prfw_is_woocommerce_activated()) {

  /**
   * Activation hook
   */
  function prfw_activate() {
    require_once PRFW_PLUGIN_DIR . 'includes/class-prfw-activator.php';
    prfw_Activator::activate();
  }

  /**
   * Deactivation hook
   */
  function prfw_deactivate() {
    require_once PRFW_PLUGIN_DIR . 'includes/class-prfw-deactivator.php';
    prfw_Deactivator::deactivate();
  }

  /**
   * Register activation/deactivation hooks
   */
  register_activation_hook( __FILE__, 'prfw_activate' );
  register_deactivation_hook( __FILE__, 'prfw_deactivate' );

  /**
   * If version mismatch, upgrade
   */
  if ( PRFW_VERSION != get_option('prfw_version' )) {
    add_action( 'plugin_loaded', 'prfw_activate' );
  }

  /**
   * Register pending payment type of emails
   */
  add_filter( 'woocommerce_email_classes', function($emails) {
    require_once PRFW_PLUGIN_DIR.'emails/class-wc-customer-pending-payment.php';
    $emails['WC_Customer_Pending_Payment'] = new WC_Customer_Pending_Payment();
    return $emails;
  }, 90, 1 );

  /**
   * Handle AJAX requests
   */
  add_action( 'wp_ajax_prfw', 'prfw_ajax_request' );
  function prfw_ajax_request(){
    if(current_user_can( 'view_woocommerce_reports' )) {
      require PRFW_PLUGIN_DIR . 'includes/class-prfw-ajax.php';
      $ajax = new prfw_Ajax();
      // Sanitize every POST data as string, additional sanitation will be applied inside methods when necessary
      $p = array_map('sanitize_text_field', $_POST);
      $ajax->run($p);
      wp_die();
    }
  }

  /**
   * Load Pending Payment Reminder for WooCommerce
   */
  add_action( 'wp_loaded', function() {
    if(current_user_can( 'view_woocommerce_reports' )) {
      $user = wp_get_current_user();
      $roles = ( array ) $user->roles;
      if ( is_admin() || in_array("shop_manager", $roles)) {
        require PRFW_PLUGIN_DIR . 'includes/class-prfw.php';
        $plugin = new prfw();
        $plugin->run();
      }
    }
  }, 30 );


  

} else {
  add_action( 'admin_notices', 'prfw_need_woocommerce' );
  return;
}


