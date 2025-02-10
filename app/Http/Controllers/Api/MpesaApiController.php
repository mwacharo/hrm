<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaApiController extends Controller
{
    private $baseUrl = 'https://sandbox.safaricom.co.ke';
    private $shortcode = 174379;
    private $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
    private $phone = '254110666140';
    private $amount = 1;
    private $appUrl = 'https://87cc-102-213-251-6.ngrok-free.app';
    private $apiUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

    public function initiateSTKPush()
    {
        $timestamp = now()->format('YmdHis');

        $password = $this->generatePassword();

        $token = $this->generateToken();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl, [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $this->amount,
            'PartyA' => $this->phone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $this->phone,
            'CallBackURL' => $this->appUrl,
            'AccountReference' => 'Toloe Inc',
            'TransactionDesc' => 'Airtime payment',
        ]);

        return $response->body();
    }

    private function generatePassword()
    {
        return base64_encode($this->shortcode . $this->passkey . now()->format('YmdHis'));
    }

    private function generateToken()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . $this->generateBase64(),
        ])->get($this->baseUrl . '/oauth/v1/generate?grant_type=client_credentials');

        $data = $response->json();

        return $data['access_token'];
    }

    private function generateBase64()
    {
        $key = "vt6TkadxyfRBrJXFtFcBZy6zbpHKiLuR";
        $secret = "2vnP24LsRYQ0g3Jd";
        return base64_encode($key . ':' . $secret);
    }

    public function handleCallback(Request $request)
    {
        Log::info($request->all());
    }

    public function validation(Request $request)
    {
        $result_code = "0";
        $result_description = "Completed";

        $result = json_encode(["ResultCode" => $result_code, "ResultDesc" => $result_description]);
        $response = new Response();
        $response->headers->set("Content-Type", "application/json; charset=utf-8");
        $response->setContent($result);

        return $response;
    }

    public function confirmation(Request $request)
    {
        $content = json_decode($request->getContent());

        Log::info($content);

        $response = new Response();
        $response->headers->set("Content-Type", "text/xml; charset=utf-8");
        $response->setContent(json_encode(["C2BPaymentConfirmationResult" => "Success"]));

        return $response;}

    public function registerUrl()
    {
        $postData = json_encode([
            "ShortCode" => 600977,
            "ResponseType" => "Completed",
            "ConfirmationURL" => "https://87cc-102-213-251-6.ngrok-free.app/api/v1/confirmation",
            "ValidationURL" => "https://87cc-102-213-251-6.ngrok-free.app/api/v1/validation",
        ]);

        $ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl');

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->generateToken(),
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    public function simulate()
    {
        $postData = json_encode([
            'ShortCode' => 600977,
            'CommandID' => 'CustomerPayBillOnline',
            'Amount' => 1,
            "Msisdn" => '254708374149',
            'BillRefNumber' => 'Test 123',
        ]);

        $ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/c2b/v1/simulate');

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$this->generateToken(),
            'Content-Type: application/json',
        ]);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        curl_close($ch);

        Log::info($response);

        return $response;
    }



}
