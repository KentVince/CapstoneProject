
<div class="p-4">
    {{-- Print Button --}}
    <div class="flex justify-end mb-4 no-print">
        <button
            onclick="window.print()"
            class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors"
        >
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5z" />
            </svg>
            Print Card
        </button>
    </div>

    {{-- Card --}}
    <div class="print-card mx-auto" style="width: 400px; border: 2px solid #16a34a; border-radius: 12px; overflow: hidden; font-family: Arial, sans-serif;">
        {{-- Header --}}
        <div style="background: #16a34a; color: white; padding: 16px; text-align: center;">
            <h2 style="margin: 0; font-size: 20px; font-weight: bold;">CAFARM</h2>
            <p style="margin: 4px 0 0; font-size: 12px;">Coffee Agriculture Farm Management</p>
        </div>

        {{-- Body --}}
        <div style="padding: 24px; text-align: center; background: white;">
            {{-- QR Code --}}
            @if($qrUrl)
                <div style="margin-bottom: 16px;">
                    <img
                        src="{{ $qrUrl }}"
                        alt="QR Code"
                        style="width: 180px; height: 180px; margin: 0 auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 6px;"
                    >
                </div>
            @endif

            {{-- Farmer Info --}}
            <div style="margin-top: 12px;">
                <p style="margin: 6px 0; font-size: 18px; font-weight: bold; color: #111;">{{ $fullName }}</p>
                <p style="margin: 6px 0; font-size: 14px; color: #555;">Application No: <strong style="color: #16a34a;">{{ $appNo }}</strong></p>
                <div style="margin-top: 12px; padding: 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px;">
                    <p style="margin: 4px 0; font-size: 13px; color: #555;">Default Password</p>
                    <p style="margin: 4px 0; font-size: 16px; font-weight: bold; color: #111; letter-spacing: 1px;">cafarm123</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div style="background: #f9fafb; padding: 10px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0; font-size: 11px; color: #999;">Please change your password after first login.</p>
        </div>
    </div>
</div>

<style>
    @media print {
        /* Hide everything except the print card */
        body * { visibility: hidden !important; }
        .print-card, .print-card * { visibility: visible !important; }
        .print-card {
            position: absolute;
            left: 50%;
            top: 50px;
            transform: translateX(-50%);
        }
        .no-print { display: none !important; }
    }
</style>
