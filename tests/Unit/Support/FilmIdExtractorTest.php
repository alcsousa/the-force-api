<?php

namespace Tests\Unit\Support;

use App\Support\PathIdExtractor;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

final class FilmIdExtractorTest extends TestCase
{
    #[DataProvider('provideValidFilmUrls')]
    public function test_extract_from_url_returns_valid_id(string $url, int $expectedId): void
    {
        Config::set('sw-api.base_url', 'https://www.swapi.tech/api/');

        $this->assertSame(
            $expectedId,
            PathIdExtractor::extractFromUrl($url, 'films')
        );
    }

    /**
     * @return array<string, array{0: string, 1: int}>
     */
    public static function provideValidFilmUrls(): array
    {
        return [
            [
                'https://www.swapi.tech/api/films/1',
                1,
            ],
            [
                'https://www.swapi.tech/api/films/5',
                5,
            ],
            [
                'https://www.swapi.tech/api/films/1000',
                1000,
            ],
            [
                "   https://www.swapi.tech/api/films/1000\n",
                1000,
            ],
        ];
    }

    #[DataProvider('provideInvalidFilmUrls')]
    public function test_extract_from_url_returns_null_for_invalid_urls(string $url): void
    {
        Config::set('sw-api.base_url', 'https://www.swapi.tech/api/');

        $this->assertNull(PathIdExtractor::extractFromUrl($url, 'films'));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function provideInvalidFilmUrls(): array
    {
        return [
            [
                'https://www.swapi.tech/api/films/',
            ],
            [
                'https://www.swapi.tech/api/films/0',
            ],
            [
                'https://www.swapi.tech/api/films/-1',
            ],
            [
                'https://www.swapi.tech/api/films/1/2',
            ],
            [
                '',
            ],
            [
                "   \t\n",
            ],
            [
                'http://www.swapi.tech/api/films/5',
            ],
            [
                'https://api.swapi.tech/api/films/5',
            ],
            [
                'https://example.com/api/films/5',
            ],
            [
                'https://www.swapi.tech/api/films/5?foo=bar',
            ],
            [
                'https://www.swapi.tech/api/films/5#top',
            ],
            [
                'https://www.swapi.tech/api/films/5/extra',
            ],
            [
                'https://www.swapi.tech/api/films/abc',
            ],
            [
                'https://www.swapi.tech/api/people/5',
            ],
            [
                'https://www.swapi.tech/api/films/',
            ],
        ];
    }

    public function test_accepts_non_api_root_when_config_has_no_path(): void
    {
        Config::set('sw-api.base_url', 'https://swapi.tech/');

        $this->assertSame(7, PathIdExtractor::extractFromUrl('https://swapi.tech/films/7', 'films'));
        $this->assertNull(PathIdExtractor::extractFromUrl('https://swapi.tech/api/films/7', 'films'));
    }
}
