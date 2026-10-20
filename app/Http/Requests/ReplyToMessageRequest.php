<?php

namespace App\Http\Requests;

use App\Services\CourseDirectory;
use App\Services\InboxService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Shared by the student and lecturer reply endpoints, which were byte-identical
 * closures. Both the recipient and the tagged module are checked against what
 * the sender can actually see.
 */
class ReplyToMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        $inbox = app(InboxService::class);
        $courses = app(CourseDirectory::class);

        return [
            'receiver_id' => ['required', Rule::in($inbox->allowedRecipientIds($user))],
            'course_id'   => ['nullable', Rule::in($courses->visibleIds($user)->all())],
            'parent_id'   => ['nullable', 'integer'],
            'subject'     => ['nullable', 'string', 'max:255'],
            'body'        => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'receiver_id.in' => 'You cannot message that person.',
            'course_id.in'   => 'That module is not one of yours.',
        ];
    }
}
