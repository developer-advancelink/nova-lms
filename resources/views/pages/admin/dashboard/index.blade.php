@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    @include('pages.admin._flash')

    {{-- HEADER --}}
    <div class="rounded-2xl bg-white border border-[#C8D3E0] p-6">
        <h2 class="text-xl font-semibold text-slate-900">Welcome back, {{ Auth::user()->name }}</h2>
        <p class="text-sm text-slate-500 mt-1">
            You oversee {{ count($departments) }} departments,
            {{ $totals['users'] }} accounts and {{ $totals['modules'] }} modules.
        </p>
    </div>

    {{-- PER-DEPARTMENT SUMMARY --}}
    <div>
        <p class="text-xs font-semibold text-slate-200 uppercase tracking-widest mb-3">Departments</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($departments as $row)
                @php $dept = $row['department']; @endphp
                <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-hidden">
                    <div class="h-2 {{ $dept->value === 'SPT' ? 'bg-sky-200' : 'bg-emerald-200' }}"></div>
                    <div class="p-5">
                        <span class="text-xs font-medium rounded-full px-2.5 py-0.5 {{ $dept->pillClasses() }}">
                            {{ $dept->value }}
                        </span>
                        <p class="font-semibold text-slate-900 mt-3">{{ $dept->label() }}</p>
                    </div>
                    <div class="flex items-center border-t border-b border-[#E8EEF4] divide-x divide-[#E8EEF4]">
                        <div class="flex-1 px-5 py-3 text-center">
                            <p class="text-lg font-semibold text-slate-900">{{ $row['lecturers'] }}</p>
                            <p class="text-[10px] uppercase tracking-widest text-slate-400 mt-0.5">Lecturers</p>
                        </div>
                        <div class="flex-1 px-5 py-3 text-center">
                            <p class="text-lg font-semibold text-slate-900">{{ $row['students'] }}</p>
                            <p class="text-[10px] uppercase tracking-widest text-slate-400 mt-0.5">Students</p>
                        </div>
                        <div class="flex-1 px-5 py-3 text-center">
                            <p class="text-lg font-semibold text-slate-900">{{ $row['modules'] }}</p>
                            <p class="text-[10px] uppercase tracking-widest text-slate-400 mt-0.5">Modules</p>
                        </div>
                    </div>
                    <div class="px-5 py-3 flex gap-2">
                        <a href="{{ route('admin.users.index', ['department' => $dept->value]) }}"
                           class="text-xs font-medium text-slate-600 border border-[#C8D3E0] rounded-lg px-3 py-1.5 hover:bg-[#E8EEF4] transition-colors">
                            View accounts
                        </a>
                        <a href="{{ route('admin.courses.index', ['department' => $dept->value]) }}"
                           class="text-xs font-medium text-slate-600 border border-[#C8D3E0] rounded-lg px-3 py-1.5 hover:bg-[#E8EEF4] transition-colors">
                            View modules
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ACCOUNTS WITH NO DEPARTMENT --}}
    @if($unassigned->isNotEmpty())
        <div class="rounded-2xl bg-white border border-[#C8D3E0] overflow-x-auto">
            <div class="px-6 py-4 border-b border-[#E8EEF4]">
                <h3 class="font-semibold text-slate-900">Accounts without a department</h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    These users cannot see any modules until a department is assigned.
                </p>
            </div>
            <div class="divide-y divide-[#E8EEF4]">
                @foreach($unassigned as $user)
                    <div class="grid grid-cols-12 min-w-[620px] px-6 py-4 items-center text-sm hover:bg-[#E8EEF4] transition-colors">
                        <div class="col-span-5 font-medium text-slate-800">{{ $user->name }}</div>
                        <div class="col-span-4 text-slate-500">{{ $user->email }}</div>
                        <div class="col-span-1">
                            <span class="text-xs font-medium rounded-full px-2.5 py-0.5 {{ $user->rolePillClasses() }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                        <div class="col-span-2 flex justify-end">
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="text-xs font-medium text-white bg-[#1B3A5C] rounded-lg px-3 py-1.5 hover:bg-[#24507A] transition-colors">
                                Assign
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- QUICK ACTIONS --}}
    <div class="flex gap-3">
        <a href="{{ route('admin.users.create') }}"
           class="rounded-lg bg-[#C9A227] text-[#0F1B2D] text-sm font-semibold px-4 py-2 hover:bg-[#D9B437] transition-colors">
            Create account
        </a>
        <a href="{{ route('admin.courses.create') }}"
           class="rounded-lg border border-white/30 text-white text-sm font-medium px-4 py-2 hover:bg-white/10 transition-colors">
            Create module
        </a>
    </div>

</div>
@endsection
