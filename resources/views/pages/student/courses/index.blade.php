@extends('layouts.app')

@section('title', 'Courses')
@section('page-title', 'Courses')

@section('content')

@php
    $accents = [
        ['bg' => 'bg-sky-50',    'text' => 'text-sky-700',    'border' => 'border-sky-200'],
        ['bg' => 'bg-amber-50',  'text' => 'text-amber-600',  'border' => 'border-amber-200'],
        ['bg' => 'bg-emerald-50','text' => 'text-emerald-600','border' => 'border-emerald-200'],
        ['bg' => 'bg-rose-50',   'text' => 'text-rose-600',   'border' => 'border-rose-200'],
    ];
@endphp

{{-- Alpine state: manages star toggles client-side --}}
<div x-data="{
    enrollments: {{ json_encode($enrollments->map(fn($e) => [
        'id'      => $e->course->id,
        'name'    => $e->course->name,
        'code'    => $e->course->code,
        'start'   => $e->course->start_date->format('d M Y'),
        'end'     => $e->course->end_date->format('d M Y'),
        'lecturer'=> $e->course->lecturer->name,
        'starred' => (bool) $e->starred,
        'url'     => '/courses/' . $e->course->id,
        'starUrl' => '/courses/' . $e->course->id . '/star',
        'accent'  => $accents[$e->course->id % count($accents)],
    ])->values()) }},

    get starred() {
        return this.enrollments.filter(e => e.starred);
    },
    get all() {
        return [...this.enrollments].sort((a, b) => b.starred - a.starred);
    },

    async toggleStar(enrollment) {
        enrollment.starred = !enrollment.starred;
        await fetch(enrollment.starUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                'Content-Type': 'application/json',
            },
        });
    }
}" class="space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-white">My Courses</h2>
            <p class="text-xs text-slate-300 mt-0.5">{{ $enrollments->count() }} course{{ $enrollments->count() === 1 ? '' : 's' }} enrolled</p>
        </div>
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
            <input type="text"
                   placeholder="Search courses..."
                   class="rounded-lg bg-white border border-[#C8D3E0] pl-9 pr-3 py-2 text-sm text-slate-700
                          placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all w-52">
        </div>
    </div>

    {{-- STARRED COURSES --}}
    <div x-show="starred.length > 0" x-cloak>
        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-3">★ Starred</p>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <template x-for="e in starred" :key="'starred-' + e.id">
                <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden hover:shadow-md transition-all group">
                    <div class="h-2" :class="e.accent.bg"></div>
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-3">
                            <span class="text-[10px] font-semibold uppercase tracking-widest rounded-full px-2.5 py-0.5 border"
                                  :class="[e.accent.text, e.accent.bg, e.accent.border]"
                                  x-text="e.code"></span>
                            <button @click="toggleStar(e)"
                                    class="text-xl leading-none transition-all hover:scale-110 text-[#C9A227]"
                                    title="Remove from starred">
                                ★
                            </button>
                        </div>
                        <h3 class="text-base font-semibold text-slate-900 mb-1 group-hover:text-[#1B3A5C] transition-colors"
                            x-text="e.name"></h3>
                        <p class="text-xs text-slate-400 mb-4" x-text="e.lecturer"></p>
                        <div class="pt-3 border-t border-[#E8EEF4]">
                            <a :href="e.url"
                               class="block text-center text-xs font-medium text-white bg-[#1B3A5C] rounded-lg py-2 hover:bg-[#24507A] transition-colors">
                                Open Course
                            </a>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ALL COURSES TABLE --}}
    <div>
        <p x-show="starred.length > 0" x-cloak
           class="text-[10px] font-semibold uppercase tracking-widest text-slate-400 mb-3">All Courses</p>

        <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-x-auto">

            {{-- TABLE HEADER --}}
            <div class="grid grid-cols-12 min-w-[620px] px-6 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest bg-[#E8EEF4] border-b border-[#C8D3E0]">
                <div class="col-span-1"></div>
                <div class="col-span-4">Course Name</div>
                <div class="col-span-2">Code</div>
                <div class="col-span-3">Duration</div>
                <div class="col-span-2">Lecturer</div>
            </div>

            {{-- COURSE ROWS --}}
            <template x-if="all.length > 0">
                <div class="divide-y divide-[#E8EEF4]">
                    <template x-for="e in all" :key="'row-' + e.id">
                        <div class="grid grid-cols-12 min-w-[620px] px-6 py-4 items-center text-sm hover:bg-[#E8EEF4] transition-colors">

                            {{-- STAR TOGGLE --}}
                            <div class="col-span-1">
                                <button @click="toggleStar(e)"
                                        class="text-xl leading-none transition-all hover:scale-110"
                                        :class="e.starred ? 'text-[#C9A227]' : 'text-slate-300 hover:text-[#C9A227]'"
                                        :title="e.starred ? 'Remove from starred' : 'Add to starred'">
                                    <span x-text="e.starred ? '★' : '☆'"></span>
                                </button>
                            </div>

                            {{-- COURSE NAME --}}
                            <div class="col-span-4">
                                <a :href="e.url"
                                   class="font-medium text-slate-900 hover:text-[#1B3A5C] transition-colors"
                                   x-text="e.name"></a>
                            </div>

                            {{-- CODE --}}
                            <div class="col-span-2">
                                <span class="text-xs font-medium text-[#1B3A5C] bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2.5 py-0.5"
                                      x-text="e.code"></span>
                            </div>

                            {{-- DURATION --}}
                            <div class="col-span-3 text-xs text-slate-500">
                                <span x-text="e.start + ' — ' + e.end"></span>
                            </div>

                            {{-- LECTURER --}}
                            <div class="col-span-2 text-xs text-slate-400" x-text="e.lecturer"></div>

                        </div>
                    </template>
                </div>
            </template>

            <template x-if="all.length === 0">
                <div class="px-6 py-12 text-sm text-slate-400 text-center">
                    You are not enrolled in any courses yet.
                </div>
            </template>

        </div>
    </div>

</div>

{{-- CSRF for fetch requests --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

@endsection