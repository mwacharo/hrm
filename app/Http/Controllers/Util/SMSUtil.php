<?php

namespace App\Http\Controllers\Util;

class SMSUtil
{
    private $api_key = 'bd21e64357b9621ce0f7918cab39d20f';
    private $partner_id = '7985';
    private $api_url = 'https://quicksms.advantasms.com/api/services/sendsms/';

    public function sendSMS($phone_number, $message)
    {
        // data to be sent
        $data = [
            'apikey' => $this->api_key,
            'partnerID' => $this->partner_id,
            'message' => $message,
            'shortcode' => 'BoxleoLtd',
            'mobile' => $phone_number,
        ];

        // data to JSON format
        $payload = json_encode($data);

        // Prepare headers
        $headers = [
            'Content-Type: application/json',
        ];

        //cURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_message = curl_error($ch);
            curl_close($ch);
            throw new \Exception('SMS sending failed: ' . $error_message);
        }
        curl_close($ch);

        $response = json_decode($result, true);

        return $response;
    }
}
