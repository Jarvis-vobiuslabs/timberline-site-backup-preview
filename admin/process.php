<?PHP

require_once("authnet.class.php");

	$login = "83YbAm3E";
	$transkey = "8t5h2mE933x4BFfR";
	
	$pid=$_GET['pid'];
	$ppid=$_GET['ppid'];
	$amount=$_GET['amount'];
	$notes=$_GET['notes'];
	
	// CIM payment XML
	$content =
	"<?xml version=\"1.0\" encoding=\"utf-8\"?>".
"<createCustomerProfileTransactionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">".
"<merchantAuthentication>".
"<name>".$login."</name>".
"<transactionKey>".$transkey."</transactionKey>".
"</merchantAuthentication>".
"<transaction>".
"<profileTransAuthOnly>".
"<amount>".$amount."</amount>".
"<customerProfileId>".$pid."</customerProfileId>".
"<customerPaymentProfileId>".$ppid."</customerPaymentProfileId>".
"<order>".
"<description>".$notes."</description>".
"</order>".
"</profileTransAuthOnly>".
"</transaction>".
"</createCustomerProfileTransactionRequest>";
	
	$payment=new Authnet();
	
	//send the xml via curl
	$host = "api.authorize.net";
	$path = "/xml/v1/request.api";
	$response = $payment->send_request_via_curl($host,$path,$content);
	
		//if the connection and send worked $response holds the return from Authorize.net
		if ($response){				
		list ($resultCode, $code, $text, $result) = $payment->parse_return($response);
		$message=explode(',',$result);
		
			if($message[0]=='2'){
			header ('Location:index.php?response=declined&result='.$text.'&reason='.$result.'');
			} 
			else if($message[0]=='1'){
			header ('Location:index.php?response=approved&result='.$text.'&reason='.$result.'');
			} 
			else if($message[0]=='3'){
			header ('Location:index.php?response=error&result='.$text.'&reason='.$result.'');
			} 
			else if($message[0]=='4'){
			header ('Location:index.php?response=review&result='.$text.'&reason='.$result.'');
			} else {
			header ('Location:index.php?response=unknown&result='.$text.'&reason='.$result.'');
			}		
		} else {
		header ('Location:index.php?response=unknown&result='.$text.'&reason='.$result.'&amount='.$_GET['amount'].'');
		}
?>