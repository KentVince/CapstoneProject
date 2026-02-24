<?php
// ─────────────────────────────────────────────────────────────────────────────
// Static reference data sourced from: Coffee Pest & Disease Recommendation PDF
// ─────────────────────────────────────────────────────────────────────────────
$staticData = [

    // ── 1. Coffee Leaf Miner ────────────────────────────────────────────────
    'Miner' => [
        'scientific_name' => 'Leucoptera coffeella',
        'detection_confidence' => 'High (85–95%)',
        'severity_levels' => 'Mild (1–10% leaf area affected) | Moderate (10–30%) | Severe (>30%)',
        'description' =>
            'The Coffee Leaf Miner (Leucoptera coffeella) is one of the most damaging insect pests of coffee in Latin America, Africa, and parts of Asia. ' .
            'It is a small white moth whose larvae tunnel (mine) inside coffee leaves, creating characteristic blotch or serpentine galleries visible on the leaf surface. ' .
            'Infestations reduce the plant\'s photosynthetic capacity, weaken overall tree health, and — when severe — cause significant premature leaf drop and yield loss. ' .
            'The pest thrives in warm, dry conditions and in farms with little canopy shade or natural enemy populations. ' .
            'Early detection through regular scouting is essential, as populations can multiply rapidly during dry seasons.',
        'symptoms' => [
            'Irregular serpentine or blotch-shaped mines on leaf surface',
            'Yellowing and browning of affected leaf areas',
            'Premature leaf drop in severe infestations',
            'Visible larvae or pupae inside leaf tissue when mines are opened',
            'Reduced photosynthetic capacity leading to stunted plant growth',
        ],
        'causes' => [
            'Infestation by the moth Leucoptera coffeella (coffee leaf miner)',
            'Adult moths lay eggs on leaf surfaces; larvae tunnel into leaf tissue',
            'High temperatures (25–30°C) and low humidity favor population explosions',
            'Monoculture farming and lack of natural predators increase risk',
            'Poor farm sanitation and dense canopy create ideal breeding conditions',
        ],
        'impact' => [
            'Reduction in photosynthesis leading to yield losses of 10–40%',
            'Weakened plant immunity making trees susceptible to secondary infections',
            'Increased production costs due to chemical treatments',
            'Long-term soil and biodiversity degradation if pesticides are overused',
        ],
        'seven_day_plan' => [
            ['day' => 'Day 1', 'title' => 'Scouting & Assessment', 'detail' => 'Walk all rows, record infested leaf percentage per tree. Map hotspots on a farm sketch. Note canopy density and microclimate.'],
            ['day' => 'Day 2', 'title' => 'Pruning & Sanitation', 'detail' => 'Remove and bag severely mined leaves. Prune dense inner branches to improve airflow and reduce micro-humidity. Do NOT compost infested material.'],
            ['day' => 'Day 3', 'title' => 'Biological Treatment', 'detail' => 'Apply Bacillus thuringiensis (Bt) foliar spray on mild–moderate areas. Release Chrysoperla carnea (lacewing) predators if available.'],
            ['day' => 'Day 4', 'title' => 'Chemical Treatment (if Severe)', 'detail' => 'Apply systemic insecticide (e.g., imidacloprid 200 SL at label rate) to severely infested blocks. Avoid spraying during flowering to protect pollinators.'],
            ['day' => 'Day 5', 'title' => 'Re-scouting & Trap Setup', 'detail' => 'Install pheromone traps (1 per hectare) to monitor adult moth populations. Record trap catches to track infestation trend.'],
            ['day' => 'Day 6', 'title' => 'Monitoring & Data Recording', 'detail' => 'Check pheromone traps, record counts. Inspect treated trees for larval mortality. Photograph mine progression for comparison.'],
            ['day' => 'Day 7', 'title' => 'Nutrition & Recovery', 'detail' => 'Apply foliar fertilizer (NPK 20-20-20 + micronutrients) to support leaf recovery. Add organic mulch at tree base to improve soil moisture.'],
        ],
        'immediate_response' => [
            'Isolate severely affected trees with physical markers',
            'Apply neem oil spray (5 mL/L) as an organic first-response measure',
            'Remove and destroy all mined leaves in sealed plastic bags',
            'Notify neighboring farms if infestation is widespread',
        ],
        'long_term' => [
            'Introduce and maintain shade trees to create unfavorable conditions for adult moths',
            'Implement integrated pest management (IPM) combining biological, cultural, and chemical controls',
            'Establish habitat for natural enemies (lacewings, parasitic wasps)',
            'Rotate chemical classes annually to prevent resistance build-up',
            'Conduct bi-weekly scouting throughout the growing season',
            'Maintain farm records for trend analysis and early intervention',
        ],
    ],

    // ── 2. Coffee Leaf Rust ─────────────────────────────────────────────────
    'Rust' => [
        'scientific_name' => 'Hemileia vastatrix',
        'detection_confidence' => 'Very High (90–98%)',
        'severity_levels' => 'Mild (scattered orange pustules, <10% canopy) | Moderate (10–40% defoliation) | Severe (>40% defoliation, branch die-back)',
        'description' =>
            'Coffee Leaf Rust, caused by the fungal pathogen Hemileia vastatrix, is the single most economically destructive disease of coffee worldwide. ' .
            'First documented in Sri Lanka in 1869, it has since spread to all major coffee-growing regions, triggering devastating epidemics including the catastrophic 2012–2013 outbreak ' .
            'in Central America that destroyed over 70% of some nations\' crops. The disease attacks Arabica varieties (Coffea arabica) with particular severity, producing characteristic ' .
            'orange-yellow powdery pustules on the underside of leaves. Infected leaves lose their ability to photosynthesize and drop prematurely, stripping trees of their productive canopy. ' .
            'Without intervention, a single rainy season can escalate a mild infection into a farm-wide epidemic.',
        'symptoms' => [
            'Pale yellow-orange powdery pustules on the undersides of leaves',
            'Corresponding chlorotic (yellow) spots on the upper leaf surface',
            'Progressive browning and necrosis of affected leaf areas',
            'Premature and extensive leaf drop (defoliation)',
            'Reduced berry set and significant yield loss in severe cases',
            'Complete branch dieback in unmanaged outbreaks',
        ],
        'causes' => [
            'Fungal infection by Hemileia vastatrix (obligate biotrophic basidiomycete)',
            'Spores (urediniospores) dispersed by wind, rain splash, insects, and farm tools',
            'Optimal infection: temperatures 15–28°C with high relative humidity (>80%)',
            'Rainy seasons and poor drainage accelerate epidemic spread',
            'Susceptible Arabica varieties (e.g., Typica, Bourbon) at highest risk',
            'Nitrogen over-fertilization promotes lush growth vulnerable to infection',
        ],
        'impact' => [
            'Yield losses of 30–80% in severe, unmanaged epidemics',
            'Weakened trees with reduced lifespan and productivity',
            'Increased economic burden — one of the most costly coffee diseases globally',
            'Forced early harvesting of under-ripe cherries in heavily affected farms',
            'Loss of farmer livelihoods especially in smallholder settings',
        ],
        'seven_day_plan' => [
            ['day' => 'Day 1', 'title' => 'Scouting & Severity Mapping', 'detail' => 'Survey entire farm, grade each tree as mild/moderate/severe. Mark GPS coordinates of hotspots. Count percentage of affected leaves per branch.'],
            ['day' => 'Day 2', 'title' => 'Pruning & Defoliation Management', 'detail' => 'Remove and bag all heavily infected leaves. Prune crossing branches to improve canopy airflow. Avoid leaving pruned material on the ground.'],
            ['day' => 'Day 3', 'title' => 'Copper-Based Fungicide Treatment', 'detail' => 'Apply copper hydroxide (Cu(OH)₂) or copper oxychloride at 2.5–3 g/L to mild and moderate trees. Ensure thorough coverage of leaf undersides.'],
            ['day' => 'Day 4', 'title' => 'Systemic Fungicide (Moderate–Severe)', 'detail' => 'Apply triazole fungicide (e.g., tebuconazole or triadimefon) to moderate-severe trees. Follow label intervals — do not tank-mix copper and triazoles on same day.'],
            ['day' => 'Day 5', 'title' => 'Re-scouting & Spore Trap Setup', 'detail' => 'Assess treated trees for new pustule development. Set up spore sampling traps to measure airborne inoculum. Record weather data (temp/humidity).'],
            ['day' => 'Day 6', 'title' => 'Monitoring & Canopy Assessment', 'detail' => 'Photograph and compare pustule activity against Day 1 baseline. Document defoliation percentage. Alert agronomist if new areas show infection.'],
            ['day' => 'Day 7', 'title' => 'Nutrition & Plant Recovery', 'detail' => 'Apply potassium-rich fertilizer (0-0-60 SOP at recommended rate) to strengthen cell walls. Add silicon (Si) foliar spray as additional resistance booster.'],
        ],
        'immediate_response' => [
            'Apply copper-based fungicide (Bordeaux mixture or copper hydroxide) immediately',
            'Remove all visibly infected leaves and dispose of them off-site',
            'Increase canopy airflow through targeted pruning',
            'Temporarily reduce nitrogen fertilization to slow lush, susceptible growth',
            'Alert neighboring coffee farmers to coordinate area-wide response',
        ],
        'long_term' => [
            'Transition to rust-resistant varieties (e.g., Catimor, Sarchimor, or local resistant hybrids)',
            'Establish a preventive fungicide calendar: copper sprays every 3–4 weeks during rainy season',
            'Maintain balanced nutrition — excess nitrogen increases rust susceptibility',
            'Introduce shade management to reduce humidity while preserving microclimates',
            'Train farm workers in early detection and reporting protocols',
            'Participate in regional rust monitoring networks and early warning systems',
        ],
    ],

    // ── 3. Cercospora Leaf Spot ─────────────────────────────────────────────
    'Cercospora' => [
        'scientific_name' => 'Cercospora coffeicola',
        'detection_confidence' => 'High (80–93%)',
        'severity_levels' => 'Mild (few isolated spots, <5% leaf area) | Moderate (coalescing spots, 5–25%) | Severe (lesions cover >25%, fruit infection visible)',
        'description' =>
            'Cercospora Leaf Spot, caused by the fungus Cercospora coffeicola, is a widespread fungal disease affecting coffee in all major producing countries. ' .
            'It is particularly damaging in nurseries and young plantings, where it can cause devastating seedling losses. The disease is easily identified by its characteristic ' .
            'circular brown spots with a pale gray or whitish center — a pattern sometimes called \'brown eye spot.\' In addition to foliage damage, the pathogen infects developing ' .
            'coffee cherries, causing surface blemishes that reduce the marketable quality and cup profile of the harvested coffee. Cercospora thrives in conditions of high humidity, ' .
            'poor nutrition, and inadequate canopy airflow, making it especially problematic in poorly managed or overly shaded plantations.',
        'symptoms' => [
            'Circular to irregular brown spots with whitish-gray centers on leaves',
            'Yellow halo surrounding each lesion on the upper leaf surface',
            'Lesions may coalesce forming large necrotic patches in moderate-severe cases',
            'Dark brown to black \'eye-spot\' pattern visible on mature lesions',
            'Fruit infection: reddish-brown spots on coffee cherries (brown eye on berries)',
            'Premature fruit drop and internal discoloration of infected cherries',
        ],
        'causes' => [
            'Fungal pathogen Cercospora coffeicola (Ascomycete)',
            'Spores spread via wind and rain splash from infected leaf/fruit debris',
            'Thrives in conditions of high humidity, poor drainage, and dense planting',
            'Nutritionally stressed plants (low N, Zn, Mg) show higher disease incidence',
            'Overripe or damaged fruits are entry points for infection',
            'Seedlings and nurseries particularly vulnerable to severe outbreaks',
        ],
        'impact' => [
            'Yield loss of 10–35% due to premature fruit drop and fruit quality reduction',
            'Grade reduction of harvested coffee — spotted berries downgraded or rejected',
            'Nursery losses can reach 50–70% if outbreaks occur in seedling stage',
            'Weakened plant vigor reducing long-term productivity',
        ],
        'seven_day_plan' => [
            ['day' => 'Day 1', 'title' => 'Field Assessment & Sampling', 'detail' => 'Collect 10 leaves and 5 fruits per tree from 20 random trees. Estimate % leaf area and % fruit affected. Record data by field block.'],
            ['day' => 'Day 2', 'title' => 'Sanitation & Pruning', 'detail' => 'Remove and destroy infected leaves and fallen fruits. Prune to reduce canopy density. Avoid overhead irrigation to reduce splash dispersal.'],
            ['day' => 'Day 3', 'title' => 'Copper Fungicide Application', 'detail' => 'Apply copper oxychloride (3 g/L) or mancozeb (2 g/L) as a protective spray. Target undersides of leaves where sporulation occurs.'],
            ['day' => 'Day 4', 'title' => 'Systemic Fungicide (Moderate-Severe)', 'detail' => 'Apply thiophanate-methyl or azoxystrobin if disease pressure is moderate to severe. Alternate between chemical classes to manage resistance.'],
            ['day' => 'Day 5', 'title' => 'Soil & Nutrition Correction', 'detail' => 'Apply zinc sulfate (0.5%) foliar spray to address micronutrient deficiency. Topdress with balanced NPK fertilizer adjusted to soil test recommendations.'],
            ['day' => 'Day 6', 'title' => 'Monitoring & Environmental Assessment', 'detail' => 'Re-examine treated trees. Monitor weather — watch for rainfall events that re-trigger sporulation. Record new lesion development vs treated areas.'],
            ['day' => 'Day 7', 'title' => 'Plant Recovery Nutrition', 'detail' => 'Apply magnesium sulfate (Epsom salt, 2 g/L) foliar spray. Add boron (0.1%) to improve fruit development and reduce infection entry points.'],
        ],
        'immediate_response' => [
            'Remove and dispose of heavily spotted leaves and infected berries immediately',
            'Apply mancozeb or copper-based fungicide as emergency protective spray',
            'Improve drainage around affected trees',
            'Correct nutritional deficiencies — especially zinc and nitrogen — with foliar treatment',
        ],
        'long_term' => [
            'Maintain balanced crop nutrition program with annual soil and leaf tissue analysis',
            'Avoid over-shading which promotes high humidity and spore germination',
            'Schedule preventive fungicide applications before and during rainy seasons',
            'Use disease-free planting materials from certified nurseries',
            'Implement proper post-harvest sanitation — remove all mummified fruits',
            'Select planting densities that allow adequate airflow between trees',
        ],
    ],

    // ── 4. Phoma Twig Blight ────────────────────────────────────────────────
    'Phoma' => [
        'scientific_name' => 'Phoma costaricensis / Botrytis cinerea complex',
        'detection_confidence' => 'Moderate–High (75–90%)',
        'severity_levels' => 'Mild (tip die-back on isolated branches) | Moderate (multiple branches affected, 10–30% canopy) | Severe (trunk lesions, >30% canopy dieback, plant mortality risk)',
        'description' =>
            'Phoma Twig Blight is a fungal disease primarily caused by Phoma costaricensis, and is most prevalent in high-altitude coffee-growing regions (above 1,200 m above sea level) ' .
            'where cool temperatures and prolonged wet conditions create ideal conditions for the pathogen to thrive. The disease causes progressive die-back of young shoots and twigs, ' .
            'often starting at the tips and working downward — a symptom pattern that can be confused with frost damage or drought stress. In severe, unmanaged cases, the fungus can ' .
            'invade older stems and form deep trunk cankers that threaten the structural integrity and long-term productivity of the tree. High-altitude specialty coffee farms are ' .
            'particularly vulnerable, as the combination of cold stress and high humidity significantly lowers plant resistance.',
        'symptoms' => [
            'Water-soaked lesions on young shoots and twigs, rapidly darkening to brown-black',
            'Tip die-back progressing downward along branches (blighting pattern)',
            'Dark, sunken cankers on older stems and trunks in severe cases',
            'Circular brown lesions on leaves, often starting at leaf margins or tip',
            'Premature drop of affected leaves and young berries',
            'White to grey fluffy mycelium visible in humid conditions',
            'Pycnidia (small black fungal fruiting bodies) visible on dead tissue',
        ],
        'causes' => [
            'Fungal pathogens in Phoma genus (primarily P. costaricensis in coffee)',
            'Thrives in cool, wet, high-altitude conditions (>1200 m asl)',
            'Conidia dispersed by rain splash and wind during wet periods',
            'Wounds from pruning, frost, or insects are primary entry points',
            'Over-dense canopy creating high-humidity microenvironments',
            'Cold-stressed plants at high elevations show increased susceptibility',
        ],
        'impact' => [
            'Significant structural damage — twig and branch loss reduces bearing surface',
            'Yield losses of 15–50% in high-altitude farms during rainy seasons',
            'Increased labor costs for remediation pruning and treatment',
            'Plant death in nurseries and young plantings if uncontrolled',
        ],
        'seven_day_plan' => [
            ['day' => 'Day 1', 'title' => 'Disease Mapping & Assessment', 'detail' => 'Map affected trees by severity grade. Note altitude, aspect, and drainage conditions. Photograph canker progression on trunk and branches.'],
            ['day' => 'Day 2', 'title' => 'Pruning & Wound Management', 'detail' => 'Prune all blighted twigs 10 cm below visible lesion margin. Paint cut surfaces with Bordeaux paste or copper-based wound sealant immediately after pruning.'],
            ['day' => 'Day 3', 'title' => 'Fungicide Application – Mild/Moderate', 'detail' => 'Apply mancozeb + copper oxychloride mixture (2 + 2 g/L) to entire canopy. Ensure thorough coverage of twig junctions and stem bases.'],
            ['day' => 'Day 4', 'title' => 'Systemic Fungicide – Moderate/Severe', 'detail' => 'Apply trifloxystrobin or azoxystrobin (QoI group) at label rate. Alternate with carbendazim for severe trunk cankers. Do not exceed label applications.'],
            ['day' => 'Day 5', 'title' => 'Drainage & Canopy Improvement', 'detail' => 'Improve soil drainage by creating channels to divert waterlogging. Thin canopy further to reduce humidity. Document canopy density before and after.'],
            ['day' => 'Day 6', 'title' => 'Monitoring & Weather Logging', 'detail' => 'Check pruned and treated areas for new blight progression. Log temperature and humidity data. Reassess trunk lesion boundaries.'],
            ['day' => 'Day 7', 'title' => 'Recovery Nutrition', 'detail' => 'Apply calcium nitrate (10 g/L) foliar spray to strengthen cell walls. Add silicon-based amendment to soil to boost plant resistance.'],
        ],
        'immediate_response' => [
            'Prune all visibly blighted twigs immediately — cutting below lesion margins',
            'Seal all pruning wounds with copper-based paste to prevent reinfection',
            'Apply systemic fungicide to severely affected trees',
            'Improve drainage around the base of affected trees',
        ],
        'long_term' => [
            'Select planting sites with good drainage and air circulation',
            'Establish windbreaks at high-altitude farms to reduce cold stress',
            'Implement structured annual pruning calendar to maintain open canopy',
            'Monitor weather data to anticipate wet, cool periods requiring preventive sprays',
            'Use resistant or tolerant coffee varieties where available',
            'Avoid mechanical wounds during farm operations — use sharp, disinfected tools',
        ],
    ],

    // ── 5. Coffee Berry Borer ───────────────────────────────────────────────
    'Berry' => [
        'scientific_name' => 'Hypothenemus hampei',
        'detection_confidence' => 'Very High (88–97%)',
        'severity_levels' => 'Mild (<5% berries infested) | Moderate (5–20% berries infested) | Severe (>20% berries infested)',
        'description' =>
            'The Coffee Berry Borer (Hypothenemus hampei) is the most economically devastating insect pest of coffee globally, causing estimated annual losses of USD 500 million worldwide. ' .
            'This tiny dark-brown beetle (1.5–2 mm long) is unique in that the female bores directly into coffee cherries through the crown (distal end) and lays her eggs inside the ' .
            'endosperm — the coffee seed itself. The developing larvae feed on and destroy the seed from the inside, rendering the bean unmarketable. Infested beans are graded as defective ' .
            'and dramatically lower the cup quality score of harvested coffee. The Coffee Berry Borer is present in virtually all coffee-producing countries and is especially difficult to ' .
            'control because the insect spends most of its life cycle hidden inside the berry, protected from external treatments. Effective management relies heavily on cultural practices ' .
            '— particularly thorough and timely harvesting — combined with biological control agents.',
        'symptoms' => [
            'Tiny circular entry hole (0.5–1 mm) on the crown (distal end) of coffee cherries',
            'Fine powdery frass (sawdust-like material) around or within the entry hole',
            'Internal galleries visible when berry is split open — damage to endosperm (seed)',
            'Premature and irregular berry drop from infested fruits',
            'Presence of adult beetles (dark brown, 1.5–2 mm) on or in berries',
            'Weight loss in harvested coffee leading to hollow/light beans',
        ],
        'causes' => [
            'Infestation by the beetle Hypothenemus hampei (Coleoptera: Curculionidae: Scolytinae)',
            'Female beetles bore into coffee berries to lay eggs inside the seed',
            'Highest risk during berry development — green stage most susceptible',
            'Temperatures of 20–30°C and humidity >80% accelerate population growth',
            'Delayed or incomplete harvest leaves residual berries as breeding sites',
            'Proximity to infested neighboring farms facilitates rapid spread',
        ],
        'impact' => [
            'Global economic loss estimated at USD 500 million annually',
            'Quality downgrade — bored beans rejected by specialty coffee buyers',
            'Yield losses of 20–80% in heavily infested unmanaged farms',
            'Increased post-harvest processing cost for sorting and grading',
            'Reduced cupping score — bored beans contribute to defective cup profiles',
        ],
        'seven_day_plan' => [
            ['day' => 'Day 1', 'title' => 'Infestation Scouting & Counting', 'detail' => 'Sample 100 berries from 10 trees per hectare. Count and record % infested berries. Identify hotspot zones for targeted management.'],
            ['day' => 'Day 2', 'title' => 'Sanitation Harvesting', 'detail' => 'Strip-harvest all ripe and overripe berries from infested trees. Collect all fallen berries from the ground. Remove and destroy all residual berries after main harvest.'],
            ['day' => 'Day 3', 'title' => 'Biological Control Application', 'detail' => 'Apply Beauveria bassiana (entomopathogenic fungus) spray (1×10⁸ conidia/mL) to entire berry surface. Best applied in the early morning or evening.'],
            ['day' => 'Day 4', 'title' => 'Chemical Treatment (Moderate–Severe)', 'detail' => 'Apply endosulfan alternative (e.g., chlorpyrifos or spinosad at label rate) to moderate-severe hotspot blocks. Observe pre-harvest intervals strictly.'],
            ['day' => 'Day 5', 'title' => 'Trap Installation & BROCAP Setup', 'detail' => 'Install BROCAP or alcohol-based traps (1:1 methanol:ethanol) at 1 per 250 m². Record trap catches daily to monitor population density.'],
            ['day' => 'Day 6', 'title' => 'Monitoring & Post-Treatment Assessment', 'detail' => 'Re-sample 100 berries from treated areas. Compare infestation rate vs Day 1 baseline. Adjust trap placement based on capture data.'],
            ['day' => 'Day 7', 'title' => 'Nutrition & Canopy Recovery', 'detail' => 'Apply NPK foliar spray to support fruit fill and tree recovery. Add boron (0.1%) to improve fruit set quality and reduce stress susceptibility.'],
        ],
        'immediate_response' => [
            'Immediately harvest all ripe and overripe berries — remove breeding substrate',
            'Collect all fallen berries from the ground and destroy them',
            'Apply Beauveria bassiana as first-response biological control',
            'Install alcohol-based attractant traps throughout the affected area',
            'Coordinate with neighboring farms for synchronized action',
        ],
        'long_term' => [
            'Adopt selective strip-picking to minimize overripe berries remaining on tree',
            'Establish a 2-week harvest cycle during peak fruiting to reduce exposure time',
            'Maintain and expand Beauveria bassiana biological control program',
            'Implement Borer Management Traps (BROCAP or Ethanol) year-round',
            'Train all farm workers in early detection — recognizing entry holes',
            'Ensure complete post-harvest sanitation: remove 100% of residual berries',
            'Collaborate with local coffee cooperatives on area-wide IPM programs',
        ],
    ],
];

