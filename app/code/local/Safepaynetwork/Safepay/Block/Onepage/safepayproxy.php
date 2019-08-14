<?php 
	/*-----------------------------------------------------------------------------
	* Safe Pay Merchant API Proxy Class
	* Author: Tommy Adeniyi
	* Company: BHS-Consultants
	* Date: January 2012
	*
	* Class handles communication with Merchant API and returns response
	**USAGE:
	$proxy = new SafePayProxy;
	$proxy->init(array("login"=>"paul@yopstore.com","password"=>"paul"));
	$proxy->authorize_trans(array(  "amount"		=>	150,
									"baddress"		=>	1,
									"bpostal"		=>	"12345",
									"bcountry"		=>	"CA",
									"bip"			=>	"196.156.2.3",
									"cid"			=>	"a@a.com",
									"bfirstname"	=>	"Andres",
									"blastname"		=>	"Lara",
									"details"		=>	"MJ's Nose",
									"mtransactionid"=>	"7778p88dmjnsssmjbbnk",
									"cfirstsix"		=>	"123456",
									"clastsix"		=>	"098765",
									"cname"			=>	"Andres Lara",
									"ctype"			=>	"VISA"));
								
	* athentication returns 'PROCEED','CEASE' and "TIMEDOUT'
	* error codes are also returned if validation fails
	* see below for complete list of error codes
	400 :Precondition Failed
	400 :Invalid Request : text validation failed (More details in the error message)
	400.0 :Invalid  card
	400.1 :Invalid card type
	400.2 :Invalid device
	400.3 :Invalid language
	400.4 :Invalid country
	400.5 :Invalid Handshake code
	400.6 :Invalid token
	400.7 :Invalid filter
	403   : Forbidden
	403.0 :Wrong credentials
	403.1 :Wrong auth code
	403.2 :Wrong handshake code
	403.3 :Wrong token
	403.4 :Not the owner
	404   :Not found
	404.0 :Merchant not found
	404.1 :Consumer not found
	404.2 :User not found
	404.3 :Card not found
	404.4 :Device not found
	404.5 :Transaction not found
	412.0 :Card has been disabled
	412   :Precondition failed
	412.1 :Card has been deleted
	412.2 :Card limit for a consumer has been reached
	412.3 :Device is not ready
	412.4 :Device has already been handshaked
	412.5 :The user profile has already been validated
	412.6 :This transaction has already been web-authenticated
	412.7 :This transaction cannot be web-authenticated
	412.8 :Transaction does not satisfy the prerequisites to be processed.
	412.9 :The given resource already exists.
	500   :Internal Server error
	500   :Internal Server Error
	*------------------------------------------------------------------------------
	* 
	*/

	error_reporting(E_ALL);
    ini_set('display_errors', 'off');
	set_time_limit(120);

	class SafePayProxy{
		
		// Class Properties
		var $merchantLogin;
		var $merchantPassword;
		var $authrequestURL;
		var $authtokenrequestURL;
		var $curl;
		var $transactionData;
		var $transactionId;
		var $insertId;
		var $curlPostData;
		var $curl_response;
		
		function SafePayProxy(){
			
		
		}
		
		function init($creds){
			
			$this->merchantLogin = $creds['login'];
			$this->merchantPassword = $creds['password'];
			$this->authrequestURL = "https://api.usesafepay.com/v1/merchants/".$this->merchantLogin."/transactions/request";
	
		}
		
		function authorize_trans($transData){
		   
		   $this->transactionData = $transData;
		   $address1=$transData["billingaddress1"];
		   $address1=str_replace("#","Unit ",$address1);
		   $address2=$transData["billingaddress2"];
		   $address2=str_replace("#","Unit ",$address2);
	   
		   $address=trim($address1).", ".trim($address2).", ".trim($transData["billingcity"]).", ".trim($transData["billingcountry"]).". ".trim($transData["billingpostal"]);
			
			$url = file_get_contents('http://maps.googleapis.com/maps/api/geocode/xml?address='.urlencode($address).'&sensor=true');
			if($url===false){return 'ERROR';}
			$xml = simplexml_load_string($url); 
			$tracks = $xml->result; 
			$streetname=""; 
			$streetnumber=""; 
			$suitenumber=""; 
			foreach($tracks as $key) { 
				foreach($key->address_component as $val) {
					if ($val->type == "subpremise") { 
						$suitenumber = $val->long_name;
					}
					if ($val->type == "street_number") { 
						$streetnumber = $val->long_name;
					}
					if ($val->type == "route") { 
						$streetname = $val->long_name;
					}
				} 
			}
		   
			//Generate XML output from object properties
		   $this->curlPostData  ="<?xml version='1.0'?>";
		   $this->curlPostData .="<transactionCreate>";
		   $this->curlPostData .="<amount>".$transData["amount"]."</amount>";
		   $this->curlPostData .="<billingCity>".$transData["billingcity"]."</billingCity>";
		   $this->curlPostData .="<billingCountry>".$transData["billingcountry"]."</billingCountry>";
		   $this->curlPostData .="<billingPostalCode>".$transData["billingpostal"]."</billingPostalCode>";  
		   
		   $this->curlPostData .="<billingStreetName>".$streetname."</billingStreetName>";
		   $this->curlPostData .="<billingStreetNumber>".$streetnumber."</billingStreetNumber>";
		   $this->curlPostData .="<billingSuiteNumber>".$suitenumber."</billingSuiteNumber>";
		   
		   $this->curlPostData .="<buyerFirstName>".$transData["billingfirstname"]."</buyerFirstName>";
		   $this->curlPostData .="<buyerLastName>".$transData["billinglastname"]."</buyerLastName>";
		   
		   $this->curlPostData .="<card>";
		   $this->curlPostData .="<firstSixDigits>".$transData["creditcardfirstsix"]."</firstSixDigits>";
		   $this->curlPostData .="<lastSixDigits>".$transData["creditcardlastsix"]."</lastSixDigits>";
		   $this->curlPostData .="<type>".$transData["creditcardtype"]."</type>";
		   $this->curlPostData .="</card>";

		   $this->curlPostData .="<consumerEmail>".$transData["customeremail"]."</consumerEmail>";  
		   $this->curlPostData .="<merchantTransactionId>".$transData["merchanttransactionid"]."</merchantTransactionId>";
		   $this->curlPostData .="</transactionCreate>";
		   // Write XML string to a temp file
		   // Neccessary for CURLOPT_PUT, CURLOPT_INFILE and CURLOPT_INFILESIZE
		   $putData = tmpfile(); 
		   fwrite($putData, $this->curlPostData); 
		   fseek($putData, 0); 
		   // CURL request
		   $this->curl = curl_init($this->authrequestURL);
		   curl_setopt($this->curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));               			       
		   curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 120);
		   curl_setopt($this->curl, CURLOPT_TIMEOUT, 120);                         
		   curl_setopt($this->curl, CURLOPT_USERPWD, $this->merchantLogin . ":" . $this->merchantPassword);   
		   curl_setopt($this->curl, CURLOPT_RETURNTRANSFER,true);
		   curl_setopt($this->curl, CURLOPT_PUT, true);
		   curl_setopt($this->curl, CURLOPT_INFILE, $putData);
		   curl_setopt($this->curl, CURLOPT_INFILESIZE, strlen($this->curlPostData));
		   // Execute CURL request
		   $this->curl_response = curl_exec($this->curl);
           //die($this->curl);
		   // Check for CURL error.. if so send an error response
		   if(curl_errno($this->curl))
			{
				 return curl_error($this->curl);
			}
		   curl_close($this->curl);
		   return $this->handle_curl_response();
		   
		}
		
		function handle_curl_response(){
			//print_r($this->curl_response);
		   // No errors so far.. create a simplexml object from response  
           libxml_use_internal_errors(true);         
		   if(@simplexml_load_string($this->curl_response)){
		   $xml = simplexml_load_string($this->curl_response);
		   }else{
			return 'ERROR';
		   }
		   //print_r ($xml);
		   // Note: xml returned could contain a status node or a code and message node
		   // If there is a status node....
		   if($xml->status){
			   $trans_response = $xml->status; //PROCEED , CEASE OR TIMEDOUT
			   $transactionId = $xml->transactionId;
			   return $trans_response;
		   }else{
		   // Else there is a code and message node
		   // Safepay Validation Errors
			   $code = $xml->code;
			   $message = $xml->message;
			   if($code==404.1){ // Consumer not found
				 return 'PROCEED';
			   }else if($code==404.2){ // User not found
				 return 'PROCEED';
			   }else{ // Info Sent for Auth is not accurate with Safepay
			   	 //codes 400.0-400.7,403.0-403.4, 404.0-404.5, 412.0-412.9,500
				 return $code;
				 //print_r ($safepay_response);
				 //return 'ERROR';
			   }
		   }
		
		}
}
?>