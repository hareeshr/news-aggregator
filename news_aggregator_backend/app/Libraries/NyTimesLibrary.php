<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Http;

class NyTimesLibrary
{
    private $newsApiKey;
    private $newsApiURL = 'https://api.nytimes.com/svc/search/v2/';

    public function __construct()
    {
        $this->newsApiKey = env('NYTIMES_API_KEY');

    }

    public function getArticles()
    {
        
        $url = "{$this->newsApiURL}articlesearch.json?fq=type_of_material:(News)&sort=newest&api-key={$this->newsApiKey}";

        $response = Http::get($url);

        if ($response->ok()) {
            $articles = $response->json()['response']['docs'];
            $articlesObject = json_decode(json_encode($articles), false);
            return $articlesObject;
        } else {
            return [];
        }

    }
}
