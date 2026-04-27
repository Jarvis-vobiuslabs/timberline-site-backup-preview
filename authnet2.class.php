<?php

class Authnet { 
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
	private $login = "7euF7zw6gjLq"; // The login is used to login into the merchant's control panel as well as identifying the merchant to Authorize.Net's API. 
	private $transkey = "7u533qMkZ7Uwg6j5"; // The transaction key is a random sequence of characters that acts like a password for API transactions. It is a security measure that prevents the merchant from putting their true account password into their scripts in an effort to help keep them safe.  
	
	// You'll notice that $approved and $declined are defaulted to false while $error is defaulted to true. This is because it is better to assume there is an error and either try again or abort the process then to assume a sale was approved only to find out later it wasn't (and the merchant already shipped their order).
	private $approved = false; 
	private $declined = false; 
	private $error = true; 
	
	private $params = array(); // The $params parameter is an array that will hold the parameters that we be passing to the Authorize.Net API. 
	private $results = array(); // The $results parameter is an array that will hold the parameters that we be receiving from the Authorize.Net API (after a little bit of processing that is). 
	
	// Since we cannot send arrays to the Authorize.Net API nor can we receive them, we will need a place to store our parameters in a format that Authorize.Net is expecting as well as a place to receive their response. 
	private $fields; // The $fields parameter will store our properly formatted parameters for us (described later). 
	private $response; // The $response parameter will store the raw data of the return response from the Authorize.Net API. 
	
	private $test; 	// Setting this member to TRUE will cause our Authnet object to use the test URL for all transactions until we set it to false. 
	
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
	
	
	// The transaction() method assigns necessary values to process a customers credit card that we did not have values for when we first created our object. 
	public function setTransaction($cardnum, $expiration, $amount, $cvv = "", $invoice = "", $tax = "") { 
		// Required for successful transaction
		$this->params['x_card_num'] = trim($cardnum); 
		$this->params['x_exp_date'] = trim($expiration); 
		$this->params['x_amount'] = trim($amount);
		
		// Optional / Situational parameters.
		// Certain types of credit cards require additional parameters to be sent to the processing bank during a transaction.
		// For example, business cards require an invoice number (also called a purchase order number(po_num)) and the tax amount for the sale to be submitted with each transaction. If they are not, that sale will downgrade to a much higher processing rate for the merchant. 
		$this->params['x_po_num'] = trim($invoice); 
		$this->params['x_tax'] = trim($tax); 
		$this->params['x_card_code'] = trim($cvv);
		 
	}
	
	
	// This method sends the transaction information and receives the response from the Authorize.Net API.
	public function process($retries = 3) { 
		
		$this->prepareParameters(); 
		
		$ch = curl_init($this->url); // initializes a new CURL session and returns a CURL handle for use with the other CURL functions. 
		
		$count = 0; // retries, if needed, are based against the $count of retries. If it does not connect after three (default parameter) attempts, it probably isn't going to work in a reasonable amount of time and we should fall back to another solution (like inviting the customer to call in their order).
		while ($count < $retries) { 
			curl_setopt($ch, CURLOPT_HEADER, 0); // tells CURL to include header information in its output
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // tells CURL to store the response as a string instead of outputting it to the browser
			curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim($this->fields, "& ")); // provides CURL with the string to send to the Authorize.Net API which for us is our parameter string created by $this->prepareParameters()
			$this->response = curl_exec($ch); // sends the request to the API and receives and stores the response 
		
			$this->parseResults(); 
		
			// Now that we have our results we need to determine whether our transaction was successful, declined, or experienced an error.
			if ($this->getResultResponseFull() == "Approved") { 
				$this->approved = true; 
				$this->declined = false; 
				$this->error = false; 
				break; // end our while loop since we do not need to connect to the API any longer
			} 
			else if ($this->getResultResponseFull() == "Declined") { 
				$this->approved = false; 
				$this->declined = true; 
				$this->error = false; 
				break; // end our while loop since we do not need to connect to the API any longer
			} 
			
			// If the transaction is neither "Approved" nor "Declined" we can assume an error occurred. In this case we will increment our counter and try again.
			$count++; 
		
		} 
		
