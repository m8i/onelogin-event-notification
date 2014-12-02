<?php    

	// Settings
	$api_key = 'ENTER-API-KEY-HERE';
	$to_email = "ENTER@EMAIL.HERE"; //May use comma separated email addresses if more than one recipient
	$from_email = "ENTER@EMAIL.HERE";


    $url = "https://app.onelogin.com/api/v1/events";
    $ch = curl_init();
	
	$hourago=time()-3600;
	
	
    curl_setopt($ch, CURLOPT_USERPWD, $api_key);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

    $http_result = curl_exec($ch);
    $error       = curl_error($ch);
    $http_code   = curl_getinfo($ch ,CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($error) {
      print $error;
    } else {
        $http_result=simplexml_load_string($http_result);
		$i_echoed=0;
		foreach ($http_result->event as $event) {
			$notes=(string)$event->notes;
			$date=strtotime($event->{'created-at'});
			if (strlen($notes) > 10 && $date >= $hourago && strlen($event->{'actor-system'}) > 5 ) {
				$output .= $event->{'actor-system'} . " at ". date("g:i a",$date).": " . $notes."\n\n";
				$i_echoed=1;
			}
		}
		
		if ($i_echoed==1) {
			$headers = 'From: ' . $from_email . "\r\n" .
		    'Reply-To: ' . $from_email . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();
			
			$message="The following events have been triggered in OneLogin. Please investigate.\n\n".$output;
			
			mail ($to_email,"OneLogin Event Notification",$message,$headers);
		}
		
    }

?>