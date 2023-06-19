<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Http;

class GuardianLibrary
{
    private $newsApiKey;
    private $newsApiURL = 'https://content.guardianapis.com/';

    public function __construct()
    {
        $this->newsApiKey = env('GUARDIAN_API_KEY');

    }

    public function getArticles()
    {
        
        $url = "{$this->newsApiURL}search?api-key={$this->newsApiKey}";

        $response = Http::get($url);

        if ($response->ok()) {
            $articles = $response->json()['response']['results'];
            $articlesObject = json_decode(json_encode($articles), false);
            return $articlesObject;
        } else {
            return [];
        }

    }
}
