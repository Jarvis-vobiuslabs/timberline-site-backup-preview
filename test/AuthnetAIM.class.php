<?php

class AuthnetAIM {

	/* var $gateway_url = "https://test.authorize.net/gateway/transact.dll"; */
	var $gateway_url = "https://secure.authorize.net/gateway/transact.dll"; 
   	var $field_string;
   	var $fields = array();
   
   	var $response_string;
   	var $response = array();
   
   function AuthnetAIM() {
      $this->setParameter('x_version', '3.1');
      $this->setParameter('x_delim_data', 'TRUE');
      $this->setParameter('x_delim_char', '|');  
      $this->setParameter('x_url', 'FALSE');
      $this->setParameter('x_type', 'AUTH_CAPTURE');
      $this->setParameter('x_method', 'CC');
      $this->setParameter('x_relay_response', 'FALSE');
   }
   
   function setParameter($field, $value) {
      $this->fields["$field"] = $value;   
   }

   function process() {

      foreach( $this->fields as $key => $value) $this->field_string .= "$key=" . urlencode( $value ) . "&";

      $ch = curl_init($this->gateway_url); 
      curl_setopt($ch, CURLOPT_HEADER, 0); 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim( $this->field_string, "& " )); 
      $this->response_string = urldecode(curl_exec($ch)); 
      
      if (curl_errno($ch)) {
         $this->response['Response Reason Text'] = curl_error($ch);
         return 3;
      }
      else curl_close ($ch);

      // load a temporary array with the values returned from authorize.net
      $temp_values = explode('|', $this->response_string);
 
      // load a temporary array with the keys corresponding to the values returned from authorize.net (taken from AIM documentation)
      $temp_keys= array (
           "Response Code", "Response Subcode", "Response Reason Code", "Response Reason Text",
           "Approval Code", "AVS Result Code", "Transaction ID", "Invoice Number", "Description",
           "Amount", "Method", "Transaction Type", "Customer ID", "Cardholder First Name",
           "Cardholder Last Name", "Company", "Billing Address", "City", "State",
           "Zip", "Country", "Phone", "Fax", "Email", "Ship to First Name", "Ship to Last Name",
           "Ship to Company", "Ship to Address", "Ship to City", "Ship to State",
           "Ship to Zip", "Ship to Country", "Tax Amount", "Duty Amount", "Freight Amount",
           "Tax Exempt Flag", "PO Number", "MD5 Hash", "Card Code (CVV2/CVC2/CID) Response Code",
           "Cardholder Authentication Verification Value (CAVV) Response Code"
      );
 
      for ($i=0; $i<=27; $i++) {
         array_push($temp_keys, 'Reserved Field '.$i);
      }
      $i=0;
      while (sizeof($temp_keys) < sizeof($temp_values)) {
         array_push($temp_keys, 'Merchant Defined Field '.$i);
         $i++;
      }
      for ($i=0; $i<sizeof($temp_values);$i++) {
         $this->response["$temp_keys[$i]"] = $temp_values[$i];
      }
      return $this->response['Response Code'];

   }

   function getResponseReasonTex() {
      return $this->response['Response Reason Text'];
   }
   
   function getTransactionID() {
      return $this->response['Transaction ID'];
   }
}

?>