@extends('layouts.app')

@section('title', $course->name)
@section('page-title', $course->name)

@section('content')

@php $section = request('section', 'home'); @endphp

{{-- SUCCESS MESSAGE --}}
@if(session('success'))
    <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700 mb-6">
        {{ session('success') }}
    </div>
@endif

<div class="flex gap-6">

    {{-- COURSE NAV --}}
    <div class="w-52 flex-shrink-0 space-y-3">

        {{-- COURSE IDENTITY CARD --}}
        <div class="rounded-2xl bg-[#1B3A5C] p-4 text-white">
            <p class="text-[10px] font-semibold uppercase tracking-widest text-[#7C9EC0] mb-1">{{ $course->code }}</p>
            <p class="text-sm font-semibold leading-snug">{{ $course->name }}</p>
            <div class="mt-3 pt-3 border-t border-[#26527A] flex items-center gap-3">
                <div class="text-center">
                    <p class="text-lg font-bold">{{ $course->students->count() }}</p>
                    <p class="text-[9px] uppercase tracking-wide text-[#7C9EC0]">Students</p>
                </div>
                <div class="w-px h-8 bg-[#26527A]"></div>
                <div class="text-center">
                    <p class="text-lg font-bold">{{ $course->assignments->count() }}</p>
                    <p class="text-[9px] uppercase tracking-wide text-[#7C9EC0]">Assignments</p>
                </div>
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
                    <h3 class="text-base font-semibold text-slate-900">Recent Announcements</h3>
                    <a href="?section=announcements"
                       class="text-xs font-medium text-[#1B3A5C] hover:underline">View all</a>
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

            {{-- RECENT ASSIGNMENTS --}}
            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#E8EEF4] flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900">Assignments</h3>
                    <a href="?section=assignments"
                       class="text-xs font-medium text-[#1B3A5C] hover:underline">View all</a>
                </div>
                <div class="divide-y divide-[#E8EEF4]">
                    @forelse($course->assignments->take(3) as $assignment)
                        <div class="px-6 py-3 flex items-center justify-between text-sm hover:bg-[#E8EEF4] transition-colors">
                            <p class="font-medium text-slate-800">{{ $assignment->title }}</p>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium text-slate-600 bg-slate-100 border border-slate-200 rounded-full px-2.5 py-0.5">
                                    {{ $assignment->submissions->count() }} / {{ $course->students->count() }}
                                </span>
                                @if($assignment->due_date->isPast())
                                    <span class="text-xs font-medium text-slate-400 bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2.5 py-0.5">Closed</span>
                                @else
                                    <span class="text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-full px-2.5 py-0.5">
                                        {{ $assignment->due_date->format('d M') }}
                                    </span>
                                @endif
                            </div>
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
                    <h3 class="text-base font-semibold text-slate-900 mb-4">Upload Module</h3>

                    <form method="POST" action="/lecturer/courses/{{ $course->id }}/materials" enctype="multipart/form-data" class="space-y-3">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <input type="text" name="week" placeholder="Week (e.g. Week 1)"
                                   class="rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900
                                          placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">
                            <input type="text" name="title" placeholder="Title (e.g. Notes)" required
                                   class="rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900
                                          placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">
                            <select name="type"
                                    class="rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-700
                                           outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">
                                <option value="notes">Notes</option>
                                <option value="tutorial">Tutorial</option>
                                <option value="slides">Slides</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-3">
                            <input type="file" name="file" accept=".pdf,.docx,.pptx" required
                                   class="text-sm flex-1 rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5
                                          text-slate-600 outline-none focus:ring-2 focus:ring-[#1B3A5C] transition-all">
                            <button type="submit"
                                    class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2.5
                                           hover:bg-[#24507A] transition-colors flex-shrink-0">
                                Upload
                            </button>
                        </div>

                        @error('file')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </form>
                </div>

                <ul class="divide-y divide-[#E8EEF4]">
                    @forelse($course->materials as $material)
                        <li class="px-6 py-4 flex justify-between items-center text-sm hover:bg-[#E8EEF4] transition-colors">
                            <div class="flex items-center gap-3">
                                <span class="font-medium text-slate-800">
                                    @if($material->week) {{ $material->week }} — @endif{{ $material->title }}
                                </span>
                                <span class="text-xs font-medium text-[#1B3A5C] bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2.5 py-0.5 capitalize">
                                    {{ $material->type }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($material->file_url)
                                    <a href="{{ Storage::url($material->file_url) }}" target="_blank"
                                       class="text-xs font-medium text-slate-600 border border-[#C8D3E0] rounded-lg px-3 py-1.5 hover:bg-[#E8EEF4] transition-colors">
                                        View
                                    </a>
                                @endif
                                <form method="POST" action="/lecturer/courses/{{ $course->id }}/materials/{{ $material->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs font-medium text-rose-600 border border-rose-200 rounded-lg px-3 py-1.5 hover:bg-rose-50 transition-colors">
                                        Delete
                                    </button>
                                </form>
                            </div>
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
                    <h3 class="text-base font-semibold text-slate-900 mb-4">New Assignment</h3>

                    <form method="POST" action="/lecturer/courses/{{ $course->id }}/assignments" enctype="multipart/form-data" class="space-y-3">
                        @csrf

                        <input type="text" name="title" placeholder="Assignment title" required
                               class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900
                                      placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">

                        <textarea name="description" rows="2" placeholder="Description (optional)"
                                  class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900
                                         placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all resize-none"></textarea>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input type="datetime-local" name="due_date" required
                                   class="rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-700
                                          outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">
                            <input type="file" name="file" accept=".pdf,.docx,.pptx"
                                   class="rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-600
                                          outline-none focus:ring-2 focus:ring-[#1B3A5C] transition-all">
                        </div>

                        <button type="submit"
                                class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2.5
                                       hover:bg-[#24507A] transition-colors">
                            Create Assignment
                        </button>
                    </form>
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
                                <div class="text-right flex-shrink-0 ml-4 space-y-1.5">
                                    @if($assignment->due_date->isPast())
                                        <span class="text-xs font-medium text-slate-400 bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2.5 py-0.5 block">
                                            Closed
                                        </span>
                                    @else
                                        <span class="text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-full px-2.5 py-0.5 block">
                                            {{ $assignment->due_date->format('d M Y, g:i A') }}
                                        </span>
                                    @endif
                                    <span class="text-xs font-medium text-slate-600 bg-slate-100 border border-slate-200 rounded-full px-2.5 py-0.5 block">
                                        {{ $assignment->submissions->count() }} / {{ $course->students->count() }} submitted
                                    </span>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="px-6 py-10 text-sm text-slate-400 text-center">No assignments created yet.</li>
                    @endforelse
                </ul>

            </div>

        {{-- ANNOUNCEMENTS --}}
        @elseif($section === 'announcements')

            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">

                <div class="px-6 py-4 border-b border-[#E8EEF4]">
                    <h3 class="text-base font-semibold text-slate-900 mb-4">Post Announcement</h3>

                    <form method="POST" action="/lecturer/courses/{{ $course->id }}/announcements" class="space-y-3">
                        @csrf

                        <input type="text" name="title" placeholder="Announcement title" required
                               class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900
                                      placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">

                        <textarea name="body" rows="3" placeholder="Write your announcement..." required
                                  class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900
                                         placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all resize-none"></textarea>

                        <button type="submit"
                                class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2.5
                                       hover:bg-[#24507A] transition-colors">
                            Post Announcement
                        </button>
                    </form>
                </div>

                <ul class="divide-y divide-[#E8EEF4]">
                    @forelse($course->announcements as $announcement)
                        <li class="px-6 py-4 text-sm hover:bg-[#E8EEF4] transition-colors">
                            <div class="flex justify-between items-start mb-1">
                                <p class="font-semibold text-slate-900">{{ $announcement->title }}</p>
                                <span class="text-xs text-slate-400 flex-shrink-0 ml-4">{{ $announcement->created_at->format('d M Y') }}</span>
                            </div>
                            <p class="text-slate-500">{{ $announcement->body }}</p>
                        </li>
                    @empty
                        <li class="px-6 py-10 text-sm text-slate-400 text-center">No announcements posted yet.</li>
                    @endforelse
                </ul>

            </div>

        {{-- GRADES --}}
        @elseif($section === 'grades')

            <div class="space-y-4">
                @forelse($course->assignments as $assignment)

                    <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-x-auto">

                        <div class="px-6 py-3 bg-[#E8EEF4] border-b border-[#C8D3E0] flex items-center justify-between">
                            <p class="text-sm font-semibold text-slate-900">{{ $assignment->title }}</p>
                            <span class="text-xs text-slate-500">
                                {{ $assignment->submissions->whereNotNull('grade')->count() }} / {{ $course->students->count() }} graded
                            </span>
                        </div>

                        <div class="grid grid-cols-12 min-w-[620px] px-6 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest bg-[#E8EEF4] border-b border-[#C8D3E0]">
                            <div class="col-span-4">Student</div>
                            <div class="col-span-3">Grade</div>
                            <div class="col-span-5">Feedback</div>
                        </div>

                        @foreach($course->students as $student)
                            @php $submission = $assignment->submissions->firstWhere('user_id', $student->id); @endphp

                            <form method="POST"
                                  action="/lecturer/courses/{{ $course->id }}/assignments/{{ $assignment->id }}/grade/{{ $student->id }}"
                                  class="grid grid-cols-12 min-w-[620px] px-6 py-3 items-center text-sm border-b border-[#E8EEF4] last:border-0 gap-2 hover:bg-[#E8EEF4] transition-colors">
                                @csrf

                                <div class="col-span-4 font-medium text-slate-800">{{ $student->name }}</div>

                                <div class="col-span-3">
                                    <input type="text" name="grade" value="{{ $submission->grade ?? '' }}"
                                           placeholder="—"
                                           class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-2.5 py-1.5 text-xs text-slate-900
                                                  outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">
                                </div>

                                <div class="col-span-4">
                                    <input type="text" name="feedback" value="{{ $submission->feedback ?? '' }}"
                                           placeholder="Add feedback"
                                           class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-2.5 py-1.5 text-xs text-slate-900
                                                  outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">
                                </div>

                                <div class="col-span-1 text-right">
                                    <button type="submit"
                                            class="text-xs font-medium text-white bg-[#1B3A5C] rounded-lg px-2.5 py-1.5 hover:bg-[#24507A] transition-colors">
                                        Save
                                    </button>
                                </div>

                            </form>
                        @endforeach

                    </div>

                @empty
                    <div class="rounded-2xl bg-white border border-[#C8D3E0] px-6 py-10 text-sm text-slate-400 text-center">
                        No assignments to grade yet.
                    </div>
                @endforelse
            </div>

        {{-- PEOPLE --}}
        @elseif($section === 'people')

            <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">

                <div class="px-6 py-4 border-b border-[#E8EEF4]">
                    <h3 class="text-base font-semibold text-slate-900">People</h3>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $course->students->count() }} student{{ $course->students->count() === 1 ? '' : 's' }} enrolled</p>
                </div>

                <ul class="divide-y divide-[#E8EEF4]">
                    @forelse($course->students as $student)
                        <li class="px-6 py-3 flex justify-between items-center text-sm hover:bg-[#E8EEF4] transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-[#1B3A5C] text-white flex items-center justify-center text-xs font-semibold flex-shrink-0">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}{{ strtoupper(substr(strrchr($student->name, ' '), 1, 1)) }}
                                </div>
                                <span class="font-medium text-slate-800">{{ $student->name }}</span>
                            </div>
                            <span class="text-xs text-slate-400">{{ $student->email }}</span>
                        </li>
                    @empty
                        <li class="px-6 py-10 text-sm text-slate-400 text-center">No students enrolled yet.</li>
                    @endforelse
                </ul>

            </div>

        @endif

    </div>

</div>

@endsection