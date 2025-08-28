<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetPeopleByNameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }
}
