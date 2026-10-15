@extends('layouts.app')

@section('title', 'My Courses')
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

{{-- SINGLE x-data SCOPE FOR BOTH MODALS --}}
<div x-data="{
    editOpen: false,
    editCourseId: null,
    editCourseName: '',
    editCourseDescription: '',
    editCourseStart: '',
    editCourseEnd: '',

    assignOpen: false,
    assignStep: 1,
    selectedCourse: null,
    selectedCourseName: '',
    courses: {{ json_encode($courses->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'code' => $c->code])->values()) }},

    openEdit(id, name, description, start, end) {
        this.editCourseId = id;
        this.editCourseName = name;
        this.editCourseDescription = description;
        this.editCourseStart = start;
        this.editCourseEnd = end;
        this.editOpen = true;
    },
    closeEdit() {
        this.editOpen = false;
        this.editCourseId = null;
    },

    selectCourse(course) {
        this.selectedCourse = course.id;
        this.selectedCourseName = course.code + ' — ' + course.name;
        this.assignStep = 2;
    },
    openAssign() {
        this.assignOpen = true;
        this.assignStep = 1;
        this.selectedCourse = null;
        this.selectedCourseName = '';
    },
    closeAssign() {
        this.assignOpen = false;
        this.assignStep = 1;
        this.selectedCourse = null;
        this.selectedCourseName = '';
    }
}"
@keydown.escape.window="closeEdit(); closeAssign()">

    {{-- ================================================ --}}
    {{-- EDIT COURSE MODAL                                --}}
    {{-- ================================================ --}}

    <div x-show="editOpen" x-cloak style="display:none"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeEdit()"
         class="fixed inset-0 bg-[#0F1B2D] bg-opacity-60 z-40">
    </div>

    <div x-show="editOpen" x-cloak style="display:none"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed inset-0 z-50 flex items-center justify-center p-6 pointer-events-none">

        <div class="bg-white rounded-2xl border border-[#C8D3E0] shadow-2xl w-full max-w-lg pointer-events-auto"
             @click.stop>

            <div class="px-6 py-4 border-b border-[#E8EEF4] flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Edit Course</h2>
                    <p class="text-xs text-slate-400 mt-0.5" x-text="editCourseName"></p>
                </div>
                <button @click="closeEdit()"
                        class="rounded-lg p-1.5 text-slate-400 hover:bg-[#E8EEF4] hover:text-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" :action="'/lecturer/courses/' + editCourseId" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PATCH">

                <div class="rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-4 py-3 text-xs text-slate-500">
                    Course code and enrolled students can only be modified by an administrator.
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Course Name</label>
                    <input type="text" name="name" x-model="editCourseName" required
                           class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900
                                  outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Description <span class="text-slate-400 font-normal">(optional)</span></label>
                    <textarea name="description" x-model="editCourseDescription" rows="3"
                              placeholder="Brief description of this course..."
                              class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900
                                     placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all resize-none"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Start Date</label>
                        <input type="date" name="start_date" x-model="editCourseStart" required
                               class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-700
                                      outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">End Date</label>
                        <input type="date" name="end_date" x-model="editCourseEnd" required
                               class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-700
                                      outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="closeEdit()"
                            class="flex-shrink-0 rounded-lg border border-[#C8D3E0] text-slate-600 text-sm font-medium px-4 py-2.5
                                   hover:bg-[#E8EEF4] transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 rounded-lg bg-[#1B3A5C] text-white text-sm font-medium py-2.5
                                   hover:bg-[#24507A] transition-colors">
                        Save Changes
                    </button>
                </div>
            </form>

        </div>
    </div>

    {{-- ================================================ --}}
    {{-- NEW ASSIGNMENT MODAL                             --}}
    {{-- ================================================ --}}

    <div x-show="assignOpen" x-cloak style="display:none"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closeAssign()"
         class="fixed inset-0 bg-[#0F1B2D] bg-opacity-60 z-40">
    </div>

    <div x-show="assignOpen" x-cloak style="display:none"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="fixed inset-0 z-50 flex items-center justify-center p-6 pointer-events-none">

        <div class="bg-white rounded-2xl border border-[#C8D3E0] shadow-2xl w-full max-w-lg pointer-events-auto"
             @click.stop>

            <div class="px-6 py-4 border-b border-[#E8EEF4] flex items-center justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">New Assignment</h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        <span x-show="assignStep === 1">Step 1 of 2 — Select a course</span>
                        <span x-show="assignStep === 2" x-cloak style="display:none" x-text="'Step 2 of 2 — ' + selectedCourseName"></span>
                    </p>
                </div>
                <button @click="closeAssign()"
                        class="rounded-lg p-1.5 text-slate-400 hover:bg-[#E8EEF4] hover:text-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- STEP INDICATOR --}}
            <div class="px-6 pt-4 flex items-center gap-2">
                <div class="flex items-center gap-2 flex-1">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0"
                         :class="assignStep >= 1 ? 'bg-[#1B3A5C] text-white' : 'bg-[#E8EEF4] text-slate-400'">1</div>
                    <div class="flex-1 h-px" :class="assignStep >= 2 ? 'bg-[#1B3A5C]' : 'bg-[#E8EEF4]'"></div>
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0"
                         :class="assignStep >= 2 ? 'bg-[#1B3A5C] text-white' : 'bg-[#E8EEF4] text-slate-400'">2</div>
                </div>
            </div>

            {{-- STEP 1 --}}
            <div x-show="assignStep === 1" class="p-6 space-y-3">
                <p class="text-sm text-slate-500 mb-4">Which course is this assignment for?</p>
                <template x-for="course in courses" :key="course.id">
                    <button @click="selectCourse(course)"
                            class="w-full flex items-center gap-4 rounded-xl border border-[#C8D3E0] px-4 py-3.5
                                   hover:border-[#1B3A5C] hover:bg-[#E8EEF4] transition-all text-left group">
                        <div class="w-9 h-9 rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] flex items-center justify-center flex-shrink-0
                                    group-hover:bg-[#1B3A5C] group-hover:border-[#1B3A5C] transition-colors">
                            <span class="text-xs font-bold text-[#1B3A5C] group-hover:text-white transition-colors"
                                  x-text="course.code.substring(0, 2)"></span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-slate-900" x-text="course.name"></p>
                            <p class="text-xs text-slate-400 mt-0.5" x-text="course.code"></p>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-[#1B3A5C] transition-colors ml-auto flex-shrink-0"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </template>
            </div>

            {{-- STEP 2 --}}
            <div x-show="assignStep === 2" x-cloak style="display:none" class="p-6">
                <form method="POST" :action="'/lecturer/courses/' + selectedCourse + '/assignments'"
                      enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="course_id" :value="selectedCourse">

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Assignment Title</label>
                        <input type="text" name="title" required placeholder="e.g. Lab Report 1"
                               class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900
                                      placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Description <span class="text-slate-400 font-normal">(optional)</span></label>
                        <textarea name="description" rows="3" placeholder="Describe what students need to do..."
                                  class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900
                                         placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all resize-none"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Due Date & Time</label>
                        <input type="datetime-local" name="due_date" required
                               class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-700
                                      outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Attachment <span class="text-slate-400 font-normal">(optional — PDF, DOCX, PPTX)</span></label>
                        <input type="file" name="file" accept=".pdf,.docx,.pptx"
                               class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-600
                                      outline-none focus:ring-2 focus:ring-[#1B3A5C] transition-all">
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="assignStep = 1"
                                class="flex-shrink-0 rounded-lg border border-[#C8D3E0] text-slate-600 text-sm font-medium px-4 py-2.5
                                       hover:bg-[#E8EEF4] transition-colors">
                            ← Back
                        </button>
                        <button type="submit"
                                class="flex-1 rounded-lg bg-[#1B3A5C] text-white text-sm font-medium py-2.5
                                       hover:bg-[#24507A] transition-colors">
                            Create Assignment
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- ================================================ --}}
    {{-- PAGE CONTENT                                     --}}
    {{-- ================================================ --}}

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-slate-900">My Courses</h2>
                <p class="text-xs text-slate-500 mt-0.5">{{ $courses->count() }} course{{ $courses->count() === 1 ? '' : 's' }} this semester</p>
            </div>
            <button @click="openAssign()"
                    class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2 hover:bg-[#24507A] transition-colors">
                + New Assignment
            </button>
        </div>

        {{-- COURSE CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

            @forelse($courses as $course)
                @php
                    $accent = $accents[$course->id % count($accents)];
                    $startFormatted = optional($course->start_date)->format('Y-m-d') ?? '';
                    $endFormatted   = optional($course->end_date)->format('Y-m-d') ?? '';
                    $nameEscaped    = addslashes($course->name);
                    $descEscaped    = addslashes($course->description ?? '');
                @endphp

                <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden hover:shadow-md transition-all group">

                    <div class="h-2 {{ $accent['bg'] }}"></div>

                    <div class="p-5">

                        <div class="flex items-start justify-between mb-3">
                            <span class="text-[10px] font-semibold uppercase tracking-widest {{ $accent['text'] }} {{ $accent['bg'] }} border {{ $accent['border'] }} rounded-full px-2.5 py-0.5">
                                {{ $course->code }}
                            </span>
                            <span class="text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full px-2.5 py-0.5">
                                Active
                            </span>
                        </div>

                        <h3 class="text-base font-semibold text-slate-900 mb-1 group-hover:text-[#1B3A5C] transition-colors">
                            {{ $course->name }}
                        </h3>
                        <p class="text-xs text-slate-400 mb-4">
                            {{ optional($course->start_date)->format('d M Y') ?? '—' }} — {{ optional($course->end_date)->format('d M Y') ?? '—' }}
                        </p>

                        <div class="flex items-center gap-4 py-3 border-t border-b border-[#E8EEF4] mb-4">
                            <div class="text-center">
                                <p class="text-lg font-bold text-slate-900">{{ $course->students->count() }}</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide">Students</p>
                            </div>
                            <div class="w-px h-8 bg-[#E8EEF4]"></div>
                            <div class="text-center">
                                <p class="text-lg font-bold text-slate-900">{{ $course->assignments->count() ?? 0 }}</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide">Assignments</p>
                            </div>
                            <div class="w-px h-8 bg-[#E8EEF4]"></div>
                            <div class="text-center">
                                @php $pending = $course->assignments->sum(fn($a) => $a->submissions->whereNull('grade')->count()); @endphp
                                <p class="text-lg font-bold {{ $pending > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $pending }}</p>
                                <p class="text-[10px] text-slate-400 uppercase tracking-wide">To Grade</p>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="/lecturer/courses/{{ $course->id }}"
                               class="flex-1 text-center text-xs font-medium text-white bg-[#1B3A5C] rounded-lg py-2 hover:bg-[#24507A] transition-colors">
                                Open Course
                            </a>
                            <button
                               @click="openEdit({{ $course->id }}, '{{ $nameEscaped }}', '{{ $descEscaped }}', '{{ $startFormatted }}', '{{ $endFormatted }}')"
                               class="text-xs font-medium text-slate-600 border border-[#C8D3E0] rounded-lg px-3 py-2 hover:bg-[#E8EEF4] transition-colors">
                                Edit
                            </button>
                        </div>

                    </div>

                </div>

            @empty
                <div class="col-span-3 rounded-2xl bg-white border border-[#C8D3E0] p-10 text-center">
                    <p class="text-sm text-slate-500">No courses assigned yet.</p>
                </div>
            @endforelse

        </div>

        {{-- ASSIGNMENTS TABLE --}}
        <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">

            <div class="px-6 py-4 border-b border-[#E8EEF4] flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Assignments</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Across all your courses</p>
                </div>
                <button @click="openAssign()"
                        class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2 hover:bg-[#24507A] transition-colors">
                    + New Assignment
                </button>
            </div>

            <div class="grid grid-cols-12 px-6 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest bg-[#E8EEF4] border-b border-[#C8D3E0]">
                <div class="col-span-4">Title</div>
                <div class="col-span-2">Course</div>
                <div class="col-span-2">Due Date</div>
                <div class="col-span-2">Submissions</div>
                <div class="col-span-2 text-right">Actions</div>
            </div>

            <div class="divide-y divide-[#E8EEF4]">

                @forelse($assignments as $assignment)
                    @php
                        $isPast    = $assignment->due_date->isPast();
                        $isToday   = $assignment->due_date->isToday();
                        $submitted = $assignment->submissions->count();
                        $total     = $assignment->course->students->count();
                        $allDone   = $submitted >= $total && $total > 0;
                    @endphp

                    <div class="grid grid-cols-12 px-6 py-4 items-center text-sm hover:bg-[#E8EEF4] transition-colors">

                        <div class="col-span-4">
                            <p class="font-medium {{ $isPast && !$isToday ? 'text-slate-400' : 'text-slate-900' }}">
                                {{ $assignment->title }}
                            </p>
                        </div>

                        <div class="col-span-2">
                            <span class="text-xs font-medium text-slate-500">{{ $assignment->course->code }}</span>
                        </div>

                        <div class="col-span-2">
                            @if($isPast && !$isToday)
                                <span class="text-xs font-medium text-slate-400 bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2.5 py-0.5">Closed</span>
                            @elseif($isToday)
                                <span class="text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-full px-2.5 py-0.5">Due today</span>
                            @else
                                <span class="text-xs font-medium text-slate-600 bg-[#E8EEF4] border border-[#C8D3E0] rounded-full px-2.5 py-0.5">
                                    {{ $assignment->due_date->format('d M, g:i A') }}
                                </span>
                            @endif
                        </div>

                        <div class="col-span-2">
                            <span class="text-xs font-medium rounded-full px-2.5 py-0.5
                                {{ $allDone ? 'text-emerald-700 bg-emerald-50 border border-emerald-200' : 'text-slate-600 bg-slate-100 border border-slate-200' }}">
                                {{ $submitted }} / {{ $total }}
                            </span>
                        </div>

                        <div class="col-span-2 flex justify-end gap-2">
                            <a href="/lecturer/courses/{{ $assignment->course_id }}?section=grades"
                               class="text-xs font-medium text-white bg-[#1B3A5C] rounded-lg px-3 py-1.5 hover:bg-[#24507A] transition-colors
                               {{ $isPast && !$isToday ? 'opacity-50' : '' }}">
                                Grade
                            </a>
                            <a href="#"
                               class="text-xs font-medium text-slate-600 border border-[#C8D3E0] rounded-lg px-3 py-1.5 hover:bg-[#E8EEF4] transition-colors">
                                Edit
                            </a>
                        </div>

                    </div>

                @empty
                    <div class="px-6 py-10 text-sm text-slate-400 text-center">No assignments created yet.</div>
                @endforelse

            </div>

        </div>

    </div>

</div>

@endsection