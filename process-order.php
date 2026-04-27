<?php
session_start();

require_once("PHPMailer.class.php");
require_once("AuthnetAIM.class.php");
require_once("AuthnetARB.class.php");

$apiLogin = "83YbAm3E";
$apiKey = "8t5h2mE933x4BFfR";

function submitOrder() {
	global $apiLogin, $apiKey, $_POST;
	
		/* lets see what they filled out. declare it as a session var in case of errors to keep the form info */	
		$first_name = $_SESSION['firstName'] = $_POST['firstName'];
		$last_name = $_SESSION['lastName'] = $_POST['lastName'];
		$email = $_SESSION['email'] = $_POST['email'];
		$phone = $_SESSION['phone'] = $_POST['phone'];

		$address = $_SESSION['address'] = $_POST['address'];
		$city = $_SESSION['city'] = $_POST['city'];
		$state = $_SESSION['state'] = $_POST['state'];
		$zip = $_SESSION['zip'] = $_POST['zip'];
		
		$service = $_SESSION['service'] = $_POST['service'];
		
		$lcs_frequency = $_SESSION['lcs-frequency'] = $_POST['lcs-frequency'];
		$lcs_lotsize = $_SESSION['lcs-lotsize'] = $_POST['lcs-lotsize'];
		
		$has_estimate = $_SESSION['has-estimate'] = $_POST['has-estimate'];
		$lcb_frequency = $_SESSION['lcb-frequency'] = $_POST['lcb-frequency'];
		$lcb_lotsize = $_SESSION['lcb-lotsize'] = $_POST['lcb-lotsize'];
		$lcb_estimate = $_SESSION['lcb-estimate'] = $_POST['lcb-estimate'];
		$lcb_price = $_SESSION['lcb-price'] = $_POST['lcb-price'];
		
		$ls_estimate = $_SESSION['ls-estimate'] = $_POST['ls-estimate'];
		$ls_price = $_SESSION['ls-price'] = $_POST['ls-price'];
		
		$notes = $_SESSION['notes'] = $_POST['notes'];
		
		$creditCardName = $_SESSION['creditCardName'] = $_POST['creditCardName'];
		$cc_address = $_SESSION['cc_address'] = $_POST['cc_address'];
		$cc_city = $_SESSION['cc_city'] = $_POST['cc_city'];
		$cc_state = $_SESSION['cc_state'] = $_POST['cc_state'];
		$cc_zip = $_SESSION['cc_zip'] = $_POST['cc_zip'];
		
		$cc_type = $_SESSION['creditCardType'] = $_POST['creditCardType'];
		$cc_number = $_SESSION['creditCardNumber'] = $_POST['creditCardNumber'];
		$cc_last_4 = substr($cc_number, -4); //
		$cc_month = $_SESSION['expDateMonth'] = $_POST['expDateMonth'];
		$cc_year = $_SESSION['expDateYear'] = $_POST['expDateYear'];
		$cc_expire = $cc_month."/".substr($cc_year, -2); //
		$cc_cvv2 = $_SESSION['cvv2Number'] = $_POST['cvv2Number'];
		
		$referral = $_SESSION['referral'] = $_POST['referral'];
		$tos_agreement = $_SESSION['agreement'] = $_POST['agreement'];		
		
		
		/* lets check for errors and redirect the customer back to the form to correct them */		
		if(empty($_SESSION['address']) || empty($_SESSION['city']) || empty($_SESSION['state']) || empty($_SESSION['zip'])) $error .= "Verify customer address information is filled out.<br />";
		if(empty($_SESSION['cc_address']) || empty($_SESSION['cc_city']) || empty($_SESSION['cc_state']) || empty($_SESSION['cc_zip'])) $error .= "Verify billing address information is filled out.<br />";
		if(empty($_SESSION['email'])) $error .= "Your email address seems invalid.<br />";
		if(empty($_SESSION['firstName'])) $error .= "First name must be filled out.<br />";
		if(empty($_SESSION['lastName'])) $error .= "Last name must be filled out.<br />";
		if(empty($_SESSION['creditCardType'])) $error .= "Credit card type must be selected.<br />";
		if(empty($_SESSION['creditCardNumber'])) $error .= "Incorrect credit card number.<br />";
		if(empty($_SESSION['expDateMonth']) || empty($_SESSION['expDateYear'])) $error .= "Incorrect credit card expidation date.<br />";
		if(empty($_SESSION['cvv2Number'])) $error .= "Missing credit card validation code.<br />";
		if($_SESSION['agreement'] != "on") $error .= "You must agree to the Terms of Service.<br />";
		
		if(isset($error)) {
			$_SESSION['error'] = $error;
			header("Location: order.php");
			exit();
		}
		
		/* lets create a payment based on the service they selected */		
		$a = new AuthnetAIM();
		
		$a->setParameter("x_login", $apiLogin);
		$a->setParameter("x_tran_key", $apiKey);
		$a->setParameter("x_version", "3.1");
		$a->setParameter("x_delim_char", "|"); 
		$a->setParameter("x_delim_data", "TRUE");
		$a->setParameter("x_url", "FALSE");
		$a->setParameter("x_type", "AUTH_CAPTURE");
		$a->setParameter("x_method", "CC");
		$a->setParameter("x_relay_response", "FALSE");
		
		// Customer Info
		$a->setParameter("x_first_name", $_SESSION['firstName']);
		$a->setParameter("x_last_name", $_SESSION['lastName']);
		$a->setParameter("x_address", $_SESSION['address']);
		$a->setParameter("x_city", $_SESSION['city']);
		$a->setParameter("x_state", $_SESSION['state']);
		$a->setParameter("x_zip", $_SESSION['zip']);
		$a->setParameter("x_country", "US");
		$a->setParameter("x_description", "Payment from ".$_SESSION['firstName']." ".$_SESSION['lastName']."");
		$a->setParameter("x_email", $_SESSION['email']);
		$a->setParameter("x_phone", $_SESSION['phone']);
		
		// Credit card info
		$a->setParameter("x_card_num", $_SESSION['creditCardNumber']);
		$a->setParameter("x_exp_date", $_SESSION['expDateMonth'].$_SESSION['expDateYear']);
		$a->setParameter("x_card_code", $_SESSION['cvv2Number']);

		/*
			Total Lot Size                        Weekly Cost                  Bi-Weekly Cost  
			<6000 Sq feet                             $24                            $32 
			6001-12,000 Sq Feet                       $28                            $36
			12,001-16,000 Sq Feet                     $36                            $44
			16,001-20,000 Sq Feet                     $44                            $52
		*/
		
		/* lets get the number of days the customer should be charged for the authorize.net ARB API and set the price */
		switch($lcs_frequency) {
			
			case "weekly":
			$lcs_frequency_numeric = "7"; // billed every 7 days
			switch ($lcs_lotsize) {
				case "small": 
					$lcs_price = "24.00";
					$lcs_lotsize = "Under 6,000";
				break;
				
				case "medium": 
					$lcs_price = "28.00";
					$lcs_lotsize = "6,001 - 12,000";
				break;
				
				case "large": 
					$lcs_price = "36.00";
					$lcs_lotsize = "12,001 - 16,000";
				break;
				
				case "maximum": 
					$lcs_price = "44.00";
					$lcs_lotsize = "16,001 - 20,000";
				break;
			}
			break;
			
			case "biweekly":
			$lcs_frequency_numeric = "14"; // billed every 14 days
			switch ($lcs_lotsize) {
				case "small": 
					$lcs_price = "32.00";
					$lcs_lotsize = "Under 6,000";
				break;
				
				case "medium": 
					$lcs_price = "36.00";
					$lcs_lotsize = "6,001 - 12,000";
				break;
				
				case "large": 
					$lcs_price = "44.00";
					$lcs_lotsize = "12,001 - 16,000";
				break;
				
				case "maximum": 
					$lcs_price = "52.00";
					$lcs_lotsize = "16,001 - 20,000";
				break;
			}
			break;
			
		}   
     	
     	/* lets get the number of days the customer should be charged for the authorize.net ARB API */
     	switch($lcb_frequency) {
			case "weekly": $lcb_frequency_numeric = "7"; // billed every 7 days
			break;
			
			case "biweekly": $lcb_frequency_numeric = "14"; // billed every 7 days
			break;
		}

		/* lets set master authorize.net parameters (price and ARB frequency) */
		switch($service) {
			case "lawncare-small":
				$price = $lcs_price;
				$frequency = $lcs_frequency_numeric;
				$lotsize = $lcs_lotsize;
			break;
			
			case "lawncare-big":
				$price = $lcb_price;
				$frequency = $lcb_frequency_numeric;
				$lotsize = $lcb_lotsize;
			break;
			
			case "landscaping":
				$price = $ls_price;
				$frequency = false; // landscaping is not an ARB subsciption
				$lotsize = false;
				$a->setParameter("x_type", "AUTH_ONLY");
			break;
		}

		$a->setParameter("x_amount", $price);
		
		switch ($a->process()) {
			case 1:
			if($frequency) {  
				
				$arb = new AuthnetARB($apiLogin, $apiKey, "api");
				
				$arb->setParameter("interval_length", $frequency); 
				$arb->setParameter("interval_unit", "days");
				$arb->setParameter("startDate", date('Y-m-d'));
				$arb->setParameter("totalOccurrences", 9999);
				$arb->setParameter("trialOccurrences", 0);
				$arb->setParameter("trialAmount", "0.00");
				$arb->setParameter("amount", $price);
				$arb->setParameter("refId", 15);
				$arb->setParameter("cardNumber", $cc_number);
				$arb->setParameter("expirationDate", $cc_expire);
				$arb->setParameter("firstName", $first_name);
				$arb->setParameter("lastName", $last_name);
				$arb->setParameter("address", $address);
				$arb->setParameter("city", $city);
				$arb->setParameter("state", $state);
				$arb->setParameter("zip", $zip);
				$arb->setParameter("country", "US");
				$arb->setParameter("subscrName", "Payment from ".$_SESSION['firstName']." ".$_SESSION['lastName']."");
				$arb->createAccount();
				
				if (!$arb->isSuccessful()) {
					echo "Error on the ARB:<br /><br />".$arb->text."";
					die();
				}
			}
			
			$trans = $a->getTransactionID();
			
			mailOrder($price, $trans, $frequency, $lotsize);
			
			header("Location: ?cmd=orderAccepted&trans=".$trans."&total=$price&freq=$frequency");
			break;
			
			case 2:
			header("Location: ?cmd=orderDeclined&trans=".$trans."&total=$price&freq=$frequency");
			break;
			
			case 3:
			header("Location: ?cmd=orderError&trans=".$trans."&total=$price&freq=$frequency");
			break;
		}
		
		//mailOrder($price, $trans, $frequency, $lotsize);
		
		/*
		echo "<pre>";
		print_r($_SESSION);
		echo "Last 4 of CC: $cc_last_4 <br />";
		echo "CC Expires: $cc_expire <br />";
		echo "The final price: $price <br />";
		echo "Service frequency: $frequency <br />";
		echo "</pre>";
		*/
	}


