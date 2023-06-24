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

    public function getArticles()
    {
        
        $q = null;
        $sources = null;
        $country = 'us';
        $page_size = 10;
        $page = 1;
        $category = null;

        $top_headlines = $this->newsapi->getTopHeadlines($q, $sources, $country, $category, $page_size, $page);

        if ($top_headlines) {
            return $top_headlines->articles;
        }

        return [];
    }

    public function getPersonalizedArticles( $category ) {

        $q = null;
        $sources = null;
        $country = 'us';
        $page_size = 10;
        $page = 1;

        $top_headlines = $this->newsapi->getTopHeadlines($q, $sources, $country, $category, $page_size, $page);

        if ($top_headlines) {
            return $top_headlines->articles;
        }

        return [];
    }

    public function search( $q, $category, $from, $to ) {

        $sources = null;
        $domains = null;
        $exclude_domains = null;
        $language = "en";
        $sort_by = null;
        $page_size = 10;
        $page = 1;

        $top_headlines = $this->newsapi->getEverything( $q.'+'.$category, $sources, $domains, $exclude_domains, $from, $to, $language, $sort_by, $page_size, $page );
        


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
