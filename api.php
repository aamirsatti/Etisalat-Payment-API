<?php
Class EtisalatApi()
{
	
	private $payment_api_url = '<API URL>';
	private $api_customer = '<API Customer>';
	private	$api_username = '<API Username>';
	private	$api_password = '<API Password>';
	private	$payment_api_general_error = '';
	private $return_url = '<Return URL>' ; // Users will be redirected to this url after the payment
	private $headers = array(
				'Content-Type: application/json',
				'Accept: application/json',
			);
	
	public function process_transaction($OrderData)
	{
		$requestJson = '{
					"Registration": {
						"Customer": "'.$this->api_customer.'",
						"Language": "en",
						"Currency": "AED",
						"OrderName": "Certificate Request",
						"OrderID": "'.$OrderData->orderID.'",
						"Channel": "W",
						"Amount": "'.$OrderData->amount.'",
						"TransactionHint": "CPT:Y;VCC:Y;",
						"ReturnPath": "'.$this->return_url.'",
						"UserName":"'.$this->api_username.'",
						"Password":"'.$this->api_password.'"
					}
				}';     
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS, $requestJson);
		curl_setopt($curl, CURLOPT_URL, $this->payment_api_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		$rawResponse = curl_exec($curl);
	   
		$response = json_decode($rawResponse,true);
		// for success
		if(!empty($response))
		{ 
			if($response['Transaction']['ResponseCode'] == 0 && $response['Transaction']['ResponseClassDescription'] === "Success")
			{
				$PaymentPortal = $response['Transaction']['PaymentPortal'];
				$transactionID = $response['Transaction']['TransactionID'];
				return array('status' => 'success',
							 'transactionID' => $transactionID,
							);        
			}
			else
			{
				$payment_api_error = $response['Transaction']['ResponseCode'];
				return array('status' => 'error',
							 'error_msg' => ($payment_api_error != '' ? $payment_api_error : $this->payment_api_general_error)
							);
			}
			
		}
		else
			return array('status' => 'error',
						 'error_msg' => $this->payment_api_general_error);
		
	}


	public function payment_finalization($transaction_id)
	{
		
		$requestJson = '{
						"Finalization": {
							"Customer": "'.$this->api_customer.'",
							"TransactionID": "'.$transaction_id.'",
							"UserName":"'.$this->api_username.'",
							"Password":"'.$this->api_password.'"
						}
						}';     
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_POSTFIELDS, $requestJson);
		curl_setopt($curl, CURLOPT_URL, $this->payment_api_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		$rawResponse = curl_exec($curl);
		$response = json_decode($rawResponse,true);
		// for success
		if(!empty($response))
		{ 
			if($response['Transaction']['ResponseCode'] == 0 && $response['Transaction']['ResponseClassDescription'] === "Success")
			{
				$ApprovalCode = $response['Transaction']['ApprovalCode'];  
				$UniqueID = $response['Transaction']['UniqueID'];
				$amount_charged = $response['Transaction']['Amount']['Value'] != '' ? $response['Transaction']['Amount']['Value'] : $amount;  

				
				return array('status' => 'success',
							 'ApprovalCode' => $ApprovalCode,
							 'UniqueID' => $UniqueID,
							 'amount_charged' => $amount_charged
							 );  
			}
			else if($response['Transaction']['ResponseDescription'] != '')
			{
				return  array('status' => 'error',
							  'message' => $response['Transaction']['ResponseDescription']); 
			}  
			else
				return array('status' => 'error',
							'message' => $payment_api_general_error);
		}
		else
			return  array('status' => 'error',
						  'message' => $this->payment_api_general_error);             
	}
}


?>