		curl_close($ch); 
	
	}
	
	
	// convert the Authorize.net API response into a format that we find easy to use	
	private function parseResults() { 
		$this->results = explode("|", $this->response); // In our constructor we told the Authorize.Net API that we will be using a pipe ("|") as our delimiting character 
	}
	
	
	// easy function for setting parameters to be set to Authorize.net API
	public function setParameter($param, $value) { 
		$param = trim($param); 
		$value = trim($value); 
	
		$this->params[$param] = $value; 
	}
	
	
	// a method that changes the transaction type to any acceptable type. This includes doing an AUTH_ONLY transaction.
	public function setTransactionType($type) { 
		$this->params['x_type'] = strtoupper(trim($type)); 
	}
	

	// prepares our parameters to be sent to the Authorize.Net API
    private function prepareParameters()
    {
        foreach($this->params as $key => $value)
        {
            $this->fields .= "$key=" . urlencode($value) . "&";
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
	
}


class AuthnetARB {
	/*	
		Before we can use our class we need to understand the context in which it will be used. In the case of recurring billing there are two scenarios we will encounter:
	
		1. Instant Payment
		In this scenario we will want to capture an immediate payment and then schedule future payments for the same credit card. In these cases we won't try to establish a recurring billing subscription unless the original transaction is successful. If the original transaction is successful we know we have a valid credit card and can set up the recurring billing subscription without doing any further validation.
	
		2. Delayed Payment
		In this scenario we have no need to charge the user immediately and seek only to schedule future recurring payments. In this case we will need to not only validate the credit card passes basic validation (right number of digits, valid expiration date, etc.) but we will also want to verify the credit card is valid and legitimate. If we establish the recurring billing subscription with an invalid credit card we won't know until the first scheduled transaction is attempted. When establishing a recurring billing subscription Authorize.Net assumes that the credit card is valid. This is because there is no way for them to check if a credit card is valid so that leaves the responsibility for verifying this up to our programming.
	*/
    private $login = "83YbAm3E";
    private $transkey = "8t5h2mE933x4BFfR";
    
	private $params = array(); // This property will store all of the information that we will be sending to Authorize.Net 
    
	// These two parameters allow us to store the state of our transaction which we can access through methods from our code. 
    private $success = false; // stores whether we successfully communicated with the ARB API. 
    private $error = true;// stores whether there was an error in our transaction.

	private $test; // allows us to easily switch back and forth between our test account and our live account.

    private $xml; // This parameter will store the XML that we will send to Authorize.Net.

    private $response; // holds the response sent to us by Authorize.Net.
    private $resultCode; // will contain a value of 'Ok' if your transaction was successful.
    private $code; // stores the error code if our transaction fails.
    private $text; // stores a description of any errors we incur. In the case of a successful transaction this will say, 'Successful'. 
    private $subscrId; // holds the subscription ID assigned to this recurring billing subscription. It is unique for each recurring billing customer.


	// Constructor
    public function __construct($test = false)
    {
    	// determines whether we are using the test server or live server
        $subdomain = ($this->test) ? 'apitest' : 'api';
        $this->url = "https://" . $subdomain . ".authorize.net/xml/v1/request.api";

        $this->params['interval_unit']    = 'months'; // tells Authorize.Net that we wish to use 'months' as our interval unit. (The other valid value here is 'days'). That means our recurring periods will always be referred to in terms of months.
        $this->params['interval_length']  = 1; // tells Authorize.Net how many "interval_units" must pass before charging the user again.
        $this->params['startDate']        = date("Y-m-d"); // tells Authorize.Net when to start the subscription. This must be the exact date you want the first payment to be charged. If you set it for the same day the transaction is run it will be charged that evening, not one month from that date.
        $this->params['totalOccurrences'] = 9999; // tells Authorize.Net how many times to charge the user. Authorize.Net does allow for subscriptions with no end date. To accomplish this you need to set your parameter to 9999. If you want the subscription to expire after 12 months, set this value to be 12.

        $this->params['trialOccurrences'] = 0; // The ARB API allows for special pricing to be set for a variable amount of recurring transactions before regular pricing kicks in. They call it a trial period and you can control how long the trial period lasts and how much to charge during this time.
        $this->params['trialAmount']      = 0.00;
    }


	// takes the parameters we have set, sends them them to Authorize.Net, receives the response, and parses the results
    private function process($retries = 3)
    {
        
		$count = 0; // retries, if needed, are based against the $count of retries. If it does not connect after three (default parameter) attempts, it probably isn't going to work in a reasonable amount of time and we should fall back to another solution (like inviting the customer to call in their order).
        
		while ($count < $retries)
        {
            $ch = curl_init(); // initializes a new CURL session and returns a CURL handle for use with the other CURL functions.

			// set the parameters for cURL            
            curl_setopt($ch, CURLOPT_URL, $this->url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xml);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            
			$this->response = curl_exec($ch); // sends our transaction to Authorize.Net and receives their response. assigns that response to our $response parameter.
            
			$this->parseResults(); // processes data in the $response parameter 
            
			// check to see if our transaction was successful		
			if ($this->resultCode === "Ok")
            {
                $this->success = true;
                $this->error   = false;
                break;
            }
            else
            {
                $this->success = false;
                $this->error   = true;
                break;
            }
            
            $count++;
            
        }
        
		curl_close($ch);
		
    }


    public function createAccount()
    {
    	// sets the value of $xml. We populate it with the XML specified by Authorize.Net to be used for creating a recurring subscription.
        $this->xml = "<?xml version='1.0' encoding='utf-8'?>
          <ARBCreateSubscriptionRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
              <merchantAuthentication>
                  <name>" . $this->login . "</name>
                  <transactionKey>" . $this->transkey . "</transactionKey>
              </merchantAuthentication>
              <refId>" . $this->params['refID'] ."</refId>
              <subscription>
                  <name>". $this->params['subscrName'] ."</name>
                  <paymentSchedule>
                      <interval>
                          <length>". $this->params['interval_length'] ."</length>
                          <unit>". $this->params['interval_unit'] ."</unit>
                      </interval>
                      <startDate>" . $this->params['startDate'] . "</startDate>
                      <totalOccurrences>". $this->params['totalOccurrences'] .
                      "</totalOccurrences>
                      <trialOccurrences>". $this->params['trialOccurrences'] .
                      "</trialOccurrences>
                  </paymentSchedule>
                  <amount>". $this->params['amount'] ."</amount>
                  <trialAmount>" . $this->params['trialAmount'] . "</trialAmount>
                  <payment>
                      <creditCard>
                          <cardNumber>" . $this->params['cardNumber'] . "</cardNumber>
                          <expirationDate>" . $this->params['expirationDate'] .
                          "</expirationDate>
                      </creditCard>
                  </payment>
                  <billTo>
                      <firstName>". $this->params['firstName'] . "</firstName>
                      <lastName>" . $this->params['lastName'] . "</lastName>
                      <address>" . $this->params['address'] . "</address>
                      <city>" . $this->params['city'] . "</city>
                      <state>" . $this->params['state'] . "</state>
                      <zip>" . $this->params['zip'] . "</zip>
                  </billTo>
              </subscription>
          </ARBCreateSubscriptionRequest>";
        
        // call the process() method to complete our transaction
        $this->process();
    }


	/* 
		The ARB API can accept over 60 different parameters when submitting a transaction. 
		To keep our code organized and optimized we store them in our $params array. 
		To add new fields to the array we simply use this method and pass it two parameters:
			$field is the name of the field in the array we wish to set
			$value is the value we wish to assign to the field
		
		Note: If you add fields to the $params array but the associated XML is not present then that parameter will never be sent to Authorize.Net.
	*/
    public function setParameter($field = "", $value = null)
    {
        $field = (is_string($field)) ? trim($field) : $field;
        $value = (is_string($value)) ? trim($value) : $value;
        $this->params[$field] = $value;
    }


	// populate some of our class properties with the data returned by Authorize.Net.
    private function parseResults()
    {
        $this->resultCode = $this->parseXML('<resultCode>', '</resultCode>');
        $this->code       = $this->parseXML('<code>', '</code>');
        $this->text       = $this->parseXML('<text>', '</text>');
        $this->subscrId   = $this->parseXML('<subscriptionId>', '</subscriptionId>');
    }


	// takes the response returned for Authorize.Net and looks for pieces of information inside the XML.
    private function parseXML($start, $end)
    {
        return preg_replace('|^.*?'.$start.'(.*?)'.$end.'.*?$|i', '$1', substr($this->response, 335));
    }


	// important for any application that will also be editing and/or deleting recurring billing subscriptions. You will need to provide Authorize.Net with this ID if you wish to work with existing records.
    public function getSubscriberID()
    {
        return $this->subscrId;
    }


	// these 2 methods allow us to check to see if our transaction was successful or not. They may be redundant but by having both options available to us it will make our client code easier to read and understand which is always a good thing.
    public function isSuccessful() { return $this->success; }
    public function isError() { return $this->error; }
    
}

?>