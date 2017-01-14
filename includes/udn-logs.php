<?php 
if (!defined('ABSPATH')) die("Unauthorized Access");
global $wpdb;
$udn_log_table=$wpdb->prefix . 'user_draft_notifier_logs';
$post_table = $wpdb->prefix . "posts";
$user_table=$wpdb->prefix . 'users'; 

if( isset($_REQUEST['clear_log_button']) ) {
    $sql = "TRUNCATE TABLE ".$udn_log_table;
    $wpdb->query($sql);
}
?>

<table width=100% border=2>
	<caption>USER DRAFT NOTIFIER LOGS</caption>
	<tr>
		<th>
			Time
		</th>
		<th>
			Post ID
		</th>
		<th>
			User Email
		</th>
		<th>
			Action
		</th>
		<th>
			Post Date
		</th>
	</tr>
<?php

$select_log="SELECT * FROM ".$udn_log_table." ltb INNER JOIN ".$post_table." ptb ON ptb.ID=ltb.post_id INNER JOIN ".$user_table." utb ON utb.ID=ptb.post_author ORDER BY action_time DESC";
$rows = $wpdb->get_results($select_log);
foreach ($rows as $row) {
	echo "<tr><td>".$row->action_time."</td><td>".$row->post_id."</td><td>".$row->user_email."</td><td>".$row->action."</td><td>".$row->post_date."</td></tr>";
}
?>
</table>
<br>
<form method="post" action="">
  <input type="submit" class="button button-primary" value="Clear Logs" name="clear_log_button">
</form>