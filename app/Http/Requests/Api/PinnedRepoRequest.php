<?php

namespace App\Http\Requests\Api;

class PinnedRepoRequest extends StatsRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'repo' => ['required', 'string', 'max:100'],
        ];
    }
}
