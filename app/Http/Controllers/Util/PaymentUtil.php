<?php

namespace App\Http\Controllers\Util;

use App\Http\Controllers\Controller;

class PaymentUtil extends Controller
{
    public function generateToken()
    {

        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        $app_consumer_key = 'ITOKqEfuGFWilAiX8F4zGULWaEANGbIL';
        $app_consumer_secret = 'ddq2b4Eho9Ng5kUd';
        $credentials = base64_encode($app_consumer_key.':'.$app_consumer_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $token_info=json_decode($curl_response,true);
        return $token_info['access_token'];

    }

    public function registerUrl(){

        $access_token = $this->generateToken();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization: Bearer '.$access_token));

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array(
            'ShortCode' => '174379',
            'ResponseType' => 'Completed',
            'ConfirmationURL' => '/api/v1/confirmation',
            'ValidationURL' => '/api/v1/validation'
        )));

        $curl_response = curl_exec($curl);
        echo $curl_response;
    }
}
