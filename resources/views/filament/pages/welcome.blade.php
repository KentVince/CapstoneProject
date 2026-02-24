<x-filament::page>
    <div class="space-y-6">
        <!-- Welcome Header -->
        <div class="px-4 sm:px-6 lg:px-8 py-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
            <div class="flex items-center gap-4 mb-4">
                <div class="flex-shrink-0">
                    <svg class="h-12 w-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h-2m0 0H10m2 0v2m0-2v-2m7 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Welcome to CaFarm</h1>
                    <p class="mt-1 text-lg text-gray-600">{{ auth()->user()->name }}</p>
                </div>
            </div>
            <p class="text-gray-700 text-base leading-relaxed">
                Welcome to the CaFarm Information Portal. This platform provides you with access to agricultural information, bulletins, and resources to help you stay informed about the latest developments in agriculture.
            </p>
        </div>

        <!-- Quick Access Guide -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- About This Portal -->
            <div class="bg-white shadow rounded-lg p-6 border-l-4 border-blue-500">
                <div class="flex items-center mb-3">
                    <svg class="h-6 w-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900">About This Portal</h2>
                </div>
                <p class="text-gray-600">
                    The CaFarm portal is designed to provide you with timely and relevant agricultural information. You can access bulletins and announcements to stay updated with the latest news in the agricultural sector.
                </p>
            </div>

            <!-- Available Resources -->
            <div class="bg-white shadow rounded-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center mb-3">
                    <svg class="h-6 w-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    <h2 class="text-xl font-semibold text-gray-900">Available Resources</h2>
                </div>
                <ul class="text-gray-600 space-y-2">
                    <li class="flex items-start">
                        <span class="text-green-600 mr-2">✓</span>
                        <span>Agricultural bulletins and announcements</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-600 mr-2">✓</span>
                        <span>Latest updates on agricultural programs</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-600 mr-2">✓</span>
                        <span>Information and guidelines</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Information Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Getting Started -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <svg class="h-6 w-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900">Getting Started</h3>
                </div>
                <ol class="text-gray-600 space-y-2 list-decimal list-inside">
                    <li>Check your profile information</li>
                    <li>Review available bulletins</li>
                    <li>Stay updated with announcements</li>
                    <li>Bookmark important resources</li>
                </ol>
            </div>

            <!-- Need Help -->
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center mb-4">
                    <svg class="h-6 w-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.172l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900">Need Help?</h3>
                </div>
                <p class="text-gray-600 mb-4">
                    If you have questions or need assistance, please reach out to the CaFarm support team. We're here to help you navigate the platform and find the information you need.
                </p>
                <p class="text-sm text-gray-500">
                    <span class="font-semibold">Department of Agriculture - CaFarm</span>
                </p>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Important Notes</h3>
            <ul class="text-gray-600 space-y-2">
                <li class="flex items-start">
                    <span class="text-blue-600 font-bold mr-3">•</span>
                    <span>All information is regularly updated to ensure accuracy and relevance</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 font-bold mr-3">•</span>
                    <span>Please ensure your contact information is current in your profile</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 font-bold mr-3">•</span>
                    <span>Check back regularly for new bulletins and announcements</span>
                </li>
                <li class="flex items-start">
                    <span class="text-blue-600 font-bold mr-3">•</span>
                    <span>For technical issues, contact the system administrator</span>
                </li>
            </ul>
        </div>
    </div>
</x-filament::page>
