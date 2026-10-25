<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function rules(): array
    {
        $user = $this->route('user'); // null quando crei

        return [
            'name' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:254', Rule::unique('users')->ignore($user)],
            'password' => [$user ? 'nullable' : 'required', Password::min(8)->uncompromised()],
            'role' => ['required', Rule::in(['admin', 'user'])],
        ];
    }
}
