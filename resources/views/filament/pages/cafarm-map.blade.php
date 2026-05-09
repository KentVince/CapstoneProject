<x-filament::page>

    {{-- MODERN TOOLBAR --}}
    <div class="mb-4 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-4 space-y-4">

        {{-- ROW 1: FILTERS --}}
        <div class="flex flex-wrap items-center gap-3">

            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Filters</span>
            </div>

            <select id="municipalityFilter"
                    onchange="filterByMunicipality()"
                    class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white text-sm py-2 px-3 min-w-[180px] focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                <option value="all">All Municipalities</option>
            </select>

            <select id="barangayFilter"
                    onchange="filterByBarangay()"
                    class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white text-sm py-2 px-3 min-w-[180px] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                <option value="all">All Barangays</option>
            </select>

            <select id="farmFilter"
                    onchange="filterByFarm()"
                    class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white text-sm py-2 px-3 min-w-[180px] focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition">
                <option value="all">All Farms</option>
            </select>

            <select id="categoryFilter"
                    onchange="handleCategoryChange()"
                    class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white text-sm py-2 px-3 min-w-[200px] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                <option value="all">All Categories</option>
                <option value="pest">1. Pests</option>
                <option value="disease">2. Diseases</option>
                <option value="soil">3. Soil Fertility</option>
            </select>

            <select id="itemFilter"
                    onchange="filterMarkers()"
                    class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white text-sm py-2 px-3 min-w-[200px] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition"
                    style="display: none;">
                <option value="all">All Items</option>
            </select>

            <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-xs font-semibold text-emerald-700 dark:text-emerald-300">
                    <span id="countNumber">0</span> locations
                </span>
            </div>

        </div>

        {{-- DIVIDER --}}
        <div class="border-t border-gray-100 dark:border-gray-700"></div>

        {{-- ROW 2: LEGENDS & BOUNDARIES --}}
        <div class="flex flex-wrap items-center gap-x-6 gap-y-3">

            {{-- MARKER TYPE LEGEND --}}
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Markers</span>
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                    <img src="/images/pest-icon.png" alt="Pest" class="w-4 h-4">
                    <span class="text-xs font-medium text-red-700 dark:text-red-300">Pest</span>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                    <img src="/images/disease-icon.png" alt="Disease" class="w-4 h-4">
                    <span class="text-xs font-medium text-blue-700 dark:text-blue-300">Disease</span>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                    <img src="/images/soil-icon.png" alt="Soil Fertility" class="w-4 h-4">
                    <span class="text-xs font-medium text-green-700 dark:text-green-300">Soil Fertility</span>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800">
                    <img src="/images/farm.png" alt="Farm" class="w-4 h-4">
                    <span class="text-xs font-medium text-yellow-700 dark:text-yellow-300">Farm</span>
                </div>
            </div>

            {{-- HEATMAP TOGGLE --}}
            <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border cursor-pointer transition-all
                          border-orange-200 dark:border-orange-800 bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 dark:hover:bg-orange-900/40">
                <input type="checkbox" id="toggleHeatmap" onchange="toggleHeatmapView()"
                       class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-3.5 h-3.5">
                <span class="w-2.5 h-2.5 rounded-full" style="background: radial-gradient(circle, #ff4500, #ff8c00, #ffd700);"></span>
                <span class="text-xs font-medium text-orange-700 dark:text-orange-300">Heatmap</span>
            </label>

            {{-- VERTICAL DIVIDER --}}
            <div class="hidden sm:block w-px h-6 bg-gray-200 dark:bg-gray-600"></div>

            {{-- BOUNDARY TOGGLES --}}
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Boundaries</span>

                <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border cursor-pointer transition-all
                              border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40">
                    <input type="checkbox" id="toggleProvincial" onchange="toggleBoundaryLayer('provincial')"
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 w-3.5 h-3.5" checked>
                    <span class="w-2.5 h-2.5 rounded-sm" style="background: #3b82f6; border: 1.5px solid #2563eb;"></span>
                    <span class="text-xs font-medium text-blue-700 dark:text-blue-300">Provincial</span>
                </label>

                <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border cursor-pointer transition-all
                              border-orange-200 dark:border-orange-800 bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 dark:hover:bg-orange-900/40">
                    <input type="checkbox" id="toggleMunicipal" onchange="toggleBoundaryLayer('municipal')"
                           class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-3.5 h-3.5" checked>
                    <span class="w-2.5 h-2.5 rounded-sm" style="background: #f97316; border: 1.5px solid #ea580c;"></span>
                    <span class="text-xs font-medium text-orange-700 dark:text-orange-300">Municipal</span>
                </label>

                <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border cursor-pointer transition-all
                              border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/40">
                    <input type="checkbox" id="toggleBarangay" onchange="toggleBoundaryLayer('barangay')"
                           class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-3.5 h-3.5" checked>
                    <span class="w-2.5 h-2.5 rounded-sm" style="background: #10b981; border: 1.5px solid #059669;"></span>
                    <span class="text-xs font-medium text-emerald-700 dark:text-emerald-300">Barangay</span>
                </label>
            </div>

        </div>

    </div>



    {{-- MAP --}}
    <div id="map"
         style="height: 800px; width: 100%;"
         class="rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
    </div>

    {{-- BUFFER ZONE LEGEND --}}
    <div id="bufferLegend" class="mt-4 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow p-4" style="display:none;">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h4 class="text-sm font-bold text-gray-800 dark:text-white">Buffer Zone Analysis</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Farm: <span id="bufferFarmName" class="font-semibold"></span></p>
            </div>
            <button onclick="clearBufferZones()" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">✕ Clear</button>
        </div>

        {{-- RISK STATUS BANNER --}}
        <div id="bufferStatusBanner" class="mb-3 rounded-lg px-3 py-2.5 flex items-start gap-2.5 border">
            <span id="bufferStatusIcon" class="text-lg leading-none flex-shrink-0">✅</span>
            <div class="flex-1 min-w-0">
                <div id="bufferStatusTitle" class="text-xs font-bold"></div>
                <div id="bufferStatusSubtitle" class="text-[11px] mt-0.5"></div>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div class="rounded-lg border border-red-200 bg-red-50 dark:bg-red-900/20 dark:border-red-800 p-3">
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-block w-3 h-3 rounded-full" style="background:#dc2626;"></span>
                    <span class="text-xs font-bold text-red-800 dark:text-red-300">High-Risk</span>
                </div>
                <div class="text-[10px] text-red-600 dark:text-red-400 mb-2">0 – 500 m · Immediate Alert</div>
                <div class="text-xl font-bold text-red-700 dark:text-red-300"><span id="bufZ1Total">0</span></div>
                <div class="text-[10px] text-gray-500">cases &nbsp;·&nbsp; <span id="bufZ1High" class="font-semibold text-red-600">0</span> high severity</div>
            </div>
            <div class="rounded-lg border border-orange-200 bg-orange-50 dark:bg-orange-900/20 dark:border-orange-800 p-3">
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-block w-3 h-3 rounded-full" style="background:#ea580c;"></span>
                    <span class="text-xs font-bold text-orange-800 dark:text-orange-300">Moderate-Risk</span>
                </div>
                <div class="text-[10px] text-orange-600 dark:text-orange-400 mb-2">500 m – 1 km · Notify &amp; Monitor</div>
                <div class="text-xl font-bold text-orange-700 dark:text-orange-300"><span id="bufZ2Total">0</span></div>
                <div class="text-[10px] text-gray-500">cases &nbsp;·&nbsp; <span id="bufZ2High" class="font-semibold text-orange-600">0</span> high severity</div>
            </div>
            <div class="rounded-lg border border-yellow-200 bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-800 p-3">
                <div class="flex items-center gap-2 mb-1">
                    <span class="inline-block w-3 h-3 rounded-full" style="background:#ca8a04;"></span>
                    <span class="text-xs font-bold text-yellow-800 dark:text-yellow-300">Extended-Risk</span>
                </div>
                <div class="text-[10px] text-yellow-600 dark:text-yellow-400 mb-2">1 – 2 km · Surveillance</div>
                <div class="text-xl font-bold text-yellow-700 dark:text-yellow-300"><span id="bufZ3Total">0</span></div>
                <div class="text-[10px] text-gray-500">cases &nbsp;·&nbsp; <span id="bufZ3High" class="font-semibold text-yellow-600">0</span> high severity</div>
            </div>
        </div>

        {{-- INFECTED FARMS NEARBY --}}
        <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <h5 class="text-xs font-bold text-red-700 dark:text-red-300 uppercase tracking-wider">⚠️ Infected Farms Nearby</h5>
                <span class="text-[10px] text-gray-500 dark:text-gray-400">Total: <span id="bufInfectedTotal" class="font-semibold">0</span></span>
            </div>
            <div id="bufferInfectedList" class="space-y-2 max-h-48 overflow-y-auto pr-1">
                <p class="text-xs text-gray-400 italic">No infected farms detected.</p>
            </div>
        </div>

        {{-- HEALTHY FARMS IN ZONE --}}
        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <h5 class="text-xs font-bold text-emerald-700 dark:text-emerald-300 uppercase tracking-wider">🌱 Healthy Farms in Zone</h5>
                <span class="text-[10px] text-gray-500 dark:text-gray-400">Total: <span id="bufHealthyTotal" class="font-semibold">0</span></span>
            </div>
            <div id="bufferHealthyList" class="space-y-2 max-h-48 overflow-y-auto pr-1">
                <p class="text-xs text-gray-400 italic">No other farms in zone.</p>
            </div>
        </div>
    </div>

    {{-- HEATMAP LEGEND --}}
    <div class="mt-4">
        <div class="flex items-center gap-3">
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap">Heatmap:</span>
            <div class="flex-1 max-w-sm">
                <div class="h-4 rounded-lg overflow-hidden" style="background: linear-gradient(to right, #ffffb2 0%, #fecc5c 25%, #fd8d3c 50%, #f03b20 75%, #bd0026 100%);"></div>
                <div class="flex justify-between mt-1">
                    <span class="text-[10px] text-gray-600 dark:text-gray-400">Low</span>
                    <span class="text-[10px] text-gray-600 dark:text-gray-400">Medium</span>
                    <span class="text-[10px] text-gray-600 dark:text-gray-400">High</span>
                </div>
            </div>
        </div>
    </div>

    {{-- DATA TABLE --}}
    <div class="mt-6" id="dataTableContainer" style="display: none;">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Filtered Data</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Showing <span id="tableCount" class="font-semibold text-emerald-600 dark:text-emerald-400">0</span> records
                </p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead id="tableHead" class="bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 uppercase text-xs">
                        <!-- Dynamic headers will be inserted here -->
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Dynamic rows will be inserted here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-filament::page>



