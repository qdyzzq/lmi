<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OTP Verification - DOLE Labor Market Intelligence</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-dole-sans">
    <div class="bg-gradient-to-br from-dole-blue via-dole-dark to-dole-red min-h-screen flex items-center justify-center p-5 relative overflow-hidden">

        <!-- Animated background glows -->
        <div class="absolute w-[600px] h-[600px] rounded-full top-[-200px] right-[-200px] animate-pulse-glow" style="background: radial-gradient(circle, rgba(252, 209, 22, 0.1) 0%, transparent 70%);"></div>
        <div class="absolute w-[400px] h-[400px] rounded-full bottom-[-150px] left-[-150px] animate-pulse-glow-reverse" style="background: radial-gradient(circle, rgba(206, 17, 38, 0.08) 0%, transparent 70%);"></div>

        <!-- OTP Container -->
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-[480px] p-12 relative z-10 animate-fade-in-up">

            <!-- Decorative corner accents -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-dole-blue to-dole-red opacity-[0.08] rounded-tr-3xl rounded-bl-full"></div>
            <div class="absolute bottom-0 left-0 w-28 h-28 bg-gradient-to-tr from-dole-yellow to-dole-red opacity-5 rounded-bl-3xl" style="clip-path: polygon(0 50%, 50% 100%, 0 100%);"></div>

            <!-- Decorative stars -->
            <div class="absolute top-5 right-8 flex gap-1.5 opacity-15">
                <div class="w-3 h-3 bg-dole-yellow star-shape"></div>
                <div class="w-3 h-3 bg-dole-yellow star-shape"></div>
                <div class="w-3 h-3 bg-dole-yellow star-shape"></div>
            </div>

            <!-- Logo & Header -->
            <div class="text-center mb-10 animate-slide-in-left">
                <!-- Logo Container -->
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
                    OTP Verification
                </h1>
                <p class="text-slate-500 text-sm font-medium">
                    A 6-digit OTP has been sent to your registered phone number. It is valid for <span class="font-semibold text-dole-blue">10 minutes</span>.
                </p>
            </div>

            <!-- Flash Messages -->
            @if (session('success'))
                <div data-flash class="bg-gradient-to-r from-green-50 to-green-100 border-[1.5px] border-green-300 text-green-900 px-5 py-3.5 rounded-xl mb-6 text-sm font-medium animate-slide-in-left">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div data-flash class="bg-gradient-to-r from-red-50 to-red-100 border-[1.5px] border-red-300 text-red-900 px-5 py-3.5 rounded-xl mb-6 text-sm font-medium animate-slide-in-left">
                    {{ session('error') }}
                </div>
            @endif

            <!-- OTP Form -->
            <form method="POST" action="{{ route('otp.verify') }}" id="otpForm" class="space-y-6 animate-fade-in-up" style="animation-delay: 0.4s; animation-fill-mode: backwards;">
                @csrf

                <!-- OTP Input -->
                <div>
                    <label for="otp" class="block text-sm font-semibold text-dole-dark mb-2">
                        Enter OTP
                    </label>
                    <input
                        type="text"
                        id="otp"
                        name="otp"
                        maxlength="6"
                        inputmode="numeric"
                        pattern="[0-9]{6}"
                        class="w-full px-5 py-3.5 text-[22px] font-bold tracking-[0.5em] text-center border-2 border-slate-200 rounded-xl focus:outline-none focus:border-dole-blue focus:ring-4 focus:ring-dole-blue/10 transition-all duration-300 focus:-translate-y-0.5 placeholder:text-slate-300 placeholder:tracking-normal placeholder:text-base placeholder:font-normal @error('otp') !border-dole-red @enderror"
                        placeholder="••••••"
                        required
                        autofocus
                    >
                    @error('otp')
                        <p class="text-dole-red text-[13px] mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Verify Button -->
                <button
                    type="submit"
                    class="w-full bg-gradient-to-r from-dole-blue to-dole-red text-white font-dole-display font-bold text-base px-4 py-4 rounded-xl hover:-translate-y-0.5 hover:shadow-2xl shadow-lg shadow-dole-blue/30 hover:shadow-dole-blue/40 transition-all duration-300 active:translate-y-0 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none relative overflow-hidden gradient-shimmer"
                >
                    Verify OTP
                </button>
            </form>

            <!-- Resend OTP -->
            <div class="mt-6 text-center">
                <p class="text-slate-500 text-sm mb-3">Didn't receive the OTP?</p>
                <form method="POST" action="{{ route('otp.resend') }}" id="resendForm">
                    @csrf
                    <button
                        type="submit"
                        class="text-dole-blue font-semibold text-sm hover:text-dole-red hover:underline transition-colors duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Resend OTP
                    </button>
                </form>
            </div>

            <!-- Back to Login -->
            <div class="mt-4 text-center">
                <a href="{{ route('login') }}" class="text-slate-400 text-sm hover:text-dole-dark transition-colors duration-300">
                    ← Back to Login
                </a>
            </div>

        </div>

        <script>
            // Add loading state on verify submit
            document.getElementById('otpForm').addEventListener('submit', function () {
                const btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.textContent = 'Verifying...';
            });

            // Add loading state on resend submit
            document.getElementById('resendForm').addEventListener('submit', function () {
                const btn = this.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.textContent = 'Sending...';
            });

            // Only allow numeric input in OTP field
            document.getElementById('otp').addEventListener('input', function () {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            // Auto-dismiss flash messages after 4 seconds
            const flashMessages = document.querySelectorAll('[data-flash]');
            flashMessages.forEach(msg => {
                setTimeout(() => {
                    msg.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    msg.style.opacity = '0';
                    msg.style.transform = 'translateY(-10px)';
                    setTimeout(() => msg.remove(), 500);
                }, 4000);
            });
        </script>

    </div><!-- end gradient wrapper -->
</body>
</html>