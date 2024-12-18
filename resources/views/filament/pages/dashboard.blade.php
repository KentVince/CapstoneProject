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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Farms Chart Data
            var farmsData = @json($this->getFarmData());
            var farmLabels = Object.keys(farmsData).map(month => `Month ${month}`);
            var farmCounts = Object.values(farmsData);

            // Render Farms Chart
            new Chart(document.getElementById('farmsChart'), {
                type: 'bar',
                data: {
                    labels: farmLabels,
                    datasets: [{
                        label: 'Farms',
                        data: farmCounts,
                        backgroundColor: 'rgba(75, 192, 192, 0.6)',
                        borderColor: 'rgba(75, 192, 192, 1)',
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
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: 'white' // Set X-axis labels to white
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.2)' // Set grid lines to white (semi-transparent)
                            }
                        },
                        y: {
                            ticks: {
                                color: 'white' // Set Y-axis labels to white
                            },
                            grid: {
                                color: 'rgba(255, 255, 255, 0.2)' // Set grid lines to white (semi-transparent)
                            }
                        }
                    }
                }
            });

            // Pest and Disease Chart Data
            var pestData = @json($this->getPestData());
            var pestLabels = Object.keys(pestData);
            var pestCounts = Object.values(pestData);

            // Render Pest and Disease Chart
            new Chart(document.getElementById('pestChart'), {
                type: 'pie',
                data: {
                    labels: pestLabels,
                    datasets: [{
                        label: 'Cases',
                        data: pestCounts,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(255, 206, 86, 0.6)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
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
