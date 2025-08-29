<?php

namespace Tests\Unit\Services;

use App\DTOs\FilmDetail;
use App\DTOs\FilmSearchResult;
use App\DTOs\FullFilmDetail;
use App\DTOs\PeopleDetail;
use App\Exceptions\Service\SearchFailedException;
use App\Repositories\FilmsRepositoryContract;
use App\Repositories\PeopleRepositoryContract;
use App\Services\FilmsService;
use Mockery;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

class FilmsServiceTest extends TestCase
{
    public function test_search_films_by_title_returns_results(): void
    {
        $peopleRepo = Mockery::mock(PeopleRepositoryContract::class);
        $filmsRepo = Mockery::mock(FilmsRepositoryContract::class);
        $filmsRepo->shouldReceive('searchByTitle')
            ->once()
            ->with('jedi')
            ->andReturn([
                new FilmSearchResult(id: 3, title: 'Return of the Jedi'),
                new FilmSearchResult(id: 300, title: 'Jedi Path'),
            ]);

        $results = new FilmsService($filmsRepo, $peopleRepo)->searchFilmByTitle('jedi');

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(FilmSearchResult::class, $results);
        $this->assertSame(3, $results[0]->id);
        $this->assertSame('Return of the Jedi', $results[0]->title);
        $this->assertSame(300, $results[1]->id);
        $this->assertSame('Jedi Path', $results[1]->title);
    }

    public function test_search_films_by_title_wraps_exception(): void
    {
        $keyword = 'jedi';
        $peopleRepo = Mockery::mock(PeopleRepositoryContract::class);
        $filmsRepo = Mockery::mock(FilmsRepositoryContract::class);
        $filmsRepo->shouldReceive('searchByTitle')
            ->once()
            ->with($keyword)
            ->andThrow(new RuntimeException('fake exception'));

        $service = new FilmsService($filmsRepo, $peopleRepo);

        try {
            $service->searchFilmByTitle($keyword);
            $this->fail('Expected exception not thrown');
        } catch (Throwable $t) {
            $this->assertInstanceOf(SearchFailedException::class, $t);
            $this->assertStringContainsString(
                "Unable to complete search at this time for keyword {$keyword}",
                $t->getMessage()
            );
            $this->assertInstanceOf(RuntimeException::class, $t->getPrevious());
        }
    }

    public function test_get_by_id_returns_full_film_detail(): void
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

        $filmRepo = Mockery::mock(FilmsRepositoryContract::class);
        $filmRepo->shouldReceive('getById')
            ->once()
            ->with('1')
            ->andReturn($filmDetail);

        $peopleRepo = Mockery::mock(PeopleRepositoryContract::class);
        $peopleRepo->shouldReceive('getDetails')
            ->twice()
            ->withArgs(function ($id) {
                return in_array($id, [1, 2], true);
            })
            ->andReturns($person1, $person2);

        $result = new FilmsService($filmRepo, $peopleRepo)->getById('1');

        $this->assertInstanceOf(FullFilmDetail::class, $result);
        $this->assertSame(1, $result->film->id);
        $this->assertEquals([$person1, $person2], $result->characters);
    }
}
