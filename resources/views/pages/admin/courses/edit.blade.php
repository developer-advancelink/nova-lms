@extends('layouts.app')

@section('title', 'Edit module')
@section('page-title', 'Modules')

@section('content')
@php
    $label = 'block text-xs font-medium text-slate-600 mb-1.5';
    $input = 'w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all';
    $enrolledCount = $course->enrollments()->count();
@endphp

<div class="space-y-6 max-w-2xl"
     x-data="{
         confirmOpen: false, confirmAction: '', confirmTitle: '',
         confirmBody: '', confirmCta: 'Delete',
         ask(action, title, body, cta = 'Delete') {
             this.confirmAction = action; this.confirmTitle = title;
             this.confirmBody = body; this.confirmCta = cta;
             this.confirmOpen = true;
         },
         closeConfirm() { this.confirmOpen = false; }
     }"
     @keydown.escape.window="closeConfirm()">

    @include('pages.admin._flash')

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-white">{{ $course->code }} — {{ $course->name }}</h2>
            <p class="text-xs text-slate-300 mt-0.5">{{ $course->departmentEnum()?->label() }}</p>
        </div>
        <a href="{{ route('admin.courses.show', $course) }}"
           class="rounded-lg border border-white/30 text-white text-sm font-medium px-4 py-2 hover:bg-white/10 transition-colors">
            Manage roster
        </a>
    </div>

    <form method="POST" action="{{ route('admin.courses.update', $course) }}"
          x-data="{
              department: '{{ old('department', $course->department) }}',
              lecturers: {{ Js::from($lecturers) }},
              get available() { return this.department ? (this.lecturers[this.department] ?? []) : []; }
          }"
          class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
        @csrf
        @method('PATCH')

        <div class="p-6 space-y-5">

            <div>
                <label for="department" class="{{ $label }}">Department</label>
                <select id="department" name="department" x-model="department" required
                        @disabled($enrolledCount > 0)
                        class="{{ $input }} {{ $enrolledCount > 0 ? 'cursor-not-allowed text-slate-500' : '' }}">
                    @foreach($departments as $dept)
                        <option value="{{ $dept->value }}">{{ $dept->label() }}</option>
                    @endforeach
                </select>
                @if($enrolledCount > 0)
                    {{-- Disabled selects are not submitted. --}}
                    <input type="hidden" name="department" value="{{ $course->department }}">
                    <p class="text-xs text-slate-400 mt-1.5">
                        {{ $enrolledCount }} student{{ $enrolledCount === 1 ? ' is' : 's are' }} enrolled. Remove them
                        before moving this module to another department.
                    </p>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="code" class="{{ $label }}">Module code</label>
                    <input type="text" id="code" name="code" value="{{ old('code', $course->code) }}" required class="{{ $input }}">
                </div>
                <div>
                    <label for="lecturer_id" class="{{ $label }}">Lecturer</label>
                    <select id="lecturer_id" name="lecturer_id" required class="{{ $input }}">
                        <template x-for="lecturer in available" :key="lecturer.id">
                            <option :value="lecturer.id" x-text="lecturer.name"
                                    :selected="lecturer.id == {{ old('lecturer_id', $course->lecturer_id) }}"></option>
                        </template>
                    </select>
                    <p x-show="available.length === 0" x-cloak class="text-xs text-rose-600 mt-1.5">
                        No lecturers in this department yet.
                    </p>
                </div>
            </div>

            <div>
                <label for="name" class="{{ $label }}">Module name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $course->name) }}" required class="{{ $input }}">
            </div>

            <div>
                <label for="description" class="{{ $label }}">Description</label>
                <textarea id="description" name="description" rows="3" class="{{ $input }}">{{ old('description', $course->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="{{ $label }}">Start date</label>
                    <input type="date" id="start_date" name="start_date"
                           value="{{ old('start_date', $course->start_date?->format('Y-m-d')) }}" required class="{{ $input }}">
                </div>
                <div>
                    <label for="end_date" class="{{ $label }}">End date</label>
                    <input type="date" id="end_date" name="end_date"
                           value="{{ old('end_date', $course->end_date?->format('Y-m-d')) }}" required class="{{ $input }}">
                </div>
            </div>

        </div>

        <div class="px-6 py-4 border-t border-[#E8EEF4] flex items-center justify-between">
            <a href="{{ route('admin.courses.index') }}"
               class="rounded-lg border border-[#C8D3E0] text-slate-600 text-sm font-medium px-4 py-2 hover:bg-[#E8EEF4] transition-colors">
                Back
            </a>
            <button type="submit"
                    class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2 hover:bg-[#24507A] transition-colors">
                Save changes
            </button>
        </div>
    </form>

    {{-- DELETE --}}
    <div class="rounded-2xl bg-white border border-rose-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-[#E8EEF4]">
            <h3 class="font-semibold text-slate-900">Delete this module</h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Permanently removes the module and everything attached to it — assignments, materials,
                announcements, schedules and submissions. This cannot be undone.
            </p>
        </div>
        <div class="px-6 py-4 flex justify-end">
            @if($enrolledCount > 0)
                <p class="text-xs text-slate-400">
                    Remove the {{ $enrolledCount }} enrolled student{{ $enrolledCount === 1 ? '' : 's' }} first.
                </p>
            @else
                <button type="button"
                        @click="ask(
                            @js(route('admin.courses.destroy', $course)),
                            @js('Delete '.$course->code.'?'),
                            @js('This permanently removes '.$course->code.' and everything attached to it — assignments, materials, announcements, schedules and submissions. This cannot be undone.'),
                            @js('Delete module')
                        )"
                        class="rounded-lg border border-rose-200 text-rose-700 text-sm font-medium px-4 py-2 hover:bg-rose-50 transition-colors">
                    Delete module
                </button>
            @endif
        </div>
    </div>

    @include('pages.admin._confirm-delete')

</div>
@endsection
