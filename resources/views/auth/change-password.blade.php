<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Change Password - DOLE Labor Market Intelligence</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-dole-sans">
    @php
        $dashboard = auth()->user()->role === 'admin'
            ? route('admin.job.Market.Demands.Form')
            : route('statistician.review');
    @endphp

    <div class="bg-gradient-to-br from-dole-blue via-dole-dark to-dole-red min-h-screen flex items-center justify-center p-5 relative overflow-hidden">

        <div class="absolute w-[600px] h-[600px] rounded-full top-[-200px] right-[-200px] animate-pulse-glow" style="background: radial-gradient(circle, rgba(252, 209, 22, 0.1) 0%, transparent 70%);"></div>

        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-[480px] p-12 relative z-10 animate-fade-in-up">

            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-dole-blue to-dole-red opacity-[0.08] rounded-tr-3xl rounded-bl-full"></div>

            <div class="text-center mb-8">
                <h1 class="font-dole-display text-3xl font-bold text-dole-dark mb-2 leading-tight">
                    Change Password
                </h1>
                <p class="text-slate-500 text-sm font-medium">
                    Signed in as <span class="font-semibold text-dole-blue">{{ auth()->user()->email }}</span>
                </p>
            </div>

            @if (session('success'))
                <div class="bg-gradient-to-r from-green-50 to-green-100 border-[1.5px] border-green-300 text-green-900 px-5 py-3.5 rounded-xl mb-6 text-sm font-medium">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.change.update') }}" id="changeForm" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="current_password" class="block text-sm font-semibold text-dole-dark mb-2">Current Password</label>
                    <input type="password" id="current_password" name="current_password" maxlength="255" required autocomplete="current-password" autofocus
                        class="w-full px-5 py-3.5 text-[15px] border-2 border-slate-200 rounded-xl focus:outline-none focus:border-dole-blue focus:ring-4 focus:ring-dole-blue/10 transition-all duration-300 @error('current_password') !border-dole-red @enderror">
                    @error('current_password')
                        <p class="text-dole-red text-[13px] mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-dole-dark mb-2">New Password</label>
                    <input type="password" id="password" name="password" maxlength="255" required autocomplete="new-password"
                        class="w-full px-5 py-3.5 text-[15px] border-2 border-slate-200 rounded-xl focus:outline-none focus:border-dole-blue focus:ring-4 focus:ring-dole-blue/10 transition-all duration-300 @error('password') !border-dole-red @enderror">
                    @error('password')
                        @foreach ($errors->get('password') as $message)
                            <p class="text-dole-red text-[13px] mt-1.5 font-medium">{{ $message }}</p>
                        @endforeach
                    @enderror
                    @include('auth.partials.password-requirements')
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-dole-dark mb-2">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" maxlength="255" required autocomplete="new-password"
                        class="w-full px-5 py-3.5 text-[15px] border-2 border-slate-200 rounded-xl focus:outline-none focus:border-dole-blue focus:ring-4 focus:ring-dole-blue/10 transition-all duration-300">
                </div>

                <button type="submit"
                    class="w-full bg-gradient-to-r from-dole-blue to-dole-red text-white font-dole-display font-bold text-base px-4 py-4 rounded-xl hover:-translate-y-0.5 hover:shadow-2xl shadow-lg shadow-dole-blue/30 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none">
                    Change Password
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ $dashboard }}" class="text-slate-400 text-sm hover:text-dole-dark transition-colors duration-300">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        <script>
            document.getElementById('changeForm').addEventListener('submit', function () {
                const btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.textContent = 'Saving...';
            });
        </script>
    </div>
</body>
</html>
