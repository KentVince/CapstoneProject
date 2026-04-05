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
                // Gemini responded but JSON parsing failed — wrap in minimal valid JSON
                // so the blade view can always display something meaningful.
                return json_encode([
                    'farmer_summary'     => $result['text'],
                    'description'        => '',
                    'symptoms'           => [],
                    'causes'             => [],
                    'treatment_protocol' => [],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            if (in_array($result['code'], [429, 404])) {
                Log::warning("GeminiService (pest): {$result['code']} on {$model}, trying next...");
                if ($result['code'] === 429) sleep(3);
                continue;
            }

            return json_encode([
                'farmer_summary'     => $result['text'],
                'description'        => '',
                'symptoms'           => [],
                'causes'             => [],
                'treatment_protocol' => [],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return json_encode([
            'farmer_summary'     => 'All available Gemini models are currently rate-limited. Please wait a minute and try again.',
            'description'        => '',
            'symptoms'           => [],
            'causes'             => [],
            'treatment_protocol' => [],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Generate structured AI fields saved as separate DB columns.
     * Returns an array with keys: description, symptoms, causes, impact,
     * action_plan, immediate_response, long_term_strategy.
     */
    public function generatePestFields(array $pestData): array
    {
        $empty = [
            'description'        => '',
            'symptoms'           => [],
            'causes'             => [],
            'impact'             => '',
            'action_plan'        => [],
            'immediate_response' => [],
            'long_term_strategy' => [],
        ];

        if (empty($this->apiKey)) {
            return $empty;
        }

        $prompt  = $this->buildPestFieldsPrompt($pestData);
        $payload = $this->buildPestPayload($prompt);

        $modelsToTry = array_unique(array_merge([$this->model], $this->fallbackModels));

        foreach ($modelsToTry as $model) {
            $result = $this->callApi($model, $payload);

            if ($result['success']) {
                $json = $this->parseJson($result['text']);
                if ($json && is_array($json)) {
                    return [
                        'description'        => $json['description']        ?? '',
                        'symptoms'           => $json['symptoms']           ?? [],
                        'causes'             => $json['causes']             ?? [],
                        'impact'             => $json['impact']             ?? '',
                        'action_plan'        => $json['action_plan']        ?? [],
                        'immediate_response' => $json['immediate_response'] ?? [],
                        'long_term_strategy' => $json['long_term_strategy'] ?? [],
                    ];
                }
                // Parse failed — put raw text in impact so something shows
                return array_merge($empty, ['impact' => $result['text']]);
            }

            if (in_array($result['code'], [429, 404])) {
                Log::warning("GeminiService (fields): {$result['code']} on {$model}, trying next...");
                if ($result['code'] === 429) sleep(3);
                continue;
            }

            return array_merge($empty, ['impact' => $result['text']]);
        }

        return array_merge($empty, [
            'impact' => 'All available Gemini models are currently rate-limited. Please wait a minute and try again.',
        ]);
    }

    protected function buildPestFieldsPrompt(array $data): string
    {
        $pest     = $data['pest']     ?? 'Unknown';
        $type     = $data['type']     ?? 'Unknown';
        $severity = $data['severity'] ?? 'Unknown';
        $area     = $data['area']     ?? 'Unknown location';
        $date     = $data['date']     ?? date('Y-m-d');
        $conf     = isset($data['confidence']) ? $data['confidence'] . '%' : 'N/A';

        return <<<PROMPT
A Philippine coffee farmer detected the following pest or disease. Generate a structured management guide.

Detection:
- Pest / Disease: {$pest} ({$type})
- Severity: {$severity}
- Farm Location: {$area}
- Date Detected: {$date}
- Detection Confidence: {$conf}

Return ONLY valid raw JSON with exactly these 7 keys — no markdown, no extra text:
{
  "description": "2-3 sentences describing what {$pest} is: its nature (insect/fungus/bacteria/virus), how it attacks Philippine coffee plants, and its economic importance to Filipino farmers.",
  "symptoms": [
    "Visible symptom 1 — specific part of the plant affected and what it looks like",
    "Visible symptom 2",
    "Visible symptom 3",
    "Visible symptom 4"
  ],
  "causes": [
    "Primary cause or pathogen (scientific name if applicable)",
    "Environmental condition that triggers or worsens the problem",
    "Farm management factor that contributes to infestation"
  ],
  "impact": "2-3 sentences explaining the current impact of {$severity} severity {$pest} on the coffee plants, what will happen if left untreated, and the potential crop and economic loss for the farmer.",
  "action_plan": [
    "Action 1 — something the farmer can do immediately within the next few hours using items already on the farm",
    "Action 2 — first product or home remedy to buy or prepare, with how to apply it",
    "Action 3 — how to isolate or protect unaffected coffee trees"
  ],
  "immediate_response": [
    "Step 1: Specific treatment step with product name, application rate, and method. Give both a chemical and an organic option where possible.",
    "Step 2: Follow-up treatment or monitoring step",
    "Step 3: Sanitation or cultural practice to stop spread",
    "Step 4: Safety precaution when applying treatment"
  ],
  "long_term_strategy": [
    "Long-term cultural practice to prevent {$pest} recurrence on Philippine coffee farms — specific and seasonal",
    "Monitoring schedule or early warning sign to watch for each season",
    "Companion crop, pruning, or soil health practice to build farm resilience",
    "When to contact the Municipal Agricultural Officer or DA extension worker"
  ]
}
PROMPT;
    }

    protected function buildPestPayload(string $prompt): array
    {
        return [
            'systemInstruction' => [
                'parts' => [[
                    'text' => 'You are an expert agricultural consultant specializing in Philippine coffee farm pest and disease management. '
                            . 'Output ONLY valid raw JSON — no markdown, no code fences, no extra text. '
                            . 'English only. Be practical, farmer-friendly, and specific to Philippine coffee farming conditions. '
                            . 'Prioritize affordable and locally available solutions.',
                ]],
            ],
            'contents' => [[
                'role'  => 'user',
                'parts' => [['text' => $prompt]],
            ]],
            'generationConfig' => [
                'temperature'     => 0.2,
                'maxOutputTokens' => 2400,
            ],
        ];
    }

    protected function buildPestPrompt(array $data): string
    {
        $pest       = $data['pest']       ?? 'Unknown';
        $type       = $data['type']       ?? 'Unknown';
        $severity   = $data['severity']   ?? 'Unknown';
        $area       = $data['area']       ?? 'Unknown location';
        $date       = $data['date']       ?? date('Y-m-d');
        $confidence = isset($data['confidence']) ? $data['confidence'] . '%' : null;

        $confidenceLine = $confidence
            ? "\n- Detection Confidence: {$confidence}" . ($data['confidence'] < 70 ? ' (Low — verify identity before treating)' : '')
            : '';

        return <<<PROMPT
A Philippine coffee farmer needs a management recommendation for the following detection:

DETECTION DETAILS:
- Pest / Disease: {$pest} ({$type})
- Severity: {$severity}
- Farm Location: {$area}
- Date Detected: {$date}{$confidenceLine}

Write a comprehensive, farmer-friendly recommendation. The farmer may have limited access to agricultural stores and limited budget. Consider:
1. What the farmer can do immediately with what they already have on the farm
2. Locally available and affordable treatments in the Philippines (DA-accredited, agri-vet stores)
3. Both chemical AND organic/natural alternatives
4. Clear signs that the farmer must escalate to an Agricultural Extension Officer (AEO) or DA/DAR
5. Safety precautions when handling chemicals or biological agents
6. Long-term farm management to prevent recurrence

Return ONLY valid raw JSON — no markdown, no extra text:
{
  "description": "2-3 sentences describing what {$pest} is — its nature (insect/fungus/bacteria), how it attacks Philippine coffee plants, and its general economic importance.",
  "symptoms": [
    "Visible symptom 1 observable on the coffee plant — be specific (which part of the plant, what it looks like)",
    "Visible symptom 2",
    "Visible symptom 3",
    "Visible symptom 4"
  ],
  "causes": [
    "Primary cause or pathogen responsible for {$pest} — scientific name if applicable",
    "Environmental or cultural condition that triggers or worsens the infestation",
    "Secondary contributing factor (e.g. farm management practice, weather, soil)"
  ],
  "diagnosis": "2-3 sentences: what is happening, the severity level's impact on the coffee plants, and what will occur if untreated. Be specific to {$pest} on Philippine coffee.",
  "urgency": "Low|Moderate|High|Critical",
  "urgency_reason": "One sentence explaining the urgency level based on the {$severity} severity of {$pest}.",
  "farmer_summary": "2-3 plain-language sentences the farmer can immediately understand — what they are dealing with, how serious it is, and the single most important thing to do right now.",
  "immediate_actions": [
    "Action 1: Something the farmer can do within the next few hours using items already on the farm (no purchase needed)",
    "Action 2: First thing to buy or prepare — name a specific product or home remedy with how to apply it",
    "Action 3: How to isolate or protect unaffected coffee trees — specific field instruction"
  ],
  "treatment_protocol": [
    {"step": "Step title", "detail": "Specific instruction with product name, application rate, and method. Where possible, give both a chemical and an organic option."},
    {"step": "Step title", "detail": "Specific instruction"},
    {"step": "Step title", "detail": "Specific instruction"},
    {"step": "Step title", "detail": "Specific instruction"}
  ],
  "organic_alternatives": [
    "Natural remedy 1 using locally available Philippine materials — how to prepare and how to apply",
    "Natural remedy 2 — specific and actionable",
    "Natural remedy 3 — specific and actionable"
  ],
  "schedule": [
    {"day": "Day 1", "task": "Specific task for a {$severity} {$pest} case"},
    {"day": "Day 2-3", "task": "Specific task"},
    {"day": "Day 4-7", "task": "Specific task"},
    {"day": "Week 2", "task": "Follow-up task"},
    {"day": "Week 3-4", "task": "Monitoring or re-treatment task"},
    {"day": "Monthly", "task": "Ongoing prevention and monitoring"}
  ],
  "products_needed": [
    {"product": "Product name (max 28 chars)", "type": "Chemical|Biological|Organic", "rate": "Rate per ha or tree", "price_range": "~PHP XXX-XXX", "where_to_buy": "e.g. agri-vet store, DA office, online"},
    {"product": "Product 2", "type": "type", "rate": "rate", "price_range": "price", "where_to_buy": "source"}
  ],
  "safety_precautions": [
    "Safety measure 1 when handling or applying the treatment — specific PPE or timing",
    "Safety measure 2 — re-entry intervals or storage instructions",
    "Safety measure 3 — disposal of containers or contaminated materials"
  ],
  "when_to_call_expert": "Specific observable conditions that mean the farmer MUST immediately contact their Municipal Agricultural Officer, AEO, or DA/DAR — be concrete about visible signs or thresholds.",
  "prevention": [
    "Long-term cultural practice to prevent {$pest} recurrence on Philippine coffee farms — specific and seasonal",
    "Second prevention measure — cover or companion cropping, pruning, or sanitation practice",
    "Third prevention measure — monitoring schedule or early warning sign to watch for"
  ],
  "warnings": [
    "Critical warning — specific consequence of ignoring {$severity} {$pest} on the farm",
    "Safety or compliance warning relevant to the recommended treatments"
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

        // Urgency & Diagnosis
        $urgency         = $json['urgency']           ?? 'Unknown';
        $urgencyReason   = $json['urgency_reason']    ?? '';
        $diagnosis       = $json['diagnosis']         ?? '';
        $farmerSummary   = $json['farmer_summary']    ?? '';
        $whenToCallExpert = $json['when_to_call_expert'] ?? '';

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

        // Organic alternatives
        $organicLines = implode("\n", array_map(
            fn($i, $a) => '  ' . ($i + 1) . '. ' . $a,
            array_keys($json['organic_alternatives'] ?? []),
            $json['organic_alternatives'] ?? []
        ));

        // Schedule table
        $schedWidths = [14, 74];
        $schedDiv    = $this->divider($schedWidths);
        $schedHead   = $this->trow(['Timeline', 'Task / Action'], $schedWidths);
        $schedRows   = implode("\n", array_map(
            fn($r) => $this->trow([$r['day'] ?? '', $r['task'] ?? ''], $schedWidths),
            $json['schedule'] ?? []
        ));
        $schedTable = implode("\n", [$schedDiv, $schedHead, $schedDiv, $schedRows, $schedDiv]);

        // Products table (with price_range and where_to_buy)
        $prodWidths = [28, 10, 16, 14, 18];
        $prodDiv    = $this->divider($prodWidths);
        $prodHead   = $this->trow(['Product', 'Type', 'Rate', 'Price (PHP)', 'Where to Buy'], $prodWidths);
        $prodRows   = implode("\n", array_map(
            fn($r) => $this->trow(
                [$r['product'] ?? '', $r['type'] ?? '', $r['rate'] ?? '', $r['price_range'] ?? '—', $r['where_to_buy'] ?? '—'],
                $prodWidths
            ),
            $json['products_needed'] ?? []
        ));
        $prodTable = !empty($json['products_needed'])
            ? implode("\n", [$prodDiv, $prodHead, $prodDiv, $prodRows, $prodDiv])
            : '  None specified.';

        // Safety precautions
        $safetyLines = implode("\n", array_map(
            fn($i, $s) => '  ' . ($i + 1) . '. ' . $s,
            array_keys($json['safety_precautions'] ?? []),
            $json['safety_precautions'] ?? []
        ));

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

        $farmerSummaryBlock = $farmerSummary
            ? "\n>> FARMER SUMMARY (Plain Language)\n{$dash}\n  {$farmerSummary}\n{$dash}\n"
            : '';

        $organicSection = $organicLines
            ? "\n3b. ORGANIC / NATURAL ALTERNATIVES\n{$organicLines}\n"
            : '';

        $safetySection = $safetyLines
            ? "\n5b. SAFETY PRECAUTIONS\n{$safetyLines}\n"
            : '';

        $expertCallSection = $whenToCallExpert
            ? "\n>> WHEN TO CALL AN EXPERT\n{$dash}\n  {$whenToCallExpert}\n{$dash}\n"
            : '';

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
{$farmerSummaryBlock}
1. IMMEDIATE ACTIONS (Within 24 Hours)
{$immediateLines}

2. TREATMENT PROTOCOL (Severity: {$severity})
{$treatTable}
{$organicSection}
3. MANAGEMENT SCHEDULE
{$schedTable}

4. PRODUCTS / INPUTS NEEDED
{$prodTable}
{$safetySection}
5. LONG-TERM PREVENTION MEASURES
{$preventionText}
{$expertCallSection}
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
     *
     * Strategy:
     *  - PHP pre-calculates all ratings and builds all structured ASCII tables
     *    (same logic as SoilRecommendationService, so tables are always accurate).
     *  - Gemini (4 000 tokens) writes ONLY the qualitative interpretation:
     *    diagnosis, farmer summary, key concerns, priority actions, remarks per
     *    parameter, organic alternatives, good practices, monitoring plan,
     *    expected outcomes, and important reminders.
     *  - The two parts are merged into one comprehensive report.
     */
    public function generateSoilRecommendation(array $soilData): string
    {
        if (empty($this->apiKey)) {
            return 'Gemini API key is not configured. Please set GEMINI_API_KEY in your .env file.';
        }

        // ── 1. Pre-calculate ratings in PHP ───────────────────────────────────
        $ph = is_numeric($soilData['ph_level']       ?? null) ? (float) $soilData['ph_level']       : null;
        $om = is_numeric($soilData['organic_matter'] ?? null) ? (float) $soilData['organic_matter'] : null;
        $n  = is_numeric($soilData['nitrogen']       ?? null) ? (float) $soilData['nitrogen']       : null;
        $p  = is_numeric($soilData['phosphorus']     ?? null) ? (float) $soilData['phosphorus']     : null;
        $k  = is_numeric($soilData['potassium']      ?? null) ? (float) $soilData['potassium']      : null;

        $ratings = [
            'ph' => $this->rateSoilPh($ph),
            'om' => $this->rateSoilOm($om),
            'n'  => $this->rateSoilN($n),
            'p'  => $this->rateSoilP($p),
            'k'  => $this->rateSoilK($k),
        ];

        $nums = compact('ph', 'om', 'n', 'p', 'k');

        // ── 2. Ask Gemini only for qualitative interpretation ──────────────────
        $prompt  = $this->buildPrompt($soilData, $ratings, $nums);
        $payload = $this->buildPayload($prompt);

        $modelsToTry = array_unique(array_merge([$this->model], $this->fallbackModels));

        foreach ($modelsToTry as $model) {
            $result = $this->callApi($model, $payload);

            if ($result['success']) {
                $json = $this->parseJson($result['text']);
                if ($json) {
                    return $this->buildReport($soilData, $nums, $ratings, $json);
                }
                return $result['text'];
            }

            if (in_array($result['code'], [429, 404])) {
                Log::warning("GeminiService (soil): {$result['code']} on {$model}, trying next...");
                if ($result['code'] === 429) sleep(3);
                continue;
            }

            return $result['text'];
        }

        return 'All available Gemini models are currently rate-limited. Please wait a minute and try again.';
    }


    public function generateSoilFields(array $soilData): array
    {
        $empty = [
            'diagnosis'             => '',
            'farmer_summary'        => '',
            'key_concerns'          => [],
            'priority_actions'      => [],
            'soil_remarks'          => [],
            'organic_alternatives'  => [],
            'practices'             => [],
            'monitoring_plan'       => [],
            'expected_outcomes'     => '',
            'reminders'             => [],
        ];

        if (empty($this->apiKey)) {
            return $empty;
        }

        // Pre-calculate ratings same as generateSoilRecommendation
        $ph = is_numeric($soilData['ph_level']       ?? null) ? (float) $soilData['ph_level']       : null;
        $om = is_numeric($soilData['organic_matter'] ?? null) ? (float) $soilData['organic_matter'] : null;
        $n  = is_numeric($soilData['nitrogen']       ?? null) ? (float) $soilData['nitrogen']       : null;
        $p  = is_numeric($soilData['phosphorus']     ?? null) ? (float) $soilData['phosphorus']     : null;
        $k  = is_numeric($soilData['potassium']      ?? null) ? (float) $soilData['potassium']      : null;

        $ratings = [
            'ph' => $this->rateSoilPh($ph),
            'om' => $this->rateSoilOm($om),
            'n'  => $this->rateSoilN($n),
            'p'  => $this->rateSoilP($p),
            'k'  => $this->rateSoilK($k),
        ];
        $nums = compact('ph', 'om', 'n', 'p', 'k');

        $prompt  = $this->buildPrompt($soilData, $ratings, $nums);
        $payload = $this->buildPayload($prompt);

        $modelsToTry = array_unique(array_merge([$this->model], $this->fallbackModels));

        foreach ($modelsToTry as $model) {
            $result = $this->callApi($model, $payload);

            if ($result['success']) {
                $json = $this->parseJson($result['text']);
                if ($json && is_array($json)) {
                    return [
                        'diagnosis'            => $json['diagnosis']            ?? '',
                        'farmer_summary'       => $json['farmer_summary']       ?? '',
                        'key_concerns'         => $json['key_concerns']         ?? [],
                        'priority_actions'     => $json['priority_actions']     ?? [],
                        'soil_remarks'         => $json['soil_remarks']         ?? [],
                        'organic_alternatives' => $json['organic_alternatives'] ?? [],
                        'practices'            => $json['practices']            ?? [],
                        'monitoring_plan'      => $json['monitoring_plan']      ?? [],
                        'expected_outcomes'    => $json['expected_outcomes']    ?? '',
                        'reminders'            => $json['reminders']            ?? [],
                    ];
                }
                // Last resort: extract individual scalar fields via regex
                $raw = $result['text'];
                $extracted = $empty;
                foreach (['diagnosis', 'farmer_summary', 'expected_outcomes'] as $key) {
                    if (preg_match('/"' . $key . '"\s*:\s*"((?:[^"\\\n]|\\.)*)"/s', $raw, $m)) {
                        $extracted[$key] = stripslashes($m[1]);
                    }
                }
                foreach (['key_concerns', 'priority_actions', 'organic_alternatives', 'practices', 'monitoring_plan', 'reminders'] as $key) {
                    if (preg_match('/"' . $key . '"\s*:\s*(\[[\s\S]*?\])/s', $raw, $m)) {
                        $arr = json_decode($m[1], true, 512, JSON_INVALID_UTF8_IGNORE);
                        if (is_array($arr)) $extracted[$key] = $arr;
                    }
                }
                if (preg_match('/"soil_remarks"\s*:\s*(\{[\s\S]*?\})/s', $raw, $m)) {
                    $obj = json_decode($m[1], true, 512, JSON_INVALID_UTF8_IGNORE);
                    if (is_array($obj)) $extracted['soil_remarks'] = $obj;
                }
                return $extracted;
            }

            if (in_array($result['code'], [429, 404])) {
                Log::warning("GeminiService (soilFields): {$result['code']} on {$model}, trying next...");
                if ($result['code'] === 429) sleep(3);
                continue;
            }

            return array_merge($empty, ['diagnosis' => $result['text']]);
        }

        return array_merge($empty, [
            'diagnosis' => 'All available Gemini models are currently rate-limited. Please wait a minute and try again.',
        ]);
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
                    'text' => 'You are a senior agricultural scientist specializing in Philippine coffee farm soil management. '
                            . 'Output ONLY valid raw JSON — no markdown, no code fences, no explanation, no extra text. '
                            . 'English only. Write detailed, comprehensive, and farmer-friendly content. '
                            . 'Reference locally available fertilizers and materials in the Philippines. '
                            . 'All ratings are already pre-calculated by the system — use them exactly as given.',
                ]],
            ],
            'contents' => [[
                'role'  => 'user',
                'parts' => [['text' => $prompt]],
            ]],
            'generationConfig' => [
                'temperature'     => 0.15,
                'maxOutputTokens' => 6000,
            ],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  PROMPT  — Ratings pre-calculated in PHP; Gemini writes qualitative text ONLY
    // ─────────────────────────────────────────────────────────────────────────

    protected function buildPrompt(array $data, array $ratings = [], array $nums = []): string
    {
        $crop         = $data['crop_variety']   ?? 'Coffee';
        $soil         = $data['soil_type']      ?? 'Unknown';
        $ph           = $nums['ph'] ?? ($data['ph_level']       ?? 'N/A');
        $om           = $nums['om'] ?? ($data['organic_matter'] ?? 'N/A');
        $n            = $nums['n']  ?? ($data['nitrogen']       ?? 'N/A');
        $p            = $nums['p']  ?? ($data['phosphorus']     ?? 'N/A');
        $k            = $nums['k']  ?? ($data['potassium']      ?? 'N/A');
        $farmName     = $data['farm_name']      ?? '';
        $location     = $data['location']       ?? '';
        $analysisType = $data['analysis_type']  ?? 'with_lab';

        $contextParts = array_filter([$farmName, $location]);
        $contextLine  = $contextParts ? ' — ' . implode(', ', $contextParts) : '';
        $typeLabel    = $analysisType === 'without_lab' ? 'Field Assessment (No Laboratory)' : 'Laboratory Analysis';

        $phRating = $ratings['ph'] ?? 'N/A';
        $omRating = $ratings['om'] ?? 'N/A';
        $nRating  = $ratings['n']  ?? 'N/A';
        $pRating  = $ratings['p']  ?? 'N/A';
        $kRating  = $ratings['k']  ?? 'N/A';

        $phVal = $ph  !== null ? $ph  : 'N/A';
        $omVal = $om  !== null ? $om  : 'N/A';
        $nVal  = $n   !== null ? $n   : 'N/A';
        $pVal  = $p   !== null ? $p   : 'N/A';
        $kVal  = $k   !== null ? $k   : 'N/A';

        return <<<PROMPT
You are preparing a comprehensive SOIL ANALYSIS RECOMMENDATION REPORT for a Philippine {$crop} farm{$contextLine}.

SOIL TEST RESULTS (ratings already calculated — use them exactly):
  Analysis Type : {$typeLabel}
  Soil Type     : {$soil}
  pH            : {$phVal}   → {$phRating}   (optimal for coffee: 5.5–6.5)
  Organic Matter: {$omVal}%  → {$omRating}
  Nitrogen (N)  : {$nVal}%   → {$nRating}
  Phosphorus (P): {$pVal} ppm → {$pRating}
  Potassium (K) : {$kVal} ppm → {$kRating}

The structured tables (soil condition table, fertilizer table, amendment table, application schedule) will be generated automatically by the system. Your task is ONLY to write the qualitative interpretation and recommendations in the JSON fields below.

Write detailed, comprehensive content for every field. Use Philippine DA-registered fertilizers. Include organic/low-cost alternatives. Align timing with Philippine coffee seasons (dry: Nov–Apr, wet: May–Oct). Do NOT truncate any field — write full, complete sentences.

Return ONLY valid raw JSON:
{
  "diagnosis": "Write 4-5 detailed sentences that: (1) summarize the overall soil condition for this {$crop} farm on {$soil} soil, (2) identify the most critical deficiency or imbalance and explain its impact on coffee growth and yield, (3) describe how the identified deficiencies interact with each other (e.g., low pH limiting nutrient uptake), (4) mention any parameter that is adequate or positive, and (5) state the overall prognosis if left untreated versus if treated promptly.",

  "farmer_summary": "Write 3 clear sentences in simple language a Filipino farmer can immediately understand: (1) what their biggest soil problem is and what it looks like in the field, (2) exactly why it matters for their coffee harvest and income, (3) the single most important action they should take this week.",

  "key_concerns": [
    "Concern 1: Write 2 full sentences — identify the most critical parameter issue ({$phRating} pH / {$nRating} N / {$pRating} P / {$kRating} K / {$omRating} OM — pick the worst) and explain its specific visible effect on coffee plants if nothing is done.",
    "Concern 2: Write 2 full sentences about the second most critical issue and its effect on coffee flowering, cherry development, or yield quality.",
    "Concern 3: Either a third concern (2 sentences) or describe a parameter that is satisfactory and advise how to maintain it going forward."
  ],

  "priority_actions": [
    "Priority 1: The single most urgent action the farmer must take immediately — be very specific: name the product, the rate, where to buy it (e.g., DA outlet, agri-vet store), and the estimated cost in PHP.",
    "Priority 2: The second most important action — again specific with product name, rate, timing, and source.",
    "Priority 3: A medium-term action the farmer should complete within the next 2–4 weeks — specific and actionable."
  ],

  "soil_remarks": {
    "ph":  "Write 2 sentences about the {$phRating} pH level ({$phVal}): (1) what this specific value means for {$crop} nutrient availability and root health, (2) exactly what the farmer must do to correct or maintain it — name the product and rate.",
    "om":  "Write 2 sentences about the {$omRating} organic matter ({$omVal}%): (1) how this OM level affects soil structure, water retention, and microbial activity for {$crop}, (2) the specific organic material the farmer should apply and at what rate.",
    "n":   "Write 2 sentences about the {$nRating} nitrogen ({$nVal}%): (1) the visible symptoms the farmer will see on coffee leaves if this deficiency is not corrected, (2) the recommended nitrogen fertilizer with exact rate and split-application timing.",
    "p":   "Write 2 sentences about the {$pRating} phosphorus ({$pVal} ppm): (1) how this phosphorus level affects coffee root development, flowering, and fruit set, (2) the specific phosphorus product, rate, and whether to band or broadcast.",
    "k":   "Write 2 sentences about the {$kRating} potassium ({$kVal} ppm): (1) how this potassium level affects coffee cherry filling, bean quality, and disease resistance, (2) the specific potassium product, rate, and timing relative to fruit development."
  },

  "organic_alternatives": [
    "Organic alternative 1 to address the most critical nutrient deficiency — name a specific locally available Philippine material (e.g., vermicast, chicken manure, rice hull ash, coffee pulp compost), how to prepare it, the application rate per hill or per hectare, and when to apply.",
    "Organic alternative 2 — for a different deficiency or the same one using a different material. Include preparation method and timing.",
    "Organic alternative 3 — a soil health improvement practice using organic inputs that addresses the {$omRating} organic matter level. Include rate and timing."
  ],

  "practices": [
    "Practice 1: Describe a specific soil management technique suited to {$soil} soil for {$crop} — include when to do it, how to do it, and what result to expect.",
    "Practice 2: Describe a mulching or composting practice that addresses the identified nutrient deficiencies — specify the material, thickness or rate, and timing in relation to Philippine coffee seasons.",
    "Practice 3: Describe water and irrigation management specific to {$soil} soil during the Philippine dry season (Nov–Apr) — include signs of water stress in coffee and how to respond.",
    "Practice 4: Describe an intercropping, shade tree, or cover-crop strategy for Philippine coffee farms that improves the specific soil conditions found in this analysis.",
    "Practice 5: Explain how to monitor soil health between tests — what visual signs in the coffee plants indicate improving or worsening soil conditions, and what triggers a new soil test."
  ],

  "monitoring_plan": [
    "Monitoring item 1: What to check every 2 weeks during the first application season — specific visual signs on leaves, roots, or soil surface, and what action to take if the sign appears.",
    "Monitoring item 2: What to record after each fertilizer application — yield data, plant height, or leaf color — and how to use this record to adjust the next application.",
    "Monitoring item 3: When to conduct a follow-up soil test — specify the timeline (e.g., 6 months after lime application) and what improvement in the ratings to expect before and after treatment."
  ],

  "expected_outcomes": "Write 3 sentences: (1) what specific improvement the farmer should see in their {$crop} plants and soil within 1 growing season after following this recommendation (refer to specific parameters), (2) the expected soil rating improvement at the next soil test (e.g., pH should rise from {$phVal} to the 5.5–6.5 range), (3) the realistic impact on coffee yield and cherry quality if the plan is followed consistently.",

  "reminders": [
    "Critical reminder 1: directly about the WORST-rated parameter ({$phRating} pH or the worst deficiency) — state the precise consequence for the coffee crop if not corrected this season and the deadline for action.",
    "Reminder 2: A fertilizer timing or safety warning specific to the Philippine wet season (May–Oct) — e.g., risk of fertilizer leaching, re-entry intervals, or PPE requirements when applying the recommended products.",
    "Reminder 3: An organic matter maintenance reminder — specify the annual rate of compost or organic amendment needed to raise or maintain organic matter for this specific {$omRating} OM level.",
    "Reminder 4: State clearly when the farmer MUST consult a licensed agriculturist or DA/BSWM technician — be specific about the conditions (e.g., pH below 4.5, no response after 2 applications, new visual symptoms appearing)."
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


        // 1) Direct decode
        $decoded = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // 1b) Try with invalid UTF-8 handling
        $decoded = json_decode($text, true, 512, JSON_INVALID_UTF8_IGNORE);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // 2) Fix trailing commas then decode
        $fixed = preg_replace('/,\s*([\}\]])/m', '$1', $text);
        $decoded = json_decode($fixed, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // 2b) Fix trailing commas + UTF-8
        $decoded = json_decode($fixed, true, 512, JSON_INVALID_UTF8_IGNORE);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        // 3) Extract outermost JSON object, then try decode + trailing-comma fix
        if (preg_match('/\{[\s\S]+\}/s', $text, $matches)) {
            $extracted = $matches[0];
            $decoded = json_decode($extracted, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            $fixed = preg_replace('/,\s*([\}\]])/m', '$1', $extracted);
            $decoded = json_decode($fixed, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
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

    // ─────────────────────────────────────────────────────────────────────────
    //  SOIL RATING HELPERS  (mirrors SoilRecommendationService exactly)
    // ─────────────────────────────────────────────────────────────────────────

    private function rateSoilPh(?float $v): string
    {
        if ($v === null) return 'N/A';
        if ($v < 4.5)   return 'Very Low';
        if ($v <= 5.5)  return 'Low';
        if ($v <= 6.5)  return 'Medium';
        if ($v <= 8.5)  return 'High';
        return 'Very High';
    }

    private function rateSoilOm(?float $v): string
    {
        if ($v === null) return 'N/A';
        if ($v <= 1.00) return 'Very Low';
        if ($v <= 1.70) return 'Low';
        if ($v <= 3.00) return 'Moderate';
        if ($v <= 5.15) return 'High';
        return 'Very High';
    }

    private function rateSoilN(?float $v): string
    {
        if ($v === null) return 'N/A';
        if ($v < 0.05)  return 'Very Low';
        if ($v <= 0.15) return 'Low';
        if ($v <= 0.20) return 'Medium';
        if ($v <= 0.30) return 'High';
        return 'Very High';
    }

    private function rateSoilP(?float $v): string
    {
        if ($v === null) return 'N/A';
        if ($v < 3)   return 'Very Low';
        if ($v <= 10) return 'Low';
        if ($v <= 20) return 'Medium';
        if ($v <= 30) return 'High';
        return 'Very High';
    }

    private function rateSoilK(?float $v): string
    {
        if ($v === null) return 'N/A';
        if ($v < 78)   return 'Very Low';
        if ($v <= 117) return 'Low';
        if ($v <= 235) return 'Medium';
        if ($v <= 391) return 'High';
        return 'Very High';
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  SOIL TABLE BUILDERS  (static lookup — same as SoilRecommendationService)
    // ─────────────────────────────────────────────────────────────────────────

    private function soilFertilizerRows(string $nR, string $pR, string $kR): array
    {
        $nData = match ($nR) {
            'Very Low' => ['Urea (46-0-0) or Ammonium Sulfate (21-0-0)', '90-120 kg N/ha', 'Split 2x (see Sec. 5)'],
            'Low'      => ['Urea (46-0-0) or Complete Fert. (14-14-14)', '60-90 kg N/ha',  'Split 2x (see Sec. 5)'],
            'Medium'   => ['Urea (46-0-0)',                               '30-60 kg N/ha',  'As needed per growth'],
            'High'     => ['Urea (46-0-0) — reduced rate',               '0-30 kg N/ha',   'Only if signs appear'],
            default    => ['None required',                               'Do not apply',   'Risk of N leaching'],
        };
        $pData = match ($pR) {
            'Very Low' => ['Triple Superphosphate (0-46-0)',            '60-90 kg P₂O₅/ha', 'At planting, banded'],
            'Low'      => ['Solophos (0-18-0) or Complete (14-14-14)', '40-60 kg P₂O₅/ha', 'At planting, banded'],
            'Medium'   => ['Solophos (0-18-0)',                         '20-40 kg P₂O₅/ha', 'At planting (maint.)'],
            'High'     => ['Reduced P — only if crop demands it',      '0-20 kg P₂O₅/ha',  'Optional only'],
            default    => ['None required',                             'Do not apply',      'Risk of P pollution'],
        };
        $kData = match ($kR) {
            'Very Low' => ['Muriate of Potash (0-0-60) or SOP (0-0-50)', '60-90 kg K₂O/ha', 'Split 2x (see Sec. 5)'],
            'Low'      => ['Muriate of Potash (0-0-60)',                  '40-60 kg K₂O/ha', 'Split 2x (see Sec. 5)'],
            'Medium'   => ['Muriate of Potash (0-0-60)',                  '20-40 kg K₂O/ha', 'At planting (maint.)'],
            'High'     => ['Muriate of Potash — reduced rate',           '0-20 kg K₂O/ha',  'High-demand crops only'],
            default    => ['None required',                              'Do not apply',     'Excess harms Ca & Mg'],
        };
        return [
            array_merge(['Nitrogen (N)'],   $nData),
            array_merge(['Phosphorus (P)'], $pData),
            array_merge(['Potassium (K)'],  $kData),
        ];
    }

    private function soilAmendmentRows(string $phR, string $omR): array
    {
        $rows = [];
        $phAmend = match ($phR) {
            'Very Low' => ['pH Correction (Acidic)',    'Agricultural Lime (CaCO₃)',          '2-4 t/ha',   'IMMEDIATELY — before fertilizing'],
            'Low'      => ['pH Correction (Acidic)',    'Agricultural Lime (CaCO₃)',          '1-2 t/ha',   'Before planting season'],
            'High'     => ['pH Correction (Alkaline)',  'Elemental Sulfur / Ammon. Sulfate',  '0.5-1 t/ha', 'Before planting season'],
            'Very High' => ['pH Correction (Alkaline)', 'Elemental Sulfur + Gypsum',          '1-3 t/ha',   'Immediately — multi-season'],
            default    => null,
        };
        if ($phAmend) $rows[] = $phAmend;

        $omAmend = match ($omR) {
            'Very Low' => ['Organic Matter (Critical)', 'Compost / Well-decomposed Manure',  '5-10 t/ha', 'Start of season + biochar'],
            'Low'      => ['Organic Matter (Build-up)', 'Compost or Organic Amendments',     '3-5 t/ha',  'Annually, every season'],
            'Moderate' => ['Organic Matter (Maintain)', 'Compost',                           '2-3 t/ha',  'Annually to maintain'],
            default    => null,
        };
        if ($omAmend) $rows[] = $omAmend;

        if (empty($rows)) {
            $rows[] = ['None needed', 'Soil pH and OM are at good levels.', '—', 'Continue current management practices.'];
        }
        return $rows;
    }

    private function soilScheduleRows(string $phR, string $omR, string $nR, string $pR, string $kR): array
    {
        $rows = [];
        $needsLime    = in_array($phR, ['Very Low', 'Low']);
        $needsSulfur  = in_array($phR, ['High', 'Very High']);
        $needsCompost = in_array($omR, ['Very Low', 'Low', 'Moderate']);
        $needsN       = in_array($nR,  ['Very Low', 'Low', 'Medium']);
        $needsP       = in_array($pR,  ['Very Low', 'Low', 'Medium']);
        $needsK       = in_array($kR,  ['Very Low', 'Low', 'Medium']);

        if ($needsLime) {
            $rows[] = ['IMMEDIATELY',          'Apply Agricultural Lime to correct acidic soil pH.'];
            $rows[] = ['After 2–4 weeks',      'Apply compost and fertilizers (after lime has worked).'];
        } elseif ($needsSulfur) {
            $rows[] = ['IMMEDIATELY',          'Apply Elemental Sulfur to correct high/alkaline soil pH.'];
        }
        if ($needsCompost) {
            $rows[] = ['Each planting season', 'Apply compost or organic matter to build soil health.'];
        }
        if ($needsP) {
            $rows[] = ['At planting',          'Apply phosphorus fertilizer near root zone (banded placement).'];
        }
        if ($needsN || $needsK) {
            $parts = array_filter([
                $needsN ? '50% of nitrogen'   : '',
                $needsK ? '50% of potassium'  : '',
            ]);
            $rows[] = ['At planting (basal)', 'Apply ' . implode(' + ', $parts) . ' fertilizer.'];
        }
        if ($needsN || $needsK) {
            $parts = array_filter([
                $needsN ? 'remaining 50% nitrogen'  : '',
                $needsK ? 'remaining 50% potassium' : '',
            ]);
            $rows[] = ['1–2 months after planting', 'Apply ' . implode(' + ', $parts) . ' (top-dress).'];
        }
        if (!$needsLime && !$needsSulfur && !$needsN && !$needsP && !$needsK) {
            $rows[] = ['Each planting season', 'Apply maintenance fertilizer based on crop needs.'];
        }
        $rows[] = ['Annually',             'Apply organic matter (compost/manure) to maintain soil health.'];
        $rows[] = ['Every 6–12 months',    'Re-test soil to evaluate progress and adjust fertilizer plan.'];

        return $rows;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  COMPREHENSIVE REPORT BUILDER
    //  Sections 1–5: PHP-built accurate tables (same format as guide-based service)
    //  Sections 6+ : Gemini AI qualitative interpretation
    // ─────────────────────────────────────────────────────────────────────────

    private function buildReport(array $data, array $nums, array $ratings, array $json): string
    {
        $crop     = $data['crop_variety'] ?? 'Coffee';
        $soil     = $data['soil_type']    ?? 'Unknown';
        $farmName = $data['farm_name']    ?? '';
        $location = $data['location']     ?? '';
        $sep      = str_repeat('=', 96);
        $dash     = str_repeat('-', 96);

        $farmLine     = implode(' | ', array_filter([$farmName, $location]));
        $farmInfoLine = $farmLine ? "\nFarm      : {$farmLine}" : '';

        $ph = $nums['ph']; $om = $nums['om']; $n = $nums['n']; $p = $nums['p']; $k = $nums['k'];
        $fv = fn($v, $unit = '') => $v !== null ? $v . $unit : 'N/A';

        $phR = $ratings['ph']; $omR = $ratings['om'];
        $nR  = $ratings['n'];  $pR  = $ratings['p'];  $kR = $ratings['k'];

        // ── AI Qualitative Sections ────────────────────────────────────────────
        $diagnosis     = $json['diagnosis']     ?? '';
        $farmerSummary = $json['farmer_summary'] ?? '';
        $keyConcerns   = $json['key_concerns']  ?? [];
        $priorityActions = $json['priority_actions'] ?? [];
        $soilRemarks   = $json['soil_remarks']  ?? [];
        $organicAlts   = $json['organic_alternatives'] ?? [];
        $practices     = $json['practices']     ?? [];
        $monitoringPlan = $json['monitoring_plan'] ?? [];
        $expectedOutcomes = $json['expected_outcomes'] ?? '';
        $reminders     = $json['reminders']     ?? [];

        // ── SECTION 1: Soil Condition (with AI remarks per parameter) ──────────
        $condWidths = [20, 10, 9, 58];
        $condDiv    = $this->divider($condWidths);
        $condHead   = $this->trow(['Parameter', 'Value', 'Rating', 'Remarks & Action'], $condWidths);
        $condRows   = implode("\n", [
            $this->trow(['Soil pH',        $fv($ph),        $phR, $soilRemarks['ph'] ?? $this->fallbackPhRemark($phR)],  $condWidths),
            $this->trow(['Organic Matter', $fv($om, '%'),   $omR, $soilRemarks['om'] ?? $this->fallbackOmRemark($omR)],  $condWidths),
            $this->trow(['Nitrogen (N)',   $fv($n, '%'),    $nR,  $soilRemarks['n']  ?? $this->fallbackNRemark($nR)],   $condWidths),
            $this->trow(['Phosphorus (P)', $fv($p, ' ppm'), $pR, $soilRemarks['p']  ?? $this->fallbackPRemark($pR)],   $condWidths),
            $this->trow(['Potassium (K)',  $fv($k, ' ppm'), $kR, $soilRemarks['k']  ?? $this->fallbackKRemark($kR)],   $condWidths),
        ]);
        $section1 = implode("\n", [$condDiv, $condHead, $condDiv, $condRows, $condDiv]);

        // ── SECTION 2: Fertilizer (static table) ──────────────────────────────
        $fertWidths  = [15, 38, 20, 22];
        $fertDiv     = $this->divider($fertWidths);
        $fertHead    = $this->trow(['Nutrient', 'Fertilizer Product', 'Rate (per ha)', 'When to Apply'], $fertWidths);
        $fertRowData = $this->soilFertilizerRows($nR, $pR, $kR);
        $fertRows    = implode("\n", array_map(fn($r) => $this->trow($r, $fertWidths), $fertRowData));
        $section2    = implode("\n", [$fertDiv, $fertHead, $fertDiv, $fertRows, $fertDiv]);

        // ── SECTION 3: Soil Amendment (static table) ──────────────────────────
        $amendWidths  = [25, 36, 12, 26];
        $amendDiv     = $this->divider($amendWidths);
        $amendHead    = $this->trow(['Concern', 'Product', 'Rate', 'When to Apply'], $amendWidths);
        $amendRowData = $this->soilAmendmentRows($phR, $omR);
        $amendRows    = implode("\n", array_map(fn($r) => $this->trow($r, $amendWidths), $amendRowData));
        $section3     = implode("\n", [$amendDiv, $amendHead, $amendDiv, $amendRows, $amendDiv]);

        // ── SECTION 4: Organic Alternatives (AI) ──────────────────────────────
        $organicLines = implode("\n", array_map(
            fn($i, $a) => '  ' . ($i + 1) . '. ' . $a,
            array_keys($organicAlts), $organicAlts
        ));
        if (!$organicLines) $organicLines = '  — No organic alternatives specified.';

        // ── SECTION 5: Application Schedule (static table) ────────────────────
        $schedWidths  = [28, 68];
        $schedDiv     = $this->divider($schedWidths);
        $schedHead    = $this->trow(['When', 'Action / What to Do'], $schedWidths);
        $schedRowData = $this->soilScheduleRows($phR, $omR, $nR, $pR, $kR);
        $schedRows    = implode("\n", array_map(fn($r) => $this->trow($r, $schedWidths), $schedRowData));
        $section5     = implode("\n", [$schedDiv, $schedHead, $schedDiv, $schedRows, $schedDiv]);

        // ── SECTION 6: Good Farming Practices (AI) ────────────────────────────
        $practiceLines = implode("\n", array_map(
            fn($i, $p) => '  ' . ($i + 1) . '. ' . $p,
            array_keys($practices), $practices
        ));
        if (!$practiceLines) $practiceLines = '  — See Section 5 schedule for immediate actions.';

        // ── SECTION 7: Monitoring Plan (AI) ───────────────────────────────────
        $monitorLines = implode("\n", array_map(
            fn($i, $m) => '  ' . ($i + 1) . '. ' . $m,
            array_keys($monitoringPlan), $monitoringPlan
        ));
        if (!$monitorLines) $monitorLines = '  — Re-test soil every 6–12 months and observe plant health.';

        // ── Priority Actions & Key Concerns ────────────────────────────────────
        $priorityLines = implode("\n", array_map(
            fn($i, $a) => '  ' . ($i + 1) . '. ' . $a,
            array_keys($priorityActions), $priorityActions
        ));
        $concernLines = implode("\n", array_map(
            fn($i, $c) => '  ' . ($i + 1) . '. ' . $c,
            array_keys($keyConcerns), $keyConcerns
        ));

        // ── Reminders ─────────────────────────────────────────────────────────
        $reminderLines = implode("\n", array_map(
            fn($r) => '  ! ' . $r,
            $reminders
        ));
        if (!$reminderLines) $reminderLines = '  ! Always consult a licensed agriculturist for site-specific advice.';

        $expectedBlock = $expectedOutcomes
            ? "\n>> EXPECTED OUTCOMES (After Following This Plan)\n{$dash}\n  {$expectedOutcomes}\n"
            : '';

        return <<<REPORT
AI-ASSISTED SOIL ANALYSIS RECOMMENDATION REPORT
Powered by Google Gemini AI  |  Based on BSWM / FAO Philippine Soil Interpretation Guidelines
{$sep}
Crop      : {$crop}
Soil Type : {$soil}{$farmInfoLine}
{$sep}

>> AI DIAGNOSIS SUMMARY
{$dash}
  {$diagnosis}

>> WHAT THIS MEANS FOR YOU (Plain Language)
{$dash}
  {$farmerSummary}

>> KEY CONCERNS IDENTIFIED
{$dash}
{$concernLines}
{$dash}

>> PRIORITY ACTIONS (Act on These First)
{$dash}
{$priorityLines}
{$dash}

1. SOIL CONDITION ASSESSMENT
   (AI-interpreted remarks for each parameter)
{$section1}

2. FERTILIZER RECOMMENDATION
   Based on BSWM / FAO Philippine soil guidelines.
   Full application timing is detailed in Section 5 below.
{$section2}

3. SOIL AMENDMENT
   Correct soil pH and organic matter BEFORE applying fertilizers.
{$section3}

4. ORGANIC / LOW-COST ALTERNATIVES
   For farmers preferring organic inputs or with limited budget:
{$organicLines}

5. APPLICATION SCHEDULE
{$section5}

6. GOOD FARMING PRACTICES (AI-Recommended)
{$practiceLines}

7. MONITORING PLAN
{$monitorLines}
{$expectedBlock}
{$sep}
IMPORTANT REMINDERS
{$sep}
{$reminderLines}
{$sep}
  This report is AI-assisted. Ratings are based on BSWM/FAO guidelines.
  Always have recommendations reviewed by a licensed agriculturist before applying.
{$sep}
REPORT;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  FALLBACK REMARKS (used when AI remark is missing for a parameter)
    // ─────────────────────────────────────────────────────────────────────────

    private function fallbackPhRemark(string $r): string
    {
        return match ($r) {
            'Very Low' => 'Extremely acidic. Apply lime immediately before any fertilizer.',
            'Low'      => 'Strongly acidic. Apply agricultural lime before planting.',
            'Medium'   => 'Good for coffee (5.5-6.5). Maintain current practices.',
            'High'     => 'Slightly alkaline. Apply elemental sulfur to lower pH.',
            'Very High' => 'Strongly alkaline. Major pH correction required urgently.',
            default    => 'No data.',
        };
    }

    private function fallbackOmRemark(string $r): string
    {
        return match ($r) {
            'Very Low' => 'Severely depleted. Apply 5-10 t/ha compost immediately.',
            'Low'      => 'Low. Apply 3-5 t/ha compost or vermicast annually.',
            'Moderate' => 'Adequate. Maintain with 2-3 t/ha compost per year.',
            'High'     => 'Good. Maintain current organic matter practices.',
            'Very High' => 'Excellent. Avoid excess manure application.',
            default    => 'No data.',
        };
    }

    private function fallbackNRemark(string $r): string
    {
        return match ($r) {
            'Very Low' => 'Severely deficient. Apply 90-120 kg N/ha in 2 splits.',
            'Low'      => 'Deficient. Apply 60-90 kg N/ha split over 2 applications.',
            'Medium'   => 'Adequate. Maintenance dose of 30-60 kg N/ha as needed.',
            'High'     => 'Sufficient. Reduce to 0-30 kg N/ha only if needed.',
            'Very High' => 'Excess nitrogen. Do not apply; risk of leaching.',
            default    => 'No data.',
        };
    }

    private function fallbackPRemark(string $r): string
    {
        return match ($r) {
            'Very Low' => 'Severely deficient. Apply 60-90 kg P₂O₅/ha at planting.',
            'Low'      => 'Deficient. Apply 40-60 kg P₂O₅/ha banded at planting.',
            'Medium'   => 'Adequate. Maintenance dose 20-40 kg P₂O₅/ha at planting.',
            'High'     => 'Sufficient. Reduce to 0-20 kg P₂O₅/ha max if needed.',
            'Very High' => 'Excess phosphorus. Do NOT apply; risk of runoff pollution.',
            default    => 'No data.',
        };
    }

    private function fallbackKRemark(string $r): string
    {
        return match ($r) {
            'Very Low' => 'Severely deficient. Apply 60-90 kg K₂O/ha in 2 splits.',
            'Low'      => 'Deficient. Apply 40-60 kg K₂O/ha in 2 split applications.',
            'Medium'   => 'Adequate. Maintenance dose 20-40 kg K₂O/ha at planting.',
            'High'     => 'Sufficient. Reduce to 0-20 kg K₂O/ha for high-demand only.',
            'Very High' => 'Excess potassium. Do not apply; excess harms Ca and Mg.',
            default    => 'No data.',
        };
    }
}
