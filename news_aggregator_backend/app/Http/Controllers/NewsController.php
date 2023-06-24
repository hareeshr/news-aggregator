<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Libraries\NewsApiLibrary;
use App\Libraries\NyTimesLibrary;
use App\Libraries\GuardianLibrary;
use Illuminate\Support\Facades\Log;



class NewsController extends Controller
{

    public function getHomeArticles(Request $request)
    {
        // $this->validate($request, [
        //     'q' => 'string',
        //     'sources' => 'string',
        //     'country' => 'string|in:ae,ar,at,au,be,bg,br,ca,ch,cn,co,cu,cz,de,eg,fr,gb,gr,hk,hu,id,ie,il,in,it,jp,kr,lt,lv,ma,mx,my,ng,nl,no,nz,ph,pl,pt,ro,rs,ru,sa,se,sg,si,sk,th,tr,tw,ua,us,ve,za',
        //     'category' => 'string|in:business,entertainment,general,health,science,sports,technology',
        //     'page_size' => 'integer',
        //     'page' => 'integer',
        // ]);
        return $this->allHomeArticles();
    }
    private function  allHomeArticles(){

        // Get articles from NewsAPI
        $newsAPI = new NewsApiLibrary();
        $newsApiArticles = $newsAPI->getArticles();
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
    public function getPersonalizedArticles(Request $request)
    {
        // try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $newsArticles = [];
            $preference = $user->preference;
            $preferenceData = json_decode($preference->preference_data, false);

            if($preferenceData->NewsAPI == false
                && $preferenceData->NyTimes == false
                && $preferenceData->Guardian == false){
                    return $this->allHomeArticles();
                }

            if($preferenceData->NewsAPI == true){
                $categories = implode(", ", array_column($preferenceData->NewsAPICategories, "key"));
                
                $newsAPI = new NewsApiLibrary();
                if($categories == "")
                    $newsApiArticles = $newsAPI->getArticles();
                else
                    $newsApiArticles = $newsAPI->getPersonalizedArticles($categories);
                $newsArticles["NewsAPI"] = $newsApiArticles;
            }

            if($preferenceData->NyTimes == true){
                $categories = implode('", "', array_column($preferenceData->NyTimesCategories, "key"));
                
                $nyTimesAPI = new NyTimesLibrary();
                if($categories == "")
                    $nyTimesArticles = $nyTimesAPI->getArticles();
                else
                    $nyTimesArticles = $nyTimesAPI->getPersonalizedArticles($categories);

                $newsArticles["NYTimesAPI"] = $nyTimesArticles;
            }
            
            if($preferenceData->Guardian == true){
                $categories = implode('|', array_column($preferenceData->GuardianCategories, "key"));
                
                $GuardianAPI = new GuardianLibrary();
                if($categories == "")
                    $guardianArticles = $GuardianAPI->getArticles();
                else
                    $guardianArticles = $GuardianAPI->getPersonalizedArticles($categories);

                    $newsArticles["GuardianAPI"] = $guardianArticles;
            }

            $combinedNews = $this->newsCombiner($newsArticles);
 
            return response()->json($combinedNews);
        // } catch (\Exception $e) {
        //     return response()->json(['message' => 'Failed to fetch personalized articles','error' => $e], 500);
        // }
    }

    public function getCategories(Request $request) {
        $newsAPI = new NewsApiLibrary();
        $nyTimesAPI = new NyTimesLibrary();
        $GuardianAPI = new GuardianLibrary();

        $categories = (object) [
            'NewsAPI' => $newsAPI->getCategories(),
            'NyTimes' => $nyTimesAPI->getCategories(),
            'Guardian' => $GuardianAPI->getCategories()
        ];

        return $categories;
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
                        $author = null;
                        if (array_key_exists(0, $article->byline->person))
                            $author = (array_key_exists(0, $article->byline->person)) ? $article->byline->person[0]->firstname . ' ' . $article->byline->person[0]->middlename . ' ' . $article->byline->person[0]->lastname : '';
                        array_push($combinedNews, (object) [
                            "id" => $article->_id,
                            "source" => $article->source,
                            "title" => $article->headline->main,
                            "author" => $author,
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
