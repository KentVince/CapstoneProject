{{-- Soil Analysis View Modal --}}

@php
    // ── Validation status badge colors ──────────────────────────────────────
    $statusColors = [
        'pending'     => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'approved'    => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'disapproved' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    ];
    $statusColor = $statusColors[$record->validation_status] ?? $statusColors['pending'];

    // ── Soil rating functions (mirrors SoilRecommendationService thresholds) ─
    // Source: BSWM / FAO Soil Interpretation Guidelines (Landon 1991)
    $ratePh = fn(?float $v): string => match(true) {
        $v === null => 'N/A',
        $v < 4.5    => 'Very Low',
        $v <= 5.5   => 'Low',
        $v <= 6.5   => 'Medium',
        $v <= 8.5   => 'High',
        default     => 'Very High',
    };
    $rateOm = fn(?float $v): string => match(true) {
        $v === null => 'N/A',
        $v <= 1.00  => 'Very Low',
        $v <= 1.70  => 'Low',
        $v <= 3.00  => 'Moderate',
        $v <= 5.15  => 'High',
        default     => 'Very High',
    };
    $rateN = fn(?float $v): string => match(true) {
        $v === null => 'N/A',
        $v < 0.05   => 'Very Low',
        $v <= 0.15  => 'Low',
        $v <= 0.20  => 'Medium',
        $v <= 0.30  => 'High',
        default     => 'Very High',
    };
    $rateP = fn(?float $v): string => match(true) {
        $v === null => 'N/A',
        $v < 3      => 'Very Low',
        $v <= 10    => 'Low',
        $v <= 20    => 'Medium',
        $v <= 30    => 'High',
        default     => 'Very High',
    };
    $rateK = fn(?float $v): string => match(true) {
        $v === null => 'N/A',
        $v < 78     => 'Very Low',
        $v <= 117   => 'Low',
        $v <= 235   => 'Medium',
        $v <= 391   => 'High',
        default     => 'Very High',
    };

    // ── Status mapping ──────────────────────────────────────────────────────
    // pH: optimal for coffee is Medium (5.5–6.5); extremes on both sides are bad
    $phToStatus = fn(string $r): string => match($r) {
        'Medium'                   => 'normal',
        'Low', 'High'              => 'critical',
        'Very Low', 'Very High'    => 'warning',
        default                    => 'none',
    };
    // NPK & Organic Matter: Very Low = warning, Low/Very High = critical, Medium/High/Moderate = normal
    $nutrientToStatus = fn(string $r): string => match($r) {
        'Medium', 'High', 'Moderate' => 'normal',
        'Low', 'Very High'           => 'critical',
        'Very Low'                   => 'warning',
        default                      => 'none',
    };

    // ── Tailwind CSS classes per indicator status ────────────────────────────
    $badgeClass = fn(string $s): string => match($s) {
        'normal'   => 'bg-green-100 text-green-800 dark:bg-green-900/60 dark:text-green-300',
        'critical' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/60 dark:text-yellow-300',
        'warning'  => 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300',
        default    => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
    };
    $dotClass = fn(string $s): string => match($s) {
        'normal'   => 'bg-green-500',
        'critical' => 'bg-yellow-400',
        'warning'  => 'bg-red-500',
        default    => 'bg-gray-400',
    };
    $borderClass = fn(string $s): string => match($s) {
        'normal'   => 'border-l-4 border-green-400 dark:border-green-500',
        'critical' => 'border-l-4 border-yellow-400 dark:border-yellow-500',
        'warning'  => 'border-l-4 border-red-400 dark:border-red-500',
        default    => '',
    };
    $statusLbl = fn(string $s): string => match($s) {
        'normal'   => 'Normal',
        'critical' => 'Critical',
        'warning'  => 'Warning',
        default    => 'N/A',
    };

    // ── Compute raw values ───────────────────────────────────────────────────
    $phVal = is_numeric($record->ph_level)       ? (float) $record->ph_level       : null;
    $omVal = is_numeric($record->organic_matter) ? (float) $record->organic_matter : null;
    $nVal  = is_numeric($record->nitrogen)       ? (float) $record->nitrogen       : null;
    $pVal  = is_numeric($record->phosphorus)     ? (float) $record->phosphorus     : null;
    $kVal  = is_numeric($record->potassium)      ? (float) $record->potassium      : null;

    // ── Compute ratings ──────────────────────────────────────────────────────
    $phRating = $ratePh($phVal);
    $omRating = $rateOm($omVal);
    $nRating  = $rateN($nVal);
    $pRating  = $rateP($pVal);
    $kRating  = $rateK($kVal);

    // ── Compute indicator statuses ───────────────────────────────────────────
    $phStat = $phVal !== null ? $phToStatus($phRating)       : 'none';
    $omStat = $omVal !== null ? $nutrientToStatus($omRating) : 'none';
    $nStat  = $nVal  !== null ? $nutrientToStatus($nRating)  : 'none';
    $pStat  = $pVal  !== null ? $nutrientToStatus($pRating)  : 'none';
    $kStat  = $kVal  !== null ? $nutrientToStatus($kRating)  : 'none';
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Status Badge --}}
    <div class="md:col-span-2">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Status</h3>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                @if($record->validation_status === 'approved')
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                @elseif($record->validation_status === 'disapproved')
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                @endif
                {{ ucfirst($record->validation_status) }}
            </span>
        </div>
    </div>

    {{-- Farm Info — 4-column row --}}
    <div class="md:col-span-2 grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Farm Name</h4>
            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $record->farm_name ?? 'N/A' }}</p>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Date Collected</h4>
            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $record->date_collected?->format('M d, Y') ?? 'N/A' }}</p>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Soil Type</h4>
            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $record->soil_type ?? 'N/A' }}</p>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Analysis Type</h4>
            <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                {{ $record->analysis_type === 'with_lab' ? 'With Laboratory' : 'Without Laboratory' }}
            </p>
        </div>
    </div>

    {{-- ── Soil Health Indicators header + legend ─────────────────────────── --}}
    <div class="md:col-span-2">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
                Soil Health Indicators
                @if($record->analysis_type !== 'with_lab')
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                        <svg class="w-2.5 h-2.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        pH Only
                    </span>
                @endif
            </h4>

            {{-- Status legend --}}
            @if($record->analysis_type === 'with_lab')
                {{-- With Lab: full Normal / Critical / Warning legend --}}
                <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                        Normal
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>
                        Critical
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                        Warning
                    </span>
                </div>
            @else
                {{-- Without Lab: pH-only legend --}}
                <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                        Optimal pH
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>
                        Slightly Off
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                        Poor pH
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>
                        No Data
                    </span>
                </div>
            @endif
        </div>

        @if($record->analysis_type === 'with_lab')
            <p class="text-xs text-gray-400 dark:text-gray-500">
                Based on BSWM / FAO Soil Interpretation Guidelines
            </p>
        @else
            <p class="text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1">
                <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                pH-based assessment only — NPK &amp; Organic Matter require laboratory analysis
            </p>
        @endif
    </div>

    {{-- pH Level --}}
    <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg {{ $borderClass($phStat) }}">
        <div class="flex items-center justify-between mb-1">
            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">pH Level</h4>
            @if($phVal !== null)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass($phStat) }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass($phStat) }}"></span>
                    {{ $statusLbl($phStat) }}
                </span>
            @endif
        </div>
        <p class="text-sm font-semibold text-gray-900 dark:text-white">
            {{ $phVal !== null ? number_format($phVal, 2) : 'N/A' }}
        </p>
        @if($phVal !== null)
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Rating: {{ $phRating }}</p>
        @endif
    </div>

    {{-- Organic Matter --}}
    <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg {{ $borderClass($omStat) }}">
        <div class="flex items-center justify-between mb-1">
            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Organic Matter</h4>
            @if($omVal !== null)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass($omStat) }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass($omStat) }}"></span>
                    {{ $statusLbl($omStat) }}
                </span>
            @endif
        </div>
        <p class="text-sm font-semibold text-gray-900 dark:text-white">
            {{ $omVal !== null ? number_format($omVal, 2) . '%' : 'N/A' }}
        </p>
        @if($omVal !== null)
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Rating: {{ $omRating }}</p>
        @endif
    </div>

    {{-- Nitrogen (N) --}}
    <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg {{ $borderClass($nStat) }}">
        <div class="flex items-center justify-between mb-1">
            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Nitrogen (N)</h4>
            @if($nVal !== null)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass($nStat) }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass($nStat) }}"></span>
                    {{ $statusLbl($nStat) }}
                </span>
            @endif
        </div>
        <p class="text-sm font-semibold text-gray-900 dark:text-white">
            {{ $nVal !== null ? number_format($nVal, 2) . '%' : 'N/A' }}
        </p>
        @if($nVal !== null)
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Rating: {{ $nRating }}</p>
        @endif
    </div>

    {{-- Phosphorus (P) --}}
    <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg {{ $borderClass($pStat) }}">
        <div class="flex items-center justify-between mb-1">
            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Phosphorus (P)</h4>
            @if($pVal !== null)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass($pStat) }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass($pStat) }}"></span>
                    {{ $statusLbl($pStat) }}
                </span>
            @endif
        </div>
        <p class="text-sm font-semibold text-gray-900 dark:text-white">
            {{ $pVal !== null ? number_format($pVal, 2) . ' ppm' : 'N/A' }}
        </p>
        @if($pVal !== null)
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Rating: {{ $pRating }}</p>
        @endif
    </div>

    {{-- Potassium (K) --}}
    <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg {{ $borderClass($kStat) }}">
        <div class="flex items-center justify-between mb-1">
            <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Potassium (K)</h4>
            @if($kVal !== null)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass($kStat) }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass($kStat) }}"></span>
                    {{ $statusLbl($kStat) }}
                </span>
            @endif
        </div>
        <p class="text-sm font-semibold text-gray-900 dark:text-white">
            {{ $kVal !== null ? number_format($kVal, 2) . ' ppm' : 'N/A' }}
        </p>
        @if($kVal !== null)
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Rating: {{ $kRating }}</p>
        @endif
    </div>

    {{-- Recommendation — PDF Viewer --}}
    @if($record->recommendation)
        <div class="md:col-span-2 rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600 shadow-md">

            {{-- Toolbar --}}
            <div class="flex items-center justify-between bg-gray-700 dark:bg-gray-900 px-4 py-2">
                <div class="flex items-center gap-2 text-white text-xs font-medium">
                    <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Soil Analysis Report — {{ $record->farm_name ?? 'Farm' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    {{-- Expand / Collapse toggle --}}
                    <button
                        type="button"
                        onclick="
                            var viewer = document.getElementById('rec-viewer-{{ $record->id }}');
                            var btn = document.getElementById('rec-expand-btn-{{ $record->id }}');
                            if (viewer.style.maxHeight === 'none') {
                                viewer.style.maxHeight = '520px';
                                btn.textContent = 'Expand';
                            } else {
                                viewer.style.maxHeight = 'none';
                                btn.textContent = 'Collapse';
                            }
                        "
                        id="rec-expand-btn-{{ $record->id }}"
                        class="text-xs text-gray-300 hover:text-white bg-gray-600 hover:bg-gray-500 px-2 py-1 rounded transition">
                        Expand
                    </button>
                    {{-- Print button --}}
                    <button
                        type="button"
                        onclick="
                            var content = document.getElementById('rec-viewer-{{ $record->id }}').innerText;
                            var win = window.open('', '_blank');
                            win.document.write('<html><head><title>Soil Analysis Report</title><style>body{font-family:monospace;font-size:13px;padding:24px;white-space:pre;}</style></head><body>' + content + '</body></html>');
                            win.document.close();
                            win.print();
                        "
                        class="text-xs text-gray-300 hover:text-white bg-gray-600 hover:bg-gray-500 px-2 py-1 rounded transition flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"></path>
                        </svg>
                        Print
                    </button>
                </div>
            </div>

            {{-- Page area --}}
            <div class="bg-gray-200 dark:bg-gray-700 px-4 py-3 overflow-y-auto"
                 style="max-height: 520px;"
                 id="rec-viewer-{{ $record->id }}">
                <div class="bg-white dark:bg-gray-900 shadow-lg rounded mx-auto px-6 py-6"
                     style="max-width: 860px; min-height: 400px;">
                    <pre class="text-xs leading-relaxed text-gray-800 dark:text-gray-200 whitespace-pre overflow-x-auto"
                         style="font-family: 'Courier New', Courier, monospace;">{{ $record->recommendation }}</pre>
                </div>
            </div>

            {{-- Footer bar --}}
            <div class="bg-gray-700 dark:bg-gray-900 px-4 py-1 text-center text-xs text-gray-400">
                Analysis Type:
                <span class="font-medium text-gray-200">
                    {{ $record->analysis_type === 'with_lab' ? 'With Laboratory' : 'Without Laboratory' }}
                </span>
                &nbsp;|&nbsp; Generated on:
                <span class="font-medium text-gray-200">{{ $record->updated_at?->format('M d, Y') ?? 'N/A' }}</span>
            </div>

        </div>
    @endif

    {{-- ── Conversation Thread (Expert ↔ Farmer) ──────────────────────────── --}}
    @if($record->expert_comments || $record->farmer_reply)
        <div class="md:col-span-2 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
            {{-- Thread header --}}
            <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Conversation Thread</span>
                @php $isApproved = $record->validation_status === 'approved'; @endphp
                @if($record->validation_status !== 'pending')
                    <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                        {{ $isApproved
                            ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'
                            : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' }}">
                        {{ $isApproved ? 'Approved' : 'Disapproved' }}
                    </span>
                @endif
            </div>

            {{-- Scrollable chat area --}}
            <div class="max-h-64 overflow-y-auto space-y-3 p-3 bg-gray-50 dark:bg-gray-800/50">

                {{-- Expert comment bubble (left, blue) --}}
                @if($record->expert_comments)
                    <div class="flex justify-start">
                        <div class="max-w-[80%] bg-blue-100 dark:bg-blue-900/40 rounded-lg p-2.5">
                            <div class="flex items-center gap-1.5 mb-1">
                                <svg class="w-3 h-3 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-xs font-semibold text-blue-800 dark:text-blue-300">
                                    {{ $record->validator?->name ?? 'Expert' }}
                                </span>
                                <span class="inline-block px-1.5 py-0.5 text-[10px] bg-blue-200 dark:bg-blue-800 text-blue-700 dark:text-blue-300 rounded">
                                    {{ $isApproved ? 'Recommendation' : 'Comments' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-900 dark:text-gray-100 leading-relaxed">{{ $record->expert_comments }}</p>
                            @if($record->validated_at)
                                <span class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 block">
                                    {{ $record->validated_at->format('M d, Y · h:i A') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Farmer reply bubble (right, amber) --}}
                @if($record->farmer_reply)
                    <div class="flex justify-end">
                        <div class="max-w-[80%] bg-amber-100 dark:bg-amber-900/40 rounded-lg p-2.5">
                            <div class="flex items-center justify-end gap-1.5 mb-1">
                                <span class="inline-block px-1.5 py-0.5 text-[10px] bg-amber-200 dark:bg-amber-800 text-amber-700 dark:text-amber-300 rounded">
                                    Action Taken
                                </span>
                                <span class="text-xs font-semibold text-amber-800 dark:text-amber-300">
                                    {{ $record->farmer
                                        ? trim(($record->farmer->firstname ?? '') . ' ' . ($record->farmer->lastname ?? ''))
                                        : 'Farmer' }}
                                </span>
                                <svg class="w-3 h-3 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-900 dark:text-gray-100 leading-relaxed">{{ $record->farmer_reply }}</p>
                            @if($record->farmer_reply_date)
                                <span class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 block text-right">
                                    {{ $record->farmer_reply_date->format('M d, Y · h:i A') }}
                                    <span class="italic ml-1">via CAFARM App</span>
                                </span>
                            @endif
                        </div>
                    </div>
                @elseif($record->expert_comments)
                    {{-- No farmer reply yet --}}
                    <p class="text-xs text-center text-gray-400 dark:text-gray-500 py-1 italic">
                        Awaiting farmer's response via mobile app…
                    </p>
                @endif

            </div>
        </div>
    @endif
</div>
