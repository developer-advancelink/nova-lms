@extends('layouts.app')

@section('title', 'Edit account')
@section('page-title', 'Accounts')

@section('content')
@php
    $label = 'block text-xs font-medium text-slate-600 mb-1.5';
    $input = 'w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all';
    $isSelf = $user->is(Auth::user());
@endphp

<div class="space-y-6 max-w-2xl">

    @include('pages.admin._flash')

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-white">{{ $user->name }}</h2>
            <p class="text-xs text-slate-300 mt-0.5">{{ $user->email }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}"
           class="rounded-lg border border-white/30 text-white text-sm font-medium px-4 py-2 hover:bg-white/10 transition-colors">
            Back to accounts
        </a>
    </div>

    {{-- DETAILS --}}
    <form method="POST" action="{{ route('admin.users.update', $user) }}"
          x-data="{ role: '{{ old('role', $user->role) }}' }"
          class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
        @csrf
        @method('PATCH')

        <div class="p-6 space-y-5">

            <div>
                <label for="name" class="{{ $label }}">Full name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="{{ $input }}">
            </div>

            <div>
                <label for="email" class="{{ $label }}">Email address</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="{{ $input }}">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role" class="{{ $label }}">Role</label>
                    <select id="role" name="role" x-model="role" required
                            @disabled($isSelf)
                            class="{{ $input }} {{ $isSelf ? 'cursor-not-allowed text-slate-500' : '' }}">
                        <option value="student">Student</option>
                        <option value="lecturer">Lecturer</option>
                        <option value="admin">Administrator</option>
                    </select>
                    @if($isSelf)
                        {{-- Disabled inputs are not submitted, and the request
                             rejects a self role change anyway. --}}
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <p class="text-xs text-slate-400 mt-1.5">You cannot change your own role.</p>
                    @endif
                </div>

                <div x-show="role !== 'admin'" x-cloak>
                    <label for="department" class="{{ $label }}">Department</label>
                    <select id="department" name="department" class="{{ $input }}">
                        <option value="">Select a department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->value }}" @selected(old('department', $user->department) === $department->value)>
                                {{ $department->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($user->isLecturer() && $user->taughtCourses()->exists())
                <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                    This lecturer owns {{ $user->taughtCourses()->count() }} module(s). Moving them to another
                    department will hide those modules from them until the modules move too.
                </p>
            @endif

            <div>
                <label for="id_number" class="{{ $label }}">Staff / Student ID</label>
                <input type="text" id="id_number" name="id_number" value="{{ old('id_number', $user->id_number) }}" class="{{ $input }}">
            </div>

            <div>
                <label for="bio" class="{{ $label }}">Bio</label>
                <input type="text" id="bio" name="bio" value="{{ old('bio', $user->bio) }}" class="{{ $input }}">
            </div>

        </div>

        <div class="px-6 py-4 border-t border-[#E8EEF4] flex items-center justify-end">
            <button type="submit"
                    class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2 hover:bg-[#24507A] transition-colors">
                Save changes
            </button>
        </div>
    </form>

    {{-- PASSWORD RESET --}}
    <form method="POST" action="{{ route('admin.users.password', $user) }}"
          class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
        @csrf

        <div class="px-6 py-4 border-b border-[#E8EEF4]">
            <h3 class="font-semibold text-slate-900">Reset password</h3>
            <p class="text-xs text-slate-500 mt-0.5">
                NOVA does not send email, so set a new password here and pass it on directly.
            </p>
        </div>

        <div class="p-6 space-y-5">
            <div>
                <label for="reset_password" class="{{ $label }}">New password</label>
                <input type="password" id="reset_password" name="password" required class="{{ $input }}">
            </div>
            <div>
                <label for="reset_password_confirmation" class="{{ $label }}">Confirm new password</label>
                <input type="password" id="reset_password_confirmation" name="password_confirmation" required class="{{ $input }}">
            </div>
        </div>

        <div class="px-6 py-4 border-t border-[#E8EEF4] flex items-center justify-end">
            <button type="submit"
                    class="rounded-lg border border-[#C8D3E0] text-slate-600 text-sm font-medium px-4 py-2 hover:bg-[#E8EEF4] transition-colors">
                Reset password
            </button>
        </div>
    </form>

</div>
@endsection
