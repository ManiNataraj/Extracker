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
        .clay-card {
            background: #1e293b;
            box-shadow: 12px 12px 30px rgba(0, 0, 0, 0.6), -6px -6px 20px rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 2rem;
        }
        .clay-inset {
            background: #0f172a;
            box-shadow: inset 4px 4px 10px rgba(0, 0, 0, 0.6), inset -3px -3px 8px rgba(255, 255, 255, 0.03);
            border-radius: 1.25rem;
        }
        .clay-btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%);
            box-shadow: 6px 8px 20px rgba(99, 102, 241, 0.45), inset 0 1px 2px rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            border-radius: 1.25rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .clay-btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 8px 12px 26px rgba(99, 102, 241, 0.55);
        }
        .clay-btn-primary:active {
            transform: translateY(1px) scale(0.98);
            box-shadow: inset 3px 3px 8px rgba(0, 0, 0, 0.6);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Ambient Soft Glows -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-cyan-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md clay-card p-8 z-10 relative">
        <div class="text-center mb-8">
            <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-indigo-500 to-cyan-500 flex items-center justify-center mx-auto mb-4 shadow-xl shadow-indigo-500/30 border border-white/20">
                <i data-lucide="wallet" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Smart Expense Tracker</h1>
            <p class="text-xs text-slate-400 mt-1 font-medium">Sign in to your financial intelligence dashboard</p>
        </div>

        @if($errors->any())
        <div class="mb-6 bg-rose-500/10 border border-rose-500/30 text-rose-400 p-3.5 rounded-2xl text-xs font-semibold">
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Email Address</label>
                <div class="relative">
                    <input type="email" name="email" value="{{ old('email', 'alex@tracker.com') }}" required class="w-full clay-inset py-3 pl-11 pr-4 text-sm text-slate-100 placeholder-slate-500 focus:outline-none">
                    <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-4 top-3.5"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">Password</label>
                <div class="relative">
                    <input type="password" name="password" value="password" required class="w-full clay-inset py-3 pl-11 pr-4 text-sm text-slate-100 placeholder-slate-500 focus:outline-none">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-4 top-3.5"></i>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center text-slate-400 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-slate-900 border-slate-700 text-indigo-500 mr-2">
                    Remember me
                </label>
                <a href="#" class="text-indigo-400 font-extrabold hover:underline">Forgot password?</a>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 clay-btn-primary font-extrabold text-sm">
                Sign In
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-800 text-center text-xs text-slate-400">
            Don't have an account? <a href="{{ route('register') }}" class="text-indigo-400 font-extrabold hover:underline">Create Account</a>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
