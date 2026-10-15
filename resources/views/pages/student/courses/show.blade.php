@extends('layouts.app')

@section('title', $course->name)
@section('page-title', $course->name)

@section('content')

@php $section = request('section', 'home'); @endphp

<div class="flex gap-6">

    {{-- COURSE NAV --}}
    <div class="w-52 flex-shrink-0 space-y-3">

        {{-- COURSE IDENTITY CARD --}}
        <div class="rounded-2xl bg-[#1B3A5C] p-4 text-white">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-[#7C9EC0] mb-1">{{ $course->code }}</p>
            <p class="text-sm font-semibold leading-snug">{{ $course->name }}</p>
            <div class="mt-3 pt-3 border-t border-[#26527A]">
                <p class="text-[10px] uppercase tracking-wide text-[#7C9EC0] mb-0.5">Lecturer</p>
                <p class="text-xs font-medium text-white">{{ $course->lecturer->name }}</p>
            </div>
        </div>

        {{-- NAV LINKS --}}
        <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
            @php
                $navLinks = [
                    'home'          => ['label' => 'Home',          'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />'],
                    'modules'       => ['label' => 'Modules',       'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />'],
                    'assignments'   => ['label' => 'Assignments',   'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />'],
                    'announcements' => ['label' => 'Announcements', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />'],
                    'grades'        => ['label' => 'Grades',        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />'],
                    'people'        => ['label' => 'People',        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />'],
                ];
            @endphp

            @foreach($navLinks as $key => $nav)
                <a href="?section={{ $key }}"
                   class="flex items-center gap-3 px-4 py-3 text-sm transition-colors
                   {{ !$loop->last ? 'border-b border-[#E8EEF4]' : '' }}
                   {{ $section === $key
                       ? 'bg-[#E8EEF4] text-[#1B3A5C] font-semibold'
                       : 'text-slate-600 hover:bg-[#E8EEF4] hover:text-slate-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0 {{ $section === $key ? 'text-[#1B3A5C]' : 'text-slate-400' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        {!! $nav['icon'] !!}
                    </svg>
                    {{ $nav['label'] }}
                </a>
            @endforeach
        </div>

    </div>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 space-y-6">

        {{-- HOME --}}
        @if($section === 'home')

            {{-- RECENT ANNOUNCEMENTS --}}
            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E8EEF4] flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900">Announcements</h3>
                    <a href="?section=announcements" class="text-xs font-medium text-[#1B3A5C] hover:underline">View all</a>
                </div>
                <div class="p-6 space-y-3">
                    @forelse($course->announcements->take(2) as $announcement)
                        <div class="rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] p-4">
                            <p class="font-medium text-sm text-slate-900 mb-1">{{ $announcement->title }}</p>
                            <p class="text-sm text-slate-500">{{ $announcement->body }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">No announcements yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- UPCOMING ASSIGNMENTS --}}
            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E8EEF4] flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900">Assignments</h3>
                    <a href="?section=assignments" class="text-xs font-medium text-[#1B3A5C] hover:underline">View all</a>
                </div>
                <div class="divide-y divide-[#E8EEF4]">
                    @forelse($course->assignments->take(3) as $assignment)
                        <div class="px-6 py-3 flex items-center justify-between text-sm hover:bg-[#E8EEF4] transition-colors">
                            <p class="font-medium text-slate-800">{{ $assignment->title }}</p>
                            @if($assignment->due_date->isPast())
                                <span class="text-xs font-medium text-slate-400 bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2.5 py-0.5 flex-shrink-0 ml-4">Closed</span>
                            @elseif($assignment->due_date->isToday())
                                <span class="text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-full px-2.5 py-0.5 flex-shrink-0 ml-4">Due today</span>
                            @else
                                <span class="text-xs font-medium text-slate-600 bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2.5 py-0.5 flex-shrink-0 ml-4">{{ $assignment->due_date->format('d M') }}</span>
                            @endif
                        </div>
                    @empty
                        <div class="px-6 py-8 text-sm text-slate-400 text-center">No assignments yet.</div>
                    @endforelse
                </div>
            </div>

        {{-- MODULES --}}
        @elseif($section === 'modules')

            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">

                <div class="px-6 py-4 border-b border-[#E8EEF4]">
                    <h3 class="text-base font-semibold text-slate-900">Modules</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $course->materials->count() }} material{{ $course->materials->count() === 1 ? '' : 's' }} available</p>
                </div>

                <ul class="divide-y divide-[#E8EEF4]">
                    @forelse($course->materials as $material)
                        <li class="px-6 py-4 flex justify-between items-center text-sm hover:bg-[#E8EEF4] transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-[#1B3A5C]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-medium text-slate-800">
                                        @if($material->week) {{ $material->week }} — @endif{{ $material->title }}
                                    </span>
                                    <span class="ml-2 text-xs font-medium text-[#1B3A5C] bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2.5 py-0.5 capitalize">
                                        {{ $material->type }}
                                    </span>
                                </div>
                            </div>
                            @if($material->file_url)
                                <a href="{{ Storage::url($material->file_url) }}" target="_blank"
                                   class="text-xs font-medium text-white bg-[#1B3A5C] rounded-lg px-3 py-1.5 hover:bg-[#24507A] transition-colors flex-shrink-0">
                                    Download
                                </a>
                            @endif
                        </li>
                    @empty
                        <li class="px-6 py-10 text-sm text-slate-400 text-center">No modules uploaded yet.</li>
                    @endforelse
                </ul>

            </div>

        {{-- ASSIGNMENTS --}}
        @elseif($section === 'assignments')

            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">

                <div class="px-6 py-4 border-b border-[#E8EEF4]">
                    <h3 class="text-base font-semibold text-slate-900">Assignments</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $course->assignments->count() }} assignment{{ $course->assignments->count() === 1 ? '' : 's' }}</p>
                </div>

                <ul class="divide-y divide-[#E8EEF4]">
                    @forelse($course->assignments as $assignment)
                        <li class="px-6 py-4 text-sm hover:bg-[#E8EEF4] transition-colors">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-slate-900">{{ $assignment->title }}</p>
                                    @if($assignment->description)
                                        <p class="text-xs text-slate-500 mt-1">{{ $assignment->description }}</p>
                                    @endif
                                </div>
                                <div class="flex-shrink-0 ml-4">
                                    @if($assignment->due_date->isPast())
                                        <span class="text-xs font-medium text-slate-400 bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2.5 py-0.5">Closed</span>
                                    @elseif($assignment->due_date->isToday())
                                        <span class="text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-full px-2.5 py-0.5">Due today</span>
                                    @else
                                        <span class="text-xs font-medium text-slate-600 bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2.5 py-0.5">
                                            {{ $assignment->due_date->format('d M Y, g:i A') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-10 text-sm text-slate-400 text-center">No assignments posted yet.</li>
                    @endforelse
                </ul>

            </div>

        {{-- ANNOUNCEMENTS --}}
        @elseif($section === 'announcements')

            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">

                <div class="px-6 py-4 border-b border-[#E8EEF4]">
                    <h3 class="text-base font-semibold text-slate-900">Announcements</h3>
                </div>

                <ul class="divide-y divide-[#E8EEF4]">
                    @forelse($course->announcements as $announcement)
                        <li class="px-6 py-4 text-sm hover:bg-[#E8EEF4] transition-colors">
                            <div class="flex justify-between items-start mb-1">
                                <p class="font-semibold text-slate-900">{{ $announcement->title }}</p>
                                <span class="text-xs text-slate-400 flex-shrink-0 ml-4">{{ $announcement->created_at->format('d M Y') }}</span>
                            </div>
                            <p class="text-slate-500 text-sm">{{ $announcement->body }}</p>
                            <p class="text-xs text-slate-400 mt-2">— {{ $announcement->poster->name }}</p>
                        </li>
                    @empty
                        <li class="px-6 py-10 text-sm text-slate-400 text-center">No announcements posted yet.</li>
                    @endforelse
                </ul>

            </div>

        {{-- GRADES --}}
        @elseif($section === 'grades')

            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">

                <div class="px-6 py-4 border-b border-[#E8EEF4]">
                    <h3 class="text-base font-semibold text-slate-900">Grades</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $submissions->whereNotNull('grade')->count() }} of {{ $submissions->count() }} graded</p>
                </div>

                <div class="grid grid-cols-12 px-6 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest bg-[#E8EEF4] border-b border-[#C8D3E0]">
                    <div class="col-span-6">Assignment</div>
                    <div class="col-span-3">Grade</div>
                    <div class="col-span-3">Feedback</div>
                </div>

                <div class="divide-y divide-[#E8EEF4]">
                    @forelse($submissions as $submission)
                        <div class="grid grid-cols-12 px-6 py-4 items-center text-sm hover:bg-[#E8EEF4] transition-colors">
                            <div class="col-span-6 font-medium text-slate-800">{{ $submission->assignment->title }}</div>
                            <div class="col-span-3">
                                @if($submission->grade)
                                    <span class="text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-2.5 py-0.5">
                                        {{ $submission->grade }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">Pending</span>
                                @endif
                            </div>
                            <div class="col-span-3 text-xs text-slate-500">{{ $submission->feedback ?? '—' }}</div>
                        </div>
                    @empty
                        <div class="px-6 py-10 text-sm text-slate-400 text-center">No grades recorded yet.</div>
                    @endforelse
                </div>

            </div>

        {{-- PEOPLE --}}
        @elseif($section === 'people')

            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">

                <div class="px-6 py-4 border-b border-[#E8EEF4]">
                    <h3 class="text-base font-semibold text-slate-900">People</h3>
                </div>

                {{-- LECTURER --}}
                <div class="px-6 py-4 border-b border-[#E8EEF4]">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-3">Lecturer</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-[#1B3A5C] text-white flex items-center justify-center text-sm font-semibold flex-shrink-0">
                            {{ strtoupper(substr($course->lecturer->name, 0, 1)) }}{{ strtoupper(substr(strrchr($course->lecturer->name, ' '), 1, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-900">{{ $course->lecturer->name }}</p>
                            <p class="text-xs text-slate-400">{{ $course->lecturer->email }}</p>
                        </div>
                    </div>
                </div>

                {{-- STUDENTS --}}
                <div class="p-6">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-3">
                        Students ({{ $course->students->count() }})
                    </p>
                    <ul class="divide-y divide-[#E8EEF4] rounded-xl border border-[#C8D3E0] overflow-hidden">
                        @foreach($course->students as $classmate)
                            <li class="px-4 py-3 flex justify-between items-center text-sm hover:bg-[#E8EEF4] transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-7 h-7 rounded-md bg-[#E8EEF4] border border-[#C8D3E0] text-[#1B3A5C] flex items-center justify-center text-[10px] font-semibold flex-shrink-0">
                                        {{ strtoupper(substr($classmate->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-slate-800">{{ $classmate->name }}</span>
                                </div>
                                <span class="text-xs text-slate-400">{{ $classmate->email }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

        @endif

    </div>

</div>

@endsection