<?php

namespace Tests\Feature\Http\Controllers;

use App\DTOs\FilmDetail;
use App\DTOs\FullPeopleDetail;
use App\DTOs\PeopleDetail;
use App\DTOs\PeopleSearchResult;
use App\Enums\ErrorIdentifierEnum;
use App\Exceptions\Service\SearchFailedException;
use App\Services\PeopleServiceContract;
use Illuminate\Testing\Fluent\AssertableJson;
use Mockery;
use Tests\TestCase;

final class PeopleControllerTest extends TestCase
{
    public function test_search_returns_response_as_expected(): void
    {
        $keyword = 'luke';
        $service = Mockery::mock(PeopleServiceContract::class);
        $service->shouldReceive('searchPeopleByName')
            ->once()
            ->with($keyword)
            ->andReturn([
                new PeopleSearchResult(id: '1', name: 'Luke Skywalker'),
                new PeopleSearchResult(id: '64', name: 'Luminara Unduli'),
            ]);

        $this->app->instance(PeopleServiceContract::class, $service);

        $response = $this->getJson("/api/v1/people/search?name={$keyword}");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ])
            ->assertJson(fn (AssertableJson $json) => $json->has('data', 2)
                ->where('data.0.id', '1')
                ->where('data.0.name', 'Luke Skywalker')
                ->where('data.1.id', '64')
                ->where('data.1.name', 'Luminara Unduli')
            );
    }

    public function test_search_validates_input(): void
    {
        $response = $this->getJson('/api/v1/people/search');

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_search_returns_error_when_service_fails(): void
    {
        $keyword = 'vader';
        $service = Mockery::mock(PeopleServiceContract::class);
        $service->shouldReceive('searchPeopleByName')
            ->once()
            ->with($keyword)
            ->andThrow(new SearchFailedException('internal error'));

        $this->app->instance(PeopleServiceContract::class, $service);

        $response = $this->getJson("/api/v1/people/search?name={$keyword}");

        $response
            ->assertServiceUnavailable()
            ->assertJson([
                'identifier' => ErrorIdentifierEnum::PeopleSearchFailed->value,
                'message' => 'Unable to complete search at this time. Please try again later.',
            ]);
    }

    public function test_details_returns_response_as_expected(): void
    {
        $peopleDetail = new PeopleDetail(
            id: '64',
            name: 'Luminara Unduli',
            birthYear: '58BBY',
            gender: 'female',
            eyeColor: 'blue',
            hairColor: 'black',
            height: '170',
            mass: '56.2',
            filmIds: [
                '5',
                '6',
            ]
        );
        $film1 = new FilmDetail('5', 'Attack of the Clones');
        $film2 = new FilmDetail('6', 'Revenge of the Sith');

        $fullPeopleDetail = new FullPeopleDetail(
            people: $peopleDetail,
            films: [$film1, $film2]
        );

        $id = '64';
        $service = Mockery::mock(PeopleServiceContract::class);
        $service->shouldReceive('getById')
            ->once()
            ->with($id)
            ->andReturn($fullPeopleDetail);

        $this->app->instance(PeopleServiceContract::class, $service);

        $response = $this->getJson("/api/v1/people/{$id}");

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'birth_year',
                    'gender',
                    'eye_color',
                    'hair_color',
                    'height',
                    'mass',
                    'films' => [
                        '*' => [
                            'id',
                            'title',
                        ],
                    ],
                ],
            ])
            ->assertJson(fn (AssertableJson $json) => $json->has('data', 9)
                ->where('data.id', $peopleDetail->id)
                ->where('data.name', $peopleDetail->name)
                ->where('data.films.0.id', $film1->id)
                ->where('data.films.0.title', $film1->title)
                ->where('data.films.1.id', $film2->id)
                ->where('data.films.1.title', $film2->title)
            );
    }
}
