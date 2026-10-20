<?php

namespace App\Http\Requests\Admin;

use App\Enums\Department;
use App\Models\User;
use App\Support\PasswordPolicy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Reaching this request already means passing the 'admin' middleware.
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role'  => ['required', Rule::in(['student', 'lecturer', 'admin'])],

            // Nullable in the database because admins belong to no department,
            // but required for everyone else.
            'department' => [
                'nullable',
                'required_unless:role,admin',
                Rule::in(Department::values()),
            ],

            'id_number' => ['nullable', 'string', 'max:50'],

            // The "must not contain the holder's name" check needs the name
            // being submitted, not the admin's own.
            'password' => PasswordPolicy::rules(new User(['name' => $this->input('name')])),
        ];
    }

    public function messages(): array
    {
        return PasswordPolicy::messages() + [
            'department.required_unless' => 'A department is required for students and lecturers.',
            'department.in'              => 'Department must be SPT or PE.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // An admin oversees every department, so it must never carry one.
        if ($this->input('role') === 'admin') {
            $this->merge(['department' => null]);
        }
    }
}
