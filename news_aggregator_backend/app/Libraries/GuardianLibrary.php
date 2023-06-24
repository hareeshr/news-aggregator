<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Http;

class GuardianLibrary
{
    private $newsApiKey;
    private $newsApiURL = 'https://content.guardianapis.com/';

    public function __construct()
    {
        $this->newsApiKey = config('services.guardian.api_key');
    }

    /**
     * Get articles from Guardian API.
     *
     * @return array
     */
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

    /**
     * Get personalized articles from Guardian API based on categories.
     *
     * @param  string  $categories
     * @return array
     */
    public function getPersonalizedArticles($categories)
    {
        $url = "{$this->newsApiURL}search?section={$categories}&api-key={$this->newsApiKey}";

        $response = Http::get($url);

        if ($response->ok()) {
            $articles = $response->json()['response']['results'];
            $articlesObject = json_decode(json_encode($articles), false);
            return $articlesObject;
        } else {
            return [];
        }
    }

    /**
     * Search articles from Guardian API based on search parameters.
     *
     * @param  string|null  $q
     * @param  string|null  $categories
     * @param  string|null  $begin_date
     * @param  string|null  $end_date
     * @return array
     */
    public function search($q = null, $categories = null, $begin_date = null, $end_date = null)
    {
        $qfilter = [];
        if ($q) {
            $qfilter['q'] = $q;
        }
        if ($categories) {
            $qfilter['section'] = $categories;
        }
        if ($begin_date) {
            $qfilter['from-date'] = $begin_date;
        }
        if ($end_date) {
            $qfilter['to-date'] = $end_date;
        }

        $queryString = http_build_query($qfilter);

        $url = "{$this->newsApiURL}search?{$queryString}&api-key={$this->newsApiKey}";

        $response = Http::get($url);

        if ($response->ok()) {
            $articles = $response->json()['response']['results'];
            $articlesObject = json_decode(json_encode($articles), false);
            return $articlesObject;
        } else {
            return [];
        }
    }

    /**
     * Get categories from Guardian API.
     *
     * @return array
     */
    public function getCategories()
    {
        $categories = [];

        $url = "{$this->newsApiURL}sections?api-key={$this->newsApiKey}";

        $response = Http::get($url);

        if ($response->ok()) {
            $articles = $response->json()['response']['results'];
            $articlesObject = json_decode(json_encode($articles), false);
            foreach ($articlesObject as $category) {
                array_push($categories,
                    (object) [
                        'key' => $category->id,
                        'name' => $category->webTitle,
                    ]
                );
            }
        }

        return $categories;
    }
}
