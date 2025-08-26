<?php

namespace App\Repositories\API;

use App\DTOs\PeopleSearchResult;
use App\Exceptions\StarWarsApiException;
use App\Repositories\PeopleRepositoryContract;
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
}
