<?php
/**
 * The admin-specific functionality of the plugin.
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 * @link              https://finpose.com
 * @since             1.0.0
 * @package           Finpose
 * @author            info@finpose.com
 */

if ( !class_exists( 'prfw_Admin' ) ) {
  class prfw_Admin {

      /**
       * The ID of this plugin.
       *
       * @since    1.0.0
       * @access   private
       * @var      string    $plugin_name    The ID of this plugin.
       */
      private $plugin_name;

      /**
       * The version of this plugin.
       *
       * @since    1.0.0
       * @access   private
       * @var      string    $version    The current version of this plugin.
       */
      private $version;
      private $hook_suffixes = array();
      /**
       * Initialize the class and set its properties.
       *
       * @since    1.0.0
       * @param      string    $plugin_name       The name of this plugin.
       * @param      string    $version    The version of this plugin.
       */

      public $pageName = '';


      public function __construct( $plugin_name, $version ) {

          $this->plugin_name = $plugin_name;
          $this->version = $version;

          
          
      }

      /**
       * Register the stylesheets for the admin area.
       *
       * @since    1.0.0
       */
      public function enqueue_styles($hook = '') {
        //if( empty( $hook ) ) $hook = bp_core_do_network_admin() ? str_replace( '-network', '', get_current_screen()->id ) : get_current_screen()->id;

        if( in_array( $hook, $this->hook_suffixes ) ) {
          wp_enqueue_style('jquery-ui-css', PRFW_BASE_URL . 'admin/assets/lib/jqueryui/jquery-ui.min.css');
          wp_enqueue_style( 'finhelper', PRFW_BASE_URL . 'admin/assets/css/prfw_helper.css', array(), $this->version, 'all' );
          wp_enqueue_style( 'fincss', PRFW_BASE_URL . 'admin/assets/css/prfw.css', array(), $this->version, 'all' );
          wp_enqueue_style( 'toastr', PRFW_BASE_URL . 'admin/assets/css/toastr.min.css', array(), $this->version, 'all' );
        }

      }

      /**
       * Register the JavaScript for the admin area.
       *
       * @since    1.0.0
       */
      public function enqueue_scripts($hook = '') {
        //if( empty( $hook ) ) $hook = bp_core_do_network_admin() ? str_replace( '-network', '', get_current_screen()->id ) : get_current_screen()->id;
        $screen = get_current_screen();
        $pageName = str_replace('woocommerce_page_prfw_', '', $screen->id);
        $pageName = str_replace('woocommerce_page_', '', $pageName);
        $pageName = str_replace('toplevel_page_prfw_', '', $pageName);
        $this->pageName = str_replace('admin_page_prfw_', '', $pageName);

        if( in_array( $hook, $this->hook_suffixes ) ) {
          add_thickbox();
          wp_enqueue_script('jquery-ui-datepicker');
          wp_enqueue_script( 'prfwmain', PRFW_BASE_URL . 'admin/assets/js/main.js', array( 'jquery' ), $this->version, true );
          wp_enqueue_script( 'vue', PRFW_BASE_URL . 'admin/assets/js/vue.js', array( ), $this->version, false );
          wp_enqueue_script( 'vuepage', PRFW_BASE_URL . 'admin/assets/js/pages/'.$this->pageName.'.js', array( 'vue', 'prfwmain' ), $this->version, true );
          if(in_array($this->pageName, array('dashboard'))) {
            wp_enqueue_script( 'table2csv', PRFW_BASE_URL . 'admin/assets/js/jquery.tabletoCSV.js', array( 'jquery' ), $this->version, false );
          }
          wp_enqueue_script( 'toastr', PRFW_BASE_URL . 'admin/assets/js/toastr.min.js', array( 'jquery' ), $this->version, false );
          
          $symbol = '';
          if (function_exists('get_woocommerce_currency_symbol')) { $symbol = get_woocommerce_currency_symbol(); }  
          wp_localize_script('prfwmain', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'symbol'=>$symbol, 'prfw_url'=> PRFW_BASE_URL, 'nonce'=>wp_create_nonce('prfwpost')));
        }
      }

      /**
       * Build Admin Menu
       *
       * @since    1.0.0
       */
      public function buildmenu() {
        $this->hook_suffixes[] = add_submenu_page('woocommerce', __('Pending Payments'), __('Pending Payments'), 'view_woocommerce_reports', 'prfw_dashboard', array($this, 'pageDisplay'));
      }

      /**
       * Display requested page
       */
      public function pageDisplay() {

        

        $handlers = array(
          'dashboard'=>'handler',
        );

        $processes = array(
          'dashboard'=>'pageOrders',
        );

        if(current_user_can( 'view_woocommerce_reports' )) {
          if(isset($handlers[$this->pageName])) {
            $hc = $handlers[$this->pageName];
            require_once PRFW_PLUGIN_DIR . 'classes/'.$hc.'.class.php';
            $hn = 'prfw_'.$hc;
            $proc = null;
            if(isset($processes[$this->pageName])) {
              $proc = $processes[$this->pageName];
            }
            $handler = new $hn($proc);
          }
          include 'views/'.$this->pageName.'.php';
        } else {
          printf(
            '<div class="notice notice-error is-dismissible"><p><strong>%s</strong></p></div>',
            esc_html__( 'You are not allowed to display this page. Please contact administrator.', 'prfw' )
          );
        }
      }


  }
}
