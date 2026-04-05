{{-- Soil Analysis View Modal --}}

@php
    // ── Validation status badge colors ──────────────────────────────────────
    $statusColors = [
        'pending'     => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
        'approved'    => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        'disapproved' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    ];
    $statusColor = $statusColors[$record->validation_status] ?? $statusColors['pending'];

    // ── Soil rating functions ────────────────────────────────────────────────
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

    // ── Status mapping ───────────────────────────────────────────────────────
    $phToStatus      = fn(string $r): string => match($r) {
        'Medium'                => 'normal',
        'Low', 'High'           => 'critical',
        'Very Low', 'Very High' => 'warning',
        default                 => 'none',
    };
    $nutrientToStatus = fn(string $r): string => match($r) {
        'Medium', 'High', 'Moderate' => 'normal',
        'Low', 'Very High'           => 'critical',
        'Very Low'                   => 'warning',
        default                      => 'none',
    };

    // ── CSS helpers ──────────────────────────────────────────────────────────
    $badgeClass = fn(string $s): string => match($s) {
        'normal'   => 'bg-green-100 text-green-800 dark:bg-green-900/60 dark:text-green-300',
        'critical' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/60 dark:text-yellow-300',
        'warning'  => 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300',
        default    => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
    };
    $dotClass   = fn(string $s): string => match($s) {
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
    $statusLbl  = fn(string $s): string => match($s) {
        'normal'   => 'Normal',
        'critical' => 'Critical',
        'warning'  => 'Warning',
        default    => 'N/A',
    };

    // ── Raw values ───────────────────────────────────────────────────────────
    $phVal = is_numeric($record->ph_level)       ? (float) $record->ph_level       : null;
    $omVal = is_numeric($record->organic_matter) ? (float) $record->organic_matter : null;
    $nVal  = is_numeric($record->nitrogen)       ? (float) $record->nitrogen       : null;
    $pVal  = is_numeric($record->phosphorus)     ? (float) $record->phosphorus     : null;
    $kVal  = is_numeric($record->potassium)      ? (float) $record->potassium      : null;

    // ── Ratings ──────────────────────────────────────────────────────────────
    $phRating = $ratePh($phVal);
    $omRating = $rateOm($omVal);
    $nRating  = $rateN($nVal);
    $pRating  = $rateP($pVal);
    $kRating  = $rateK($kVal);

    // ── Indicator statuses ───────────────────────────────────────────────────
    $phStat = $phVal !== null ? $phToStatus($phRating)       : 'none';
    $omStat = $omVal !== null ? $nutrientToStatus($omRating) : 'none';
    $nStat  = $nVal  !== null ? $nutrientToStatus($nRating)  : 'none';
    $pStat  = $pVal  !== null ? $nutrientToStatus($pRating)  : 'none';
    $kStat  = $kVal  !== null ? $nutrientToStatus($kRating)  : 'none';

    // ── Remarks ──────────────────────────────────────────────────────────────
    $nRemark = match($nRating) {
        'Very Low'  => 'Severely deficient. Apply 90–120 kg N/ha.',
        'Low'       => 'Deficient. Apply 60–90 kg N/ha in 2 splits.',
        'Medium'    => 'Adequate. Apply 30–60 kg N/ha as needed.',
        'High'      => 'Sufficient. Reduce to 0–30 kg N/ha only.',
        'Very High' => 'Excess. No nitrogen fertilizer needed.',
        default     => 'No data.',
    };
    $pRemark = match($pRating) {
        'Very Low'  => 'Severely deficient. Apply 60–90 kg P₂O₅/ha.',
        'Low'       => 'Deficient. Apply 40–60 kg P₂O₅/ha (banded).',
        'Medium'    => 'Adequate. Maintenance dose 20–40 kg P₂O₅/ha.',
        'High'      => 'Sufficient. Reduce to 0–20 kg P₂O₅/ha max.',
        'Very High' => 'Excess. Do NOT apply phosphorus fertilizer.',
        default     => 'No data.',
    };
    $kRemark = match($kRating) {
        'Very Low'  => 'Severely deficient. Apply 60–90 kg K₂O/ha.',
        'Low'       => 'Deficient. Apply 40–60 kg K₂O/ha in 2 splits.',
        'Medium'    => 'Adequate. Maintenance dose 20–40 kg K₂O/ha.',
        'High'      => 'Sufficient. Reduce to 0–20 kg K₂O/ha only.',
        'Very High' => 'Excess. No potassium fertilizer needed.',
        default     => 'No data.',
    };

    // ── Fertilizer rows ──────────────────────────────────────────────────────
    $fertRows = [];
    $nFert = match($nRating) {
        'Very Low'  => ['Urea (46-0-0) or Ammonium Sulfate (21-0-0)', '90–120 kg N/ha',  'Split 2× (see Schedule)', $nRemark],
        'Low'       => ['Urea (46-0-0) or Complete Fert. (14-14-14)', '60–90 kg N/ha',   'Split 2× (see Schedule)', $nRemark],
        'Medium'    => ['Urea (46-0-0)',                               '30–60 kg N/ha',   'As needed per growth',    $nRemark],
        'High'      => ['Urea (46-0-0) — reduced rate',               '0–30 kg N/ha',    'Only if signs appear',    $nRemark],
        'Very High' => ['None required',                               'Do not apply',    'Risk of N leaching',      $nRemark],
        default     => ['N/A', 'N/A', 'N/A', 'N/A'],
    };
    $fertRows[] = array_merge(['Nitrogen (N)'], $nFert);

    $pFert = match($pRating) {
        'Very Low'  => ['Triple Superphosphate (0-46-0)',            '60–90 kg P₂O₅/ha', 'At planting, banded',     $pRemark],
        'Low'       => ['Solophos (0-18-0) or Complete (14-14-14)', '40–60 kg P₂O₅/ha', 'At planting, banded',     $pRemark],
        'Medium'    => ['Solophos (0-18-0)',                         '20–40 kg P₂O₅/ha', 'At planting (maint.)',    $pRemark],
        'High'      => ['Reduced P — only if crop demands it',      '0–20 kg P₂O₅/ha',  'Optional only',           $pRemark],
        'Very High' => ['None required',                             'Do not apply',      'Risk of pollution',       $pRemark],
        default     => ['N/A', 'N/A', 'N/A', 'N/A'],
    };
    $fertRows[] = array_merge(['Phosphorus (P)'], $pFert);

    $kFert = match($kRating) {
        'Very Low'  => ['Muriate of Potash (0-0-60) or SOP (0-0-50)', '60–90 kg K₂O/ha', 'Split 2× (see Schedule)', $kRemark],
        'Low'       => ['Muriate of Potash (0-0-60)',                  '40–60 kg K₂O/ha', 'Split 2× (see Schedule)', $kRemark],
        'Medium'    => ['Muriate of Potash (0-0-60)',                  '20–40 kg K₂O/ha', 'At planting (maint.)',    $kRemark],
        'High'      => ['Muriate of Potash — reduced rate',           '0–20 kg K₂O/ha',  'High-demand crops only',  $kRemark],
        'Very High' => ['None required',                              'Do not apply',     'Excess harms Ca & Mg',    $kRemark],
        default     => ['N/A', 'N/A', 'N/A', 'N/A'],
    };
    $fertRows[] = array_merge(['Potassium (K)'], $kFert);

    // ── Amendment rows ───────────────────────────────────────────────────────
    $amendRows = [];
    $phAmend = match($phRating) {
        'Very Low'  => ['pH Correction (Acidic)',    'Agricultural Lime (CaCO₃)',          '2–4 t/ha',   'NOW — before fertilizing'],
        'Low'       => ['pH Correction (Acidic)',    'Agricultural Lime (CaCO₃)',          '1–2 t/ha',   'Before planting season'],
        'High'      => ['pH Correction (Alkaline)',  'Elemental Sulfur / Ammon. Sulfate', '0.5–1 t/ha', 'Before planting season'],
        'Very High' => ['pH Correction (Alkaline)',  'Elemental Sulfur + Gypsum',          '1–3 t/ha',   'Immediately — multi-season'],
        default     => null,
    };
    if ($phAmend) $amendRows[] = $phAmend;

    $omAmend = match($omRating) {
        'Very Low'  => ['Organic Matter (Build-up)',  'Compost or Well-decomposed Manure', '5–10 t/ha', 'Start of season + biochar'],
        'Low'       => ['Organic Matter (Build-up)',  'Compost or Organic Amendments',     '3–5 t/ha',  'Annually, every season'],
        'Moderate'  => ['Organic Matter (Maintain)',  'Compost',                           '2–3 t/ha',  'Annually to maintain'],
        default     => null,
    };
    if ($omAmend) $amendRows[] = $omAmend;

    if (empty($amendRows)) {
        $amendRows[] = ['None needed', 'Soil pH and OM are at good levels.', '—', 'Continue current practices.'];
    }

    // ── Schedule rows ────────────────────────────────────────────────────────
    $schedRows   = [];
    $needsLime   = in_array($phRating, ['Very Low', 'Low']);
    $needsSulfur = in_array($phRating, ['High', 'Very High']);
    $needsCompost= in_array($omRating, ['Very Low', 'Low', 'Moderate']);
    $needsN      = in_array($nRating,  ['Very Low', 'Low', 'Medium']);
    $needsP      = in_array($pRating,  ['Very Low', 'Low', 'Medium']);
    $needsK      = in_array($kRating,  ['Very Low', 'Low', 'Medium']);

    if ($needsLime) {
        $schedRows[] = ['IMMEDIATELY', 'Apply Agricultural Lime to correct acidic soil pH.'];
        $schedRows[] = ['After 2–4 weeks', 'Apply compost and fertilizers (after lime has worked).'];
    } elseif ($needsSulfur) {
        $schedRows[] = ['IMMEDIATELY', 'Apply Elemental Sulfur to lower high soil pH.'];
    }
    if ($needsCompost) {
        $schedRows[] = ['Every planting season', 'Apply compost or organic matter to build soil health.'];
    }
    if ($needsP) {
        $schedRows[] = ['At planting', 'Apply phosphorus fertilizer near root zone (banded).'];
    }
    if ($needsN || $needsK) {
        $apps = [];
        if ($needsN) $apps[] = '50% of nitrogen';
        if ($needsK) $apps[] = '50% of potassium';
        $schedRows[] = ['At planting (basal)', 'Apply ' . implode(' + ', $apps) . ' fertilizer.'];
    }
    if ($needsN || $needsK) {
        $apps = [];
        if ($needsN) $apps[] = 'remaining 50% nitrogen';
        if ($needsK) $apps[] = 'remaining 50% potassium';
        $schedRows[] = ['1–2 months after planting', 'Apply ' . implode(' + ', $apps) . ' (top-dress).'];
    }
    if (!$needsLime && !$needsSulfur && !$needsN && !$needsP && !$needsK) {
        $schedRows[] = ['Every planting season', 'Apply maintenance fertilizer based on crop needs.'];
    }
    $schedRows[] = ['Every year', 'Apply organic matter (compost/manure) to maintain soil health.'];
    $schedRows[] = ['Every 6–12 months', 'Re-test soil to evaluate progress and update fertilizer plan.'];

    // ── Tabs ─────────────────────────────────────────────────────────────────
    $soilTabs = ['Fertilizer Recommendation', 'Soil Amendment', 'Application Schedule', 'Farming Practices', 'Important Notes'];
@endphp

<div class="space-y-4"
     x-data="{ activeTab: 'Fertilizer Recommendation' }">

    {{-- ── Status Badge ─────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Status</h3>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
            @if($record->validation_status === 'approved')
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            @elseif($record->validation_status === 'disapproved')
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            @endif
            {{ ucfirst($record->validation_status) }}
        </span>
    </div>

    {{-- ── Farm Info ────────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
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

    {{-- ── Analysis Results — Cards (not in tabs) ──────────────────────────── --}}
    <div>
        {{-- Header + legend --}}
        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Soil Health Indicators
                @if($record->analysis_type !== 'with_lab')
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                        <svg class="w-2.5 h-2.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        pH Only
                    </span>
                @endif
            </h4>
            <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>Normal</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-yellow-400 inline-block"></span>Critical</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>Warning</span>
            </div>
        </div>

        @if($record->analysis_type === 'with_lab')
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-2">Based on BSWM / FAO Soil Interpretation Guidelines (Landon 1991)</p>
        @else
            <p class="text-xs text-amber-600 dark:text-amber-400 flex items-center gap-1 mb-2">
                <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                pH-based assessment only — NPK &amp; Organic Matter require laboratory analysis
            </p>
        @endif

        {{-- Cards grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

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
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $phVal !== null ? number_format($phVal, 2) : 'N/A' }}</p>
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
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $omVal !== null ? number_format($omVal, 2).'%' : 'N/A' }}</p>
                @if($omVal !== null)
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Rating: {{ $omRating }}</p>
                @endif
            </div>

            {{-- Nitrogen --}}
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
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $nVal !== null ? number_format($nVal, 2).'%' : 'N/A' }}</p>
                @if($nVal !== null)
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Rating: {{ $nRating }}</p>
                @endif
            </div>

            {{-- Phosphorus --}}
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
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $pVal !== null ? number_format($pVal, 2).' ppm' : 'N/A' }}</p>
                @if($pVal !== null)
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Rating: {{ $pRating }}</p>
                @endif
            </div>

            {{-- Potassium --}}
            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg md:col-span-2 {{ $borderClass($kStat) }}">
                <div class="flex items-center justify-between mb-1">
                    <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Potassium (K)</h4>
                    @if($kVal !== null)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass($kStat) }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dotClass($kStat) }}"></span>
                            {{ $statusLbl($kStat) }}
                        </span>
                    @endif
                </div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $kVal !== null ? number_format($kVal, 2).' ppm' : 'N/A' }}</p>
                @if($kVal !== null)
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Rating: {{ $kRating }}</p>
                @endif
            </div>

        </div>
    </div>

    {{-- ── Tabbed Recommendation Section ───────────────────────────────────── --}}
    <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">

        {{-- Tab Header --}}
        <div class="bg-gray-50 dark:bg-gray-800/60 border-b border-gray-200 dark:border-gray-700">
            <div class="flex overflow-x-auto soil-tabs-scroll" style="scrollbar-width: thin; scrollbar-color: #d1d5db transparent;">
                <style>
                    .soil-tabs-scroll::-webkit-scrollbar { height: 4px; }
                    .soil-tabs-scroll::-webkit-scrollbar-track { background: transparent; }
                    .soil-tabs-scroll::-webkit-scrollbar-thumb { background-color: #d1d5db; border-radius: 9999px; }
                    .dark .soil-tabs-scroll::-webkit-scrollbar-thumb { background-color: #6b7280; }
                </style>
                @foreach($soilTabs as $tab)
                    <button
                        type="button"
                        @click.stop.prevent="activeTab = '{{ $tab }}'"
                        :class="activeTab === '{{ $tab }}'
                            ? 'border-b-2 border-green-600 text-green-700 dark:text-green-400 bg-white dark:bg-gray-900 font-semibold'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                        class="flex-shrink-0 px-4 py-2.5 text-xs transition-colors duration-150 whitespace-nowrap focus:outline-none"
                    >{{ $tab }}</button>
                @endforeach
            </div>
        </div>

        {{-- Tab Body --}}
        <div class="p-4 bg-white dark:bg-gray-900 min-h-[180px]">

            {{-- ── Fertilizer Recommendation ────────────────────────────────── --}}
            <div x-show="activeTab === 'Fertilizer Recommendation'" style="display:none;">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Source: BSWM / FAO Guidelines &amp; Philippine Fertilizer Recommendations</p>
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead>
                            <tr class="bg-green-50 dark:bg-green-900/20">
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Nutrient</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Fertilizer</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Rate (per ha)</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Application Time</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @foreach($fertRows as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                    <td class="px-3 py-2.5 font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">{{ $row[0] }}</td>
                                    <td class="px-3 py-2.5 text-gray-700 dark:text-gray-300">{{ $row[1] }}</td>
                                    <td class="px-3 py-2.5 font-medium text-blue-700 dark:text-blue-300 whitespace-nowrap">{{ $row[2] }}</td>
                                    <td class="px-3 py-2.5 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $row[3] }}</td>
                                    <td class="px-3 py-2.5 text-xs text-gray-500 dark:text-gray-400">{{ $row[4] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── Soil Amendment ───────────────────────────────────────────── --}}
            <div x-show="activeTab === 'Soil Amendment'" style="display:none;">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Soil pH and organic matter corrections based on assessment.</p>
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead>
                            <tr class="bg-amber-50 dark:bg-amber-900/20">
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Concern</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Product</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Rate</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">When to Apply</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @foreach($amendRows as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                    <td class="px-3 py-2.5 font-semibold text-gray-800 dark:text-gray-200">{{ $row[0] }}</td>
                                    <td class="px-3 py-2.5 text-gray-700 dark:text-gray-300">{{ $row[1] }}</td>
                                    <td class="px-3 py-2.5 font-medium text-amber-700 dark:text-amber-300 whitespace-nowrap">{{ $row[2] }}</td>
                                    <td class="px-3 py-2.5 text-gray-600 dark:text-gray-400">{{ $row[3] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── Application Schedule Summary ─────────────────────────────── --}}
            <div x-show="activeTab === 'Application Schedule'" style="display:none;">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Fertilizer application schedule based on soil condition assessment.</p>
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead>
                            <tr class="bg-blue-50 dark:bg-blue-900/20">
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase w-1/3">When</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Action / What to Do</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @foreach($schedRows as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                                    <td class="px-3 py-2.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold
                                            @if(str_contains($row[0], 'IMMEDIATELY')) bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300
                                            @elseif(str_contains($row[0], 'planting')) bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300
                                            @elseif(str_contains($row[0], 'month')) bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300
                                            @else bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300
                                            @endif">
                                            {{ $row[0] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2.5 text-gray-700 dark:text-gray-300">{{ $row[1] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ── Good Farming Practices ───────────────────────────────────── --}}
            <div x-show="activeTab === 'Farming Practices'" style="display:none;">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Recommended cultural practices for sustainable coffee soil management.</p>
                <ul class="space-y-3">
                    @foreach([
                        'Mulch around coffee plants with dried leaves or rice straw (5–10 cm thick).',
                        'Keep crop residues on the field after harvest — do NOT burn them.',
                        'Plant legume cover crops during fallow season to naturally add nitrogen.',
                        'Avoid deep plowing to protect soil structure and organic matter.',
                        'Never apply fertilizer on extremely dry or waterlogged soil.',
                        'Always wash hands after handling fertilizers and chemicals.',
                    ] as $practice)
                        <li class="flex items-start gap-2.5 text-sm text-gray-700 dark:text-gray-300">
                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center mt-0.5">
                                <svg class="w-3 h-3 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                            <span>{{ $practice }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- ── Important Notes ──────────────────────────────────────────── --}}
            <div x-show="activeTab === 'Important Notes'" style="display:none;">
                <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">Critical reminders to ensure effective fertilizer use and soil health improvement.</p>
                <ul class="space-y-3">
                    @foreach([
                        ['ALWAYS correct soil pH FIRST — fertilizers do not work on wrong pH.', 'red'],
                        ['SPLIT nitrogen and potassium — never apply the full dose at once.', 'orange'],
                        ['RE-TEST your soil every 6–12 months after major amendments.', 'blue'],
                        ['BANDED application of phosphorus near roots is more efficient than broadcasting.', 'purple'],
                    ] as [$note, $color])
                        <li class="flex items-start gap-2.5">
                            <span class="flex-shrink-0 w-5 h-5 rounded-full bg-{{ $color }}-100 dark:bg-{{ $color }}-900/40 flex items-center justify-center mt-0.5">
                                <svg class="w-3 h-3 text-{{ $color }}-600 dark:text-{{ $color }}-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            </span>
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $note }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>

    {{-- ── AI Recommendation (structured) ───────────────────────────────── --}}
    @php
        // If ai_diagnosis looks like raw JSON (parse fallback), re-parse it now for display
        $aiDiagnosis       = $record->ai_diagnosis ?? '';
        $aiFarmerSummary   = $record->ai_farmer_summary ?? '';
        $aiKeyConcerns     = is_array($record->ai_key_concerns) ? $record->ai_key_concerns : [];
        $aiPriorityActions = is_array($record->ai_priority_actions) ? $record->ai_priority_actions : [];
        $aiSoilRemarks     = is_array($record->ai_soil_remarks) ? $record->ai_soil_remarks : [];
        $aiOrganicAlts     = is_array($record->ai_organic_alternatives) ? $record->ai_organic_alternatives : [];
        $aiPractices       = is_array($record->ai_practices) ? $record->ai_practices : [];
        $aiMonitoring      = is_array($record->ai_monitoring_plan) ? $record->ai_monitoring_plan : [];
        $aiOutcomes        = $record->ai_expected_outcomes ?? '';
        $aiReminders       = is_array($record->ai_reminders) ? $record->ai_reminders : [];

        // Detect if ai_diagnosis contains raw JSON and re-parse
        if (!empty($aiDiagnosis) && str_starts_with(ltrim($aiDiagnosis), '{')) {
            $reparsed = json_decode($aiDiagnosis, true, 512, JSON_INVALID_UTF8_IGNORE);
            if (is_array($reparsed)) {
                $aiDiagnosis       = $reparsed['diagnosis']            ?? $aiDiagnosis;
                $aiFarmerSummary   = $reparsed['farmer_summary']       ?? $aiFarmerSummary;
                $aiKeyConcerns     = $reparsed['key_concerns']         ?? $aiKeyConcerns;
                $aiPriorityActions = $reparsed['priority_actions']     ?? $aiPriorityActions;
                $aiSoilRemarks     = $reparsed['soil_remarks']         ?? $aiSoilRemarks;
                $aiOrganicAlts     = $reparsed['organic_alternatives'] ?? $aiOrganicAlts;
                $aiPractices       = $reparsed['practices']            ?? $aiPractices;
                $aiMonitoring      = $reparsed['monitoring_plan']      ?? $aiMonitoring;
                $aiOutcomes        = $reparsed['expected_outcomes']    ?? $aiOutcomes;
                $aiReminders       = $reparsed['reminders']            ?? $aiReminders;
            }
        }

        $hasAi = !empty($aiDiagnosis) || !empty($aiFarmerSummary)
               || !empty($aiKeyConcerns) || !empty($aiPriorityActions) || !empty($aiPractices);
        $soilRemarks = is_array($aiSoilRemarks) ? $aiSoilRemarks : [];
        $paramLabels = ['ph' => 'Soil pH', 'om' => 'Organic Matter', 'n' => 'Nitrogen (N)', 'p' => 'Phosphorus (P)', 'k' => 'Potassium (K)'];
    @endphp

    @if($hasAi)
    <div class="rounded-xl overflow-hidden border border-purple-200 dark:border-purple-800 shadow-md mt-2">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-3 bg-gradient-to-r from-purple-700 to-purple-900">
            <div class="flex items-center gap-2 text-white text-sm font-semibold">
                <svg class="w-4 h-4 text-purple-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                </svg>
                AI Soil Analysis Recommendation — {{ $record->farm_name ?? 'Farm' }}
            </div>
            <span class="text-xs text-purple-300">Powered by Google Gemini AI</span>
        </div>

        <div class="bg-white dark:bg-gray-900 px-6 py-5 space-y-6">

            {{-- 1. Farmer Summary --}}
            @if(!empty($record->ai_farmer_summary))
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-lg p-4">
                <h2 class="text-sm font-bold text-yellow-800 dark:text-yellow-300 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    Summary for the Farmer
                </h2>
                <p class="text-sm text-yellow-900 dark:text-yellow-200 leading-relaxed">{{ $aiFarmerSummary }}</p>
            </div>
            @endif

            {{-- 2. Diagnosis --}}
            @if(!empty($record->ai_diagnosis))
            <div>
                <h2 class="text-xs font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wider border-b border-purple-200 dark:border-purple-700 pb-1 mb-3">Soil Diagnosis</h2>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $aiDiagnosis }}</p>
            </div>
            @endif

            {{-- 3. Key Concerns --}}
            @if(!empty($aiKeyConcerns))
            <div>
                <h2 class="text-xs font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wider border-b border-purple-200 dark:border-purple-700 pb-1 mb-3">Key Concerns</h2>
                <ul class="space-y-2">
                    @foreach($aiKeyConcerns as $concern)
                    <li class="flex gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <span class="mt-1 flex-shrink-0 w-2 h-2 rounded-full bg-red-400"></span>
                        <span class="leading-relaxed">{{ $concern }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- 4. Priority Actions --}}
            @if(!empty($aiPriorityActions))
            <div>
                <h2 class="text-xs font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wider border-b border-purple-200 dark:border-purple-700 pb-1 mb-3">Priority Actions</h2>
                <ol class="space-y-2 list-none">
                    @foreach($aiPriorityActions as $idx => $action)
                    <li class="flex gap-3 text-sm text-gray-700 dark:text-gray-300">
                        <span class="flex-shrink-0 w-6 h-6 rounded-full bg-purple-600 text-white flex items-center justify-center text-xs font-bold">{{ $idx + 1 }}</span>
                        <span class="leading-relaxed pt-0.5">{{ $action }}</span>
                    </li>
                    @endforeach
                </ol>
            </div>
            @endif

            {{-- 5. Soil Parameter Remarks --}}
            @if(!empty($soilRemarks))
            <div>
                <h2 class="text-xs font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wider border-b border-purple-200 dark:border-purple-700 pb-1 mb-3">Soil Parameter Analysis</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-purple-700 text-white">
                                <th class="text-left px-3 py-2 font-semibold text-xs w-32">Parameter</th>
                                <th class="text-left px-3 py-2 font-semibold text-xs">Remarks &amp; Recommendation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paramLabels as $key => $label)
                            @if(!empty($soilRemarks[$key]))
                            <tr class="border-b border-gray-100 dark:border-gray-700 {{ $loop->even ? 'bg-gray-50 dark:bg-gray-800' : 'bg-white dark:bg-gray-900' }}">
                                <td class="px-3 py-3 font-semibold text-purple-700 dark:text-purple-400 text-xs align-top">{{ $label }}</td>
                                <td class="px-3 py-3 text-gray-700 dark:text-gray-300 leading-relaxed text-xs">{{ $soilRemarks[$key] }}</td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- 6. Organic Alternatives --}}
            @if(!empty($aiOrganicAlts))
            <div>
                <h2 class="text-xs font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wider border-b border-purple-200 dark:border-purple-700 pb-1 mb-3">Organic &amp; Low-Cost Alternatives</h2>
                <ul class="space-y-2">
                    @foreach($aiOrganicAlts as $idx => $alt)
                    <li class="flex gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <span class="flex-shrink-0 text-green-500 font-bold text-xs mt-0.5">{{ $idx + 1 }}.</span>
                        <span class="leading-relaxed">{{ $alt }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- 7. Good Farming Practices --}}
            @if(!empty($aiPractices))
            <div>
                <h2 class="text-xs font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wider border-b border-purple-200 dark:border-purple-700 pb-1 mb-3">Good Farming Practices</h2>
                <ol class="space-y-2 list-none">
                    @foreach($aiPractices as $idx => $practice)
                    <li class="flex gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <span class="flex-shrink-0 text-purple-500 font-bold text-xs mt-0.5">{{ $idx + 1 }}.</span>
                        <span class="leading-relaxed">{{ $practice }}</span>
                    </li>
                    @endforeach
                </ol>
            </div>
            @endif

            {{-- 8. Monitoring Plan --}}
            @if(!empty($aiMonitoring))
            <div>
                <h2 class="text-xs font-bold text-purple-700 dark:text-purple-400 uppercase tracking-wider border-b border-purple-200 dark:border-purple-700 pb-1 mb-3">Monitoring Plan</h2>
                <ul class="space-y-2">
                    @foreach($aiMonitoring as $item)
                    <li class="flex gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <span class="mt-1.5 flex-shrink-0 w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                        <span class="leading-relaxed">{{ $item }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- 9. Expected Outcomes --}}
            @if(!empty($record->ai_expected_outcomes))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4">
                <h2 class="text-xs font-bold text-green-800 dark:text-green-400 uppercase tracking-wide mb-2">Expected Outcomes</h2>
                <p class="text-sm text-green-900 dark:text-green-200 leading-relaxed">{{ $aiOutcomes }}</p>
            </div>
            @endif

            {{-- 10. Reminders --}}
            @if(!empty($aiReminders))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
                <h2 class="text-xs font-bold text-red-800 dark:text-red-400 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    Important Reminders
                </h2>
                <ul class="space-y-2 mt-1">
                    @foreach($aiReminders as $reminder)
                    <li class="flex gap-2 text-sm text-red-900 dark:text-red-200">
                        <span class="mt-1 flex-shrink-0 w-2 h-2 rounded-full bg-red-500"></span>
                        <span class="leading-relaxed">{{ $reminder }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

        </div>

        {{-- Footer --}}
        <div class="bg-purple-900 px-5 py-2 text-center text-xs text-purple-300">
            Generated on: <span class="font-medium text-white">{{ $record->updated_at?->format('M d, Y · h:i A') ?? 'N/A' }}</span>
            &nbsp;|&nbsp; Powered by Google Gemini AI
        </div>
    </div>
    @endif

    {{-- ── Conversation Thread (Expert ↔ Farmer) ───────────────────────────── --}}

    @if($record->expert_comments || $record->farmer_reply)
        @php $isApproved = $record->validation_status === 'approved'; @endphp
        <div id="conversation-thread" style="border-radius:14px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.13);border:1px solid #e2e8f0;">
            <div style="display:flex;align-items:center;gap:8px;padding:10px 16px;background:linear-gradient(90deg,#1e293b 0%,#334155 100%);border-bottom:1px solid #475569;">
                <svg style="width:15px;height:15px;color:#94a3b8;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                <span style="font-size:11px;font-weight:700;color:#f1f5f9;letter-spacing:0.5px;text-transform:uppercase;">Conversation Thread</span>
                @if($record->validation_status !== 'pending')
                    <span style="margin-left:auto;display:inline-flex;align-items:center;padding:2px 10px;border-radius:999px;font-size:10px;font-weight:700;{{ $isApproved ? 'background:#dcfce7;color:#15803d;' : 'background:#fee2e2;color:#b91c1c;' }}">
                        {{ $isApproved ? '✓ Approved' : '✕ Disapproved' }}
                    </span>
                @endif
            </div>
            <div style="max-height:280px;overflow-y:auto;padding:14px 12px;display:flex;flex-direction:column;gap:12px;background:#f1f5f9;">
                @if($record->expert_comments)
                    <div style="display:flex;justify-content:flex-start;align-items:flex-end;gap:8px;">
                        <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#1d4ed8);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:14px;height:14px;color:white;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                        </div>
                        <div style="max-width:78%;">
                            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                                <div>
                                    <span style="font-size:11px;font-weight:700;color:#1e40af;">{{ $record->validator?->name ?? 'Expert' }}</span>
                                    @if($record->validator?->agriculturalProfessional?->agency)
                                        <div style="font-size:10px;font-weight:600;color:#3b82f6;">Expert from {{ $record->validator->agriculturalProfessional->agency }}</div>
                                    @endif
                                </div>
                                <span style="font-size:9px;font-weight:600;padding:1px 7px;border-radius:999px;background:#dbeafe;color:#1d4ed8;">{{ $isApproved ? 'Recommendation' : 'Comments' }}</span>
                            </div>
                            <div style="background:white;border-radius:0 12px 12px 12px;padding:10px 13px;box-shadow:0 1px 4px rgba(0,0,0,0.08);border-left:3px solid #3b82f6;">
                                <p style="font-size:13px;color:#1e293b;line-height:1.55;margin:0;">{{ $record->expert_comments }}</p>
                                @if($record->validated_at)
                                    <span style="font-size:10px;color:#94a3b8;display:block;margin-top:6px;">{{ $record->validated_at->format('M d, Y · h:i A') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Additional expert comments from multiple experts --}}
                @foreach($record->expertComments()->with('expert.agriculturalProfessional')->get() as $comment)
                    <div style="display:flex;justify-content:flex-start;align-items:flex-end;gap:8px;">
                        <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#1d4ed8);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:14px;height:14px;color:white;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                        </div>
                        <div style="max-width:78%;">
                            <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                                <div>
                                    <span style="font-size:11px;font-weight:700;color:#1e40af;">{{ $comment->expert?->name ?? 'Expert' }}</span>
                                    @if($comment->expert?->agriculturalProfessional?->agency)
                                        <div style="font-size:10px;font-weight:600;color:#3b82f6;">Expert from {{ $comment->expert->agriculturalProfessional->agency }}</div>
                                    @endif
                                </div>
                                <span style="font-size:9px;font-weight:600;padding:1px 7px;border-radius:999px;background:#dbeafe;color:#1d4ed8;">Recommendation</span>
                            </div>
                            <div style="background:white;border-radius:0 12px 12px 12px;padding:10px 13px;box-shadow:0 1px 4px rgba(0,0,0,0.08);border-left:3px solid #3b82f6;">
                                <p style="font-size:13px;color:#1e293b;line-height:1.55;margin:0;">{{ $comment->message }}</p>
                                <span style="font-size:10px;color:#94a3b8;display:block;margin-top:6px;">{{ $comment->created_at->format('M d, Y · h:i A') }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($record->farmer_reply)
                    <div style="display:flex;justify-content:flex-end;align-items:flex-end;gap:8px;">
                        <div style="max-width:78%;">
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;margin-bottom:4px;">
                                <span style="font-size:9px;font-weight:600;padding:1px 7px;border-radius:999px;background:#fef3c7;color:#92400e;">Action Taken</span>
                                <div style="text-align:right;">
                                    <span style="font-size:11px;font-weight:700;color:#92400e;">{{ $record->farmer ? trim(($record->farmer->firstname ?? '') . ' ' . ($record->farmer->lastname ?? '')) : 'Farmer' }}</span>
                                    <div style="font-size:10px;font-weight:600;color:#d97706;">Farmer</div>
                                </div>
                            </div>
                            <div style="background:linear-gradient(135deg,#fef9c3,#fde68a);border-radius:12px 0 12px 12px;padding:10px 13px;box-shadow:0 1px 4px rgba(0,0,0,0.08);border-right:3px solid #f59e0b;">
                                <p style="font-size:13px;color:#1c1917;line-height:1.55;margin:0;">{{ $record->farmer_reply }}</p>
                                @if($record->farmer_reply_date)
                                    <span style="font-size:10px;color:#78716c;display:block;margin-top:6px;text-align:right;">
                                        {{ $record->farmer_reply_date->format('M d, Y · h:i A') }} <em>via CofSys App</em>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:14px;height:14px;color:white;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                        </div>
                    </div>
                @elseif($record->expert_comments)
                    <div style="text-align:center;padding:6px 0;">
                        <span style="font-size:11px;color:#64748b;font-style:italic;background:#e2e8f0;padding:4px 14px;border-radius:999px;">⏳ Awaiting farmer's response via mobile app…</span>
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