{{-- ================= STYLES ================= --}}
@push('styles')

<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>

.leaflet-popup-content { margin: 0; padding: 0; }
.leaflet-popup-content-wrapper { padding: 0; border-radius: 12px; overflow: hidden; }
.buffer-tooltip {
    background: rgba(15,23,42,0.88);
    color: #f1f5f9;
    border: none;
    border-radius: 8px;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 500;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}
.buffer-tooltip::before { display: none; }

.popup-container { min-width: 280px; max-width: 320px; }

.popup-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
    cursor: pointer;
}

.popup-content { padding: 12px; }

.popup-title {
    font-weight: 700;
    font-size: 14px;
    color: #065f46;
    margin-bottom: 8px;
}

.popup-detail {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    padding: 4px 0;
    border-bottom: 1px solid #e5e7eb;
}

.popup-detail:last-child { border-bottom: none; }

.popup-label { color: #6b7280; font-weight: 500; }
.popup-value { color: #111827; font-weight: 600; }

.severity-high { color: #dc2626; }
.severity-medium { color: #d97706; }
.severity-low { color: #16a34a; }

.status-pending { background: #fef3c7; color: #92400e; }
.status-approved { background: #d1fae5; color: #065f46; }
.status-disapproved { background: #fee2e2; color: #991b1b; }

.status-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 9999px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
}

.no-image-placeholder {
    width: 100%;
    height: 100px;
    background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 12px;
}

.boundary-tooltip {
    background: rgba(0, 0, 0, 0.75);
    color: #fff;
    border: none;
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 12px;
    font-weight: 500;
    box-shadow: 0 2px 6px rgba(0,0,0,0.3);
}

.boundary-tooltip::before {
    border-top-color: rgba(0, 0, 0, 0.75);
}

</style>

@endpush



{{-- ================= SCRIPTS ================= --}}
@push('scripts')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const allPestCases = @json($this->getPestAndDiseaseCases());
    const soilAnalysisData = @json($this->getSoilAnalysisLocations());
    const pestsList = @json($this->getPestsList());
    const diseasesList = @json($this->getDiseasesList());
    const allFarms = @json($this->getAllFarms());


    // Davao de Oro province bounds (with padding for comfortable viewing)
    const provinceBounds = L.latLngBounds(
        [7.05, 125.65],  // Southwest corner
        [8.05, 126.55]   // Northeast corner
    );

    const map = L.map('map', {
        maxBounds: provinceBounds,
        maxBoundsViscosity: 1.0,
        minZoom: 10,
    }).setView([7.56, 126.10], 11);


    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);


    const pestMarkers = L.layerGroup();
    let heatLayer = null;
    let highlightLayer = null;

    // GeoJSON data cache
    let municipalGeoData = null;
    let barangayGeoData = null;


    // ================= HELPER FUNCTIONS =================

    function getSeverityColor(severity) {
        if (!severity) return '#16a34a';
        switch (severity.toLowerCase()) {
            case 'high':
            case 'severe': return '#dc2626';
            case 'medium': return '#d97706';
            default: return '#16a34a';
        }
    }

    function getSeverityClass(severity) {
        if (!severity) return 'severity-low';
        switch (severity.toLowerCase()) {
            case 'high':
            case 'severe': return 'severity-high';
            case 'medium': return 'severity-medium';
            default: return 'severity-low';
        }
    }

    function getStatusClass(status) {
        if (!status) return 'status-pending';
        switch (status.toLowerCase()) {
            case 'approved': return 'status-approved';
            case 'disapproved': return 'status-disapproved';
            default: return 'status-pending';
        }
    }

    function getSeverityWeight(severity) {
        if (!severity) return 0.6;
        switch (severity.toLowerCase()) {
            case 'high':
            case 'severe': return 1.0;
            case 'medium': return 0.85;
            default: return 0.6;
        }
    }

    const pestIcon = L.icon({
        iconUrl: '/images/pest-icon.png',
        iconSize: [30, 41],
        iconAnchor: [15, 41],
        popupAnchor: [0, -35],
    });

    const diseaseIcon = L.icon({
        iconUrl: '/images/disease-icon.png',
        iconSize: [30, 41],
        iconAnchor: [15, 41],
        popupAnchor: [0, -35],
    });

    const soilIcon = L.icon({
        iconUrl: '/images/soil-icon.png',
        iconSize: [30, 41],
        iconAnchor: [15, 41],
        popupAnchor: [0, -35],
    });

    const farmIcon = L.icon({
        iconUrl: '/images/farm.png',
        iconSize: [35, 35],
        iconAnchor: [17, 17],
        popupAnchor: [0, -17],
    });

    function createFarmPopupContent(farm) {
        return `
        <div class="popup-container">
            <div class="no-image-placeholder" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                <span style="color: black; font-weight: bold; font-size: 15px;">${farm.name ?? 'Farm Location'}</span>
            </div>
            <div class="popup-content">
                <div class="popup-detail">
                    <span class="popup-label">Barangay</span>
                    <span class="popup-value">${farm.barangay_name ?? farm.barangay ?? 'N/A'}</span>
                </div>
                <div class="popup-detail">
                    <span class="popup-label">Municipality</span>
                    <span class="popup-value">${farm.municipality_name ?? farm.municipality ?? 'N/A'}</span>
                </div>
                <div class="popup-detail">
                    <span class="popup-label">Soil Type</span>
                    <span class="popup-value">${farm.soil_type ?? 'N/A'}</span>
                </div>
                <div class="popup-detail">
                    <span class="popup-label">Crop Variety</span>
                    <span class="popup-value">${farm.crop_variety ?? 'N/A'}</span>
                </div>
                <div class="popup-detail">
                    <span class="popup-label">Crop Area</span>
                    <span class="popup-value">${farm.crop_area ? farm.crop_area + ' ha' : 'N/A'}</span>
                </div>
                <div class="popup-detail">
                    <span class="popup-label">Coordinates</span>
                    <span class="popup-value">${parseFloat(farm.latitude).toFixed(6)}, ${parseFloat(farm.longitude).toFixed(6)}</span>
                </div>
                <div style="margin-top:10px; display:flex; gap:6px; flex-direction:column;">
                    <a href="/admin/pest-and-diseases?farm_id=${farm.id}"
                       style="display:block; text-align:center; padding:6px 12px; background:#dc2626; color:white; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none;">
                        View Pest &amp; Disease
                    </a>
                    <a href="/admin/soil-analyses?farm_id=${farm.id}"
                       style="display:block; text-align:center; padding:6px 12px; background:#16a34a; color:white; border-radius:6px; font-size:12px; font-weight:600; text-decoration:none;">
                        View Soil Analysis
                    </a>
                    <button type="button" onclick="showFarmDangerZone(${farm.id})"
                       style="display:block; text-align:center; padding:6px 12px; background:#ea580c; color:white; border:none; border-radius:6px; font-size:12px; font-weight:600; cursor:pointer;">
                        Show Danger Zone
                    </button>
                </div>
            </div>
        </div>`;
    }

    function getTypeIcon(type) {
        if (!type) return pestIcon;
        if (type.toLowerCase() === 'disease') return diseaseIcon;
        if (type.toLowerCase() === 'soil') return soilIcon;
        if (type.toLowerCase() === 'farm') return farmIcon;
        return pestIcon;
    }

    function createPopupContent(item) {
        const image = item.image_url
            ? `<img src="${item.image_url}" class="popup-image"
                onclick="window.open('${item.image_url}','_blank')">`
            : `<div class="no-image-placeholder">No Image</div>`;

        const date = item.date_detected
            ? new Date(item.date_detected).toLocaleDateString()
            : 'N/A';

        return `
        <div class="popup-container">
            ${image}
            <div class="popup-content">
                <div class="popup-title">${item.pest ?? 'Unknown'}</div>
                <div class="popup-detail">
                    <span class="popup-label">Confidence</span>
                    <span class="popup-value">${item.confidence ?? 'N/A'}%</span>
                </div>
                <div class="popup-detail">
                    <span class="popup-label">Severity</span>
                    <span class="popup-value ${getSeverityClass(item.severity)}">
                        ${item.severity ?? 'N/A'}
                    </span>
                </div>
                <div class="popup-detail">
                    <span class="popup-label">Date</span>
                    <span class="popup-value">${date}</span>
                </div>
                <div class="popup-detail">
                    <span class="popup-label">Status</span>
                    <span class="status-badge ${getStatusClass(item.validation_status)}">
                        ${item.validation_status ?? 'pending'}
                    </span>
                </div>
            </div>
        </div>`;
    }

    function createSoilPopupContent(item) {
        const date = item.date_collected
            ? new Date(item.date_collected).toLocaleDateString()
            : 'N/A';

        return `
        <div class="popup-container">
            <div class="no-image-placeholder" style="background: linear-gradient(135deg, #86efac 0%, #10b981 100%);">
                <span style="color: white; font-weight: bold;">Soil Analysis</span>
            </div>
            <div class="popup-content">
                <div class="popup-title">${item.farm_name ?? 'Unknown Farm'}</div>
                <div class="popup-detail">
                    <span class="popup-label">Date Collected</span>
                    <span class="popup-value">${date}</span>
                </div>
                <div class="popup-detail">
                    <span class="popup-label">pH Level</span>
                    <span class="popup-value">${item.ph_level ?? 'N/A'}</span>
                </div>
                <div class="popup-detail">
                    <span class="popup-label">Nitrogen</span>
                    <span class="popup-value">${item.nitrogen ?? 'N/A'}%</span>
                </div>
                <div class="popup-detail">
                    <span class="popup-label">Phosphorus</span>
                    <span class="popup-value">${item.phosphorus ?? 'N/A'} ppm</span>
                </div>
                <div class="popup-detail">
                    <span class="popup-label">Potassium</span>
                    <span class="popup-value">${item.potassium ?? 'N/A'} ppm</span>
                </div>
                <div class="popup-detail">
                    <span class="popup-label">Organic Matter</span>
                    <span class="popup-value">${item.organic_matter ?? 'N/A'}%</span>
                </div>
                ${item.recommendation ? `
                <div style="margin-top: 8px; padding: 8px; background: #f0fdf4; border-radius: 6px;">
                    <span class="popup-label">Recommendation:</span>
                    <p style="font-size: 11px; color: #166534; margin-top: 4px;">${item.recommendation}</p>
                </div>` : ''}
            </div>
        </div>`;
    }


    // ================= POINT IN POLYGON =================

    function pointInPolygon(lat, lng, polygon) {
        let inside = false;
        for (let i = 0, j = polygon.length - 1; i < polygon.length; j = i++) {
            const xi = polygon[i][1], yi = polygon[i][0];
            const xj = polygon[j][1], yj = polygon[j][0];
            const intersect = ((yi > lng) !== (yj > lng)) &&
                (lat < (xj - xi) * (lng - yi) / (yj - yi) + xi);
            if (intersect) inside = !inside;
        }
        return inside;
    }

    function pointInFeature(lat, lng, feature) {
        const geom = feature.geometry;

        if (geom.type === 'Polygon') {
            return pointInPolygon(lat, lng, geom.coordinates[0]);
        }

        if (geom.type === 'MultiPolygon') {
            for (const poly of geom.coordinates) {
                if (pointInPolygon(lat, lng, poly[0])) return true;
            }
        }

        return false;
    }


    // ================= HEATMAP =================

    function updateHeatmap(cases) {
        if (heatLayer) {
            map.removeLayer(heatLayer);
            heatLayer = null;
        }

        const heatData = cases
            .filter(c => c.latitude && c.longitude)
            .map(c => [
                parseFloat(c.latitude),
                parseFloat(c.longitude),
                getSeverityWeight(c.severity)
            ]);

        if (heatData.length > 0) {
            heatLayer = L.heatLayer(heatData, {
                radius: 60,
                blur: 25,
                maxZoom: 17,
                max: 0.5,
                minOpacity: 0.55,
                gradient: {
                    0.0: '#fecc5c',
                    0.3: '#fd8d3c',
                    0.6: '#f03b20',
                    0.8: '#bd0026',
                    1.0: '#7b0008'
                }
            }).addTo(map);
        }
    }


    // ================= FILTERING =================

    function getFilteredData() {
        const categoryVal = document.getElementById('categoryFilter').value;
        const itemVal = document.getElementById('itemFilter').value;

        let data = [];

        if (categoryVal === 'all') {
            // Show all: pests, diseases, and soil analysis
            data = allPestCases.map(c => ({ ...c, dataType: 'pest_disease' }))
                .concat(soilAnalysisData.map(s => ({ ...s, dataType: 'soil', type: 'soil' })));
        } else if (categoryVal === 'soil') {
            // Show only soil analysis
            data = soilAnalysisData.map(s => ({ ...s, dataType: 'soil', type: 'soil' }));
        } else if (categoryVal === 'disease') {
            // Show only diseases based on database type
            let filtered = allPestCases.filter(c => {
                return c.type.toLowerCase() === 'disease';
            });

            // Apply item filter if selected
            if (itemVal !== 'all') {
                filtered = filtered.filter(c => c.pest === itemVal);
            }

            data = filtered.map(c => ({ ...c, dataType: 'pest_disease' }));
        } else if (categoryVal === 'pest') {
            // Show only pests based on database type
            let filtered = allPestCases.filter(c => {
                return c.type.toLowerCase() === 'pest';
            });

            // Apply item filter if selected
            if (itemVal !== 'all') {
                filtered = filtered.filter(c => c.pest === itemVal);
            }

            data = filtered.map(c => ({ ...c, dataType: 'pest_disease' }));
        }

        return data;
    }

    // ================= BUFFER ZONES =================

    function haversineMeters(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const φ1 = lat1 * Math.PI / 180, φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(Δφ/2)**2 + Math.cos(φ1)*Math.cos(φ2)*Math.sin(Δλ/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    // Buffer circles stored as plain array so nothing can accidentally clear them
    let bufferCircles = [];

    window.clearBufferZones = function () {
        bufferCircles.forEach(c => map.removeLayer(c));
        bufferCircles = [];
        const legend = document.getElementById('bufferLegend');
        if (legend) legend.style.display = 'none';
    };
    const clearBufferZones = window.clearBufferZones;

    window.showFarmDangerZone = function (farmId) {
        const farm = allFarms.find(f => f.id == farmId);
        if (!farm || !farm.latitude || !farm.longitude) return;

        const lat = parseFloat(farm.latitude);
        const lng = parseFloat(farm.longitude);

        map.closePopup();
        setHighlight(null);
        map.setView([lat, lng], 15);
        drawBufferZones(lat, lng, farm.name, farm.id);
    };

    function drawBufferZones(lat, lng, farmName, farmId) {
        clearBufferZones();

        const isHigh = s => s && (s.toLowerCase() === 'high' || s.toLowerCase() === 'severe');

        const casesWithDist = allPestCases
            .filter(c => c.latitude && c.longitude)
            .map(c => ({
                ...c,
                dist: haversineMeters(lat, lng, parseFloat(c.latitude), parseFloat(c.longitude))
            }));

        const z1 = casesWithDist.filter(c => c.dist <= 500);
        const z2 = casesWithDist.filter(c => c.dist > 500  && c.dist <= 1000);
        const z3 = casesWithDist.filter(c => c.dist > 1000 && c.dist <= 2000);

        const h1 = z1.filter(c => isHigh(c.severity)).length;
        const h2 = z2.filter(c => isHigh(c.severity)).length;
        const h3 = z3.filter(c => isHigh(c.severity)).length;

        // Draw outermost first so inner rings render on top
        const makeCircle = (radius, color, fillColor, fillOpacity, dashArray, label, total, high) => {
            const riskLabel = high > 0 ? `⚠️ ${high} high-severity` : (total > 0 ? `${total} case(s)` : 'No cases');
            const circle = L.circle([lat, lng], {
                radius,
                color,
                weight: 2.5,
                dashArray: dashArray || null,
                fillColor,
                fillOpacity,
                pane: 'overlayPane',
            })
            .bindTooltip(
                `<strong>${label}</strong><br>Cases: ${total} &nbsp;|&nbsp; ${riskLabel}`,
                { sticky: true, className: 'buffer-tooltip' }
            )
            .addTo(map);

            bufferCircles.push(circle);
        };

        makeCircle(2000, '#ca8a04', '#fef9c3', 0.12, '8 5', '🟡 Extended-Risk Zone (1–2 km)',      z3.length, h3);
        makeCircle(1000, '#ea580c', '#fed7aa', 0.18, '6 4', '🟠 Moderate-Risk Zone (500 m–1 km)', z2.length, h2);
        makeCircle(500,  '#dc2626', '#fca5a5', 0.25, null,  '🔴 High-Risk Zone (0–500 m)',         z1.length, h1);

        // Update legend panel
        const legend = document.getElementById('bufferLegend');
        if (legend) {
            document.getElementById('bufferFarmName').textContent = farmName || 'Selected Farm';
            document.getElementById('bufZ1Total').textContent = z1.length;
            document.getElementById('bufZ1High').textContent  = h1;
            document.getElementById('bufZ2Total').textContent = z2.length;
            document.getElementById('bufZ2High').textContent  = h2;
            document.getElementById('bufZ3Total').textContent = z3.length;
            document.getElementById('bufZ3High').textContent  = h3;
            legend.style.display = 'block';
        }

        renderAffectedFarms(lat, lng, farmName, farmId);
    }

    function renderAffectedFarms(lat, lng, sourceName, sourceFarmId) {
        const infectedList = document.getElementById('bufferInfectedList');
        const healthyList  = document.getElementById('bufferHealthyList');
        const infectedTotal = document.getElementById('bufInfectedTotal');
        const healthyTotal  = document.getElementById('bufHealthyTotal');
        const banner       = document.getElementById('bufferStatusBanner');
        const bannerIcon   = document.getElementById('bufferStatusIcon');
        const bannerTitle  = document.getElementById('bufferStatusTitle');
        const bannerSub    = document.getElementById('bufferStatusSubtitle');
        if (!infectedList || !healthyList || !banner) return;

        const isHigh = s => s && (s.toLowerCase() === 'high' || s.toLowerCase() === 'severe');

        // Build the set of farm_ids that have at least one approved detection
        const infectedFarmIds = new Set(
            (allPestCases || [])
                .filter(c => c.farm_id != null)
                .map(c => String(c.farm_id))
        );

        // Identify the source farm and whether it is infected
        const sourceFarm = (allFarms || []).find(f =>
            (sourceFarmId != null && f.id == sourceFarmId) ||
            (f.name === sourceName)
        );
        const sourceIsInfected = sourceFarm ? infectedFarmIds.has(String(sourceFarm.id)) : false;

        // Cases within 2km (used for banner counts)
        const casesInZone = (allPestCases || [])
            .filter(c => c.latitude && c.longitude)
            .map(c => ({ ...c, dist: haversineMeters(lat, lng, parseFloat(c.latitude), parseFloat(c.longitude)) }))
            .filter(c => c.dist <= 2000);

        const highSeverityCount = casesInZone.filter(c => isHigh(c.severity)).length;

        // Nearby farms within 2km (excluding the source farm)
        const farmsWithDist = (allFarms || [])
            .filter(f => f.latitude && f.longitude)
            .filter(f => !sourceFarm || f.id != sourceFarm.id)
            .map(f => ({
                ...f,
                dist: haversineMeters(lat, lng, parseFloat(f.latitude), parseFloat(f.longitude)),
                infected: infectedFarmIds.has(String(f.id))
            }))
            .filter(f => f.dist <= 2000)
            .sort((a, b) => a.dist - b.dist);

        const infected = farmsWithDist.filter(f => f.infected);
        const healthy  = farmsWithDist.filter(f => !f.infected);

        // === Risk status banner ===
        banner.className = 'mb-3 rounded-lg px-3 py-2.5 flex items-start gap-2.5 border';
        if (sourceIsInfected) {
            banner.classList.add('bg-red-50','dark:bg-red-900/20','border-red-300','dark:border-red-800');
            bannerIcon.textContent = '🚨';
            bannerTitle.className = 'text-xs font-bold text-red-800 dark:text-red-300';
            bannerSub.className   = 'text-[11px] mt-0.5 text-red-700 dark:text-red-400';
            bannerTitle.textContent = 'Infected farm — active detections on this property';
            bannerSub.textContent = `${casesInZone.length} case(s) within 2 km · ${infected.length} other infected farm(s) nearby`;
        } else if (casesInZone.length > 0) {
            banner.classList.add('bg-orange-50','dark:bg-orange-900/20','border-orange-300','dark:border-orange-800');
            bannerIcon.textContent = '⚠️';
            bannerTitle.className = 'text-xs font-bold text-orange-800 dark:text-orange-300';
            bannerSub.className   = 'text-[11px] mt-0.5 text-orange-700 dark:text-orange-400';
            bannerTitle.textContent = 'At risk — nearby infections detected';
            bannerSub.textContent = `Healthy farm with ${casesInZone.length} pest/disease case(s) within 2 km` +
                (highSeverityCount > 0 ? ` · ${highSeverityCount} high-severity` : '') +
                (infected.length > 0 ? ` · ${infected.length} infected farm(s) nearby` : '');
        } else {
            banner.classList.add('bg-emerald-50','dark:bg-emerald-900/20','border-emerald-300','dark:border-emerald-800');
            bannerIcon.textContent = '✅';
            bannerTitle.className = 'text-xs font-bold text-emerald-800 dark:text-emerald-300';
            bannerSub.className   = 'text-[11px] mt-0.5 text-emerald-700 dark:text-emerald-400';
            bannerTitle.textContent = 'No threats detected within 2 km';
            bannerSub.textContent = 'This farm and its surroundings are currently clear.';
        }

        // === Lists ===
        infectedTotal.textContent = infected.length;
        healthyTotal.textContent  = healthy.length;

        infectedList.innerHTML = infected.length === 0
            ? '<p class="text-xs text-gray-400 italic">No infected farms detected.</p>'
            : infected.map(f => renderFarmRow(f, true)).join('');

        healthyList.innerHTML = healthy.length === 0
            ? '<p class="text-xs text-gray-400 italic">No other farms in zone.</p>'
            : healthy.map(f => renderFarmRow(f, false)).join('');
    }

    function renderFarmRow(f, infected) {
        const escape = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
        const distLabel = f.dist < 1000 ? `${Math.round(f.dist)} m` : `${(f.dist / 1000).toFixed(2)} km`;
        const location = [f.barangay_name ?? f.barangay, f.municipality_name ?? f.municipality].filter(Boolean).join(', ');

        const zone = f.dist <= 500 ? 'High' : (f.dist <= 1000 ? 'Moderate' : 'Extended');
        const dotColor = f.dist <= 500 ? '#dc2626' : (f.dist <= 1000 ? '#ea580c' : '#ca8a04');

        const style = infected
            ? { bg: 'bg-red-50 dark:bg-red-900/20', border: 'border-red-200 dark:border-red-800', text: 'text-red-700 dark:text-red-300' }
            : { bg: 'bg-emerald-50 dark:bg-emerald-900/20', border: 'border-emerald-200 dark:border-emerald-800', text: 'text-emerald-700 dark:text-emerald-300' };

        return `
            <div class="rounded-md border ${style.border} ${style.bg} px-2.5 py-2 flex items-start gap-2">
                <span class="inline-block w-2.5 h-2.5 rounded-full mt-1 flex-shrink-0" style="background:${dotColor};"></span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-semibold ${style.text} truncate">${escape(f.name)}</span>
                        <span class="text-[10px] font-mono ${style.text} flex-shrink-0">${distLabel}</span>
                    </div>
                    <div class="text-[10px] text-gray-500 dark:text-gray-400 truncate">${escape(location || 'Location N/A')}</div>
                    <div class="text-[10px] text-gray-400 dark:text-gray-500">${zone}-Risk Zone</div>
                </div>
            </div>
        `;
    }


    let heatmapMode = false;

    function rebuildAll() {
        let data = getFilteredData();

        // Apply geographic filter
        const munVal = document.getElementById('municipalityFilter').value;
        const brgyVal = document.getElementById('barangayFilter').value;
        const farmVal = document.getElementById('farmFilter').value;

        // Apply farm filter first (most specific)
        if (farmVal !== 'all') {
            const selectedFarm = allFarms.find(f => f.id == farmVal);
            if (selectedFarm) {
                // Filter data to show only items related to this farm
                data = data.filter(c => {
                    if (c.dataType === 'soil') {
                        return c.farm_id == farmVal;
                    } else {
                        // For pest/disease, check if coordinates match farm location (within small radius)
                        const distance = Math.sqrt(
                            Math.pow(parseFloat(c.latitude) - parseFloat(selectedFarm.latitude), 2) +
                            Math.pow(parseFloat(c.longitude) - parseFloat(selectedFarm.longitude), 2)
                        );
                        return distance < 0.01; // Approximate proximity check
                    }
                });
            }
        } else if (brgyVal !== 'all' && barangayGeoData) {
            const feature = barangayGeoData.features.find(
                f => f.properties.Brgy === brgyVal
            );
            if (feature) {
                data = data.filter(c => pointInFeature(c.latitude, c.longitude, feature));
            }
        } else if (munVal !== 'all' && municipalGeoData) {
            const feature = municipalGeoData.features.find(
                f => f.properties.MUN === munVal
            );
            if (feature) {
                data = data.filter(c => pointInFeature(c.latitude, c.longitude, feature));
            }
        }

        // Rebuild markers
        pestMarkers.clearLayers();
        data.forEach(c => {
            if (!c.latitude || !c.longitude) return;

            const marker = L.marker([c.latitude, c.longitude], {
                icon: getTypeIcon(c.type)
            });

            // Use different popup content based on data type
            if (c.dataType === 'soil') {
                marker.bindPopup(createSoilPopupContent(c));
            } else {
                marker.bindPopup(createPopupContent(c));
            }

            marker.addTo(pestMarkers);
        });

        // Add farm markers
        if (farmVal === 'all') {
            // Show all farm markers when "All Farms" is selected, filtered by geography
            let farmsToShow = allFarms;

            // Apply geographic filter to farms
            if (brgyVal !== 'all' && barangayGeoData) {
                const feature = barangayGeoData.features.find(
                    f => f.properties.Brgy === brgyVal
                );
                if (feature) {
                    farmsToShow = farmsToShow.filter(farm =>
                        pointInFeature(parseFloat(farm.latitude), parseFloat(farm.longitude), feature)
                    );
                }
            } else if (munVal !== 'all' && municipalGeoData) {
                const feature = municipalGeoData.features.find(
                    f => f.properties.MUN === munVal
                );
                if (feature) {
                    farmsToShow = farmsToShow.filter(farm =>
                        pointInFeature(parseFloat(farm.latitude), parseFloat(farm.longitude), feature)
                    );
                }
            }

            farmsToShow.forEach(farm => {
                if (!farm.latitude || !farm.longitude) return;

                const farmMarker = L.marker([parseFloat(farm.latitude), parseFloat(farm.longitude)], {
                    icon: farmIcon
                });

                farmMarker.bindPopup(createFarmPopupContent(farm));
                farmMarker.addTo(pestMarkers);
            });
        } else if (farmVal !== 'all') {
            // Show selected farm + any other farms within the 2km danger zone
            const selectedFarm = allFarms.find(f => f.id == farmVal);
            if (selectedFarm) {
                const selLat = parseFloat(selectedFarm.latitude);
                const selLng = parseFloat(selectedFarm.longitude);

                const farmsInZone = allFarms.filter(f => {
                    if (!f.latitude || !f.longitude) return false;
                    if (f.id == selectedFarm.id) return true;
                    return haversineMeters(selLat, selLng, parseFloat(f.latitude), parseFloat(f.longitude)) <= 2000;
                });

                farmsInZone.forEach(f => {
                    const m = L.marker([parseFloat(f.latitude), parseFloat(f.longitude)], { icon: farmIcon });
                    m.bindPopup(createFarmPopupContent(f));
                    m.addTo(pestMarkers);
                });
            }
        }

        // Update count to include farm markers when showing all
        let totalCount = data.length;
        if (farmVal === 'all') {
            // Count filtered farms
            let farmsToCount = allFarms;
            if (brgyVal !== 'all' && barangayGeoData) {
                const feature = barangayGeoData.features.find(f => f.properties.Brgy === brgyVal);
                if (feature) {
                    farmsToCount = farmsToCount.filter(farm =>
                        pointInFeature(parseFloat(farm.latitude), parseFloat(farm.longitude), feature)
                    );
                }
            } else if (munVal !== 'all' && municipalGeoData) {
                const feature = municipalGeoData.features.find(f => f.properties.MUN === munVal);
                if (feature) {
                    farmsToCount = farmsToCount.filter(farm =>
                        pointInFeature(parseFloat(farm.latitude), parseFloat(farm.longitude), feature)
                    );
                }
            }
            totalCount += farmsToCount.length;
        } else if (farmVal !== 'all') {
            totalCount += 1; // Add 1 for the selected farm
        }
        document.getElementById('countNumber').textContent = totalCount;

        // Show either heatmap or pins based on toggle
        if (heatmapMode) {
            map.removeLayer(pestMarkers);
            // Only create heatmap for pest/disease data, not soil analysis
            const heatData = data.filter(d => d.dataType === 'pest_disease');
            updateHeatmap(heatData);
        } else {
            if (heatLayer) {
                map.removeLayer(heatLayer);
                heatLayer = null;
            }
            map.addLayer(pestMarkers);
        }

        // Update data table
        let farmsForTable = [];
        if (farmVal === 'all') {
            farmsForTable = allFarms;
            // Apply geographic filter to farms for table
            if (brgyVal !== 'all' && barangayGeoData) {
                const feature = barangayGeoData.features.find(f => f.properties.Brgy === brgyVal);
                if (feature) {
                    farmsForTable = farmsForTable.filter(farm =>
                        pointInFeature(parseFloat(farm.latitude), parseFloat(farm.longitude), feature)
                    );
                }
            } else if (munVal !== 'all' && municipalGeoData) {
                const feature = municipalGeoData.features.find(f => f.properties.MUN === munVal);
                if (feature) {
                    farmsForTable = farmsForTable.filter(farm =>
                        pointInFeature(parseFloat(farm.latitude), parseFloat(farm.longitude), feature)
                    );
                }
            }
        } else if (farmVal !== 'all') {
            const selectedFarm = allFarms.find(f => f.id == farmVal);
            if (selectedFarm) {
                farmsForTable = [selectedFarm];
            }
        }
        updateDataTable(data, farmsForTable);
    }

    window.toggleHeatmapView = function () {
        heatmapMode = document.getElementById('toggleHeatmap').checked;
        rebuildAll();
    };


    // ================= MUNICIPALITY / BARANGAY FILTERS =================

    function setHighlight(feature) {
        if (highlightLayer) {
            map.removeLayer(highlightLayer);
            highlightLayer = null;
        }

        if (feature) {
            highlightLayer = L.geoJSON(feature, {
                style: {
                    color: '#6366f1',
                    weight: 3,
                    fillColor: '#818cf8',
                    fillOpacity: 0.15,
                    dashArray: null,
                }
            }).addTo(map);

            map.fitBounds(highlightLayer.getBounds(), { padding: [40, 40] });
        }
    }

    function loadGeoData() {
        return Promise.all([
            fetch('/maps/MunicipalBoundary.json').then(r => r.json()),
            fetch('/maps/BarangayBoundary.json').then(r => r.json()),
        ]).then(([munData, brgyData]) => {
            municipalGeoData = munData;
            barangayGeoData = brgyData;

            // Populate municipality dropdown
            const munNames = [...new Set(
                munData.features.map(f => f.properties.MUN).filter(Boolean)
            )].sort();

            const munSelect = document.getElementById('municipalityFilter');
            munNames.forEach(name => {
                const opt = document.createElement('option');
                opt.value = name;
                opt.textContent = name;
                munSelect.appendChild(opt);
            });
        });
    }

    function populateBarangayDropdown(municipality) {
        const brgySelect = document.getElementById('barangayFilter');
        brgySelect.innerHTML = '<option value="all">All Barangays</option>';

        if (!barangayGeoData || municipality === 'all') return;

        const brgyNames = [...new Set(
            barangayGeoData.features
                .filter(f => f.properties.Muni_Adjus === municipality && f.properties.MUN === municipality)
                .map(f => f.properties.Brgy)
                .filter(name => name && !/\sVS\s/i.test(name))
        )].sort();

        brgyNames.forEach(name => {
            const opt = document.createElement('option');
            opt.value = name;
            opt.textContent = name;
            brgySelect.appendChild(opt);
        });
    }

    function populateFarmDropdown() {
        const farmSelect = document.getElementById('farmFilter');
        farmSelect.innerHTML = '<option value="all">All Farms</option>';

        const munVal = document.getElementById('municipalityFilter').value;
        const brgyVal = document.getElementById('barangayFilter').value;

        let farmsToShow = allFarms;

        if (brgyVal !== 'all' && barangayGeoData) {
            // Filter farms by barangay polygon
            const feature = barangayGeoData.features.find(f => f.properties.Brgy === brgyVal);
            if (feature) {
                farmsToShow = farmsToShow.filter(farm =>
                    farm.latitude && farm.longitude &&
                    pointInFeature(parseFloat(farm.latitude), parseFloat(farm.longitude), feature)
                );
            }
        } else if (munVal !== 'all' && municipalGeoData) {
            // Filter farms by municipality polygon
            const feature = municipalGeoData.features.find(f => f.properties.MUN === munVal);
            if (feature) {
                farmsToShow = farmsToShow.filter(farm =>
                    farm.latitude && farm.longitude &&
                    pointInFeature(parseFloat(farm.latitude), parseFloat(farm.longitude), feature)
                );
            }
        }

        farmsToShow.forEach(farm => {
            const opt = document.createElement('option');
            opt.value = farm.id;
            opt.textContent = farm.name;
            farmSelect.appendChild(opt);
        });
    }


    window.filterByMunicipality = function () {
        clearBufferZones();
        const munVal = document.getElementById('municipalityFilter').value;

        // Reset barangay and farm dropdowns based on selected municipality
        populateBarangayDropdown(munVal);
        populateFarmDropdown();

        if (munVal === 'all') {
            setHighlight(null);
            // Fit back to provincial bounds
            if (boundaryLayers.provincial) {
                map.fitBounds(boundaryLayers.provincial.getBounds(), { padding: [20, 20] });
            }
        } else if (municipalGeoData) {
            const feature = municipalGeoData.features.find(f => f.properties.MUN === munVal);
            setHighlight(feature);
        }

        rebuildAll();
    };


    window.filterByBarangay = function () {
        clearBufferZones();
        const brgyVal = document.getElementById('barangayFilter').value;

        // Update farm dropdown based on selected barangay
        populateFarmDropdown();

        if (brgyVal === 'all') {
            // Zoom back to selected municipality
            const munVal = document.getElementById('municipalityFilter').value;
            if (munVal !== 'all' && municipalGeoData) {
                const feature = municipalGeoData.features.find(f => f.properties.MUN === munVal);
                setHighlight(feature);
            } else {
                setHighlight(null);
            }
        } else if (barangayGeoData) {
            const feature = barangayGeoData.features.find(f => f.properties.Brgy === brgyVal);
            setHighlight(feature);
        }

        rebuildAll();
    };


    window.filterByFarm = function () {
        const farmVal = document.getElementById('farmFilter').value;

        if (farmVal === 'all') {
            clearBufferZones();

            // Reset to current barangay or municipality view
            const brgyVal = document.getElementById('barangayFilter').value;
            const munVal = document.getElementById('municipalityFilter').value;

            if (brgyVal !== 'all' && barangayGeoData) {
                const feature = barangayGeoData.features.find(f => f.properties.Brgy === brgyVal);
                setHighlight(feature);
            } else if (munVal !== 'all' && municipalGeoData) {
                const feature = municipalGeoData.features.find(f => f.properties.MUN === munVal);
                setHighlight(feature);
            } else {
                setHighlight(null);
            }
        } else {
            // Zoom to selected farm and draw buffer zones
            const selectedFarm = allFarms.find(f => f.id == farmVal);
            if (selectedFarm && selectedFarm.latitude && selectedFarm.longitude) {
                setHighlight(null);
                const lat = parseFloat(selectedFarm.latitude);
                const lng = parseFloat(selectedFarm.longitude);
                map.setView([lat, lng], 15);
                drawBufferZones(lat, lng, selectedFarm.name, selectedFarm.id);
            }
        }

        rebuildAll();
    };


    window.handleCategoryChange = function () {
        const categoryVal = document.getElementById('categoryFilter').value;
        const itemFilter = document.getElementById('itemFilter');

        // Clear the item filter
        itemFilter.innerHTML = '<option value="all">All Items</option>';

        if (categoryVal === 'pest') {
            // Show pest list from database categories
            itemFilter.style.display = 'block';
            pestsList.forEach(pest => {
                const opt = document.createElement('option');
                opt.value = pest;
                opt.textContent = pest;
                itemFilter.appendChild(opt);
            });
        } else if (categoryVal === 'disease') {
            // Show disease list from database categories
            itemFilter.style.display = 'block';
            diseasesList.forEach(disease => {
                const opt = document.createElement('option');
                opt.value = disease;
                opt.textContent = disease;
                itemFilter.appendChild(opt);
            });
        } else {
            // Hide item filter for 'all' or 'soil'
            itemFilter.style.display = 'none';
        }

        rebuildAll();
    };

    window.filterMarkers = () => rebuildAll();


    // ================= DATA TABLE =================

    function updateDataTable(data, farmData = []) {
        const tableContainer = document.getElementById('dataTableContainer');
        const tableHead = document.getElementById('tableHead');
        const tableBody = document.getElementById('tableBody');
        const tableCount = document.getElementById('tableCount');

        const categoryVal = document.getElementById('categoryFilter').value;
        const munVal = document.getElementById('municipalityFilter').value;
        const farmVal = document.getElementById('farmFilter').value;

        // Show table only if municipality is selected and there's data
        if (munVal === 'all' || (data.length === 0 && farmData.length === 0)) {
            tableContainer.style.display = 'none';
            return;
        }

        tableContainer.style.display = 'block';

        // Determine what type of data to display
        let headers = [];
        let rows = [];

        if (farmVal !== 'all' || categoryVal === 'all') {
            // Show combined data when farm is selected or showing all
            if (data.length > 0) {
                // Determine headers based on data type
                const hasPestDisease = data.some(d => d.dataType === 'pest_disease');
                const hasSoil = data.some(d => d.dataType === 'soil');

                if (hasPestDisease && !hasSoil) {
                    // Pest & Disease table
                    headers = ['Case ID', 'Pest/Disease', 'Type', 'Severity', 'Confidence', 'Date Detected', 'Area (ha)', 'Location'];
                    rows = data.filter(d => d.dataType === 'pest_disease').map(item => [
                        item.case_id || 'N/A',
                        item.pest || 'N/A',
                        `<span class="px-2 py-1 text-xs font-semibold rounded-full ${item.type === 'pest' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'}">${item.type || 'N/A'}</span>`,
                        `<span class="px-2 py-1 text-xs font-semibold rounded-full ${
                            item.severity === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                            item.severity === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' :
                            'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                        }">${item.severity || 'N/A'}</span>`,
                        item.confidence ? `${(item.confidence * 100).toFixed(1)}%` : 'N/A',
                        item.date_detected ? new Date(item.date_detected).toLocaleDateString() : 'N/A',
                        item.area || 'N/A',
                        `${item.barangay || 'N/A'}, ${item.municipality || 'N/A'}`
                    ]);
                } else if (hasSoil && !hasPestDisease) {
                    // Soil Analysis table
                    headers = ['Farm Name', 'Date Collected', 'pH Level', 'Nitrogen (%)', 'Phosphorus (ppm)', 'Potassium (ppm)', 'Organic Matter (%)', 'Location'];
                    rows = data.filter(d => d.dataType === 'soil').map(item => [
                        item.farm_name || 'N/A',
                        item.date_collected ? new Date(item.date_collected).toLocaleDateString() : 'N/A',
                        `<span class="px-2 py-1 text-xs font-semibold rounded-full ${
                            item.ph_level < 5.5 ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                            item.ph_level >= 6.0 && item.ph_level < 7.0 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'
                        }">${item.ph_level ? item.ph_level.toFixed(2) : 'N/A'}</span>`,
                        item.nitrogen ? item.nitrogen.toFixed(2) : 'N/A',
                        item.phosphorus ? item.phosphorus.toFixed(2) : 'N/A',
                        item.potassium ? item.potassium.toFixed(2) : 'N/A',
                        item.organic_matter ? item.organic_matter.toFixed(2) : 'N/A',
                        `${item.barangay || 'N/A'}, ${item.municipality || 'N/A'}`
                    ]);
                } else {
                    // Mixed data
                    headers = ['Type', 'Name/ID', 'Details', 'Date', 'Location'];
                    rows = data.map(item => {
                        if (item.dataType === 'soil') {
                            return [
                                '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Soil</span>',
                                item.farm_name || 'N/A',
                                `pH: ${item.ph_level ? item.ph_level.toFixed(2) : 'N/A'}`,
                                item.date_collected ? new Date(item.date_collected).toLocaleDateString() : 'N/A',
                                `${item.barangay || 'N/A'}, ${item.municipality || 'N/A'}`
                            ];
                        } else {
                            return [
                                `<span class="px-2 py-1 text-xs font-semibold rounded-full ${item.type === 'pest' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'}">${item.type || 'N/A'}</span>`,
                                item.pest || 'N/A',
                                `Severity: ${item.severity || 'N/A'}`,
                                item.date_detected ? new Date(item.date_detected).toLocaleDateString() : 'N/A',
                                `${item.barangay || 'N/A'}, ${item.municipality || 'N/A'}`
                            ];
                        }
                    });
                }
            }

            // Add farm data if showing all farms
            if (farmData.length > 0 && farmVal === 'all') {
                if (rows.length === 0) {
                    // Only farms to show
                    headers = ['Farm Name', 'Barangay', 'Municipality', 'Soil Type', 'Crop Variety', 'Crop Area', 'Coordinates'];
                    rows = farmData.map(farm => [
                        farm.name || 'N/A',
                        farm.barangay_name || farm.barangay || 'N/A',
                        farm.municipality_name || farm.municipality || 'N/A',
                        farm.soil_type || 'N/A',
                        farm.crop_variety || 'N/A',
                        farm.crop_area ? farm.crop_area + ' ha' : 'N/A',
                        `${parseFloat(farm.latitude).toFixed(6)}, ${parseFloat(farm.longitude).toFixed(6)}`
                    ]);
                }
            }
        } else if (categoryVal === 'pest' || categoryVal === 'disease') {
            // Pest & Disease table
            headers = ['Case ID', 'Pest/Disease', 'Type', 'Severity', 'Confidence', 'Date Detected', 'Area (ha)', 'Location'];
            rows = data.filter(d => d.dataType === 'pest_disease').map(item => [
                item.case_id || 'N/A',
                item.pest || 'N/A',
                `<span class="px-2 py-1 text-xs font-semibold rounded-full ${item.type === 'pest' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'}">${item.type || 'N/A'}</span>`,
                `<span class="px-2 py-1 text-xs font-semibold rounded-full ${
                    item.severity === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                    item.severity === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' :
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                }">${item.severity || 'N/A'}</span>`,
                item.confidence ? `${(item.confidence * 100).toFixed(1)}%` : 'N/A',
                item.date_detected ? new Date(item.date_detected).toLocaleDateString() : 'N/A',
                item.area || 'N/A',
                `${item.barangay || 'N/A'}, ${item.municipality || 'N/A'}`
            ]);
        } else if (categoryVal === 'soil') {
            // Soil Analysis table
            headers = ['Farm Name', 'Date Collected', 'pH Level', 'Nitrogen (%)', 'Phosphorus (ppm)', 'Potassium (ppm)', 'Organic Matter (%)', 'Location'];
            rows = data.filter(d => d.dataType === 'soil').map(item => [
                item.farm_name || 'N/A',
                item.date_collected ? new Date(item.date_collected).toLocaleDateString() : 'N/A',
                `<span class="px-2 py-1 text-xs font-semibold rounded-full ${
                    item.ph_level < 5.5 ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                    item.ph_level >= 6.0 && item.ph_level < 7.0 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'
                }">${item.ph_level ? item.ph_level.toFixed(2) : 'N/A'}</span>`,
                item.nitrogen ? item.nitrogen.toFixed(2) : 'N/A',
                item.phosphorus ? item.phosphorus.toFixed(2) : 'N/A',
                item.potassium ? item.potassium.toFixed(2) : 'N/A',
                item.organic_matter ? item.organic_matter.toFixed(2) : 'N/A',
                `${item.barangay || 'N/A'}, ${item.municipality || 'N/A'}`
            ]);
        }

        // Update table count
        tableCount.textContent = rows.length;

        // Build table headers
        tableHead.innerHTML = `
            <tr>
                ${headers.map(h => `<th class="px-4 py-3 text-left font-semibold">${h}</th>`).join('')}
            </tr>
        `;

        // Build table rows
        if (rows.length > 0) {
            tableBody.innerHTML = rows.map(row => `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    ${row.map(cell => `<td class="px-4 py-3 text-gray-700 dark:text-gray-300">${cell}</td>`).join('')}
                </tr>
            `).join('');
        } else {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${headers.length}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        No data available for the selected filters
                    </td>
                </tr>
            `;
        }
    }


    // ================= BOUNDARY LAYERS =================

    const boundaryLayers = {
        provincial: null,
        municipal: null,
        barangay: null,
    };

    const boundaryStyles = {
        provincial: {
            color: '#2563eb',
            weight: 3,
            fillColor: 'transparent',
            fillOpacity: 0,
            dashArray: null,
        },
        municipal: {
            color: '#ea580c',
            weight: 2,
            fillColor: '#f97316',
            fillOpacity: 0.06,
            dashArray: '6, 4',
        },
        barangay: {
            color: '#059669',
            weight: 1.5,
            fillColor: '#10b981',
            fillOpacity: 0.04,
            dashArray: '4, 3',
        },
    };

    const boundaryFiles = {
        provincial: '/maps/ProvincialBoundary.json',
        municipal: '/maps/MunicipalBoundary.json',
        barangay: '/maps/BarangayBoundary.json',
    };

    const boundaryNameKeys = {
        provincial: 'Prov',
        municipal: 'MUN',
        barangay: 'Brgy',
    };


    function loadBoundaryLayer(type) {

        return fetch(boundaryFiles[type])
            .then(response => response.json())
            .then(geojsonData => {

                const style = boundaryStyles[type];
                const nameKey = boundaryNameKeys[type];

                const layer = L.geoJSON(geojsonData, {

                    style: function () {
                        return {
                            color: style.color,
                            weight: style.weight,
                            fillColor: style.fillColor,
                            fillOpacity: style.fillOpacity,
                            dashArray: style.dashArray,
                        };
                    },

                    onEachFeature: function (feature, layer) {

                        const name = feature.properties[nameKey] || 'Unknown';

                        layer.bindTooltip(name, {
                            sticky: true,
                            className: 'boundary-tooltip',
                            direction: 'top',
                            offset: [0, -10],
                        });

                        layer.on('mouseover', function () {
                            this.setStyle({
                                weight: style.weight + 2,
                                fillOpacity: style.fillOpacity + 0.12,
                            });
                        });

                        layer.on('mouseout', function () {
                            this.setStyle({
                                weight: style.weight,
                                fillOpacity: style.fillOpacity,
                            });
                        });
                    },
                });

                boundaryLayers[type] = layer;
                return layer;
            });
    }


    window.toggleBoundaryLayer = function (type) {

        const checkbox = document.getElementById(
            'toggle' + type.charAt(0).toUpperCase() + type.slice(1)
        );

        if (checkbox.checked) {
            if (boundaryLayers[type]) {
                map.addLayer(boundaryLayers[type]);
            } else {
                loadBoundaryLayer(type).then(layer => map.addLayer(layer));
            }
        } else {
            if (boundaryLayers[type]) {
                map.removeLayer(boundaryLayers[type]);
            }
        }
    };


    // ================= PROVINCE MASK =================

    function addProvinceMask(geojsonData) {
        // World outer ring (covers the entire map)
        const worldOuter = [
            [-90, -180], [-90, 180], [90, 180], [90, -180], [-90, -180]
        ];

        // Extract province polygon coordinates as holes
        const holes = [];
        geojsonData.features.forEach(feature => {
            const geom = feature.geometry;
            if (geom.type === 'Polygon') {
                // GeoJSON is [lng, lat], Leaflet needs [lat, lng]
                holes.push(geom.coordinates[0].map(c => [c[1], c[0]]));
            } else if (geom.type === 'MultiPolygon') {
                geom.coordinates.forEach(poly => {
                    holes.push(poly[0].map(c => [c[1], c[0]]));
                });
            }
        });

        // Create polygon: world as outer, province as hole(s)
        const maskCoords = [worldOuter, ...holes];
        L.polygon(maskCoords, {
            color: 'none',
            fillColor: '#ffffff',
            fillOpacity: 0.85,
            interactive: false,
        }).addTo(map);
    }


    // ================= INIT =================

    // Load all boundary layers and fit map to Davao de Oro
    const provDataPromise = fetch('/maps/ProvincialBoundary.json').then(r => r.json());

    Promise.all([
        provDataPromise,
        loadBoundaryLayer('provincial'),
        loadBoundaryLayer('municipal'),
        loadBoundaryLayer('barangay'),
    ]).then(([provGeoJson, provLayer, munLayer, brgyLayer]) => {
        // Add mask first (below boundaries)
        addProvinceMask(provGeoJson);

        map.addLayer(provLayer);
        map.addLayer(munLayer);
        map.addLayer(brgyLayer);

        const bounds = provLayer.getBounds().pad(0.10);
        map.setMaxBounds(bounds);
        map.fitBounds(provLayer.getBounds(), { padding: [20, 20] });
    });

    // Load geo data for municipality/barangay/farm dropdowns
    loadGeoData().then(function() {
        // Populate farm dropdown after GeoJSON data is loaded
        populateFarmDropdown();
    });

    // Initial render with markers (heatmap off by default)
    rebuildAll();

});

</script>

@endpush
