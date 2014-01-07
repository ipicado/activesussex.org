<?php
include_once("../../configuration.php");
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

if(isset($_POST['form_type']))
{
	switch($_POST['form_type'])
	{
		case "rsform":
		
			$code = $_POST['random_code'];
			
			//Get submission ID from code
			
			$query = "SELECT sub.`SubmissionId` as 'sub_id' FROM `jos_rsform_submissions` sub, `jos_rsform_submission_values` val
						WHERE sub.`SubmissionId` = val.`SubmissionId`
						AND val.`FieldName` = 'random_code'
						AND val.`FieldValue` = '$code'";
			$result = $db->query($query);
			if($result->num_rows > 0)
			{
				$row = $result->fetch_assoc();
				$oid = $row['sub_id'];		
			
				$payment_method = $_POST['payment_method'];
				$total_cost = $_POST['total_cost'];
				$name = $_POST['applicant_name'];
				$email = $_POST['email'];
				$tel = isset($_POST['phone']) ? $_POST['phone'] : '';
				$addr1 	  = isset($_POST['addr1'])? $_POST['addr1']:'';
				$addr2	  = isset($_POST['addr2'])? $_POST['addr2']:'';
				$addr3 	  = isset($_POST['addr3'])? $_POST['addr3']:'';
				$town	  = isset($_POST['town'])? $_POST['town']:'';
				$county	  = isset($_POST['county'])? $_POST['county']:'';	
				$postcode = isset($_POST['postcode'])? $_POST['postcode']:'';  		
				
				$qs = "?oid=$oid&total=$total_cost&name=$name&email=$email";
				$qs.= $tel != '' ? "&tel=".$tel : '';
				$qs.= $addr1 	!= '' ? "&addr1=".$addr1 : '';
				$qs.= $addr2	!= '' ? "&addr2=".$addr2 : '';
				$qs.= $addr3 	!= '' ? "&addr3=".$addr3 : '';
				$qs.= $town	  	!= '' ? "&town=".$town : '';
				$qs.= $county	!= '' ? "&county=".$county : '';
				$qs.= $postcode != '' ? "&postcode=".$postcode : ''; 
	
				
				if($payment_method == 'online')
				{
					header("location: http://www.activesussex.org/processing/ssa/barclays_cpi.php".$qs);
					exit;
				}
				else
				{
					header("location: http://www.activesussex.org/submission-received");
					exit;
				}
			}
			else
			{
				die("There seems to be a problem. Please go back and try again.");
			}
			
		break;
	}
}