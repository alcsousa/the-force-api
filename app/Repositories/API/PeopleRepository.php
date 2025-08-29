<?php

namespace App\Repositories\API;

use App\DTOs\PeopleDetail;
use App\DTOs\PeopleSearchResult;
use App\Exceptions\StarWarsApiException;
use App\Repositories\PeopleRepositoryContract;
use App\Support\PathIdExtractor;
use Illuminate\Support\Facades\Http;
use Throwable;

class PeopleRepository implements PeopleRepositoryContract
{
    /**
     * @return PeopleSearchResult[]
     *
     * @throws StarWarsApiException
     */
    public function searchPeopleByName(string $name): array
    {
        try {
            $searchResults = [];
            $result = Http::get(config('sw-api.base_url').'people', [
                'name' => $name,
            ]);

            if (! $result->successful()) {
                throw new StarWarsApiException(
                    "Star Wars API request failed with status code: {$result->status()}"
                );
            }

            $items = $result->json('result') ?? [];

            foreach ($items as $item) {
                $searchResults[] = new PeopleSearchResult(
                    id: $item['uid'],
                    name: $item['properties']['name']
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
    public function getDetails(string $id): PeopleDetail
    {
        try {
            $result = Http::get(config('sw-api.base_url').'people'."/{$id}");

            if (! $result->successful()) {
                throw new StarWarsApiException(
                    "Star Wars API request failed with status code: {$result->status()}"
                );
            }

            $item = $result->json('result.properties');
            $filmUrls = $item['films'] ?? [];
            $filmIds = $this->extractFilmIdsFromUrls($filmUrls);

            return new PeopleDetail(
                id: $id,
                name: $item['name'] ?? '',
                birthYear: $item['birth_year'] ?? '',
                gender: $item['gender'] ?? '',
                eyeColor: $item['eye_color'] ?? '',
                hairColor: $item['hair_color'] ?? '',
                height: $item['height'] ?? '',
                mass: $item['mass'] ?? '',
                filmIds: $filmIds
            );
        } catch (Throwable $throwable) {
            throw new StarWarsApiException(
                message: "Error: {$throwable->getMessage()}",
                previous: $throwable
            );
        }
    }

    /**
     * @param  string[]  $filmUrls
     * @return array <int>
     */
    private function extractFilmIdsFromUrls(array $filmUrls): array
    {
        $filmIds = [];

        foreach ($filmUrls as $filmUrl) {
            $extractedId = PathIdExtractor::extractFromUrl($filmUrl, 'films');

            if ($extractedId !== null) {
                $filmIds[] = $extractedId;
            }
        }

        return $filmIds;
    }
}
