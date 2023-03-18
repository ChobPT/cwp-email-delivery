<?php

$log_file = '/var/log/maillog';
$domain_to_track = $_SESSION['general']["domain"];;


$email_data = [];

// Read log file content using shell_exec
$log_content = shell_exec("cat $log_file");
if ($log_content === null) {
    echo "Error: Unable to read log file.";
    exit(1);
}


// Process log file line by line
$lines = explode("\n", $log_content);
$lines=array_reverse($lines);
foreach ($lines as $line) {
    if (strpos($line, $domain_to_track) === false) {
		preg_match('/([A-Fa-f0-9]{10,12}):/', $line, $queue_id_matches);
		if (!empty($queue_id_matches)){
			if(empty($email_data[$queue_id_matches[1]])) {
				continue;
			}
		}
        
    }
	$changes=0;

    // Extract the email queue ID
    preg_match('/([A-Fa-f0-9]{10,12}):/', $line, $queue_id_matches);
    if (!empty($queue_id_matches)) {
        $queue_id = $queue_id_matches[1];
    } else {
        continue;
    }

    // Extract the email status
    preg_match('/status=(\w+)/', $line, $status_matches);
    if (!empty($status_matches)) {
        $status = $status_matches[1];
		$changes++;
    }

    // Extract the recipient email address
    preg_match('/to=<([^>]+)>/', $line, $recipient_matches);
    if (!empty($recipient_matches)) {
        $recipient = $recipient_matches[1];
		$changes++;
    }
	
	preg_match('/from=<([^>]+)>/', $line, $sender_matches);
	if (!empty($sender_matches)) {
            $sender = $sender_matches[1];
			$changes++;
    }else{
		//$sender=$line;
	}
	if($changes==0){
		continue;
	}
    // Extract the timestamp
    preg_match('/^\w{3}\s+\d{1,2}\s+\d{2}:\d{2}:\d{2}/', $line, $timestamp_matches);
    if (!empty($timestamp_matches)) {
        $timestamp = $timestamp_matches[0];
    }

    if (isset($status) && isset($recipient) ) {
        $email_data[$queue_id] = [
            'timestamp' => $timestamp,
            'recipient' => $recipient,
			'sender'	=> $sender,
            'status' => $status
        ];
    }
}
ob_start();
echo "<table class='dataTable table table-responsive table-bordered'>";
echo "<thead><tr><th>Queue ID</th><th>Timestamp</th><th>Sender</th><th>Recipient</th><th>Status</th></tr></thead><tbody>";
foreach ($email_data as $queue_id => $email) {
    //foreach ($emails as $email) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($queue_id) . "</td>";
        echo "<td>" . htmlspecialchars($email['timestamp']) . "</td>";
        echo "<td>" . htmlspecialchars($email['sender']) . "</td>";
		echo "<td>" . htmlspecialchars($email['recipient']) . "</td>";
        echo "<td>" . htmlspecialchars($email['status']) . "</td>";
        echo "</tr>";
    //}
}
echo "</tbody></table>";
$mod["content"]=ob_get_clean();
?>
