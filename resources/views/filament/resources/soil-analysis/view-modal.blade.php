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

<script>
window['printSoilReport_{{ $record->id }}'] = function() {
    var d = {!! json_encode([
        'farmName'        => $record->farm_name ?? 'N/A',
        'soilType'        => $record->soil_type ?? 'N/A',
        'cropVariety'     => $record->crop_variety ?? 'N/A',
        'dateCollected'   => $record->date_collected?->format('F d, Y') ?? '—',
        'phLevel'         => $record->ph_level ?? '—',
        'nitrogen'        => $record->nitrogen ?? '—',
        'phosphorus'      => $record->phosphorus ?? '—',
        'potassium'       => $record->potassium ?? '—',
        'organicMatter'   => $record->organic_matter ?? '—',
        'location'        => $record->location ?? '—',
        'generatedAt'     => now()->format('F d, Y \a\t h:i A'),
        'diagnosis'       => $record->ai_diagnosis ?? '',
        'farmerSummary'   => $record->ai_farmer_summary ?? '',
        'keyConcerns'     => is_array($record->ai_key_concerns) ? $record->ai_key_concerns : [],
        'priorityActions' => is_array($record->ai_priority_actions) ? $record->ai_priority_actions : [],
        'organicAlts'     => is_array($record->ai_organic_alternatives) ? $record->ai_organic_alternatives : [],
        'practices'       => is_array($record->ai_practices) ? $record->ai_practices : [],
        'monitoring'      => is_array($record->ai_monitoring_plan) ? $record->ai_monitoring_plan : [],
        'outcomes'        => $record->ai_expected_outcomes ?? '',
        'reminders'       => is_array($record->ai_reminders) ? $record->ai_reminders : [],
        'soilRemarks'     => is_array($record->ai_soil_remarks) ? $record->ai_soil_remarks : [],
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};
    function esc(s){if(!s)return'';return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
    function ul(arr){if(!arr||!arr.length)return'';var h='\x3Cul style="margin:4px 0 8px;padding-left:20px"\x3E';arr.forEach(function(x){h+='\x3Cli style="margin:3px 0"\x3E'+esc(x)+'\x3C/li\x3E';});return h+'\x3C/ul\x3E';}
    function ol(arr){if(!arr||!arr.length)return'';var h='\x3Col style="margin:4px 0 8px;padding-left:20px"\x3E';arr.forEach(function(x){h+='\x3Cli style="margin:3px 0"\x3E'+esc(x)+'\x3C/li\x3E';});return h+'\x3C/ol\x3E';}
    var css='body{font-family:Arial,sans-serif;margin:24px 32px;color:#1f2937;font-size:12px;line-height:1.55}'
        +'h1{font-size:17px;color:#4c1d95;border-bottom:2px solid #4c1d95;padding-bottom:6px;margin-bottom:14px}'
        +'h2{font-size:12px;font-weight:800;color:#1e5631;margin:18px 0 6px;padding-bottom:4px;border-bottom:2px solid #1e5631;text-transform:uppercase;letter-spacing:.5px}'
        +'.ig{display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin:6px 0 10px}'
        +'.ii{background:#f9fafb;border:1px solid #e5e7eb;padding:6px 9px;border-radius:5px}'
        +'.il{font-size:9px;text-transform:uppercase;color:#6b7280;margin-bottom:2px}'
        +'.iv{font-size:12px;font-weight:600;color:#111827}'
        +'.ab{background:#fef3c7;color:#92400e;padding:8px 12px;border-radius:6px;border-left:3px solid #f59e0b;margin:4px 0}'
        +'.rb{background:#fef2f2;color:#991b1b;padding:8px 12px;border-radius:6px;margin:4px 0}'
        +'.gb{background:#f0fdf4;color:#166534;padding:8px 12px;border-radius:6px;margin:4px 0}'
        +'table{width:100%;border-collapse:collapse;margin:6px 0}'
        +'th{background:#4c1d95;color:#fff;padding:7px 10px;text-align:left;font-size:10px}'
        +'td{padding:7px 10px;border:1px solid #e5e7eb;font-size:11px}'
        +'tr:nth-child(even) td{background:#f9fafb}'
        +'.ft{margin-top:20px;border-top:1px solid #e5e7eb;padding-top:6px;font-size:9px;color:#9ca3af;text-align:center}'
        +'@media print{body{margin:8px 16px}}';
    var h='\x3C!DOCTYPE html\x3E\x3Chtml\x3E\x3Chead\x3E\x3Cmeta charset="utf-8"\x3E\x3Ctitle\x3ESoil AI Report\x3C/title\x3E\x3Cstyle\x3E'+css+'\x3C/style\x3E\x3C/head\x3E\x3Cbody\x3E';
    h+='\x3Cdiv style="text-align:center;border-bottom:3px solid #4c1d95;padding-bottom:12px;margin-bottom:18px"\x3E';
    h+='\x3Cp style="font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#9ca3af;margin:0 0 4px"\x3ECofSys \u2014 Coffee Agri-Farming Management System\x3C/p\x3E';
    h+='\x3Ch1 style="font-size:18px;font-weight:900;color:#4c1d95;margin:0 0 4px"\x3EAI Soil Fertility Recommendation\x3C/h1\x3E';
    h+='\x3Cp style="font-size:13px;font-weight:700;color:#374151;margin:0 0 8px"\x3E'+esc(d.farmName)+'\x3C/p\x3E';
    h+='\x3Cp style="font-size:10px;color:#9ca3af;margin:0"\x3ESoil Type: \x3Cstrong\x3E'+esc(d.soilType)+'\x3C/strong\x3E &bull; Crop: \x3Cstrong\x3E'+esc(d.cropVariety)+'\x3C/strong\x3E &bull; Date: \x3Cstrong\x3E'+esc(d.dateCollected)+'\x3C/strong\x3E &bull; Generated: \x3Cstrong\x3E'+esc(d.generatedAt)+'\x3C/strong\x3E\x3C/p\x3E\x3C/div\x3E';
    h+='\x3Ch2\x3ESoil Test Values\x3C/h2\x3E\x3Cdiv class="ig"\x3E';
    [['pH Level',d.phLevel],['Nitrogen (N)',d.nitrogen],['Phosphorus (P)',d.phosphorus],['Potassium (K)',d.potassium],['Organic Matter',d.organicMatter],['Location',d.location]].forEach(function(f){h+='\x3Cdiv class="ii"\x3E\x3Cdiv class="il"\x3E'+f[0]+'\x3C/div\x3E\x3Cdiv class="iv"\x3E'+esc(String(f[1]))+'\x3C/div\x3E\x3C/div\x3E';});
    h+='\x3C/div\x3E';
    if(d.farmerSummary){h+='\x3Ch2\x3ESummary for the Farmer\x3C/h2\x3E\x3Cdiv class="ab"\x3E'+esc(d.farmerSummary)+'\x3C/div\x3E';}
    if(d.diagnosis){h+='\x3Ch2\x3ESoil Diagnosis\x3C/h2\x3E\x3Cp style="text-align:justify"\x3E'+esc(d.diagnosis)+'\x3C/p\x3E';}
    if(d.keyConcerns&&d.keyConcerns.length){h+='\x3Ch2\x3EKey Concerns\x3C/h2\x3E'+ul(d.keyConcerns);}
    if(d.priorityActions&&d.priorityActions.length){h+='\x3Ch2\x3EPriority Actions\x3C/h2\x3E'+ol(d.priorityActions);}
    if(d.soilRemarks&&Object.keys(d.soilRemarks).length){
        var labels={ph:'Soil pH',om:'Organic Matter',n:'Nitrogen (N)',p:'Phosphorus (P)',k:'Potassium (K)'};
        h+='\x3Ch2\x3ESoil Parameter Analysis\x3C/h2\x3E\x3Ctable\x3E\x3Cthead\x3E\x3Ctr\x3E\x3Cth style="width:140px"\x3EParameter\x3C/th\x3E\x3Cth\x3ERemarks\x3C/th\x3E\x3C/tr\x3E\x3C/thead\x3E\x3Ctbody\x3E';
        Object.keys(labels).forEach(function(k){if(d.soilRemarks[k]){h+='\x3Ctr\x3E\x3Ctd style="font-weight:700;color:#4c1d95"\x3E'+labels[k]+'\x3C/td\x3E\x3Ctd\x3E'+esc(d.soilRemarks[k])+'\x3C/td\x3E\x3C/tr\x3E';}});
        h+='\x3C/tbody\x3E\x3C/table\x3E';
    }
    if(d.organicAlts&&d.organicAlts.length){h+='\x3Ch2\x3EOrganic &amp; Low-Cost Alternatives\x3C/h2\x3E'+ol(d.organicAlts);}
    if(d.practices&&d.practices.length){h+='\x3Ch2\x3EGood Farming Practices\x3C/h2\x3E'+ol(d.practices);}
    if(d.monitoring&&d.monitoring.length){h+='\x3Ch2\x3EMonitoring Plan\x3C/h2\x3E'+ul(d.monitoring);}
    if(d.outcomes){h+='\x3Ch2\x3EExpected Outcomes\x3C/h2\x3E\x3Cdiv class="gb"\x3E'+esc(d.outcomes)+'\x3C/div\x3E';}
    if(d.reminders&&d.reminders.length){h+='\x3Ch2\x3EImportant Reminders\x3C/h2\x3E\x3Cdiv class="rb"\x3E'+ul(d.reminders)+'\x3C/div\x3E';}
    h+='\x3Cdiv class="ft"\x3ECofSys \u2014 AI Soil Fertility Recommendation &nbsp;|&nbsp; Powered by Google Gemini AI &nbsp;|&nbsp; Generated on '+esc(d.generatedAt)+'\x3C/div\x3E\x3C/body\x3E\x3C/html\x3E';
    var w=window.open('','_blank');w.document.write(h);w.document.close();setTimeout(function(){w.print();},350);
};
</script>

<div class="space-y-4" x-data="{ activeTab: 'Fertilizer Recommendation' }">

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
        @if($record->analysis_type === 'without_lab' && !empty($record->topography))
            <div class="bg-emerald-50 dark:bg-emerald-900/20 p-3 rounded-lg border border-emerald-200 dark:border-emerald-800 col-span-2 md:col-span-4">
                <h4 class="text-xs font-medium text-emerald-700 dark:text-emerald-300 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 20l4.5-9 4.5 9m0 0h9l-4.5-9-4.5 9m9 0H3"/></svg>
                    Farm Topography
                </h4>
                @php
                    $topoData = [
                        'Plains/Flat Land' => [
                            'characteristics' => 'Flat or gently sloping terrain (0–5% gradient). Water drains slowly and may accumulate during heavy rainfall. Soil depth is typically good with moderate to high nutrient retention.',
                            'fertilizer'      => 'Apply fertilizer in shallow ring furrows (5–10 cm deep) around the drip line. Standard KAPE rates apply without adjustment. Single-dose applications are acceptable since flat terrain retains nutrients well.',
                            'water'           => 'Ensure proper drainage channels between rows to prevent waterlogging. Construct shallow ditches (15–20 cm deep) to direct excess water away from roots. Avoid fertilizer application before heavy rain.',
                            'erosion'         => 'Minimal erosion risk. Maintain ground cover crops or organic mulch to prevent surface compaction. Avoid bare soil between rows during rainy season.',
                            'organic'         => 'Apply 1–2 kg compost per tree per application. Mulch around tree base with dried leaves, grass, or rice straw (5–10 cm layer) to improve soil structure and drainage.',
                            'special'         => 'Monitor for waterlogging during prolonged rain. If standing water persists more than 24 hours, improve drainage immediately. Suitable for intercropping with leguminous cover crops.',
                            'erosionRisk'     => 'Low',
                            'waterConcern'    => 'Waterlogging',
                            'keyAction'       => 'Ensure proper drainage channels',
                        ],
                        'Hills/Hilly Land' => [
                            'characteristics' => 'Moderately to steeply sloping terrain (15–45% gradient). High risk of erosion and nutrient runoff. Water drains rapidly downhill, reducing fertilizer retention in the root zone.',
                            'fertilizer'      => 'Split each application into 2 smaller doses 4–6 weeks apart. Apply fertilizer on the uphill side of each tree in crescent-shaped micro-basins to catch water and nutrients. Never apply during or before heavy rain.',
                            'water'           => 'Construct contour trenches (12–18 inches deep) following the natural curves of the slope to slow water flow and encourage infiltration. Space trenches closer on steeper slopes. Install staggered water collection pits between rows.',
                            'erosion'         => 'Plant nitrogen-fixing hedgerows (Gliricidia sepium, Flemingia macrophylla, Desmodium rensonii) in contour rows every 3–5 m across the slope (SALT method). Maintain permanent ground cover. Apply heavy mulching (10–15 cm).',
                            'organic'         => 'Increase compost to 2–3 kg per tree to improve water and nutrient retention. Place mulch on the uphill side of trees to trap moisture and reduce runoff. Use hedgerow prunings as green manure in alleys.',
                            'special'         => 'Establish shade trees (madre de cacao, Erythrina) to reduce erosion and protect soil from rainfall impact. Avoid deep tillage. Plant coffee rows along contour lines, not up/down the slope. Consider slow-release fertilizers.',
                            'erosionRisk'     => 'High',
                            'waterConcern'    => 'Rapid runoff',
                            'keyAction'       => 'Contour hedgerows (SALT); heavy mulching',
                        ],
                        'Valleys' => [
                            'characteristics' => 'Low-lying areas between hills that naturally collect water, sediment, and nutrients from higher ground. Soils are often deeper and more fertile but prone to flooding and waterlogging.',
                            'fertilizer'      => 'Reduce fertilizer rates by 10–20% from standard KAPE recommendations since valley soils naturally receive nutrient-rich sediment. Apply in slightly raised mounds or berms (10–15 cm above ground) to prevent submersion.',
                            'water'           => 'Install robust drainage with main channels and connecting lateral ditches. Raise planting beds 15–30 cm above the surrounding ground to keep roots above flood level. Clear drainage outlets before rainy season.',
                            'erosion'         => 'Minimal slope erosion risk, but manage sediment deposition from uphill runoff. Plant vegetative buffer strips at valley edges to filter incoming runoff and trap excess sediment. Maintain ground cover.',
                            'organic'         => 'Standard 1 kg compost per tree is sufficient since valley soils accumulate organic material naturally. Focus on maintaining soil structure and aeration through composting rather than increasing nutrient inputs.',
                            'special'         => 'Monitor soil moisture closely — valley soils may remain saturated, risking root rot. Select moisture-tolerant coffee varieties if available. Avoid planting in the lowest points where standing water is frequent. Apply fertilizer during drier periods.',
                            'erosionRisk'     => 'Low (slope)',
                            'waterConcern'    => 'Flooding; saturation',
                            'keyAction'       => 'Raised beds; robust drainage',
                        ],
                        'Plateaus/Tablelands' => [
                            'characteristics' => 'Elevated, relatively flat terrain with good sun exposure and typically deep, fertile soil. Combines flat-land ease of management with good natural drainage. May be more exposed to wind and temperature extremes.',
                            'fertilizer'      => 'Standard KAPE fertilizer rates apply. Apply in ring furrows around the drip line. Single-dose applications per schedule are generally effective since plateaus have good nutrient retention and moderate drainage.',
                            'water'           => 'Drainage is naturally good on elevated terrain. Focus on moisture conservation through mulching rather than drainage construction. On volcanic or porous soils, thicken the mulch layer (10–15 cm) to slow evaporation.',
                            'erosion'         => 'Low erosion risk on the flat plateau surface. Monitor plateau edges where slopes begin — runoff from the flat area can concentrate and cause gully erosion at edges. Plant vegetative barriers along plateau margins.',
                            'organic'         => 'Apply 1–2 kg compost per tree. Heavier mulching (10–15 cm layer) is recommended to conserve moisture and protect soil from wind erosion, which is more common on exposed elevated terrain.',
                            'special'         => 'Establish windbreak hedgerows or shade trees around the farm perimeter. Monitor soil pH annually — volcanic plateaus may be naturally acidic. Greater sun exposure may increase water demand; consider supplemental irrigation during prolonged dry periods.',
                            'erosionRisk'     => 'Low (edges)',
                            'waterConcern'    => 'Wind; evaporation',
                            'keyAction'       => 'Windbreaks; heavy mulching for moisture',
                        ],
                        'Terraces' => [
                            'characteristics' => 'Constructed or naturally stepped terrain where slopes have been converted into level planting platforms (SALT technology). Combines flat-land benefits (reduced erosion, nutrient retention) with the natural drainage of sloped terrain.',
                            'fertilizer'      => 'Apply fertilizer on the inner (uphill) side of each terrace level in shallow furrows along the drip line. Standard KAPE rates apply since terraces retain nutrients effectively. Ensure fertilizer is placed away from terrace edges.',
                            'water'           => 'Maintain terrace walls and retaining structures — damaged walls cause worse concentrated erosion. Ensure each level has a slight inward slope (1–2%) to direct water toward the hillside. Install overflow channels for heavy rain.',
                            'erosion'         => 'Reinforce terrace risers with vegetation (grasses, leguminous shrubs) or stone to prevent wall collapse. Plant cover crops on each level between coffee rows. Inspect terrace structures before each rainy season and repair immediately.',
                            'organic'         => 'Apply 1–2 kg compost per tree. Use hedgerow prunings from terrace-edge plantings as green manure on each level. Mulching is especially effective on terraces since the level surface retains mulch in place.',
                            'special'         => 'Terrace construction requires initial labor but can reduce soil loss by over 90% (Paningbatan et al., 1995). Combine terracing with SALT agroforestry: plant nitrogen-fixing hedgerows (Gliricidia, Flemingia, Leucaena) along terrace edges.',
                            'erosionRisk'     => 'Low (if maintained)',
                            'waterConcern'    => 'Wall overflow',
                            'keyAction'       => 'Maintain walls; vegetative reinforcement',
                        ],
                    ];
                    $topo = $topoData[$record->topography] ?? null;
                @endphp
                <p class="mt-1 text-sm font-bold text-emerald-900 dark:text-emerald-100">{{ $record->topography }}</p>
                @if($topo)
                    <p class="mt-2 text-xs text-gray-700 dark:text-gray-300 italic">{{ $topo['characteristics'] }}</p>
                    <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-2 text-xs">
                        <div class="bg-white dark:bg-gray-800 p-2 rounded border border-emerald-200 dark:border-emerald-800">
                            <div class="font-semibold text-emerald-800 dark:text-emerald-300">Fertilizer Application Method</div>
                            <div class="text-gray-700 dark:text-gray-300 mt-0.5">{{ $topo['fertilizer'] }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-2 rounded border border-emerald-200 dark:border-emerald-800">
                            <div class="font-semibold text-blue-700 dark:text-blue-300">Water Management</div>
                            <div class="text-gray-700 dark:text-gray-300 mt-0.5">{{ $topo['water'] }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-2 rounded border border-emerald-200 dark:border-emerald-800">
                            <div class="font-semibold text-amber-700 dark:text-amber-300">Erosion Control</div>
                            <div class="text-gray-700 dark:text-gray-300 mt-0.5">{{ $topo['erosion'] }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-2 rounded border border-emerald-200 dark:border-emerald-800">
                            <div class="font-semibold text-green-700 dark:text-green-300">Organic Matter</div>
                            <div class="text-gray-700 dark:text-gray-300 mt-0.5">{{ $topo['organic'] }}</div>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-2 rounded border border-emerald-200 dark:border-emerald-800 md:col-span-2">
                            <div class="font-semibold text-orange-700 dark:text-orange-300">Special Considerations</div>
                            <div class="text-gray-700 dark:text-gray-300 mt-0.5">{{ $topo['special'] }}</div>
                        </div>
                    </div>
                    <div class="mt-2 flex flex-wrap gap-2 text-[10px]">
                        <span class="px-2 py-0.5 rounded bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-200"><strong>Erosion Risk:</strong> {{ $topo['erosionRisk'] }}</span>
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200"><strong>Water Concern:</strong> {{ $topo['waterConcern'] }}</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200"><strong>Key Action:</strong> {{ $topo['keyAction'] }}</span>
                    </div>
                    <p class="mt-2 text-[10px] text-gray-500 dark:text-gray-400 italic">Source: Topography-Based Coffee Soil Management Guide (KAPE + SALT).</p>
                @endif
            </div>
        @endif
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
    {{-- Document viewer (matches pest & disease style) --}}
    <div class="rounded-xl overflow-hidden border border-gray-300 dark:border-gray-600 shadow-md mt-2">

        {{-- Toolbar --}}
        <div class="flex items-center justify-between bg-gray-700 dark:bg-gray-900 px-4 py-2">
            <div class="flex items-center gap-2 text-white text-xs font-medium">
                <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                </svg>
                <span>AI Soil Fertility Recommendation — {{ $record->farm_name ?? 'Farm' }}</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button"
                    onclick="var v=document.getElementById('soil-ai-doc-{{ $record->id }}'),b=document.getElementById('soil-ai-exp-{{ $record->id }}');if(v.style.maxHeight==='none'){v.style.maxHeight='600px';b.textContent='Expand';}else{v.style.maxHeight='none';b.textContent='Collapse';}"
                    id="soil-ai-exp-{{ $record->id }}"
                    class="text-xs text-gray-300 hover:text-white bg-gray-600 hover:bg-gray-500 px-2 py-1 rounded transition">
                    Expand
                </button>
                <button type="button" onclick="window['printSoilReport_{{ $record->id }}']&&window['printSoilReport_{{ $record->id }}']();"
                    class="text-xs text-gray-300 hover:text-white bg-gray-600 hover:bg-gray-500 px-2 py-1 rounded transition flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/></svg>
                    Print
                </button>
            </div>
        </div>

        {{-- Paper --}}
        <div class="bg-gray-200 dark:bg-gray-700 px-4 py-4 overflow-y-auto"
             style="max-height:600px;" id="soil-ai-doc-{{ $record->id }}">
            <div class="bg-white shadow-lg rounded mx-auto"
                 style="max-width:720px; min-height:420px; padding:32px 36px; font-family:'Segoe UI',Arial,sans-serif;">

                @php
                    $hs = "font-size:13px; font-weight:800; color:#4c1d95; margin:0 0 8px 0; padding-bottom:5px; border-bottom:2px solid #4c1d95; text-transform:uppercase; letter-spacing:0.5px;";
                @endphp

                {{-- Document Title --}}
                <div style="text-align:center; border-bottom:3px solid #4c1d95; padding-bottom:16px; margin-bottom:26px;">
                    <p style="font-size:9px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:#9ca3af; margin:0 0 4px 0;">
                        CofSys &mdash; Coffee Agri-Farming Management System
                    </p>
                    <h1 style="font-size:18px; font-weight:900; color:#4c1d95; margin:0 0 4px 0;">
                        AI Soil Fertility Recommendation
                    </h1>
                    <p style="font-size:14px; font-weight:700; color:#374151; margin:0 0 10px 0;">
                        {{ $record->farm_name ?? 'N/A' }}
                    </p>
                    <p style="font-size:10px; color:#9ca3af; margin:8px 0 0 0;">
                        Soil Type: <strong style="color:#374151;">{{ $record->soil_type ?? '—' }}</strong>
                        &nbsp;&bull;&nbsp;
                        Crop: <strong style="color:#374151;">{{ $record->crop_variety ?? '—' }}</strong>
                        &nbsp;&bull;&nbsp;
                        Date Collected: <strong style="color:#374151;">{{ $record->date_collected?->format('F d, Y') ?? '—' }}</strong>
                        &nbsp;&bull;&nbsp;
                        Generated: <strong style="color:#374151;">{{ now()->format('F d, Y') }}</strong>
                    </p>
                </div>

                {{-- 1. Farmer Summary --}}
                @if(!empty($aiFarmerSummary))
                <div style="margin-bottom:22px; background:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; border-radius:6px; padding:14px 16px;">
                    <h2 style="{{ $hs }}">Summary for the Farmer</h2>
                    <p style="font-size:12.5px; line-height:1.85; color:#1f2937; margin:0; text-align:justify;">{{ $aiFarmerSummary }}</p>
                </div>
                @endif

                {{-- 2. Diagnosis --}}
                @if(!empty($aiDiagnosis))
                <div style="margin-bottom:22px;">
                    <h2 style="{{ $hs }}">Soil Diagnosis</h2>
                    <p style="font-size:12.5px; line-height:1.85; color:#1f2937; margin:0; text-align:justify;">{{ $aiDiagnosis }}</p>
                </div>
                @endif

                {{-- 3. Key Concerns --}}
                @if(!empty($aiKeyConcerns))
                <div style="margin-bottom:22px;">
                    <h2 style="{{ $hs }}">Key Concerns</h2>
                    <ul style="margin:0; padding-left:22px; font-size:12.5px; color:#1f2937; line-height:1.85;">
                        @foreach($aiKeyConcerns as $concern)
                            <li style="margin-bottom:5px; text-align:justify;">{{ $concern }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- 4. Priority Actions --}}
                @if(!empty($aiPriorityActions))
                <div style="margin-bottom:22px;">
                    <h2 style="{{ $hs }}">Priority Actions</h2>
                    <ol style="margin:0; padding-left:22px; font-size:12.5px; color:#1f2937; line-height:1.85;">
                        @foreach($aiPriorityActions as $action)
                            <li style="margin-bottom:7px; text-align:justify;">{{ $action }}</li>
                        @endforeach
                    </ol>
                </div>
                @endif

                {{-- 5. Soil Parameter Remarks --}}
                @if(!empty($soilRemarks))
                <div style="margin-bottom:22px;">
                    <h2 style="{{ $hs }}">Soil Parameter Analysis</h2>
                    <table style="width:100%; border-collapse:collapse; font-size:12px;">
                        <thead>
                            <tr>
                                <th style="background:#4c1d95; color:#fff; padding:9px 12px; text-align:left; font-size:11px; font-weight:700; width:140px; border:1px solid #4c1d95;">Parameter</th>
                                <th style="background:#4c1d95; color:#fff; padding:9px 12px; text-align:left; font-size:11px; font-weight:700; border:1px solid #4c1d95;">Remarks &amp; Recommendation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paramLabels as $key => $label)
                            @if(!empty($soilRemarks[$key]))
                            <tr style="background:{{ $loop->even ? '#f9fafb' : '#ffffff' }};">
                                <td style="padding:9px 12px; vertical-align:top; border:1px solid #e5e7eb; font-weight:700; color:#4c1d95; font-size:11px;">{{ $label }}</td>
                                <td style="padding:9px 12px; vertical-align:top; border:1px solid #e5e7eb; color:#1f2937; line-height:1.75; text-align:justify; font-size:12px;">{{ $soilRemarks[$key] }}</td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                {{-- 6. Organic Alternatives --}}
                @if(!empty($aiOrganicAlts))
                <div style="margin-bottom:22px;">
                    <h2 style="{{ $hs }}">Organic &amp; Low-Cost Alternatives</h2>
                    <ol style="margin:0; padding-left:22px; font-size:12.5px; color:#1f2937; line-height:1.85;">
                        @foreach($aiOrganicAlts as $alt)
                            <li style="margin-bottom:5px; text-align:justify;">{{ $alt }}</li>
                        @endforeach
                    </ol>
                </div>
                @endif

                {{-- 7. Good Farming Practices --}}
                @if(!empty($aiPractices))
                <div style="margin-bottom:22px;">
                    <h2 style="{{ $hs }}">Good Farming Practices</h2>
                    <ol style="margin:0; padding-left:22px; font-size:12.5px; color:#1f2937; line-height:1.85;">
                        @foreach($aiPractices as $practice)
                            <li style="margin-bottom:7px; text-align:justify;">{{ $practice }}</li>
                        @endforeach
                    </ol>
                </div>
                @endif

                {{-- 8. Monitoring Plan --}}
                @if(!empty($aiMonitoring))
                <div style="margin-bottom:22px;">
                    <h2 style="{{ $hs }}">Monitoring Plan</h2>
                    <ul style="margin:0; padding-left:22px; font-size:12.5px; color:#1f2937; line-height:1.85;">
                        @foreach($aiMonitoring as $item)
                            <li style="margin-bottom:5px; text-align:justify;">{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- 9. Expected Outcomes --}}
                @if(!empty($aiOutcomes))
                <div style="margin-bottom:22px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:6px; padding:14px 16px;">
                    <h2 style="{{ $hs }}">Expected Outcomes</h2>
                    <p style="font-size:12.5px; line-height:1.85; color:#1f2937; margin:0; text-align:justify;">{{ $aiOutcomes }}</p>
                </div>
                @endif

                {{-- 10. Reminders --}}
                @if(!empty($aiReminders))
                <div style="margin-bottom:8px; background:#fef2f2; border:1px solid #fecaca; border-radius:6px; padding:14px 16px;">
                    <h2 style="{{ $hs }}">Important Reminders</h2>
                    <ul style="margin:0; padding-left:22px; font-size:12.5px; color:#1f2937; line-height:1.85;">
                        @foreach($aiReminders as $reminder)
                            <li style="margin-bottom:5px; text-align:justify;">{{ $reminder }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Document footer --}}
                <div style="margin-top:28px; border-top:1px solid #e5e7eb; padding-top:8px; font-size:10px; color:#9ca3af; text-align:center;">
                    CofSys — Coffee Agri-Farming Management System &nbsp;·&nbsp; AI-Assisted Soil Fertility Recommendation
                </div>

            </div>{{-- end paper --}}
        </div>{{-- end bg-gray-200 scroll --}}

        {{-- Bottom bar --}}
        <div class="bg-gray-700 dark:bg-gray-900 px-4 py-1 text-center text-xs text-gray-400">
            Powered by Google Gemini AI &nbsp;|&nbsp; Generated on:
            <span class="font-medium text-gray-200">{{ $record->updated_at?->format('M d, Y') ?? now()->format('M d, Y') }}</span>
        </div>

    </div>{{-- end document viewer --}}

    @else
        {{-- Placeholder when no AI data yet --}}
        <div class="border border-dashed border-purple-200 dark:border-purple-800 rounded-xl p-4 flex items-center gap-3 bg-purple-50/50 dark:bg-purple-900/10">
            <svg class="w-5 h-5 text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <p class="text-xs text-purple-600 dark:text-purple-400">
                No AI recommendation yet. Click <strong>Generate AI Draft</strong> below to get an AI-assisted soil fertility recommendation for this analysis.
            </p>
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
