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

    public function getPersonalizedArticles($categories)
    {
        
        $url = "{$this->newsApiURL}articlesearch.json?fq=type_of_material:(News) AND section_name.contains:(".'"'.$categories.'"'.")&sort=newest&api-key={$this->newsApiKey}";

        $response = Http::get($url);

        if ($response->ok()) {
            $articles = $response->json()['response']['docs'];
            $articlesObject = json_decode(json_encode($articles), false);
            return $articlesObject;
        } else {
            return [];
        }

    }
    public function search( $q, $categories, $begin_date, $end_date )
    {
        $qfilter = [];
        if($q)
            $qfilter['q'] = $q;
        if($categories)
            $qfilter['fq'] = "type_of_material:(News) AND section_name.contains:(".'"'.$categories.'"'.")";
        else $qfilter['fq'] = "type_of_material:(News)";
        if($begin_date)
            $qfilter['begin_date'] = $begin_date;
        if($end_date)
            $qfilter['end_date'] = $end_date;

        $queryString = http_build_query($qfilter);

        $url = "{$this->newsApiURL}articlesearch.json?{$queryString}&sort=newest&api-key={$this->newsApiKey}";

        $response = Http::get($url);

        if ($response->ok()) {
            $articles = $response->json()['response']['docs'];
            $articlesObject = json_decode(json_encode($articles), false);
            return $articlesObject;
        } else {
            return [];
        }

    }
    
    public function getCategories(){
        $categories = [
            (object) ['key'=>'Arts', 'name'=>'Arts'],
            (object) ['key'=>'Automobiles', 'name'=>'Automobiles'],
            (object) ['key'=>'Autos', 'name'=>'Autos'],
            (object) ['key'=>'Blogs', 'name'=>'Blogs'],
            (object) ['key'=>'Books', 'name'=>'Books'],
            (object) ['key'=>'Booming', 'name'=>'Booming'],
            (object) ['key'=>'Business', 'name'=>'Business'],
            (object) ['key'=>'Business Day', 'name'=>'Business Day'],
            (object) ['key'=>'Corrections', 'name'=>'Corrections'],
            (object) ['key'=>'Crosswords & Games', 'name'=>'Crosswords & Games'],
            (object) ['key'=>'Crosswords/Games', 'name'=>'Crosswords/Games'],
            (object) ['key'=>'Dining & Wine', 'name'=>'Dining & Wine'],
            (object) ['key'=>'Dining and Wine', 'name'=>'Dining and Wine'],
            (object) ['key'=>"Editors' Notes", 'name'=>"Editors' Notes"],
            (object) ['key'=>'Education', 'name'=>'Education'],
            (object) ['key'=>'Fashion & Style', 'name'=>'Fashion & Style'],
            (object) ['key'=>'Food', 'name'=>'Food'],
            (object) ['key'=>'Front Page', 'name'=>'Front Page'],
            (object) ['key'=>'Giving', 'name'=>'Giving'],
            (object) ['key'=>'Global Home', 'name'=>'Global Home'],
            (object) ['key'=>'Great Homes & Destinations', 'name'=>'Great Homes & Destinations'],
            (object) ['key'=>'Great Homes and Destinations', 'name'=>'Great Homes and Destinations'],
            (object) ['key'=>'Health', 'name'=>'Health'],
            (object) ['key'=>'Home & Garden', 'name'=>'Home & Garden'],
            (object) ['key'=>'Home and Garden', 'name'=>'Home and Garden'],
            (object) ['key'=>'International Home', 'name'=>'International Home'],
            (object) ['key'=>'Job Market', 'name'=>'Job Market'],
            (object) ['key'=>'Learning', 'name'=>'Learning'],
            (object) ['key'=>'Magazine', 'name'=>'Magazine'],
            (object) ['key'=>'Movies', 'name'=>'Movies'],
            (object) ['key'=>'Multimedia', 'name'=>'Multimedia'],
            (object) ['key'=>'Multimedia/Photos', 'name'=>'Multimedia/Photos'],
            (object) ['key'=>'N.Y. / Region', 'name'=>'N.Y. / Region'],
            (object) ['key'=>'N.Y./Region', 'name'=>'N.Y./Region'],
            (object) ['key'=>'NYRegion', 'name'=>'NYRegion'],
            (object) ['key'=>'NYT Now', 'name'=>'NYT Now'],
            (object) ['key'=>'National', 'name'=>'National'],
            (object) ['key'=>'New York', 'name'=>'New York'],
            (object) ['key'=>'New York and Region', 'name'=>'New York and Region'],
            (object) ['key'=>'Obituaries', 'name'=>'Obituaries'],
            (object) ['key'=>'Olympics', 'name'=>'Olympics'],
            (object) ['key'=>'Open', 'name'=>'Open'],
            (object) ['key'=>'Opinion', 'name'=>'Opinion'],
            (object) ['key'=>'Paid Death Notices', 'name'=>'Paid Death Notices'],
            (object) ['key'=>'Public Editor', 'name'=>'Public Editor'],
            (object) ['key'=>'Real Estate', 'name'=>'Real Estate'],
            (object) ['key'=>'Science', 'name'=>'Science'],
            (object) ['key'=>'Sports', 'name'=>'Sports'],
            (object) ['key'=>'Style', 'name'=>'Style'],
            (object) ['key'=>'Sunday Magazine', 'name'=>'Sunday Magazine'],
            (object) ['key'=>'Sunday Review', 'name'=>'Sunday Review'],
            (object) ['key'=>'T Magazine', 'name'=>'T Magazine'],
            (object) ['key'=>'T:Style', 'name'=>'T:Style'],
            (object) ['key'=>'Technology', 'name'=>'Technology'],
            (object) ['key'=>'The Public Editor', 'name'=>'The Public Editor'],
            (object) ['key'=>'The Upshot', 'name'=>'The Upshot'],
            (object) ['key'=>'Theater', 'name'=>'Theater'],
            (object) ['key'=>'Times Topics', 'name'=>'Times Topics'],
            (object) ['key'=>'TimesMachine', 'name'=>'TimesMachine'],
            (object) ['key'=>"Today's Headlines", 'name'=>"Today's Headlines"],
            (object) ['key'=>'Topics', 'name'=>'Topics'],
            (object) ['key'=>'Travel', 'name'=>'Travel'],
            (object) ['key'=>'U.S.', 'name'=>'U.S.'],
            (object) ['key'=>'Universal', 'name'=>'Universal'],
            (object) ['key'=>'UrbanEye', 'name'=>'UrbanEye'],
            (object) ['key'=>'Washington', 'name'=>'Washington'],
            (object) ['key'=>'Week in Review', 'name'=>'Week in Review'],
            (object) ['key'=>'World', 'name'=>'World'],
            (object) ['key'=>'Your Money', 'name'=>'Your Money']
        ];
    
        return $categories;
    }
    
}
