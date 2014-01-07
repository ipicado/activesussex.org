<?php

#the following function performs a HTTP Post and returns the whole response
function pullpage( $host, $usepath, $postdata = "" ) {
 
# open socket to filehandle(epdq encryption cgi)
 $fp = fsockopen( $host, 80, $errno, $errstr, 60 );

#check that the socket has been opened successfully
 if( !$fp ) {
    print "$errstr ($errno)<br>\n";
 }
 else {

    #write the data to the encryption cgi
    fputs( $fp, "POST $usepath HTTP/1.0\n");
    $strlength = strlen( $postdata );
    fputs( $fp, "Content-type: application/x-www-form-urlencoded\n" );
    fputs( $fp, "Content-length: ".$strlength."\n\n" );
    fputs( $fp, $postdata."\n\n" );

    #clear the response data
   $output = "";
 
 
    #read the response from the remote cgi 
    #while content exists, keep retrieving document in 1K chunks
    while( !feof( $fp ) ) {
        $output .= fgets( $fp, 1024);
    }

    #close the socket connection
    fclose( $fp);
 }

#return the response
 return $output;
}

#define the remote cgi in readiness to call pullpage function 
$server="secure2.epdq.co.uk";
$url="/cgi-bin/CcxBarclaysEpdqEncTool.e";

#the following parameters have been obtained earlier in the merchant's webstore
#clientid, passphrase, oid, currencycode, total
$oid = isset($_GET['oid'])?$_GET['oid']:0;
$total = isset($_GET['total'])?$_GET['total']:0;
//$return = isset($_GET['return'])? urldecode($_GET['return']):'';
$return = "http://www.activesussex.org/processing/return.php";
$name = isset($_GET['name'])? $_GET['name']:'';
$email = isset($_GET['email'])? $_GET['email']:'';
$addr1 	  = isset($_GET['addr1'])? $_GET['addr1']:'';
$addr2	  = isset($_GET['addr2'])? $_GET['addr2']:'';
$addr3 	  = isset($_GET['addr3'])? $_GET['addr3']:'';
$town	  = isset($_GET['town'])? $_GET['town']:'';
$county	  = isset($_GET['county'])? $_GET['county']:'';	
$postcode = isset($_GET['postcode'])? $_GET['postcode']:'';  		
$tel	  = isset($_GET['tel'])? $_GET['tel']:'';

$params="clientid=71744";
$params.="&password=Fid65spEW80";
$params.="&oid=$oid";
$params.="&chargetype=Auth";
$params.="&currencycode=826";
$params.="&total=$total";
/*
$params.="&name=$name";
$params.="&email=$email";
$params.="&baddr1=$addr1";
$params.="&baddr2=$addr2";
$params.="&baddr3=$addr3";
$params.="&bcity=$town";
$params.="&bcountyprovince=$county";
//$params.="&bcountry=$addr1";
$params.="&bpostalcode=$postcode";
//$params.="&bstate=$addr1";
$params.="&btelephonenumber=$tel";
$params.="&saddr1=$addr1";
$params.="&saddr2=$addr2";
$params.="&saddr3=$addr3";
$params.="&scity=$town";
$params.="&scountyprovince=$county";
//$params.="&scountry=$addr1";
$params.="&spostalcode=$postcode";
//$params.="&sstate=$addr1";
$params.="&stelephonenumber=$tel";
*/
//echo htmlentities($params);
#perform the HTTP Post
$response = pullpage( $server,$url,$params );
   
#split the response into separate lines
$response_lines=explode("\n",$response);

#for each line in the response check for the presence of the string 'epdqdata'
#this line contains the encrypted string
$response_line_count=count($response_lines);
for ($i=0;$i<$response_line_count;$i++){
    if (preg_match('/epdqdata/',$response_lines[$i])){
        $strEPDQ=$response_lines[$i];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Redirecting...</title>
<script type="text/javascript" src="../../templates/activesussex/js/jquery-1.6.2.min.js"></script>
<script type="text/javascript">
$(function() {
	$("form").submit();
});
</script>
</head>
<body>

<form action="http://www.activesussex.org/processing/barclays_cpi.php" method="POST">
	<?php print "$strEPDQ"; ?>
	<input type="hidden" name="returnurl" value="<?=$return?>">
	<input type="hidden" name="merchantdisplayname" value="Active Sussex">
	<input type="hidden" name="collectdeliveryaddress" value="0">
	<input type="hidden" name="name" value="<?=$name?>">
	<input type="hidden" name="email" value="<?=$email?>">
	<input type="hidden" name="baddr1" value="<?=$addr1?>">
	<input type="hidden" name="baddr2" value="<?=$addr2?>">
	<input type="hidden" name="baddr3" value="<?=$addr3?>">
	<input type="hidden" name="bcity" value="<?=$town?>">
	<input type="hidden" name="bcountyprovince" value="<?=$county?>">
	<input type="hidden" name="bpostalcode" value="<?=$postcode?>">
	<input type="hidden" name="btelephonenumber" value="<?=$tel?>">
	<input type="hidden" name="oid" value="ssa-<?=$oid?>">
	<!-- <input TYPE="submit" VALUE="purchase"> -->
</form>

</body>
</html>