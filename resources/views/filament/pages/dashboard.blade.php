<x-filament::page>
    <style>
        /* Custom dark theme card colors for Dashboard sections (#003432) */
        .dark .cafarm-dark-card {
            background-color: #003432 !important;
            border-color: #005250 !important;
        }
        /* Table sub-headers & footers — slightly darker for contrast */
        .dark .cafarm-dark-table-header {
            background-color: #002624 !important;
            border-color: #004442 !important;
        }
        /* thead row */
        .dark .cafarm-dark-table-head {
            background-color: #001e1c !important;
        }
        /* Table row hover */
        .dark .cafarm-dark-row:hover {
            background-color: #004442 !important;
        }
        /* Pagination buttons & page indicator */
        .dark .cafarm-dark-pagination {
            background-color: #003432 !important;
            border-color: #005250 !important;
        }
    </style>
    <div>
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Pests Card -->
            <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-white/80 uppercase tracking-wide">Total Pests</h3>
                        <svg class="w-8 h-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-white">{{ $this->getTotalPests() }}</div>
                    <p class="text-xs text-white/70 mt-2">Reported pest cases</p>
                </div>
                <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white/10 rounded-full"></div>
            </div>

            <!-- Total Diseases Card -->
            <div class="relative overflow-hidden bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-white/80 uppercase tracking-wide">Total Diseases</h3>
                        <svg class="w-8 h-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-white">{{ $this->getTotalDiseases() }}</div>
                    <p class="text-xs text-white/70 mt-2">Reported disease cases</p>
                </div>
                <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white/10 rounded-full"></div>
            </div>

            <!-- Total Farmers Card -->
            <div class="relative overflow-hidden bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-white/80 uppercase tracking-wide">Total Farmers</h3>
                        <svg class="w-8 h-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-white">{{ $this->getTotalFarmers() }}</div>
                    <p class="text-xs text-white/70 mt-2">Registered farmers</p>
                </div>
                <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white/10 rounded-full"></div>
            </div>

            <!-- Total Soil Tested Card -->
            <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-white/80 uppercase tracking-wide">Soil Tests</h3>
                        <svg class="w-8 h-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-white">{{ $this->getTotalSoilTested() }}</div>
                    <p class="text-xs text-white/70 mt-2">Completed soil tests</p>
                </div>
                <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white/10 rounded-full"></div>
            </div>
        </div>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Pest vs Disease Distribution -->
            <div class="p-6 shadow-lg rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 cafarm-dark-card">
                <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Pest vs Disease Distribution</h2>
                <div style="max-width: 100%; height: 300px;">
                    <canvas id="pestVsDiseaseChart"></canvas>
                </div>
            </div>

            <!-- Farms Registered by Month -->
            <div class="p-6 shadow-lg rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 cafarm-dark-card">
                <h2 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Farms Registered by Month</h2>
                <div style="max-width: 100%; height: 300px;">
                    <canvas id="farmsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Data Table Section -->
        <div class="mt-6">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden cafarm-dark-card">
                <!-- Table Header -->
                <div class="px-6 py-5 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 cafarm-dark-table-header">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">📋 Data Records</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                View and filter through all records by type
                            </p>
                        </div>

                        <!-- Search and Filters -->
                        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                            <div class="w-full sm:w-auto">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search:</label>
                                <input type="text"
                                       id="tableSearch"
                                       placeholder="Search records..."
                                       class="w-full px-4 py-2 text-sm border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       oninput="filterTableData()">
                            </div>

                            <div class="w-full sm:w-auto">
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Filter by Type:</label>
                                <select id="dataTypeFilter"
                                        onchange="filterTableData()"
                                        class="w-full sm:w-auto px-4 py-2 text-sm font-medium border-2 border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                                    <option value="all">📊 All Data</option>
                                    <option value="pest_disease">🐛 Pest & Disease</option>
                                    <option value="farms">🌾 Farms</option>
                                    <option value="farmers">👨‍🌾 Farmers</option>
                                    <option value="soil">🌱 Soil Analysis</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead id="dashboardTableHead" class="bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-gray-300 uppercase text-xs cafarm-dark-table-head">
                            <!-- Dynamic headers -->
                        </thead>
                        <tbody id="dashboardTableBody" class="divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Dynamic rows -->
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Info -->
                <div class="px-6 py-4 border-t-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 cafarm-dark-pagination cafarm-dark-table-header">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-4">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing <span id="recordsCount" class="font-bold text-blue-600 dark:text-blue-400">0</span> records
                            </p>
                            <span id="currentFilterLabel" class="text-xs px-2 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 font-semibold">
                                All Data
                            </span>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="changeTablePage(-1)"
                                    id="prevPageBtn"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition cafarm-dark-pagination">
                                ← Previous
                            </button>
                            <span class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-lg cafarm-dark-pagination">
                                Page <span id="currentPage" class="font-bold">1</span> of <span id="totalPages" class="font-bold">1</span>
                            </span>
                            <button onclick="changeTablePage(1)"
                                    id="nextPageBtn"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition cafarm-dark-pagination">
                                Next →
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get data from backend
        var farmsData = @json($farmData) || {};
        var pestVsDisease = @json($pestVsDisease) || {};

        // Check if dark mode
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#ffffff' : '#1f2937';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';

        // Chart colors
        const colors = {
            primary: 'rgba(59, 130, 246, 0.8)',
            success: 'rgba(34, 197, 94, 0.8)',
            warning: 'rgba(251, 146, 60, 0.8)',
            danger: 'rgba(239, 68, 68, 0.8)',
            info: 'rgba(99, 102, 241, 0.8)',
            purple: 'rgba(168, 85, 247, 0.8)',
        };

        // 1. Pest vs Disease Distribution Chart
        var pestVsDiseaseLabels = Object.keys(pestVsDisease) || ['No Data'];
        var pestVsDiseaseCounts = Object.values(pestVsDisease) || [0];

        new Chart(document.getElementById('pestVsDiseaseChart'), {
            type: 'pie',
            data: {
                labels: pestVsDiseaseLabels,
                datasets: [{
                    data: pestVsDiseaseCounts,
                    backgroundColor: [
                        colors.warning,
                        colors.danger
                    ],
                    borderWidth: 2,
                    borderColor: isDark ? '#1f2937' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: textColor, padding: 15 }
                    }
                }
            }
        });

        // 2. Farms Registered by Month Chart
        var farmLabels = Object.keys(farmsData).map(month => {
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return monthNames[month - 1] || `Month ${month}`;
        }) || ['No Data'];
        var farmCounts = Object.values(farmsData) || [0];

        new Chart(document.getElementById('farmsChart'), {
            type: 'bar',
            data: {
                labels: farmLabels,
                datasets: [{
                    label: 'Farms Registered',
                    data: farmCounts,
                    backgroundColor: colors.success,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        ticks: { color: textColor },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: textColor },
                        grid: { color: gridColor }
                    }
                }
            }
        });
    });

    // ==================== TABLE DATA FUNCTIONALITY ====================

    // Helper function to format dates in long format
    function formatLongDate(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    }

    // Fetch all data needed for the table
    let allTableData = {
        pestDisease: [],
        farms: [],
        farmers: [],
        soil: []
    };

    let currentTablePage = 1;
    const recordsPerPage = 10;
    let filteredTableData = [];

    // Fetch data via AJAX
    async function fetchTableData() {
        try {
            const response = await fetch('/api/dashboard-table-data');
            if (response.ok) {
                allTableData = await response.json();
                filterTableData();
            }
        } catch (error) {
            console.error('Error fetching table data:', error);
            // Use placeholder data if API fails
            initializePlaceholderData();
        }
    }

    function initializePlaceholderData() {
        // This will be populated with actual data from your backend
        allTableData = {
            pestDisease: @json($this->getRecentPestDiseases() ?? []),
            farms: @json($this->getRecentFarms() ?? []),
            farmers: @json($this->getRecentFarmers() ?? []),
            soil: @json($this->getRecentSoilAnalysis() ?? [])
        };

        // Debug logging
        console.log('Table data loaded:', {
            pestDisease: allTableData.pestDisease.length,
            farms: allTableData.farms.length,
            farmers: allTableData.farmers.length,
            soil: allTableData.soil.length
        });

        filterTableData();
    }

    function filterTableData() {
        const searchTerm = document.getElementById('tableSearch').value.toLowerCase();
        const dataType = document.getElementById('dataTypeFilter').value;

        // Update filter label
        const filterLabels = {
            'all': '📊 All Data',
            'pest_disease': '🐛 Pest & Disease',
            'farms': '🌾 Farms',
            'farmers': '👨‍🌾 Farmers',
            'soil': '🌱 Soil Analysis'
        };
        const filterLabel = document.getElementById('currentFilterLabel');
        if (filterLabel) {
            filterLabel.textContent = filterLabels[dataType] || 'All Data';
        }

        let data = [];

        // Combine data based on filter
        if (dataType === 'all' || dataType === 'pest_disease') {
            data = data.concat(allTableData.pestDisease.map(item => ({
                ...item,
                dataType: 'pest_disease'
            })));
        }
        if (dataType === 'all' || dataType === 'farms') {
            data = data.concat(allTableData.farms.map(item => ({
                ...item,
                dataType: 'farms'
            })));
        }
        if (dataType === 'all' || dataType === 'farmers') {
            data = data.concat(allTableData.farmers.map(item => ({
                ...item,
                dataType: 'farmers'
            })));
        }
        if (dataType === 'all' || dataType === 'soil') {
            data = data.concat(allTableData.soil.map(item => ({
                ...item,
                dataType: 'soil'
            })));
        }

        // Apply search filter
        if (searchTerm) {
            data = data.filter(item => {
                const searchableText = JSON.stringify(item).toLowerCase();
                return searchableText.includes(searchTerm);
            });
        }

        filteredTableData = data;
        currentTablePage = 1;
        renderTable();
    }

    function renderTable() {
        const tableHead = document.getElementById('dashboardTableHead');
        const tableBody = document.getElementById('dashboardTableBody');
        const dataType = document.getElementById('dataTypeFilter').value;

        // Calculate pagination
        const startIndex = (currentTablePage - 1) * recordsPerPage;
        const endIndex = startIndex + recordsPerPage;
        const paginatedData = filteredTableData.slice(startIndex, endIndex);
        const totalPages = Math.ceil(filteredTableData.length / recordsPerPage);

        // Update pagination info
        document.getElementById('recordsCount').textContent = filteredTableData.length;
        document.getElementById('currentPage').textContent = currentTablePage;
        document.getElementById('totalPages').textContent = totalPages || 1;
        document.getElementById('prevPageBtn').disabled = currentTablePage === 1;
        document.getElementById('nextPageBtn').disabled = currentTablePage >= totalPages;

        let headers = [];
        let rows = [];

        // Determine table structure based on data type
        if (dataType === 'pest_disease') {
            headers = ['Case ID', 'Pest/Disease', 'Type', 'Severity', 'Confidence', 'Date Detected', 'Area (ha)'];
            rows = paginatedData.map(item => [
                item.case_id || 'N/A',
                item.pest || 'N/A',
                `<span class="px-2 py-1 text-xs font-semibold rounded-full ${item.type === 'pest' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300'}">${item.type || 'N/A'}</span>`,
                `<span class="px-2 py-1 text-xs font-semibold rounded-full ${
                    item.severity === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                    item.severity === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' :
                    'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                }">${item.severity || 'N/A'}</span>`,
                item.confidence ? `${(item.confidence * 100).toFixed(1)}%` : 'N/A',
                formatLongDate(item.date_detected),
                item.area || 'N/A'
            ]);
        } else if (dataType === 'farms') {
            headers = ['Farm Name', 'Farmer', 'Barangay', 'Area (ha)', 'Registered Date'];
            rows = paginatedData.map(item => [
                item.name || 'N/A',
                item.farmer_name || 'N/A',
                item.barangay_name || item.barangay || 'N/A',
                item.lot_hectare || 'N/A',
                formatLongDate(item.created_at)
            ]);
        } else if (dataType === 'farmers') {
            headers = ['App No', 'Full Name', 'Barangay', 'Sex', 'Age', 'Phone', 'Crop', 'Application Date'];
            rows = paginatedData.map(item => [
                item.app_no || 'N/A',
                item.full_name || 'N/A',
                item.barangay_name || item.barangay || 'N/A',
                `<span class="px-2 py-1 text-xs font-semibold rounded-full ${item.sex === 'Male' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-300'}">${item.sex || 'N/A'}</span>`,
                item.age || 'N/A',
                item.phone_no || 'N/A',
                item.crop || 'N/A',
                item.date_of_application ? formatLongDate(item.date_of_application) : formatLongDate(item.created_at)
            ]);
        } else if (dataType === 'soil') {
            headers = ['Farm Name', 'Date Collected', 'pH Level', 'Nitrogen (%)', 'Phosphorus (ppm)', 'Potassium (ppm)'];
            rows = paginatedData.map(item => [
                item.farm_name || 'N/A',
                formatLongDate(item.date_collected),
                `<span class="px-2 py-1 text-xs font-semibold rounded-full ${
                    item.ph_level < 5.5 ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300' :
                    item.ph_level >= 6.0 && item.ph_level < 7.0 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'
                }">${item.ph_level ? item.ph_level.toFixed(2) : 'N/A'}</span>`,
                item.nitrogen ? item.nitrogen.toFixed(2) : 'N/A',
                item.phosphorus ? item.phosphorus.toFixed(2) : 'N/A',
                item.potassium ? item.potassium.toFixed(2) : 'N/A'
            ]);
        } else {
            // Mixed data view
            headers = ['Type', 'Name/ID', 'Details', 'Date', 'Status'];
            rows = paginatedData.map(item => {
                if (item.dataType === 'pest_disease') {
                    return [
                        '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">Pest/Disease</span>',
                        item.pest || 'N/A',
                        `Severity: ${item.severity || 'N/A'}`,
                        formatLongDate(item.date_detected),
                        `<span class="px-2 py-1 text-xs rounded-full ${item.validation_status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">${item.validation_status || 'N/A'}</span>`
                    ];
                } else if (item.dataType === 'farms') {
                    return [
                        '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300">Farm</span>',
                        item.name || 'N/A',
                        item.barangay_name || item.barangay || 'N/A',
                        formatLongDate(item.created_at),
                        `<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">${item.lot_hectare || 'N/A'} ha</span>`
                    ];
                } else if (item.dataType === 'farmers') {
                    return [
                        '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">Farmer</span>',
                        item.full_name || 'N/A',
                        item.barangay_name || item.barangay || 'N/A',
                        item.date_of_application ? formatLongDate(item.date_of_application) : formatLongDate(item.created_at),
                        `<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">${item.crop || 'N/A'}</span>`
                    ];
                } else {
                    return [
                        '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">Soil</span>',
                        item.farm_name || 'N/A',
                        `pH: ${item.ph_level ? item.ph_level.toFixed(2) : 'N/A'}`,
                        formatLongDate(item.date_collected),
                        `<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Analysis</span>`
                    ];
                }
            });
        }

        // Render table headers
        tableHead.innerHTML = `
            <tr>
                ${headers.map(h => `<th class="px-4 py-3 text-left font-semibold">${h}</th>`).join('')}
            </tr>
        `;

        // Render table rows
        if (rows.length > 0) {
            tableBody.innerHTML = rows.map(row => `
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition cafarm-dark-row">
                    ${row.map(cell => `<td class="px-4 py-3 text-gray-700 dark:text-gray-300">${cell}</td>`).join('')}
                </tr>
            `).join('');
        } else {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="${headers.length}" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        No records found
                    </td>
                </tr>
            `;
        }
    }

    function changeTablePage(direction) {
        const totalPages = Math.ceil(filteredTableData.length / recordsPerPage);
        const newPage = currentTablePage + direction;

        if (newPage >= 1 && newPage <= totalPages) {
            currentTablePage = newPage;
            renderTable();
        }
    }

    // Initialize table data on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializePlaceholderData();
    });
</script>

</x-filament::page>
