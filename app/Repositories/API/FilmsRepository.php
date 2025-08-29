<?php

namespace App\Repositories\API;

use App\DTOs\FilmDetail;
use App\Exceptions\StarWarsApiException;
use App\Repositories\FilmsRepositoryContract;
use Illuminate\Support\Facades\Http;
use Throwable;

class FilmsRepository implements FilmsRepositoryContract
{
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

            return new FilmDetail(
                id: $id,
                title: $filmData['title'] ?? '',
            );
        } catch (Throwable $throwable) {
            throw new StarWarsApiException(
                message: "Error: {$throwable->getMessage()}",
                previous: $throwable
            );
        }
    }
}
