@extends('layouts.app')

@section('title', 'New module')
@section('page-title', 'Modules')

@section('content')
@php
    $label = 'block text-xs font-medium text-slate-600 mb-1.5';
    $input = 'w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all';
@endphp

<div class="space-y-6 max-w-2xl">

    @include('pages.admin._flash')

    <div>
        <h2 class="text-lg font-semibold text-slate-900">Create a module</h2>
        <p class="text-xs text-slate-500 mt-0.5">
            Pick the department first — only lecturers from that department can own the module.
        </p>
    </div>

    {{-- The lecturer list is filtered client-side by the chosen department.
         The server enforces the same rule, so this is convenience, not the
         actual guard. --}}
    <form method="POST" action="{{ route('admin.courses.store') }}"
          x-data="{
              department: '{{ old('department', '') }}',
              lecturers: {{ Js::from($lecturers) }},
              get available() { return this.department ? (this.lecturers[this.department] ?? []) : []; }
          }"
          class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
        @csrf

        <div class="p-6 space-y-5">

            <div>
                <label for="department" class="{{ $label }}">Department</label>
                <select id="department" name="department" x-model="department" required class="{{ $input }}">
                    <option value="">Select a department</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->value }}">{{ $dept->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="code" class="{{ $label }}">Module code</label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" placeholder="e.g. PE103" required class="{{ $input }}">
                </div>
                <div>
                    <label for="lecturer_id" class="{{ $label }}">Lecturer</label>
                    <select id="lecturer_id" name="lecturer_id" required class="{{ $input }}" :disabled="!department">
                        <option value="">
                            <span x-text="department ? 'Select a lecturer' : 'Pick a department first'"></span>
                        </option>
                        <template x-for="lecturer in available" :key="lecturer.id">
                            <option :value="lecturer.id" x-text="lecturer.name"
                                    :selected="lecturer.id == {{ old('lecturer_id', 0) }}"></option>
                        </template>
                    </select>
                    <p x-show="department && available.length === 0" x-cloak class="text-xs text-rose-600 mt-1.5">
                        No lecturers in this department yet — create one first.
                    </p>
                </div>
            </div>

            <div>
                <label for="name" class="{{ $label }}">Module name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="{{ $input }}">
            </div>

            <div>
                <label for="description" class="{{ $label }}">Description <span class="text-slate-400">(optional)</span></label>
                <textarea id="description" name="description" rows="3" class="{{ $input }}">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="{{ $label }}">Start date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required class="{{ $input }}">
                </div>
                <div>
                    <label for="end_date" class="{{ $label }}">End date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required class="{{ $input }}">
                </div>
            </div>

        </div>

        <div class="px-6 py-4 border-t border-[#E8EEF4] flex items-center justify-end gap-3">
            <a href="{{ route('admin.courses.index') }}"
               class="rounded-lg border border-[#C8D3E0] text-slate-600 text-sm font-medium px-4 py-2 hover:bg-[#E8EEF4] transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2 hover:bg-[#24507A] transition-colors">
                Create module
            </button>
        </div>
    </form>

</div>
@endsection
