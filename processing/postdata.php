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
		if(strlen($oid) < 10) //RS Form
		{
			if(strtolower($_POST['transactionstatus'])=="success")
			{
				$query = "UPDATE `jos_rsform_submission_values` SET `FieldValue` = 'PAID' WHERE `FieldName` = 'payment_status' AND `SubmissionId` = '$oid'";
				$db->query($query);
			}
			else
			{
				$query = "UPDATE `jos_rsform_submission_values` SET `FieldValue` = 'FAILED' WHERE `FieldName` = 'payment_status' AND `SubmissionId` = '$oid'";
				$db->query($query);
			}
		}
		else //RS Event
		{
			if(strtolower($_POST['transactionstatus'])=="success")
			{
				$query = "UPDATE jos_rseventspro_users SET `state` = 1 WHERE verification = '$oid'";
				$db->query($query);
	
			}
			else
			{
				$query = "UPDATE jos_rseventspro_users SET `state` = 2 WHERE verification = '$oid'";
				$db->query($query);
			}
		}
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