<?PHP
class Authnet{
	/*
	
	 Before we start to tackle processing the credit card payment programmatically, we should have an understanding of how the customer data flows from the beginning of the process (they submit the form) to the end of the process (they are informed of the success of their transaction). 
	
	   1. Capture Customer Information
	
	      What we'll need to do:
	          * Receive customer submitted data
	          * Validate all customer submitted data
	          * Inform the customer of any errors
	          * Ensure all data is in proper format for submission to the Authorize.Net API
	
	   2. Send Data to Authorize.Net
	
	      What we'll need to do:
	          * Connect to the Authorize.Net API
	          * Send the transaction data
	
	   3. React to the Response
	
	         1. Approval/Success
	            The transaction was approved by the processing bank. The merchant will be credited with their funds in a couple of business days and the customer will see the charge on their next credit card statement.
	         
			 2. Decline
	            The transaction was declined by the processing bank. For reasons known only to the processing bank, meaning you cannot find out why the transaction was declined, this sale was declined. The merchant will not get paid and the customer will not be charged.
	
	         3. Error
	            Somewhere between your application sending the transaction data to Authorize.Net and your application receiving the data back an error occurred. The transaction was not processed and cannot be considered approved or declined.
	
	      Each result will require a different response by your application:
	          * Approval/Success
	            At the very least your application should display a message indicating the transaction was successful. Ideally your application will present the customer with a printable receipt that they can keep for their records and a confirmation email will be sent as well.
	
	          * Decline
	            Because the transaction was not successful we essentially can consider it to be an error. It would be no different then if the customer entered incorrect information into the order form. The customer should be informed of the results of the transaction and be offered another opportunity to pay using a different credit card.
	
	            Tip: You may want to store the credit card information into a session variable and compare it to the new credit card information submitted by the customer. If they match you should flag it as an error without ever sending it to the gateway to be processed. Since the odds of it being declined twice is very high, almost 100%, you can save money by not incurring a transaction fee for the new declined transaction.
	
	          * Error
	            There are a variety of reasons an error can occur. Since most of them are out of our control and beyond the scope of this article, we'll only concern ourselves with how we might handle them. Basically we have two options:
	               1. Try Again
	                  Temporary connection errors and other various errors happen from time-to-time and there is no reason one won't occur while trying to process a transaction. Just like any other computer error there is no harm in trying again. And considering a sale is at stake it makes sense that we will try again. Trying again is as simple as re-sending the transaction information back to Authorize.Net.
	               
				   2. Inform the customer of the error
	                  Repeated processing errors is an indication that a transaction will not be successful. This does not mean the sale will necessarily be declined, but unfortunately something beyond your control is preventing the transaction from being processed properly. At this point all you can do is inform the customer of the error and invite them to make payment in an alternate way (e.g. Paypal, or by telephone).
	
	      What we'll need to do:
	          * Receive the response from Authorize.Net
	          * React to the response based on the results of the transaction
	          
		  To cause the system to generate a specific error, set the account to Test Mode and submit a transaction with the card number 4222222222222. 
		  The system will return the response reason code equal to the amount of the submitted transaction. 
		  For example, to test response reason code number 27, a test transaction would be submitted with the credit card number, "4222222222222," and the amount, "27.00."
	
	*/
	
	
	// We will hardcode this information into our class as opposed to making them parameters of the constructor. By placing this information directly in our class we can keep it out of our webpage's code. 
	// If the server experiences an error and were to suddenly display our source code, this sensitive information would not be available to the general public. It also allows us to place this class outside of our web root so it cannot be displayed by a web browser.
	private $login = "83YbAm3E"; // The login is used to login into the merchant's control panel as well as identifying the merchant to Authorize.Net's API. 
	private $transkey = "8t5h2mE933x4BFfR"; // The transaction key is a random sequence of characters that acts like a password for API transactions. It is a security measure that prevents the merchant from putting their true account password into their scripts in an effort to help keep them safe.  
	
	// You'll notice that $approved and $declined are defaulted to false while $error is defaulted to true. This is because it is better to assume there is an error and either try again or abort the process then to assume a sale was approved only to find out later it wasn't (and the merchant already shipped their order).
	private $approved = false; 
	private $declined = false; 
	private $error = true; 
	
	private $params = array(); // The $params parameter is an array that will hold the parameters that we be passing to the Authorize.Net API. 
	private $results = array(); // The $results parameter is an array that will hold the parameters that we be receiving from the Authorize.Net API (after a little bit of processing that is). 
	
	// Since we cannot send arrays to the Authorize.Net API nor can we receive them, we will need a place to store our parameters in a format that Authorize.Net is expecting as well as a place to receive their response. 
	private $fields; // The $fields parameter will store our properly formatted parameters for us (described later). 
	private $response; // The $response parameter will store the raw data of the return response from the Authorize.Net API. 
	
	private $test = false; 	// Setting this member to TRUE will cause our Authnet object to use the test URL for all transactions until we set it to false. 
	
