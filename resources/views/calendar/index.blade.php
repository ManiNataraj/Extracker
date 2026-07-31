@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ dateModalOpen: false, selectedDate: '', dateTotal: 0, dateExpenses: [], categoryBreakdown: {} }">
    <!-- Header Title -->
    <div>
        <h1 class="text-2xl font-extrabold text-white flex items-center gap-2">
            <i data-lucide="calendar" class="w-7 h-7 text-cyan-400"></i>
            <span>Interactive Expense Calendar</span>
        </h1>
        <p class="text-xs text-slate-400 mt-1">Click on any date to inspect full daily transactions and category breakdown.</p>
    </div>

    <!-- Calendar Card Container -->
    <div class="glass-card p-6 rounded-3xl border border-slate-800">
        <div id="calendar" class="min-h-[550px] text-slate-200"></div>
    </div>

    <!-- Date Details Modal -->
    <div x-show="dateModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div class="glass-card rounded-3xl max-w-lg w-full p-6 border border-slate-700/80 shadow-2xl relative" @click.away="dateModalOpen = false">
            <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                <div>
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i data-lucide="calendar-check" class="w-5 h-5 text-cyan-400"></i>
                        <span x-text="selectedDate"></span>
                    </h3>
                    <p class="text-xs text-slate-400">Daily expense summary & transactions</p>
                </div>
                <button @click="dateModalOpen = false" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <div class="mt-4 space-y-4">
                <!-- Total Spent Card -->
                <div class="p-4 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-between">
                    <span class="text-xs font-semibold text-slate-300">Total Spent on Date</span>
                    <span class="text-xl font-extrabold text-cyan-400" x-text="'{{ auth()->user()->currency_symbol }}' + Number(dateTotal).toFixed(2)"></span>
                </div>

                <!-- Expenses List -->
                <div>
                    <h4 class="text-xs font-bold uppercase text-slate-400 mb-2">Recorded Expenses</h4>
                    <div class="space-y-2 max-h-48 overflow-y-auto custom-scrollbar">
                        <template x-for="exp in dateExpenses" :key="exp.id">
                            <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-bold text-white" x-text="exp.title"></div>
                                    <div class="text-[10px] text-slate-400" x-text="exp.payment_method + (exp.notes ? ' • ' + exp.notes : '')"></div>
                                </div>
                                <div class="text-sm font-extrabold text-white" x-text="'{{ auth()->user()->currency_symbol }}' + Number(exp.amount).toFixed(2)"></div>
                            </div>
                        </template>
                        <div x-show="dateExpenses.length === 0" class="text-xs text-slate-500 py-4 text-center">No expenses recorded for this date.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek'
            },
            events: '{{ route("calendar.events") }}',
            eventClick: function(info) {
                const dateStr = info.event.extendedProps.date;
                if (!dateStr) return;
                fetch('/calendar/details/' + dateStr)
                    .then(res => res.json())
                    .then(data => {
                        const alpineData = Alpine.$data(document.querySelector('[x-data]'));
                        alpineData.selectedDate = data.date;
                        alpineData.dateTotal = data.total_spent;
                        alpineData.dateExpenses = data.expenses;
                        alpineData.categoryBreakdown = data.category_breakdown;
                        alpineData.dateModalOpen = true;
                    });
            }
        });
        calendar.render();
    });
</script>
@endpush
