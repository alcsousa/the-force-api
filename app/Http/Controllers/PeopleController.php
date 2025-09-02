<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetPeopleByNameRequest;
use App\Http\Resources\PeopleDetailResource;
use App\Http\Resources\PeopleSearchResource;
use App\Services\PeopleServiceContract;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PeopleController extends Controller
{
    public function __construct(
        private readonly PeopleServiceContract $peopleService
    ) {}

    public function search(GetPeopleByNameRequest $request): AnonymousResourceCollection
    {
        $data = $this->peopleService->searchPeopleByName(
            $request->input('name')
        );

        return PeopleSearchResource::collection($data);
    }

    public function details(string $id): PeopleDetailResource
    {
        $data = $this->peopleService->getById($id);

        return PeopleDetailResource::make($data);
    }
}