$pestName = $record->pest ?? '';

// Exact match first, then fallback to partial contains match (e.g. "Berry Borer" → key "Berry")
$info = $staticData[$pestName] ?? null;
if (!$info) {
    foreach ($staticData as $key => $val) {
        if (str_contains(strtolower($pestName), strtolower($key))
            || str_contains(strtolower($key), strtolower($pestName))) {
            $info = $val;
            break;
        }
    }
}

$tabs = ['Description', 'Symptoms', 'Causes / Risk Factors', 'Impact', 'Seven-Day Action Plan', 'Immediate Response', 'Long-term Strategy'];

// ── AI recommendation decoding (for print report) ─────────────────────────
$printAiJson = null;
if (!empty($record->ai_recommendation)) {
    $rawAi = $record->ai_recommendation;
    $rawAi = preg_replace('/^```(?:json)?\s*/m', '', $rawAi);
    $rawAi = preg_replace('/\s*```$/m', '', $rawAi);
    $rawAi = trim($rawAi);
    if (preg_match('/\{[\s\S]+\}/s', $rawAi, $m)) { $rawAi = $m[0]; }
    $dAi = json_decode($rawAi, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($dAi)) { $printAiJson = $dAi; }
}

// ── Print data array (passed to JS via @js()) ─────────────────────────────
$printData = [
    'pest'             => $record->pest ?? 'Unknown',
    'scientificName'   => $info['scientific_name'] ?? null,
    'type'             => ucfirst($record->type ?? 'N/A'),
    'severity'         => $record->severity ?? 'N/A',
    'confidence'       => $record->confidence ? number_format($record->confidence * 100, 1).'%' : 'N/A',
    'dateDetected'     => $record->date_detected ? \Carbon\Carbon::parse($record->date_detected)->format('F d, Y') : '—',
    'farmer'           => trim(($record->farmer?->first_name ?? '').' '.($record->farmer?->last_name ?? '')) ?: '—',
    'farm'             => $record->farm?->name ?? '—',
    'area'             => $record->area ?? '—',
    'coordinates'      => number_format($record->latitude ?? 0, 4).', '.number_format($record->longitude ?? 0, 4),
    'status'           => ucfirst($record->validation_status ?? 'pending'),
    'validatedBy'      => $record->validator?->name ?? null,
    'validatedAt'      => $record->validated_at ? $record->validated_at->format('F d, Y \a\t h:i A') : null,
    'staticInfo'       => $info,
    'aiData'           => $printAiJson,
    'expertComments'   => $record->expert_comments ?? null,
    'expertStatus'     => $record->validation_status ?? null,
    'farmerAction'     => $record->farmer_action ?? null,
    'farmerActionDate' => !empty($record->farmer_action_date) ? \Carbon\Carbon::parse($record->farmer_action_date)->format('F d, Y') : null,
    'generatedAt'      => now()->format('F d, Y \a\t h:i A'),
    'appNo'            => $record->app_no ?? null,
];
?>

