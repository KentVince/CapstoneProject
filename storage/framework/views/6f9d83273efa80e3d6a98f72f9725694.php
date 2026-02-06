<?php if (isset($component)) { $__componentOriginalbe23554f7bded3778895289146189db7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbe23554f7bded3778895289146189db7 = $attributes; } ?>
<?php $component = Filament\View\LegacyComponents\Page::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('filament::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Filament\View\LegacyComponents\Page::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>

    
    <div class="mb-4 rounded-xl bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 p-4 space-y-4">

        
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

            <select id="pestFilter"
                    onchange="filterMarkers()"
                    class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white text-sm py-2 px-3 min-w-[200px] focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                <option value="all">All Pest & Diseases</option>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $this->getPestTypes(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type); ?>"><?php echo e($type); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </select>

            <select id="statusFilter"
                    onchange="filterMarkers()"
                    class="rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 dark:text-white text-sm py-2 px-3 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="disapproved">Disapproved</option>
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

        
        <div class="border-t border-gray-100 dark:border-gray-700"></div>

        
        <div class="flex flex-wrap items-center gap-x-6 gap-y-3">

            
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
            </div>

            
            <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border cursor-pointer transition-all
                          border-orange-200 dark:border-orange-800 bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 dark:hover:bg-orange-900/40">
                <input type="checkbox" id="toggleHeatmap" onchange="toggleHeatmapView()"
                       class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-3.5 h-3.5">
                <span class="w-2.5 h-2.5 rounded-full" style="background: radial-gradient(circle, #ff4500, #ff8c00, #ffd700);"></span>
                <span class="text-xs font-medium text-orange-700 dark:text-orange-300">Heatmap</span>
            </label>

            
            <div class="hidden sm:block w-px h-6 bg-gray-200 dark:bg-gray-600"></div>

            
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
                           class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 w-3.5 h-3.5">
                    <span class="w-2.5 h-2.5 rounded-sm" style="background: #f97316; border: 1.5px solid #ea580c;"></span>
                    <span class="text-xs font-medium text-orange-700 dark:text-orange-300">Municipal</span>
                </label>

                <label class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border cursor-pointer transition-all
                              border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20 hover:bg-emerald-100 dark:hover:bg-emerald-900/40">
                    <input type="checkbox" id="toggleBarangay" onchange="toggleBoundaryLayer('barangay')"
                           class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-3.5 h-3.5">
                    <span class="w-2.5 h-2.5 rounded-sm" style="background: #10b981; border: 1.5px solid #059669;"></span>
                    <span class="text-xs font-medium text-emerald-700 dark:text-emerald-300">Barangay</span>
                </label>
            </div>

        </div>

    </div>



    
    <div id="map"
         style="height: 800px; width: 100%;"
         class="rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
    </div>

 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbe23554f7bded3778895289146189db7)): ?>
<?php $attributes = $__attributesOriginalbe23554f7bded3778895289146189db7; ?>
<?php unset($__attributesOriginalbe23554f7bded3778895289146189db7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbe23554f7bded3778895289146189db7)): ?>
<?php $component = $__componentOriginalbe23554f7bded3778895289146189db7; ?>
<?php unset($__componentOriginalbe23554f7bded3778895289146189db7); ?>
<?php endif; ?>




<?php $__env->startPush('styles'); ?>

<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

<style>

.leaflet-popup-content { margin: 0; padding: 0; }
.leaflet-popup-content-wrapper { padding: 0; border-radius: 12px; overflow: hidden; }

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

<?php $__env->stopPush(); ?>




<?php $__env->startPush('scripts'); ?>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const allPestCases = <?php echo json_encode($this->getPestAndDiseaseCases(), 15, 512) ?>;


    const map = L.map('map')
        .setView([7.738017313026259, 126.14915197088574], 11);


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
        if (!severity) return 0.3;
        switch (severity.toLowerCase()) {
            case 'high':
            case 'severe': return 1.0;
            case 'medium': return 0.6;
            default: return 0.3;
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

    function getTypeIcon(type) {
        if (!type) return pestIcon;
        return type.toLowerCase() === 'disease' ? diseaseIcon : pestIcon;
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
                radius: 30,
                blur: 20,
                maxZoom: 17,
                max: 1.0,
                gradient: {
                    0.2: '#ffffb2',
                    0.4: '#fecc5c',
                    0.6: '#fd8d3c',
                    0.8: '#f03b20',
                    1.0: '#bd0026'
                }
            }).addTo(map);
        }
    }


    // ================= FILTERING =================

    function getFilteredCases() {
        const pf = document.getElementById('pestFilter').value;
        const sf = document.getElementById('statusFilter').value;

        return allPestCases.filter(c =>
            (pf === 'all' || c.pest === pf) &&
            (sf === 'all' || (c.validation_status ?? 'pending') === sf)
        );
    }

    let heatmapMode = false;

    function rebuildAll() {
        let cases = getFilteredCases();

        // Apply geographic filter
        const munVal = document.getElementById('municipalityFilter').value;
        const brgyVal = document.getElementById('barangayFilter').value;

        if (brgyVal !== 'all' && barangayGeoData) {
            const feature = barangayGeoData.features.find(
                f => f.properties.Brgy === brgyVal
            );
            if (feature) {
                cases = cases.filter(c => pointInFeature(c.latitude, c.longitude, feature));
            }
        } else if (munVal !== 'all' && municipalGeoData) {
            const feature = municipalGeoData.features.find(
                f => f.properties.MUN === munVal
            );
            if (feature) {
                cases = cases.filter(c => pointInFeature(c.latitude, c.longitude, feature));
            }
        }

        // Rebuild markers
        pestMarkers.clearLayers();
        cases.forEach(c => {
            if (!c.latitude || !c.longitude) return;
            L.marker([c.latitude, c.longitude], {
                icon: getTypeIcon(c.type)
            }).bindPopup(createPopupContent(c))
              .addTo(pestMarkers);
        });

        document.getElementById('countNumber').textContent = cases.length;

        // Show either heatmap or pins based on toggle
        if (heatmapMode) {
            map.removeLayer(pestMarkers);
            updateHeatmap(cases);
        } else {
            if (heatLayer) {
                map.removeLayer(heatLayer);
                heatLayer = null;
            }
            map.addLayer(pestMarkers);
        }
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
                .filter(f => f.properties.Muni_Adjus === municipality || f.properties.MUN === municipality)
                .map(f => f.properties.Brgy)
                .filter(Boolean)
        )].sort();

        brgyNames.forEach(name => {
            const opt = document.createElement('option');
            opt.value = name;
            opt.textContent = name;
            brgySelect.appendChild(opt);
        });
    }


    window.filterByMunicipality = function () {
        const munVal = document.getElementById('municipalityFilter').value;

        // Reset barangay
        populateBarangayDropdown(munVal);

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
        const brgyVal = document.getElementById('barangayFilter').value;

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


    window.filterMarkers = () => rebuildAll();


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


    // ================= INIT =================

    // Load provincial boundary and fit map
    loadBoundaryLayer('provincial').then(layer => {
        map.addLayer(layer);
        map.fitBounds(layer.getBounds(), { padding: [20, 20] });
    });

    // Load geo data for municipality/barangay dropdowns
    loadGeoData();

    // Initial render with markers (heatmap off by default)
    rebuildAll();

});

</script>

<?php $__env->stopPush(); ?>
<?php /**PATH /var/www/html/CapstoneProject/resources/views/filament/pages/cafarm-map.blade.php ENDPATH**/ ?>