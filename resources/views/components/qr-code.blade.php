@if ($getRecord() && $getRecord()->qr_code)
    <div class="flex flex-col items-center space-y-3">
        <img 
            src="{{ Storage::url($getRecord()->qr_code) }}" 
            alt="QR Code" 
            class="rounded-lg shadow-md border p-2 bg-white w-48 h-48"
        >
        <a 
            href="{{ Storage::url($getRecord()->qr_code) }}" 
            download="{{ $getRecord()->app_no }}_QR.png"
            class="px-4 py-2 text-white bg-green-600 rounded-md hover:bg-green-700"
        >
            ⬇️ Download QR Code
        </a>
    </div>
@else
    <p class="text-gray-500 text-center italic">QR Code will be generated after saving this record.</p>
@endif
