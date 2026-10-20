<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * The inbox's shared reads.
 *
 * Replaces the $inboxData and $allowedRecipientIds closures that used to be
 * assigned to variables in routes/web.php and `use`-injected into both route
 * groups.
 *
 * recipientsFor() is the single source of truth for who a user may message:
 * the inbox screen renders it, and ReplyToMessageRequest validates against it.
 * Keeping those two in one place is what stops the dropdown and the validation
 * drifting apart — the reply endpoints used to accept any receiver_id at all,
 * which was a cross-department messaging channel.
 */
class InboxService
{
    public function __construct(private CourseDirectory $courses)
    {
    }

    /**
     * Thread list plus the open thread, if one was requested.
     *
     * @return array{threads: Collection, activeThread: ?Message}
     */
    public function threadData(User $user, ?string $threadId = null): array
    {
        $threads = Message::threads()
            ->forUser($user->id)
            ->with(['sender', 'receiver', 'course', 'replies.sender'])
            ->orderByDesc('created_at')
            ->get();

        $activeThread = null;

        if ($threadId !== null) {
            // forUser() wraps its orWhere in a grouping closure; without that
            // the id filter would be escaped and this would match any message
            // the user has ever been involved in.
            $activeThread = Message::with(['sender', 'receiver', 'course', 'replies.sender'])
                ->forUser($user->id)
                ->find($threadId);

            if ($activeThread && $activeThread->receiver_id === $user->id && is_null($activeThread->read_at)) {
                $activeThread->update(['read_at' => now()]);
            }
        }

        return compact('threads', 'activeThread');
    }

    /**
     * Who this user may message: for a lecturer, the students enrolled in
     * their own modules; for a student, the lecturers teaching theirs.
     */
    public function recipientsFor(User $user): Collection
    {
        $courseIds = $this->courses->visibleIds($user);

        if ($user->isLecturer()) {
            return User::whereHas('enrollments', fn ($q) => $q->whereIn('course_id', $courseIds))
                ->where('role', 'student')
                ->orderBy('name')
                ->get();
        }

        return User::whereIn('id', $this->courses->visibleLecturerIds($user))
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array<int, int>
     */
    public function allowedRecipientIds(User $user): array
    {
        return $this->recipientsFor($user)->pluck('id')->all();
    }

    /**
     * A thread the user is part of, or fail. Used before accepting a reply, so
     * nobody can post into a conversation they are not in.
     */
    public function assertParticipatesIn(User $user, $messageId): void
    {
        Message::where('id', $messageId)
            ->forUser($user->id)
            ->firstOrFail();
    }
}
