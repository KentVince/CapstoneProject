@php($farmer = $getState())

@if ($farmer && $farmer->qr_code)

    <div class=" flex items-center justify-center" x-data="{ open: false }">
        {{-- Clickable QR Thumbnail --}}
        <img
            src="{{ asset('storage/' . $farmer->qr_code) }}"
            alt="QR Code"
            class="w-10 h-10 rounded border border-gray-300 object-contain cursor-pointer hover:scale-105 transition-transform"
            x-on:click.stop="open = true"
            title="Click to preview QR"
        >

        {{-- QR Modal Preview with Teleport --}}
        <template x-teleport="body" >
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[9999] overflow-y-auto"
                x-cloak
            >
                {{-- Backdrop --}}
                <div
                    class="fixed inset-0 bg-black/60 backdrop-blur-sm"
                    x-on:click="open = false"
                ></div>

                {{-- Modal Content --}}
                <div class="fixed inset-0 flex items-center justify-center p-4 ">
                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="relative bg-white qr-modal-wrapper rounded-2xl shadow-2xl max-w-md w-full mx-auto overflow-hidden"
                        x-on:click.stop
                    >
                        {{-- Modal Header --}}
                        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white qr-modal-header">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z" />
                                </svg>
                                <span class="font-semibold text-gray-800 dark:text-white">QR Code Preview</span>
                            </div>
                            <button
                                x-on:click="open = false"
                                class="p-1 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-emerald-900 transition-colors"
                            >
                                <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="flex flex-col items-center gap-6 p-6 bg-white qr-modal-body">
                            <div class="p-4 bg-emerald rounded-xl shadow-lg border border-gray-200">
                                <img
                                    src="{{ asset('storage/' . $farmer->qr_code) }}"
                                    alt="QR Code"
                                    class="w-64 h-64 object-contain rounded-lg"
                                >
                            </div>

                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Application No: <strong class="text-emerald-600">{{ $farmer->app_no }}</strong>
                            </p>

                            <a 
                                href="{{ asset('storage/' . $farmer->qr_code) }}"
                                download="QR-{{ $farmer->app_no }}.png"
                                class="inline-flex items-center px-5 py-2.5 bg-gray-600 text-gray-500 text-sm font-medium rounded-xl shadow-lg hover:bg-emerald-700 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all hover:shadow-xl"
                            >
                                <svg class="w-4 h-4 mr-2 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                </svg>
                                Download QR
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <style>
        .bg-coil {
            background: linear-gradient(135deg, #0d3320 0%, #1e5631 25%, #2d7a4a 50%, #0d3320 100%);
        }
        .dark .qr-modal-wrapper {
            background-color: #003432 !important;
        }
        .dark .qr-modal-header {
            background-color: #003432 !important;
            border-color: #065f46 !important;
        }
        .dark .qr-modal-body {
            background-color: #003432 !important;
        }
        </style>

    </div>
@else
    <span class="text-gray-400 text-xs italic">No QR</span>
@endif
