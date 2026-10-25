<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Il form manda gli esercizi come rows[0][name], rows[0][sets], ... (una riga = un esercizio).
 * Le righe senza nome vengono ignorate.
 */
class WorkoutRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $rows = collect($this->input('rows', []))
            ->filter(fn ($row) => trim((string) ($row['name'] ?? '')) !== '')
            ->map(fn ($row) => [
                'name' => trim($row['name']),
                'sets' => $row['sets'] ?? 3,
                'reps_target' => trim((string) ($row['reps_target'] ?? '')) ?: null,
                'weight_target' => ($w = str_replace(',', '.', trim((string) ($row['weight_target'] ?? '')))) === '' ? null : $w,
                'rest_seconds' => $row['rest_seconds'] ?? 90,
                'notes' => trim((string) ($row['notes'] ?? '')) ?: null,
            ])
            ->values()
            ->all();

        $this->merge(['rows' => $rows]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'color' => ['required', Rule::in(array_keys(config('gymlog.colors')))],
            'notes' => ['nullable', 'string', 'max:255'],
            'rows' => ['required', 'array', 'min:1', 'max:30'],
            'rows.*.name' => ['required', 'string', 'max:100'],
            'rows.*.sets' => ['required', 'integer', 'between:1,10'],
            'rows.*.reps_target' => ['nullable', 'string', 'max:20'],
            'rows.*.weight_target' => ['nullable', 'numeric', 'between:0,999'],
            'rows.*.rest_seconds' => ['required', 'integer', Rule::in(config('gymlog.rest_options'))],
            'rows.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return ['rows' => 'esercizi', 'rows.*.name' => 'nome esercizio', 'rows.*.weight_target' => 'peso', 'rows.*.sets' => 'serie'];
    }

    /** Stesso esercizio due volte? Meglio aumentare le serie. */
    public function after(): array
    {
        return [function (Validator $validator) {
            $names = array_map('mb_strtolower', array_column($this->input('rows', []), 'name'));
            $duplicates = array_unique(array_diff_assoc($names, array_unique($names)));

            if ($duplicates !== []) {
                $validator->errors()->add('rows', 'Hai inserito due volte: '.implode(', ', $duplicates).'.');
            }
        }];
    }

    public function workoutData(): array
    {
        return $this->only('name', 'color', 'notes');
    }

    public function rows(): array
    {
        return collect($this->validated('rows'))->map(fn ($row) => [
            'name' => $row['name'],
            'sets' => (int) $row['sets'],
            'reps_target' => $row['reps_target'] ?? null,
            'weight_target' => isset($row['weight_target']) ? (float) $row['weight_target'] : null,
            'rest_seconds' => (int) $row['rest_seconds'],
            'notes' => $row['notes'] ?? null,
        ])->all();
    }
}
