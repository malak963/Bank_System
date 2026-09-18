<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Premium Banking Experience') }}</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-950">{{ __('Dashboard') }}</h2>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-emerald-100 p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-emerald-700"><path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Total Balance') }}</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-950">{{ number_format(124567.89, 2) }}</p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-slate-500">{{ __('Across all accounts') }}</p>
            </div>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-emerald-200 p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-emerald-800"><path d="M11 20h7a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"/><path d="M16 2v4"/><path d="M18 6h4"/><path d="M11 20H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h2"/><path d="M3 6H1"/><path d="M6 2v4"/><circle cx="12" cy="13" r="4"/><path d="M12 9v1"/><path d="M12 17v1"/><path d="M15 13h-1"/><path d="M9 13H8"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase text-emerald-700">{{ __('Active Loans') }}</p>
                        <p class="mt-1 text-2xl font-semibold text-emerald-950">3</p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-emerald-600">{{ __('2 personal, 1 mortgage') }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-slate-100 p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-slate-700"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Pending Appointments') }}</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-950">1</p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-slate-500">{{ __('Tomorrow at 10:00 AM') }}</p>
            </div>
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="rounded-lg bg-amber-100 p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-amber-700"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase text-slate-500">{{ __('Credit Score') }}</p>
                        <p class="mt-1 text-2xl font-semibold text-slate-950">785</p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-emerald-600">{{ __('Excellent') }}</p>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h3 class="font-semibold text-slate-950">{{ __('Quick Actions') }}</h3>
                    <p class="text-sm text-slate-500">{{ __('Frequently used banking services') }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 pt-4 md:grid-cols-4">
                <a href="{{ route('branches.index') }}" class="flex flex-col items-center rounded-lg border border-slate-200 bg-slate-50 p-4 text-center hover:border-emerald-300 hover:bg-emerald-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8 text-slate-700"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/><path d="M8 18h.01"/><path d="M12 18h.01"/><path d="M16 18h.01"/></svg>
                    <p class="mt-2 text-sm font-semibold text-slate-950">{{ __('Book Appointment') }}</p>
                    <p class="text-xs text-slate-500">{{ __('Schedule Visit') }}</p>
                </a>
                
                <a href="#" class="flex flex-col items-center rounded-lg border border-slate-200 bg-slate-50 p-4 text-center hover:border-emerald-300 hover:bg-emerald-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8 text-slate-700"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    <p class="mt-2 text-sm font-semibold text-slate-950">{{ __('Find Branch') }}</p>
                    <p class="text-xs text-slate-500">{{ __('Locator') }}</p>
                </a>
                
                <a href="#" class="flex flex-col items-center rounded-lg border border-slate-200 bg-slate-50 p-4 text-center hover:border-emerald-300 hover:bg-emerald-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8 text-slate-700"><rect width="16" height="20" x="4" y="2" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><line x1="16" x2="16" y1="14" y2="14"/><line x1="16" x2="16" y1="18" y2="18"/><line x1="8" x2="8" y1="14" y2="14"/><line x1="8" x2="8" y1="18" y2="18"/><line x1="12" x2="12" y1="14" y2="14"/><line x1="12" x2="12" y1="18" y2="18"/></svg>
                    <p class="mt-2 text-sm font-semibold text-slate-950">{{ __('Loan Calculator') }}</p>
                    <p class="text-xs text-slate-500">{{ __('Calculate') }}</p>
                </a>
                
                <a href="#" class="flex flex-col items-center rounded-lg border border-slate-200 bg-slate-50 p-4 text-center hover:border-emerald-300 hover:bg-emerald-50 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8 text-slate-700"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect width="7" height="5" x="7" y="7" rx="1"/><rect width="7" height="5" x="10" y="12" rx="1"/></svg>
                    <p class="mt-2 text-sm font-semibold text-slate-950">{{ __('Join Queue') }}</p>
                    <p class="text-xs text-slate-500">{{ __('Get Ticket') }}</p>
                </a>
            </div>
        </div>

        <!-- Branch Locator Preview -->
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h3 class="font-semibold text-slate-950">{{ __('Nearby Branches') }}</h3>
                    <p class="text-sm text-slate-500">{{ __('Branch locations and wait times') }}</p>
                </div>
            </div>
            <div class="divide-y divide-slate-100">
                <div class="flex items-center justify-between px-5 py-4 hover:bg-slate-50">
                    <div>
                        <h4 class="font-semibold text-slate-950">Main Branch - Downtown</h4>
                        <p class="text-sm text-slate-500">0.5 km away • Queue: 3 people • Wait: ~5 min</p>
                    </div>
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-200">Open</span>
                </div>
                
                <div class="flex items-center justify-between px-5 py-4 hover:bg-slate-50">
                    <div>
                        <h4 class="font-semibold text-slate-950">City Center Branch</h4>
                        <p class="text-sm text-slate-500">1.2 km away • Queue: 7 people • Wait: ~12 min</p>
                    </div>
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-emerald-50 text-emerald-700 ring-emerald-200">Open</span>
                </div>
                
                <div class="flex items-center justify-between px-5 py-4 hover:bg-slate-50">
                    <div>
                        <h4 class="font-semibold text-slate-950">Airport Branch</h4>
                        <p class="text-sm text-slate-500">3.8 km away • Queue: 1 person • Wait: ~2 min</p>
                    </div>
                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset bg-amber-50 text-amber-700 ring-amber-200">Limited</span>
                </div>
            </div>
        </div>

        <!-- Recent Transactions Preview -->
        <div class="rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h3 class="font-semibold text-slate-950">{{ __('Recent Transactions') }}</h3>
                    <p class="text-sm text-slate-500">{{ __('Your latest banking activities') }}</p>
                </div>
                <a href="#" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">{{ __('View All') }}</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Date') }}</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Description') }}</th>
                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-slate-500">{{ __('Account') }}</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-slate-500">{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 text-sm text-slate-700">2026-09-15</td>
                            <td class="px-5 py-4 text-sm text-slate-900">ATM Withdrawal</td>
                            <td class="px-5 py-4 text-sm text-slate-700">****4567</td>
                            <td class="px-5 py-4 text-right text-sm font-semibold text-red-600">-$500.00</td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 text-sm text-slate-700">2026-09-14</td>
                            <td class="px-5 py-4 text-sm text-slate-900">Direct Deposit</td>
                            <td class="px-5 py-4 text-sm text-slate-700">****4567</td>
                            <td class="px-5 py-4 text-right text-sm font-semibold text-emerald-600">+$3,250.00</td>
                        </tr>
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-4 text-sm text-slate-700">2026-09-13</td>
                            <td class="px-5 py-4 text-sm text-slate-900">Loan Payment</td>
                            <td class="px-5 py-4 text-sm text-slate-700">****4567</td>
                            <td class="px-5 py-4 text-right text-sm font-semibold text-red-600">-$450.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
