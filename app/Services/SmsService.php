<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class SmsService
{
    protected $client;
    protected $apiUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->apiUrl = env('RISING_API_URL');
        $this->apiKey = env('RISING_API_KEY');

        $this->client = new Client();
    }

    public function sendSms($to, $message)
    {
        try {
            $response = $this->client->post($this->apiUrl, [
                'json' => [
                    'api_key' => $this->apiKey,
                    'to' => $to,
                    'message' => $message,
                ],
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            return $responseBody;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $message = $response->getBody()->getContents();
                throw new \Exception($message);
            }
            throw $e;
        }
    }
}
