<?php

namespace App\Repositories\API;

use App\DTOs\FilmDetail;
use App\DTOs\FilmSearchResult;
use App\Exceptions\StarWarsApiException;
use App\Repositories\FilmsRepositoryContract;
use App\Support\PathIdExtractor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

class FilmsRepository implements FilmsRepositoryContract
{
    /**
     * @return FilmSearchResult[]
     *
     * @throws StarWarsApiException
     */
    public function searchByTitle(string $title): array
    {
        try {
            $searchResults = [];
            $cacheKey = 'film_search:'.md5($title);
            $cached = Cache::get($cacheKey);

            if ($cached !== null) {
                foreach ($cached as $cachedItem) {
                    $searchResults[] = new FilmSearchResult(
                        id: $cachedItem['id'],
                        title: $cachedItem['title']
                    );
                }

                return $searchResults;
            }

            $result = Http::get(config('sw-api.base_url').'films', [
                'title' => $title,
            ]);

            if (! $result->successful()) {
                throw new StarWarsApiException(
                    "Star Wars API request failed with status code: {$result->status()}"
                );
            }

            $items = $result->json('result') ?? [];
            $toCache = [];
            foreach ($items as $item) {
                $filmResult = new FilmSearchResult(
                    id: $item['uid'],
                    title: $item['properties']['title']
                );
                $searchResults[] = $filmResult;
                $toCache[] = [
                    'id' => $filmResult->id,
                    'title' => $filmResult->title,
                ];
            }

            Cache::put($cacheKey, $toCache, now()->addMinutes(config('sw-api.cache_ttl')));

            return $searchResults;
        } catch (Throwable $throwable) {
            throw new StarWarsApiException(
                message: "Error: {$throwable->getMessage()}",
                previous: $throwable
            );
        }
    }

    /**
     * @throws StarWarsApiException
     */
    public function getById(int $id): FilmDetail
    {
        try {
            $cacheKey = "film:{$id}";

            if (Cache::has($cacheKey)) {
                $filmData = Cache::get($cacheKey);
            } else {
                $result = Http::get(config('sw-api.base_url').'films'."/{$id}");

                if (! $result->successful()) {
                    throw new StarWarsApiException(
                        "Star Wars API request failed with status code: {$result->status()}"
                    );
                }

                $filmData = $result->json('result.properties');

                Cache::put($cacheKey, $filmData, now()->addMinutes(config('sw-api.cache_ttl')));
            }

            $characterUrls = $filmData['characters'] ?? [];
            $characterIds = $this->extractCharacterIdsFromUrls($characterUrls);

            return new FilmDetail(
                id: $id,
                title: $filmData['title'] ?? '',
                openingCrawl: $filmData['opening_crawl'] ?? '',
                characterIds: $characterIds,
            );
        } catch (Throwable $throwable) {
            throw new StarWarsApiException(
                message: "Error: {$throwable->getMessage()}",
                previous: $throwable
            );
        }
    }

    /**
     * @param  string[]  $characterUrls
     * @return array <int>
     */
    private function extractCharacterIdsFromUrls(array $characterUrls): array
    {
        $characterIds = [];

        foreach ($characterUrls as $characterUrl) {
            $extractedId = PathIdExtractor::extractFromUrl($characterUrl, 'people');

            if ($extractedId !== null) {
                $characterIds[] = $extractedId;
            }
        }

        return $characterIds;
    }
}
