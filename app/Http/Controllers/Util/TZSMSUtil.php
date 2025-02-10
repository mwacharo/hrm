<?php

namespace App\Http\Controllers\Util;

class TZSMSUtil
{
    private $client_secret = '09add717-145e-4fe8-9978-936ec4f26535';
    private $client_id = 'fc10f8c8-3744-4f10-b4b1-1b7138623efe';
    private $api_url = 'https://bulksms.fasthub.co.tz/api/sms/send';

    private $source = 'BoxleoLtd';

    public function sendSMS($msisdn, $text)
    {
        $curl = curl_init();

        $postData = [
            'auth' => [
                'clientId' => $this->client_id,
                'clientSecret' => $this->client_secret,
            ],
            'messages' => [
                [
                    'text' => $text,
                    'msisdn' => $msisdn,
                    'source' => $this->source,
                    'coding' => 'GSM7',
                ],
            ],
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => $this->api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($postData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            curl_close($curl);
            return ['success' => false, 'message' => $error_msg];
        }

        curl_close($curl);
        return ['success' => true, 'response' => json_decode($response, true)];
    }
}
