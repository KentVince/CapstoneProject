<x-filament::page>
<div>
    <style>
        /* Custom dark theme card colors for Dashboard sections (#003432) */
        .dark .cafarm-dark-card {
            background-color: #003432 !important;
            border-color: #005250 !important;
        }
        /* Table sub-headers & footers — slightly darker for contrast */
        .dark .cafarm-dark-table-header {
            background-color: #002624 !important;
            border-color: #004442 !important;
        }
        /* thead row */
        .dark .cafarm-dark-table-head {
            background-color: #001e1c !important;
        }
        /* Table row hover */
        .dark .cafarm-dark-row:hover {
            background-color: #004442 !important;
        }
        /* Pagination buttons & page indicator */
        .dark .cafarm-dark-pagination {
            background-color: #003432 !important;
            border-color: #005250 !important;
        }
    </style>
    <div>
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Pests Card -->
            <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-white/80 uppercase tracking-wide">Total Pests</h3>
                        <svg class="w-8 h-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-white">{{ $this->getTotalPests() }}</div>
                    <p class="text-xs text-white/70 mt-2">Reported pest cases</p>
                </div>
                <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white/10 rounded-full"></div>
            </div>

            <!-- Total Diseases Card -->
            <div class="relative overflow-hidden bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-white/80 uppercase tracking-wide">Total Diseases</h3>
                        <svg class="w-8 h-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-white">{{ $this->getTotalDiseases() }}</div>
                    <p class="text-xs text-white/70 mt-2">Reported disease cases</p>
                </div>
                <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white/10 rounded-full"></div>
            </div>

                 <!-- Total Soil Tested Card -->
            <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-white/80 uppercase tracking-wide">Soil Tests</h3>
                        <svg class="w-8 h-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-white">{{ $this->getTotalSoilTested() }}</div>
                    <p class="text-xs text-white/70 mt-2">Completed soil tests</p>
                </div>
                <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white/10 rounded-full"></div>
            </div>

            <!-- Total Farmers Card -->
            <div class="relative overflow-hidden bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-white/80 uppercase tracking-wide">Total Farmers</h3>
                        <svg class="w-8 h-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-white">{{ $this->getTotalFarmers() }}</div>
                    <p class="text-xs text-white/70 mt-2">Registered farmers</p>
                </div>
                <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white/10 rounded-full"></div>
            </div>

       
        </div>

        <!-- Pending Backlog Section -->
        @php
            $pendingPest        = $this->getPendingPestCases();
            $pendingSoil        = $this->getPendingSoilAnalyses();
            $totalPending       = $pendingPest + $pendingSoil;
            $oldestPest         = $this->getOldestPendingPestDate();
            $oldestSoil         = $this->getOldestPendingSoilDate();
            $pendingPestRecords = $this->getPendingPestRecords();
            $pendingSoilRecords = $this->getPendingSoilRecords();
        @endphp

        <div class="mb-6">
            <!-- Section Header -->
            <div class="flex items-center gap-2 mb-3">
                <h2 class="text-base font-bold text-gray-800 dark:text-white">Validation Queue</h2>
                @if ($totalPending > 0)
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 animate-pulse">
                    {{ $totalPending }} pending
                </span>
                @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">
                    All clear
                </span>
                @endif
            </div>

            <!-- 2-Column Layout: Left = Pest & Disease, Right = Soil Analysis -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- ===== LEFT COLUMN: Pest & Disease ===== --}}
                <div class="flex flex-col rounded-xl border {{ $pendingPest > 0 ? 'border-amber-300 dark:border-amber-700' : 'border-gray-200 dark:border-gray-700' }} bg-white dark:bg-gray-800 shadow-sm overflow-hidden cafarm-dark-card">

                    <!-- Card Header -->
                    <div class="flex items-center justify-between px-6 py-5 {{ $pendingPest > 0 ? 'bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-700' : 'bg-gray-50 dark:bg-gray-900/40 border-b border-gray-200 dark:border-gray-700' }} cafarm-dark-table-header">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $pendingPest > 0 ? 'bg-amber-200 dark:bg-amber-800/60' : 'bg-gray-200 dark:bg-gray-700' }}">
                                <svg class="w-5 h-5 {{ $pendingPest > 0 ? 'text-amber-700 dark:text-amber-300' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold {{ $pendingPest > 0 ? 'text-amber-800 dark:text-amber-200' : 'text-gray-600 dark:text-gray-400' }}">Pest &amp; Disease</p>
                                <p class="text-xs {{ $pendingPest > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400 dark:text-gray-500' }}">
                                    @if ($pendingPest > 0 && $oldestPest)
                                        Oldest: {{ \Carbon\Carbon::parse($oldestPest)->diffForHumans() }}
                                    @else
                                        No pending detections
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-3xl font-extrabold {{ $pendingPest > 0 ? 'text-amber-700 dark:text-amber-300' : 'text-gray-300 dark:text-gray-600' }}">{{ $pendingPest }}</span>
                            @if ($pendingPest > 0)
                            <a href="/admin/pest-and-diseases" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white transition whitespace-nowrap">
                                View all
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Pest Table -->
                    <div class="overflow-x-auto flex-1">
                        @if ($pendingPestRecords->count() > 0)
                        <table class="w-full text-sm">
                            <thead class="cafarm-dark-table-head">
                                <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide bg-gray-50 dark:bg-gray-900">
                                    <th class="px-4 py-2.5">Pest / Disease</th>
                                    <th class="px-4 py-2.5">Farmer</th>
                                    <th class="px-4 py-2.5">Severity</th>
                                    <th class="px-4 py-2.5">Date</th>
                                    <th class="px-4 py-2.5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($pendingPestRecords as $record)
                                <tr class="hover:bg-amber-50/50 dark:hover:bg-amber-900/10 transition cafarm-dark-row">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-800 dark:text-gray-200 text-xs leading-tight">{{ $record->pest ?? '—' }}</div>
                                        <div class="text-xs text-gray-400 font-mono mt-0.5">#{{ $record->case_id }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">
                                        {{ $record->farmer ? trim($record->farmer->first_name . ' ' . $record->farmer->last_name) : '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @php $sev = strtolower($record->severity ?? ''); @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                            {{ $sev === 'high'   ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' :
                                               ($sev === 'medium' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' :
                                                                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300') }}">
                                            {{ ucfirst($record->severity ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                        {{ $record->date_detected ? \Carbon\Carbon::parse($record->date_detected)->format('M d, Y') : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="/admin/pest-and-diseases/{{ $record->case_id }}/edit"
                                           class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-100 hover:bg-amber-200 text-amber-800 dark:bg-amber-900/40 dark:hover:bg-amber-900/60 dark:text-amber-300 transition">
                                            Validate
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="flex flex-col items-center justify-center py-10 text-gray-300 dark:text-gray-600">
                            <svg class="w-9 h-9 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm text-gray-400">No pending detections</p>
                        </div>
                        @endif
                    </div>

                    @if ($pendingPest > 5)
                    <div class="px-4 py-2.5 border-t border-amber-100 dark:border-amber-900/40 bg-amber-50/50 dark:bg-amber-900/10">
                        <a href="/admin/pest-and-diseases" class="text-xs font-semibold text-amber-600 dark:text-amber-400 hover:underline">
                            + {{ $pendingPest - 5 }} more — View all →
                        </a>
                    </div>
                    @endif
                </div>

                {{-- ===== RIGHT COLUMN: Soil Analysis ===== --}}
                <div class="flex flex-col rounded-xl border {{ $pendingSoil > 0 ? 'border-blue-300 dark:border-blue-700' : 'border-gray-200 dark:border-gray-700' }} bg-white dark:bg-gray-800 shadow-sm overflow-hidden cafarm-dark-card">

                    <!-- Card Header -->
                    <div class="flex items-center justify-between px-6 py-5 {{ $pendingSoil > 0 ? 'bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-700' : 'bg-gray-50 dark:bg-gray-900/40 border-b border-gray-200 dark:border-gray-700' }} cafarm-dark-table-header">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $pendingSoil > 0 ? 'bg-blue-200 dark:bg-blue-800/60' : 'bg-gray-200 dark:bg-gray-700' }}">
                                <svg class="w-5 h-5 {{ $pendingSoil > 0 ? 'text-blue-700 dark:text-blue-300' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold {{ $pendingSoil > 0 ? 'text-blue-800 dark:text-blue-200' : 'text-gray-600 dark:text-gray-400' }}">Soil Analysis</p>
                                <p class="text-xs {{ $pendingSoil > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500' }}">
                                    @if ($pendingSoil > 0 && $oldestSoil)
                                        Oldest: {{ \Carbon\Carbon::parse($oldestSoil)->diffForHumans() }}
                                    @else
                                        No pending analyses
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-3xl font-extrabold {{ $pendingSoil > 0 ? 'text-blue-700 dark:text-blue-300' : 'text-gray-300 dark:text-gray-600' }}">{{ $pendingSoil }}</span>
                            @if ($pendingSoil > 0)
                            <a href="/admin/soil-analyses" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-blue-500 hover:bg-blue-600 text-white transition whitespace-nowrap">
                                View all
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            @endif
                        </div>
                    </div>

                    <!-- Soil Table -->
                    <div class="overflow-x-auto flex-1">
                        @if ($pendingSoilRecords->count() > 0)
                        <table class="w-full text-sm">
                            <thead class="cafarm-dark-table-head">
                                <tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide bg-gray-50 dark:bg-gray-900">
                                    <th class="px-4 py-2.5">Farm</th>
                                    <th class="px-4 py-2.5">Farmer</th>
                                    <th class="px-4 py-2.5">pH</th>
                                    <th class="px-4 py-2.5">Date</th>
                                    <th class="px-4 py-2.5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach ($pendingSoilRecords as $record)
                                <tr class="hover:bg-blue-50/50 dark:hover:bg-blue-900/10 transition cafarm-dark-row">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-800 dark:text-gray-200 text-xs leading-tight">{{ $record->farm_name ?? '—' }}</div>
                                        <div class="text-xs text-gray-400 font-mono mt-0.5">
                                            N:{{ $record->nitrogen ? number_format($record->nitrogen,1) : '—' }}
                                            P:{{ $record->phosphorus ? number_format($record->phosphorus,1) : '—' }}
                                            K:{{ $record->potassium ? number_format($record->potassium,1) : '—' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">
                                        {{ $record->farmer ? trim($record->farmer->first_name . ' ' . $record->farmer->last_name) : '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($record->ph_level)
                                        @php $ph = (float) $record->ph_level; @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold
                                            {{ $ph < 5.5 ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' :
                                               ($ph < 6.5 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' :
                                               ($ph < 7.0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' :
                                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300')) }}">
                                            {{ number_format($ph, 2) }}
                                        </span>
                                        @else
                                            <span class="text-gray-400 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                        {{ $record->date_collected ? \Carbon\Carbon::parse($record->date_collected)->format('M d, Y') : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="/admin/soil-analyses/{{ $record->id }}/edit"
                                           class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-100 hover:bg-blue-200 text-blue-800 dark:bg-blue-900/40 dark:hover:bg-blue-900/60 dark:text-blue-300 transition">
                                            Validate
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="flex flex-col items-center justify-center py-10 text-gray-300 dark:text-gray-600">
                            <svg class="w-9 h-9 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm text-gray-400">No pending analyses</p>
                        </div>
                        @endif
                    </div>

                    @if ($pendingSoil > 5)
                    <div class="px-4 py-2.5 border-t border-blue-100 dark:border-blue-900/40 bg-blue-50/50 dark:bg-blue-900/10">
                        <a href="/admin/soil-analyses" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            + {{ $pendingSoil - 5 }} more — View all →
                        </a>
                    </div>
                    @endif
                </div>

            </div>{{-- end 2-col grid --}}
        </div>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Pest Incidence Rate Trend -->
            <div class="p-6 shadow-lg rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 cafarm-dark-card">
                <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Pest Incidence Rate Trend</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 -mt-2 mb-3">Monthly avg. severity & incidence rating</p>
                <div style="max-width: 100%; height: 300px;">
                    <canvas id="incidenceRateChart"></canvas>
                </div>
            </div>

            <!-- Soil Nutrient Levels by Municipality -->
            <div class="p-6 shadow-lg rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 cafarm-dark-card">
                <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Soil Nutrient Levels by Barangay</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 -mt-2 mb-3">Average N, P, K & Organic Matter per barangay</p>
                <div style="max-width: 100%; height: 300px;">
                    <canvas id="soilNutrientChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Cases by Severity & Top 10 Pests Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Pest & Disease Assessment per Barangay -->
            <div class="p-6 shadow-lg rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 cafarm-dark-card">
                <h2 class="text-lg font-semibold mb-1 text-gray-900 dark:text-white">Pest & Disease Assessment per Barangay</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Total approved detections per barangay in Maragusan</p>
                <div style="max-width: 100%; height: 300px;">
                    <canvas id="pestBarangayChart"></canvas>
                </div>
            </div>

            <!-- Pest & Disease Distribution per Barangay -->
            <div class="p-6 shadow-lg rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 cafarm-dark-card">
                <h2 class="text-lg font-semibold mb-1 text-gray-900 dark:text-white">Pest & Disease Distribution per Barangay</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Detected pests and diseases count per barangay</p>
                @php
                    $pivotPests = $pestDistributionByBarangay['pests'] ?? [];
                    $pivotData  = $pestDistributionByBarangay['pivot'] ?? [];
                @endphp
                <div class="overflow-auto" style="max-height: 310px;">
                    <table class="w-full text-sm border-collapse">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-gray-50 dark:bg-gray-700/60">
                                <th class="text-left px-3 py-2 font-bold text-gray-700 dark:text-gray-300 text-xs uppercase tracking-wide border-b-2 border-gray-200 dark:border-gray-600">Barangay</th>
                                @foreach($pivotPests as $pest)
                                    <th class="text-center px-2 py-2 font-bold text-gray-700 dark:text-gray-300 text-xs uppercase tracking-wide border-b-2 border-gray-200 dark:border-gray-600 whitespace-nowrap">{{ $pest }}</th>
                                @endforeach
                                <th class="text-center px-3 py-2 font-bold text-gray-900 dark:text-white text-xs uppercase tracking-wide border-b-2 border-gray-200 dark:border-gray-600">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pivotData as $barangay => $counts)
                                <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                    <td class="px-3 py-2 font-medium text-gray-900 dark:text-white text-xs">{{ $barangay }}</td>
                                    @php $rowTotal = 0; @endphp
                                    @foreach($pivotPests as $pest)
                                        @php
                                            $val = $counts[$pest] ?? 0;
                                            $rowTotal += $val;
                                        @endphp
                                        <td class="text-center px-2 py-2 text-xs {{ $val > 0 ? 'font-semibold text-gray-800 dark:text-gray-200' : 'text-gray-300 dark:text-gray-600' }}">{{ $val > 0 ? $val : '' }}</td>
                                    @endforeach
                                    <td class="text-center px-3 py-2 text-xs font-bold text-green-600 dark:text-green-400">{{ $rowTotal }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($pivotPests) + 2 }}" class="px-3 py-8 text-center text-gray-400 text-sm">No approved detections yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($pivotData) > 0)
                        <tfoot class="sticky bottom-0">
                            <tr class="bg-gray-100 dark:bg-gray-700/80 border-t-2 border-gray-300 dark:border-gray-500">
                                <td class="px-3 py-2 font-bold text-gray-900 dark:text-white text-xs uppercase">Total</td>
                                @php $grandTotal = 0; @endphp
                                @foreach($pivotPests as $pest)
                                    @php
                                        $colTotal = collect($pivotData)->sum(fn($c) => $c[$pest] ?? 0);
                                        $grandTotal += $colTotal;
                                    @endphp
                                    <td class="text-center px-2 py-2 text-xs font-bold text-gray-900 dark:text-white">{{ $colTotal }}</td>
                                @endforeach
                                <td class="text-center px-3 py-2 text-xs font-extrabold text-green-700 dark:text-green-400">{{ $grandTotal }}</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Data Table Section -->
        <div class="mt-6">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden cafarm-dark-card">
                <!-- Table Header -->
                <div class="px-6 py-5 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 cafarm-dark-table-header">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">📋 Data Records</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                View and filter through all records by type
                            </p>
                        </div>

                        <!-- Search and Filters -->
                        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                            <div class="w-full sm:w-auto">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search:</label>
                                <input type="text"
                                       id="tableSearch"
                                       placeholder="Search records..."
                                       class="w-full px-4 py-2 text-sm border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       oninput="filterTableData()">
                            </div>

                            <div class="w-full sm:w-auto">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Type:</label>
                                <select id="dataTypeFilter"
                                        onchange="filterTableData()"
                                        class="w-full sm:w-auto px-4 py-2 text-sm font-medium border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                    <option value="all">📊 All Data</option>
                                    <option value="pest_disease">🐛 Pest & Disease</option>
                                    <option value="farms">🌾 Farms</option>
                                    <option value="farmers">👨‍🌾 Farmers</option>
                                    <option value="soil">🌱 Soil Analysis</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead id="dashboardTableHead" class="bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 uppercase text-xs cafarm-dark-table-head">
                            <!-- Dynamic headers -->
                        </thead>
                        <tbody id="dashboardTableBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Info -->
                <div class="px-6 py-4 border-t-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 cafarm-dark-pagination cafarm-dark-table-header">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing <span id="recordsCount" class="font-bold text-blue-600 dark:text-blue-400">0</span> records
                            </p>
                            <span id="currentFilterLabel" class="text-xs px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 font-semibold">
                                All Data
                            </span>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="changeTablePage(-1)"
                                    id="prevPageBtn"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition cafarm-dark-pagination">
                                ← Previous
                            </button>
                            <span class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-lg cafarm-dark-pagination">
                                Page <span id="currentPage" class="font-bold">1</span> of <span id="totalPages" class="font-bold">1</span>
                            </span>
                            <button onclick="changeTablePage(1)"
                                    id="nextPageBtn"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition cafarm-dark-pagination">
                                Next →
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get data from backend
        var soilNutrientData = @json($soilNutrientData) || [];
        var incidenceRateTrend = @json($incidenceRateTrend) || [];
        var pestByBarangay = @json($pestByBarangay) || {};

        // Check if dark mode
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#ffffff' : '#1f2937';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';

        // Chart colors
        const colors = {
            primary: 'rgba(59, 130, 246, 0.8)',
            success: 'rgba(34, 197, 94, 0.8)',
            warning: 'rgba(251, 146, 60, 0.8)',
            danger: 'rgba(239, 68, 68, 0.8)',
            info: 'rgba(99, 102, 241, 0.8)',
            purple: 'rgba(168, 85, 247, 0.8)',
        };

        // 1. Pest Incidence Rate Trend Chart
        var trendLabels = incidenceRateTrend.map(item => {
            const parts = item.month.split('-');
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return monthNames[parseInt(parts[1]) - 1] + ' ' + parts[0];
        }) || ['No Data'];
        var severityData = incidenceRateTrend.map(item => parseFloat(item.avg_severity || 0).toFixed(2));
        var incidenceData = incidenceRateTrend.map(item => parseFloat(item.avg_incidence || 0).toFixed(2));

        new Chart(document.getElementById('incidenceRateChart'), {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [
                    {
                        label: 'Avg. Severity (%)',
                        data: severityData,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        borderWidth: 2,
                    },
                    {
                        label: 'Avg. Incidence Rating',
                        data: incidenceData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        borderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: textColor, padding: 15, usePointStyle: true, pointStyle: 'circle' }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: textColor, maxRotation: 45, minRotation: 0 },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: textColor },
                        grid: { color: gridColor },
                    }
                }
            }
        });

        // 2. Soil Nutrient Levels by Municipality Chart
        var nutrientLabels = soilNutrientData.map(item => item.barangay_name) || ['No Data'];
        var nitrogenData = soilNutrientData.map(item => parseFloat(item.avg_nitrogen || 0).toFixed(2));
        var phosphorusData = soilNutrientData.map(item => parseFloat(item.avg_phosphorus || 0).toFixed(2));
        var potassiumData = soilNutrientData.map(item => parseFloat(item.avg_potassium || 0).toFixed(2));
        var organicMatterData = soilNutrientData.map(item => parseFloat(item.avg_organic_matter || 0).toFixed(2));

        new Chart(document.getElementById('soilNutrientChart'), {
            type: 'bar',
            data: {
                labels: nutrientLabels,
                datasets: [
                    {
                        label: 'Nitrogen (N)',
                        data: nitrogenData,
                        backgroundColor: '#3b82f6',
                        borderRadius: 3,
                    },
                    {
                        label: 'Phosphorus (P)',
                        data: phosphorusData,
                        backgroundColor: '#f59e0b',
                        borderRadius: 3,
                    },
                    {
                        label: 'Potassium (K)',
                        data: potassiumData,
                        backgroundColor: '#22c55e',
                        borderRadius: 3,
                    },
                    {
                        label: 'Organic Matter',
                        data: organicMatterData,
                        backgroundColor: '#8b5cf6',
                        borderRadius: 3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: textColor, padding: 12, usePointStyle: true, pointStyle: 'rectRounded' }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: textColor, maxRotation: 45, minRotation: 0 },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: textColor },
                        grid: { color: gridColor },
                        title: {
                            display: true,
                            text: 'Average Level',
                            color: textColor
                        }
                    }
                }
            }
        });

        // 3. Pest & Disease Assessment per Barangay (Horizontal Bar)
        var barangayLabels = Object.keys(pestByBarangay);
        var barangayCounts = Object.values(pestByBarangay);

        new Chart(document.getElementById('pestBarangayChart'), {
            type: 'bar',
            data: {
                labels: barangayLabels.length ? barangayLabels : ['No Data'],
                datasets: [{
                    label: 'Detections',
                    data: barangayCounts.length ? barangayCounts : [0],
                    backgroundColor: 'rgba(22, 163, 74, 0.75)',
                    borderColor: 'rgba(22, 163, 74, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.parsed.x + ' approved detections';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { color: textColor, stepSize: 1 },
                        grid: { color: gridColor },
                        title: { display: true, text: 'No. of Detections', color: textColor }
                    },
                    y: {
                        ticks: { color: textColor },
                        grid: { display: false }
                    }
                }
            }
        });

        // 4. (Removed — replaced by Pest Distribution table)
    });

    // ==================== TABLE DATA FUNCTIONALITY ====================

    // Helper function to format dates in long format
    function formatLongDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    // Fetch all data needed for the table
    let allTableData = {
        pestDisease: [],
        farms: [],
        farmers: [],
        soil: []
    };

    let currentTablePage = 1;
    const recordsPerPage = 10;
    let filteredTableData = [];

    // Fetch data via AJAX
    async function fetchTableData() {
        try {
            const response = await fetch('/api/dashboard-table-data');
            if (response.ok) {
                allTableData = await response.json();
                filterTableData();
            }
        } catch (error) {
            console.error('Error fetching table data:', error);
            // Use placeholder data if API fails
            initializePlaceholderData();
        }
    }

    function initializePlaceholderData() {
        // This will be populated with actual data from your backend
        allTableData = {
            pestDisease: @json($this->getRecentPestDiseases() ?? []),
            farms: @json($this->getRecentFarms() ?? []),
            farmers: @json($this->getRecentFarmers() ?? []),
            soil: @json($this->getRecentSoilAnalysis() ?? [])
        };

        // Debug logging
        console.log('Table data loaded:', {
            pestDisease: allTableData.pestDisease.length,
            farms: allTableData.farms.length,
            farmers: allTableData.farmers.length,
            soil: allTableData.soil.length
        });

        filterTableData();
    }

    function filterTableData() {
        const searchTerm = document.getElementById('tableSearch').value.toLowerCase();
        const dataType = document.getElementById('dataTypeFilter').value;

        // Update filter label
        const filterLabels = {
            'all': '📊 All Data',
            'pest_disease': '🐛 Pest & Disease',
            'farms': '🌾 Farms',
            'farmers': '👨‍🌾 Farmers',
            'soil': '🌱 Soil Analysis'
        };
        const filterLabel = document.getElementById('currentFilterLabel');
        if (filterLabel) {
            filterLabel.textContent = filterLabels[dataType] || 'All Data';
        }

        let data = [];

        // Combine data based on filter
        if (dataType === 'all' || dataType === 'pest_disease') {
            data = data.concat(allTableData.pestDisease.map(item => ({
                ...item,
                dataType: 'pest_disease'
            })));
        }
        if (dataType === 'all' || dataType === 'farms') {
            data = data.concat(allTableData.farms.map(item => ({
                ...item,
                dataType: 'farms'
            })));
        }
        if (dataType === 'all' || dataType === 'farmers') {
            data = data.concat(allTableData.farmers.map(item => ({
                ...item,
                dataType: 'farmers'
            })));
        }
        if (dataType === 'all' || dataType === 'soil') {
            data = data.concat(allTableData.soil.map(item => ({
                ...item,
                dataType: 'soil'
            })));
        }

        // Apply search filter
        if (searchTerm) {
            data = data.filter(item => {
                const searchableFields = [
                    item.last_name,
                    item.first_name,
                    item.middle_name,
                    item.full_name,
                    item.farmer_name,
                    item.farm_name,
                    item.app_no,
                    item.rsbsa_no,
                    item.barangay_name,
                    item.pest,
                    item.case_id,
                ];
                return searchableFields.some(field =>
                    field && field.toString().toLowerCase().includes(searchTerm)
                );
            });
        }

        filteredTableData = data;
        currentTablePage = 1;
        renderTable();
    }

    function renderTable() {
        const tableHead = document.getElementById('dashboardTableHead');
        const tableBody = document.getElementById('dashboardTableBody');
        const dataType = document.getElementById('dataTypeFilter').value;

        // Calculate pagination
        const startIndex = (currentTablePage - 1) * recordsPerPage;
        const endIndex = startIndex + recordsPerPage;
        const paginatedData = filteredTableData.slice(startIndex, endIndex);
        const totalPages = Math.ceil(filteredTableData.length / recordsPerPage);

        // Update pagination info
        document.getElementById('recordsCount').textContent = filteredTableData.length;
        document.getElementById('currentPage').textContent = currentTablePage;
        document.getElementById('totalPages').textContent = totalPages || 1;
        document.getElementById('prevPageBtn').disabled = currentTablePage === 1;
        document.getElementById('nextPageBtn').disabled = currentTablePage >= totalPages;

        let headers = [];
        let rows = [];

        // Determine table structure based on data type
        if (dataType === 'pest_disease') {
            headers = ['Case ID', 'Pest/Disease', 'Severity', 'Confidence', 'Date Detected', 'Area (ha)'];
            rows = paginatedData.map(item => [
                item.case_id || 'N/A',
                item.pest || 'N/A',
                `<span class="px-2 py-1 text-xs font-semibold rounded-full ${
                    item.severity === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                    item.severity === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' :
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                }">${item.severity || 'N/A'}</span>`,
                item.confidence ? `${(item.confidence * 100).toFixed(1)}%` : 'N/A',
                formatLongDate(item.date_detected),
                item.area || 'N/A'
            ]);
        } else if (dataType === 'farms') {
            headers = ['Farm Name', 'Farmer', 'Barangay', 'Area (ha)', 'Variety'];
            rows = paginatedData.map(item => [
                item.farm_name || 'N/A',
                item.farmer_name || 'N/A',
                item.barangay_name || item.barangay || 'N/A',
                item.crop_area || 'N/A',
                item.crop_variety || 'N/A',
                // formatLongDate(item.created_at)
            ]);
        } else if (dataType === 'farmers') {
            headers = ['App No', 'Full Rsbsa','Full Name', 'Barangay', 'Sex', 'Phone'];
            rows = paginatedData.map(item => [
                item.app_no || 'N/A',
                item.rsbsa_no || 'N/A',
                item.full_name || 'N/A',
                item.barangay_name || item.barangay || 'N/A',
                `<span class="px-2 py-1 text-xs font-semibold rounded-full ${item.gender === 'Male' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-300'}">${item.gender || 'N/A'}</span>`,
               
                item.contact_num || 'N/A',
               
                // item.date_of_application ? formatLongDate(item.date_of_application) : formatLongDate(item.created_at)
            ]);
        } else if (dataType === 'soil') {
            headers = ['Farm Name', 'Date Collected', 'pH Level', 'Nitrogen (%)', 'Phosphorus (ppm)', 'Potassium (ppm)'];
            rows = paginatedData.map(item => [
                item.farm_name || 'N/A',
                formatLongDate(item.date_collected),
                `<span class="px-2 py-1 text-xs font-semibold rounded-full ${
                    item.ph_level < 5.5 ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                    item.ph_level >= 6.0 && item.ph_level < 7.0 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'
                }">${item.ph_level ? item.ph_level.toFixed(2) : 'N/A'}</span>`,
                item.nitrogen ? item.nitrogen.toFixed(2) : 'N/A',
                item.phosphorus ? item.phosphorus.toFixed(2) : 'N/A',
                item.potassium ? item.potassium.toFixed(2) : 'N/A'
            ]);
        } else {
            // Mixed data view
            headers = ['Name/ID', 'Details', 'Date', 'Status'];
            rows = paginatedData.map(item => {
                if (item.dataType === 'pest_disease') {
                    return [
                        item.pest || 'N/A',
                        `Severity: ${item.severity || 'N/A'}`,
                        formatLongDate(item.date_detected),
                        `<span class="px-2 py-1 text-xs rounded-full ${item.validation_status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">${item.validation_status || 'N/A'}</span>`
                    ];
                } else if (item.dataType === 'farms') {
                    return [
                        item.farm_name || item.name || 'N/A',
                        item.barangay_name || item.barangay || 'N/A',
                        formatLongDate(item.created_at),
                        `<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">${item.lot_hectare || 'N/A'} ha</span>`
                    ];
                } else if (item.dataType === 'farmers') {
                    return [
                        item.full_name || 'N/A',
                        item.barangay_name || item.barangay || 'N/A',
                        item.date_of_application ? formatLongDate(item.date_of_application) : formatLongDate(item.created_at),
                        `<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">${item.crop || 'N/A'}</span>`
                    ];
                } else {
                    return [
                        item.farm_name || 'N/A',
                        `pH: ${item.ph_level ? item.ph_level.toFixed(2) : 'N/A'}`,
                        formatLongDate(item.date_collected),
                        `<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Analysis</span>`
                    ];
                }
            });
        }

        // Render table headers
        tableHead.innerHTML = `
            <tr>
                ${headers.map(h => `<th class="px-4 py-3 text-left font-semibold">${h}</th>`).join('')}
            </tr>
        `;

        // Render table rows
        if (rows.length > 0) {
            tableBody.innerHTML = rows.map(row => `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition cafarm-dark-row">
                    ${row.map(cell => `<td class="px-4 py-3 text-gray-700 dark:text-gray-300">${cell}</td>`).join('')}
                </tr>
            `).join('');
        } else {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${headers.length}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        No records found
                    </td>
                </tr>
            `;
        }
    }

    function changeTablePage(direction) {
        const totalPages = Math.ceil(filteredTableData.length / recordsPerPage);
        const newPage = currentTablePage + direction;

        if (newPage >= 1 && newPage <= totalPages) {
            currentTablePage = newPage;
            renderTable();
        }
    }

    // Initialize table data on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializePlaceholderData();
    });
</script>
</div>

</x-filament::page>
