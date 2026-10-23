@extends('sprs.layouts.layout')

@section('title', 'Create Account')

@section('content')
<div class="min-h-screen bg-slate-50 py-10 px-4">
    <div class="max-w-2xl mx-auto">

        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-slate-800">Create New Account</h1>
            <p class="text-sm text-slate-500 mt-1">Add new SPRS users.</p>
        </div>

        {{-- Session success --}}
        @if (session('success'))
            <div class="mb-6 flex items-start gap-3 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">
                <svg class="w-5 h-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l1.75 1.75L15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-emerald-700">{{ session('success') }}</p>
            </div>
        @endif

        {{-- General validation error summary --}}
        @if ($errors->any())
            <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                <p class="text-sm font-medium text-red-700 mb-1">Please fix the following:</p>
                <ul class="list-disc list-inside text-sm text-red-600 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{--
            Alpine component handles:
            - showing/hiding the Province field (skipped for RO and super_admin)
            - showing/hiding the Office field (skipped for super_admin)
            - filtering the office dropdown by role AND province
            - basic password visibility toggle
            - clearing the whole form

            Expects the controller to pass:
              $roleTypes -> role_types rows (id, name) — single source of truth
              $offices   -> offices rows (id, name, office_type, province_id)
              $provinces -> provinces_type rows (id, name)
        --}}
        <div
            x-data="{
                role: '{{ old('role', '') }}',
                provinceId: '{{ old('province_id', '') }}',
                officeId: '{{ old('office_id', '') }}',
                offices: {{ $offices->map(fn($o) => ['id' => $o->id, 'name' => $o->name, 'office_type' => (string) $o->office_type, 'province_id' => $o->province_id ? (string) $o->province_id : null])->values() }},
                roleTypesMap: {{ $roleTypes->pluck('name', 'id')->mapWithKeys(fn($name, $id) => [(string) $id => $name]) }},
                showPassword: false,
                showConfirm: false,
                get isSuperAdmin() { return this.role === 'super_admin' },
                get isRO() { return this.roleTypesMap[this.role] === 'RO' },
                get needsProvince() { return this.role && !this.isSuperAdmin && !this.isRO },
                get filteredOffices() {
                    if (!this.role || this.isSuperAdmin) return [];
                    if (this.isRO) return this.offices.filter(o => o.office_type === String(this.role));
                    if (!this.provinceId) return [];
                    return this.offices.filter(o => o.office_type === String(this.role) && o.province_id === String(this.provinceId));
                },
                resetForm() {
                    // Clear the Alpine-bound dropdowns
                    this.role = '';
                    this.provinceId = '';
                    this.officeId = '';
                    this.showPassword = false;
                    this.showConfirm = false;
                    // Clear the plain (non x-model) text inputs
                    this.$refs.name.value = '';
                    this.$refs.email.value = '';
                    this.$refs.phone_number.value = '';
                    this.$refs.password.value = '';
                    this.$refs.password_confirmation.value = '';
                }
            }"
            x-init="
                $watch('role', () => { provinceId = ''; officeId = ''; });
                $watch('provinceId', () => { officeId = ''; });
            "
            class="bg-white rounded-xl border border-slate-200 shadow-sm"
        >
            <form method="POST" action="{{ route('sprs.superadmin.create_account.store') }}" class="p-6 sm:p-8 space-y-6">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        x-ref="name"
                        value="{{ old('name') }}"
                        placeholder="Juan Dela Cruz"
                        class="w-full rounded-lg border px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 @else border-slate-300 @enderror"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        x-ref="email"
                        value="{{ old('email') }}"
                        placeholder="name@dole.gov.ph"
                        class="w-full rounded-lg border px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-300 @else border-slate-300 @enderror"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone Number (used for OTP delivery via SMS) --}}
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-slate-700 mb-1.5">Phone Number</label>
                    <input
                        type="tel"
                        id="phone_number"
                        name="phone_number"
                        x-ref="phone_number"
                        value="{{ old('phone_number') }}"
                        placeholder="09171234567"
                        class="w-full rounded-lg border px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone_number') border-red-300 @else border-slate-300 @enderror"
                    >
                    <p class="mt-1.5 text-xs text-slate-400">Used to send the OTP code at login.</p>
                    @error('phone_number')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password / Confirm Password --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                        <div class="relative">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                id="password"
                                name="password"
                                x-ref="password"
                                placeholder="Min. 8 characters"
                                class="w-full rounded-lg border px-3.5 py-2.5 pr-10 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-300 @else border-slate-300 @enderror"
                            >
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                                <svg x-show="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <input
                                :type="showConfirm ? 'text' : 'password'"
                                id="password_confirmation"
                                name="password_confirmation"
                                x-ref="password_confirmation"
                                placeholder="Re-enter password"
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 pr-10 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                            <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600">
                                <svg x-show="!showConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <svg x-show="showConfirm" x-cloak class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-slate-400 -mt-3">Minimum 8 characters. Both fields must match.</p>

                <hr class="border-slate-100">

                {{-- Role --}}
                <div>
                    <label for="role" class="block text-sm font-medium text-slate-700 mb-1.5">Role</label>
                    <select
                        id="role"
                        name="role"
                        x-model="role"
                        class="w-full rounded-lg border px-3.5 py-2.5 text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('role') border-red-300 @else border-slate-300 @enderror"
                    >
                        <option value="" disabled>Select a role</option>
                        {{-- Every role option comes from role_types — never hardcode these,
                             the DB is the single source of truth. --}}
                        @foreach ($roleTypes as $roleType)
                            <option value="{{ $roleType->id }}">{{ $roleType->name }}</option>
                        @endforeach
                        <option value="super_admin">Super Admin</option>
                    </select>
                    @error('role')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Province (hidden for RO and super_admin, required for everything else) --}}
                <div x-show="needsProvince" x-cloak x-transition>
                    <label for="province_id" class="block text-sm font-medium text-slate-700 mb-1.5">Province</label>
                    <select
                        id="province_id"
                        name="province_id"
                        x-model="provinceId"
                        class="w-full rounded-lg border px-3.5 py-2.5 text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('province_id') border-red-300 @else border-slate-300 @enderror"
                    >
                        <option value="" disabled>Select a province</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1.5 text-xs text-slate-400">Filters the office list below to that province only.</p>
                    @error('province_id')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Office (hidden for super_admin, filtered by role + province) --}}
                <div x-show="!isSuperAdmin" x-cloak x-transition>
                    <label for="office_id" class="block text-sm font-medium text-slate-700 mb-1.5">Office</label>
                    <select
                        id="office_id"
                        name="office_id"
                        x-model="officeId"
                        class="w-full rounded-lg border px-3.5 py-2.5 text-sm text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-slate-50 disabled:text-slate-400 @error('office_id') border-red-300 @else border-slate-300 @enderror"
                        :disabled="!role || (needsProvince && !provinceId)"
                    >
                        <option value="" disabled>
                            <span x-text="!role ? 'Select a role first' : (needsProvince && !provinceId ? 'Select a province first' : 'Select an office')"></span>
                        </option>
                        <template x-for="office in filteredOffices" :key="office.id">
                            <option :value="office.id" x-text="office.name"></option>
                        </template>
                    </select>
                    <p x-show="role && !isSuperAdmin && (!needsProvince || provinceId) && filteredOffices.length === 0" x-cloak class="mt-1.5 text-xs text-amber-600">
                        No offices found for this role/province.
                    </p>
                    @error('office_id')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Super admin notice --}}
                <div x-show="isSuperAdmin" x-cloak x-transition class="flex items-start gap-2.5 rounded-lg bg-blue-50 border border-blue-100 px-4 py-3">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-xs text-blue-700">Super admin accounts aren't tied to a specific office and have system-wide access.</p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="resetForm()" class="px-4 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-800 rounded-lg hover:bg-slate-50 transition">
                        Clear
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection