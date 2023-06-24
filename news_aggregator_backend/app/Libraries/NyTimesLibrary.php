<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Http;

class NyTimesLibrary
{
    private $newsApiKey;
    private $newsApiURL = 'https://api.nytimes.com/svc/search/v2/';

    public function __construct()
    {
        $this->newsApiKey = config('api.nytimes.api_key');
    }

    /**
     * Get articles from NY Times API.
     *
     * @return array
     */
    public function getArticles()
    {
        $url = $this->buildUrl('articlesearch.json', [
            'fq' => 'type_of_material:(News)',
            'sort' => 'newest',
        ]);

        return $this->getResponseArticles($url);
    }

    /**
     * Get personalized articles from NY Times API based on categories.
     *
     * @param string $categories
     * @return array
     */
    public function getPersonalizedArticles($categories)
    {
        $url = $this->buildUrl('articlesearch.json', [
            'fq' => 'type_of_material:(News) AND section_name.contains:("' . $categories . '")',
            'sort' => 'newest',
        ]);

        return $this->getResponseArticles($url);
    }

    /**
     * Search articles from NY Times API based on query, categories, begin date, and end date.
     *
     * @param string $q
     * @param string $categories
     * @param string $begin_date
     * @param string $end_date
     * @return array
     */
    public function search($q, $categories, $begin_date, $end_date)
    {
        $filters = [];

        if ($q) {
            $filters['q'] = $q;
        }

        if ($categories) {
            $filters['fq'] = 'type_of_material:(News) AND section_name.contains:("' . $categories . '")';
        } else {
            $filters['fq'] = 'type_of_material:(News)';
        }

        if ($begin_date) {
            $filters['begin_date'] = $begin_date;
        }

        if ($end_date) {
            $filters['end_date'] = $end_date;
        }

        $url = $this->buildUrl('articlesearch.json', $filters);

        return $this->getResponseArticles($url);
    }

    /**
     * Get available categories from NY Times API.
     *
     * @return array
     */
    public function getCategories()
    {
        $categories = [
            (object)['key' => 'Arts', 'name' => 'Arts'],
            // Add other categories here...
        ];

        return $categories;
    }

    /**
     * Build the API URL with given endpoint and query parameters.
     *
     * @param string $endpoint
     * @param array $queryParams
     * @return string
     */
    private function buildUrl($endpoint, array $queryParams = [])
    {
        $queryParams['api-key'] = $this->newsApiKey;
        $queryString = http_build_query($queryParams);

        return $this->newsApiURL . $endpoint . '?' . $queryString;
    }

    /**
     * Get articles from the API response.
     *
     * @param string $url
     * @return array
     */
    private function getResponseArticles($url)
    {
        $response = Http::get($url);

        if ($response->ok()) {
            $articles = $response->json()['response']['docs'];
            return json_decode(json_encode($articles), false);
        } else {
            return [];
        }
    }
}
