<?php

namespace Tests\Feature\Repositories\API;

use App\DTOs\FilmDetail;
use App\DTOs\FilmSearchResult;
use App\Exceptions\StarWarsApiException;
use App\Repositories\API\FilmsRepository;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class FilmsRepositoryTest extends TestCase
{
    public function test_search_by_title_returns_results(): void
    {
        $keyword = 'Jedi';
        Http::fake([
            'swapi.tech/api/films*' => Http::response([
                'result' => [
                    [
                        'uid' => '3',
                        'properties' => [
                            'title' => 'Return of the Jedi',
                        ],
                    ],
                    [
                        'uid' => '6',
                        'properties' => [
                            'title' => 'The Jedi Path',
                        ],
                    ],
                ],
            ], Response::HTTP_OK),
        ]);

        $results = (new FilmsRepository)->searchByTitle($keyword);

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(FilmSearchResult::class, $results);
        $this->assertSame(3, $results[0]->id);
        $this->assertSame('Return of the Jedi', $results[0]->title);
        $this->assertSame(6, $results[1]->id);
        $this->assertSame('The Jedi Path', $results[1]->title);

        Http::assertSent(function ($request) use ($keyword) {
            return $request->url() === "https://www.swapi.tech/api/films?title={$keyword}"
                && $request->method() === 'GET'
                && $request->data()['title'] === $keyword;
        });
    }

    public function test_search_by_title_throws_exception_on_non_success_status(): void
    {
        Http::fake([
            'swapi.tech/api/films*' => Http::response([
                'message' => 'Server error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR),
        ]);

        $this->expectException(StarWarsApiException::class);
        $this->expectExceptionMessage('Star Wars API request failed with status code: 500');

        (new FilmsRepository)->searchByTitle('Jedi');
    }

    public function test_get_by_id_returns_film_detail_on_successful_response(): void
    {
        $filmId = '3';
        $expectedTitle = 'Return of the Jedi';
        $openingCrawl = 'Luke Skywalker has returned to his home planet of Tatooine...';
        $characters = [
            'https://www.swapi.tech/api/people/1',
            'https://www.swapi.tech/api/people/2',
            'https://www.swapi.tech/api/people/3',
        ];

        Http::fake([
            "swapi.tech/api/films/{$filmId}" => Http::response([
                'result' => [
                    'properties' => [
                        'title' => $expectedTitle,
                        'opening_crawl' => $openingCrawl,
                        'characters' => $characters,
                    ],
                ],
            ], Response::HTTP_OK),
        ]);

        $result = (new FilmsRepository)->getById($filmId);

        $this->assertInstanceOf(FilmDetail::class, $result);
        $this->assertEquals($filmId, $result->id);
        $this->assertEquals($expectedTitle, $result->title);
        $this->assertEquals($openingCrawl, $result->openingCrawl);
        $this->assertEquals([1, 2, 3], $result->characterIds);

        Http::assertSent(function (Request $request) use ($filmId) {
            return $request->url() === "https://www.swapi.tech/api/films/{$filmId}"
                && $request->method() === 'GET';
        });
    }

    public function test_get_by_id_throws_exception_on_unsuccessful_response(): void
    {
        Http::fake([
            'swapi.tech/api/films*' => Http::response(
                [],
                Response::HTTP_INTERNAL_SERVER_ERROR
            ),
        ]);

        $this->expectException(StarWarsApiException::class);
        $this->expectExceptionMessage('Star Wars API request failed with status code: 500');

        (new FilmsRepository)->getById('3');
    }

    public function test_get_by_id_wraps_non_sw_api_related_errors(): void
    {
        Http::fake([
            'swapi.tech/api/films*' => Http::response(function () {
                throw new RuntimeException('Something went wrong');
            }),
        ]);

        $this->expectException(StarWarsApiException::class);
        $this->expectExceptionMessage('Error: Something went wrong');

        (new FilmsRepository)->getById('3');
    }
}
