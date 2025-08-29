<?php

namespace Tests\Unit\Services;

use App\DTOs\FilmSearchResult;
use App\Exceptions\Service\SearchFailedException;
use App\Repositories\FilmsRepositoryContract;
use App\Services\FilmsService;
use Mockery;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

class FilmsServiceTest extends TestCase
{
    public function test_search_films_by_title_returns_results(): void
    {
        $filmsRepo = Mockery::mock(FilmsRepositoryContract::class);
        $filmsRepo->shouldReceive('searchByTitle')
            ->once()
            ->with('jedi')
            ->andReturn([
                new FilmSearchResult(id: 3, title: 'Return of the Jedi'),
                new FilmSearchResult(id: 300, title: 'Jedi Path'),
            ]);

        $results = new FilmsService($filmsRepo)->searchFilmByTitle('jedi');

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
        $filmsRepo = Mockery::mock(FilmsRepositoryContract::class);
        $filmsRepo->shouldReceive('searchByTitle')
            ->once()
            ->with($keyword)
            ->andThrow(new RuntimeException('fake exception'));

        $service = new FilmsService($filmsRepo);

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
}
