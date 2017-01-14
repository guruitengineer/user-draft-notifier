<?php 
if (!defined('ABSPATH')) die("Unauthorized Access");
if( isset($_GET['settings-updated']) ) { ?>
<div id="message" class="updated">
  <p><strong><?php _e('Settings Saved.') ?></strong></p>
</div>
<?php }

if( isset($_REQUEST['udn_clear_button']) ) 
{
    global $wpdb;
    $table_name = $wpdb->prefix . "user_draft_notifier";
    $log_table_name = $wpdb->prefix . "user_draft_notifier_logs";
    $sql = "DROP TABLE " . $table_name;
    $log = "DROP TABLE " . $log_table_name;
    $wpdb->query($sql);
    $wpdb->query($log);
    ?>
    <div id="message" class="updated">
      <p><strong><?php _e('Everything has been cleared.') ?></strong></p>
    </div>
    <?php
}

?>

<h1>User Draft Notifier Settings</h1>
<form method="post" action=<?php echo admin_url().'options.php';?>>
  <?php settings_fields( 'udn_options' ); ?>
  <?php do_settings_sections( 'udn_options' ); ?>
  <table class="form-table">
    <tr>
      <th scope="row">You want to send draft notification after </th>
      <td>
        <?php $udn_draft_notification_days=get_option( 'udn_draft_notification_days'); ?>
        <input type="number" name="udn_draft_notification_days" value=
        <?php
          if($udn_draft_notification_days) echo $udn_draft_notification_days;
          else echo '7';
        ?>
        > Days.
      </td>
    </tr>
    <tr>
      <th scope="row">You want to discard draft after </th>
      <td>
        <?php $udn_discard_draft_days=get_option( 'udn_discard_draft_days'); ?>
        <input type="number" name="udn_discard_draft_days" value=
        <?php
          if($udn_discard_draft_days) echo $udn_discard_draft_days;
          else echo '15';
        ?>
        > Days.
      </td>
    </tr>
  </table>
  <?php 
  submit_button(); ?>
</form>
<h1>Clear Everything</h1>
<form method="post" action=''>
  <table class="form-table">
    <tr>
      <td>
        <input type="submit" class="button button-warning" value="Clear" name="udn_clear_button">
      </td>
    </tr>
  </table>
  <b>Note :<b> <i>This option will drop all the UDN tables from the database and you need to re-install or re-activate this plugin to use.</i>
</form>