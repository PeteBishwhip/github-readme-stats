<?php

namespace App\Http\Requests\Api;

class GistRequest extends StatsRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'id' => ['required', 'string', 'max:64'],
        ];
    }
}
