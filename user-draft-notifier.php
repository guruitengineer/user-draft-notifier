<?php
/*
Plugin Name: User Draft Notifier
Plugin URI: https://wordpress.org/plugins/user-draft-notifier/
Description: With this plugin you can send email notification to those users who have saved a draft for more than defined days. You can also discard all user's draft which is saved before defined period.
Version: 1.0
Author: Gurmeet Singh
Author URI: http://guruitengineer.in/
License: GPLv2+
*/
if (!defined('ABSPATH')) die("Unauthorized Access");

class User_Draft_Notifier{

  
  function __construct() {
    add_action('admin_menu', array( $this, 'udn_add_menu' ));
    register_activation_hook( __FILE__, array( $this, 'udn_install' ) );
    register_deactivation_hook( __FILE__, array( $this, 'udn_uninstall' ) ); 
  }

  /*
  * Actions perform at loading of admin menu
  */
  function udn_add_menu() {

    add_menu_page( 'Notify User', 'User Draft Notifier', 'manage_options', 'udn_notify_page', 
      array(__CLASS__,'udn_notify_page'));
    add_submenu_page( 'udn_notify_page', 'Notify User', 'Notify User', 'manage_options', 'udn_notify_page', 
      array(__CLASS__,'udn_notify_page'));
    add_submenu_page( 'udn_notify_page', 'Settings', 'Settings', 'manage_options', 'udn_setting_page', 
      array(__CLASS__,'udn_setting_page'));
    add_submenu_page( 'udn_notify_page', 'Logs', 'Logs', 'manage_options', 'udn_log_page', 
      array(__CLASS__,'udn_log_page'));
    add_action( 'admin_init', array($this,'udn_register_settings' ));
  }
  /*
  * Actions perform on loading of Notify User submenu page
  */
  function udn_notify_page()
  {
    require_once("includes/udn-notify.php");
  }
  /*
  * Actions perform on loading of Settings submenu page
  */
  function udn_setting_page()
  {
    require_once("includes/udn-custom-settings.php");
  }
  /*
  * Actions perform on loading of Logs submenu page
  */
  function udn_log_page()
  {
    require_once("includes/udn-logs.php");
  }
  
  /*
  * Register the settings
  */
  function udn_register_settings() {
      register_setting(
      'udn_options',  // settings section
      'udn_draft_notification_days' // setting name
    );
      register_setting(
      'udn_options',  // settings section
      'udn_discard_draft_days' // setting name
    );
  }

  /*
  * Actions perform on activation of plugin
  */
  function udn_install() {

    global $wpdb;
    $table_name = $wpdb->prefix . "user_draft_notifier";
    $log_table_name = $wpdb->prefix . "user_draft_notifier_logs";
    $charset_collate = $wpdb->get_charset_collate();
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $sql = "CREATE TABLE " . $table_name . " (
            post_id BIGINT(20) NOT NULL, 
            notification_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
            UNIQUE KEY  (post_id),
            FOREIGN KEY (post_id) REFERENCES ".$wpdb->prefix."posts(ID)
        ) ". $charset_collate .";";

    $log = "CREATE TABLE " . $log_table_name . " (
            post_id BIGINT(20) NOT NULL, 
            action_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
            action VARCHAR(100) NOT NULL,
            FOREIGN KEY (post_id) REFERENCES ".$wpdb->prefix."posts(ID)
        ) ". $charset_collate .";";
        dbDelta($sql);
        dbDelta($log);
  }
  
  /*
  * Actions perform on de-activation of plugin
  */
  function udn_uninstall() {
  }
}
new User_Draft_Notifier();
?>