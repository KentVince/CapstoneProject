<?php

namespace App\Services;

/**
 * Fertilizer recommendation engine for WITHOUT-LABORATORY soil analysis.
 *
 * All data sourced directly from:
 *   "Non-Bearing Coffee Soil Analysis — Fertilizer Recommendations Without
 *    Laboratory Soil Analysis" (KAPE / University-based, Philippines, 2026)
 *
 * Rates are given in grams per hill (g/hill) per application.
 * Coffee stage covered: Non-Bearing (vegetative growth phase).
 */
class NoLabRecommendationService
{
    // ─────────────────────────────────────────────────────────────────────────
    //  PUBLIC ENTRY POINT
    // ─────────────────────────────────────────────────────────────────────────

    public function generate(array $data): string
    {
        $crop     = $data['crop_variety'] ?? 'Coffee';
        $soilType = $data['soil_type']    ?? 'Unknown';
        $ph       = is_numeric($data['ph_level'] ?? null) ? (float) $data['ph_level'] : null;

        $soilKey  = $this->mapSoilType($soilType);

        return $this->buildReport($crop, $soilType, $soilKey, $ph);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  SOIL TYPE MAPPING
    // ─────────────────────────────────────────────────────────────────────────

    private function mapSoilType(string $soilType): string
    {
        return match (strtolower(trim($soilType))) {
            'clay', 'silty clay'         => 'Clay',
            'sandy loam'                 => 'Sandy Loam',
            'loam'                       => 'Loam',
            default                      => 'General',
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  NPK DATA  (Table from Section 2 & 3 of the guide, non-bearing stage)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns [N, P, K] in g/hill per application for non-bearing coffee.
     * Source: Section 2 & Summary Comparison Table (Section 3).
     */
    private function getNpk(string $soilKey): array
    {
        return match ($soilKey) {
            'Clay'       => ['n' => 150, 'p' => 50,  'k' => 150],
            'Sandy Loam' => ['n' => 70,  'p' => 35,  'k' => 80],
            'Loam'       => ['n' => 65,  'p' => 30,  'k' => 65],
            default      => ['n' => 120, 'p' => 120, 'k' => 60],  // General / Unknown
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  SCHEDULE
    // ─────────────────────────────────────────────────────────────────────────

    private function getScheduleNote(string $soilKey): string
    {
        return match ($soilKey) {
            'Sandy Loam' => 'Every 6 weeks (split 3-month dose into 2 smaller doses to reduce leaching).',
            default      => 'Every 3 months — 4 applications per year.',
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  pH ASSESSMENT  (Section 4 of the guide)
    // ─────────────────────────────────────────────────────────────────────────

    private function getPhRating(?float $ph): string
    {
        if ($ph === null) return 'Not Tested';
        if ($ph < 4.5)   return 'Too Acidic';
        if ($ph <= 5.5)  return 'Acidic';
        if ($ph <= 6.5)  return 'Ideal';
        if ($ph <= 7.5)  return 'Slightly Alkaline';
        return 'Too Alkaline';
    }

    private function getPhEffect(?float $ph): string
    {
        if ($ph === null) return 'Use pH strips, vinegar/baking soda test, or BSWM STK for assessment.';
        if ($ph < 4.5)   return 'Aluminum toxicity damages roots. P, Ca, Mg unavailable. Poor growth.';
        if ($ph <= 5.5)  return 'Reduced nutrient availability. Some root damage. Moderate growth limit.';
        if ($ph <= 6.5)  return 'Optimal nutrient availability. Best growth and yield potential.';
        if ($ph <= 7.5)  return 'Fe, Zn, Mn less available. Chlorosis (yellowing) of young leaves.';
        return 'Severe micronutrient deficiency. Stunted growth. Poor fruit development.';
    }

    private function getPhAction(?float $ph): string
    {
        if ($ph === null) return 'Test pH before applying fertilizer. Target 5.5-6.5 for coffee.';
        if ($ph < 4.5)   return 'Apply agricultural lime 1-2 kg/hill. Reapply every 6-12 months.';
        if ($ph <= 5.5)  return 'Apply lime 0.5-1 kg/hill + compost. Re-check pH after 6 months.';
        if ($ph <= 6.5)  return 'No pH correction needed. Maintain with regular organic matter.';
        if ($ph <= 7.5)  return 'Add organic matter; use ammonium sulfate; apply ferrous sulfate.';
        return 'Apply elemental sulfur 0.5-1 kg/hill + heavy compost. Use acid fertilizers.';
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  SOIL-TYPE TIPS  (Sections 2.1 / 2.2 / 2.3 of the guide)
    // ─────────────────────────────────────────────────────────────────────────

    private function getSoilTips(string $soilKey): array
    {
        return match ($soilKey) {
            'Clay' => [
                'Incorporate compost or rice hull to improve drainage and aeration.',
                'Apply fertilizer in shallow furrows around the drip line (not deep holes).',
                'Mulch with organic materials to prevent surface cracking in dry season.',
                'Avoid waterlogging — ensure proper drainage channels between rows.',
                'Split fertilizer into 2 smaller doses if water-logging is observed.',
            ],
            'Sandy Loam' => [
                'Add extra organic matter (2-3 kg compost/tree) to improve nutrient retention.',
                'Mulch heavily around the base to conserve soil moisture.',
                'Use slow-release fertilizers if available to reduce leaching.',
                'Split each application into smaller, more frequent doses (every 6 weeks).',
                'Consider foliar feeding as a supplement during critical growth stages.',
            ],
            'Loam' => [
                'Maintain organic matter through regular composting and residue retention.',
                'Standard fertilizer application practices are effective — no special adjustments.',
                'Monitor soil pH annually (target: 5.5-6.5 for coffee).',
                'Practice intercropping with leguminous shade trees (e.g., madre de cacao).',
                'Keep crop residues on the field after pruning — do not burn them.',
            ],
            default => [
                'Identify soil type using the Ribbon Test before adjusting fertilizer rates.',
                'Apply 1 kg compost per tree per application for all soil types.',
                'Mulch around the base of each tree with dried leaves or rice straw.',
                'Contact the nearest BSWM office for soil type identification assistance.',
                'Observe plant leaves for deficiency signs and adjust fertilizer accordingly.',
            ],
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  TABLE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    private function col(string $text, int $width): string
    {
        return str_pad(mb_substr($text, 0, $width), $width);
    }

    private function divider(array $widths): string
    {
        $parts = array_map(fn($w) => str_repeat('-', $w + 2), $widths);
        return '+' . implode('+', $parts) . '+';
    }

    private function row(array $cells, array $widths): string
    {
        $parts = array_map(
            fn($i) => ' ' . $this->col($cells[$i] ?? '', $widths[$i]) . ' ',
            array_keys($widths)
        );
        return '|' . implode('|', $parts) . '|';
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  REPORT BUILDER
    // ─────────────────────────────────────────────────────────────────────────

    private function buildReport(string $crop, string $soilType, string $soilKey, ?float $ph): string
    {
        $sep   = str_repeat('=', 90);
        $npk   = $this->getNpk($soilKey);
        $fv    = fn($v) => $v !== null ? $v : 'N/A';
        $phStr = $ph !== null ? $ph : 'Not tested';

        // ── Section 1: NPK Fertilizer Recommendation ───────────────────────────
        // Guide data: NPK in g/hill per application + 1 kg compost per tree
        $rationale = match ($soilKey) {
            'Clay'       => 'Higher N & K; lower P because clay fixes phosphorus.',
            'Sandy Loam' => 'Lower rates; nutrients leach easily in sandy soils.',
            'Loam'       => 'Lowest rates; loam retains nutrients efficiently.',
            default      => 'General rates for unknown soil type.',
        };

        $fertWidths = [18, 14, 14, 42];
        $fertDiv    = $this->divider($fertWidths);
        $fertHead   = $this->row(['Nutrient', 'Rate (g/hill)', 'Frequency', 'Notes'], $fertWidths);
        $schedNote  = $this->getScheduleNote($soilKey);

        $fertRows =
            $this->row(['Nitrogen (N)',    $npk['n'] . ' g/hill', 'Per application', $rationale],                                  $fertWidths) . "\n" .
            $this->row(['Phosphorus (P)',  $npk['p'] . ' g/hill', 'Per application', 'Band near root zone for better uptake.'],    $fertWidths) . "\n" .
            $this->row(['Potassium (K)',   $npk['k'] . ' g/hill', 'Per application', 'Split into 2 doses for sandy/clay soils.'], $fertWidths) . "\n" .
            $this->row(['Organic Matter',  '1 kg compost/tree',   'Per application', '1 kg well-decomposed compost or manure.'],   $fertWidths);

        $section1 = implode("\n", [$fertDiv, $fertHead, $fertDiv, $fertRows, $fertDiv]);

        // ── Section 2: NPK Summary (all soil types for reference) ─────────────
        $summWidths = [18, 12, 12, 12, 18, 18];
        $summDiv    = $this->divider($summWidths);
        $summHead   = $this->row(['Soil Type', 'N (g/hill)', 'P (g/hill)', 'K (g/hill)', 'Organic', 'Schedule'], $summWidths);

        $summRows =
            $this->row(['General/Unknown', '120', '120', '60',  '1 kg compost', 'Every 3 months'], $summWidths) . "\n" .
            $this->row(['Clay / Silty Clay','150', '50',  '150', '1 kg compost', 'Every 3 months'], $summWidths) . "\n" .
            $this->row(['Sandy Loam',       '70',  '35',  '80',  '1 kg compost', 'Every 6 weeks'],  $summWidths) . "\n" .
            $this->row(['Loam',             '65',  '30',  '65',  '1 kg compost', 'Every 3 months'], $summWidths);

        $currentRow = match ($soilKey) {
            'Clay'       => '> YOUR SOIL (Clay): N=150, P=50, K=150 g/hill — every 3 months.',
            'Sandy Loam' => '> YOUR SOIL (Sandy Loam): N=70, P=35, K=80 g/hill — every 6 weeks.',
            'Loam'       => '> YOUR SOIL (Loam): N=65, P=30, K=65 g/hill — every 3 months.',
            default      => '> YOUR SOIL (Unknown): N=120, P=120, K=60 g/hill — every 3 months.',
        };

        $section2 = implode("\n", [$summDiv, $summHead, $summDiv, $summRows, $summDiv]);

        // ── Section 3: pH Assessment ───────────────────────────────────────────
        $phWidths = [20, 16, 44];
        $phDiv    = $this->divider($phWidths);
        $phHead   = $this->row(['pH Range', 'Status', 'Farmer Action'], $phWidths);

        $phRows =
            $this->row(['< 4.5',     'Too Acidic',        'Apply agricultural lime 1-2 kg/hill. Reapply every 6-12 mo.'], $phWidths) . "\n" .
            $this->row(['4.5 - 5.5', 'Acidic',            'Apply lime 0.5-1 kg/hill + compost. Re-check after 6 months.'], $phWidths) . "\n" .
            $this->row(['5.5 - 6.5', 'Ideal for Coffee',  'No pH correction needed. Maintain with organic matter.'],       $phWidths) . "\n" .
            $this->row(['6.5 - 7.5', 'Slightly Alkaline', 'Add organic matter; use ammonium sulfate; apply ferrous sulfate.'], $phWidths) . "\n" .
            $this->row(['> 7.5',     'Too Alkaline',       'Apply elemental sulfur 0.5-1 kg/hill + heavy compost.'],       $phWidths);

        $phStatus  = $this->getPhRating($ph);
        $phEffect  = $this->getPhEffect($ph);
        $phAction  = $this->getPhAction($ph);
        $phResult  = "  Current pH   : {$phStr}\n  Status       : {$phStatus}\n  Effect       : {$phEffect}\n  Recommended  : {$phAction}";

        $section3 = implode("\n", [$phDiv, $phHead, $phDiv, $phRows, $phDiv]);

        // ── Section 4: Application Schedule ───────────────────────────────────
        $schedWidths = [26, 62];
        $schedDiv    = $this->divider($schedWidths);
        $schedHead   = $this->row(['When', 'Action / What to Do'], $schedWidths);

        $needsLime   = $ph !== null && $ph < 5.5;
        $needsSulfur = $ph !== null && $ph > 7.5;

        $schedData = [];
        if ($needsLime) {
            $schedData[] = ['IMMEDIATELY',             'Apply agricultural lime to correct acidic soil pH first.'];
            $schedData[] = ['After 2-4 weeks',         'Apply compost and NPK fertilizer after lime has activated.'];
        } elseif ($needsSulfur) {
            $schedData[] = ['IMMEDIATELY',             'Apply elemental sulfur to reduce high soil pH.'];
        }

        $schedData[] = ['Start of season',         'Apply 1 kg compost per tree around drip line.'];
        $schedData[] = [$this->getScheduleNote($soilKey) === 'Every 3 months — 4 applications per year.'
                        ? '1st Application (Month 1)'
                        : '1st Application (Week 1)',  "Apply N={$npk['n']}g + P={$npk['p']}g + K={$npk['k']}g per hill."];
        $schedData[] = [$soilKey === 'Sandy Loam'
                        ? '2nd Application (Week 7)'
                        : '2nd Application (Month 4)',  "Apply N={$npk['n']}g + P={$npk['p']}g + K={$npk['k']}g per hill."];
        $schedData[] = [$soilKey === 'Sandy Loam'
                        ? '3rd Application (Week 13)'
                        : '3rd Application (Month 7)',  'Apply same NPK rates + 1 kg compost per tree.'];
        $schedData[] = [$soilKey === 'Sandy Loam'
                        ? '4th Application (Week 19)'
                        : '4th Application (Month 10)', 'Apply same NPK rates. Observe plant response.'];
        $schedData[] = ['Every year',              'Apply compost and maintain mulch around each tree.'];
        $schedData[] = ['Annually (field check)',  'Re-assess soil with STK or pH strips. Adjust rates if needed.'];

        $schedRows = implode("\n", array_map(
            fn($r) => $this->row($r, $schedWidths),
            $schedData
        ));
        $section4 = implode("\n", [$schedDiv, $schedHead, $schedDiv, $schedRows, $schedDiv]);

        // ── Section 5: Soil Management Tips ───────────────────────────────────
        $tips = implode("\n", array_map(
            fn($t) => '  * ' . $t,
            $this->getSoilTips($soilKey)
        ));

        // ── Section 6: Good Farming Practices ─────────────────────────────────
        $practices = implode("\n", [
            '  * Apply fertilizer when soil is moist (after rain or irrigation) for better absorption.',
            '  * Always weed around the tree before applying fertilizer to reduce competition.',
            '  * Apply fertilizer in a shallow furrow or ring around the drip line — cover after.',
            '  * Mulch around the base of the tree with dried leaves or rice straw (5-10 cm thick).',
            '  * Make compost from coffee pulp, rice hull, animal manure, and kitchen waste.',
            '  * Plant leguminous shade trees (e.g., madre de cacao) as nitrogen-fixing intercrops.',
            '  * Never burn crop residues — return them to the soil as organic matter.',
        ]);

        // ── Reminders ─────────────────────────────────────────────────────────
        $reminders = implode("\n", [
            '  ! Correct soil pH FIRST — fertilizers are ineffective at wrong pH levels.',
            '  ! Sandy soils need more frequent, smaller applications to prevent leaching.',
            '  ! Clay soils need compost to improve drainage — waterlogging kills coffee roots.',
            '  ! Signs of N deficiency: yellowing of older leaves.',
            '  ! Signs of P deficiency: purplish discoloration on leaves.',
            '  ! Signs of K deficiency: browning of leaf edges (tip burn).',
            '  ! For best results, have soil tested at the nearest BSWM Regional Laboratory.',
        ]);

        // ── Assemble full report ───────────────────────────────────────────────
        return <<<REPORT
SOIL ANALYSIS RECOMMENDATION REPORT (WITHOUT LABORATORY)
Source: KAPE Program / BSWM / FAO — Non-Bearing Coffee Fertilizer Guide
{$sep}
Crop       : {$crop}
Soil Type  : {$soilType}
Stage      : Non-Bearing (Vegetative Growth Phase)
Analysis   : Without Laboratory — Based on Soil Type & Visual Assessment
{$sep}

NOTE: These are general field-based recommendations. For precision farming,
laboratory soil analysis is strongly recommended at the nearest BSWM office.
{$sep}

1. FERTILIZER RECOMMENDATION FOR YOUR SOIL TYPE ({$soilKey})
{$section1}
   Schedule : {$schedNote}
   Organic  : 1 kg well-decomposed compost or animal manure per tree per application.

2. ALL SOIL TYPES — COMPARISON REFERENCE TABLE
   (Use this to compare with neighboring farms or verify your soil type)
{$section2}
   {$currentRow}

3. pH ASSESSMENT GUIDE
{$section3}

   YOUR pH READING:
{$phResult}

4. APPLICATION SCHEDULE
{$section4}

5. SOIL MANAGEMENT TIPS (Specific to {$soilType} Soil)
{$tips}

6. GOOD FARMING PRACTICES
{$practices}

{$sep}
IMPORTANT REMINDERS
{$sep}
{$reminders}
{$sep}
REPORT;
    }
}