<div
    class="space-y-4 p-4 dark:bg-custom-color-darkmode"
    x-data="{
        showLightbox: false,
        activeTab: 'Description',
        tabs: <?php echo \Illuminate\Support\Js::from($tabs)->toHtml() ?>,
        aiTab: 'Diagnosis',
        printReport: function() {
            var d = <?php echo \Illuminate\Support\Js::from($printData)->toHtml() ?>;
            function e(s){if(!s)return'';return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
            var css='body{font-family:Arial,sans-serif;margin:24px 32px;color:#1f2937;font-size:12px;line-height:1.5}'
                +'h1{font-size:17px;color:#166534;border-bottom:2px solid #166534;padding-bottom:6px;margin-bottom:14px}'
                +'h2{font-size:12px;font-weight:700;color:#1e3a5f;background:#eff6ff;border-left:4px solid #3b82f6;padding:3px 10px;margin-top:14px;margin-bottom:6px;border-radius:0 4px 4px 0}'
                +'h2.ai{color:#6d28d9;background:#f5f3ff;border-color:#8b5cf6}'
                +'.ig{display:grid;grid-template-columns:1fr 1fr 1fr;gap:6px;margin:6px 0 10px}'
                +'.ii{background:#f9fafb;border:1px solid #e5e7eb;padding:6px 9px;border-radius:5px}'
                +'.il{font-size:9px;text-transform:uppercase;color:#6b7280;margin-bottom:2px}'
                +'.iv{font-size:12px;font-weight:600;color:#111827}'
                +'ul{margin:2px 0 6px;padding-left:18px}li{margin:2px 0}'
                +'.dr{display:flex;gap:10px;align-items:flex-start;margin:4px 0}'
                +'.db{background:#d1fae5;color:#065f46;padding:2px 7px;border-radius:4px;font-size:9px;font-weight:700;white-space:nowrap;min-width:64px;text-align:center;flex-shrink:0}'
                +'.sb{background:#ede9fe;color:#4c1d95;padding:2px 7px;border-radius:4px;font-size:9px;font-weight:700;white-space:nowrap;min-width:80px;text-align:center;flex-shrink:0}'
                +'.pc{border:1px solid #e5e7eb;border-radius:5px;padding:5px 9px;margin:3px 0;background:#f9fafb}'
                +'.bdg{display:inline-block;padding:1px 5px;border-radius:10px;font-size:9px;font-weight:600}'
                +'.bio{background:#d1fae5;color:#065f46}.chem{background:#fee2e2;color:#991b1b}.org{background:#fef3c7;color:#92400e}'
                +'.eb{border:1px solid #bbf7d0;background:#f0fdf4;padding:8px 12px;border-radius:5px;margin:5px 0}'
                +'.ew{border-color:#fecaca;background:#fef2f2}'
                +'.fb{border:1px solid #fde68a;background:#fffbeb;padding:8px 12px;border-radius:5px;margin:5px 0}'
                +'.ft{margin-top:20px;border-top:1px solid #e5e7eb;padding-top:6px;font-size:9px;color:#9ca3af;text-align:center}'
                +'@media print{body{margin:8px 16px}}';
            var h='\x3C!DOCTYPE html\x3E\x3Chtml\x3E\x3Chead\x3E\x3Cmeta charset=\x22utf-8\x22\x3E\x3Ctitle\x3EPest Report\x3C\/title\x3E\x3Cstyle\x3E'+css+'\x3C\/style\x3E\x3C\/head\x3E\x3Cbody\x3E';
            h+='\x3Ch1\x3E&#9749; CAFARM \u2014 Pest &amp; Disease Detection Report\x3C\/h1\x3E';
            h+='\x3Ch2\x3ECase Information\x3C\/h2\x3E\x3Cdiv class=\x22ig\x22\x3E';
            var flds=[
                ['Pest \/ Disease',e(d.pest)+(d.scientificName?'\x3Cbr\x3E\x3Cem style=\x22font-size:10px;font-weight:400;color:#6b7280\x22\x3E'+e(d.scientificName)+'\x3C\/em\x3E':'')],
                ['Type',e(d.type)],['Severity',e(d.severity)],['Confidence',e(d.confidence)],
                ['Date Detected',e(d.dateDetected)],['Status',e(d.status)],
                ['Farmer',e(d.farmer)],['Farm',e(d.farm)],['Area \/ Location',e(d.area)],
                ['Coordinates','\x3Cspan style=\x22font-size:10px\x22\x3E'+e(d.coordinates)+'\x3C\/span\x3E']
            ];
            if(d.appNo)flds.push(['App No.',e(d.appNo)]);
            flds.forEach(function(f){h+='\x3Cdiv class=\x22ii\x22\x3E\x3Cdiv class=\x22il\x22\x3E'+f[0]+'\x3C\/div\x3E\x3Cdiv class=\x22iv\x22\x3E'+f[1]+'\x3C\/div\x3E\x3C\/div\x3E';});
            h+='\x3C\/div\x3E';
            if(d.staticInfo){
                var s=d.staticInfo;
                h+='\x3Ch2\x3EReference Guide\x3C\/h2\x3E';
                h+='\x3Cp\x3E\x3Cstrong\x3EDescription:\x3C\/strong\x3E '+e(s.description)+'\x3C\/p\x3E';
                [['Symptoms',s.symptoms],['Causes \/ Risk Factors',s.causes],['Impact',s.impact],
                 ['Immediate Response (within 24 hours)',s.immediate_response],['Long-term Strategy',s.long_term]
                ].forEach(function(sec){
                    h+='\x3Cp\x3E\x3Cstrong\x3E'+sec[0]+':\x3C\/strong\x3E\x3C\/p\x3E\x3Cul\x3E';
                    (sec[1]||[]).forEach(function(x){h+='\x3Cli\x3E'+e(x)+'\x3C\/li\x3E';});
                    h+='\x3C\/ul\x3E';
                });
                h+='\x3Cp\x3E\x3Cstrong\x3ESeven-Day Action Plan:\x3C\/strong\x3E\x3C\/p\x3E';
                (s.seven_day_plan||[]).forEach(function(step){
                    h+='\x3Cdiv class=\x22dr\x22\x3E\x3Cspan class=\x22db\x22\x3E'+e(step.day)+'\x3C\/span\x3E\x3Cdiv\x3E\x3Cstrong\x3E'+e(step.title)+'\x3C\/strong\x3E\x3Cbr\x3E'+e(step.detail)+'\x3C\/div\x3E\x3C\/div\x3E';
                });
            }
            if(d.aiData){
                var a=d.aiData;
                h+='\x3Ch2 class=\x22ai\x22\x3E&#10024; AI-Generated Recommendation (Google Gemini)\x3C\/h2\x3E';
                if(a.urgency)h+='\x3Cp\x3E\x3Cstrong\x3EUrgency:\x3C\/strong\x3E '+e(a.urgency)+(a.urgency_reason?' &mdash; '+e(a.urgency_reason):'')+'\x3C\/p\x3E';
                if(a.diagnosis)h+='\x3Cp\x3E\x3Cstrong\x3EDiagnosis:\x3C\/strong\x3E '+e(a.diagnosis)+'\x3C\/p\x3E';
                if(a.immediate_actions&&a.immediate_actions.length){
                    h+='\x3Cp\x3E\x3Cstrong\x3EImmediate Actions (within 24 hours):\x3C\/strong\x3E\x3C\/p\x3E\x3Cul\x3E';
                    a.immediate_actions.forEach(function(x){h+='\x3Cli\x3E'+e(x)+'\x3C\/li\x3E';});h+='\x3C\/ul\x3E';
                }
                if(a.treatment_protocol&&a.treatment_protocol.length){
                    h+='\x3Cp\x3E\x3Cstrong\x3ETreatment Protocol:\x3C\/strong\x3E\x3C\/p\x3E';
                    a.treatment_protocol.forEach(function(step){h+='\x3Cdiv class=\x22dr\x22\x3E\x3Cspan class=\x22sb\x22\x3E'+e(step.step||'')+'\x3C\/span\x3E\x3Cspan\x3E'+e(step.detail||'')+'\x3C\/span\x3E\x3C\/div\x3E';});
                }
                if(a.schedule&&a.schedule.length){
                    h+='\x3Cp\x3E\x3Cstrong\x3ESchedule:\x3C\/strong\x3E\x3C\/p\x3E';
                    a.schedule.forEach(function(ent){h+='\x3Cdiv class=\x22dr\x22\x3E\x3Cspan class=\x22db\x22\x3E'+e(ent.day||'')+'\x3C\/span\x3E\x3Cspan\x3E'+e(ent.task||'')+'\x3C\/span\x3E\x3C\/div\x3E';});
                }
                if(a.products_needed&&a.products_needed.length){
                    h+='\x3Cp\x3E\x3Cstrong\x3EProducts Needed:\x3C\/strong\x3E\x3C\/p\x3E';
                    a.products_needed.forEach(function(p){
                        var bc=p.type==='Biological'?'bio':p.type==='Chemical'?'chem':'org';
                        h+='\x3Cdiv class=\x22pc\x22\x3E\x3Cstrong\x3E'+e(p.product||'')+'\x3C\/strong\x3E \x3Cspan class=\x22bdg '+bc+'\x22\x3E'+e(p.type||'')+'\x3C\/span\x3E\x3Cbr\x3E\x3Csmall\x3ERate: '+e(p.rate||'\u2014')+(p.notes?' &bull; '+e(p.notes):'')+'\x3C\/small\x3E\x3C\/div\x3E';
                    });
                }
                if(a.prevention&&a.prevention.length){
                    h+='\x3Cp\x3E\x3Cstrong\x3EPrevention Strategies:\x3C\/strong\x3E\x3C\/p\x3E\x3Cul\x3E';
                    a.prevention.forEach(function(x){h+='\x3Cli\x3E'+e(x)+'\x3C\/li\x3E';});h+='\x3C\/ul\x3E';
                }
                if(a.warnings&&a.warnings.length){
                    h+='\x3Cp\x3E\x3Cstrong\x3E&#9888; Warnings:\x3C\/strong\x3E\x3C\/p\x3E\x3Cul\x3E';
                    a.warnings.forEach(function(x){h+='\x3Cli\x3E'+e(x)+'\x3C\/li\x3E';});h+='\x3C\/ul\x3E';
                }
            }
            if(d.expertComments){
                var ok=d.expertStatus==='approved';
                h+='\x3Ch2\x3E'+(ok?'&#10003; Expert Recommendation':'&#9888; Expert Comments')+'\x3C\/h2\x3E';
                h+='\x3Cdiv class=\x22eb'+(ok?'':' ew')+'\x22\x3E'+e(d.expertComments)+'\x3C\/div\x3E';
                if(d.validatedBy)h+='\x3Cp style=\x22font-size:10px;color:#6b7280\x22\x3EValidated by \x3Cstrong\x3E'+e(d.validatedBy)+'\x3C\/strong\x3E'+(d.validatedAt?' on '+e(d.validatedAt):'')+'\x3C\/p\x3E';
            }
            if(d.farmerAction){
                h+='\x3Ch2\x3EFarmer\x27s Action Taken\x3C\/h2\x3E';
                h+='\x3Cdiv class=\x22fb\x22\x3E'+e(d.farmerAction)+'\x3C\/div\x3E';
                if(d.farmerActionDate)h+='\x3Cp style=\x22font-size:10px;color:#6b7280\x22\x3ESubmitted on '+e(d.farmerActionDate)+'\x3C\/p\x3E';
            }
            h+='\x3Cdiv class=\x22ft\x22\x3ECAFARM \u2014 Coffee Agri-Farming Management System &nbsp;|&nbsp; Generated on '+e(d.generatedAt)+'\x3C\/div\x3E';
            h+='\x3C\/body\x3E\x3C\/html\x3E';
            var w=window.open('','_blank');
            w.document.write(h);
            w.document.close();
            setTimeout(function(){w.print();},350);
        }
    }"
