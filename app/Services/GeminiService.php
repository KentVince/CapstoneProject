<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected Client $client;
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    // Faster model first; fallbacks on 429/404
    protected array $fallbackModels = [
        'gemini-2.0-flash',
        'gemini-2.5-flash',
        'gemini-2.0-flash-001',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
        $this->model  = config('services.gemini.model', 'gemini-2.0-flash');
        $this->client = new Client(['timeout' => 60]);
    }

    /**
     * Generate a pest / disease management recommendation using Gemini AI.
     */
    public function generatePestRecommendation(array $pestData): string
    {
        if (empty($this->apiKey)) {
            return 'Gemini API key is not configured. Please set GEMINI_API_KEY in your .env file.';
        }

        $prompt  = $this->buildPestPrompt($pestData);
        $payload = $this->buildPestPayload($prompt);

        $modelsToTry = array_unique(array_merge([$this->model], $this->fallbackModels));

        foreach ($modelsToTry as $model) {
            $result = $this->callApi($model, $payload);

            if ($result['success']) {
                $json = $this->parseJson($result['text']);
                if ($json) {
                    return json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                return $result['text'];
            }

            if (in_array($result['code'], [429, 404])) {
                Log::warning("GeminiService (pest): {$result['code']} on {$model}, trying next...");
                if ($result['code'] === 429) sleep(3);
                continue;
            }

            return $result['text'];
        }

        return 'All available Gemini models are currently rate-limited. Please wait a minute and try again.';
    }

    protected function buildPestPayload(string $prompt): array
    {
        return [
            'systemInstruction' => [
                'parts' => [[
                    'text' => 'You are an expert agricultural consultant specializing in Philippine coffee farm pest and disease management. '
                            . 'Output ONLY valid raw JSON — no markdown, no code fences, no extra text. '
                            . 'English only. Be concise, practical, and specific to Philippine coffee farming conditions.',
                ]],
            ],
            'contents' => [[
                'role'  => 'user',
                'parts' => [['text' => $prompt]],
            ]],
            'generationConfig' => [
                'temperature'     => 0.2,
                'maxOutputTokens' => 1600,
            ],
        ];
    }

    protected function buildPestPrompt(array $data): string
    {
        $pest     = $data['pest']     ?? 'Unknown';
        $type     = $data['type']     ?? 'Unknown';
        $severity = $data['severity'] ?? 'Unknown';
        $area     = $data['area']     ?? 'Unknown location';
        $date     = $data['date']     ?? date('Y-m-d');

        return <<<PROMPT
A coffee farm in the Philippines ({$area}) has a confirmed detection of the following:
- Pest/Disease: {$pest} ({$type})
- Severity: {$severity}
- Date Detected: {$date}

Generate a management recommendation report. Return ONLY valid JSON — no other text:
{
  "diagnosis": "2-3 sentences diagnosing the situation, mentioning the pest/disease, severity level, and expected impact on the coffee farm if untreated.",
  "urgency": "Low|Moderate|High|Critical",
  "urgency_reason": "One sentence explaining urgency level based on severity.",
  "immediate_actions": [
    "Specific action to take within 24 hours — what, how, and where on the farm",
    "Second immediate action with specific product or method if applicable",
    "Third immediate action"
  ],
  "treatment_protocol": [
    {"step": "Step 1 title", "detail": "Specific instruction including product name, rate, and timing for {$severity} severity"},
    {"step": "Step 2 title", "detail": "Specific instruction"},
    {"step": "Step 3 title", "detail": "Specific instruction"},
    {"step": "Step 4 title", "detail": "Specific instruction"}
  ],
  "schedule": [
    {"day": "Day 1", "task": "Specific task for a {$severity} {$pest} case"},
    {"day": "Day 2-3", "task": "Specific task"},
    {"day": "Day 4-5", "task": "Specific task"},
    {"day": "Day 6-7", "task": "Specific task"},
    {"day": "Week 2-4", "task": "Follow-up task"},
    {"day": "Monthly", "task": "Ongoing monitoring task"}
  ],
  "products_needed": [
    {"product": "Product name (max 30 chars)", "type": "Biological|Chemical|Organic", "rate": "Rate per ha or tree", "notes": "Safety or application notes, max 50 chars"},
    {"product": "Product 2", "type": "type", "rate": "rate", "notes": "notes"}
  ],
  "prevention": [
    "Long-term prevention measure specific to {$pest} on Philippine coffee farms",
    "Second prevention measure",
    "Third prevention measure"
  ],
  "warnings": [
    "Critical warning for {$severity} {$pest} with specific consequence if ignored",
    "Second warning or safety note"
  ]
}
PROMPT;
    }

    protected function buildPestReport(array $data, array $json): string
    {
        $pest     = $data['pest']     ?? 'Unknown';
        $type     = ucfirst($data['type'] ?? 'Unknown');
        $severity = $data['severity'] ?? 'Unknown';
        $area     = $data['area']     ?? 'Unknown location';
        $date     = $data['date']     ?? date('Y-m-d');
        $sep      = str_repeat('=', 90);
        $dash     = str_repeat('-', 90);

        // Urgency
        $urgency       = $json['urgency']        ?? 'Unknown';
        $urgencyReason = $json['urgency_reason'] ?? '';
        $diagnosis     = $json['diagnosis']      ?? '';

        // Immediate actions
        $immediateLines = implode("\n", array_map(
            fn($i, $a) => '  ' . ($i + 1) . '. ' . $a,
            array_keys($json['immediate_actions'] ?? []),
            $json['immediate_actions'] ?? []
        ));

        // Treatment protocol table
        $treatWidths = [24, 64];
        $treatDiv    = $this->divider($treatWidths);
        $treatHead   = $this->trow(['Step', 'Instructions'], $treatWidths);
        $treatRows   = implode("\n", array_map(
            fn($r) => $this->trow([$r['step'] ?? '', $r['detail'] ?? ''], $treatWidths),
            $json['treatment_protocol'] ?? []
        ));
        $treatTable = implode("\n", [$treatDiv, $treatHead, $treatDiv, $treatRows, $treatDiv]);

        // Schedule table
        $schedWidths = [14, 74];
        $schedDiv    = $this->divider($schedWidths);
        $schedHead   = $this->trow(['Timeline', 'Task / Action'], $schedWidths);
        $schedRows   = implode("\n", array_map(
            fn($r) => $this->trow([$r['day'] ?? '', $r['task'] ?? ''], $schedWidths),
            $json['schedule'] ?? []
        ));
        $schedTable = implode("\n", [$schedDiv, $schedHead, $schedDiv, $schedRows, $schedDiv]);

        // Products table
        $prodWidths = [30, 10, 18, 30];
        $prodDiv    = $this->divider($prodWidths);
        $prodHead   = $this->trow(['Product', 'Type', 'Rate', 'Notes'], $prodWidths);
        $prodRows   = implode("\n", array_map(
            fn($r) => $this->trow(
                [$r['product'] ?? '', $r['type'] ?? '', $r['rate'] ?? '', $r['notes'] ?? ''],
                $prodWidths
            ),
            $json['products_needed'] ?? []
        ));
        $prodTable = !empty($json['products_needed'])
            ? implode("\n", [$prodDiv, $prodHead, $prodDiv, $prodRows, $prodDiv])
            : '  None specified.';

        // Prevention
        $preventionText = implode("\n", array_map(
            fn($p) => '  * ' . $p,
            $json['prevention'] ?? []
        ));

        // Warnings
        $warningText = implode("\n", array_map(
            fn($w) => '  ! ' . $w,
            $json['warnings'] ?? []
        ));

        return <<<REPORT
AI-ASSISTED PEST & DISEASE MANAGEMENT RECOMMENDATION
Powered by Google Gemini AI
{$sep}
Pest / Disease : {$pest} ({$type})
Severity       : {$severity}
Location       : {$area}
Date Detected  : {$date}
Urgency Level  : {$urgency} — {$urgencyReason}
{$sep}

>> AI DIAGNOSIS
{$dash}
  {$diagnosis}
{$dash}

1. IMMEDIATE ACTIONS (Within 24 Hours)
{$immediateLines}

2. TREATMENT PROTOCOL (Severity: {$severity})
{$treatTable}

3. MANAGEMENT SCHEDULE
{$schedTable}

4. PRODUCTS / INPUTS NEEDED
{$prodTable}

5. LONG-TERM PREVENTION MEASURES
{$preventionText}

{$sep}
IMPORTANT WARNINGS
{$sep}
{$warningText}
{$sep}
  This report is AI-assisted. Always consult a licensed agriculturist for site-specific advice.
{$sep}
REPORT;
    }

    /**
     * Generate a soil analysis recommendation using Gemini AI.
     * Returns an ASCII-table report in the same format as SoilRecommendationService.
     */
    public function generateSoilRecommendation(array $soilData): string
    {
        if (empty($this->apiKey)) {
            return 'Gemini API key is not configured. Please set GEMINI_API_KEY in your .env file.';
        }

        $prompt  = $this->buildPrompt($soilData);
        $payload = $this->buildPayload($prompt);

        $modelsToTry = array_unique(array_merge([$this->model], $this->fallbackModels));

        foreach ($modelsToTry as $model) {
            $result = $this->callApi($model, $payload);

            if ($result['success']) {
                $json = $this->parseJson($result['text']);
                if ($json) {
                    return $this->buildReport($soilData, $json);
                }
                // JSON parse failed — return raw text as fallback
                return $result['text'];
            }

            if ($result['code'] === 429) {
                Log::warning("GeminiService: 429 on {$model}, trying next model...");
                sleep(3);
                continue;
            }

            if ($result['code'] === 404) {
                Log::warning("GeminiService: 404 on {$model}, trying next model...");
                continue;
            }

            return $result['text'];
        }

        return 'All available Gemini models are currently rate-limited. Please wait a minute and try again.';
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  API CALL
    // ─────────────────────────────────────────────────────────────────────────

    protected function callApi(string $model, array $payload): array
    {
        try {
            $response = $this->client->post(
                "{$this->baseUrl}/models/{$model}:generateContent?key={$this->apiKey}",
                ['json' => $payload]
            );

            $body = json_decode($response->getBody()->getContents(), true);
            $text = $body['candidates'][0]['content']['parts'][0]['text']
                ?? 'Unable to parse AI response. Please try again.';

            return ['success' => true, 'text' => $text, 'code' => 200];
        } catch (ClientException $e) {
            $code = $e->getResponse()->getStatusCode();
            Log::error("GeminiService [{$model}] HTTP {$code}: " . $e->getMessage());
            return ['success' => false, 'text' => '', 'code' => $code];
        } catch (\Exception $e) {
            Log::error("GeminiService [{$model}] error: " . $e->getMessage());
            return [
                'success' => false,
                'text'    => 'Error generating AI recommendation: ' . $e->getMessage(),
                'code'    => 500,
            ];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  PAYLOAD  — ask for compact JSON so response is small & fast
    // ─────────────────────────────────────────────────────────────────────────

    protected function buildPayload(string $prompt): array
    {
        return [
            'systemInstruction' => [
                'parts' => [[
                    'text' => 'You are an agricultural expert for Philippine coffee farms. '
                            . 'Output ONLY valid raw JSON — no markdown, no code fences, no explanation, no extra text. '
                            . 'English only. Be concise. Follow character limits strictly.',
                ]],
            ],
            'contents' => [[
                'role'  => 'user',
                'parts' => [['text' => $prompt]],
            ]],
            'generationConfig' => [
                'temperature'     => 0.1,
                'maxOutputTokens' => 1800,
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  PROMPT  — pass rating scales so Gemini rates correctly; enforce char limits
    // ─────────────────────────────────────────────────────────────────────────

    protected function buildPrompt(array $data): string
    {
        $crop = $data['crop_variety']   ?? 'Coffee';
        $soil = $data['soil_type']      ?? 'Unknown';
        $ph   = $data['ph_level']       ?? 'N/A';
        $om   = $data['organic_matter'] ?? 'N/A';
        $n    = $data['nitrogen']       ?? 'N/A';
        $p    = $data['phosphorus']     ?? 'N/A';
        $k    = $data['potassium']      ?? 'N/A';

        return <<<PROMPT
Soil test for a {$crop} farm in the Philippines:
- Soil type: {$soil}
- pH={$ph}, Organic Matter={$om}%, Nitrogen={$n}%, Phosphorus={$p}ppm, Potassium={$k}ppm

Rating scales to use:
- pH: <4.5=Very Low, 4.5-5.5=Low, 5.5-6.5=Medium, 6.5-8.5=High, >8.5=Very High
- OM(%): <=1=Very Low, <=1.7=Low, <=3=Moderate, <=5.15=High, >5.15=Very High
- N(%): <0.05=Very Low, <=0.15=Low, <=0.20=Medium, <=0.30=High, >0.30=Very High
- P(ppm): <3=Very Low, <=10=Low, <=20=Medium, <=30=High, >30=Very High
- K(ppm): <78=Very Low, <=117=Low, <=235=Medium, <=391=High, >391=Very High

Return ONLY valid JSON — no other text, no markdown. Use simple English. Obey max character lengths:
{
  "summary": "Write 2-3 sentences diagnosing the overall soil health for this specific {$crop} farm on {$soil} soil. Mention the most critical issue and its impact on yield.",
  "key_concerns": [
    "Most urgent issue with a specific reason why it matters for {$crop}",
    "Second concern with its effect on plant health",
    "Third concern or a positive observation about the soil"
  ],
  "soil_conditions": [
    {"parameter":"Soil pH","value":"{$ph}","rating":"[rate it]","remark":"[specific insight, max 50 chars]"},
    {"parameter":"Organic Matter","value":"{$om}%","rating":"[rate it]","remark":"[specific insight, max 50 chars]"},
    {"parameter":"Nitrogen (N)","value":"{$n}%","rating":"[rate it]","remark":"[specific insight, max 50 chars]"},
    {"parameter":"Phosphorus (P)","value":"{$p} ppm","rating":"[rate it]","remark":"[specific insight, max 50 chars]"},
    {"parameter":"Potassium (K)","value":"{$k} ppm","rating":"[rate it]","remark":"[specific insight, max 50 chars]"}
  ],
  "fertilizers": [
    {"nutrient":"Nitrogen (N)","product":"[product name, max 34 chars]","rate":"[exact rate, max 16 chars]","when":"[timing, max 18 chars]"},
    {"nutrient":"Phosphorus (P)","product":"[product name, max 34 chars]","rate":"[exact rate, max 16 chars]","when":"[timing, max 18 chars]"},
    {"nutrient":"Potassium (K)","product":"[product name, max 34 chars]","rate":"[exact rate, max 16 chars]","when":"[timing, max 18 chars]"}
  ],
  "amendments": [
    {"concern":"[max 20 chars]","product":"[max 32 chars]","rate":"[max 10 chars]","when":"[max 22 chars]"}
  ],
  "schedule": [
    {"when":"[max 24 chars]","action":"[specific action for this farm, max 66 chars]"},
    {"when":"[max 24 chars]","action":"[specific action for this farm, max 66 chars]"},
    {"when":"[max 24 chars]","action":"[specific action for this farm, max 66 chars]"},
    {"when":"[max 24 chars]","action":"[specific action for this farm, max 66 chars]"},
    {"when":"[max 24 chars]","action":"[specific action for this farm, max 66 chars]"},
    {"when":"[max 24 chars]","action":"[specific action for this farm, max 66 chars]"}
  ],
  "practices": [
    "Practice specific to {$crop} on {$soil} soil — how and when",
    "Practice 2 — specific and actionable",
    "Practice 3 — specific and actionable",
    "Practice 4 — specific and actionable",
    "Practice 5 — specific and actionable"
  ],
  "reminders": [
    "Critical warning specific to these soil results with explanation",
    "Reminder 2 with explanation",
    "Reminder 3 with explanation",
    "Reminder 4 with explanation"
  ]
}
PROMPT;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  JSON PARSER  — strips markdown fences if present
    // ─────────────────────────────────────────────────────────────────────────

    protected function parseJson(string $text): ?array
    {
        $text = preg_replace('/^```(?:json)?\s*/m', '', $text);
        $text = preg_replace('/\s*```$/m', '', $text);
        $text = trim($text);

        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // Try to extract a JSON object if surrounded by extra text
        if (preg_match('/\{[\s\S]+\}/s', $text, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        Log::warning('GeminiService: JSON parse failed. Raw: ' . mb_substr($text, 0, 300));
        return null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  TABLE HELPERS  (same as SoilRecommendationService)
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

    private function trow(array $cells, array $widths): string
    {
        $parts = array_map(
            fn($i) => ' ' . $this->col($cells[$i] ?? '', $widths[$i]) . ' ',
            array_keys($widths)
        );
        return '|' . implode('|', $parts) . '|';
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  REPORT BUILDER  — identical table format as SoilRecommendationService
    // ─────────────────────────────────────────────────────────────────────────

    private function buildReport(array $data, array $json): string
    {
        $crop = $data['crop_variety'] ?? 'Coffee';
        $soil = $data['soil_type']    ?? 'Unknown';
        $sep  = str_repeat('=', 90);
        $dash = str_repeat('-', 90);

        // ── AI Summary & Key Concerns (unique to AI version) ──────────────────
        $summary     = $json['summary'] ?? 'No AI summary available.';
        $keyConcerns = $json['key_concerns'] ?? [];
        $concernText = implode("\n", array_map(
            fn($i, $c) => '  ' . ($i + 1) . '. ' . $c,
            array_keys($keyConcerns),
            $keyConcerns
        ));

        // ── Section 1: Soil Condition ──────────────────────────────────────────
        $condWidths = [20, 10, 9, 52];
        $condDiv    = $this->divider($condWidths);
        $condHead   = $this->trow(['Parameter', 'Value', 'Rating', 'AI Remarks'], $condWidths);
        $condRows   = implode("\n", array_map(
            fn($r) => $this->trow(
                [$r['parameter'] ?? '', $r['value'] ?? '', $r['rating'] ?? '', $r['remark'] ?? ''],
                $condWidths
            ),
            $json['soil_conditions'] ?? []
        ));
        $section1 = implode("\n", [$condDiv, $condHead, $condDiv, $condRows, $condDiv]);

        // ── Section 2: Fertilizer ──────────────────────────────────────────────
        $fertWidths = [15, 36, 18, 20];
        $fertDiv    = $this->divider($fertWidths);
        $fertHead   = $this->trow(['Nutrient', 'Fertilizer Product', 'Rate (per ha)', 'When to Apply'], $fertWidths);
        $fertRows   = implode("\n", array_map(
            fn($r) => $this->trow(
                [$r['nutrient'] ?? '', $r['product'] ?? '', $r['rate'] ?? '', $r['when'] ?? ''],
                $fertWidths
            ),
            $json['fertilizers'] ?? []
        ));
        $section2 = implode("\n", [$fertDiv, $fertHead, $fertDiv, $fertRows, $fertDiv]);

        // ── Section 3: Soil Amendment ──────────────────────────────────────────
        $amendWidths = [22, 34, 12, 24];
        $amendDiv    = $this->divider($amendWidths);
        $amendHead   = $this->trow(['Concern', 'Product', 'Rate', 'When to Apply'], $amendWidths);
        $amendData   = !empty($json['amendments'])
            ? $json['amendments']
            : [['concern' => 'None needed', 'product' => 'Soil pH and OM are at good levels.', 'rate' => '-', 'when' => 'Continue current practices']];
        $amendRows   = implode("\n", array_map(
            fn($r) => $this->trow(
                [$r['concern'] ?? '', $r['product'] ?? '', $r['rate'] ?? '', $r['when'] ?? ''],
                $amendWidths
            ),
            $amendData
        ));
        $section3 = implode("\n", [$amendDiv, $amendHead, $amendDiv, $amendRows, $amendDiv]);

        // ── Section 4: Application Schedule ───────────────────────────────────
        $schedWidths = [26, 68];
        $schedDiv    = $this->divider($schedWidths);
        $schedHead   = $this->trow(['When', 'Action / What to Do'], $schedWidths);
        $schedRows   = implode("\n", array_map(
            fn($r) => $this->trow([$r['when'] ?? '', $r['action'] ?? ''], $schedWidths),
            $json['schedule'] ?? []
        ));
        $section4 = implode("\n", [$schedDiv, $schedHead, $schedDiv, $schedRows, $schedDiv]);

        // ── Section 5: Good Farming Practices ─────────────────────────────────
        $practices = implode("\n", array_map(
            fn($p) => '  * ' . $p,
            $json['practices'] ?? []
        ));

        // ── Reminders ─────────────────────────────────────────────────────────
        $reminders = implode("\n", array_map(
            fn($r) => '  ! ' . $r,
            $json['reminders'] ?? []
        ));

        return <<<REPORT
AI-ASSISTED SOIL ANALYSIS RECOMMENDATION REPORT
Powered by Google Gemini AI | Based on BSWM/FAO Philippine Soil Guidelines
{$sep}
Crop     : {$crop}
Soil Type: {$soil}
{$sep}

>> AI DIAGNOSIS SUMMARY
{$dash}
  {$summary}

>> KEY CONCERNS IDENTIFIED
{$dash}
{$concernText}
{$dash}

1. SOIL CONDITION ASSESSMENT
{$section1}

2. FERTILIZER RECOMMENDATION
   (Note: full application timing is in Section 4 below)
{$section2}

3. SOIL AMENDMENT
{$section3}

4. APPLICATION SCHEDULE SUMMARY
{$section4}

5. GOOD FARMING PRACTICES (AI-Recommended)
{$practices}

{$sep}
IMPORTANT REMINDERS
{$sep}
{$reminders}
{$sep}
  This report is AI-assisted. Always consult a licensed agriculturist for site-specific advice.
{$sep}
REPORT;
    }
}
