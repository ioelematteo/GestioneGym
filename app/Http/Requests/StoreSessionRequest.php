<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSessionRequest extends FormRequest
{
    /** Prima di validare: "56,8" -> "56.8" */
    protected function prepareForValidation(): void
    {
        $this->merge(['body_weight' => str_replace(',', '.', (string) $this->input('body_weight'))]);
    }

    public function rules(): array
    {
        return [
            'date' => ['required', 'date', 'before_or_equal:today'],
            'duration_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'body_weight' => ['nullable', 'numeric', 'between:20,400'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'sets' => ['array'],
        ];
    }
}
