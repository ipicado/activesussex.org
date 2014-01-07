<?php
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
$success = false;
$oid = isset($_GET['oid']) && $_GET['oid'] != '' ? $_GET['oid'] : '';
if($oid !='')
{
	$getData = print_r($_GET,true);
	mail('steve@trafficwebsites.co.uk', 'Active Sussex return data', $getData);
	
	if(strlen($oid) < 10) //RS Form
	{

		$query = "SELECT FieldValue FROM `jos_rsform_submission_values` WHERE `FieldName` = 'payment_status' AND `SubmissionId` = '$oid' LIMIT 1";
		$result = $db->query($query);
		if($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			$pay_status = $row['FieldValue'];		
			
			if($pay_status == 'PAID') $success = true;
		}
	}
	else
	{
		$query = "SELECT state FROM `jos_rseventspro_users` WHERE `verification` = '$oid' LIMIT 1";
		$result = $db->query($query);
		if($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			$state = $row['state'];		
			
			if($state == '1') $success = true;
		}
	}	
	
	if($success == true)
	{
		//success
		header("location: http://www.activesussex.org/payment-received");
		exit;
	}
	else
	{
		//fail
		//header("location: http://www.activesussex.org/payment-received");
		header("location: http://www.activesussex.org/payment-failed");
		exit;
	}
}
else
{
	//No order ID specified
	header("location: http://www.activesussex.org");
	exit;
}
/*
SELECT val.`SubmissionId` AS 'sub_id' FROM `jos_rsform_submissions` sub, `jos_rsform_submission_values` val
					WHERE sub.`SubmissionId` = val.`SubmissionId`
					AND val.`FieldName` = 'random_code'
					AND val.`FieldValue` = '8e78a9ee5cf5f2dc330a88583327671c';
					
					
					


SELECT FieldValue FROM `jos_rsform_submission_values` WHERE `FieldName` = 'payment_status' AND `SubmissionId` = '2195';


*/
?>