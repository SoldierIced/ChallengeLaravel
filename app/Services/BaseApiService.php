<?php

namespace App\Services;

use GuzzleHttp\Client;

class BaseApiService
{
    protected $client;

    public function __construct($baseUri, $timeOut = 120.0)
    {
        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout' => $timeOut,
        ]);
    }

    protected function request(string $method, string $uri, array $options = [])
    {
        try {
            $response = $this->client->request($method, $uri, $options);
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new \Exception('Error en la solicitud: ' . $e->getMessage());
        }
    }
}
