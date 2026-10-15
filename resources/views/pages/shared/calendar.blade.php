{{-- SHARED CALENDAR PARTIAL --}}
@php
    use Carbon\Carbon;

    $month = $currentMonth;
    $today = Carbon::today();

    $startOfMonth = $month->copy()->startOfMonth();
    $endOfMonth = $month->copy()->endOfMonth();
    $startWeekday = $startOfMonth->dayOfWeek; // 0 = Sunday
    $daysInMonth = $month->daysInMonth;

    $prevMonth = $month->copy()->subMonth()->format('Y-m');
    $nextMonth = $month->copy()->addMonth()->format('Y-m');

    $eventsByDay = $events->groupBy(fn($e) => $e->due_date->day);
@endphp

<div class="grid grid-cols-1 xl:grid-cols-4 gap-6">

    {{-- MAIN CALENDAR --}}
    <div class="xl:col-span-3 rounded-2xl bg-white shadow-sm p-6">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6">

            <h2 class="text-xl font-semibold text-slate-900">
                {{ $month->format('F Y') }}
            </h2>

            <div class="flex gap-2 text-sm">
                <a href="?month={{ $prevMonth }}"
                   class="rounded-lg border border-[#C8D3E0] px-3 py-1.5 text-slate-600 hover:bg-[#E8EEF4] transition-colors">
                    ← Prev
                </a>
                <a href="?month={{ Carbon::today()->format('Y-m') }}"
                   class="rounded-lg bg-[#1B3A5C] text-white px-3 py-1.5 font-medium hover:bg-[#24507A] transition-colors">
                    Today
                </a>
                <a href="?month={{ $nextMonth }}"
                   class="rounded-lg border border-[#C8D3E0] px-3 py-1.5 text-slate-600 hover:bg-[#E8EEF4] transition-colors">
                    Next →
                </a>
            </div>

        </div>

        {{-- WEEK HEADER --}}
        <div class="grid grid-cols-7 rounded-xl overflow-hidden border border-[#C8D3E0] mb-1">
            @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                <div class="py-2.5 text-center text-xs font-medium text-slate-500 uppercase tracking-wide bg-[#E8EEF4]
                    {{ !$loop->last ? 'border-r border-[#C8D3E0]' : '' }}">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        {{-- CALENDAR GRID --}}
        <div class="grid grid-cols-7 rounded-xl overflow-hidden border border-[#C8D3E0]">

            {{-- EMPTY CELLS BEFORE MONTH STARTS --}}
            @for($i = 0; $i < $startWeekday; $i++)
                <div class="h-28 bg-[#E8EEF4] border-r border-b border-[#C8D3E0]"></div>
            @endfor

            {{-- DAYS OF MONTH --}}
            @for($day = 1; $day <= $daysInMonth; $day++)

                @php
                    $isToday = $today->isSameDay($month->copy()->day($day));
                    $dayEvents = $eventsByDay->get($day, collect());
                    $col = ($startWeekday + $day - 1) % 7;
                    $isLastCol = $col === 6;
                @endphp

                <div class="h-28 p-2 text-sm overflow-hidden border-b border-[#C8D3E0] transition-colors
                    {{ !$isLastCol ? 'border-r border-[#C8D3E0]' : '' }}
                    {{ $isToday ? 'bg-[#E8EEF4]' : 'bg-white hover:bg-[#F2F5F9]' }}">

                    {{-- DAY NUMBER --}}
                    <div class="mb-1">
                        @if($isToday)
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#1B3A5C] text-white text-xs font-semibold">
                                {{ $day }}
                            </span>
                        @else
                            <span class="text-xs font-medium text-slate-500">{{ $day }}</span>
                        @endif
                    </div>

                    {{-- EVENTS --}}
                    @foreach($dayEvents->take(2) as $event)
                        <div class="mt-0.5 text-xs font-medium text-[#1B3A5C] bg-[#E8EEF4] border border-[#C8D3E0] rounded-md px-1.5 py-0.5 truncate"
                             title="{{ $event->title }} — {{ $event->course->code }}">
                            {{ $event->title }}
                        </div>
                    @endforeach

                    @if($dayEvents->count() > 2)
                        <div class="text-xs text-slate-400 mt-0.5">+{{ $dayEvents->count() - 2 }} more</div>
                    @endif

                </div>

            @endfor

        </div>

    </div>

    {{-- RIGHT PANEL --}}
    <div class="space-y-6">

        {{-- UPCOMING DUE DATES --}}
        <div class="rounded-2xl bg-white shadow-sm p-5">

            <h3 class="text-base font-semibold text-slate-900 mb-4">Upcoming Due Dates</h3>

            <ul class="space-y-3 text-sm">

                @forelse($upcoming as $event)
                    <li class="{{ !$loop->last ? 'border-b border-[#C8D3E0] pb-3' : '' }}">
                        <p class="font-medium text-slate-800">{{ $event->title }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $event->course->code }} · {{ $event->due_date->format('D, d M, g:i A') }}
                        </p>
                        @if($event->due_date->isToday())
                            <span class="inline-block mt-1.5 text-xs font-medium text-amber-700 bg-amber-50 ring-1 ring-amber-200 rounded-full px-2 py-0.5">
                                Due today
                            </span>
                        @elseif($event->due_date->isTomorrow())
                            <span class="inline-block mt-1.5 text-xs font-medium text-rose-700 bg-rose-50 ring-1 ring-rose-200 rounded-full px-2 py-0.5">
                                Due tomorrow
                            </span>
                        @endif
                    </li>
                @empty
                    <li class="text-sm text-slate-400">No upcoming due dates.</li>
                @endforelse

            </ul>

        </div>

    </div>

</div>