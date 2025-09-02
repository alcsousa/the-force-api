<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetFilmByTitleRequest extends FormRequest
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
            'title' => 'required|string|max:50',
        ];
    }
}
