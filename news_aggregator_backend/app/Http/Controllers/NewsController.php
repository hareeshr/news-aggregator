<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Libraries\NewsApiLibrary;
use App\Libraries\NyTimesLibrary;
use App\Libraries\GuardianLibrary;


class NewsController extends Controller
{

    public function getHomeArticles(Request $request)
    {
        return $this->allHomeArticles();
    }
    private function  allHomeArticles(){

        // Get articles from NewsAPI
        $newsAPIlib = new NewsApiLibrary();
        $newsApiArticles = $newsAPIlib->getArticles();

        $nyTimesAPIlib = new NyTimesLibrary();
        $nyTimesArticles = $nyTimesAPIlib->getArticles();

        $GuardianAPIlib = new GuardianLibrary();
        $guardianArticles = $GuardianAPIlib->getArticles();

        $combinedNews = $this->newsCombiner([
            "NewsAPI" => $newsApiArticles,
            "NYTimesAPI" => $nyTimesArticles,
            "GuardianAPI" => $guardianArticles,
        ]);

        return response()->json($combinedNews);
    }
    public function getPersonalizedArticles(Request $request)
    {
        try {
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
                
                $newsAPIlib = new NewsApiLibrary();
                if($categories == "")
                    $newsApiArticles = $newsAPIlib->getArticles();
                else
                    $newsApiArticles = $newsAPIlib->getPersonalizedArticles($categories);
                $newsArticles["NewsAPI"] = $newsApiArticles;
            }

            if($preferenceData->NyTimes == true){
                $categories = implode('", "', array_column($preferenceData->NyTimesCategories, "key"));
                
                $nyTimesAPIlib = new NyTimesLibrary();
                if($categories == "")
                    $nyTimesArticles = $nyTimesAPIlib->getArticles();
                else
                    $nyTimesArticles = $nyTimesAPIlib->getPersonalizedArticles($categories);

                $newsArticles["NYTimesAPI"] = $nyTimesArticles;
            }
            
            if($preferenceData->Guardian == true){
                $categories = implode('|', array_column($preferenceData->GuardianCategories, "key"));
                
                $GuardianAPIlib = new GuardianLibrary();
                if($categories == "")
                    $guardianArticles = $GuardianAPIlib->getArticles();
                else
                    $guardianArticles = $GuardianAPIlib->getPersonalizedArticles($categories);

                    $newsArticles["GuardianAPI"] = $guardianArticles;
            }

            $combinedNews = $this->newsCombiner($newsArticles);
 
            return response()->json($combinedNews);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch personalized articles','error' => $e], 500);
        }
    }

    
    public function search(Request $request)
    {
        try {

            // Define the validation rules for the query parameters
            $rules = [
                'q' => 'nullable|string',
                'startDate' => 'nullable|date',
                'endDate' => 'nullable|date',
                'NewsAPI' => 'nullable|string|in:true,false',
                'NewsAPICategories' => 'nullable|string',
                'NyTimes' => 'nullable|string|in:true,false',
                'NyTimesCategories' => 'nullable|string',
                'Guardian' => 'nullable|string|in:true,false',
                'GuardianCategories' => 'nullable|string',
            ];

            // Create a validator instance
            $validator = Validator::make($request->query(), $rules);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            // Retrieve the validated query parameters
            $validatedData = $validator->validated();

            // Assign the validated query parameters to variables with null as default value
            $q = $validatedData['q'] ?? null;
            $startDate = $validatedData['startDate'] ?? null;
            $endDate = $validatedData['endDate'] ?? null;
            $newsApi = isset($validatedData['NewsAPI']) ? filter_var($validatedData['NewsAPI'], FILTER_VALIDATE_BOOLEAN) : false;
            $newsApiCategories = $validatedData['NewsAPICategories'] ?? null;
            $nyTimes = isset($validatedData['NyTimes']) ? filter_var($validatedData['NyTimes'], FILTER_VALIDATE_BOOLEAN) : false;
            $nyTimesCategories = $validatedData['NyTimesCategories'] ?? null;
            $guardian = isset($validatedData['Guardian']) ? filter_var($validatedData['Guardian'], FILTER_VALIDATE_BOOLEAN) : false;
            $guardianCategories = $validatedData['GuardianCategories'] ?? null;


            if($newsApi == false
                && $nyTimes == false
                && $guardian == false){
                    $newsApi = $nyTimes = $guardian = true;
                }


            if($newsApi == true){                
                $newsAPIlib = new NewsApiLibrary();
                $newsApiArticles = $newsAPIlib->search($q, $newsApiCategories, $startDate, $endDate);
                $newsArticles["NewsAPI"] = $newsApiArticles;
            }

            if($nyTimes == true){
                $nyTimesAPIlib = new NyTimesLibrary();
                $nyTimesArticles = $nyTimesAPIlib->search($q, $nyTimesCategories, $startDate, $endDate);
                $newsArticles["NYTimesAPI"] = $nyTimesArticles;
            }
            
            if($guardian == true){
                $GuardianAPIlib = new GuardianLibrary();
                $guardianArticles = $GuardianAPIlib->search($q, str_replace(",", "|", $guardianCategories), $startDate, $endDate);
                $newsArticles["GuardianAPI"] = $guardianArticles;
            }
            $combinedNews = $this->newsCombiner($newsArticles);
 
            return response()->json($combinedNews);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch personalized articles','error' => $e], 500);
        }
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
