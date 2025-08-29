<?php

namespace App\Http\Resources;

use App\DTOs\FullPeopleDetail;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read FullPeopleDetail $resource
 */
class PeopleDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->people->id,
            'name' => $this->resource->people->name,
            'birth_year' => $this->resource->people->birthYear,
            'gender' => $this->resource->people->gender,
            'eye_color' => $this->resource->people->eyeColor,
            'hair_color' => $this->resource->people->hairColor,
            'height' => $this->resource->people->height,
            'mass' => $this->resource->people->mass,
            'films' => FilmResource::collection($this->resource->films),
        ];
    }
}
