<?php

namespace App\Libraries;

use jcobhams\NewsApi\NewsApi;

class NewsApiLibrary
{
    private $newsapi;

    public function __construct()
    {
        $newsApiKey = env('NEWSAPI_API_KEY');
        $this->newsapi = new NewsApi($newsApiKey);
    }

    public function getArticles($q, $sources, $country, $category, $page_size, $page)
    {
        // Set default values if the parameters are missing or invalid
        $q = $q ?? null; // Default to null if 'q' parameter is missing
        $sources = $sources ?? null; // Default to null if 'sources' parameter is missing
        $country = $country ?? 'us'; // Default to 'us' if 'country' parameter is missing
        $category = $category ?? null; // Default to null if 'category' parameter is missing
        $page_size = 10; // Default to 20 if 'page_size' parameter is missing
        $page = max(1, $page ?? 1); // Default to 1 if 'page' parameter is missing

        $top_headlines = $this->newsapi->getTopHeadlines($q, $sources, $country, $category, $page_size, $page);

        if ($top_headlines) {
            return $top_headlines->articles;
        }

        return [];
    }
    public function getCategories(){
        $categories = [
            (object) ['key'=>'business', 'name'=>'Business'],
            (object) ['key'=>'entertainment', 'name'=>'Entertainment'],
            (object) ['key'=>'general', 'name'=>'General'],
            (object) ['key'=>'health', 'name'=>'Health'],
            (object) ['key'=>'science', 'name'=>'Science'],
            (object) ['key'=>'sports', 'name'=>'Sports'],
            (object) ['key'=>'technology', 'name'=>'Technology']
        ];

        return $categories;
    }
}
