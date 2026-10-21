<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - DOLE Labor Market Intelligence</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-dole-sans">
    <div class="bg-gradient-to-br from-dole-blue via-dole-dark to-dole-red min-h-screen flex items-center justify-center p-5 relative overflow-hidden">

        <div class="absolute w-[600px] h-[600px] rounded-full top-[-200px] right-[-200px] animate-pulse-glow" style="background: radial-gradient(circle, rgba(252, 209, 22, 0.1) 0%, transparent 70%);"></div>
        <div class="absolute w-[400px] h-[400px] rounded-full bottom-[-150px] left-[-150px] animate-pulse-glow-reverse" style="background: radial-gradient(circle, rgba(206, 17, 38, 0.08) 0%, transparent 70%);"></div>

        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-[480px] p-12 relative z-10 animate-fade-in-up">

            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-dole-blue to-dole-red opacity-[0.08] rounded-tr-3xl rounded-bl-full"></div>

            <div class="text-center mb-8 animate-slide-in-left">
                <div class="inline-flex flex-col items-center gap-2 mb-5 px-6 py-4 bg-gradient-to-r from-dole-blue/5 to-dole-red/5 rounded-2xl border-2 border-dole-blue/10">
                    <img src="{{ asset('images/dole_logo.png') }}" alt="DOLE Logo" class="w-16 h-16 object-contain drop-shadow-md">
                    <div class="text-center">
                        <div class="font-dole-display text-sm font-extrabold text-dole-dark uppercase tracking-widest">
                            Department of Labor and Employment
                        </div>
                        <div class="text-base font-bold text-dole-dark uppercase tracking-widest mt-0.5">
                            Region XI
                        </div>
                    </div>
                </div>

                <h1 class="font-dole-display text-3xl font-bold text-dole-dark mb-2 leading-tight">
                    Reset Password
                </h1>
            </div>

            <form method="POST" action="{{ route('password.update') }}" id="resetForm" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label for="email" class="block text-sm font-semibold text-dole-dark mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $email) }}" maxlength="255" required
                        class="w-full px-5 py-3.5 text-[15px] border-2 border-slate-200 rounded-xl focus:outline-none focus:border-dole-blue focus:ring-4 focus:ring-dole-blue/10 transition-all duration-300 @error('email') !border-dole-red @enderror">
                    @error('email')
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
                    Reset Password
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-slate-400 text-sm hover:text-dole-dark transition-colors duration-300">
                    ← Back to Login
                </a>
            </div>
        </div>

        <script>
            document.getElementById('resetForm').addEventListener('submit', function () {
                const btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.textContent = 'Saving...';
            });
        </script>
    </div>
</body>
</html>