function orderAccepted($trans, $total) {
	$_SESSION['orderAccepted'] = true;
}


function orderDeclined($trans, $total) {
	$_SESSION['orderDeclined'] = true;
}

function orderError($trans, $total) {
	$_SESSION['error'] = true;
}

function mailOrder($total, $trans="", $freq=false, $lotsize=false) {
	
	session_start();
	
	($freq == 7)? $frequency="Weekly":$frequency="Bi-Weekly";
	
	$domain = str_replace("www.", "", $_SERVER["HTTP_HOST"]);
	$adminEmail = "nick@networkstrategics.com";
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
		if($freq){ $cumsg .= "<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Service Frequency:</b> </td><td valign=\"top\">".$frequency."</td></tr>"; }
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
		if($freq){ $ownermsg .= "<tr><td valign=\"top\" width=\"30%\" align=\"right\"><b>Service Frequency:</b> </td><td valign=\"top\">".$frequency."</td></tr>"; }
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


switch ($_REQUEST['cmd']):
	
	case "submitOrder":
	submitOrder();
	break;
	
	case "orderAccepted":
	orderAccepted($trans, $total);
	break;
	
	case "orderDeclined":
	orderDeclined($trans, $total);
	break;
	
	case "orderError":
	orderError($trans, $total);
	break;
	
endswitch;
?>