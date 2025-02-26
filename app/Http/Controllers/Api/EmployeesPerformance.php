<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeesPerformance extends Controller
{
    // protected $crm_email = env('CRM_EMAIL');
    // protected $crm_password = env('CRM_PASSWORD');

    private function getAccessToken()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.boxleocourier.com/api/admin-login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode([
                "email" => $this->crm_email,
                "password" => $this->crm_password,
            ]),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);
        if (curl_errno($curl)) {
            // Handle error or log it
            error_log('cURL error: ' . curl_error($curl));
        }
        curl_close($curl);

        $data = json_decode($response, true);

        if (isset($data['access_token'])) {
            return $data['access_token'];
        } else {
            // Log the response to see why no token is returned
            error_log('API response: ' . $response);
            return null;
        }
    }

    public function agentPerformance(Request $request)
    {
        $start_date = $request->start_date ?? '2024-02-13';
        $end_date = $request->end_date ?? '2024-02-29';
        $agent_id = $request->agent_id ?? [57,133,236];

        $accessToken = $this->getAccessToken();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.boxleocourier.com/api/analysis/perfomance',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
                "start_date": "2024-02-13",
                "end_date": "2024-02-29",
                "agent_id": [
                    57,
                    133,
                    236
                ],
                "report": {
                    "label": "Agent Performance Report",
                    "value": "generateAgentPerformanceReport"
                }
            }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        echo $response;

    }
}
