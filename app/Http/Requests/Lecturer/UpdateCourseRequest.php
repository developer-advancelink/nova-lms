<?php

namespace App\Http\Requests\Lecturer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * What a lecturer may change about their own module.
 *
 * Deliberately narrower than the admin's UpdateCourseRequest: `code`,
 * `department` and `lecturer_id` are not here, because reassigning a module or
 * moving it between departments is an admin action. The lecturer courses page
 * already says as much ("Course code and enrolled students can only be
 * modified by an administrator").
 */
class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // The route carries ->can('update', 'course'), and the {course}
        // binding has already scoped it to this lecturer's own modules.
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }
}
