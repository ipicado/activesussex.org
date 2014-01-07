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

$epdqdata = $_POST['epdqdata'];
$returnurl = $_POST['returnurl'];		
$merchantdisplayname = $_POST['merchantdisplayname'];
$collectdeliveryaddress = $_POST['collectdeliveryaddress'];
$oid = $_POST['oid'];

$query = "SELECT submissionId FROM jos_rseventspro_users WHERE verification = '$oid'";
$result = $db->query($query);
$row = $result->fetch_assoc();
$subID = $row['submissionId'];

$query = "SELECT FieldName,FieldValue FROM jos_rsform_submission_values WHERE SubmissionId = '$subID'";
$result = $db->query($query);
while($row = $result->fetch_assoc())
{
	$FieldName = $row['FieldName'];
	$FieldValue = $row['FieldValue'];
	${$FieldName} = $FieldValue;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Redirecting...</title>
<script type="text/javascript" src="../templates/activesussex/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript">
$(function() {
	$("form").submit();
});
</script>
</head>
<body>
<form action="https://secure2.epdq.co.uk/cgi-bin/CcxBarclaysEpdq.e" method="POST">
	<input type="hidden" name="epdqdata" value="<?=$epdqdata?>">
	<input type="hidden" name="returnurl" value="<?=$returnurl?>">
	<input type="hidden" name="merchantdisplayname" value="<?=$merchantdisplayname?>">
	<input type="hidden" name="collectdeliveryaddress" value="<?=$collectdeliveryaddress?>">
	<input type="hidden" name="oid" value="<?=$oid?>">
	<input type="hidden" name="name" value="<?=$RSEProName?>" />
	<input type="hidden" name="email" value="<?=$RSEProEmail?>" />
	<input type="hidden" name="baddr1" value="<?=$addr1?>" />
	<input type="hidden" name="baddr2" value="<?=$addr2?>" />
	<input type="hidden" name="baddr3" value="<?=$addr3?>" />
	<input type="hidden" name="bcity" value="<?=$town?>" />
	<input type="hidden" name="bcountyprovince" value="<?=$county?>" />
	<input type="hidden" name="bpostalcode" value="<?=$postcode?>" />
	<input type="hidden" name="btelephonenumber" value="<?=$phone?>" />
	<!-- <input TYPE="submit" VALUE="purchase"> -->
</form>

</body>
</html>