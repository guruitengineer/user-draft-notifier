<?php if (!defined('ABSPATH')) die("Unauthorized Access"); ?>
<div id="message" class="danger">
<p><strong><?php 
 echo "<b>Note :</b> Please take backup of your posts for safety purpose only. <br>Dashboard->Tools->Export";
 ?></strong></p>
</div>

<?php if( isset($_REQUEST['notify_user_button']) ) { 

    $udn_draft_notification_days=get_option( 'udn_draft_notification_days');
    $udn_discard_draft_days=get_option( 'udn_discard_draft_days');

    global $wpdb;
    $post_table = $wpdb->prefix . "posts";
    $user_table=$wpdb->prefix . 'users';  
    $udn_table_name = $wpdb->prefix . 'user_draft_notifier';
    $udn_log_table=$wpdb->prefix . 'user_draft_notifier_logs';

    $subject="Draft Notification";
    $from_name=get_option('blogname');
    $from_email=get_option('admin_email');
    $mailheaders='';
    $mailheaders .= "Content-Type: text/html; charset=\"UTF-8\"\n";
    $mailheaders .= "From: $from_name <$from_email>" . "\r\n";
    
    $select_draft_to_discard = "SELECT ptb.ID,utb.user_email FROM " . $post_table." ptb INNER JOIN ".$user_table." utb ON ptb.post_author=utb.ID WHERE DATEDIFF(CURDATE(),post_date)>='$udn_discard_draft_days' AND (post_status='draft' OR post_status='auto-draft') AND post_type='post'";
    $rows = $wpdb->get_results($select_draft_to_discard);
    foreach ($rows as $row) {
      if(!($wpdb->delete( 
          $post_table, 
          array( 
            'ID'=>$row->ID
          ) 
        )) && !($wpdb->delete( 
          $udn_table_name, 
          array( 
            'post_id'=>$row->ID
          ) 
        )))
      {
         $text='Time : '.date("Y-m-d h:i:sa").' Post ID : '.$row->ID.' User :'. $row->user_email.' Unable to discard draft.';
         $wpdb->insert($udn_log_table, array( 'post_id'=>$row->ID,'action_time' => current_time( 'mysql' ),'action'=>'Unable to discard draft.'));
          ?>
            <div id="message" class="updated">
              <p><strong><?php _e($text) ?></strong></p>
            </div>
          <?php
      }
      else
      {

            $emailBody="Hi,<br>Your draft (Post ID : ".$row->ID.") older ".$udn_discard_draft_days." days from the date of draft creation have been discarded.";
            $emailBody=stripslashes(htmlspecialchars_decode($emailBody));
            $message=$emailBody;
            $returns=wp_mail($row->user_email, $subject, $message, $mailheaders); 
              if($returns)
              {
                $text='Time : '.date("Y-m-d h:i:sa").' Post ID : '.$row->ID.' User :'. $row->user_email.' Draft Discarded and User Notified.';
                $wpdb->insert($udn_log_table, array( 'post_id'=>$row->ID,'action_time' => current_time( 'mysql' ),'action'=>'Draft Discarded and User Notified.'));
                ?>
                  <div id="message" class="updated">
                    <p><strong><?php _e($text) ?></strong></p>
                  </div>
                <?php
              }
              else
              {
                $text='Time : '.date("Y-m-d h:i:sa").' Post ID : '.$row->ID.' User :'. $row->user_email.' Draft Discarded. But, unable to send email notification to user.';
                $wpdb->insert($udn_log_table, array( 'post_id'=>$row->ID,'action_time' => current_time( 'mysql' ),'action'=>'Draft Discarded. But, unable to send email notification to user.'));
                ?>
                  <div id="message" class="updated">
                    <p><strong><?php _e($text) ?></strong></p>
                  </div>
                <?php
              }
      }
    }

    $select_user_to_notify = "SELECT ptb.ID,utb.user_email FROM " . $post_table." ptb INNER JOIN ".$user_table." utb ON ptb.post_author=utb.ID WHERE DATEDIFF(CURDATE(),post_date)>='$udn_draft_notification_days' AND (post_status='draft' OR post_status='auto-draft') AND post_type='post'";
    $rows = $wpdb->get_results($select_user_to_notify);
    foreach ($rows as $row) { 
        $check_query="SELECT * FROM ".$udn_table_name." WHERE post_id='$row->ID'";
        $row_count=$wpdb->query($check_query);
        if($row_count!=1)
        {
            if(!($wpdb->insert( 
            $udn_table_name, 
            array( 
              'post_id'=>$row->ID,
              'notification_date' => current_time( 'mysql' )
            ) 
            )))
            {
              $text='Time : '.date("Y-m-d h:i:sa").' Post ID : '.$row->ID.' User :'. $row->user_email.' Unable to notify user. Please try again later.';
              $wpdb->insert($udn_log_table, array( 'post_id'=>$row->ID,'action_time' => current_time( 'mysql' ),'action'=>'Unable to notify user. Please try again later.'));
              ?>
                <div id="message" class="updated">
                  <p><strong><?php _e($text) ?></strong></p>
                </div>
              <?php
            }
            else
            {
              
            $emailBody="Hi,<br>You have saved a draft (Post ID : ".$row->ID.") before ".$udn_draft_notification_days." days. Please publish it immediately or discard it if not required. Otherwise the draft will be discarded after ".$udn_discard_draft_days." days from the date of draft creation.";
            $emailBody=stripslashes($emailBody);
            $emailBody=stripslashes(htmlspecialchars_decode($emailBody));
            $message=$emailBody;
            $returns=wp_mail($row->user_email, $subject, $message, $mailheaders); 
              if($returns)
              {
                $text='Time : '.date("Y-m-d h:i:sa").' Post ID : '.$row->ID.' User :'. $row->user_email.' User Notified.';
                $wpdb->insert($udn_log_table, array( 'post_id'=>$row->ID,'action_time' => current_time( 'mysql' ),'action'=>'User Notified.'));
                ?>
                  <div id="message" class="updated">
                    <p><strong><?php _e($text) ?></strong></p>
                  </div>
                <?php
              }
              else
              {
                $text='Time : '.date("Y-m-d h:i:sa").' Post ID : '.$row->ID.' User :'. $row->user_email.' Unable to send email notification to user.';
                $wpdb->insert($udn_log_table, array( 'post_id'=>$row->ID,'action_time' => current_time( 'mysql' ),'action'=>'Unable to send email notification to user.'));
                ?>
                  <div id="message" class="updated">
                    <p><strong><?php _e($text) ?></strong></p>
                  </div>
                <?php
              }
            }
        }
        else
        {
          $text='Time : '.date("Y-m-d h:i:sa").' Post ID : '.$row->ID.' User :'. $row->user_email.' User Already Notified.';
          $wpdb->insert($udn_log_table, array( 'post_id'=>$row->ID,'action_time' => current_time( 'mysql' ),'action'=>'User Already Notified.'));
                ?>
                  <div id="message" class="updated">
                    <p><strong><?php _e($text); ?></strong></p>
                  </div>
                <?php
        } 
        
    }
}

?>

<h1>Notify User</h1>
<form method="post" action="">
  <input type="submit" value="Notify Your Users" class="button button-primary" name="notify_user_button">
</form>
