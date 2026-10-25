<?php

namespace App\Http\Requests;

use App\Models\ProgressPhoto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhotoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'taken_on' => ['required', 'date', 'before_or_equal:today'],
            'pose' => ['required', Rule::in(ProgressPhoto::POSES)],
            'notes' => ['nullable', 'string', 'max:255'],
            'photos' => ['required', 'array', 'max:6'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp,heic', 'max:12288'], // 12 MB
        ];
    }

    public function attributes(): array
    {
        return ['photos' => 'foto', 'photos.*' => 'foto', 'taken_on' => 'data', 'pose' => 'posa'];
    }
}
