<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Smart Personal Expense Tracker</title>
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
    <div class="w-full max-w-md glass-box rounded-3xl p-8 shadow-2xl z-10 relative">
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-500 to-blue-600 flex items-center justify-center mx-auto mb-4 shadow-lg shadow-cyan-500/30">
                <i data-lucide="user-plus" class="w-7 h-7 text-white"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-white">Create Account</h1>
            <p class="text-xs text-slate-400 mt-1">Start tracking expenses & optimizing spending habits</p>
        </div>

        @if($errors->any())
        <div class="mb-6 bg-rose-500/10 border border-rose-500/30 text-rose-400 p-3.5 rounded-xl text-xs">
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Full Name *</label>
                <input type="text" name="name" required placeholder="John Doe" class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl py-3 px-4 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Email Address *</label>
                <input type="email" name="email" required placeholder="john@example.com" class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl py-3 px-4 text-sm text-slate-100 placeholder-slate-500 focus:outline-none focus:border-cyan-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Password *</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl py-3 px-4 text-sm text-slate-100 focus:outline-none focus:border-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Confirm Password *</label>
                    <input type="password" name="password_confirmation" required placeholder="••••••••" class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl py-3 px-4 text-sm text-slate-100 focus:outline-none focus:border-cyan-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Preferred Currency Symbol</label>
                <select name="currency_symbol" class="w-full bg-slate-900/80 border border-slate-700/80 rounded-xl py-3 px-4 text-sm text-slate-100 focus:outline-none focus:border-cyan-500">
                    <option value="₹">₹ (INR)</option>
                    <option value="$">$ (USD)</option>
                    <option value="€">€ (EUR)</option>
                    <option value="£">£ (GBP)</option>
                    <option value="AED">AED (AED)</option>
                </select>
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-cyan-500/25 transition">
                Create Account
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-800 text-center text-xs text-slate-400">
            Already have an account? <a href="{{ route('login') }}" class="text-cyan-400 font-bold hover:underline">Sign In</a>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
