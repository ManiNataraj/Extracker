<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: true }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Smart Expense Tracker' }} - Intelligent Financial Insights</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        clay: {
                            bg: '#0f172a',
                            card: '#1e293b',
                            cardLight: '#f8fafc',
                            accent: '#6366f1',
                            accentCyan: '#0ea5e9',
                            success: '#10b981',
                            warning: '#f59e0b',
                            danger: '#f43f5e',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- FullCalendar CDN -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* ----------------------------------------------------
           PREMIUM CLAYMORPHISM & SOFT UI DESIGN SYSTEM
        ---------------------------------------------------- */
        /* Light Mode Clay Card */
        .clay-card {
            background: #f8fafc;
            box-shadow: 10px 10px 24px #cbd5e1, -10px -10px 24px #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.9);
            border-radius: 1.75rem;
        }

        /* Dark Mode Clay Card */
        .dark .clay-card {
            background: #1e293b;
            box-shadow: 8px 8px 22px rgba(0, 0, 0, 0.5), -5px -5px 16px rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 1.75rem;
        }

        /* Clay Inset / Sunken Field */
        .clay-inset {
            background: #e2e8f0;
            box-shadow: inset 3px 3px 6px #cbd5e1, inset -3px -3px 6px #ffffff;
            border-radius: 1.25rem;
            color: #0f172a;
        }
        .dark .clay-inset {
            background: #0f172a;
            box-shadow: inset 4px 4px 10px rgba(0, 0, 0, 0.6), inset -3px -3px 8px rgba(255, 255, 255, 0.03);
            border-radius: 1.25rem;
            color: #f8fafc;
        }
        .clay-inset option {
            background-color: #ffffff;
            color: #0f172a;
        }
        .dark .clay-inset option {
            background-color: #0f172a;
            color: #f8fafc;
        }

        /* ----------------------------------------------------
           TACTILE PREMIUM CLAY BUTTONS
        ---------------------------------------------------- */
        /* Secondary Neutral Clay Button */
        .clay-btn {
            background: linear-gradient(145deg, #f8fafc, #e2e8f0);
            box-shadow: 5px 5px 12px #cbd5e1, -4px -4px 10px #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.8);
            color: #334155;
            border-radius: 1.25rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dark .clay-btn {
            background: linear-gradient(145deg, #273549, #17202e);
            box-shadow: 5px 5px 14px rgba(0, 0, 0, 0.45), -4px -4px 10px rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            border-radius: 1.25rem;
        }
        .clay-btn:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 8px 8px 18px rgba(0, 0, 0, 0.5), -5px -5px 14px rgba(255, 255, 255, 0.06);
            color: #6366f1;
        }
        .clay-btn:active {
            transform: translateY(1px) scale(0.98);
            box-shadow: inset 3px 3px 6px rgba(0, 0, 0, 0.5);
        }

        /* Primary Accent Clay Button */
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
            box-shadow: 8px 12px 26px rgba(99, 102, 241, 0.55), inset 0 1px 2px rgba(255, 255, 255, 0.5);
        }
        .clay-btn-primary:active {
            transform: translateY(1px) scale(0.98);
            box-shadow: inset 3px 3px 8px rgba(0, 0, 0, 0.6);
        }

        /* Cyan Accent Clay Button */
        .clay-btn-cyan {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            box-shadow: 6px 8px 20px rgba(14, 165, 233, 0.4), inset 0 1px 2px rgba(255, 255, 255, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            border-radius: 1.25rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .clay-btn-cyan:hover {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 8px 12px 26px rgba(14, 165, 233, 0.55);
        }

        /* Clay Icon Badge */
        .clay-badge {
            box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.3), -2px -2px 6px rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Sidebar Soft Styling */
        .clay-sidebar {
            background: #111827;
            box-shadow: 8px 0 24px rgba(0, 0, 0, 0.4);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(100, 116, 139, 0.4);
            border-radius: 9999px;
        }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen font-sans flex flex-col antialiased selection:bg-indigo-500 selection:text-white"
      x-data="{ sidebarOpen: false, quickModalOpen: false, searchOpen: false }">

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 clay-sidebar flex flex-col justify-between transition-transform duration-300 transform md:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div>
                <!-- Brand Header -->
                <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800/80">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-indigo-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-indigo-500/30 group-hover:scale-105 transition-transform border border-white/20">
                            <i data-lucide="wallet" class="w-6 h-6 text-white"></i>
                        </div>
                        <div>
                            <span class="font-extrabold text-lg text-white tracking-wide">WalletWatch</span>
                            <span class="text-[10px] block text-indigo-400 font-bold tracking-widest uppercase">Smart Tracker</span>
                        </div>
                    </a>
                    <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <!-- Navigation Links -->
                <nav class="px-4 py-6 space-y-2 custom-scrollbar overflow-y-auto max-h-[calc(100vh-190px)]">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-indigo-500/20 to-cyan-500/10 text-indigo-400 border border-indigo-500/30 shadow-md' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('expenses.index') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('expenses.*') ? 'bg-gradient-to-r from-indigo-500/20 to-cyan-500/10 text-indigo-400 border border-indigo-500/30 shadow-md' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i data-lucide="receipt" class="w-5 h-5 mr-3"></i>
                        Expenses
                    </a>

                    <a href="{{ route('categories.index') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('categories.*') ? 'bg-gradient-to-r from-indigo-500/20 to-cyan-500/10 text-indigo-400 border border-indigo-500/30 shadow-md' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i data-lucide="grid" class="w-5 h-5 mr-3"></i>
                        Categories
                    </a>

                    <a href="{{ route('food.index') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('food.*') ? 'bg-gradient-to-r from-indigo-500/20 to-cyan-500/10 text-indigo-400 border border-indigo-500/30 shadow-md' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i data-lucide="apple" class="w-5 h-5 mr-3"></i>
                        Food Intelligence
                    </a>

                    <a href="{{ route('calendar.index') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('calendar.*') ? 'bg-gradient-to-r from-indigo-500/20 to-cyan-500/10 text-indigo-400 border border-indigo-500/30 shadow-md' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i data-lucide="calendar" class="w-5 h-5 mr-3"></i>
                        Calendar View
                    </a>

                    <a href="{{ route('analytics.index') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('analytics.*') ? 'bg-gradient-to-r from-indigo-500/20 to-cyan-500/10 text-indigo-400 border border-indigo-500/30 shadow-md' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i data-lucide="bar-chart-3" class="w-5 h-5 mr-3"></i>
                        Analytics
                    </a>

                    <a href="{{ route('budgets.index') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('budgets.*') ? 'bg-gradient-to-r from-indigo-500/20 to-cyan-500/10 text-indigo-400 border border-indigo-500/30 shadow-md' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i data-lucide="target" class="w-5 h-5 mr-3"></i>
                        Budgets
                    </a>

                    <a href="{{ route('recurring.index') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('recurring.*') ? 'bg-gradient-to-r from-indigo-500/20 to-cyan-500/10 text-indigo-400 border border-indigo-500/30 shadow-md' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i data-lucide="repeat" class="w-5 h-5 mr-3"></i>
                        Recurring
                    </a>

                    <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-indigo-500/20 to-cyan-500/10 text-indigo-400 border border-indigo-500/30 shadow-md' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i data-lucide="file-text" class="w-5 h-5 mr-3"></i>
                        Reports & Export
                    </a>

                    <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('settings.*') ? 'bg-gradient-to-r from-indigo-500/20 to-cyan-500/10 text-indigo-400 border border-indigo-500/30 shadow-md' : 'text-slate-400 hover:bg-slate-800/60 hover:text-slate-200' }}">
                        <i data-lucide="settings" class="w-5 h-5 mr-3"></i>
                        Settings
                    </a>
                </nav>
            </div>

            <!-- User Footer Profile & Quick Action -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/40">
                <div class="flex items-center justify-between mb-3 px-2">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold border border-indigo-500/30 shadow-inner">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="truncate">
                            <div class="text-sm font-bold text-slate-200 truncate">{{ auth()->user()->name ?? 'User' }}</div>
                            <div class="text-xs text-slate-400 truncate">{{ auth()->user()->email ?? '' }}</div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-400 rounded-xl hover:bg-slate-800/80 transition">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
                <button @click="quickModalOpen = true" class="w-full py-3 px-4 clay-btn-primary text-white text-xs font-extrabold flex items-center justify-center space-x-2">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Add Expense</span>
                </button>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col md:pl-64 overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-20 border-b border-slate-200 dark:border-slate-800/80 bg-white/70 dark:bg-slate-900/80 backdrop-blur-md px-6 flex items-center justify-between z-40">
                <div class="flex items-center space-x-4">
                    <button @click="sidebarOpen = true" class="md:hidden text-slate-400 hover:text-white">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                    <div class="relative w-64 md:w-96 hidden sm:block">
                        <input type="text"
                               @focus="searchOpen = true"
                               placeholder="Global search expenses, categories, tags... (Ctrl+K)"
                               class="w-full clay-inset py-2.5 pl-10 pr-4 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <button @click="darkMode = !darkMode" class="p-2.5 clay-btn flex items-center justify-center" title="Toggle Dark/Light Mode">
                        <i data-lucide="moon" class="w-4 h-4" x-show="darkMode"></i>
                        <i data-lucide="sun" class="w-4 h-4 text-amber-400" x-show="!darkMode" x-cloak></i>
                    </button>

                    <button @click="quickModalOpen = true" class="px-4 py-2.5 clay-btn-primary text-white text-xs font-extrabold flex items-center space-x-2">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">New Expense</span>
                    </button>
                </div>
            </header>

            <!-- Notification Toasts -->
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="fixed top-24 right-6 z-50 bg-emerald-500/90 text-white px-5 py-3 rounded-2xl shadow-xl backdrop-blur-md flex items-center space-x-3 border border-emerald-400/40">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
                <button @click="show = false" class="text-white/80 hover:text-white"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
            @endif

            <!-- Main Page View Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8 custom-scrollbar">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Quick Add Expense Modal -->
    <div x-show="quickModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="clay-card rounded-3xl max-w-lg w-full p-6 border border-slate-200 dark:border-slate-700/80 shadow-2xl relative" @click.away="quickModalOpen = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 dark:border-slate-700/60">
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center space-x-2">
                    <i data-lucide="plus-circle" class="w-5 h-5 text-indigo-500 dark:text-indigo-400"></i>
                    <span>Record New Expense</span>
                </h3>
                <button @click="quickModalOpen = false" class="text-slate-400 hover:text-slate-900 dark:hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Grocery Shopping, Fuel, Coffee" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Amount ({{ auth()->user()->currency_symbol ?? '₹' }}) *</label>
                        <input type="number" step="0.01" min="0.01" name="amount" required placeholder="0.00" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Date *</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full clay-inset px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Category</label>
                        <select name="category_id" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none">
                            <option value="">-- Select Category --</option>
                            @foreach(\App\Models\Category::where('user_id', auth()->id())->orWhereNull('user_id')->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Payment Method</label>
                        <select name="payment_method" class="w-full clay-inset px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 focus:outline-none">
                            <option value="UPI">UPI</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="Cash">Cash</option>
                            <option value="Net Banking">Net Banking</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Notes & Details</label>
                    <textarea name="notes" rows="2" placeholder="Optional notes..." class="w-full clay-inset px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200 dark:border-slate-700/60">
                    <button type="button" @click="quickModalOpen = false" class="px-5 py-2.5 clay-btn text-xs font-extrabold">Cancel</button>
                    <button type="submit" class="px-6 py-2.5 clay-btn-primary text-xs font-extrabold">Save Expense</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                lucide.createIcons();
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
