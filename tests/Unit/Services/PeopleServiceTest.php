<?php

namespace Tests\Unit\Services;

use App\DTOs\FilmDetail;
use App\DTOs\FullPeopleDetail;
use App\DTOs\PeopleDetail;
use App\DTOs\PeopleSearchResult;
use App\Exceptions\Service\SearchFailedException;
use App\Repositories\FilmsRepositoryContract;
use App\Repositories\PeopleRepositoryContract;
use App\Services\PeopleService;
use Mockery;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

class PeopleServiceTest extends TestCase
{
    public function test_search_people_by_name_returns_results(): void
    {
        $filmsRepo = Mockery::mock(FilmsRepositoryContract::class);
        $peopleRepo = Mockery::mock(PeopleRepositoryContract::class);
        $peopleRepo->shouldReceive('searchPeopleByName')
            ->once()
            ->with('luke')
            ->andReturn([
                new PeopleSearchResult(id: '1', name: 'Luke Skywalker'),
                new PeopleSearchResult(id: '64', name: 'Luminara Unduli'),
            ]);

        $results = new PeopleService($peopleRepo, $filmsRepo)->searchPeopleByName('luke');

        $this->assertCount(2, $results);
        $this->assertContainsOnlyInstancesOf(PeopleSearchResult::class, $results);
        $this->assertSame('1', $results[0]->id);
        $this->assertSame('Luke Skywalker', $results[0]->name);
        $this->assertSame('64', $results[1]->id);
        $this->assertSame('Luminara Unduli', $results[1]->name);
    }

    public function test_search_people_by_name_wraps_exceptions(): void
    {
        $keyword = 'vader';
        $filmsRepo = Mockery::mock(FilmsRepositoryContract::class);
        $peopleRepo = Mockery::mock(PeopleRepositoryContract::class);
        $peopleRepo->shouldReceive('searchPeopleByName')
            ->once()
            ->with($keyword)
            ->andThrow(new RuntimeException('fake exception'));

        $service = new PeopleService($peopleRepo, $filmsRepo);

        try {
            $service->searchPeopleByName($keyword);
            $this->fail('Expected exception not thrown');
        } catch (Throwable $e) {
            $this->assertInstanceOf(SearchFailedException::class, $e);
            $this->assertStringContainsString(
                "Unable to complete search at this time for keyword {$keyword}",
                $e->getMessage()
            );
            $this->assertInstanceOf(RuntimeException::class, $e->getPrevious());
        }
    }

    public function test_get_by_id_returns_full_people_detail()
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
        $peopleRepo = Mockery::mock(PeopleRepositoryContract::class);
        $peopleRepo->shouldReceive('getDetails')
            ->once()
            ->with('64')
            ->andReturn($peopleDetail);

        $film1 = new FilmDetail('5', 'Attack of the Clones');
        $film2 = new FilmDetail('6', 'Revenge of the Sith');
        $filmsRepo = Mockery::mock(FilmsRepositoryContract::class);
        $filmsRepo->shouldReceive('getById')
            ->twice()
            ->withArgs(function ($id) {
                return in_array($id, [5, 6], true);
            })
            ->andReturns($film1, $film2);

        $result = new PeopleService($peopleRepo, $filmsRepo)->getById('64');

        $this->assertInstanceOf(FullPeopleDetail::class, $result);
        $this->assertSame($peopleDetail, $result->people);
        $this->assertEquals([$film1, $film2], $result->films);
    }
}
