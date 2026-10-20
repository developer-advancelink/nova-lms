@extends('layouts.app')

@section('title', 'Dashboard — NOVA')
@section('page-title', 'Dashboard')

@section('content')

@php
    $accents = [
        ['bg' => 'bg-sky-50',    'ring' => 'ring-sky-100',    'text' => 'text-sky-700'],
        ['bg' => 'bg-amber-50',  'ring' => 'ring-amber-100',  'text' => 'text-amber-600'],
        ['bg' => 'bg-emerald-50','ring' => 'ring-emerald-100','text' => 'text-emerald-600'],
        ['bg' => 'bg-rose-50',   'ring' => 'ring-rose-100',   'text' => 'text-rose-600'],
    ];
@endphp

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- LEFT SECTION --}}
    <div class="xl:col-span-2 space-y-6">

        {{-- WELCOME --}}
        <div class="rounded-2xl bg-[#1B3A5C] p-6">
            <h2 class="text-2xl font-semibold text-white mb-1">
                Welcome back, {{ Auth::user()->name }}
            </h2>
            <p class="text-sm text-[#7C8AA0]">
                You have {{ $courses->count() }} active course{{ $courses->count() === 1 ? '' : 's' }} this semester.
            </p>
        </div>

        {{-- COURSE CARDS --}}
        <div>
            <h3 class="text-xs font-semibold text-slate-200 uppercase tracking-wide mb-3">Your courses</h3>

            <div class="grid md:grid-cols-2 gap-4">

                @forelse($courses as $course)
                    @php $accent = $accents[$course->id % count($accents)]; @endphp

                    <a href="/lecturer/courses/{{ $course->id }}"
                       class="group rounded-2xl bg-white shadow-sm overflow-hidden hover:shadow-md transition-all">

                        <div class="h-28 {{ $accent['bg'] }} flex items-center justify-center">
                            <span class="text-3xl font-semibold {{ $accent['text'] }} tracking-tight">
                                {{ strtoupper(substr($course->code, 0, 2)) }}
                            </span>
                        </div>

                        <div class="p-4">
                            <p class="text-xs font-medium text-slate-400 mb-1">{{ $course->code }}</p>
                            <h3 class="text-base font-semibold text-slate-900 group-hover:text-sky-700 transition-colors">
                                {{ $course->name }}
                            </h3>
                            <p class="text-xs text-slate-400 mt-2">
                                {{ $course->students->count() }} student{{ $course->students->count() === 1 ? '' : 's' }} enrolled
                            </p>
                        </div>

                    </a>

                @empty

                    <div class="rounded-2xl bg-white p-8 shadow-sm col-span-2 text-center">
                        <p class="text-sm text-slate-500">No courses assigned yet.</p>
                        <p class="text-xs text-slate-400 mt-1">New course assignments will appear here.</p>
                    </div>

                @endforelse

            </div>
        </div>

        {{-- PENDING SUBMISSIONS --}}
        <div class="rounded-2xl bg-white shadow-sm overflow-x-auto">

            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-900">Pending submissions</h3>
                @if($pendingSubmissions->count() > 0)
                    <span class="text-xs font-medium text-amber-700 bg-amber-50 ring-1 ring-amber-200 rounded-full px-2.5 py-0.5">
                        {{ $pendingSubmissions->count() }} to grade
                    </span>
                @endif
            </div>

            <div class="divide-y divide-slate-100">

                @forelse($pendingSubmissions as $submission)
                    <div class="grid grid-cols-12 min-w-[620px] px-6 py-3.5 items-center text-sm hover:bg-slate-50 transition-colors">
                        <div class="col-span-4 font-medium text-slate-800">{{ $submission->user->name }}</div>
                        <div class="col-span-4 text-slate-600">{{ $submission->assignment->title }}</div>
                        <div class="col-span-2 text-xs text-slate-400">{{ $submission->assignment->course->code }}</div>
                        <div class="col-span-2 text-right">
                            <a href="/lecturer/courses/{{ $submission->assignment->course_id }}?section=grades"
                               class="inline-block text-xs font-medium text-white bg-[#1B3A5C] rounded-lg px-3 py-1.5
                                      hover:bg-[#24507A] transition-colors">
                                Grade
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-sm text-slate-400 text-center">
                        Nothing waiting on you — all caught up.
                    </div>
                @endforelse

            </div>

        </div>

    </div>

    {{-- RIGHT SECTION --}}
    <div class="space-y-6">

        {{-- QUICK ACTIONS --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Quick actions</h3>
            <div class="space-y-2">
                <a href="/lecturer/courses"
                   class="flex items-center justify-center rounded-xl bg-[#1B3A5C] text-white py-2.5 text-sm font-medium
                          hover:bg-[#24507A] transition-colors">
                    New assignment
                </a>
                <a href="/lecturer/courses"
                   class="flex items-center justify-center rounded-xl bg-[#E8EEF4] text-slate-700 py-2.5 text-sm font-medium
                          hover:bg-slate-200 transition-colors">
                    Post announcement
                </a>
                <a href="/lecturer/inbox"
                   class="flex items-center justify-center rounded-xl text-slate-500 py-2.5 text-sm font-medium
                          hover:bg-[#E8EEF4] transition-colors">
                    Open inbox
                </a>
            </div>
        </div>

        {{-- COMING UP --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-semibold text-slate-900">Coming up</h3>
                <a href="/lecturer/calendar" class="text-xs font-medium text-sky-700 hover:text-sky-800">
                    View calendar
                </a>
            </div>
            <ul class="space-y-3 text-sm">
                @forelse($upcoming as $item)
                    <li class="{{ !$loop->last ? 'border-b border-slate-100 pb-3' : '' }}">
                        <p class="font-medium text-slate-800">{{ $item->title }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $item->subtitle }}</p>
                    </li>
                @empty
                    <li class="text-sm text-slate-400">Nothing coming up.</li>
                @endforelse
            </ul>
        </div>

        {{-- RECENT ACTIVITY --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Recent activity</h3>
            <ul class="space-y-3 text-sm">
                @forelse($recentActivity as $submission)
                    <li class="{{ !$loop->last ? 'border-b border-slate-100 pb-3' : '' }}">
                        <p class="text-slate-700">
                            <span class="font-medium text-slate-900">{{ $submission->user->name }}</span>
                            submitted {{ $submission->assignment->title }}
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $submission->assignment->course->code }} · {{ $submission->submitted_at?->diffForHumans() ?? $submission->created_at->diffForHumans() }}
                        </p>
                    </li>
                @empty
                    <li class="text-sm text-slate-400">No recent activity.</li>
                @endforelse
            </ul>
        </div>

    </div>

</div>

@endsection