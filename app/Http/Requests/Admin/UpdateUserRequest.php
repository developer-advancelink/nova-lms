<?php

namespace App\Http\Requests\Admin;

use App\Enums\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'  => ['required', Rule::in(['student', 'lecturer', 'admin'])],
            'department' => [
                'nullable',
                'required_unless:role,admin',
                Rule::in(Department::values()),
            ],
            'id_number' => ['nullable', 'string', 'max:50'],
            'bio'       => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'department.required_unless' => 'A department is required for students and lecturers.',
            'department.in'              => 'Department must be SPT or PE.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('role') === 'admin') {
            $this->merge(['department' => null]);
        }
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->route('user');

            // An admin must not be able to demote or re-department themselves.
            // There is no artisan recovery path, so the last admin could
            // otherwise lock every admin out of these screens permanently.
            if ($user->is($this->user()) && $this->input('role') !== $user->role) {
                $validator->errors()->add('role', 'You cannot change your own role.');
            }
        });
    }
}
