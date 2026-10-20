@extends('layouts.app')

@section('title', $course->code)
@section('page-title', 'Modules')

@section('content')
@php
    $input = 'w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all';
    $dept = $course->departmentEnum();
@endphp

<div class="space-y-6"
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

    {{-- HEADER --}}
    <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
        <div class="h-2 {{ $course->department === 'SPT' ? 'bg-sky-200' : 'bg-emerald-200' }}"></div>
        <div class="p-6 flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-semibold text-slate-900">{{ $course->code }}</h2>
                    @if($dept)
                        <span class="text-xs font-medium rounded-full px-2.5 py-0.5 {{ $dept->pillClasses() }}">
                            {{ $dept->value }}
                        </span>
                    @endif
                </div>
                <p class="text-slate-700 mt-1">{{ $course->name }}</p>
                @if($course->description)
                    <p class="text-sm text-slate-500 mt-2 max-w-xl">{{ $course->description }}</p>
                @endif
                <p class="text-xs text-slate-400 mt-2">
                    {{ $course->lecturer?->name ?? 'No lecturer' }} ·
                    {{ $course->start_date?->format('d M Y') }} — {{ $course->end_date?->format('d M Y') }}
                </p>
            </div>
            <a href="{{ route('admin.courses.edit', $course) }}"
               class="rounded-lg border border-[#C8D3E0] text-slate-600 text-sm font-medium px-4 py-2 hover:bg-[#E8EEF4] transition-colors">
                Edit details
            </a>
        </div>
        <div class="flex items-center border-t border-[#E8EEF4] divide-x divide-[#E8EEF4]">
            <div class="flex-1 px-6 py-3">
                <p class="text-lg font-semibold text-slate-900">{{ $course->students->count() }}</p>
                <p class="text-[10px] uppercase tracking-widest text-slate-400 mt-0.5">Students</p>
            </div>
            <div class="flex-1 px-6 py-3">
                <p class="text-lg font-semibold text-slate-900">{{ $course->assignments->count() }}</p>
                <p class="text-[10px] uppercase tracking-widest text-slate-400 mt-0.5">Assignments</p>
            </div>
            <div class="flex-1 px-6 py-3">
                <p class="text-lg font-semibold text-slate-900">{{ $course->materials->count() }}</p>
                <p class="text-[10px] uppercase tracking-widest text-slate-400 mt-0.5">Materials</p>
            </div>
        </div>
    </div>

    {{-- ENROL A STUDENT --}}
    <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
        <div class="px-6 py-4 border-b border-[#E8EEF4]">
            <h3 class="font-semibold text-slate-900">Enrol a student</h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Only {{ $dept?->value ?? 'this department' }} students are listed — a student cannot be
                enrolled in another department's module.
            </p>
        </div>
        <div class="p-6">
            @if($enrollable->isEmpty())
                <p class="text-sm text-slate-400">
                    Every {{ $dept?->value }} student is already enrolled in this module.
                </p>
            @else
                <form method="POST" action="{{ route('admin.courses.students.store', $course) }}"
                      class="flex items-end gap-3">
                    @csrf
                    <div class="flex-1">
                        <label for="user_id" class="block text-xs font-medium text-slate-600 mb-1.5">Student</label>
                        <select id="user_id" name="user_id" required class="{{ $input }}">
                            @foreach($enrollable as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} — {{ $student->email }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit"
                            class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2.5 hover:bg-[#24507A] transition-colors">
                        Enrol
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- ROSTER --}}
    <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-x-auto">
        <div class="px-6 py-4 border-b border-[#E8EEF4]">
            <h3 class="font-semibold text-slate-900">Enrolled students</h3>
        </div>

        <div class="grid grid-cols-12 min-w-[620px] px-6 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest bg-[#E8EEF4] border-b border-[#C8D3E0]">
            <div class="col-span-4">Name</div>
            <div class="col-span-4">Email</div>
            <div class="col-span-2">Department</div>
            <div class="col-span-2 text-right">Actions</div>
        </div>

        <div class="divide-y divide-[#E8EEF4]">
            @forelse($course->students as $student)
                <div class="grid grid-cols-12 min-w-[620px] px-6 py-4 items-center text-sm hover:bg-[#E8EEF4] transition-colors">
                    <div class="col-span-4 font-medium text-slate-800">{{ $student->name }}</div>
                    <div class="col-span-4 text-slate-500 truncate">{{ $student->email }}</div>
                    <div class="col-span-2">
                        @php $studentDept = $student->departmentEnum(); @endphp
                        @if($studentDept && $student->department === $course->department)
                            <span class="text-xs font-medium rounded-full px-2.5 py-0.5 {{ $studentDept->pillClasses() }}">
                                {{ $studentDept->value }}
                            </span>
                        @else
                            {{-- Shouldn't happen: flagged so it can be corrected. --}}
                            <span class="text-xs font-medium rounded-full px-2.5 py-0.5 bg-rose-50 text-rose-700">
                                {{ $student->department ?? 'None' }}
                            </span>
                        @endif
                    </div>
                    <div class="col-span-2 flex justify-end">
                        <button type="button"
                                @click="ask(
                                    @js(route('admin.courses.students.destroy', [$course, $student])),
                                    @js('Remove '.$student->name.' from '.$course->code.'?'),
                                    @js('They lose access to this module immediately. Their existing submissions are kept, and you can enrol them again later.'),
                                    @js('Remove')
                                )"
                                class="text-xs font-medium text-slate-600 border border-[#C8D3E0] rounded-lg px-3 py-1.5 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 transition-colors">
                            Remove
                        </button>
                    </div>
                </div>
            @empty
                <p class="px-6 py-10 text-sm text-slate-400 text-center">No students enrolled yet.</p>
            @endforelse
        </div>
    </div>

    @include('pages.admin._confirm-delete')

</div>
@endsection
