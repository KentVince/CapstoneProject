
<pre>{{ var_dump($url) }}</pre>

@if ($url)
    <div class="flex justify-center">
        <img src="{{ $url }}" alt="QR Code" class="h-40 w-40">
    </div>
@else
    <p class="text-center text-gray-500">QR code will be generated dynamically after saving the record.</p>
@endif

