
{{-- var map = L.map('map').setView([7.738017313026259, 126.14915197088574], 11); --}}

<x-filament::page>
    <div>
        {{-- <h1 class="text-xl font-bold mb-4">CAFARM Map</h1> --}}

        <!-- Toggle Switch -->
        <div class="mb-4 flex items-center gap-4">
            <label for="toggleMarkers" class="flex items-center cursor-pointer">
                {{-- <span class="mr-2 text-green-500 font-bold">Show Farms</span> --}}
                <!-- Toggle switch -->
                <input id="toggleMarkers" type="checkbox" class="hidden" onchange="toggleMarkers()">
                <div class="w-14 h-8 bg-gray-300 rounded-full relative transition">
                    <div id="toggleThumb" class="absolute w-7 h-7 bg-amber-600 rounded-full top-0.5 left-0.5 transition-transform transform"></div>
                </div>
                <span id="toggleLabel" class="ml-2 text-amber-500">Pest and Disease</span>
            </label>
        </div>

        <!-- Map Container -->
        <div id="map" style="height: 600px; width: 100%;"></div>

        <!-- Leaflet CSS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <!-- Leaflet JS -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize the map
                var map = L.map('map').setView([7.738017313026259, 126.14915197088574], 11);

                // Add tile layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);

                // Fetch data
                var farms = @json($this->getFarms());
                var pestAndDiseaseCases = @json($this->getPestAndDiseaseCases());

                // Define layers
                var farmMarkers = L.layerGroup();
                var pestMarkers = L.layerGroup();

                // Add farm markers
                farms.forEach(function (farm) {
                    var marker = L.marker([parseFloat(farm.latitude), parseFloat(farm.longitude)], {
                        icon: L.icon({
                            iconUrl: '/images/farm-icon.png',
                            iconSize: [30, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34]
                        })
                    }).bindPopup(`<b>Farm Name:</b> ${farm.name}`);
                    farmMarkers.addLayer(marker);
                });

                // Add pest and disease markers
                pestAndDiseaseCases.forEach(function (caseItem) {
                    var iconUrl = caseItem.type === 'Pest' ? '/images/pest-icon.png' : '/images/disease-icon.png';
                    var marker = L.marker([parseFloat(caseItem.latitude), parseFloat(caseItem.longitude)], {
                        icon: L.icon({
                            iconUrl: iconUrl,
                            iconSize: [30, 41],
                            iconAnchor: [12, 41],
                            popupAnchor: [1, -34]
                        })
                    }).bindPopup(`<b>${caseItem.name}</b><br>Type: ${caseItem.type}`);
                    pestMarkers.addLayer(marker);
                });

                // Add pest markers by default
                map.addLayer(pestMarkers);
                var currentLayer = "pests";

                // Toggle logic
                window.toggleMarkers = function () {
                    var toggle = document.getElementById('toggleMarkers');
                    var toggleThumb = document.getElementById('toggleThumb');
                    var toggleLabel = document.getElementById('toggleLabel');

                    if (toggle.checked) {
                        // Show Farms
                        map.removeLayer(pestMarkers);
                        map.addLayer(farmMarkers);
                        toggleThumb.style.transform = 'translateX(100%)';
                        toggleLabel.textContent = "Farms";
                    } else {
                        // Show Pest and Disease
                        map.removeLayer(farmMarkers);
                        map.addLayer(pestMarkers);
                        toggleThumb.style.transform = 'translateX(0)';
                        toggleLabel.textContent = "Pest and Disease";
                    }
                };
            });
        </script>
    </div>
</x-filament::page>
