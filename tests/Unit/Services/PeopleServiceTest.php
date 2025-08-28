<?php

namespace Tests\Unit\Services;

use App\DTOs\PeopleSearchResult;
use App\Exceptions\Service\SearchFailedException;
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
        $repo = Mockery::mock(PeopleRepositoryContract::class);
        $repo->shouldReceive('searchPeopleByName')
            ->once()
            ->with('luke')
            ->andReturn([
                new PeopleSearchResult(id: '1', name: 'Luke Skywalker'),
                new PeopleSearchResult(id: '64', name: 'Luminara Unduli'),
            ]);

        $results = new PeopleService($repo)->searchPeopleByName('luke');

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
        $repo = Mockery::mock(PeopleRepositoryContract::class);
        $repo->shouldReceive('searchPeopleByName')
            ->once()
            ->with($keyword)
            ->andThrow(new RuntimeException('fake exception'));

        $service = new PeopleService($repo);

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
}
