{{-- SHARED PASSWORD PARTIAL --}}

<div class="rounded-2xl bg-white shadow-sm overflow-hidden max-w-xl">

    {{-- HEADER --}}
    <div class="px-6 py-4 border-b border-[#C8D3E0] flex justify-between items-center">
        <h2 class="text-base font-semibold text-slate-900">Change Password</h2>
        <button form="password-form" type="submit"
                class="rounded-lg bg-[#1B3A5C] text-white text-sm font-medium px-4 py-2 hover:bg-[#24507A] transition-colors">
            Update Password
        </button>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="px-6 pt-4">
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- ERROR MESSAGES --}}
    @if($errors->any())
        <div class="px-6 pt-4">
            <div class="rounded-lg bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form id="password-form"
          method="POST"
          action="{{ Auth::user()->role === 'lecturer' ? '/lecturer/account/password' : '/account/password' }}">
        @csrf
        @method('PATCH')

        <div class="p-6 space-y-5" x-data="{
            password: '',
            name: '{{ strtolower(Auth::user()->name) }}',
            get hasLength()  { return this.password.length >= 12 },
            get hasUpper()   { return /[A-Z]/.test(this.password) },
            get hasLower()   { return /[a-z]/.test(this.password) },
            get hasNumber()  { return /[0-9]/.test(this.password) },
            get hasSymbol()  { return /[^A-Za-z0-9]/.test(this.password) },
            get noName()     {
                if (!this.password || !this.name) return true;
                return !this.password.toLowerCase().includes(this.name.split(' ')[0]) &&
                       !this.password.toLowerCase().includes(this.name.split(' ').pop());
            },
            get strength() {
                let score = [this.hasLength, this.hasUpper, this.hasLower, this.hasNumber, this.hasSymbol, this.noName].filter(Boolean).length;
                if (score <= 2) return 'weak';
                if (score <= 4) return 'fair';
                if (score === 5) return 'good';
                return 'strong';
            },
            get strengthColor() {
                return { weak: 'bg-rose-500', fair: 'bg-amber-500', good: 'bg-sky-500', strong: 'bg-emerald-500' }[this.strength];
            },
            get strengthWidth() {
                return { weak: 'w-1/4', fair: 'w-2/4', good: 'w-3/4', strong: 'w-full' }[this.strength];
            },
            get strengthLabel() {
                return { weak: 'Weak', fair: 'Fair', good: 'Good', strong: 'Strong' }[this.strength];
            }
        }">

            {{-- CURRENT PASSWORD --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Current Password</label>
                <input type="password"
                       name="current_password"
                       placeholder="Enter current password"
                       class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900
                              placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white focus:border-[#1B3A5C] transition-all">
            </div>

            {{-- NEW PASSWORD --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">New Password</label>
                <input type="password"
                       name="password"
                       placeholder="Enter new password"
                       x-model="password"
                       class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900
                              placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white focus:border-[#1B3A5C] transition-all">

                {{-- STRENGTH BAR --}}
                <div x-show="password.length > 0" class="mt-2 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <div class="h-1.5 flex-1 rounded-full bg-[#E8EEF4] overflow-hidden mr-3">
                            <div class="h-full rounded-full transition-all duration-300"
                                 :class="[strengthColor, strengthWidth]"></div>
                        </div>
                        <span class="text-xs font-medium" :class="{
                            'text-rose-600': strength === 'weak',
                            'text-amber-600': strength === 'fair',
                            'text-sky-600': strength === 'good',
                            'text-emerald-600': strength === 'strong'
                        }" x-text="strengthLabel"></span>
                    </div>
                </div>

                {{-- REQUIREMENTS CHECKLIST --}}
                <div x-show="password.length > 0" class="mt-3 rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] p-3 space-y-1.5">
                    <template x-for="[check, label] in [
                        [hasLength,  'At least 12 characters'],
                        [hasUpper,   'One uppercase letter (A–Z)'],
                        [hasLower,   'One lowercase letter (a–z)'],
                        [hasNumber,  'One number (0–9)'],
                        [hasSymbol,  'One symbol (!@#$%^&*...)'],
                        [noName,     'Does not contain your name']
                    ]">
                        <div class="flex items-center gap-2 text-xs">
                            <svg x-show="check" class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <svg x-show="!check" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12m-1 0a1 1 0 102 0 1 1 0 10-2 0" />
                            </svg>
                            <span :class="check ? 'text-slate-700' : 'text-slate-400'" x-text="label"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- CONFIRM PASSWORD --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Confirm New Password</label>
                <input type="password"
                       name="password_confirmation"
                       placeholder="Confirm new password"
                       class="w-full rounded-lg bg-[#E8EEF4] border border-[#C8D3E0] px-3 py-2.5 text-sm text-slate-900
                              placeholder:text-slate-400 outline-none focus:ring-2 focus:ring-[#1B3A5C] focus:bg-white focus:border-[#1B3A5C] transition-all">
            </div>

        </div>

    </form>

</div>