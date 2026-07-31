<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Personal Expense Tracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-box {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Ambient Glows -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md glass-box rounded-3xl p-8 shadow-2xl z-10 relative">
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-cyan-500/30">
                <i data-lucide="wallet" class="w-7 h-7 text-white"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Smart Expense Tracker</h1>
            <p class="text-xs text-slate-400 mt-1">Sign in to your financial intelligence dashboard</p>
        </div>

        @if($errors->any())
        <div class="mb-6 bg-rose-500/10 border border-rose-500/30 text-rose-400 p-3.5 rounded-xl text-xs">
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address</label>
                <div class="relative">
                    <input type="email" name="email" value="{{ old('email', 'alex@tracker.com') }}" required class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                    <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Password</label>
                <div class="relative">
                    <input type="password" name="password" value="password" required class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl py-3 pl-10 pr-4 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center text-slate-400 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-700 text-cyan-500 mr-2">
                    Remember me
                </label>
                <a href="#" class="text-cyan-400 hover:underline">Forgot password?</a>
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-cyan-500/25 transition">
                Sign In
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-800 text-center text-xs text-slate-400">
            Don't have an account? <a href="{{ route('register') }}" class="text-cyan-400 font-bold hover:underline">Create Account</a>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
