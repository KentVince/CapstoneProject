<x-filament::page>
    <div>
        {{-- <h1 class="text-2xl font-bold mb-8 text-center text-white">Admin Dashboard</h1> --}}

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Farms Chart -->
            <div class="p-4 shadow-lg rounded-lg bg-transparent border border-white">
                <h2 class="text-lg font-semibold mb-4 text-center text-white">Farms by Month</h2>
                <div style="max-width: 100%; margin: auto;">
                    <canvas id="farmsChart"></canvas>
                </div>
            </div>

            <!-- Pest and Disease Chart -->
            <div class="p-4 shadow-lg rounded-lg bg-transparent border border-white">
                <h2 class="text-lg font-semibold mb-4 text-center text-white">Pest and Disease Cases by Type</h2>
                <div style="max-width: 100%; margin: auto;">
                    <canvas id="pestChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pest and Disease Cases Over Time Chart -->
<div class="p-4 shadow-lg rounded-lg bg-transparent border border-white">
    <h2 class="text-lg font-semibold mb-4 text-center text-white">Pest and Disease Cases Over Time</h2>
    <div style="max-width: 100%; margin: auto;">
        <canvas id="pestCasesOverTimeChart"></canvas>
    </div>
</div>

<!-- Active Cases by Severity Chart -->
<div class="p-4 shadow-lg rounded-lg bg-transparent border border-white">
    <h2 class="text-lg font-semibold mb-4 text-center text-white">Active Cases by Severity</h2>
    <div style="max-width: 100%; margin: auto;">
        <canvas id="casesBySeverityChart"></canvas>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function () {
           // Farms Chart Data
           var farmsData = @json($farmData) || {};
            var pestData = @json($pestData) || {};
            var pestAndDiseaseData = @json($pestAndDiseaseData) || [];

            var farmLabels = Object.keys(farmsData).map(month => `Month ${month}`) || ['No Data'];
            var farmCounts = Object.values(farmsData) || [0];

            var pestLabels = Object.keys(pestData) || ['No Data'];
            var pestCounts = Object.values(pestData) || [0];

            var casesLabels = pestAndDiseaseData.map(data => data.month) || ['No Data'];
            var casesCounts = pestAndDiseaseData.map(data => data.total_cases) || [0];


             // Active Cases by Severity Data
            var casesBySeverity = @json($casesBySeverity);
            var severityLabels = Object.keys(casesBySeverity) || ['No Data'];
            var severityCounts = Object.values(casesBySeverity) || [0];

        // Render Pest and Disease Cases Over Time Chart
        new Chart(document.getElementById('pestCasesOverTimeChart'), {
    type: 'line',
    data: {
        labels: casesLabels,
        datasets: [{
            label: 'Cases',
            data: casesCounts,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: 'white'
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: 'white'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.2)'
                }
            },
            y: {
                ticks: {
                    color: 'white'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.2)'
                }
            }
        }
    }
}),// Render Active Cases by Severity Chart
    new Chart(document.getElementById('casesBySeverityChart'), {
        type: 'pie',
        data: {
            labels: severityLabels,
            datasets: [{
                label: 'Active Cases by Severity',
                data: severityCounts,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)', // Red for High
                    'rgba(255, 206, 86, 0.6)', // Yellow for Medium
                    'rgba(75, 192, 192, 0.6)'  // Green for Low
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: 'white' // Set legend text color to white
                    }
                }
            }
        }
    });



    });
</script>

</x-filament::page>
