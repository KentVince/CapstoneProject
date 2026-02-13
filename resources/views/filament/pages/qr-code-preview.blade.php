
<x-filament::page>
@if ($record && $record->qr_code)
    <div class="flex justify-center ">
        <img src="{{ Storage::disk('public')->url($record->qr_code) }}" alt="QR Code" class="h-40 w-40">
    </div>
@else
    <p class="text-center text-gray-500">QR code will be generated after saving the record.</p>
@endif


</x-filament::page>