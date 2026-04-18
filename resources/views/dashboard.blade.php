<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome banner --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 flex items-center">
                    <div class="mr-4">
                        @if (Auth::user()->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile"
                                class="h-16 w-16 rounded-full object-cover">
                        @else
                            <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-xl font-bold">
                                {{ substr(Auth::user()->first_name, 0, 1) }}{{ substr(Auth::user()->last_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold">Welcome, {{ Auth::user()->full_name }}!</h3>
                        <p class="text-sm text-gray-500 capitalize">Role: {{ Auth::user()->role }}</p>
                    </div>
                    <div class="text-right text-sm text-gray-500 hidden sm:block">
                        <div>{{ now()->format('l, F j, Y') }}</div>
                        <div class="text-xs">Financials as of {{ \Carbon\Carbon::parse($ratios['as_of'])->format('M j, Y') }}</div>
                    </div>
                </div>
            </div>

            {{-- Action Items / Alerts --}}
            @if (!empty($alerts))
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-bold text-lg text-gray-800">Action Items</h4>
                        <span class="text-xs text-gray-400 uppercase tracking-wide">What needs attention</span>
                    </div>

                    @php
                        $openAlerts = collect($alerts)->filter(fn($a) => $a['count'] > 0);
                    @endphp

                    @if ($openAlerts->isEmpty())
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"/>
                            </svg>
                            All clear — nothing waiting on you right now.
                        </div>
                    @else
                        <ul class="divide-y divide-gray-100">
                            @foreach ($openAlerts as $alert)
                                <li class="py-2 flex items-center gap-3">
                                    <span class="inline-flex items-center justify-center min-w-[2rem] h-7 px-2 rounded-full text-xs font-bold
                                        {{ $alert['severity'] === 'warn' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $alert['count'] }}
                                    </span>
                                    <span class="flex-1 text-sm text-gray-700">{{ $alert['label'] }}</span>
                                    <a href="{{ $alert['url'] }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-900">
                                        Review →
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            {{-- Financial Ratios --}}
            <div>
                <div class="flex items-baseline justify-between mb-3 px-1">
                    <h3 class="font-bold text-lg text-gray-800">Financial Ratios</h3>
                    <span class="text-xs text-gray-500">
                        Color coding — <span class="inline-block h-2 w-2 rounded-full bg-green-500 align-middle"></span> healthy ·
                        <span class="inline-block h-2 w-2 rounded-full bg-yellow-500 align-middle"></span> watch ·
                        <span class="inline-block h-2 w-2 rounded-full bg-red-500 align-middle"></span> attention
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    {{-- Liquidity --}}
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V7m0 9v1m0-10c1.11 0 2.08.402 2.599 1M12 16c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            <h4 class="font-semibold text-gray-800">Liquidity</h4>
                        </div>
                        <div class="space-y-2">
                            @foreach ($ratios['liquidity'] as $r)
                                <x-ratio-tile
                                    :label="$r['label']"
                                    :display="$r['display']"
                                    :status="$r['status']"
                                    :subtitle="$r['subtitle']"
                                    :formula="$r['formula']"
                                />
                            @endforeach
                        </div>
                    </div>

                    {{-- Profitability --}}
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            <h4 class="font-semibold text-gray-800">Profitability</h4>
                        </div>
                        <div class="space-y-2">
                            @foreach ($ratios['profitability'] as $r)
                                <x-ratio-tile
                                    :label="$r['label']"
                                    :display="$r['display']"
                                    :status="$r['status']"
                                    :subtitle="$r['subtitle']"
                                    :formula="$r['formula']"
                                />
                            @endforeach
                        </div>
                    </div>

                    {{-- Leverage --}}
                    <div class="bg-white p-5 rounded-lg shadow-sm border border-gray-100">
                        <div class="flex items-center gap-2 mb-3">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                            </svg>
                            <h4 class="font-semibold text-gray-800">Leverage</h4>
                        </div>
                        <div class="space-y-2">
                            @foreach ($ratios['leverage'] as $r)
                                <x-ratio-tile
                                    :label="$r['label']"
                                    :display="$r['display']"
                                    :status="$r['status']"
                                    :subtitle="$r['subtitle']"
                                    :formula="$r['formula']"
                                />
                            @endforeach
                        </div>
                    </div>
                </div>

                <p class="mt-3 text-xs text-gray-500 px-1">
                    Ratios computed from approved journal entries as of {{ \Carbon\Carbon::parse($ratios['as_of'])->format('F j, Y') }}.
                </p>
            </div>

            {{-- Role-scoped quick links --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if (Auth::user()->isAdmin())
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <h4 class="font-bold text-lg mb-3 text-gray-800">Admin Controls</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('admin.users.index') }}"
                                    class="flex items-center gap-2 text-indigo-600 hover:text-indigo-900 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    Manage Users</a></li>
                            <li><a href="{{ route('admin.requests.index') }}"
                                    class="flex items-center gap-2 text-indigo-600 hover:text-indigo-900 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                    Pending Access Requests</a></li>
                            <li><a href="{{ route('admin.reports.users') }}"
                                    class="flex items-center gap-2 text-indigo-600 hover:text-indigo-900 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    User Report</a></li>
                            <li><a href="{{ route('admin.reports.expired-passwords') }}"
                                    class="flex items-center gap-2 text-red-600 hover:text-red-900 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Expired Passwords Report</a></li>
                            <li><a href="{{ route('admin.activity-logs.index') }}"
                                    class="flex items-center gap-2 text-indigo-600 hover:text-indigo-900 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    Activity Log</a></li>
                        </ul>
                    </div>
                @endif

                @if (Auth::user()->isManager() || Auth::user()->isAdmin())
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <h4 class="font-bold text-lg mb-3 text-gray-800">Manager Actions</h4>
                        <ul class="space-y-2">
                            <li>
                                <a href="{{ route('journal-entries.index') }}" class="flex items-center gap-2 text-indigo-600 hover:text-indigo-900 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Review Journal Entries
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('adjusting-entries.index') }}" class="flex items-center gap-2 text-indigo-600 hover:text-indigo-900 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                    Review Adjusting Entries
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('reports.index') }}" class="flex items-center gap-2 text-indigo-600 hover:text-indigo-900 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    Financial Reports
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif

                @if (Auth::user()->isAccountant() || Auth::user()->isAdmin() || Auth::user()->isManager())
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <h4 class="font-bold text-lg mb-3 text-gray-800">Accounting</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('accounts.index') }}"
                                    class="flex items-center gap-2 text-indigo-600 hover:text-indigo-900 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    Chart of Accounts</a></li>
                            <li>
                                <a href="{{ route('ledger.index') }}" class="flex items-center gap-2 text-indigo-600 hover:text-indigo-900 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    General Ledger
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('journal-entries.create') }}" class="flex items-center gap-2 text-indigo-600 hover:text-indigo-900 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Create Journal Entry
                                </a>
                            </li>
                            @if (!Auth::user()->isManager() && !Auth::user()->isAdmin())
                                <li>
                                    <a href="{{ route('reports.index') }}" class="flex items-center gap-2 text-indigo-600 hover:text-indigo-900 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        Financial Reports
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
