<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farmer Details — {{ $farmer->app_no }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 24px;
            color: #111;
            background: #f5f5f5;
        }
        .sheet {
            max-width: 820px;
            margin: 0 auto;
            background: white;
            padding: 32px 36px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        .print-bar {
            max-width: 820px;
            margin: 0 auto 12px;
            display: flex;
            justify-content: flex-end;
        }
        .print-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }
        .print-btn:hover { background: #15803d; }

        header.report-header {
            border-bottom: 3px solid #16a34a;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-grid {
            display: grid;
            grid-template-columns: 120px 1fr 120px;
            gap: 16px;
            align-items: center;
        }
        .header-left,
        .header-right {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .header-left img { max-width: 110px; max-height: 110px; object-fit: contain; }
        .header-right img {
            width: 110px;
            height: 110px;
            object-fit: contain;
            border: 1px solid #e5e7eb;
            padding: 4px;
            background: white;
        }
        .header-center { text-align: center; }
        .header-center img.cafarm-icon {
            height: 80px;
            margin-bottom: 6px;
        }
        .header-center h1 {
            margin: 0;
            font-size: 22px;
            color: #16a34a;
            letter-spacing: 0.5px;
        }
        .header-center p {
            margin: 4px 0 0;
            font-size: 12px;
            color: #6b7280;
        }
        .qr-placeholder {
            width: 110px;
            height: 110px;
            border: 1px dashed #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #9ca3af;
            text-align: center;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 18px;
        }

        section.block {
            margin-bottom: 22px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }
        section.block h2 {
            margin: 0;
            padding: 8px 12px;
            background: #f0fdf4;
            color: #16a34a;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e5e7eb;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
        }
        .grid .field {
            padding: 10px 12px;
            border-bottom: 1px solid #f3f4f6;
        }
        .grid .field:nth-child(odd) {
            border-right: 1px solid #f3f4f6;
        }
        .grid .field .label {
            display: block;
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .grid .field .value {
            font-size: 14px;
            color: #111;
            font-weight: 600;
            word-break: break-word;
        }
        .grid .field .value.empty {
            color: #9ca3af;
            font-weight: 400;
            font-style: italic;
        }

        footer.report-footer {
            margin-top: 28px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }
        footer.report-footer .site-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #16a34a;
            font-weight: 600;
            text-decoration: none;
        }
        footer.report-footer .site-link svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        @media print {
            /* A4 paper. Margin:0 hides Chrome/Edge's auto-inserted page header
               (URL, date) and footer (page number). The sheet then restores
               inner spacing. */
            @page {
                size: A4;
                margin: 0;
            }
            html, body { margin: 0; padding: 0; }
            body { background: white; }
            .sheet {
                box-shadow: none;
                border-radius: 0;
                padding: 12mm 14mm;
                max-width: 100%;
            }
            .print-bar, .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    @php
        $fmt = function ($value) {
            if ($value === null || $value === '') return null;
            return $value;
        };

        $middleInitial = $farmer->middle_name ? strtoupper(substr($farmer->middle_name, 0, 1)) . '.' : '';
        $fullName = trim("{$farmer->last_name}, {$farmer->first_name} {$middleInitial} " . ($farmer->ext_name ?? ''));

        $barangayName    = optional($farmer->barangayData)->barangay    ?? $farmer->farmer_address_bgy;
        $municipalityName = optional($farmer->municipalityData)->municipality ?? $farmer->farmer_address_mun;
        $birthday = $farmer->birthday ? \Illuminate\Support\Carbon::parse($farmer->birthday)->format('F j, Y') : null;

        $farm = $farmer->farm;

        $renderField = function ($label, $value) {
            $isEmpty = ($value === null || $value === '');
            return '<div class="field"><span class="label">' . e($label) . '</span>' .
                '<span class="value' . ($isEmpty ? ' empty' : '') . '">' . ($isEmpty ? '—' : e($value)) . '</span></div>';
        };
    @endphp

    <div class="print-bar no-print">
        <button type="button" class="print-btn" onclick="window.print()">
            Print
        </button>
    </div>

    <div class="sheet">
        <header class="report-header">
            <div class="header-grid">
                {{-- Left: MAGSO logo --}}
                <div class="header-left">
                    <img src="{{ asset('images/magso_logo.png') }}" alt="MAGSO">
                </div>

                {{-- Center: CAFARM icon + title --}}
                <div class="header-center">
                    <img src="{{ asset('images/cofsys_print_logo.png') }}" alt="CofSys" class="cafarm-icon">
                    <h1>Farmer Information Report</h1>
                    <p>Coffee Farm Management System with Smart Disease Detection and Geoanalytics</p>
                </div>

                {{-- Right: QR code --}}
                <div class="header-right">
                    @if ($farmer->qr_code && \Illuminate\Support\Facades\Storage::disk('public')->exists($farmer->qr_code))
                        <img src="{{ asset('storage/' . $farmer->qr_code) }}" alt="QR Code">
                    @else
                        <div class="qr-placeholder">QR not generated</div>
                    @endif
                </div>
            </div>
        </header>

        <div class="meta-row">
            <div><strong>App No.:</strong> {{ $farmer->app_no ?? '—' }}</div>
            <div><strong>Generated:</strong> {{ now()->format('F j, Y, g:i a') }}</div>
        </div>

        {{-- ─── Personal Information ─── --}}
        <section class="block">
            <h2>Personal Information</h2>
            <div class="grid">
                {!! $renderField('Full Name', $fullName) !!}
                {!! $renderField('RSBSA No.', $farmer->rsbsa_no) !!}
                {!! $renderField('Gender', $farmer->gender) !!}
                {!! $renderField('Birthday', $birthday) !!}
                {!! $renderField('Contact No.', $farmer->contact_num) !!}
                {!! $renderField('Email', $farmer->email_add) !!}
                {!! $renderField('User Type', $farmer->user_type) !!}
                {!! $renderField('Agency', $farmer->agency) !!}
            </div>
        </section>

        {{-- ─── Address ─── --}}
        <section class="block">
            <h2>Address</h2>
            <div class="grid">
                {!! $renderField('Purok', $farmer->farmer_address_prk) !!}
                {!! $renderField('Barangay', $barangayName) !!}
                {!! $renderField('Municipality', $municipalityName) !!}
                {!! $renderField('Province', $farmer->farmer_address_prv) !!}
            </div>
        </section>

        {{-- ─── Farm Information ─── --}}
        @if ($farm)
            <section class="block">
                <h2>Farm Information</h2>
                <div class="grid">
                    {!! $renderField('Farm Name', $farm->farm_name) !!}
                    {!! $renderField('Crop Name', $farm->crop_name) !!}
                    {!! $renderField('Crop Variety', $farm->crop_variety) !!}
                    {!! $renderField('Crop Area', $farm->crop_area ? $farm->crop_area . ' ha' : null) !!}
                    {!! $renderField('Soil Type', $farm->soil_type) !!}
                    {!! $renderField('Cropping', $farm->cropping) !!}
                    {!! $renderField('Farmworker', $farm->farmworker) !!}
                    {!! $renderField('Verified Area', $farm->verified_area) !!}
                    {!! $renderField('Status', $farm->status) !!}
                    {!! $renderField('GPS', ($farm->latitude && $farm->longtitude) ? "{$farm->latitude}, {$farm->longtitude}" : null) !!}
                </div>
            </section>
        @else
            <section class="block">
                <h2>Farm Information</h2>
                <div style="padding: 16px; text-align: center; color: #9ca3af; font-style: italic;">
                    No farm record linked to this farmer.
                </div>
            </section>
        @endif

        <footer class="report-footer">
            <span class="site-link">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="2" y1="12" x2="22" y2="12"></line>
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                </svg>
                cofsys.davaodeoro.gov.ph
            </span>
        </footer>
    </div>
</body>
</html>
