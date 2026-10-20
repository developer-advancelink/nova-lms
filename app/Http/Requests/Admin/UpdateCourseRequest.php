<?php

namespace App\Http\Requests\Admin;

use App\Enums\Department;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $course = $this->route('course');

        return [
            'code'        => ['required', 'string', 'max:20', Rule::unique('courses', 'code')->ignore($course->id)],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'department'  => ['required', Rule::in(Department::values())],

            // Same cross-department guard as on create. Changing a module's
            // department therefore also requires moving it to a lecturer in
            // the new department, which is the correct constraint.
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $course = $this->route('course');

            // Moving a module between departments would strand every enrolled
            // student on the wrong side of the boundary, so require the roster
            // to be emptied first rather than silently orphaning it.
            if ($this->input('department') !== $course->department && $course->enrollments()->exists()) {
                $validator->errors()->add(
                    'department',
                    'Remove the enrolled students before moving this module to another department.'
                );
            }
        });
    }
}
