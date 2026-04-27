<?php
session_start();

if($_SERVER["SERVER_PORT"] != "443") { // A secure connection is required for payment processing 
	header("Location: https://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]); 
	exit(); 
}

// IE has a habit of caching pages that have the same URL, even if the content between the pages differs
header("Cache-Control: no-cache");  

require_once("authnet.class.php");
require_once("PHPMailer.class.php");

function submit_payment($redirect) {
	// Lets work with the form information
	global $_POST;
	
	// Define variables from POST
	// Store them in SESSION for form autopopulation
	$firstName 			= $_SESSION["firstName"]		= $_POST["firstName"];
	$lastName 			= $_SESSION["lastName"]			= $_POST["lastName"];
	$emailAddress 		= $_SESSION["email"]			= $_POST["email"];
	//$companyName		= $_SESSION["company"]			= $_POST["company"];
	$phoneNumber 		= $_SESSION["phone"]			= $_POST["phone"];
	//$faxNumber		= $_SESSION["fax"]				= $_POST["fax"];
	$streetAddress 		= $_SESSION["address"]			= $_POST["address"];
	$theCity 			= $_SESSION["city"]				= $_POST["city"];
	$theState 			= $_SESSION["state"]			= $_POST["state"];
	$zipCode 			= $_SESSION["zip"]				= $_POST["zip"];
	//$country			= $_SESSION["country"]			= $_POST["country"];
	$creditCard 		= $_SESSION["creditCardNumber"]	= $_POST["creditCardNumber"];
	$expirationMonth 	= $_SESSION["expDateMonth"]		= $_POST["expDateMonth"];
	$expirationYear		= $_SESSION["expDateYear"]		= $_POST["expDateYear"];
	$cvv2		 		= $_SESSION["cvv2Number"]		= $_POST["cvv2Number"];
	$totalAmount 		= $_SESSION["ls-price"]			= $_POST["ls-price"];
	//$ipAddress		= $_SESSION["ipaddress"]		= $_POST["ipaddress"];
	$notes				= $_SESSION["notes"]			= $_POST["notes"];
	$creditCardName		= $_SESSION["creditCardName"]	= $_POST["creditCardName"];
	$cc_address			= $_SESSION["cc_address"]		= $_POST["cc_address"];
	$cc_city			= $_SESSION["cc_city"]			= $_POST["cc_city"];
	$cc_state			= $_SESSION["cc_state"]			= $_POST["cc_state"];
	$cc_zip				= $_SESSION["cc_zip"]			= $_POST["cc_zip"];
	$creditCardType		= $_SESSION["creditCardType"]	= $_POST["creditCardType"];
	$promoOffer			= $_SESSION["promo-offer"]		= $_POST["promo-offer"];
	$lcbFrequency		= $_SESSION["lcb-frequency"]	= $_POST["lcb-frequency"];
	$lcbLotsize			= $_SESSION["lcb-lotsize"]		= $_POST["lcb-lotsize"];
	$lcbEstimate		= $_SESSION["lcb-estimate"]		= $_POST["lcb-estimate"];
	$calendar			= $_SESSION["calendar"]			= $_POST["calendar"];
	$lsEstimate			= $_SESSION["ls-estimate"]		= $_POST["ls-estimate"];
	$lsPrice			= $_SESSION["ls-price"]			= $_POST["ls-price"];
	$referral			= $_SESSION["referral"]			= $_POST["referral"];
	$agreement			= $_SESSION["agreement"]		= $_POST["agreement"];
	$serviceable		= $_SESSION["serviceable"]		= $_POST["serviceable"];
	$service			= $_SESSION["service"]			= $_POST["service"];
	
	// TODO: Sanitize user-submitted information
	//
	//
	
	$totalAmount = str_replace("$","",$totalAmount);
	$fullName = $firstName." ".$lastName;
	$expirationDate = $expirationMonth.$expirationYear;
	$expirationDate2 = $expirationYear."-".$expirationMonth;
	
	// Construct the class. Passing "true" as a parameter enables test mode, default is false
	$payment = new Authnet(); 

	// Set the authnet parameters
	$payment->setParameter("x_first_name", $firstName);
	$payment->setParameter("x_last_name", $lastName);
	$payment->setParameter("x_email", $emailAddress);
	//$payment->setParameter("x_company", $companyName);
	$payment->setParameter("x_phone", $phoneNumber);
	//$payment->setParameter("x_fax", $faxNumber);
	$payment->setParameter("x_address", $cc_address); 
	$payment->setParameter("x_city", $cc_city);
	$payment->setParameter("x_state", $cc_state);
	$payment->setParameter("x_zip", $cc_zip); 
	//$payment->setParameter("x_country", $country);
	//$payment->setParameter("x_customer_ip", $ipAddress);

	// Set the transaction parameters
	$payment->setTransaction($creditCard, $expirationDate, $totalAmount, $cvv2);
	$payment->setTransactionType("AUTH_ONLY");

	// Process the payment and continue based on the response (approved, declined, error)
	$payment->process(); 
	
	if ($payment->isApproved()) { 
	
	
	
		// Payment successful. 
		 $login = "83YbAm3E"; // The login is used to login into the merchant's control panel as well as identifying the merchant to Authorize.Net's API. 
	$transkey = "8t5h2mE933x4BFfR";
	
	$cc_name=explode(" ",$creditCardName);
	
		// Create new CIM profile
	$content =
	"<?xml version=\"1.0\" encoding=\"utf-8\"?>".
	"<createCustomerProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">".
	"<merchantAuthentication>".
	"<name>".$login."</name>".
	"<transactionKey>".$transkey."</transactionKey>".
	"</merchantAuthentication>".
	"<profile>".	
	"<description>Account For ".$firstName." ".$lastName."</description>".
	"<email>".$emailAddress ."</email>".
	"<paymentProfiles>".
	"<customerType>individual</customerType>".
	"<billTo>".
	"<firstName>".$cc_name[0]."</firstName>".
	"<lastName>".$cc_name[1]."</lastName>".
	"<address>".$cc_address."</address>".
	"<city>".$cc_city."</city>".
	"<state>".$cc_state."</state>".
	"<zip>".$cc_zip."</zip>".
	"<phoneNumber>".$phoneNumber."</phoneNumber>".
	"</billTo>".
	"<payment>".
	"<creditCard>".
	"<cardNumber>".$creditCard."</cardNumber>".
	"<expirationDate>".$expirationDate2."</expirationDate>".
	"<cardCode>".$cvv2."</cardCode>".
	"</creditCard>".
	"</payment>".	
	"</paymentProfiles>".	
	"</profile>".
	"<validationMode>liveMode</validationMode>".
	"</createCustomerProfileRequest>";
	
	//send the xml via curl
	$host = "api.authorize.net";
$path = "/xml/v1/request.api";
	$response = $payment->send_request_via_curl($host,$path,$content);
	
		//if the connection and send worked $response holds the return from Authorize.net
		if ($response){
		list ($resultCode, $code, $text, $customerProfileId, $customerPayProfileId2) = $payment->parse_return($response);
			
			$dbconn=mysql_connect ("localhost", "timberli_timberl","Y@V84*6va6w") or die('Cannot connect to the database because: ' . mysql_error());
$db_selected = mysql_select_db("timberli_timberline", $dbconn);

$clientcq="SELECT * FROM clients WHERE clientphone='$phoneNumber'";
$clientc=mysql_query($clientcq,$dbconn);
$totalRows_clientc=mysql_num_rows($clientc);

if($totalRows_clientc=='0'){
$clientq="INSERT INTO clients (clientfirst, clientlast, clientphone, pid, ppid) VALUES ('$firstName','$lastName','$phoneNumber','$customerProfileId','$customerPayProfileId2')";
$client=mysql_query($clientq,$dbconn);

			
		} 
}	
		// Possible things to do now: setup ARB, add to DB, send emails, etc
			$transID = $payment->getTransactionID();
			mailOrder($totalAmount, $transID);		
		
			header("Location: $redirect?response=success&transaction=$transID&resultcode=$code&ppid=$customerPayProfileId2");
	} 
	else if ($payment->isDeclined()) { 
		// Payment declined. 
			$reason = "(".$payment->getResponseReasonCode().")".$payment->getResponseText();
			header("Location: $redirect?response=declined&reason=$reason");
	} 
	else { 
		// An error occurred. 
		// Not approved nor declined; Reference $reason for more information.
			// return $payment->viewResults();
			$reason = "(".$payment->getResponseReasonCode().")".$payment->getResponseText();
			header("Location: $redirect?response=error&reason=$reason");
	}
}

function setup_ARB($redirect, $subscriptionName="Subscription", $frequency="monthly", $until="9999", $verify=false, $start_date=false) {
	// Lets work with the form information
	global $_POST;
	
	// Define variables from POST
	// Store them in SESSION for form autopopulation
	$firstName 			= $_SESSION["firstName"]		= $_POST["firstName"];
	$lastName 			= $_SESSION["lastName"]			= $_POST["lastName"];
	$emailAddress 		= $_SESSION["email"]			= $_POST["email"];
	//$companyName		= $_SESSION["company"]			= $_POST["company"];
	$phoneNumber 		= $_SESSION["phone"]			= $_POST["phone"];
	//$faxNumber		= $_SESSION["fax"]				= $_POST["fax"];
	$streetAddress 		= $_SESSION["address"]			= $_POST["address"];
	$theCity 			= $_SESSION["city"]				= $_POST["city"];
	$theState 			= $_SESSION["state"]			= $_POST["state"];
	$zipCode 			= $_SESSION["zip"]				= $_POST["zip"];
	//$country			= $_SESSION["country"]			= $_POST["country"];
	$creditCard 		= $_SESSION["creditCardNumber"]	= $_POST["creditCardNumber"];
	$expirationMonth 	= $_SESSION["expDateMonth"]		= $_POST["expDateMonth"];
	$expirationYear		= $_SESSION["expDateYear"]		= $_POST["expDateYear"];
	$expirationDate2 = $expirationYear."-".$expirationMonth;
	$cvv2		 		= $_SESSION["cvv2Number"]		= $_POST["cvv2Number"];
	$totalAmount 		= $_SESSION["lcb-price"]		= $_POST["lcb-price"];
	//$ipAddress		= $_SESSION["ipaddress"]		= $_POST["ipaddress"];
	$lcb_lotsize 		= $_SESSION['lcb-lotsize'] 		= $_POST['lcb-lotsize'];
	$notes				= $_SESSION["notes"]			= $_POST["notes"];
	$creditCardName		= $_SESSION["creditCardName"]	= $_POST["creditCardName"];
	$cc_address			= $_SESSION["cc_address"]		= $_POST["cc_address"];
	$cc_city			= $_SESSION["cc_city"]			= $_POST["cc_city"];
	$cc_state			= $_SESSION["cc_state"]			= $_POST["cc_state"];
	$cc_zip				= $_SESSION["cc_zip"]			= $_POST["cc_zip"];
	$creditCardType		= $_SESSION["creditCardType"]	= $_POST["creditCardType"];
	$promoOffer			= $_SESSION["promo-offer"]		= $_POST["promo-offer"];
	$lcbFrequency		= $_SESSION["lcb-frequency"]	= $_POST["lcb-frequency"];
	$lcbLotsize			= $_SESSION["lcb-lotsize"]		= $_POST["lcb-lotsize"];
	$lcbPrice			= $_SESSION["lcb-price"]		= $_POST["lcb-price"];
	$lcbEstimate		= $_SESSION["lcb-estimate"]		= $_POST["lcb-estimate"];
	$calendar			= $_SESSION["calendar"]			= $_POST["calendar"];
	$lsEstimate			= $_SESSION["ls-estimate"]		= $_POST["ls-estimate"];
	$lsPrice			= $_SESSION["ls-price"]			= $_POST["ls-price"];
	$referral			= $_SESSION["referral"]			= $_POST["referral"];
	$agreement			= $_SESSION["agreement"]		= $_POST["agreement"];
	$serviceable		= $_SESSION["serviceable"]		= $_POST["serviceable"];
	$service			= $_SESSION["service"]			= $_POST["service"];
	
	// TODO: Sanitize user-submitted information
	//
	//
	
	$totalAmount = str_replace("$","",$totalAmount);
	$fullName = $firstName." ".$lastName;
	$expirationDate = $expirationMonth.$expirationYear;
	
	// Verify funds are available on the credit card
	if($verify) {
		// Construct the AIM class. Passing "true" as a parameter enables test mode, default is false
		$verify = new Authnet(); 
	
		// Set the authnet parameters
		$verify->setParameter("x_first_name", $firstName);
		$verify->setParameter("x_last_name", $lastName);
		$verify->setParameter("x_email", $emailAddress);
		//$verify->setParameter("x_company", $companyName);
		$verify->setParameter("x_phone", $phoneNumber);
		//$verify->setParameter("x_fax", $faxNumber);
		$verify->setParameter("x_address", $streetAddress); 
		$verify->setParameter("x_city", $theCity);
		$verify->setParameter("x_state", $theState);
		$verify->setParameter("x_zip", $zipCode); 
		//$verify->setParameter("x_country", $country);
		//$verify->setParameter("x_customer_ip", $ipAddress);
	
		// Set the transaction parameters
		$verify->setTransaction($creditCard, $expirationDate, $totalAmount, $cvv2);
		$verify->setTransactionType("AUTH_ONLY");
		
		// Process the verification and continue based on the response (approved, declined, error)
		$verify->process(); 
		
		if ($verify->isApproved()) { 
			// Verify successful. 
			// Lets void the verification transaction and continue creating the ARB subscription
				$transID = $verify->getTransactionID();				
				$void = new Authnet(); 
				
				// Set the authnet parameters
				$void->setParameter("x_first_name", $firstName);
				$void->setParameter("x_last_name", $lastName);
				$void->setParameter("x_email", $emailAddress);
				//$void->setParameter("x_company", $companyName);
				$void->setParameter("x_phone", $phoneNumber);
				//$void->setParameter("x_fax", $faxNumber);
				$void->setParameter("x_address", $streetAddress); 
				$void->setParameter("x_city", $theCity);
				$void->setParameter("x_state", $theState);
				$void->setParameter("x_zip", $zipCode); 
				//$void->setParameter("x_country", $country);
				//$void->setParameter("x_customer_ip", $ipAddress);
				
				$void->setTransaction($creditCard, $expirationDate, $totalAmount, $cvv2);
				$void->setTransactionType("VOID");
				$void->setParameter("x_trans_id", $transID);
				
				$void->process();
				
				if(!$void->isApproved()) {
					$reason = "(".$void->getResponseReasonCode().")".$void->getResponseText();
					return "VOID Error: ".$reason; 
				}
		}
		else if ($verify->isDeclined()) { 
			// Verification declined. They dont even have a dollar available, so we can stop right here. 
				$reason = "(".$verify->getResponseReasonCode().")".$verify->getResponseText();
				return "VERIFY Declined";
		} 
		else { 
			// An error occurred. 
			// Not approved nor declined; Reference $reason for more information.
				$reason = "(".$verify->getResponseReasonCode().")".$verify->getResponseText();
				return "VERIFY Error: ".$reason;
		}		
	}
	
	// Construct the class. Passing "true" as a parameter enables test mode, default is false
	$arb = new AuthnetARB();
	
	$arb->setParameter('amount', $totalAmount);
	$arb->setParameter('cardNumber', $creditCard); 
	$arb->setParameter('expirationDate', $expirationDate); 
	$arb->setParameter('firstName', $firstName); 
	$arb->setParameter('lastName', $lastName); 
	$arb->setParameter('address', $streetAddress); 
	$arb->setParameter('city', $theCity); 
	$arb->setParameter('state', $theState); 
	$arb->setParameter('zip', $zipCode); 
	$arb->setParameter('email', $emailAddress); 
	$arb->setParameter('subscrName', $subscriptionName);
	
	// Modify the billing start date. Default 1st payment is today (More accurately, every 24 hours at 5AM EST)
	if($start_date) {
		// Accept the new start date in the format of MM/DD/YEAR
		$date_values = explode("/",$start_date);
		$month = $date_values[0];
		$day = $date_values[1];
		$year = $date_values[2];
		
		$arb->setParameter('startDate', date("Y-m-d", mktime(0,0,0,$month,$day,$year)));
	}
	
	// Determine how frequently the subscription should bill the customer. Default is monthly.
	switch($frequency) { 
		case "yearly":
	$arb->setParameter('interval_unit', "months");
	$arb->setParameter('interval_length', "12");  
		break;
		case "biyearly":
	$arb->setParameter('interval_unit', "months");
	$arb->setParameter('interval_length', "6");  
		break;
		case "trimonthly":
	$arb->setParameter('interval_unit', "months");
	$arb->setParameter('interval_length', "3");  
		break;
		case "bimonthly":
	$arb->setParameter('interval_unit', "months");
	$arb->setParameter('interval_length', "2");  
		break;
		case "monthly":
	$arb->setParameter('interval_unit', "months");
	$arb->setParameter('interval_length', "1");  
		break;
		case "biweekly":
	$arb->setParameter('interval_unit', "days");
	$arb->setParameter('interval_length', "14");  
		break;
		case "weekly":
	$arb->setParameter('interval_unit', "days");
	$arb->setParameter('interval_length', "7");  
		break;
	}
	
	// Set the length of the subscription. Default is infinite.
	$arb->setParameter('totalOccurrences', $until);	
	
	// Create the subscription.
	$arb->createAccount();

	if ($arb->isSuccessful()) { 
		// ARB subscription created successfully
		
		 $login = "83YbAm3E"; // The login is used to login into the merchant's control panel as well as identifying the merchant to Authorize.Net's API. 
	$transkey = "8t5h2mE933x4BFfR";
	
	$cc_name=explode(" ",$creditCardName);
	
		// Create new CIM profile
	$content =
	"<?xml version=\"1.0\" encoding=\"utf-8\"?>".
	"<createCustomerProfileRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">".
	"<merchantAuthentication>".
	"<name>".$login."</name>".
	"<transactionKey>".$transkey."</transactionKey>".
	"</merchantAuthentication>".
	"<profile>".	
	"<description>Account For ".$firstName." ".$lastName."</description>".
	"<email>".$emailAddress ."</email>".
	"<paymentProfiles>".
	"<customerType>individual</customerType>".
	"<billTo>".
	"<firstName>".$cc_name[0]."</firstName>".
	"<lastName>".$cc_name[1]."</lastName>".
	"<address>".$cc_address."</address>".
	"<city>".$cc_city."</city>".
	"<state>".$cc_state."</state>".
	"<zip>".$cc_zip."</zip>".
	"<phoneNumber>".$phoneNumber."</phoneNumber>".
	"</billTo>".
	"<payment>".
	"<creditCard>".
	"<cardNumber>".$creditCard."</cardNumber>".
	"<expirationDate>".$expirationDate2."</expirationDate>".
	"<cardCode>".$cvv2."</cardCode>".
	"</creditCard>".
	"</payment>".	
	"</paymentProfiles>".	
	"</profile>".
	"<validationMode>liveMode</validationMode>".
	"</createCustomerProfileRequest>";
	
	//send the xml via curl
	$host = "api.authorize.net";
$path = "/xml/v1/request.api";
	$response = $arb->send_request_via_curl($host,$path,$content);
	
		//if the connection and send worked $response holds the return from Authorize.net
		if ($response){
		list ($resultCode, $code, $text, $customerProfileId, $customerPayProfileId2) = $arb->parse_return($response);
		
			$dbconn=mysql_connect ("localhost", "timberli_timberl","Y@V84*6va6w") or die('Cannot connect to the database because: ' . mysql_error());
$db_selected = mysql_select_db("timberli_timberline", $dbconn);

$clientcq="SELECT * FROM clients WHERE clientphone='$phoneNumber'";
$clientc=mysql_query($clientcq,$dbconn);
$totalRows_clientc=mysql_num_rows($clientc);

if($totalRows_clientc=='0'){
$clientq="INSERT INTO clients (clientfirst, clientlast, clientphone, pid, ppid) VALUES ('$firstName','$lastName','$phoneNumber','$customerProfileId','$customerPayProfileId2')";
$client=mysql_query($clientq,$dbconn);

			
		} 
			
		} else {
		echo "Error";
		}
		
		$subscriber_id = $arb->getSubscriberID();	
		mailOrder($totalAmount, $subscriber_id, $frequency, $lotsize);
		
		
		
		
		
		header("Location: $redirect?response=success&transaction=$subscriber_id&result=$text");	
	}
	else if ($arb->isError()) {
		// Error creating the ARB subscription
		$_SESSION['error'] = "There was an error creating your lawncare subscription. Please call us to get this resolved.";
		return false;
	}
}

function mailOrder($total, $trans="", $freq=false, $lotsize=false) {
	
	session_start();
	
	($freq=="weekly")? $freq="Weekly":$freq="Bi-Weekly";
	
	$domain = str_replace("www.", "", $_SERVER["HTTP_HOST"]);
	$adminEmail = "john@networkstrategics.com";
	// $adminEmail = "TimberlineLawnandLandscape@gmail.com";
	$companyName = "Timberline Lawn and Landscape";
	
	$cumsg .= "<html>
		<head>
			<title>Transaction successfully approved</title>
			<style type=\"text/css\">
				td {
					font: 11px Verdana, Arial, Helvetica, sans-serif;
					color: #000;		
				}
				.tableFoot { 
				 	height: 20px; 
					background: #E1E1E1; 
					padding-left: 10px; 
					border: 1px solid #C0C0C0; 
				}
			</style>
		</head>
		<body style=\"background: #FFFFFF;\">
			<table align=\"center\" width=\"700\">
				<tr><td valign=\"top\" colspan=\"2\"> <b>Transaction successfully approved.</b></td></tr>
				<tr><td colspan=\"2\" class=\"tableFoot\"><b>Order Information</b></td></tr>
				<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Order Date:</b> </td><td valign=\"top\">".strftime('%D %T %p', strtotime('now'))."</td></tr>
				<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Transaction Number:</b> </td><td valign=\"top\">$trans</td></tr>
				<tr><td colspan=\"2\" class=\"tableFoot\"><b>Customer Information</b></td></tr>
				<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Contact:</b> </td><td valign=\"top\">".$_SESSION['firstName']." ".$_SESSION['lastName']."</td></tr>
				<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Phone #:</b> </td><td valign=\"top\">".$_SESSION['phone']."</td></tr>
				<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Email Address:</b> </td><td valign=\"top\">".$_SESSION['email']."</td></tr>
				<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Service Address:</b> </td><td valign=\"top\">".nl2br($_SESSION['address'])."<br />".$_SESSION['city'].", ".$_SESSION['state']." ".$_SESSION['zip']."</td></tr>
				<tr><td colspan=\"2\" class=\"tableFoot\"><b>Service Information</b></td></tr>";
		if($lotsize){ $cumsg .= "<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Lot size:</b> </td><td valign=\"top\">".$lotsize." sq. ft.</td></tr>"; }
		if($freq){ $cumsg .= "<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Service Frequency:</b> </td><td valign=\"top\">".$freq."</td></tr>"; }
		$cumsg.="<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Total Charged:</b> </td><td valign=\"top\">".$total."</td></tr>
				<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Notes:</b> </td><td valign=\"top\">".$_SESSION['notes']."</td></tr>
			</table>
		</body>
	</html>";
	
	$ownermsg .= "<html>
		<head>
			<title>Transaction successfully approved</title>
			<style type=\"text/css\">
				td {
					font: 11px Verdana, Arial, Helvetica, sans-serif;
					color: #000;		
				}
				.tableFoot { 
				 	height: 20px; 
					background: #E1E1E1; 
					padding-left: 10px; 
					border: 1px solid #C0C0C0; 
				}
			</style>
		</head>
		<body style=\"background: #FFFFFF;\">
			<table align=\"center\" width=\"700\">
				<tr><td valign=\"top\" colspan=\"2\"> <b>Transaction successfully approved.</b></td></tr>
				<tr><td colspan=\"2\" class=\"tableFoot\"><b>Order Information</b></td></tr>
				<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Order Date:</b> </td><td valign=\"top\">".strftime('%D %T %p', strtotime('now'))."</td></tr>
				<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Transaction Number:</b> </td><td valign=\"top\">$trans</td></tr>
				<tr><td colspan=\"2\" class=\"tableFoot\"><b>Customer Information</b></td></tr>
				<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Contact:</b> </td><td valign=\"top\">".$_SESSION['firstName']." ".$_SESSION['lastName']."</td></tr>
				<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Phone #:</b> </td><td valign=\"top\">".$_SESSION['phone']."</td></tr>
				<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Email Address:</b> </td><td valign=\"top\">".$_SESSION['email']."</td></tr>
				<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Service Address:</b> </td><td valign=\"top\">".nl2br($_SESSION['address'])."<br />".$_SESSION['city'].", ".$_SESSION['state']." ".$_SESSION['zip']."</td></tr>
				<tr><td colspan=\"2\" class=\"tableFoot\"><b>Service Information</b></td></tr>";
		if($lotsize){ $ownermsg .= "<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Lot size:</b> </td><td valign=\"top\">".$lotsize." sq. ft.</td></tr>"; }
		if($freq){ $ownermsg .= "<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Service Frequency:</b> </td><td valign=\"top\">".$freq."</td></tr>"; }
		$ownermsg.="<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Total Charged:</b> </td><td valign=\"top\">".$total."</td></tr>"; 
		if((isset($_SESSION['referral'])) && ($_SESSION['referral'] != "select")) { $ownermsg .= "<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Referred By:</b> </td><td valign=\"top\">".$_SESSION['referral']."</td></tr>"; }
		$ownermsg.="<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Notes:</b> </td><td valign=\"top\">".$_SESSION['notes']."</td></tr>
			</table>
		</body>
	</html>";
	
	$clientSubject = "Transaction Confirmation From: ".ucfirst($domain)."";
	$ownerSubject = "New Transaction On: ".ucfirst($domain)."";
		 	
	$mail = new phpmailer();
	$mail->From = "transactions@$domain";
	$mail->FromName = $companyName;
	$mail->ContentType = "text/html";

	// email to the customer
	$mail->Body = $cumsg;
	$mail->Subject = $clientSubject;
	$mail->AddAddress($_SESSION['email'], $_SESSION['firstName']." ".$_SESSION['lastName']);
	$mail->Send();
	$mail->ClearAddresses();

	// email to the owner
	$mail->Body = $ownermsg;
	$mail->Subject = $ownerSubject;
	$mail->AddAddress($adminEmail, $companyName);
	$mail->Send();
	$mail->ClearAddresses();
		
	session_destroy();
}

?>