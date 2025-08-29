<?php

namespace Tests\Feature\Http\Controllers;

use App\DTOs\FilmDetail;
use App\DTOs\FilmSearchResult;
use App\DTOs\FullFilmDetail;
use App\DTOs\PeopleDetail;
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

    public function test_details_returns_response_as_expected(): void
    {
        $filmDetail = new FilmDetail(
            id: '1',
            title: 'A New Hope',
            openingCrawl: 'It is a period of civil war....',
            characterIds: [1, 2]
        );
        $person1 = new PeopleDetail(
            id: '1',
            name: 'Luke Skywalker',
            birthYear: '19BBY',
            gender: 'Male',
            eyeColor: 'blue',
            hairColor: 'blond',
            height: '172',
            mass: '77',
            filmIds: ['1', '2', '3']
        );
        $person2 = new PeopleDetail(
            id: '2',
            name: 'C-3PO',
            birthYear: '112BBY',
            gender: 'Male',
            eyeColor: 'yellow',
            hairColor: 'n/a',
            height: '167',
            mass: '75',
            filmIds: ['1', '2', '3', '4', '5', '6']
        );

        $fullFilmDetail = new FullFilmDetail($filmDetail, [$person1, $person2]);

        $service = Mockery::mock(FilmsServiceContract::class);
        $service->shouldReceive('getById')
            ->once()
            ->with('1')
            ->andReturn($fullFilmDetail);

        $this->app->instance(FilmsServiceContract::class, $service);

        $response = $this->getJson('/api/v1/films/1');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'opening_crawl',
                    'characters' => [
                        '*' => [
                            'id',
                            'name',
                        ],
                    ],
                ],
            ])
            ->assertJson(fn (AssertableJson $json) => $json->has('data', 4)
                ->where('data.id', $filmDetail->id)
                ->where('data.title', $filmDetail->title)
                ->where('data.opening_crawl', $filmDetail->openingCrawl)
                ->where('data.characters.0.id', $person1->id)
                ->where('data.characters.0.name', $person1->name)
                ->where('data.characters.1.id', $person2->id)
                ->where('data.characters.1.name', $person2->name)
            );
    }
}
