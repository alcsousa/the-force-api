<?php

namespace Tests\Feature\Http\Controllers;

use App\DTOs\FilmSearchResult;
use App\Enums\ErrorIdentifierEnum;
use App\Exceptions\Service\SearchFailedException;
use App\Services\FilmsServiceContract;
use Illuminate\Testing\Fluent\AssertableJson;
use Mockery;
use Tests\TestCase;

class FilmsControllerTest extends TestCase
{
    public function test_search_returns_response_as_expected(): void
    {
        $keyword = 'jedi';
        $service = Mockery::mock(FilmsServiceContract::class);
        $service->shouldReceive('searchFilmByTitle')
            ->once()
            ->with($keyword)
            ->andReturn([
                new FilmSearchResult(id: '1', title: 'Return of the Jedi'),
                new FilmSearchResult(id: '2', title: 'Jedi: Fallen Order'),
            ]);

        $this->app->instance(FilmsServiceContract::class, $service);

        $response = $this->getJson("/api/v1/films/search?title={$keyword}");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                    ],
                ],
            ])
            ->assertJson(fn (AssertableJson $json) => $json->has('data', 2)
                ->where('data.0.id', 1)
                ->where('data.0.title', 'Return of the Jedi')
                ->where('data.1.id', 2)
                ->where('data.1.title', 'Jedi: Fallen Order')
            );
    }

    public function test_search_validates_input(): void
    {
        $response = $this->getJson('/api/v1/films/search');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    public function test_search_returns_error_when_service_fails(): void
    {
        $keyword = 'jedi';
        $service = Mockery::mock(FilmsServiceContract::class);
        $service->shouldReceive('searchFilmByTitle')
            ->once()
            ->with($keyword)
            ->andThrow(
                new SearchFailedException(
                    errorIdentifierEnum: ErrorIdentifierEnum::FilmSearchFailed,
                    message: 'internal error',
                )
            );

        $this->app->instance(FilmsServiceContract::class, $service);

        $response = $this->getJson("/api/v1/films/search?title={$keyword}");

        $response
            ->assertServiceUnavailable()
            ->assertJson([
                'identifier' => ErrorIdentifierEnum::FilmSearchFailed->value,
                'message' => 'Unable to complete search at this time. Please try again later.',
            ]);
    }
}
