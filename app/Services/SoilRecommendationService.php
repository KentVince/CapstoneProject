<?php

namespace App\Services;

/**
 * Static soil analysis recommendation engine.
 *
 * Rating thresholds are based on:
 * - Guidelines for the Interpretation of Soils Test Results (BSWM / FAO / Landon 1991)
 * - Soil Test Interpretation and Recommendations Guide (BSWM / Oregon State / FAO)
 *
 * Potassium stored in ppm is converted from cmol/kg: 1 cmol/kg K = 391 ppm.
 */
class SoilRecommendationService
{
    // ─────────────────────────────────────────────────────────────────────────
    //  PUBLIC ENTRY POINT
    // ─────────────────────────────────────────────────────────────────────────

    public function generate(array $data): string
    {
        $crop = $data['crop_variety']   ?? 'Coffee';
        $soil = $data['soil_type']      ?? 'Unknown';
        $ph   = is_numeric($data['ph_level']       ?? null) ? (float) $data['ph_level']       : null;
        $om   = is_numeric($data['organic_matter'] ?? null) ? (float) $data['organic_matter'] : null;
        $n    = is_numeric($data['nitrogen']       ?? null) ? (float) $data['nitrogen']       : null;
        $p    = is_numeric($data['phosphorus']     ?? null) ? (float) $data['phosphorus']     : null;
        $k    = is_numeric($data['potassium']      ?? null) ? (float) $data['potassium']      : null;

        $phRating = $this->ratePh($ph);
        $omRating = $this->rateOm($om);
        $nRating  = $this->rateN($n);
        $pRating  = $this->rateP($p);
        $kRating  = $this->rateK($k);

        return $this->buildReport($crop, $soil, $ph, $om, $n, $p, $k,
                                  $phRating, $omRating, $nRating, $pRating, $kRating);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  RATING METHODS
    // ─────────────────────────────────────────────────────────────────────────

    private function ratePh(?float $v): string
    {
        if ($v === null) return 'N/A';
        if ($v < 4.5)   return 'Very Low';
        if ($v <= 5.5)  return 'Low';
        if ($v <= 6.5)  return 'Medium';
        if ($v <= 8.5)  return 'High';
        return 'Very High';
    }

    private function rateOm(?float $v): string
    {
        if ($v === null) return 'N/A';
        if ($v <= 1.00) return 'Very Low';
        if ($v <= 1.70) return 'Low';
        if ($v <= 3.00) return 'Moderate';
        if ($v <= 5.15) return 'High';
        return 'Very High';
    }

    private function rateN(?float $v): string
    {
        if ($v === null) return 'N/A';
        if ($v < 0.05)  return 'Very Low';
        if ($v <= 0.15) return 'Low';
        if ($v <= 0.20) return 'Medium';
        if ($v <= 0.30) return 'High';
        return 'Very High';
    }

    private function rateP(?float $v): string
    {
        if ($v === null) return 'N/A';
        if ($v < 3)   return 'Very Low';
        if ($v <= 10) return 'Low';
        if ($v <= 20) return 'Medium';
        if ($v <= 30) return 'High';
        return 'Very High';
    }

    private function rateK(?float $v): string
    {
        if ($v === null) return 'N/A';
        if ($v < 78)   return 'Very Low';
        if ($v <= 117) return 'Low';
        if ($v <= 235) return 'Medium';
        if ($v <= 391) return 'High';
        return 'Very High';
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  TABLE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /** Pad a string to exact width (truncates if longer) */
    private function col(string $text, int $width): string
    {
        return str_pad(mb_substr($text, 0, $width), $width);
    }

    private function divider(array $widths, string $char = '-'): string
    {
        $parts = array_map(fn($w) => str_repeat($char, $w + 2), $widths);
        return '+' . implode('+', $parts) . '+';
    }

    private function row(array $cells, array $widths): string
    {
        $parts = array_map(fn($i) => ' ' . $this->col($cells[$i] ?? '', $widths[$i]) . ' ', array_keys($widths));
        return '|' . implode('|', $parts) . '|';
    }


    // ─────────────────────────────────────────────────────────────────────────
    //  SOIL CONDITION DATA
    // ─────────────────────────────────────────────────────────────────────────

    private function phRemark(string $rating, ?float $ph): string
    {
        return match ($rating) {
            'Very Low' => 'Extremely acidic. Fix FIRST before fertilizing.',
            'Low'      => 'Strongly acidic. Apply lime before fertilizing.',
            'Medium'   => 'Good range for coffee (5.5-6.5). Maintain.',
            'High'     => ($ph !== null && $ph <= 7.2) ? 'Near neutral. Monitor closely.' : 'Slightly alkaline. Apply sulfur.',
            'Very High' => 'Strongly alkaline. Major correction needed.',
            default    => 'No data.',
        };
    }

    private function omRemark(string $rating): string
    {
        return match ($rating) {
            'Very Low' => 'Severely depleted. Apply 5-10 t/ha compost.',
            'Low'      => 'Low. Apply 3-5 t/ha compost annually.',
            'Moderate' => 'Adequate. Maintain with 2-3 t/ha compost/yr.',
            'High'     => 'Good level. Maintain current practices.',
            'Very High' => 'Excellent. Avoid excess manure/compost.',
            default    => 'No data.',
        };
    }

    private function nRemark(string $rating): string
    {
        return match ($rating) {
            'Very Low' => 'Severely deficient. Apply 90-120 kg N/ha.',
            'Low'      => 'Deficient. Apply 60-90 kg N/ha in 2 splits.',
            'Medium'   => 'Adequate. Apply 30-60 kg N/ha as needed.',
            'High'     => 'Sufficient. Reduce to 0-30 kg N/ha only.',
            'Very High' => 'Excess. No nitrogen fertilizer needed.',
            default    => 'No data.',
        };
    }

    private function pRemark(string $rating): string
    {
        return match ($rating) {
            'Very Low' => 'Severely deficient. Apply 60-90 kg P2O5/ha.',
            'Low'      => 'Deficient. Apply 40-60 kg P2O5/ha (banded).',
            'Medium'   => 'Adequate. Maintenance dose 20-40 kg P2O5/ha.',
            'High'     => 'Sufficient. Reduce to 0-20 kg P2O5/ha max.',
            'Very High' => 'Excess. Do NOT apply phosphorus fertilizer.',
            default    => 'No data.',
        };
    }

    private function kRemark(string $rating): string
    {
        return match ($rating) {
            'Very Low' => 'Severely deficient. Apply 60-90 kg K2O/ha.',
            'Low'      => 'Deficient. Apply 40-60 kg K2O/ha in 2 splits.',
            'Medium'   => 'Adequate. Maintenance dose 20-40 kg K2O/ha.',
            'High'     => 'Sufficient. Reduce to 0-20 kg K2O/ha only.',
            'Very High' => 'Excess. No potassium fertilizer needed.',
            default    => 'No data.',
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  FERTILIZER RECOMMENDATION ROWS
    // ─────────────────────────────────────────────────────────────────────────

    private function fertilizerRows(string $nRating, string $pRating, string $kRating): array
    {
        $rows = [];

        // Nitrogen
        $nData = match ($nRating) {
            'Very Low' => ['Urea (46-0-0) or Ammonium Sulfate (21-0-0)', '90-120 kg N/ha',  'Split 2x (see Sec. 4)'],
            'Low'      => ['Urea (46-0-0) or Complete Fert. (14-14-14)', '60-90 kg N/ha',   'Split 2x (see Sec. 4)'],
            'Medium'   => ['Urea (46-0-0)',                              '30-60 kg N/ha',   'As needed per growth'],
            'High'     => ['Urea (46-0-0) — reduced rate',              '0-30 kg N/ha',    'Only if signs appear'],
            'Very High' => ['None required',                             'Do not apply',    'Risk of N leaching'],
            default    => ['N/A', 'N/A', 'N/A'],
        };
        $rows[] = ['Nitrogen (N)', $nData[0], $nData[1], $nData[2]];

        // Phosphorus
        $pData = match ($pRating) {
            'Very Low' => ['Triple Superphosphate (0-46-0)',             '60-90 kg P2O5/ha', 'At planting, banded'],
            'Low'      => ['Solophos (0-18-0) or Complete (14-14-14)',  '40-60 kg P2O5/ha', 'At planting, banded'],
            'Medium'   => ['Solophos (0-18-0)',                          '20-40 kg P2O5/ha', 'At planting (maint.)'],
            'High'     => ['Reduced P — only if crop demands it',       '0-20 kg P2O5/ha',  'Optional only'],
            'Very High' => ['None required',                            'Do not apply',     'Risk of pollution'],
            default    => ['N/A', 'N/A', 'N/A'],
        };
        $rows[] = ['Phosphorus (P)', $pData[0], $pData[1], $pData[2]];

        // Potassium
        $kData = match ($kRating) {
            'Very Low' => ['Muriate of Potash (0-0-60) or SOP (0-0-50)', '60-90 kg K2O/ha', 'Split 2x (see Sec. 4)'],
            'Low'      => ['Muriate of Potash (0-0-60)',                  '40-60 kg K2O/ha', 'Split 2x (see Sec. 4)'],
            'Medium'   => ['Muriate of Potash (0-0-60)',                  '20-40 kg K2O/ha', 'At planting (maint.)'],
            'High'     => ['Muriate of Potash — reduced rate',           '0-20 kg K2O/ha',  'High-demand crops only'],
            'Very High' => ['None required',                             'Do not apply',    'Excess harms Ca & Mg'],
            default    => ['N/A', 'N/A', 'N/A'],
        };
        $rows[] = ['Potassium (K)', $kData[0], $kData[1], $kData[2]];

        return $rows;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  SOIL AMENDMENT ROWS
    // ─────────────────────────────────────────────────────────────────────────

    private function amendmentRows(string $phRating, string $omRating): array
    {
        $rows = [];

        // pH amendment
        $phAmend = match ($phRating) {
            'Very Low' => ['pH Correction (Acidic)',   'Agricultural Lime (CaCO3)',          '2-4 t/ha',   'NOW — before fertilizing'],
            'Low'      => ['pH Correction (Acidic)',   'Agricultural Lime (CaCO3)',          '1-2 t/ha',   'Before planting season'],
            'High'     => ['pH Correction (Alkaline)', 'Elemental Sulfur / Ammon. Sulfate', '0.5-1 t/ha', 'Before planting season'],
            'Very High' => ['pH Correction (Alkaline)','Elemental Sulfur + Gypsum',         '1-3 t/ha',   'Immediately — multi-season'],
            default    => null,
        };
        if ($phAmend) $rows[] = $phAmend;

        // Organic matter amendment
        $omAmend = match ($omRating) {
            'Very Low' => ['Organic Matter (Build-up)', 'Compost or Well-decomposed Manure', '5-10 t/ha', 'Start of season + biochar'],
            'Low'      => ['Organic Matter (Build-up)', 'Compost or Organic Amendments',     '3-5 t/ha',  'Annually, every season'],
            'Moderate' => ['Organic Matter (Maintain)', 'Compost',                           '2-3 t/ha',  'Annually to maintain'],
            default    => null,
        };
        if ($omAmend) $rows[] = $omAmend;

        if (empty($rows)) {
            $rows[] = ['None needed', 'Soil pH and OM are at good levels.', '-', 'Continue current management practices.'];
        }

        return $rows;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  APPLICATION SCHEDULE
    // ─────────────────────────────────────────────────────────────────────────

    private function scheduleRows(string $phRating, string $omRating,
                                  string $nRating, string $pRating, string $kRating): array
    {
        $rows = [];
        $needsLime    = in_array($phRating, ['Very Low', 'Low']);
        $needsSulfur  = in_array($phRating, ['High', 'Very High']);
        $needsCompost = in_array($omRating, ['Very Low', 'Low', 'Moderate']);
        $needsN       = in_array($nRating,  ['Very Low', 'Low', 'Medium']);
        $needsP       = in_array($pRating,  ['Very Low', 'Low', 'Medium']);
        $needsK       = in_array($kRating,  ['Very Low', 'Low', 'Medium']);

        if ($needsLime) {
            $rows[] = ['IMMEDIATELY', 'Apply Agricultural Lime to correct acidic soil pH.'];
            $rows[] = ['After 2-4 weeks', 'Apply compost and fertilizers (after lime has worked).'];
        } elseif ($needsSulfur) {
            $rows[] = ['IMMEDIATELY', 'Apply Elemental Sulfur to lower high soil pH.'];
        }

        if ($needsCompost) {
            $rows[] = ['Every planting season', 'Apply compost or organic matter to build soil health.'];
        }

        if ($needsP) {
            $rows[] = ['At planting', 'Apply phosphorus fertilizer near root zone (banded).'];
        }

        if ($needsN || $needsK) {
            $apps = [];
            if ($needsN) $apps[] = '50% of nitrogen';
            if ($needsK) $apps[] = '50% of potassium';
            $rows[] = ['At planting (basal)', 'Apply ' . implode(' + ', $apps) . ' fertilizer.'];
        }

        if ($needsN || $needsK) {
            $apps = [];
            if ($needsN) $apps[] = 'remaining 50% nitrogen';
            if ($needsK) $apps[] = 'remaining 50% potassium';
            $rows[] = ['1-2 months after planting', 'Apply ' . implode(' + ', $apps) . ' (top-dress).'];
        }

        if (!$needsLime && !$needsSulfur && !$needsN && !$needsP && !$needsK) {
            $rows[] = ['Every planting season', 'Apply maintenance fertilizer based on crop needs.'];
        }

        $rows[] = ['Every year', 'Apply organic matter (compost/manure) to maintain soil health.'];
        $rows[] = ['Every 6-12 months', 'Re-test soil to evaluate progress and update fertilizer plan.'];

        return $rows;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  REPORT BUILDER
    // ─────────────────────────────────────────────────────────────────────────

    private function buildReport(
        string $crop, string $soil,
        ?float $ph, ?float $om, ?float $n, ?float $p, ?float $k,
        string $phRating, string $omRating, string $nRating, string $pRating, string $kRating
    ): string {

        $fv = fn($v, $unit = '') => $v !== null ? $v . $unit : 'N/A';

        // ── Section 1: Soil Condition ──────────────────────────────────────────
        $condWidths = [20, 10, 9, 52];
        $condDiv    = $this->divider($condWidths);
        $condHead   = $this->row(['Parameter', 'Value', 'Rating', 'Remarks'], $condWidths);

        $condRows =
            $this->row(['Soil pH',         $fv($ph),       $phRating, $this->phRemark($phRating, $ph)], $condWidths) . "\n" .
            $this->row(['Organic Matter',  $fv($om, '%'),  $omRating, $this->omRemark($omRating)],       $condWidths) . "\n" .
            $this->row(['Nitrogen (N)',     $fv($n, '%'),   $nRating,  $this->nRemark($nRating)],         $condWidths) . "\n" .
            $this->row(['Phosphorus (P)',   $fv($p, ' ppm'),$pRating, $this->pRemark($pRating)],         $condWidths) . "\n" .
            $this->row(['Potassium (K)',    $fv($k, ' ppm'),$kRating, $this->kRemark($kRating)],         $condWidths);

        $section1 = implode("\n", [
            $condDiv, $condHead, $condDiv, $condRows, $condDiv,
        ]);

        // ── Section 2: Fertilizer Recommendation ──────────────────────────────
        $fertWidths = [15, 36, 18, 20];
        $fertDiv    = $this->divider($fertWidths);
        $fertHead   = $this->row(['Nutrient', 'Fertilizer Product', 'Rate (per ha)', 'When to Apply'], $fertWidths);

        $fertRowsData = $this->fertilizerRows($nRating, $pRating, $kRating);
        $fertRows = implode("\n", array_map(
            fn($r) => $this->row($r, $fertWidths),
            $fertRowsData
        ));

        $section2 = implode("\n", [
            $fertDiv, $fertHead, $fertDiv, $fertRows, $fertDiv,
        ]);

        // Note: "When to Apply" is abbreviated in the table; full schedule is in Section 4

        // ── Section 3: Soil Amendment ──────────────────────────────────────────
        $amendWidths = [22, 34, 12, 24];
        $amendDiv    = $this->divider($amendWidths);
        $amendHead   = $this->row(['Concern', 'Product', 'Rate', 'When to Apply'], $amendWidths);

        $amendRowsData = $this->amendmentRows($phRating, $omRating);
        $amendRows = implode("\n", array_map(
            fn($r) => $this->row($r, $amendWidths),
            $amendRowsData
        ));

        $section3 = implode("\n", [
            $amendDiv, $amendHead, $amendDiv, $amendRows, $amendDiv,
        ]);

        // ── Section 4: Application Schedule ───────────────────────────────────
        $schedWidths = [26, 68];
        $schedDiv    = $this->divider($schedWidths);
        $schedHead   = $this->row(['When', 'Action / What to Do'], $schedWidths);

        $schedRowsData = $this->scheduleRows($phRating, $omRating, $nRating, $pRating, $kRating);
        $schedRows = implode("\n", array_map(
            fn($r) => $this->row($r, $schedWidths),
            $schedRowsData
        ));

        $section4 = implode("\n", [
            $schedDiv, $schedHead, $schedDiv, $schedRows, $schedDiv,
        ]);

        // ── Section 5: Good Farming Practices ─────────────────────────────────
        $practices = implode("\n", [
            '  * Mulch around coffee plants with dried leaves or rice straw (5-10 cm thick).',
            '  * Keep crop residues on the field after harvest -- do NOT burn them.',
            '  * Plant legume cover crops during fallow season to naturally add nitrogen.',
            '  * Avoid deep plowing to protect soil structure and organic matter.',
            '  * Never apply fertilizer on extremely dry or waterlogged soil.',
            '  * Always wash hands after handling fertilizers and chemicals.',
        ]);

        // ── Reminders ─────────────────────────────────────────────────────────
        $reminders = implode("\n", [
            '  ! ALWAYS correct soil pH FIRST -- fertilizers do not work on wrong pH.',
            '  ! SPLIT nitrogen and potassium -- never apply the full dose at once.',
            '  ! RE-TEST your soil every 6-12 months after major amendments.',
            '  ! BANDED application of phosphorus near roots is more efficient than broadcasting.',
        ]);

        // ── Assemble full report ───────────────────────────────────────────────
        $sep = str_repeat('=', 90);

        return <<<REPORT
SOIL ANALYSIS RECOMMENDATION REPORT
Source: Bureau of Soils & Water Management (BSWM) / FAO Soil Interpretation Guidelines
{$sep}
Crop     : {$crop}
Soil Type: {$soil}
{$sep}

1. SOIL CONDITION ASSESSMENT
{$section1}

2. FERTILIZER RECOMMENDATION
   (Note: full application timing is in Section 4 below)
{$section2}

3. SOIL AMENDMENT
{$section3}

4. APPLICATION SCHEDULE SUMMARY
{$section4}

5. GOOD FARMING PRACTICES
{$practices}

{$sep}
IMPORTANT REMINDERS
{$sep}
{$reminders}
{$sep}
REPORT;
    }
}
