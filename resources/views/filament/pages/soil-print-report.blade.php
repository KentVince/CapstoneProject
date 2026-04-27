<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Soil Analysis Report</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 24px;
            color: #111;
            background: #f5f5f5;
        }

        /* ── Toolbar ─────────────────────────────────────────────────────── */
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
        .btn-print { background: #d97706; color: white; }
        .btn-print:hover { background: #b45309; }
        .btn-back  { background: #e5e7eb; color: #374151; }
        .btn-back:hover { background: #d1d5db; }

        /* ── Sheet ───────────────────────────────────────────────────────── */
        .sheet {
            max-width: 860px;
            margin: 0 auto;
            background: white;
            padding: 32px 36px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        /* ── Header ──────────────────────────────────────────────────────── */
        header.report-header {
            border-bottom: 3px solid #d97706;
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
            color: #d97706;
            letter-spacing: 0.5px;
        }
        .header-center p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #6b7280;
        }

        /* ── Meta ────────────────────────────────────────────────────────── */
        .meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 18px;
            flex-wrap: wrap;
            gap: 4px;
        }

        /* ── Summary cards ───────────────────────────────────────────────── */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 22px;
        }
        .summary-card {
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 12px 14px;
            background: #fffbeb;
        }
        .summary-card .card-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 4px;
        }
        .summary-card .card-value {
            font-size: 26px;
            font-weight: 800;
            color: #d97706;
            line-height: 1;
        }
        .summary-card .card-value.green { color: #16a34a; }
        .summary-card .card-sub {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
        }

        /* ── Avg nutrient strip ───────────────────────────────────────────── */
        .nutrient-strip {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
            margin-bottom: 22px;
        }
        .nutrient-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
            background: #f9fafb;
        }
        .nutrient-card .n-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .nutrient-card .n-value {
            font-size: 20px;
            font-weight: 800;
            color: #374151;
            line-height: 1.2;
        }

        /* ── Section block ───────────────────────────────────────────────── */
        section.block {
            margin-bottom: 22px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            overflow: hidden;
        }
        section.block h2 {
            margin: 0;
            padding: 8px 12px;
            background: #fffbeb;
            color: #d97706;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #fde68a;
        }

        /* ── Tables ──────────────────────────────────────────────────────── */
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th {
            background: #fffbeb;
            border: 1px solid #fde68a;
            padding: 6px 8px;
            text-align: left;
            font-weight: 700;
            color: #374151;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        td {
            border: 1px solid #e5e7eb;
            padding: 5px 8px;
            color: #374151;
        }
        tr:nth-child(even) td { background: #fafafa; }
        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .font-bold   { font-weight: 700; }

        /* Barangay sub-header */
        .bgy-row td {
            background: #d97706 !important;
            color: white !important;
            font-weight: 700;
            font-size: 11px;
        }

        /* pH rating colour */
        .ph-very-acidic  { color: #dc2626; font-weight: 700; }
        .ph-acidic       { color: #ea580c; font-weight: 700; }
        .ph-slightly     { color: #ca8a04; font-weight: 700; }
        .ph-neutral      { color: #16a34a; font-weight: 700; }
        .ph-alkaline     { color: #2563eb; font-weight: 700; }

        /* Status badges */
        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
        }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-pending  { background: #fef3c7; color: #92400e; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }

        /* Bar */
        .bar-wrap { background: #e5e7eb; border-radius: 2px; height: 8px; display: inline-block; width: 80px; vertical-align: middle; }
        .bar-fill { background: #d97706; border-radius: 2px; height: 8px; display: block; }

        /* ── Footer ──────────────────────────────────────────────────────── */
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
            color: #d97706;
            font-weight: 600;
            text-decoration: none;
        }
        footer.report-footer .site-link svg { width: 14px; height: 14px; flex-shrink: 0; }

        /* ── Print ───────────────────────────────────────────────────────── */
        @media print {
            @page { size: A4; margin: 0; }
            html, body { margin: 0; padding: 0; }
            body { background: white; }
            .sheet { box-shadow: none; border-radius: 0; padding: 12mm 14mm; max-width: 100%; }
            .print-bar, .no-print { display: none !important; }
            section.block { page-break-inside: avoid; }
        }
    </style>
</head>
<body>

    <div class="print-bar no-print">
        <a href="javascript:history.back()" class="btn btn-back">&#8592; Back</a>
        <button class="btn btn-print" onclick="window.print()">Print Report</button>
    </div>

    <div class="sheet">

        {{-- ── Header ─────────────────────────────────────────────────────── --}}
        <header class="report-header">
            <div class="header-grid">
                <div class="header-left">
                    <img src="{{ asset('images/magso_logo.png') }}" alt="MAGSO">
                </div>
                <div class="header-center">
                    <h1>Soil Analysis Report</h1>
                    <p>Coffee Farm Management System with Smart Disease Detection and Geoanalytics</p>
                </div>
                <div class="header-right">
                    <img src="{{ asset('images/cofsys_print_logo.png') }}" alt="CofSys" class="cofsys-logo">
                </div>
            </div>
        </header>

        {{-- ── Meta ───────────────────────────────────────────────────────── --}}
        <div class="meta-row">
            <div><strong>Municipality:</strong> {{ $municipal ?? 'All Municipalities' }}</div>
            <div><strong>Barangay:</strong> {{ $barangayName ?? 'All Barangays' }}</div>
            <div><strong>Generated:</strong> {{ now()->format('F j, Y, g:i a') }}</div>
        </div>

        {{-- ── Summary cards ───────────────────────────────────────────────── --}}
        <div class="summary-grid">
            <div class="summary-card">
                <div class="card-label">Total Farms (Scope)</div>
                <div class="card-value">{{ number_format($totalFarmsInScope) }}</div>
                <div class="card-sub">In selected area</div>
            </div>
            <div class="summary-card">
                <div class="card-label">Farms with Soil Records</div>
                <div class="card-value green">{{ number_format($farmsWithSoilRecord) }}</div>
                <div class="card-sub">At least one analysis submitted</div>
            </div>
            <div class="summary-card">
                <div class="card-label">Farms with Lab Data</div>
                <div class="card-value green">{{ number_format($farmsWithLabData) }}</div>
                <div class="card-sub">Have a laboratory number</div>
            </div>
            <div class="summary-card">
                <div class="card-label">Total Soil Tests</div>
                <div class="card-value">{{ number_format($totalSoilTests) }}</div>
                <div class="card-sub">All statuses</div>
            </div>
            <div class="summary-card">
                <div class="card-label">Approved Tests</div>
                <div class="card-value green">{{ number_format($approvedTests) }}</div>
                <div class="card-sub">Validated by expert</div>
            </div>
            <div class="summary-card">
                <div class="card-label">Pending Tests</div>
                <div class="card-value">{{ number_format($pendingTests) }}</div>
                <div class="card-sub">Awaiting validation</div>
            </div>
        </div>

        {{-- ── Average Nutrient Levels ──────────────────────────────────────── --}}
        <section class="block">
            <h2>Average Nutrient Levels (Approved Samples)</h2>
            <div style="padding: 14px;">
                <div class="nutrient-strip">
                    <div class="nutrient-card">
                        <div class="n-label">Soil pH</div>
                        <div class="n-value">{{ $avgPh ?: '—' }}</div>
                        <div style="font-size:10px;color:#6b7280;margin-top:2px;">
                            @if($avgPh)
                                @if($avgPh < 5.5) Very Acidic
                                @elseif($avgPh < 6.0) Acidic
                                @elseif($avgPh < 6.5) Slightly Acidic
                                @elseif($avgPh < 7.0) Neutral
                                @else Alkaline @endif
                            @endif
                        </div>
                    </div>
                    <div class="nutrient-card">
                        <div class="n-label">Nitrogen (N)</div>
                        <div class="n-value">{{ $avgN ?: '—' }}</div>
                        <div style="font-size:10px;color:#6b7280;margin-top:2px;">%</div>
                    </div>
                    <div class="nutrient-card">
                        <div class="n-label">Phosphorus (P)</div>
                        <div class="n-value">{{ $avgP ?: '—' }}</div>
                        <div style="font-size:10px;color:#6b7280;margin-top:2px;">ppm</div>
                    </div>
                    <div class="nutrient-card">
                        <div class="n-label">Potassium (K)</div>
                        <div class="n-value">{{ $avgK ?: '—' }}</div>
                        <div style="font-size:10px;color:#6b7280;margin-top:2px;">cmol/kg</div>
                    </div>
                    <div class="nutrient-card">
                        <div class="n-label">Organic Matter</div>
                        <div class="n-value">{{ $avgOm ?: '—' }}</div>
                        <div style="font-size:10px;color:#6b7280;margin-top:2px;">%</div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ── pH Distribution ──────────────────────────────────────────────── --}}
        <section class="block">
            <h2>Soil pH Distribution</h2>
            @php
                $phOrder = ['Very Acidic (< 5.5)', 'Acidic (5.5–6.0)', 'Slightly Acidic (6.0–6.5)', 'Neutral (6.5–7.0)', 'Alkaline (> 7.0)'];
                $phMap   = $phDistribution->pluck('count', 'ph_range');
                $phTotal = $phDistribution->sum('count');
                $phClasses = [
                    'Very Acidic (< 5.5)'      => 'ph-very-acidic',
                    'Acidic (5.5–6.0)'          => 'ph-acidic',
                    'Slightly Acidic (6.0–6.5)' => 'ph-slightly',
                    'Neutral (6.5–7.0)'         => 'ph-neutral',
                    'Alkaline (> 7.0)'          => 'ph-alkaline',
                ];
            @endphp
            <table>
                <thead>
                    <tr>
                        <th>pH Range</th>
                        <th class="text-right" style="width:70px;">Samples</th>
                        <th class="text-right" style="width:55px;">%</th>
                        <th style="width:120px;">Distribution</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($phOrder as $range)
                        @php
                            $cnt  = (int)($phMap[$range] ?? 0);
                            $pct  = $phTotal > 0 ? round($cnt / $phTotal * 100, 1) : 0;
                            $barW = max(2, (int)$pct);
                        @endphp
                        <tr>
                            <td class="{{ $phClasses[$range] ?? '' }}">{{ $range }}</td>
                            <td class="text-right font-bold">{{ number_format($cnt) }}</td>
                            <td class="text-right">{{ $pct }}%</td>
                            <td>
                                @if($cnt > 0)
                                    <span class="bar-wrap"><span class="bar-fill" style="width:{{ $barW }}%;"></span></span>
                                @else
                                    <span style="color:#d1d5db;">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if($phTotal > 0)
                        <tr>
                            <td class="font-bold">Total</td>
                            <td class="text-right font-bold">{{ number_format($phTotal) }}</td>
                            <td class="text-right font-bold">100%</td>
                            <td></td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </section>

        {{-- ── Nutrient Levels per Barangay ────────────────────────────────── --}}
        <section class="block">
            <h2>Average Nutrient Levels per Barangay</h2>
            @if($nutrientByBarangay->isEmpty())
                <div style="padding:14px;text-align:center;color:#9ca3af;font-style:italic;">No approved soil analysis data in the selected area.</div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Barangay</th>
                            <th class="text-right" style="width:60px;">Samples</th>
                            <th class="text-right" style="width:55px;">pH</th>
                            <th class="text-right" style="width:55px;">N (%)</th>
                            <th class="text-right" style="width:60px;">P (ppm)</th>
                            <th class="text-right" style="width:65px;">K (cmol)</th>
                            <th class="text-right" style="width:55px;">OM (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($nutrientByBarangay as $row)
                            <tr>
                                <td class="font-bold">{{ $row->barangay_name }}</td>
                                <td class="text-right">{{ number_format($row->sample_count) }}</td>
                                <td class="text-right">{{ $row->avg_ph ?: '—' }}</td>
                                <td class="text-right">{{ $row->avg_n ?: '—' }}</td>
                                <td class="text-right">{{ $row->avg_p ?: '—' }}</td>
                                <td class="text-right">{{ $row->avg_k ?: '—' }}</td>
                                <td class="text-right">{{ $row->avg_om ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        {{-- ── Lab Records Table ───────────────────────────────────────────── --}}
        <section class="block">
            <h2>Soil Analysis Records</h2>
            @if($labRecords->isEmpty())
                <div style="padding:14px;text-align:center;color:#9ca3af;font-style:italic;">No soil analysis records found for the selected area.</div>
            @else
                @php $grouped = $labRecords->groupBy('barangay_name'); @endphp
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Purok</th>
                            <th>Farm Name</th>
                            <th>Lab No.</th>
                            <th>Crop Variety</th>
                            <th class="text-center" style="width:75px;">Date Collected</th>
                            <th class="text-right" style="width:40px;">pH</th>
                            <th class="text-right" style="width:38px;">N</th>
                            <th class="text-right" style="width:38px;">P</th>
                            <th class="text-right" style="width:38px;">K</th>
                            <th class="text-right" style="width:40px;">OM</th>
                            <th class="text-center" style="width:65px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowNum = 0; @endphp
                        @foreach($grouped as $barangay => $records)
                            <tr class="bgy-row">
                                <td colspan="12">{{ strtoupper($barangay) }} &nbsp;({{ $records->count() }} records)</td>
                            </tr>
                            @foreach($records as $rec)
                                @php $rowNum++; @endphp
                                <tr>
                                    <td class="text-center" style="color:#9ca3af;">{{ $rowNum }}</td>
                                    <td>{{ $rec->purok ?? '—' }}</td>
                                    <td class="font-bold">{{ $rec->farm_name ?? '—' }}</td>
                                    <td>{{ $rec->lab_no ?? '—' }}</td>
                                    <td>{{ $rec->crop_variety ?? '—' }}</td>
                                    <td class="text-center">
                                        {{ $rec->date_collected ? \Carbon\Carbon::parse($rec->date_collected)->format('M j, Y') : '—' }}
                                    </td>
                                    <td class="text-right">{{ $rec->ph_level ?? '—' }}</td>
                                    <td class="text-right">{{ $rec->nitrogen ?? '—' }}</td>
                                    <td class="text-right">{{ $rec->phosphorus ?? '—' }}</td>
                                    <td class="text-right">{{ $rec->potassium ?? '—' }}</td>
                                    <td class="text-right">{{ $rec->organic_matter ?? '—' }}</td>
                                    <td class="text-center">
                                        @if($rec->validation_status === 'approved')
                                            <span class="badge badge-approved">Approved</span>
                                        @elseif($rec->validation_status === 'pending')
                                            <span class="badge badge-pending">Pending</span>
                                        @else
                                            <span class="badge badge-rejected">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        {{-- ── Footer ─────────────────────────────────────────────────────── --}}
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
