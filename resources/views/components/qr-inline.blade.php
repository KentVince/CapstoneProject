@php($farmer = $getState())

@if ($farmer && $farmer->qr_code)
    <div class="flex items-center justify-center">
        {{-- 🖼️ Clickable QR Thumbnail --}}
        <img 
            src="{{ asset('storage/' . $farmer->qr_code) }}" 
            alt="QR Code" 
            class="w-10 h-10 rounded border border-gray-300 object-contain cursor-pointer hover:scale-105 transition-transform"
            x-data
            x-on:click.stop="$dispatch('open-modal', { id: 'qr-preview-{{ $farmer->id }}' })"
            title="Click to preview QR"
        >

        {{-- 🌟 QR Modal Preview --}}
        <x-filament::modal 
            id="qr-preview-{{ $farmer->id }}" 
            width="lg" 
            alignment="center"
            class="backdrop-blur-sm bg-black/30"
        >
            <x-slot name="heading">
                <div class="flex justify-center items-center gap-2">
                    <x-heroicon-o-qr-code class="w-5 h-5 text-green-600" />
                    <span class="font-semibold text-gray-800">QR Code Preview</span>
                </div>
            </x-slot>

            <div class="flex flex-col items-center gap-6 p-6">
                <div class="p-4 bg-white rounded-xl shadow-lg border border-gray-200">
                    <img 
                        src="{{ asset('storage/' . $farmer->qr_code) }}" 
                        alt="QR Code" 
                        class="w-72 h-72 object-contain rounded-lg drop-shadow-md"
                    >
                </div>

                <p class="text-sm text-amber-500">
                    Application No: <strong class="text-amber-500">{{ $farmer->app_no }}</strong>
                </p>

                <a 
                    href="{{ asset('storage/' . $farmer->qr_code) }}" 
                    download="QR-{{ $farmer->app_no }}.png"
                    class="inline-flex items-center px-5 py-2 bg-green-600 text-white text-sm font-medium rounded-lg shadow-md hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition"
                >
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 mr-2" />
                    Download QR
                </a>
            </div>
        </x-filament::modal>
    </div>
@else
    <span class="text-gray-400 text-xs italic">No QR</span>
@endif
