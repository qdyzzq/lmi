<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login - DOLE Labor Market Intelligence</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-dole-blue via-dole-dark to-dole-red min-h-screen flex items-center justify-center p-5 relative overflow-hidden font-dole-sans">
    
    <!-- Animated background glows -->
    <div class="absolute w-[600px] h-[600px] rounded-full top-[-200px] right-[-200px] animate-pulse-glow" style="background: radial-gradient(circle, rgba(252, 209, 22, 0.1) 0%, transparent 70%);"></div>
    <div class="absolute w-[400px] h-[400px] rounded-full bottom-[-150px] left-[-150px] animate-pulse-glow-reverse" style="background: radial-gradient(circle, rgba(206, 17, 38, 0.08) 0%, transparent 70%);"></div>

    <!-- Login Container -->
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
            <div class="inline-flex items-center gap-4 mb-5 px-6 py-3 bg-gradient-to-r from-dole-blue/5 to-dole-red/5 rounded-2xl border-2 border-dole-blue/10">
                <img src="{{ asset('images/dole_logo.png') }}" alt="DOLE Logo" class="w-16 h-16 object-contain drop-shadow-md">
                <div class="text-left">
                    <div class="font-dole-display text-2xl font-extrabold bg-gradient-to-r from-dole-blue to-dole-red bg-clip-text text-transparent tracking-wide">
                        DOLE
                    </div>
                    <div class="text-[11px] font-semibold text-dole-blue uppercase tracking-widest -mt-0.5">
                        Philippines
                    </div>
                </div>
            </div>
            
           
            <h1 class="font-dole-display text-3xl font-bold text-dole-dark mb-2 leading-tight">
                Labor Market Intelligence System
</h1>
        </div>

        <!-- Error Messages -->
        @if (session('error'))
            <div class="bg-gradient-to-r from-red-50 to-red-100 border-[1.5px] border-red-300 text-red-900 px-5 py-3.5 rounded-xl mb-6 text-sm font-medium animate-slide-in-left">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="bg-gradient-to-r from-green-50 to-green-100 border-[1.5px] border-green-300 text-green-900 px-5 py-3.5 rounded-xl mb-6 text-sm font-medium animate-slide-in-left">
                {{ session('success') }}
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login.post') }}" id="loginForm" class="space-y-6 animate-fade-in-up" style="animation-delay: 0.4s; animation-fill-mode: backwards;">
            @csrf

            <!-- Email Field -->
            <div>
                <label for="email" class="block text-sm font-semibold text-dole-dark mb-2">
                    Email Address
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="w-full px-5 py-3.5 text-[15px] border-2 border-slate-200 rounded-xl focus:outline-none focus:border-dole-blue focus:ring-4 focus:ring-dole-blue/10 transition-all duration-300 focus:-translate-y-0.5 placeholder:text-slate-400 @error('email') !border-dole-red @enderror"
                    value="{{ old('email') }}"
                    placeholder="Enter your email address" 
                    required 
                    autofocus
                >
                @error('email')
                    <p class="text-dole-red text-[13px] mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field -->
            <div>
                <label for="password" class="block text-sm font-semibold text-dole-dark mb-2">
                    Password
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="w-full px-5 py-3.5 text-[15px] border-2 border-slate-200 rounded-xl focus:outline-none focus:border-dole-blue focus:ring-4 focus:ring-dole-blue/10 transition-all duration-300 focus:-translate-y-0.5 placeholder:text-slate-400 @error('password') !border-dole-red @enderror"
                    placeholder="Enter your password" 
                    required
                >
                @error('password')
                    <p class="text-dole-red text-[13px] mt-1.5 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 cursor-pointer text-slate-700 font-medium">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember" 
                        class="w-[18px] h-[18px] cursor-pointer accent-dole-blue rounded"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <span>Remember me</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-dole-blue font-semibold hover:text-dole-red hover:underline transition-colors duration-300">
                    Forgot password?
                </a>
            </div>

            <!-- Login Button -->
            <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-dole-blue to-dole-red text-white font-dole-display font-bold text-base px-4 py-4 rounded-xl hover:-translate-y-0.5 hover:shadow-2xl shadow-lg shadow-dole-blue/30 hover:shadow-dole-blue/40 transition-all duration-300 active:translate-y-0 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none relative overflow-hidden gradient-shimmer"
            >
                Login 
            </button>
        </form>
    </div>

    <script>
        // Add loading state to button on submit
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.textContent = 'Logging in...';
        });
    </script>
</body>
</html>