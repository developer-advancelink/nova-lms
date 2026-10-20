<?php

namespace App\Http\Requests\Admin;

use App\Enums\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'        => ['required', 'string', 'max:20', 'unique:courses,code'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'department'  => ['required', Rule::in(Department::values())],

            // The lecturer must belong to the SAME department as the module.
            // Without this an admin could hand a PE module to an SPT lecturer,
            // and Course::scopeVisibleTo()'s department clause would then make
            // the module invisible to its own owner.
            'lecturer_id' => [
                'required',
                Rule::exists('users', 'id')->where(function ($q) {
                    $q->where('role', 'lecturer')
                      ->where('department', $this->input('department'));
                }),
            ],

            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'lecturer_id.exists' => 'That lecturer is not in the selected department.',
            'department.in'      => 'Department must be SPT or PE.',
        ];
    }
}
