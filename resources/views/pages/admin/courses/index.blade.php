@extends('layouts.app')

@section('title', 'Modules')
@section('page-title', 'Modules')

@section('content')
<div class="space-y-6">

    @include('pages.admin._flash')

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">All modules</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $courses->total() }} module{{ $courses->total() === 1 ? '' : 's' }} across both departments
            </p>
        </div>
        <a href="{{ route('admin.courses.create') }}"
           class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2 hover:bg-[#24507A] transition-colors">
            New module
        </a>
    </div>

    {{-- FILTERS --}}
    <form method="GET" action="{{ route('admin.courses.index') }}"
          class="rounded-2xl bg-white border border-[#C8D3E0] px-6 py-4 flex flex-wrap items-end gap-4">

        <div class="flex-1 min-w-full sm:min-w-48">
            <label for="q" class="block text-xs font-medium text-slate-600 mb-1.5">Search</label>
            <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Code or name"
                   class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">
        </div>

        <div class="w-full sm:w-48">
            <label for="department" class="block text-xs font-medium text-slate-600 mb-1.5">Department</label>
            <select id="department" name="department"
                    class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">
                <option value="">All departments</option>
                @foreach($departments as $department)
                    <option value="{{ $department->value }}" @selected(request('department') === $department->value)>
                        {{ $department->label() }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit"
                class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2.5 hover:bg-[#24507A] transition-colors">
            Filter
        </button>

        @if(request()->hasAny(['q', 'department']))
            <a href="{{ route('admin.courses.index') }}"
               class="rounded-lg border border-[#C8D3E0] text-slate-600 text-sm font-medium px-4 py-2.5 hover:bg-[#E8EEF4] transition-colors">
                Clear
            </a>
        @endif
    </form>

    {{-- TABLE --}}
    <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-x-auto">
        <div class="grid grid-cols-12 min-w-[620px] px-6 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest bg-[#E8EEF4] border-b border-[#C8D3E0]">
            <div class="col-span-2">Code</div>
            <div class="col-span-3">Module</div>
            <div class="col-span-2">Department</div>
            <div class="col-span-2">Lecturer</div>
            <div class="col-span-1">Students</div>
            <div class="col-span-2 text-right">Actions</div>
        </div>

        <div class="divide-y divide-[#E8EEF4]">
            @forelse($courses as $course)
                @php $dept = $course->departmentEnum(); @endphp
                <div class="grid grid-cols-12 min-w-[620px] px-6 py-4 items-center text-sm hover:bg-[#E8EEF4] transition-colors">

                    <div class="col-span-2 font-medium text-slate-800">{{ $course->code }}</div>

                    <div class="col-span-3">
                        <p class="text-slate-800">{{ $course->name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $course->start_date?->format('d M Y') }} — {{ $course->end_date?->format('d M Y') }}
                        </p>
                    </div>

                    <div class="col-span-2">
                        @if($dept)
                            <span class="text-xs font-medium rounded-full px-2.5 py-0.5 {{ $dept->pillClasses() }}">
                                {{ $dept->value }}
                            </span>
                        @endif
                    </div>

                    <div class="col-span-2 text-slate-500 truncate">{{ $course->lecturer?->name ?? '—' }}</div>

                    <div class="col-span-1 text-slate-500">{{ $course->enrollments_count }}</div>

                    <div class="col-span-2 flex justify-end gap-2">
                        <a href="{{ route('admin.courses.show', $course) }}"
                           class="text-xs font-medium text-white bg-[#1B3A5C] rounded-lg px-3 py-1.5 hover:bg-[#24507A] transition-colors">
                            Manage
                        </a>
                        <a href="{{ route('admin.courses.edit', $course) }}"
                           class="text-xs font-medium text-slate-600 border border-[#C8D3E0] rounded-lg px-3 py-1.5 hover:bg-[#E8EEF4] transition-colors">
                            Edit
                        </a>
                    </div>

                </div>
            @empty
                <p class="px-6 py-10 text-sm text-slate-400 text-center">No modules match these filters.</p>
            @endforelse
        </div>
    </div>

    @if($courses->hasPages())
        <div>{{ $courses->links() }}</div>
    @endif

</div>
@endsection
