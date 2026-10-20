<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReplyToMessageRequest;
use App\Models\Message;
use App\Services\InboxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Sending, editing and deleting messages — shared by the student and lecturer
 * inboxes, whose closures were byte-identical.
 *
 * Reading the inbox is per-role (different course lists and recipient
 * dropdowns), so that stays in Student\InboxController and
 * Lecturer\InboxController.
 */
class MessageController extends Controller
{
    public function __construct(private InboxService $inbox)
    {
    }

    public function reply(ReplyToMessageRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Replying into a thread requires being part of it.
        if (! empty($data['parent_id'])) {
            $this->inbox->assertParticipatesIn($request->user(), $data['parent_id']);
        }

        Message::create([
            'sender_id'   => $request->user()->id,
            'receiver_id' => $data['receiver_id'],
            'course_id'   => $data['course_id'] ?? null,
            'parent_id'   => $data['parent_id'] ?? null,
            'subject'     => $data['subject'] ?? null,
            'body'        => $data['body'],
        ]);

        return redirect()->back();
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate(['body' => ['required', 'string']]);

        $this->ownedBySender($id)->update(['body' => $request->input('body')]);

        return response()->json(['success' => true]);
    }

    public function destroy($id): JsonResponse
    {
        $this->ownedBySender($id)->delete();

        return response()->json(['success' => true]);
    }

    /**
     * You may only edit or delete what you sent.
     */
    private function ownedBySender($id): Message
    {
        return Message::where('id', $id)
            ->where('sender_id', Auth::id())
            ->firstOrFail();
    }
}
