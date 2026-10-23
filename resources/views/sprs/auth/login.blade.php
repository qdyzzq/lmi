<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SPRS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .outer-glow {
            background: radial-gradient(circle at 20% 15%, rgba(37, 99, 235, 0.45), transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(185, 28, 28, 0.5), transparent 50%),
                #05070d;
        }

        .border-gradient {
            background: linear-gradient(135deg, #1d4ed8 0%, #b91c1c 100%);
        }

        .btn-gradient {
            background: linear-gradient(90deg, #1d4ed8 0%, #b91c1c 100%);
        }
    </style>
</head>

<body class="outer-glow min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-4xl rounded-3xl p-[3px] shadow-2xl border-gradient relative">
       

        <div class="rounded-[21px] bg-white flex flex-col md:flex-row overflow-hidden relative">

            

            <div class="md:w-[45%] p-8 sm:p-10 relative" style="background: linear-gradient(160deg,#f1f5f9,#e2e8f0);">
                <div class="flex flex-col items-center text-center mb-6">
                    <img src="{{ asset('images/logopeso.png') }}" alt="PESO logo" class="w-33 h-33 mb-3 object-contain">
                    <h2 class="text-xl font-bold text-slate-800">PESO SPRS</h2>
                </div>

                <p class="text-sm leading-relaxed text-slate-600 text-justify mb-6">
                    The Public Employment Service Office (PESO) is a non-fee charging multi-employment service facility
                    or entity established or accredited pursuant to Republic Act No. 8759, otherwise known as the PESO
                    Act of 1999.
                    The Act provides that in order to carry out full employment and equality of employment opportunities
                    for all,
                    and to strengthen and expand the existing employment facilitation service machinery of the
                    government particularly
                    at the local levels, there shall be established in all capital towns of provinces, key cities, and
                    other strategic areas a Public Employment Service Office.
                </p>
            </div>

            <div class="flex-1 p-8 sm:p-10 relative overflow-hidden flex flex-col justify-center">
                <svg class="absolute inset-0 w-full h-full opacity-[0.35] pointer-events-none" viewBox="0 0 400 400">
                    <path d="M0 60 h50 v40 h60" stroke="#cbd5e1" stroke-width="1.5" fill="none" />
                    <circle cx="110" cy="100" r="3" fill="#cbd5e1" />
                    <path d="M400 320 h-60 v-50 h-40" stroke="#cbd5e1" stroke-width="1.5" fill="none" />
                    <circle cx="300" cy="270" r="3" fill="#cbd5e1" />
                    <path d="M20 380 v-60 h40" stroke="#cbd5e1" stroke-width="1.5" fill="none" />
                </svg>

                

                <h1 class="text-2xl font-semibold text-slate-500 text-center mb-6">Login to Your Account</h1>

                @if (session('success'))
                    <div data-flash class="mb-4 rounded-xl border border-green-200 bg-green-50 text-green-800 text-sm px-4 py-3 text-center">
                        {{ session('success') }}
                    </div>
                @endif

                @error('email')
                    <div data-flash class="mb-4 rounded-xl border border-red-200 bg-red-50 text-red-800 text-sm px-4 py-3 text-center">
                        {{ $message }}
                    </div>
                @enderror

                <form method="POST" action="{{ route('sprs.login.store') }}" class="space-y-5 relative z-10"
                    x-data="{ showPassword: false, loading: false }">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5">Username (your email)</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="you@region11.dole.gov.ph"
                            class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 outline-none text-sm text-slate-700 placeholder:text-slate-400 focus:ring-2 focus:border-blue-600 transition"
                            style="--tw-ring-color: rgba(29,78,216,0.15);" required autofocus>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1.5">Password</label>
                        <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 focus-within:ring-2 focus-within:border-blue-600 transition"
                            style="--tw-ring-color: rgba(29,78,216,0.15);">
                            <input :type="showPassword ? 'text' : 'password'" name="password" placeholder="Enter your password"
                                class="w-full outline-none text-sm text-slate-700 placeholder:text-slate-400" required>
                            <button type="button" @click="showPassword = !showPassword"
                                class="text-slate-400 hover:text-slate-600">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="1.5" style="display:none">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12c1.292 4.338 5.31 7.5 10.066 7.5.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" @click="loading = true"
                        class="w-full btn-gradient text-white font-semibold py-3.5 rounded-xl shadow-lg hover:opacity-95 active:scale-[0.99] transition-all duration-150 flex items-center justify-center gap-2">
                        <svg x-show="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24"
                            style="display:none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span x-text="loading ? 'Signing in...' : 'Log in'"></span>
                    </button>

                    <p class="text-center">
                        <a href="{{ route('sprs.password.request') }}" class="text-sm font-medium text-blue-700">Forgot password?</a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>

</html>