<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAccountRequest;
use App\Http\Requests\UpdateOwnPasswordRequest;
use App\Services\CourseDirectory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Your own profile and password, for every role.
 *
 * This was three near-identical pairs of closures (student, lecturer, admin)
 * differing only in which view they returned. The view name is derivable from
 * the role, so one controller serves all three.
 */
class AccountController extends Controller
{
    public function __construct(private CourseDirectory $courses)
    {
    }

    public function edit(): View
    {
        return view($this->view('profile'), $this->courseBindings());
    }

    public function update(UpdateAccountRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * The password form now lives on the profile page, so this only exists to
     * keep the old /account/password URL working for bookmarks and the
     * browser's history.
     */
    public function editPassword(): RedirectResponse
    {
        return redirect(Auth::user()->accountPath());
    }

    public function updatePassword(UpdateOwnPasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->validated()['password']),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    private function view(string $page): string
    {
        return "pages.{$this->role()}.account.{$page}";
    }

    private function role(): string
    {
        return Auth::user()->role;
    }

    /**
     * The sidebar reads $courses for a lecturer and $enrolledCourses for a
     * student, so both are passed; an admin's sidebar shows neither.
     *
     * @return array<string, mixed>
     */
    private function courseBindings(): array
    {
        $user = Auth::user();
        $list = $user->isAdmin() ? collect() : $this->courses->visibleTo($user);

        return ['courses' => $list, 'enrolledCourses' => $list];
    }
}
