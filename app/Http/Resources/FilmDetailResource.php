<?php

namespace App\Http\Resources;

use App\DTOs\FullFilmDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read FullFilmDetail $resource
 */
class FilmDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->film->id,
            'title' => $this->resource->film->title,
            'opening_crawl' => $this->resource->film->openingCrawl,
            'characters' => PeopleResource::collection($this->resource->characters),
        ];
    }
}
