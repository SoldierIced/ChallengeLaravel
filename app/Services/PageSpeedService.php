<?php

namespace App\Services;

class PageSpeedService extends BaseApiService
{
    private $apiKey;

    public function __construct()
    {
        $baseUri = 'https://www.googleapis.com/pagespeedonline/v5/';
        parent::__construct($baseUri);

        $this->apiKey = config('services.google.api_key'); // Configura tu API Key en config/services.php
    }

    public function getMetrics(string $url, array $categories, string $strategy)
    {
        //TODO implementar cath errors.
        $categoriesParam = implode('&category=', $categories);
        $uri = "runPagespeed?url={$url}&key={$this->apiKey}&category={$categoriesParam}&strategy={$strategy}";
        return $this->request('GET', $uri);
    }
}
