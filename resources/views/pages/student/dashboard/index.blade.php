@extends('layouts.app')

@section('title', 'Dashboard — NOVA')
@section('page-title', 'Dashboard')

@section('content')

{{-- Course accent palette — cycled by course id so each card reads distinctly --}}
@php
    $accents = [
        ['bg' => 'bg-indigo-50', 'ring' => 'ring-indigo-100', 'text' => 'text-indigo-600'],
        ['bg' => 'bg-amber-50',  'ring' => 'ring-amber-100',  'text' => 'text-amber-600'],
        ['bg' => 'bg-emerald-50','ring' => 'ring-emerald-100','text' => 'text-emerald-600'],
        ['bg' => 'bg-rose-50',   'ring' => 'ring-rose-100',   'text' => 'text-rose-600'],
    ];
@endphp

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- LEFT SECTION --}}
    <div class="xl:col-span-2 space-y-6">

        {{-- WELCOME --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h2 class="text-2xl font-semibold text-slate-900 mb-1">
                Welcome back, {{ Auth::user()->name }}
            </h2>
            <p class="text-sm text-slate-500">
                You're enrolled in {{ $courses->count() }} course{{ $courses->count() === 1 ? '' : 's' }} this semester.
            </p>
        </div>

        {{-- COURSE CARDS --}}
        <div>
            <h3 class="text-sm font-medium text-slate-200 uppercase tracking-wide mb-3">Your courses</h3>

            <div class="grid md:grid-cols-2 gap-4">

                @forelse($courses as $course)
                    @php $accent = $accents[$course->id % count($accents)]; @endphp

                    <a href="/courses/{{ $course->id }}"
                       class="group rounded-2xl bg-white shadow-sm ring-1 ring-slate-200 overflow-hidden
                              hover:shadow-md hover:ring-slate-300 transition-all">

                        <div class="h-28 {{ $accent['bg'] }} ring-1 {{ $accent['ring'] }} flex items-center justify-center">
                            <span class="text-3xl font-semibold {{ $accent['text'] }} tracking-tight">
                                {{ strtoupper(substr($course->code, 0, 2)) }}
                            </span>
                        </div>

                        <div class="p-4">
                            <p class="text-xs font-medium text-slate-400 mb-1">{{ $course->code }}</p>
                            <h3 class="text-base font-semibold text-slate-900 group-hover:text-indigo-600 transition-colors">
                                {{ $course->name }}
                            </h3>
                            <p class="text-xs text-slate-400 mt-2">
                                {{ $course->lecturer->name }}
                            </p>
                        </div>

                    </a>

                @empty

                    <div class="rounded-2xl bg-white p-8 shadow-sm ring-1 ring-slate-200 col-span-2 text-center">
                        <p class="text-sm text-slate-500">You're not enrolled in any courses yet.</p>
                        <p class="text-xs text-slate-400 mt-1">Courses you join will show up here.</p>
                    </div>

                @endforelse

            </div>
        </div>

    </div>

    {{-- RIGHT SECTION --}}
    <div class="space-y-6">

        {{-- TO DO --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">

            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-slate-900">To do</h3>
                @if($pendingAssignments->count() > 0)
                    <span class="text-xs font-medium text-indigo-700 bg-indigo-50 ring-1 ring-indigo-200 rounded-full px-2.5 py-0.5">
                        {{ $pendingAssignments->count() }} pending
                    </span>
                @endif
            </div>

            <ul class="space-y-3 text-sm">

                @forelse($pendingAssignments as $assignment)

                    @php
                        $isOverdue = $assignment->due_date->isPast() && !$assignment->due_date->isToday();
                        $isToday = $assignment->due_date->isToday();
                    @endphp

                    <li class="flex justify-between items-start {{ !$loop->last ? 'border-b border-slate-100 pb-3' : '' }}">

                        <div>
                            <p class="font-medium text-slate-800">{{ $assignment->title }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $assignment->course->code }}</p>
                        </div>

                        <span class="text-xs font-medium flex-shrink-0 ml-3 rounded-full px-2.5 py-0.5
                            {{ $isOverdue ? 'text-rose-700 bg-rose-50 ring-1 ring-rose-200' : '' }}
                            {{ $isToday ? 'text-amber-700 bg-amber-50 ring-1 ring-amber-200' : '' }}
                            {{ !$isOverdue && !$isToday ? 'text-slate-500 bg-slate-50 ring-1 ring-slate-200' : '' }}">
                            @if($isToday)
                                Due today
                            @elseif($assignment->due_date->isTomorrow())
                                Tomorrow
                            @elseif($isOverdue)
                                Overdue
                            @else
                                {{ $assignment->due_date->format('d M') }}
                            @endif
                        </span>

                    </li>

                @empty

                    <li class="text-sm text-slate-400">Nothing pending — you're all caught up.</li>

                @endforelse

            </ul>

        </div>

        {{-- COMING UP --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-semibold text-slate-900">Coming up</h3>
                <a href="/calendar" class="text-xs font-medium text-indigo-600 hover:text-indigo-700">
                    View calendar
                </a>
            </div>

            <ul class="space-y-3 text-sm">

                @forelse($schedules as $schedule)

                    <li class="{{ !$loop->last ? 'border-b border-slate-100 pb-3' : '' }}">
                        <p class="font-medium text-slate-800">{{ $schedule->title }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $schedule->day }}, {{ \Carbon\Carbon::parse($schedule->time)->format('g:i A') }}
                        </p>
                    </li>

                @empty

                    <li class="text-sm text-slate-400">No upcoming classes.</li>

                @endforelse

            </ul>
        </div>

        {{-- RECENT FEEDBACK --}}
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <h3 class="text-base font-semibold text-slate-900 mb-4">Recent feedback</h3>

            <ul class="space-y-3 text-sm">

                @forelse($recentFeedback as $submission)

                    <li class="{{ !$loop->last ? 'border-b border-slate-100 pb-3' : '' }}">
                        <div class="flex justify-between items-start gap-2">
                            <p class="font-medium text-slate-800">{{ $submission->assignment->title }}</p>
                            <span class="text-xs font-medium text-emerald-700 bg-emerald-50 ring-1 ring-emerald-200 rounded-full px-2.5 py-0.5 flex-shrink-0">
                                {{ $submission->grade }}
                            </span>
                        </div>
                        <p class="text-slate-500 text-xs mt-1">{{ $submission->feedback }}</p>
                    </li>

                @empty

                    <li class="text-sm text-slate-400">No feedback yet.</li>

                @endforelse

            </ul>
        </div>

        {{-- VIEW GRADES --}}
        <a href="/courses"
           class="block text-center rounded-xl bg-indigo-600 text-white py-3 text-sm font-medium
                  hover:bg-indigo-700 transition-colors">
            View grades
        </a>

    </div>

</div>

@endsection