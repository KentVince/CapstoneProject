<div class="p-2 space-y-3">
    <p class="text-sm text-gray-500 mb-4">Choose the report you want to generate and print.</p>

    <a href="{{ $pestUrl }}" target="_blank"
       class="flex items-start gap-4 p-4 border border-green-200 rounded-xl bg-green-50 hover:bg-green-100 transition-colors no-underline group">
        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-green-600 flex items-center justify-center" style="background-color: #16a34a;">
            <svg class="w-5 h-5 text-white" style="color: #ffffff;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
        </div>
        <div>
            <div class="font-semibold text-green-800 text-sm group-hover:text-green-900">Pest &amp; Disease Distribution Report</div>
            <div class="text-xs text-gray-500 mt-0.5">Per-barangay breakdown of approved pest &amp; disease cases, severity levels, and top detections.</div>
        </div>
    </a>

    <a href="{{ $soilUrl }}" target="_blank"
       class="flex items-start gap-4 p-4 border border-amber-200 rounded-xl bg-amber-50 hover:bg-amber-100 transition-colors no-underline group">
        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-500 flex items-center justify-center" style="background-color: #f59e0b;">
            <svg class="w-5 h-5 text-white" style="color: #ffffff;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 1-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21a48.25 48.25 0 0 1-8.135-.687c-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" />
            </svg>
        </div>
        <div>
            <div class="font-semibold text-amber-800 text-sm group-hover:text-amber-900">Soil Analysis Report</div>
            <div class="text-xs text-gray-500 mt-0.5">Farms with laboratory data, soil pH distribution, nutrient levels (N, P, K, OM) per barangay.</div>
        </div>
    </a>

    @isset($farmerFarmUrl)
    <a href="{{ $farmerFarmUrl }}" target="_blank"
       class="flex items-start gap-4 p-4 border border-blue-200 rounded-xl bg-blue-50 hover:bg-blue-100 transition-colors no-underline group">
        <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center" style="background-color: #2563eb;">
            <svg class="w-5 h-5 text-white" style="color: #ffffff;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
        </div>
        <div>
            <div class="font-semibold text-blue-800 text-sm group-hover:text-blue-900">Farmers &amp; Farms Report</div>
            <div class="text-xs text-gray-500 mt-0.5">List of farmers and their registered farms in the selected barangay, with crop, area, and verification status.</div>
        </div>
    </a>
    @endisset
</div>
