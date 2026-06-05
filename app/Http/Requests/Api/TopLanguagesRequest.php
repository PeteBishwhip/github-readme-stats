<?php

namespace App\Http\Requests\Api;

class TopLanguagesRequest extends StatsRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'limit' => ['sometimes', 'integer', 'min:1', 'max:10'],
        ];
    }
}
