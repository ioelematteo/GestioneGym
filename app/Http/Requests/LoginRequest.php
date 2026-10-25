<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            // HONEYPOT: campo nascosto. Un umano lo lascia vuoto, un bot lo riempie -> bloccato
            'website' => ['prohibited'],
        ];
    }

    public function messages(): array
    {
        return ['website.prohibited' => 'Richiesta non valida.'];
    }
}
