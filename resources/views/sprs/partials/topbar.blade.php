<header
    x-data="{
        open: false,
        uploading: false,
        avatarUrl: @js(Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : null),

        async uploadAvatar(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('Please choose an image file.');
                event.target.value = '';
                return;
            }

            this.uploading = true;

            const formData = new FormData();
            formData.append('avatar', file);

            try {
                const res = await fetch('{{ route('sprs.profile.avatar.update') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: formData,
                });

                if (!res.ok) throw new Error('Upload failed');

                const data = await res.json();
                this.avatarUrl = data.avatar_url;
            } catch (e) {
                alert('Could not upload avatar. Please try again.');
            } finally {
                this.uploading = false;
                event.target.value = '';
            }
        },
    }"
    class="sticky top-0 z-40 bg-white border-b border-slate-200 px-6 py-3 flex items-center justify-between gap-4"
>
    {{-- WHO'S SIGNED IN, AND WHAT PERIOD

         These are shared office machines, and `no.duplicate` quietly ends
         the previous session when someone else signs in. Whoever sits
         down should be able to tell whose account is open without
         clicking the avatar.

         The period sits here rather than only on the pages that happen to
         show it — someone deep in DOLE Programs on the 20th shouldn't
         have to navigate away to learn the deadline is tomorrow. --}}
    <div class="min-w-0">
        @if ($navUser)
            <div class="flex items-center gap-2 min-w-0">
                <p class="text-[13.5px] font-medium text-slate-800 truncate">{{ $navUser['name'] }}</p>
                @if ($navUser['role'])
                    <span class="shrink-0 px-2 py-0.5 rounded text-[10.5px] font-semibold bg-slate-100 text-slate-600">
                        {{ $navUser['role'] }}
                    </span>
                @endif
            </div>

            <p class="text-[11.5px] text-slate-500 truncate mt-0.5">
                {{ $navUser['office'] }}

                @if ($navPeriod)
                    · {{ $navPeriod['label'] }}

                    @if ($navPeriod['daysLeft'] !== null)
                        @if ($navPeriod['daysLeft'] < 0)
                            · <span class="text-red-600">deadline passed {{ $navPeriod['deadline'] }}</span>
                        @elseif ($navPeriod['daysLeft'] === 0)
                            · <span class="text-red-600 font-medium">deadline today</span>
                        @elseif ($navPeriod['daysLeft'] <= 5)
                            · <span class="text-amber-700 font-medium">{{ $navPeriod['daysLeft'] }} day{{ $navPeriod['daysLeft'] === 1 ? '' : 's' }} left</span>
                        @else
                            · {{ $navPeriod['daysLeft'] }} days left
                        @endif
                    @endif
                @endif
            </p>
        @endif
    </div>

    <div class="relative shrink-0" @click.outside="open = false">
        {{-- Avatar trigger --}}
        <button
            type="button"
            @click="open = !open"
            class="flex items-center gap-2 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            <template x-if="avatarUrl">
                <img :src="avatarUrl" alt="Avatar" class="w-9 h-9 rounded-full object-cover border border-slate-200">
            </template>
            <template x-if="!avatarUrl">
                <span class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center border border-slate-300">
                    <svg class="w-5 h-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </span>
            </template>
        </button>

        {{-- Dropdown --}}
        <div
            x-show="open"
            x-cloak
            x-transition
            class="absolute right-0 mt-2 w-64 rounded-lg border border-slate-200 bg-white shadow-lg py-2"
        >
            {{-- Identity --}}
            <div class="px-4 py-3 flex items-center gap-3 border-b border-slate-100">
                <template x-if="avatarUrl">
                    <img :src="avatarUrl" alt="Avatar" class="w-11 h-11 rounded-full object-cover border border-slate-200 flex-shrink-0">
                </template>
                <template x-if="!avatarUrl">
                    <span class="w-11 h-11 rounded-full bg-slate-200 flex items-center justify-center border border-slate-300 flex-shrink-0">
                        <svg class="w-6 h-6 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </span>
                </template>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-slate-800 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                    @if ($navUser && $navUser['office'])
                        <p class="text-xs text-slate-400 truncate mt-0.5">{{ $navUser['office'] }}</p>
                    @endif
                </div>
            </div>

            {{-- Avatar upload --}}
            <div class="px-4 py-2.5 border-b border-slate-100">
                <label class="flex items-center justify-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700 cursor-pointer rounded-lg px-3 py-2 hover:bg-blue-50 transition">
                    <svg x-show="!uploading" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 7.5L12 3m0 0L7.5 7.5M12 3v13.5" />
                    </svg>
                    <svg x-show="uploading" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span x-text="uploading ? 'Uploading…' : (avatarUrl ? 'Change avatar' : 'Add avatar')"></span>
                    <input type="file" accept="image/*" class="hidden" :disabled="uploading" @change="uploadAvatar($event)">
                </label>
            </div>

            {{-- Logout --}}
            <form method="POST" action="{{ route('sprs.logout') }}" class="px-2 pt-1">
                @csrf
                <button
                    type="submit"
                    class="w-full flex items-center gap-2 text-sm font-medium text-red-600 hover:text-red-700 rounded-lg px-3 py-2 hover:bg-red-50 transition"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>