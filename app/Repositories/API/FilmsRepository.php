<?php

namespace App\Repositories\API;

use App\DTOs\FilmDetail;
use App\DTOs\FilmSearchResult;
use App\Exceptions\StarWarsApiException;
use App\Repositories\FilmsRepositoryContract;
use App\Support\PathIdExtractor;
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
            $result = Http::get(config('sw-api.base_url').'films', [
                'title' => $title,
            ]);

            if (! $result->successful()) {
                throw new StarWarsApiException(
                    "Star Wars API request failed with status code: {$result->status()}"
                );
            }

            $items = $result->json('result') ?? [];

            foreach ($items as $item) {
                $searchResults[] = new FilmSearchResult(
                    id: $item['uid'],
                    title: $item['properties']['title']
                );
            }

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
            $result = Http::get(config('sw-api.base_url').'films'."/{$id}");

            if (! $result->successful()) {
                throw new StarWarsApiException(
                    "Star Wars API request failed with status code: {$result->status()}"
                );
            }

            $filmData = $result->json('result.properties');
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
