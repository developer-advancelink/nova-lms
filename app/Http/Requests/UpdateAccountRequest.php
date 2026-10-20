<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Editing your own profile — name, email and bio only.
 *
 * `role`, `department` and `id_number` are deliberately absent: they are set by
 * an admin on /admin/users/{user}/edit, and the shared profile view renders
 * them readonly for everyone. The closures this replaces had NO validation at
 * all, which meant you could set your email to one already in use.
 */
class UpdateAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'bio'   => ['nullable', 'string', 'max:255'],
        ];
    }
}