>
    
    <div class="flex flex-col md:flex-row gap-6 pt-2">

        
        <div class="flex-shrink-0">
            <!--[if BLOCK]><![endif]--><?php if($record->image_path): ?>
                <img
                    src="<?php echo e(Storage::disk('public')->url($record->image_path)); ?>"
                    alt="Detection Image"
                    class="w-48 h-48 rounded-lg shadow-lg object-cover cursor-pointer hover:opacity-80 transition-opacity"
                    @click="showLightbox = true"
                    title="Click to enlarge"
                />
            <?php else: ?>
                <div class="flex items-center justify-center w-48 h-48 bg-gray-100 dark:bg-gray-800 rounded-lg">
                    <span class="text-gray-400 text-sm">No image</span>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            
            <div class="mt-3 flex justify-center">
                <?php
                    $statusColors = [
                        'pending'     => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                        'approved'    => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                        'disapproved' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                    ];
                    $statusColor = $statusColors[$record->validation_status] ?? $statusColors['pending'];
                ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo e($statusColor); ?>">
                    <!--[if BLOCK]><![endif]--><?php if($record->validation_status === 'approved'): ?>
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    <?php elseif($record->validation_status === 'disapproved'): ?>
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                    <?php else: ?>
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php echo e(ucfirst($record->validation_status)); ?>

                </span>
            </div>

            <!--[if BLOCK]><![endif]--><?php if($record->image_path): ?>
                <p class="text-xs text-gray-400 text-center mt-1">Click image to enlarge</p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="flex-1 grid grid-cols-2 gap-3">
            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Pest / Disease</h4>
                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($record->pest); ?></p>
                <!--[if BLOCK]><![endif]--><?php if($info): ?>
                    <p class="mt-0.5 text-xs text-gray-400 italic"><?php echo e($info['scientific_name']); ?></p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Type</h4>
                <p class="mt-1">
                    <?php
                        $typeColors = [
                            'pest'    => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                            'disease' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                        ];
                        $typeColor = $typeColors[$record->type] ?? 'bg-gray-100 text-gray-800';
                    ?>
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium <?php echo e($typeColor); ?>">
                        <?php echo e(ucfirst($record->type ?? 'N/A')); ?>

                    </span>
                </p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Severity</h4>
                <p class="mt-1">
                    <?php
                        $severityColors = [
                            'Low'    => 'bg-green-100 text-green-800',
                            'Medium' => 'bg-yellow-100 text-yellow-800',
                            'High'   => 'bg-red-100 text-red-800',
                            'Severe' => 'bg-red-100 text-red-800',
                        ];
                        $severityColor = $severityColors[$record->severity] ?? 'bg-gray-100 text-gray-800';
                    ?>
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium <?php echo e($severityColor); ?>">
                        <?php echo e($record->severity); ?>

                    </span>
                </p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Date Detected</h4>
                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                    <?php echo e($record->date_detected ? \Carbon\Carbon::parse($record->date_detected)->format('M d, Y') : '—'); ?>

                </p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Location</h4>
                <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($record->area ?? '—'); ?></p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-800 p-3 rounded-lg">
                <h4 class="text-xs font-medium text-gray-500 dark:text-gray-400">Coordinates</h4>
                <p class="mt-1 text-xs text-gray-900 dark:text-white">
                    <?php echo e(number_format($record->latitude, 4)); ?>, <?php echo e(number_format($record->longitude, 4)); ?>

                </p>
            </div>

            <!--[if BLOCK]><![endif]--><?php if($info): ?>
                <div class="col-span-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-3 rounded-lg">
                    <h4 class="text-xs font-medium text-blue-600 dark:text-blue-400">Detection Confidence</h4>
                    <p class="mt-0.5 text-sm text-blue-800 dark:text-blue-200"><?php echo e($info['detection_confidence']); ?></p>
                    <p class="mt-1 text-xs text-blue-600 dark:text-blue-400">
                        <span class="font-medium">Severity Scale:</span> <?php echo e($info['severity_levels']); ?>

                    </p>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>


    
    <div class="flex items-center justify-between bg-gray-700 dark:bg-gray-900 rounded-lg px-4 py-2 shadow-sm">
        <div class="flex items-center gap-2 text-white text-xs font-medium">
            <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
            </svg>
            <span>Pest &amp; Disease Report &mdash; <?php echo e($record->pest); ?></span>
        </div>
        <button
            type="button"
            @click.stop.prevent="printReport()"
            class="text-xs text-gray-300 hover:text-white bg-gray-600 hover:bg-gray-500 px-2 py-1 rounded transition flex items-center gap-1">
            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/>
            </svg>
            Print PDF
        </button>
    </div>

    
    <!--[if BLOCK]><![endif]--><?php if($info): ?>
        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">

            
            <div class="bg-gray-50 dark:bg-gray-800/60 border-b border-gray-200 dark:border-gray-700">
                <div class="flex overflow-x-auto pest-tabs-scroll" style="scrollbar-width: thin; scrollbar-color: #d1d5db transparent;">
                    <style>
                        .pest-tabs-scroll::-webkit-scrollbar { height: 4px; }
                        .pest-tabs-scroll::-webkit-scrollbar-track { background: transparent; }
                        .pest-tabs-scroll::-webkit-scrollbar-thumb { background-color: #d1d5db; border-radius: 9999px; }
                        .dark .pest-tabs-scroll::-webkit-scrollbar-thumb { background-color: #6b7280; }
                    </style>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button
                            type="button"
                            @click.stop.prevent="activeTab = '<?php echo e($tab); ?>'"
                            :class="activeTab === '<?php echo e($tab); ?>'
                                ? 'border-b-2 border-green-600 text-green-700 dark:text-green-400 bg-white dark:bg-gray-900 font-semibold'
                                : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"
                            class="flex-shrink-0 px-4 py-2.5 text-xs transition-colors duration-150 whitespace-nowrap focus:outline-none"
                        >
                            <?php echo e($tab); ?>

                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>

            
            <div class="p-4 bg-white dark:bg-gray-900 min-h-[180px]">

                
                <div x-show="activeTab === 'Description'" style="display:none;">
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                        <?php echo e($info['description']); ?>

                    </p>
                </div>

                
                <div x-show="activeTab === 'Symptoms'" style="display:none;">
                    <ul class="space-y-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $info['symptoms']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span><?php echo e($item); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </ul>
                </div>

                
                <div x-show="activeTab === 'Causes / Risk Factors'" style="display:none;">
                    <ul class="space-y-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $info['causes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <span><?php echo e($item); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </ul>
                </div>

                
                <div x-show="activeTab === 'Impact'" style="display:none;">
                    <ul class="space-y-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $info['impact']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span><?php echo e($item); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </ul>
                </div>

                
                <div x-show="activeTab === 'Seven-Day Action Plan'" style="display:none;">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 italic">
                        Tailored daily schedule based on severity. For Mild cases Day 4 chemical treatment may be skipped; for Severe cases all steps are critical.
                    </p>
                    <div class="space-y-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $info['seven_day_plan']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="flex gap-3 items-start">
                                <span class="flex-shrink-0 inline-flex items-center justify-center w-16 text-center px-2 py-1 rounded-md text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                    <?php echo e($step['day']); ?>

                                </span>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800 dark:text-gray-200"><?php echo e($step['title']); ?></p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5"><?php echo e($step['detail']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                
                <div x-show="activeTab === 'Immediate Response'" style="display:none;">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-xs font-semibold text-red-600 dark:text-red-400">Take these actions within 24 hours of detection</span>
                    </div>
                    <ul class="space-y-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $info['immediate_response']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <span class="flex-shrink-0 inline-flex items-center justify-center w-5 h-5 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">
                                    <?php echo e($idx + 1); ?>

                                </span>
                                <span><?php echo e($item); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </ul>
                </div>

                
                <div x-show="activeTab === 'Long-term Strategy'" style="display:none;">
                    <ul class="space-y-2">
                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $info['long_term']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="flex items-start gap-2 text-sm text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><?php echo e($item); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                    </ul>
                </div>

            </div>
        </div>

    <?php else: ?>
        
        <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 bg-gray-50 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                No reference data available for "<strong><?php echo e($pestName); ?></strong>". Please consult the expert recommendation below.
            </p>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <?php
        $aiJson = null;
        if ($record->ai_recommendation) {
            $raw = $record->ai_recommendation;
            // Strip markdown code fences Gemini may include
            $raw = preg_replace('/^```(?:json)?\s*/m', '', $raw);
            $raw = preg_replace('/\s*```$/m', '', $raw);
            $raw = trim($raw);
            // Extract JSON object if surrounded by extra text
            if (preg_match('/\{[\s\S]+\}/s', $raw, $m)) {
                $raw = $m[0];
            }
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $aiJson = $decoded;
            }
        }
        $aiTabs = ['Diagnosis', 'Immediate Actions', 'Treatment Protocol', 'Schedule', 'Products Needed', 'Prevention', 'Warnings'];
    ?>

    <!--[if BLOCK]><![endif]--><?php if($aiJson || $record->ai_recommendation): ?>
        <?php
            $urgency   = $aiJson['urgency'] ?? null;
            $urgBgMap  = ['Low' => '#dcfce7', 'Moderate' => '#fef9c3', 'High' => '#ffedd5', 'Critical' => '#fee2e2'];
            $urgTxtMap = ['Low' => '#166534', 'Moderate' => '#854d0e', 'High' => '#9a3412', 'Critical' => '#991b1b'];
            $urgBg     = $urgBgMap[$urgency]  ?? '#ede9fe';
            $urgTxt    = $urgTxtMap[$urgency] ?? '#5b21b6';
        ?>

        
        <div class="rounded-xl overflow-hidden border border-gray-300 dark:border-gray-600 shadow-md">

            
            <div class="flex items-center justify-between bg-gray-700 dark:bg-gray-900 px-4 py-2">
                <div class="flex items-center gap-2 text-white text-xs font-medium">
                    <svg class="w-4 h-4 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                    </svg>
                    <span>AI Recommendation — <?php echo e($record->pest ?? 'Pest & Disease'); ?></span>
                    <!--[if BLOCK]><![endif]--><?php if($urgency): ?>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                              style="background:<?php echo e($urgBg); ?>; color:<?php echo e($urgTxt); ?>">
                            <?php echo e($urgency); ?> Urgency
                        </span>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div class="flex items-center gap-2">
                    
                    <button
                        type="button"
                        onclick="
                            var v = document.getElementById('ai-pdf-viewer-<?php echo e($record->id); ?>');
                            var b = document.getElementById('ai-pdf-expand-btn-<?php echo e($record->id); ?>');
                            if (v.style.maxHeight === 'none') {
                                v.style.maxHeight = '540px';
                                b.textContent = 'Expand';
                            } else {
                                v.style.maxHeight = 'none';
                                b.textContent = 'Collapse';
                            }"
                        id="ai-pdf-expand-btn-<?php echo e($record->id); ?>"
                        class="text-xs text-gray-300 hover:text-white bg-gray-600 hover:bg-gray-500 px-2 py-1 rounded transition">
                        Expand
                    </button>
                    
                    <button
                        type="button"
                        @click.stop.prevent="printReport()"
                        class="text-xs text-gray-300 hover:text-white bg-gray-600 hover:bg-gray-500 px-2 py-1 rounded transition flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 4v3H4a2 2 0 00-2 2v3a2 2 0 002 2h1v2a2 2 0 002 2h6a2 2 0 002-2v-2h1a2 2 0 002-2V9a2 2 0 00-2-2h-1V4a2 2 0 00-2-2H7a2 2 0 00-2 2zm8 0H7v3h6V4zm0 8H7v4h6v-4z" clip-rule="evenodd"/>
                        </svg>
                        Print
                    </button>
                </div>
            </div>

            
            <div class="bg-gray-200 dark:bg-gray-700 px-4 py-4 overflow-y-auto"
                 style="max-height: 540px;"
                 id="ai-pdf-viewer-<?php echo e($record->id); ?>">
                <div class="bg-white shadow-lg rounded mx-auto"
                     style="max-width: 720px; min-height: 420px; padding: 28px 32px; font-family: 'Segoe UI', Arial, sans-serif;">

                    <!--[if BLOCK]><![endif]--><?php if($aiJson): ?>
                        
                        <div style="border-bottom: 2px solid #7c3aed; padding-bottom: 12px; margin-bottom: 18px;">
                            <div style="font-size: 10px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; color: #9ca3af; margin-bottom: 4px;">
                                CAFARM — AI-Generated Pest &amp; Disease Recommendation
                            </div>
                            <div style="font-size: 17px; font-weight: 800; color: #4c1d95;">
                                <?php echo e($record->pest ?? 'Pest & Disease'); ?>

                                <!--[if BLOCK]><![endif]--><?php if(!empty($aiJson['urgency'])): ?>
                                    <span style="font-size: 11px; font-weight: 600; background: <?php echo e($urgBg); ?>; color: <?php echo e($urgTxt); ?>; padding: 2px 10px; border-radius: 20px; margin-left: 8px; vertical-align: middle;">
                                        <?php echo e($aiJson['urgency']); ?> Urgency
                                    </span>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                            <div style="font-size: 11px; color: #6b7280; margin-top: 4px;">
                                Powered by Google Gemini AI &nbsp;·&nbsp; Generated: <?php echo e(now()->format('F d, Y')); ?>

                            </div>
                        </div>

                        
                        <!--[if BLOCK]><![endif]--><?php if(!empty($aiJson['diagnosis'])): ?>
                            <div style="margin-bottom: 16px;">
                                <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #7c3aed; border-bottom: 1px solid #ede9fe; padding-bottom: 4px; margin-bottom: 8px;">
                                    Diagnosis
                                </div>
                                <div style="font-size: 12px; line-height: 1.7; color: #374151; background: #faf5ff; border-left: 3px solid #8b5cf6; padding: 10px 14px; border-radius: 0 6px 6px 0;">
                                    <?php echo e($aiJson['diagnosis']); ?>

                                </div>
                                <!--[if BLOCK]><![endif]--><?php if(!empty($aiJson['urgency_reason'])): ?>
                                    <div style="margin-top: 8px; font-size: 11px; color: <?php echo e($urgTxt); ?>; background: <?php echo e($urgBg); ?>; padding: 6px 12px; border-radius: 5px;">
                                        ⚠ <?php echo e($aiJson['urgency_reason']); ?>

                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <!--[if BLOCK]><![endif]--><?php if(!empty($aiJson['immediate_actions'])): ?>
                            <div style="margin-bottom: 16px;">
                                <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #dc2626; border-bottom: 1px solid #fee2e2; padding-bottom: 4px; margin-bottom: 8px;">
                                    ⚡ Immediate Actions <span style="font-weight:400; text-transform:none; font-size:10px;">(within 24 hours)</span>
                                </div>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $aiJson['immediate_actions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div style="display: flex; gap: 10px; align-items: flex-start; margin: 6px 0;">
                                        <span style="flex-shrink:0; background:#fee2e2; color:#991b1b; font-size:9px; font-weight:800; padding:2px 7px; border-radius:4px; min-width:50px; text-align:center;">
                                            Step <?php echo e($idx + 1); ?>

                                        </span>
                                        <span style="font-size: 12px; color: #374151; line-height: 1.5;"><?php echo e($item); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <!--[if BLOCK]><![endif]--><?php if(!empty($aiJson['treatment_protocol'])): ?>
                            <div style="margin-bottom: 16px;">
                                <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #7c3aed; border-bottom: 1px solid #ede9fe; padding-bottom: 4px; margin-bottom: 8px;">
                                    Treatment Protocol
                                </div>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $aiJson['treatment_protocol']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div style="display: flex; gap: 10px; align-items: flex-start; margin: 6px 0;">
                                        <span style="flex-shrink:0; background:#ede9fe; color:#5b21b6; font-size:9px; font-weight:800; padding:2px 7px; border-radius:4px; min-width:80px; text-align:center;">
                                            <?php echo e($step['step'] ?? ''); ?>

                                        </span>
                                        <span style="font-size: 12px; color: #374151; line-height: 1.5;"><?php echo e($step['detail'] ?? ''); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <!--[if BLOCK]><![endif]--><?php if(!empty($aiJson['schedule'])): ?>
                            <div style="margin-bottom: 16px;">
                                <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #166534; border-bottom: 1px solid #dcfce7; padding-bottom: 4px; margin-bottom: 8px;">
                                    📅 Schedule
                                </div>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $aiJson['schedule']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div style="display: flex; gap: 10px; align-items: flex-start; margin: 6px 0;">
                                        <span style="flex-shrink:0; background:#dcfce7; color:#166534; font-size:9px; font-weight:800; padding:2px 7px; border-radius:4px; min-width:60px; text-align:center; white-space:nowrap;">
                                            <?php echo e($entry['day'] ?? ''); ?>

                                        </span>
                                        <span style="font-size: 12px; color: #374151; line-height: 1.5;"><?php echo e($entry['task'] ?? ''); ?></span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <!--[if BLOCK]><![endif]--><?php if(!empty($aiJson['products_needed'])): ?>
                            <div style="margin-bottom: 16px;">
                                <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #7c3aed; border-bottom: 1px solid #ede9fe; padding-bottom: 4px; margin-bottom: 8px;">
                                    ⚗ Products Needed
                                </div>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $aiJson['products_needed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prod): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $ptBg  = ['Biological' => '#dcfce7', 'Chemical' => '#fee2e2', 'Organic' => '#fef3c7'][$prod['type'] ?? ''] ?? '#f3f4f6';
                                        $ptTxt = ['Biological' => '#166534', 'Chemical' => '#991b1b', 'Organic' => '#92400e'][$prod['type'] ?? ''] ?? '#374151';
                                    ?>
                                    <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:6px; padding:8px 12px; margin:5px 0;">
                                        <div style="font-size:12px; font-weight:600; color:#111827;">
                                            <?php echo e($prod['product'] ?? ''); ?>

                                            <span style="font-size:9px; font-weight:700; background:<?php echo e($ptBg); ?>; color:<?php echo e($ptTxt); ?>; padding:1px 6px; border-radius:10px; margin-left:6px;">
                                                <?php echo e($prod['type'] ?? ''); ?>

                                            </span>
                                        </div>
                                        <div style="font-size:11px; color:#6b7280; margin-top:3px;">
                                            Rate: <?php echo e($prod['rate'] ?? '—'); ?>

                                            <!--[if BLOCK]><![endif]--><?php if(!empty($prod['notes'])): ?> &bull; <?php echo e($prod['notes']); ?> <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <!--[if BLOCK]><![endif]--><?php if(!empty($aiJson['prevention'])): ?>
                            <div style="margin-bottom: 16px;">
                                <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #166534; border-bottom: 1px solid #dcfce7; padding-bottom: 4px; margin-bottom: 8px;">
                                    🛡 Prevention Strategies
                                </div>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $aiJson['prevention']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div style="display:flex; gap:8px; align-items:flex-start; margin:5px 0; font-size:12px; color:#374151; line-height:1.5;">
                                        <span style="color:#7c3aed; font-weight:700; flex-shrink:0;">✔</span>
                                        <?php echo e($item); ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        
                        <!--[if BLOCK]><![endif]--><?php if(!empty($aiJson['warnings'])): ?>
                            <div style="margin-bottom: 8px;">
                                <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #dc2626; border-bottom: 1px solid #fee2e2; padding-bottom: 4px; margin-bottom: 8px;">
                                    ⚠ Warnings
                                </div>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $aiJson['warnings']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div style="display:flex; gap:8px; align-items:flex-start; margin:5px 0; font-size:12px; color:#374151; line-height:1.5;">
                                        <span style="color:#dc2626; font-weight:700; flex-shrink:0;">⚠</span>
                                        <?php echo e($item); ?>

                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    <?php else: ?>
                        
                        <pre style="font-family: 'Courier New', monospace; font-size: 11px; line-height: 1.6; color: #374151; white-space: pre-wrap; overflow-x: auto;"><?php echo e($record->ai_recommendation); ?></pre>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                    
                    <div style="margin-top: 24px; border-top: 1px solid #e5e7eb; padding-top: 8px; font-size: 10px; color: #9ca3af; text-align: center;">
                        CAFARM — Coffee Agri-Farming Management System &nbsp;·&nbsp; AI-Assisted Recommendation
                    </div>
                </div>
            </div>

            
            <div class="bg-gray-700 dark:bg-gray-900 px-4 py-1 text-center text-xs text-gray-400">
                Powered by Google Gemini AI &nbsp;|&nbsp; Generated on:
                <span class="font-medium text-gray-200"><?php echo e($record->updated_at?->format('M d, Y') ?? now()->format('M d, Y')); ?></span>
            </div>

        </div>

    <?php else: ?>
        <div class="border border-dashed border-purple-200 dark:border-purple-800 rounded-xl p-4 flex items-center gap-3 bg-purple-50/50 dark:bg-purple-900/10">
            <svg class="w-5 h-5 text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
            </svg>
            <p class="text-xs text-purple-600 dark:text-purple-400">
                No AI recommendation yet. Click <strong>Generate AI Recommendation</strong> below to get an AI-assisted management plan for this detection.
            </p>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    
    <!--[if BLOCK]><![endif]--><?php if($record->expert_comments || $record->farmer_action): ?>
        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden shadow-sm">
            
            <div class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Conversation Thread</span>
                <?php $isApproved = $record->validation_status === 'approved'; ?>
                <!--[if BLOCK]><![endif]--><?php if($record->validation_status !== 'pending'): ?>
                    <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold
                        <?php echo e($isApproved
                            ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'
                            : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'); ?>">
                        <?php echo e($isApproved ? 'Approved' : 'Disapproved'); ?>

                    </span>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>

            
            <div class="max-h-64 overflow-y-auto space-y-3 p-3 bg-gray-50 dark:bg-gray-800/50">

                
                <!--[if BLOCK]><![endif]--><?php if($record->expert_comments): ?>
                    <div class="flex justify-start">
                        <div class="max-w-[80%] bg-blue-100 dark:bg-blue-900/40 rounded-lg p-2.5">
                            <div class="flex items-center gap-1.5 mb-1">
                                <svg class="w-3 h-3 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                <span class="text-xs font-semibold text-blue-800 dark:text-blue-300">
                                    <?php echo e($record->validator?->name ?? 'Expert'); ?>

                                </span>
                                <span class="inline-block px-1.5 py-0.5 text-[10px] bg-blue-200 dark:bg-blue-800 text-blue-700 dark:text-blue-300 rounded">
                                    <?php echo e($isApproved ? 'Recommendation' : 'Comments'); ?>

                                </span>
                            </div>
                            <p class="text-sm text-gray-900 dark:text-gray-100 leading-relaxed"><?php echo e($record->expert_comments); ?></p>
                            <!--[if BLOCK]><![endif]--><?php if($record->validated_at): ?>
                                <span class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 block">
                                    <?php echo e($record->validated_at->format('M d, Y · h:i A')); ?>

                                </span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                
                <!--[if BLOCK]><![endif]--><?php if($record->farmer_action): ?>
                    <div class="flex justify-end">
                        <div class="max-w-[80%] bg-amber-100 dark:bg-amber-900/40 rounded-lg p-2.5">
                            <div class="flex items-center justify-end gap-1.5 mb-1">
                                <span class="inline-block px-1.5 py-0.5 text-[10px] bg-amber-200 dark:bg-amber-800 text-amber-700 dark:text-amber-300 rounded">
                                    Action Taken
                                </span>
                                <span class="text-xs font-semibold text-amber-800 dark:text-amber-300">
                                    <?php echo e($record->farmer
                                        ? trim(($record->farmer->firstname ?? '') . ' ' . ($record->farmer->lastname ?? ''))
                                        : 'Farmer'); ?>

                                </span>
                                <svg class="w-3 h-3 text-amber-600 dark:text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-900 dark:text-gray-100 leading-relaxed"><?php echo e($record->farmer_action); ?></p>
                            <!--[if BLOCK]><![endif]--><?php if($record->farmer_action_date): ?>
                                <span class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 block text-right">
                                    <?php echo e($record->farmer_action_date->format('M d, Y · h:i A')); ?>

                                    <span class="italic ml-1">via CAFARM App</span>
                                </span>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    </div>
                <?php elseif($record->expert_comments): ?>
                    
                    <p class="text-xs text-center text-gray-400 dark:text-gray-500 py-1 italic">
                        Awaiting farmer's response via mobile app…
                    </p>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->


    
    <!--[if BLOCK]><![endif]--><?php if($record->image_path): ?>
        <div
            x-show="showLightbox"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="showLightbox = false"
            @keydown.escape.window="showLightbox = false"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 p-4"
            style="display: none;"
        >
            <button
                @click="showLightbox = false"
                class="absolute top-4 right-4 text-white hover:text-gray-300 transition-colors"
            >
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            <img
                src="<?php echo e(Storage::disk('public')->url($record->image_path)); ?>"
                alt="Detection Image - Full Size"
                class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl"
                @click.stop
            />
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black/60 text-white px-4 py-2 rounded-lg text-sm">
                <?php echo e($record->pest); ?> - <?php echo e(ucfirst($record->type ?? 'N/A')); ?>

            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH /var/www/html/CapstoneProject/resources/views/filament/resources/pest-and-disease/view-modal.blade.php ENDPATH**/ ?>