@extends('layouts.app')

@section('title', 'New account')
@section('page-title', 'Accounts')

@section('content')
@php
    $label = 'block text-xs font-medium text-slate-600 mb-1.5';
    $input = 'w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all';
@endphp

<div class="space-y-6 max-w-2xl">

    @include('pages.admin._flash')

    <div>
        <h2 class="text-lg font-semibold text-slate-900">Create an account</h2>
        <p class="text-xs text-slate-500 mt-0.5">
            Set an initial password and pass it to the account holder — NOVA does not send email.
        </p>
    </div>

    {{-- x-data drives one thing only: hiding the department field for admins,
         who belong to no single department. --}}
    <form method="POST" action="{{ route('admin.users.store') }}"
          x-data="{ role: '{{ old('role', 'student') }}' }"
          class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
        @csrf

        <div class="p-6 space-y-5">

            <div>
                <label for="name" class="{{ $label }}">Full name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="{{ $input }}">
            </div>

            <div>
                <label for="email" class="{{ $label }}">Email address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="{{ $input }}">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role" class="{{ $label }}">Role</label>
                    <select id="role" name="role" x-model="role" required class="{{ $input }}">
                        <option value="student">Student</option>
                        <option value="lecturer">Lecturer</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>

                <div x-show="role !== 'admin'" x-cloak>
                    <label for="department" class="{{ $label }}">Department</label>
                    <select id="department" name="department" class="{{ $input }}">
                        <option value="">Select a department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->value }}" @selected(old('department') === $department->value)>
                                {{ $department->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <p x-show="role === 'admin'" x-cloak class="text-xs text-slate-500">
                Administrators oversee every department, so they are not assigned to one.
            </p>

            <div>
                <label for="id_number" class="{{ $label }}">Staff / Student ID <span class="text-slate-400">(optional)</span></label>
                <input type="text" id="id_number" name="id_number" value="{{ old('id_number') }}" class="{{ $input }}">
            </div>

            <div class="pt-2 border-t border-[#E8EEF4]">
                <label for="password" class="{{ $label }}">Initial password</label>
                <input type="password" id="password" name="password" required class="{{ $input }}">
                <p class="text-xs text-slate-400 mt-1.5">
                    At least 12 characters, with uppercase, lowercase, a number and a symbol.
                    It must not contain the account holder's name.
                </p>
            </div>

            <div>
                <label for="password_confirmation" class="{{ $label }}">Confirm password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="{{ $input }}">
            </div>

        </div>

        <div class="px-6 py-4 border-t border-[#E8EEF4] flex items-center justify-end gap-3">
            <a href="{{ route('admin.users.index') }}"
               class="rounded-lg border border-[#C8D3E0] text-slate-600 text-sm font-medium px-4 py-2 hover:bg-[#E8EEF4] transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2 hover:bg-[#24507A] transition-colors">
                Create account
            </button>
        </div>
    </form>

</div>
@endsection
