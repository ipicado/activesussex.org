<?php
$login = 'trafficdev';
$pass = 'Doges11KnuR';

if(($_SERVER['PHP_AUTH_PW']!= $pass || $_SERVER['PHP_AUTH_USER'] != $login)|| !$_SERVER['PHP_AUTH_USER'])
{
    header('WWW-Authenticate: Basic realm="Test auth"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Auth failed';
    exit;
}
else
{
	include_once("../configuration.php");
	$config = new JConfig;
	$db = new mysqli($config->host,$config->user,$config->password,$config->db);
	/* check connection */
	if (mysqli_connect_error()) {
	    die('Connect Error (' . mysqli_connect_errno() . ') '
	            . mysqli_connect_error());
	}
	/* change character set to utf8 */
	if (!$db->set_charset("utf8")) {
	    printf("Error loading character set utf8: %s\n", $db->error);
	}

	$oid = isset($_POST['oid']) && $_POST['oid'] != '' ? $_POST['oid'] : '';
	if($oid !='')
	{
		
/*
		$query = "SELECT val.`SubmissionId` as 'sub_id' FROM `jos_rsform_submissions` sub, `jos_rsform_submission_values` val
					WHERE sub.`SubmissionId` = val.`SubmissionId`
					AND val.`FieldName` = 'random_code'
					AND val.`FieldValue` = '$oid'";
		$result = $db->query($query);
		if($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			$sub_id = $row['sub_id'];		
*/
		
			if(strtolower($_POST['transactionstatus'])=="success")
			{
				$query = "UPDATE `jos_rsform_submission_values` SET `FieldValue` = 'PAID' WHERE `FieldName` = 'payment_status' AND `SubmissionId` = '$oid'";
				$db->query($query);

				/*
				$query = "UPDATE `jos_ohanah_registrations` SET `paid` = 1 WHERE `ohanah_registration_id` = '$oid'";
				$db->query($query);
				
				$query = "SELECT * FROM jos_ohanah_registrations r ,jos_ohanah_events e WHERE r.ohanah_registration_id = '$oid' AND r.ohanah_event_id = e.ohanah_event_id";
				$result = $db->query($query);
				*/
			}
			else
			{
				$query = "UPDATE `jos_rsform_submission_values` SET `FieldValue` = 'FAILED' WHERE `FieldName` = 'payment_status' AND `SubmissionId` = '$oid'";
				$db->query($query);
				/*
				$query = "DELETE FROM `jos_ohanah_registrations` WHERE `ohanah_registration_id` = '$oid'";
				$db->query($query);
				*/
			}
		/* } */
	}

	
	$postData = print_r($_POST,true);
	mail('steve@trafficwebsites.co.uk', 'Active Sussex post data', $postData);
/*
   $_POST['transactionstatus'] => DECLINED
   $_POST['total'] => 20.00
   $_POST['clientid'] => 71744
   $_POST['oid'] => 16
   $_POST['datetime'] => Oct 08 2012 17:19:24
   $_POST['chargetype'] => Auth
   $_POST['ecistatus'] => 1
   $_POST['cardprefix'] => 4

	*/
}
?>