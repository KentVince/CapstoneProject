<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farmers & Farms Report</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 24px;
            color: #111;
            background: #f5f5f5;
        }

        .print-bar {
            max-width: 860px;
            margin: 0 auto 12px;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-print { background: #2563eb; color: white; }
        .btn-print:hover { background: #1d4ed8; }
        .btn-back  { background: #e5e7eb; color: #374151; }
        .btn-back:hover { background: #d1d5db; }

        .sheet {
            max-width: 860px;
            margin: 0 auto;
            background: white;
            padding: 32px 36px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        header.report-header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-grid {
            display: grid;
            grid-template-columns: 120px 1fr 120px;
            gap: 16px;
            align-items: center;
        }
        .header-left, .header-right {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .header-left img, .header-right img {
            max-width: 110px;
            max-height: 110px;
            object-fit: contain;
            background: #ffffff;
            padding: 6px;
            border-radius: 8px;
            box-shadow: 0 0 0 1px #e5e7eb;
        }
        .header-center { text-align: center; }
        .header-center img.cofsys-logo { height: 80px; margin-bottom: 6px; }
        .header-center h1 {
            margin: 0;
            font-size: 20px;
            color: #2563eb;
            letter-spacing: 0.5px;
        }
        .header-center p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #6b7280;
        }

        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 18px;
            color: #374151;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 22px;
        }
        .summary-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
        }
        .card-label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.4px; }
        .card-value { font-size: 22px; font-weight: 700; color: #111827; margin-top: 4px; }
        .card-value.blue { color: #2563eb; }
        .card-sub { font-size: 10px; color: #6b7280; margin-top: 2px; }

        section.block { margin-bottom: 24px; }
        section.block h2 {
            font-size: 14px;
            color: #1f2937;
            border-left: 4px solid #2563eb;
            padding: 4px 8px;
            background: #eff6ff;
            margin: 0 0 8px 0;
            border-radius: 0 4px 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        thead th {
            background: #1e40af;
            color: white;
            text-align: left;
            padding: 8px 10px;
            font-weight: 600;
        }
        tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }
        tbody tr:nth-child(even) { background: #f9fafb; }
        .farmer-row {
            background: #eff6ff !important;
            font-weight: 700;
            color: #1e3a8a;
        }
        .farm-row td:first-child { padding-left: 22px; color: #4b5563; font-weight: normal; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
        }
        .badge-yes { background: #d1fae5; color: #065f46; }
        .badge-no  { background: #fee2e2; color: #991b1b; }
        .muted { color: #9ca3af; }

        footer.report-footer {
            margin-top: 28px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
            text-align: center;
        }

        @media print {
            @page { size: A4; margin: 0; }
            html, body { margin: 0; padding: 0; }
            body { background: white; }
            .sheet { box-shadow: none; border-radius: 0; padding: 12mm 14mm; max-width: 100%; }
            .print-bar, .no-print { display: none !important; }
            section.block { page-break-inside: avoid; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

    <div class="print-bar no-print">
        <a href="javascript:history.back()" class="btn btn-back">&#8592; Back</a>
        <button class="btn btn-print" onclick="window.print()">Print Report</button>
    </div>

    <div class="sheet">

        <header class="report-header">
            <div class="header-grid">
                <div class="header-left">
                    <img src="{{ asset('images/magso_logo.png') }}" alt="MAGSO">
                </div>
                <div class="header-center">
                    <h1>Farmers &amp; Farms Report</h1>
                    <p>Coffee Farm Management System with Smart Disease Detection and Geoanalytics</p>
                </div>
                <div class="header-right">
                    <img src="{{ asset('images/cofsys_print_logo.png') }}" alt="CofSys" class="cofsys-logo">
                </div>
            </div>
        </header>

        <div class="meta-row">
            <div><strong>Municipality:</strong> {{ $municipal ?? 'All Municipalities' }}</div>
            <div><strong>Barangay:</strong> {{ $barangayName ?? 'All Barangays' }}</div>
            <div><strong>Generated:</strong> {{ now()->format('F j, Y, g:i a') }}</div>
        </div>

        <div class="summary-grid">
            <div class="summary-card">
                <div class="card-label">Total Farmers</div>
                <div class="card-value blue">{{ number_format($totalFarmers) }}</div>
                <div class="card-sub">In selected area</div>
            </div>
            <div class="summary-card">
                <div class="card-label">Total Farms</div>
                <div class="card-value">{{ number_format($totalFarms) }}</div>
                <div class="card-sub">Registered farms</div>
            </div>
            <div class="summary-card">
                <div class="card-label">Verified Farms</div>
                <div class="card-value blue">{{ number_format($verifiedFarms) }}</div>
                <div class="card-sub">Verified area</div>
            </div>
            <div class="summary-card">
                <div class="card-label">Total Crop Area</div>
                <div class="card-value">{{ number_format($totalArea, 2) }}</div>
                <div class="card-sub">Hectares</div>
            </div>
        </div>

        <section class="block">
            <h2>List of Farmers and their Farms</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width:32%;">Farmer / Farm</th>
                        <th style="width:14%;">Purok / Crop</th>
                        <th style="width:14%;">Contact / Variety</th>
                        <th class="text-right" style="width:10%;">Area (ha)</th>
                        <th class="text-center" style="width:10%;">Verified</th>
                        <th style="width:20%;">Barangay</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($farmers as $farmer)
                        @php
                            $fullName = trim(($farmer->first_name ?? '') . ' ' . ($farmer->middle_name ?? '') . ' ' . ($farmer->last_name ?? ''));
                            $farmerBarangay = $farmer->barangayData->barangay ?? '—';
                        @endphp
                        <tr class="farmer-row">
                            <td>{{ $fullName ?: '—' }}</td>
                            <td>{{ $farmer->farmer_address_prk ?? '—' }}</td>
                            <td>{{ $farmer->contact_num ?? '—' }}</td>
                            <td class="text-right">{{ $farmer->farms->count() }} farm(s)</td>
                            <td class="text-center muted">—</td>
                            <td>{{ $farmerBarangay }}</td>
                        </tr>
                        @forelse($farmer->farms as $farm)
                            <tr class="farm-row">
                                <td>↳ {{ $farm->farm_name ?? '—' }}</td>
                                <td>{{ $farm->crop_name ?? '—' }}</td>
                                <td>{{ $farm->crop_variety ?? '—' }}</td>
                                <td class="text-right">{{ number_format((float) ($farm->crop_area ?? 0), 2) }}</td>
                                <td class="text-center">
                                    @if(strtolower((string) $farm->verified_area) === 'yes')
                                        <span class="badge badge-yes">Yes</span>
                                    @else
                                        <span class="badge badge-no">No</span>
                                    @endif
                                </td>
                                <td>{{ $farmerBarangay }}</td>
                            </tr>
                        @empty
                            <tr class="farm-row">
                                <td colspan="6" class="muted">↳ No farms registered for this farmer.</td>
                            </tr>
                        @endforelse
                    @empty
                        <tr>
                            <td colspan="6" class="text-center muted" style="padding: 18px;">
                                No farmers found for the selected filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <footer class="report-footer">
            CofSys &middot; Coffee Farm Management System &middot; {{ now()->format('Y') }}
        </footer>

    </div>

</body>
</html>
