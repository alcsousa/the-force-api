<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetFilmByTitleRequest;
use App\Http\Resources\FilmDetailResource;
use App\Http\Resources\FilmSearchResource;
use App\Services\FilmsServiceContract;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FilmsController extends Controller
{
    public function __construct(
        private readonly FilmsServiceContract $filmsService
    ) {}

    public function search(GetFilmByTitleRequest $request): AnonymousResourceCollection
    {
        $data = $this->filmsService->searchFilmByTitle(
            $request->input('title')
        );

        return FilmSearchResource::collection($data);
    }

    public function details(string $id): FilmDetailResource
    {
        $data = $this->filmsService->getById($id);

        return FilmDetailResource::make($data);
    }
}
