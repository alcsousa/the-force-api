<?php

namespace Tests\Feature\Repositories\API;

use App\DTOs\PeopleDetail;
use App\DTOs\PeopleSearchResult;
use App\Exceptions\StarWarsApiException;
use App\Repositories\API\PeopleRepository;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class PeopleRepositoryTest extends TestCase
{
    public function test_search_people_by_name_returns_results(): void
    {
        $keyword = 'Lu';
        Http::fake([
            'swapi.tech/api/people*' => Http::response([
                'result' => [
                    [
                        'uid' => '1',
                        'properties' => [
                            'name' => 'Luke Skywalker',
                        ],
                    ],
                    [
                        'uid' => '64',
                        'properties' => [
                            'name' => 'Luminara Unduli',
                        ],
                    ],
                ],
            ], Response::HTTP_OK),
        ]);

        $results = (new PeopleRepository)->searchPeopleByName($keyword);

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(PeopleSearchResult::class, $results);
        $this->assertSame('1', $results[0]->id);
        $this->assertSame('Luke Skywalker', $results[0]->name);
        $this->assertSame('64', $results[1]->id);
        $this->assertSame('Luminara Unduli', $results[1]->name);

        Http::assertSent(function ($request) use ($keyword) {
            return $request->url() === "https://www.swapi.tech/api/people?name={$keyword}"
                && $request->method() === 'GET'
                && $request->data()['name'] === $keyword;
        });
    }

    public function test_search_people_by_name_handles_empty_results(): void
    {
        Http::fake([
            'swapi.tech/api/people*' => Http::response([
                'result' => [],
            ], Response::HTTP_OK),
        ]);

        $results = (new PeopleRepository)->searchPeopleByName('Vito Corleone');

        $this->assertIsArray($results);
        $this->assertCount(0, $results);
    }

    public function test_search_people_by_name_throws_exception_on_non_success_status(): void
    {
        Http::fake([
            'swapi.tech/api/people*' => Http::response([
                'message' => 'Server error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR),
        ]);

        $this->expectException(StarWarsApiException::class);
        $this->expectExceptionMessage('Star Wars API request failed with status code: 500');

        (new PeopleRepository)->searchPeopleByName('Luke');
    }

    public function test_search_people_by_name_wraps_non_sw_api_related_errors(): void
    {
        Http::fake([
            'swapi.tech/api/people*' => Http::response(function () {
                throw new RuntimeException('Something went wrong');
            }),
        ]);

        $this->expectException(StarWarsApiException::class);
        $this->expectExceptionMessage('Error: Something went wrong');

        (new PeopleRepository)->searchPeopleByName('Lu');
    }

    public function test_get_details_returns_people_detail()
    {
        $id = '64';
        Http::fake([
            "swapi.tech/api/people/{$id}" => Http::response([
                'result' => [
                    'properties' => [
                        'name' => 'Luminara Unduli',
                        'birth_year' => '58BBY',
                        'gender' => 'female',
                        'eye_color' => 'blue',
                        'hair_color' => 'black',
                        'height' => '170',
                        'mass' => '56.2',
                        'films' => [
                            'https://www.swapi.tech/api/films/5',
                            'https://www.swapi.tech/api/films/6',
                        ],
                    ],
                ],
            ], Response::HTTP_OK),
        ]);

        $detail = (new PeopleRepository)->getDetails($id);

        $this->assertInstanceOf(PeopleDetail::class, $detail);
        $this->assertEquals('64', $detail->id);
        $this->assertEquals('Luminara Unduli', $detail->name);
        $this->assertSame([5, 6], $detail->filmIds);
    }
}
