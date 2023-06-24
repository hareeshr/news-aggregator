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
    /**
     * Get home articles.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHomeArticles(Request $request)
    {
        return $this->getAllHomeArticles();
    }

    /**
     * Get all home articles from different sources.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function getAllHomeArticles()
    {
        $newsAPIlib = new NewsApiLibrary();
        $newsApiArticles = $newsAPIlib->getArticles();

        $nyTimesAPIlib = new NyTimesLibrary();
        $nyTimesArticles = $nyTimesAPIlib->getArticles();

        $guardianAPIlib = new GuardianLibrary();
        $guardianArticles = $guardianAPIlib->getArticles();

        $combinedNews = $this->newsCombiner([
            "NewsAPI" => $newsApiArticles,
            "NYTimesAPI" => $nyTimesArticles,
            "GuardianAPI" => $guardianArticles,
        ]);

        return response()->json($combinedNews);
    }

    /**
     * Get personalized articles for the authenticated user.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
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

            if (!$preferenceData->NewsAPI && !$preferenceData->NyTimes && !$preferenceData->Guardian) {
                return $this->getAllHomeArticles();
            }

            if ($preferenceData->NewsAPI) {
                $categories = implode(", ", array_column($preferenceData->NewsAPICategories, "key"));
                $newsAPIlib = new NewsApiLibrary();
                $newsApiArticles = $categories ? $newsAPIlib->getPersonalizedArticles($categories) : $newsAPIlib->getArticles();
                $newsArticles["NewsAPI"] = $newsApiArticles;
            }

            if ($preferenceData->NyTimes) {
                $categories = implode('", "', array_column($preferenceData->NyTimesCategories, "key"));
                $nyTimesAPIlib = new NyTimesLibrary();
                $nyTimesArticles = $categories ? $nyTimesAPIlib->getPersonalizedArticles($categories) : $nyTimesAPIlib->getArticles();
                $newsArticles["NYTimesAPI"] = $nyTimesArticles;
            }

            if ($preferenceData->Guardian) {
                $categories = implode('|', array_column($preferenceData->GuardianCategories, "key"));
                $guardianAPIlib = new GuardianLibrary();
                $guardianArticles = $categories ? $guardianAPIlib->getPersonalizedArticles($categories) : $guardianAPIlib->getArticles();
                $newsArticles["GuardianAPI"] = $guardianArticles;
            }

            $combinedNews = $this->newsCombiner($newsArticles);
 
            return response()->json($combinedNews);
        // } catch (\Exception $e) {
        //     return response()->json(['message' => 'Failed to fetch personalized articles', 'error' => $e], 500);
        // }
    }

    /**
     * Search articles based on query parameters.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
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

            $validator = Validator::make($request->query(), $rules);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $validatedData = $validator->validated();
            $q = $validatedData['q'] ?? null;
            $startDate = $validatedData['startDate'] ?? null;
            $endDate = $validatedData['endDate'] ?? null;
            $newsApi = isset($validatedData['NewsAPI']) ? filter_var($validatedData['NewsAPI'], FILTER_VALIDATE_BOOLEAN) : false;
            $newsApiCategories = $validatedData['NewsAPICategories'] ?? null;
            $nyTimes = isset($validatedData['NyTimes']) ? filter_var($validatedData['NyTimes'], FILTER_VALIDATE_BOOLEAN) : false;
            $nyTimesCategories = $validatedData['NyTimesCategories'] ?? null;
            $guardian = isset($validatedData['Guardian']) ? filter_var($validatedData['Guardian'], FILTER_VALIDATE_BOOLEAN) : false;
            $guardianCategories = $validatedData['GuardianCategories'] ?? null;

            if (!$newsApi && !$nyTimes && !$guardian) {
                $newsApi = $nyTimes = $guardian = true;
            }

            $newsArticles = [];

            if ($newsApi) {
                $newsAPIlib = new NewsApiLibrary();
                $newsApiArticles = $newsAPIlib->search($q, $newsApiCategories, $startDate, $endDate);
                $newsArticles["NewsAPI"] = $newsApiArticles;
            }

            if ($nyTimes) {
                $nyTimesAPIlib = new NyTimesLibrary();
                $nyTimesArticles = $nyTimesAPIlib->search($q, $nyTimesCategories, $startDate, $endDate);
                $newsArticles["NYTimesAPI"] = $nyTimesArticles;
            }

            if ($guardian) {
                $guardianAPIlib = new GuardianLibrary();
                $guardianArticles = $guardianAPIlib->search($q, str_replace(",", "|", $guardianCategories), $startDate, $endDate);
                $newsArticles["GuardianAPI"] = $guardianArticles;
            }

            $combinedNews = $this->newsCombiner($newsArticles);
            return response()->json($combinedNews);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch personalized articles', 'error' => $e], 500);
        }
    }

    /**
     * Get categories from different news sources.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCategories(Request $request)
    {
        $newsAPI = new NewsApiLibrary();
        $nyTimesAPI = new NyTimesLibrary();
        $guardianAPI = new GuardianLibrary();

        $categories = (object) [
            'NewsAPI' => $newsAPI->getCategories(),
            'NyTimes' => $nyTimesAPI->getCategories(),
            'Guardian' => $guardianAPI->getCategories()
        ];

        return response()->json($categories);
    }

    /**
     * Combine and sort news articles from different sources.
     *
     * @param  array  $sources
     * @return array
     */
    private function newsCombiner($sources)
    {
        $combinedNews = [];

        foreach ($sources as $source => $articles) {
            switch ($source) {
                case "NewsAPI":
                    foreach ($articles as $article) {
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
                    foreach ($articles as $article) {
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
                    foreach ($articles as $article) {
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

        usort($combinedNews, function ($a, $b) {
            $publishedAtA = strtotime($a->publishedAt);
            $publishedAtB = strtotime($b->publishedAt);

            return $publishedAtB - $publishedAtA;
        });

        return $combinedNews;
    }
}
