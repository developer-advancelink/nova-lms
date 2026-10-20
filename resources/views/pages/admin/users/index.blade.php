@extends('layouts.app')

@section('title', 'Accounts')
@section('page-title', 'Accounts')

@section('content')
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
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="text-lg font-semibold text-slate-900">All accounts</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                {{ $users->total() }} account{{ $users->total() === 1 ? '' : 's' }} across both departments
            </p>

            {{-- Breakdown by role, so the mix is legible before scrolling. --}}
            <div class="flex flex-wrap items-center gap-3 mt-2">
                @foreach(['student' => 'Students', 'lecturer' => 'Lecturers', 'admin' => 'Administrators'] as $key => $label)
                    <span class="flex items-center gap-1.5 text-xs text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full
                            {{ $key === 'admin' ? 'bg-amber-500' : ($key === 'lecturer' ? 'bg-violet-500' : 'bg-slate-400') }}"></span>
                        <span class="font-medium text-slate-700">{{ $roleCounts[$key] ?? 0 }}</span>
                        {{ $label }}
                    </span>
                @endforeach
            </div>
        </div>
        <a href="{{ route('admin.users.create') }}"
           class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2 hover:bg-[#24507A] transition-colors">
            New account
        </a>
    </div>

    {{-- FILTERS --}}
    <form method="GET" action="{{ route('admin.users.index') }}"
          class="rounded-2xl bg-white border border-[#C8D3E0] px-6 py-4 flex flex-wrap items-end gap-4">

        <div class="flex-1 min-w-full sm:min-w-48">
            <label for="q" class="block text-xs font-medium text-slate-600 mb-1.5">Search</label>
            <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="Name or email"
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
                <option value="none" @selected(request('department') === 'none')>No department</option>
            </select>
        </div>

        <div class="w-full sm:w-40">
            <label for="role" class="block text-xs font-medium text-slate-600 mb-1.5">Role</label>
            <select id="role" name="role"
                    class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white transition-all">
                <option value="">All roles</option>
                @foreach(['student', 'lecturer', 'admin'] as $role)
                    <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit"
                class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2.5 hover:bg-[#24507A] transition-colors">
            Filter
        </button>

        @if(request()->hasAny(['q', 'department', 'role']))
            <a href="{{ route('admin.users.index') }}"
               class="rounded-lg border border-[#C8D3E0] text-slate-600 text-sm font-medium px-4 py-2.5 hover:bg-[#E8EEF4] transition-colors">
                Clear
            </a>
        @endif
    </form>

    {{-- TABLE --}}
    <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-x-auto">
        <div class="grid grid-cols-12 min-w-[620px] px-6 py-3 text-[10px] font-semibold text-slate-400 uppercase tracking-widest bg-[#E8EEF4] border-b border-[#C8D3E0]">
            <div class="col-span-3">Name</div>
            <div class="col-span-3">Email</div>
            <div class="col-span-2">Role</div>
            <div class="col-span-2">Department</div>
            <div class="col-span-2 text-right">Actions</div>
        </div>

        {{-- Rows arrive ordered by role (students, lecturers, admins) then
             name, so a heading is emitted each time the role changes. A role
             that spans a page boundary gets its heading again on the next
             page, which is the behaviour you want. --}}
        @php $currentRole = null; @endphp

        <div class="divide-y divide-[#E8EEF4]">
            @forelse($users as $user)

                @if($user->role !== $currentRole)
                    @php $currentRole = $user->role; @endphp
                    <div class="min-w-[620px] px-6 py-2.5 bg-[#F2F6FA] border-y border-[#C8D3E0] flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full
                            {{ $user->isAdmin() ? 'bg-amber-500' : ($user->isLecturer() ? 'bg-violet-500' : 'bg-slate-400') }}"></span>
                        <p class="text-[11px] font-semibold text-slate-600 uppercase tracking-widest">
                            {{ $user->roleGroupLabel() }}
                        </p>
                        <span class="text-[11px] text-slate-400">
                            {{ $roleCounts[$user->role] ?? 0 }}
                        </span>
                    </div>
                @endif

                <div class="grid grid-cols-12 min-w-[620px] px-6 py-4 items-center text-sm hover:bg-[#E8EEF4] transition-colors">

                    <div class="col-span-3">
                        <p class="font-medium text-slate-800">{{ $user->name }}</p>
                        @if($user->id_number)
                            <p class="text-xs text-slate-400 mt-0.5">{{ $user->id_number }}</p>
                        @endif
                    </div>

                    <div class="col-span-3 text-slate-500 truncate">{{ $user->email }}</div>

                    <div class="col-span-2">
                        <span class="text-xs font-medium rounded-full px-2.5 py-0.5 {{ $user->rolePillClasses() }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>

                    <div class="col-span-2">
                        @if($dept = $user->departmentEnum())
                            <span class="text-xs font-medium rounded-full px-2.5 py-0.5 {{ $dept->pillClasses() }}">
                                {{ $dept->value }}
                            </span>
                        @elseif($user->isAdmin())
                            <span class="text-xs text-slate-400">All departments</span>
                        @else
                            {{-- Fail-closed state: this account can see nothing. --}}
                            <span class="text-xs font-medium rounded-full px-2.5 py-0.5 bg-rose-50 text-rose-700">
                                No department
                            </span>
                        @endif
                    </div>

                    <div class="col-span-2 flex justify-end gap-2">
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-xs font-medium text-white bg-[#1B3A5C] rounded-lg px-3 py-1.5 hover:bg-[#24507A] transition-colors">
                            Edit
                        </a>
                        @unless($user->is(Auth::user()))
                            <button type="button"
                                    @click="ask(
                                        @js(route('admin.users.destroy', $user)),
                                        @js('Delete '.$user->name.'?'),
                                        @js($user->isLecturer()
                                            ? 'If this lecturer owns any modules the deletion will be refused, so you can reassign them first. Otherwise the account is removed permanently.'
                                            : 'This removes the account and every enrollment, submission and message belonging to it. This cannot be undone.')
                                    )"
                                    class="text-xs font-medium text-slate-600 border border-[#C8D3E0] rounded-lg px-3 py-1.5 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 transition-colors">
                                Delete
                            </button>
                        @endunless
                    </div>

                </div>
            @empty
                <p class="px-6 py-10 text-sm text-slate-400 text-center">No accounts match these filters.</p>
            @endforelse
        </div>
    </div>

    @if($users->hasPages())
        <div>{{ $users->links() }}</div>
    @endif

    @include('pages.admin._confirm-delete')

</div>
@endsection