	static $instances = 0;
	
	
	// Constructor
	public function __construct($test = false) { 

		if (self::$instances == 0) {

			// Determine whether to use the testing URL, production URL or to dump the Authorize.net parameters for development purposes
			$this->test = trim($test); 
			if ($this->test == "dump") { $this->url = "https://developer.authorize.net/param_dump.asp"; }
			else if ($this->test) { $this->url = "https://test.authorize.net/gateway/transact.dll"; } 
			else { $this->url = "https://secure.authorize.net/gateway/transact.dll"; } 
			
			// These 5 parameters tells Authorize.net how we will be using their API
			$this->params['x_delim_data'] = "TRUE"; 
			$this->params['x_delim_char'] = "|"; 
			$this->params['x_relay_response'] = "FALSE"; 
			$this->params['x_url'] = "FALSE"; 
			$this->params['x_version'] = "3.1"; 
			
			// These 2 parameters tell Authorize.net what kind of transaction we are running
			$this->params['x_method'] = "CC"; 
			$this->params['x_type'] = "AUTH_CAPTURE"; 
			
			// These 2 parameters are our login and transaction key for the API. Obviously without these our access to the API will be denied.
			$this->params['x_login'] = $this->login; 
			$this->params['x_tran_key'] = $this->transkey; 
			
			self::$instances++;
		}
		else {
			return false;
		}
	
	} 	
	
public function getResultResponse() { 
	/* 
		When a transaction is processed and Authorize.Net returns the results of that transaction, it sends 73 fields back containing various pieces of information about the transaction. These 73 fields are all contained in the $this->results array created by the parseResults() method.
		The first field contains a numerical indicator of the results of our transaction. getResultResponse() returns that number directly to our script. If you choose to use this method you will receive one of the following numbers:
	    	* 1 - Indicates an Approval
    		* 2 - Indicates a Decline
    		* 3 - Indicates an Error
    */
		return $this->results[0]; 
	}
	public function getResultResponseFull() { 
	/* 
		To make displaying this easier, and also making our script possibly easier to read, we can use getResultResponseFull() to output a human-readable result of our transaction. 
		We simply put the human-readable results into an array and use the response code as our array key.
	*/
		$response = array("", "Approved", "Declined", "Error"); 
		return $response[$this->results[0]]; 
	}
	
	
	// These three methods are just accessor methods that allow our scripts to access private properties of our class.
	public function isApproved() { return $this->approved; }
	public function isDeclined() { return $this->declined; }
	public function isError() { return $this->error; } 
	
		
	// Just like we have created accessor methods for the results of the transaction, we can create accessor methods to access the other 72 fields returned by the Authorize.Net API.
	public function getResponseText() { return $this->results[3]; } // If an error occurs or a transaction is declined, this field will contain text explaining what happened. If it is approved it will simply say, "This transaction has been approved.".
	public function getResponseReasonCode() { return $this->results[2]; } // If an error occurs or a transaction is declined, there are 271 possible reasons for why this may have happened. Reference the AIM Implementation Guide with this code for the exact reason. 
	public function getAuthCode() { return $this->results[4]; } // This is the six-digit approval code provided by the processing bak for the transaction. This is good to have for any transaction and vital for AUTH_ONLY transactions.
	public function getAVSResponse() { // This is a one character response indicating the results of AVS (Address Verification Service).  
		/*
			Here are the important AVS codes to know: 
				X or Y - Both the numeric address and the Zip code match the card issuing bank’s database.
				A - Address matches but the Zip code does not.
				W or Z - Zip code matches but numeric address does not.
				N - Neither the zip code or street address matches.
				U - The issuing bank doesn’t support AVS.
				G - An international credit card. AVS is not supported.
		*/
		return $this->results[5]; 
	}  
	public function getTransactionID() { return $this->results[6]; } // This is the transaction ID assigned to this transaction by Authorize.Net.
	public function viewResults() { return print_r($this->results); }
	public function getMoreResponseText() {
		/*
			Sometimes, the response text is too vague and more information may be needed. 
			Multiple error codes can produce the same response text, but the actual reason differs between them. 
			Example:
				The Response Text for Response Reason Codes 3, 4, and 41 is "The transaction has been declined."
					The reason for Reason Code 3 is the generic "The transaction has been declined". No more info is available.
					The reason for Reason Code 4 is the credit card processor has deemed this card as a pickup/destroy. 
					The reason for Reason Code 41 is if a given transaction's fraud score is higher than the threshold set by the merchant. Only merchants set up for the FraudScreen.Net service would receive this decline. 
				 
			This information is taken from the the latest Authorize.net AIM Implementation Guide (v2.0 as of this writing).
		*/
		switch($this->results[2]) {
			//TODO: Switch between the 271 reason codes -_-
		}
	}

	//function to send xml request via curl
public function send_request_via_curl($host,$path,$content)
{
	$posturl = "https://" . $host . $path;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $posturl);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($ch);
	return $response;
}

//function to parse Authorize.net response for customer profile
public function parse_return($content)
{

	$resultCode = $this->substring_between($content,'<resultCode>','</resultCode>');
	$code = $this->substring_between($content,'<code>','</code>');
	$text = $this->substring_between($content,'<text>','</text>');
	$result = $this->substring_between($content,'<directResponse>','</directResponse>');
	return array ($resultCode, $code, $text, $result); 
}

//helper function for parsing response
public function substring_between($haystack,$start,$end) 
{
	if (strpos($haystack,$start) === false || strpos($haystack,$end) === false) 
	{
		return false;
	} 
	else 
	{
		$start_position = strpos($haystack,$start)+strlen($start);
		$end_position = strpos($haystack,$end);
		return substr($haystack,$start_position,$end_position-$start_position);
	}
}

}
?>
