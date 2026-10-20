<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Department;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Support\PasswordPolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = $this->filtered($request)
            // Grouped by role in the view, so the ordering has to put the
            // roles in blocks first and sort by name within each.
            ->orderByRaw(User::roleOrderSql())
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('pages.admin.users.index', [
            'users'       => $users,
            'departments' => Department::cases(),
            // Counts per role across the whole filtered set, not just this
            // page, so a group heading still reads correctly when a role spans
            // a page boundary.
            'roleCounts'  => $this->filtered($request)
                ->selectRaw('role, count(*) as total')
                ->groupBy('role')
                ->pluck('total', 'role'),
        ]);
    }

    /**
     * The search and filter clauses, shared by the listing and its counts so
     * the two cannot drift apart.
     */
    private function filtered(Request $request): Builder
    {
        return User::query()
            ->when($request->filled('department'), function ($q) use ($request) {
                $request->input('department') === 'none'
                    ? $q->whereNull('department')
                    : $q->where('department', $request->input('department'));
            })
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->input('role')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = $request->input('q');
                $q->where(fn ($inner) => $inner
                    ->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%"));
            });
    }

    public function create(): View
    {
        return view('pages.admin.users.create', ['departments' => Department::cases()]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = new User([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'role'       => $data['role'],
            'department' => $data['department'] ?? null,
            'id_number'  => $data['id_number'] ?? null,
            'password'   => Hash::make($data['password']),
        ]);

        // Set directly rather than through the constructor: email_verified_at
        // is not fillable, so mass assignment would silently drop it. Mail is
        // not configured (MAIL_MAILER=log), so an unverified account would sit
        // behind Breeze's verification prompt with no way to clear it.
        $user->email_verified_at = now();
        $user->save();

        // Flashed once so the admin can hand the credentials over in person.
        // Deliberately not stored or emailed anywhere.
        return redirect()
            ->route('admin.users.index')
            ->with('success', "Account created for {$user->name}.")
            ->with('new_account_email', $user->email);
    }

    public function edit(User $user): View
    {
        return view('pages.admin.users.edit', [
            'user'        => $user,
            'departments' => Department::cases(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        $user->update([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'role'       => $data['role'],
            'department' => $data['department'] ?? null,
            'id_number'  => $data['id_number'] ?? null,
            'bio'        => $data['bio'] ?? null,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$user->name} updated.");
    }

    /**
     * Set a new password for someone who has lost theirs. Mail is not
     * configured, so this is the recovery path rather than a reset link.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $request->validate(
            ['password' => PasswordPolicy::rules($user)],
            PasswordPolicy::messages()
        );

        $user->update(['password' => Hash::make($request->input('password'))]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Password reset for {$user->name}.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        // Deleting yourself would be unrecoverable if you are the last admin.
        if ($user->is($request->user())) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        // courses.lecturer_id is ON DELETE CASCADE, so removing a lecturer
        // would silently take their modules — and every enrollment, assignment
        // and submission under them — with it. Refuse and make the admin
        // reassign first.
        if ($user->isLecturer() && $user->taughtCourses()->exists()) {
            $count = $user->taughtCourses()->count();

            return back()->withErrors([
                'user' => "{$user->name} still owns {$count} module(s). Reassign them to another lecturer before deleting this account.",
            ]);
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$name} deleted.");
    }
}
