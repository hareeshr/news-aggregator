<?php

namespace App\Libraries;

use jcobhams\NewsApi\NewsApi;

class NewsApiLibrary
{
    private $newsApi;

    public function __construct()
    {
        $newsApiKey = config('services.newsapi.api_key');
        $this->newsApi = new NewsApi($newsApiKey);
    }

    /**
     * Get articles from NewsAPI.
     *
     * @return array
     */
    public function getArticles()
    {
        $q = null;
        $sources = null;
        $country = 'us';
        $pageSize = 10;
        $page = 1;
        $category = null;

        $topHeadlines = $this->newsApi->getTopHeadlines($q, $sources, $country, $category, $pageSize, $page);

        if ($topHeadlines && $topHeadlines->status === 'ok') {
            return $topHeadlines->articles;
        }

        return [];
    }

    /**
     * Get personalized articles from NewsAPI based on category.
     *
     * @param  string  $category
     * @return array
     */
    public function getPersonalizedArticles($category)
    {
        $q = null;
        $sources = null;
        $country = 'us';
        $pageSize = 10;
        $page = 1;

        $topHeadlines = $this->newsApi->getTopHeadlines($q, $sources, $country, $category, $pageSize, $page);

        if ($topHeadlines && $topHeadlines->status === 'ok') {
            return $topHeadlines->articles;
        }

        return [];
    }

    /**
     * Search articles from NewsAPI based on search parameters.
     *
     * @param  string|null  $q
     * @param  string|null  $category
     * @param  string|null  $from
     * @param  string|null  $to
     * @return array
     */
    public function search($q = null, $category = null, $from = null, $to = null)
    {
        $sources = null;
        $domains = null;
        $excludeDomains = null;
        $language = 'en';
        $sortBy = null;
        $pageSize = 10;
        $page = 1;

        $articles = $this->newsApi->getEverything(
            $q,
            $sources,
            $domains,
            $excludeDomains,
            $from,
            $to,
            $language,
            $sortBy,
            $pageSize,
            $page
        );

        if ($articles && $articles->status === 'ok') {
            return $articles->articles;
        }

        return [];
    }

    /**
     * Get categories.
     *
     * @return array
     */
    public function getCategories()
    {
        $categories = [
            (object)['key' => 'business', 'name' => 'Business'],
            (object)['key' => 'entertainment', 'name' => 'Entertainment'],
            (object)['key' => 'general', 'name' => 'General'],
            (object)['key' => 'health', 'name' => 'Health'],
            (object)['key' => 'science', 'name' => 'Science'],
            (object)['key' => 'sports', 'name' => 'Sports'],
            (object)['key' => 'technology', 'name' => 'Technology'],
        ];

        return $categories;
    }
}
