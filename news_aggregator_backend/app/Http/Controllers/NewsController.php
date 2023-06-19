<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use jcobhams\NewsApi\NewsApi;
use Illuminate\Support\Facades\Http;


use App\Libraries\NewsApiLibrary;
use App\Libraries\NyTimesLibrary;
use App\Libraries\GuardianLibrary;


class NewsController extends Controller
{

    public function getCombinedNews(Request $request)
    {
        $this->validate($request, [
            'q' => 'string',
            'sources' => 'string',
            'country' => 'string|in:ae,ar,at,au,be,bg,br,ca,ch,cn,co,cu,cz,de,eg,fr,gb,gr,hk,hu,id,ie,il,in,it,jp,kr,lt,lv,ma,mx,my,ng,nl,no,nz,ph,pl,pt,ro,rs,ru,sa,se,sg,si,sk,th,tr,tw,ua,us,ve,za',
            'category' => 'string|in:business,entertainment,general,health,science,sports,technology',
            'page_size' => 'integer',
            'page' => 'integer',
        ]);


        $q = $request->input('q');
        $sources = $request->input('sources');
        $country = $request->input('country');
        $category = $request->input('category');
        $page_size = $request->input('page_size');
        $page = $request->input('page');

        // Get articles from NewsAPI
        $newsAPI = new NewsApiLibrary();
        $newsApiArticles = $newsAPI->getArticles($q, $sources, $country, $category, $page_size, $page);
        // return response()->json($newsApiArticles);

        $nyTimesAPI = new NyTimesLibrary();
        $nyTimesArticles = $nyTimesAPI->getArticles();
        // return response()->json($nyTimesArticles);

        $GuardianAPI = new GuardianLibrary();
        $guardianArticles = $GuardianAPI->getArticles();
        // return response()->json($guardianArticles);
        $combinedNews = $this->newsCombiner([
            "NewsAPI" => $newsApiArticles,
            "NYTimesAPI" => $nyTimesArticles,
            "GuardianAPI" => $guardianArticles,
        ]);

        return response()->json($combinedNews);
    }

    private function newsCombiner($sources){
        $combinedNews = [];

        foreach($sources as $source => $articles){
            switch($source){
                case "NewsAPI":
                    foreach($articles as $article){
                        array_push($combinedNews, (object) [
                            "id" => $article->source->id . '-' . $article->title,
                            "source" => $article->source->name,
                            "title" => $article->title,
                            "author" => $article->author,
                            "publishedAt" => $article->publishedAt,
                            "url" => $article->url,
                        ]);
                    }
                    break;
                
                case "NYTimesAPI":
                    foreach($articles as $article){
                        $datetime = new \DateTime($article->pub_date);
                        array_push($combinedNews, (object) [
                            "id" => $article->_id,
                            "source" => $article->source,
                            "title" => $article->headline->main,
                            "author" => $article->byline->person[0]->firstname . ' ' . $article->byline->person[0]->middlename . ' ' . $article->byline->person[0]->lastname,
                            "publishedAt" => $datetime->format('Y-m-d\TH:i:s\Z'),
                            "url" => $article->web_url,
                        ]);
                    }
                    break;

                case "GuardianAPI":
                    foreach($articles as $article){
                        array_push($combinedNews, (object) [
                            "id" => $article->id,
                            "source" => "Guardian",
                            "title" => $article->webTitle,
                            "author" => null,
                            "publishedAt" => $article->webPublicationDate,
                            "url" => $article->webUrl,
                        ]);
                    }
                    break;
            }
        }
        usort($combinedNews, array($this, 'comparePublishedAt'));

        return $combinedNews;
    }
    private function comparePublishedAt($a, $b) {
        $publishedAtA = strtotime($a->publishedAt);
        $publishedAtB = strtotime($b->publishedAt);
    
        return $publishedAtB - $publishedAtA;
    }
}
