<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BodyEntryRequest extends FormRequest
{
    /** "56,8" -> "56.8" su tutti i numeri */
    protected function prepareForValidation(): void
    {
        foreach (['weight_kg', 'body_fat', 'waist_cm'] as $field) {
            if ($this->filled($field)) {
                $this->merge([$field => str_replace(',', '.', $this->input($field))]);
            }
        }

        $this->mergeIfMissing(['measured_on' => now()->toDateString()]);
    }

    public function rules(): array
    {
        return [
            'measured_on' => ['required', 'date', 'before_or_equal:today'],
            'weight_kg' => ['required', 'numeric', 'between:20,400'],
            'body_fat' => ['nullable', 'numeric', 'between:2,70'],
            'waist_cm' => ['nullable', 'numeric', 'between:30,250'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return ['weight_kg' => 'peso', 'body_fat' => '% grasso', 'waist_cm' => 'vita', 'measured_on' => 'data'];
    }
}
