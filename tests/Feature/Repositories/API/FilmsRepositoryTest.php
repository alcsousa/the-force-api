<?php

namespace Tests\Feature\Repositories\API;

use App\DTOs\FilmDetail;
use App\Exceptions\StarWarsApiException;
use App\Repositories\API\FilmsRepository;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class FilmsRepositoryTest extends TestCase
{
    public function test_get_by_id_returns_film_detail_on_successful_response()
    {
        $filmId = '3';
        $expectedTitle = 'Return of the Jedi';

        Http::fake([
            "swapi.tech/api/films/{$filmId}" => Http::response([
                'result' => [
                    'properties' => [
                        'title' => $expectedTitle,
                    ],
                ],
            ], Response::HTTP_OK),
        ]);

        $result = (new FilmsRepository)->getById($filmId);

        $this->assertInstanceOf(FilmDetail::class, $result);
        $this->assertEquals($filmId, $result->id);
        $this->assertEquals($expectedTitle, $result->title);

        Http::assertSent(function (Request $request) use ($filmId) {
            return $request->url() === "https://www.swapi.tech/api/films/{$filmId}"
                && $request->method() === 'GET';
        });
    }

    public function test_get_by_id_throws_exception_on_unsuccessful_response()
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

    public function test_get_by_id_wraps_non_sw_api_related_errors()
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
