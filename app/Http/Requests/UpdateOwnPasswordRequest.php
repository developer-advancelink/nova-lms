<?php

namespace App\Http\Requests;

use App\Support\PasswordPolicy;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Changing your own password. The policy itself lives in App\Support\
 * PasswordPolicy so that admin-set initial passwords obey the same rules.
 */
class UpdateOwnPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // The framework rule replaces the hand-rolled Hash::check that the
            // closures did after validating; the message below keeps the
            // original wording.
            'current_password' => ['required', 'current_password'],

            // Self-service change, so the "must not contain your name" check
            // applies to the signed-in user.
            'password' => PasswordPolicy::rules($this->user()),
        ];
    }

    public function messages(): array
    {
        return PasswordPolicy::messages() + [
            'current_password.current_password' => 'The current password is incorrect.',
        ];
    }
}
