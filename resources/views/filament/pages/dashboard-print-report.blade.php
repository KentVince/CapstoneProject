<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pest &amp; Disease Distribution Report</title>
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
        .btn-print { background: #16a34a; color: white; }
        .btn-print:hover { background: #15803d; }
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

        /* ── Header (3-column, same as farmer print) ─────────────────────── */
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
        .header-left img,
        .header-right img {
            max-width: 110px;
            max-height: 110px;
            object-fit: contain;
            object-position: center;
            background: #ffffff;
            padding: 6px;
            border-radius: 8px;
            box-shadow: 0 0 0 1px #e5e7eb;
        }
        .header-center { text-align: center; }
        .header-center img.cofsys-logo {
            height: 80px;
            margin-bottom: 6px;
        }
        .header-center h1 {
            margin: 0;
            font-size: 20px;
            color: #16a34a;
            letter-spacing: 0.5px;
        }
        .header-center p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #6b7280;
        }

        /* ── Meta row ────────────────────────────────────────────────────── */
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
            border: 1px solid #d1fae5;
            border-radius: 6px;
            padding: 12px 14px;
            background: #f0fdf4;
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
            color: #16a34a;
            line-height: 1;
        }
        .summary-card .card-value.danger { color: #dc2626; }
        .summary-card .card-value.warn   { color: #d97706; }
        .summary-card .card-sub {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
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
            background: #f0fdf4;
            color: #16a34a;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e5e7eb;
        }

        /* ── Tables ──────────────────────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th {
            background: #f0fdf4;
            border: 1px solid #d1fae5;
            padding: 7px 10px;
            text-align: left;
            font-weight: 700;
            color: #374151;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        td {
            border: 1px solid #e5e7eb;
            padding: 6px 10px;
            color: #374151;
            font-size: 12px;
        }
        tr:nth-child(even) td { background: #f9fafb; }
        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .font-bold   { font-weight: 700; }

        /* Barangay sub-header row inside distribution table */
        .bgy-row td {
            background: #16a34a !important;
            color: white !important;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: 0.3px;
        }

        /* Severity badges */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
        }
        .badge-low    { background: #dcfce7; color: #15803d; }
        .badge-medium { background: #fef9c3; color: #a16207; }
        .badge-high   { background: #fee2e2; color: #b91c1c; }

        /* Bar */
        .bar-wrap { background: #e5e7eb; border-radius: 2px; height: 8px; display: inline-block; width: 80px; vertical-align: middle; }
        .bar-fill { background: #16a34a; border-radius: 2px; height: 8px; display: block; }

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
            color: #16a34a;
            font-weight: 600;
            text-decoration: none;
        }
        footer.report-footer .site-link svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        /* ── Print ───────────────────────────────────────────────────────── */
        @media print {
            @page { size: A4; margin: 0; }
            html, body { margin: 0; padding: 0; }
            body { background: white; }
            .sheet {
                box-shadow: none;
                border-radius: 0;
                padding: 12mm 14mm;
                max-width: 100%;
            }
            .print-bar, .no-print { display: none !important; }
            section.block { page-break-inside: avoid; }
            .bgy-section  { page-break-inside: avoid; }
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
                    <h1>Pest &amp; Disease Distribution Report</h1>
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
                <div class="card-label">Total Cases</div>
                <div class="card-value">{{ number_format($totalCases) }}</div>
                <div class="card-sub">All validation statuses</div>
            </div>
            <div class="summary-card">
                <div class="card-label">Approved Cases</div>
                <div class="card-value">{{ number_format($approvedCases) }}</div>
                <div class="card-sub">Validated detections</div>
            </div>
            <div class="summary-card">
                <div class="card-label">Pending / Rejected</div>
                <div class="card-value warn">{{ number_format($pendingCases) }}</div>
                <div class="card-sub">{{ number_format($rejectedCases) }} rejected</div>
            </div>
            <div class="summary-card">
                <div class="card-label">Low Severity</div>
                <div class="card-value">{{ number_format($low) }}</div>
                <div class="card-sub">Approved cases</div>
            </div>
            <div class="summary-card">
                <div class="card-label">Medium Severity</div>
                <div class="card-value warn">{{ number_format($medium) }}</div>
                <div class="card-sub">Approved cases</div>
            </div>
            <div class="summary-card">
                <div class="card-label">High Severity</div>
                <div class="card-value danger">{{ number_format($high) }}</div>
                <div class="card-sub">Approved cases</div>
            </div>
        </div>

        {{-- ── Per-Barangay Distribution ────────────────────────────────── --}}
        <section class="block">
            <h2>Pest &amp; Disease Distribution per Barangay</h2>
            @if($byBarangay->isEmpty())
                <div style="padding: 16px; text-align: center; color: #9ca3af; font-style: italic;">
                    No approved pest &amp; disease records found for the selected filter.
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th style="width:32px;">#</th>
                            <th style="width:110px;">Purok</th>
                            <th>Pest / Disease</th>
                            <th style="width:90px;">Severity</th>
                            <th class="text-right" style="width:60px;">Cases</th>
                            <th style="width:100px;">Last Detected</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $rowNum = 0; @endphp
                        @foreach($byBarangay as $barangay => $rows)
                            <tr class="bgy-row">
                                <td colspan="6">{{ strtoupper($barangay) }} &nbsp;({{ $rows->sum('case_count') }} cases)</td>
                            </tr>
                            @foreach($rows as $row)
                                @php $rowNum++; @endphp
                                <tr>
                                    <td class="text-center" style="color:#9ca3af;">{{ $rowNum }}</td>
                                    <td>{{ $row->purok ?? '—' }}</td>
                                    <td class="font-bold">{{ $row->pest }}</td>
                                    <td>
                                        @if(strtolower($row->severity) === 'high')
                                            <span class="badge badge-high">High</span>
                                        @elseif(strtolower($row->severity) === 'medium')
                                            <span class="badge badge-medium">Medium</span>
                                        @else
                                            <span class="badge badge-low">Low</span>
                                        @endif
                                    </td>
                                    <td class="text-right font-bold">{{ number_format($row->case_count) }}</td>
                                    <td>
                                        {{ $row->last_detected
                                            ? \Carbon\Carbon::parse($row->last_detected)->format('M j, Y')
                                            : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>

        {{-- ── Top Pests Overall ───────────────────────────────────────────── --}}
        @if($topPests->isNotEmpty())
        <section class="block">
            <h2>Top Pests &amp; Diseases (Overall in Selected Area)</h2>
            @php $pdTotal = $topPests->sum('count'); @endphp
            <table>
                <thead>
                    <tr>
                        <th style="width:32px;">#</th>
                        <th>Pest / Disease</th>
                        <th class="text-right" style="width:60px;">Cases</th>
                        <th class="text-right" style="width:50px;">%</th>
                        <th style="width:120px;">Distribution</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topPests as $i => $item)
                        @php
                            $pct  = $pdTotal > 0 ? round($item->count / $pdTotal * 100, 1) : 0;
                            $barW = max(2, (int)$pct);
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td class="font-bold">{{ $item->pest }}</td>
                            <td class="text-right">{{ number_format($item->count) }}</td>
                            <td class="text-right">{{ $pct }}%</td>
                            <td>
                                <span class="bar-wrap">
                                    <span class="bar-fill" style="width:{{ $barW }}%;"></span>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>
        @endif

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
