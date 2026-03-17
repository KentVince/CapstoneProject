<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CofSys Dashboard Report</title>
    <style>
        /* ── Base ────────────────────────────────────────────────────────── */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #1a1a1a;
            background: #f3f4f6;
            padding: 24px;
        }

        .page-wrap {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 32px 36px;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,.12);
        }

        /* ── No-print toolbar ────────────────────────────────────────────── */
        .toolbar {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-print  { background: #16a34a; color: #fff; }
        .btn-print:hover { background: #15803d; }
        .btn-back   { background: #e5e7eb; color: #374151; }
        .btn-back:hover { background: #d1d5db; }

        /* ── Report header ───────────────────────────────────────────────── */
        .report-header {
            text-align: center;
            border-bottom: 3px solid #16a34a;
            padding-bottom: 16px;
            margin-bottom: 24px;
        }

        .report-header .system-name {
            font-size: 26px;
            font-weight: 900;
            color: #16a34a;
            letter-spacing: 2px;
        }

        .report-header .system-sub {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        .report-header .report-title {
            font-size: 16px;
            font-weight: 700;
            margin: 10px 0 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .report-header .meta {
            font-size: 11px;
            color: #6b7280;
            line-height: 1.6;
        }

        .report-header .meta strong { color: #374151; }

        /* ── Section titles ──────────────────────────────────────────────── */
        .section { margin-bottom: 24px; }

        .section-title {
            background: #16a34a;
            color: #fff;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            border-radius: 4px 4px 0 0;
        }

        .section-body {
            border: 1px solid #d1fae5;
            border-top: none;
            border-radius: 0 0 4px 4px;
            padding: 14px;
        }

        /* ── Summary grid ────────────────────────────────────────────────── */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
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
            margin-bottom: 4px;
        }

        .summary-card .card-value {
            font-size: 24px;
            font-weight: 800;
            color: #16a34a;
            line-height: 1;
        }

        .summary-card .card-sub {
            font-size: 11px;
            color: #6b7280;
            margin-top: 3px;
        }

        /* ── Two-column row ──────────────────────────────────────────────── */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

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
        }

        td {
            border: 1px solid #e5e7eb;
            padding: 6px 10px;
            color: #374151;
        }

        tr:nth-child(even) td { background: #f9fafb; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: 700; }

        /* Severity badges */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-low      { background: #dcfce7; color: #15803d; }
        .badge-medium   { background: #fef9c3; color: #a16207; }
        .badge-high     { background: #fee2e2; color: #b91c1c; }
        .badge-pending  { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }

        /* Trend bar (text-based) */
        .bar-wrap { background: #e5e7eb; border-radius: 2px; height: 10px; display: inline-block; width: 100px; vertical-align: middle; }
        .bar-fill { background: #16a34a; border-radius: 2px; height: 10px; display: block; }

        /* ── Footer ──────────────────────────────────────────────────────── */
        .report-footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #9ca3af;
        }

        /* ── Print media ─────────────────────────────────────────────────── */
        @media print {
            body { background: #fff; padding: 0; }
            .page-wrap { box-shadow: none; border-radius: 0; padding: 20px; }
            .toolbar { display: none !important; }

            table { page-break-inside: auto; }
            tr    { page-break-inside: avoid; }
            .section { page-break-inside: avoid; }

            @page {
                size: A4 portrait;
                margin: 15mm 12mm;
            }
        }
    </style>
</head>
<body>
<div class="page-wrap">

    
    <div class="toolbar">
        <a href="javascript:history.back()" class="btn btn-back">&#8592; Back</a>
        <button class="btn btn-print" onclick="window.print()">
            🖨️&nbsp; Print Report
        </button>
    </div>

    
    <div class="report-header">
        <div class="system-name">CofSys</div>
        <div class="system-sub">Coffee Farm Management System with Smart Disease Detection and GeoAnalytics</div>
        <div class="report-title">Dashboard Summary Report</div>
        <div class="meta">
            <strong>Report Period:</strong>
            <?php echo e(\Carbon\Carbon::parse($startDate)->format('F j, Y')); ?>

            &nbsp;–&nbsp;
            <?php echo e(\Carbon\Carbon::parse($endDate)->format('F j, Y')); ?>

            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Municipality:</strong> <?php echo e($municipal ?? 'All Municipalities'); ?>

            &nbsp;&nbsp;|&nbsp;&nbsp;
            <strong>Generated:</strong> <?php echo e(now()->format('F j, Y  h:i A')); ?>

        </div>
    </div>

    
    <div class="section">
        <div class="section-title">1. Summary Statistics</div>
        <div class="section-body">
            <div class="summary-grid">

                <div class="summary-card">
                    <div class="card-label">Total Registered Farmers</div>
                    <div class="card-value"><?php echo e(number_format($totalFarmers)); ?></div>
                    <div class="card-sub">All-time registrations</div>
                </div>

                <div class="summary-card">
                    <div class="card-label">Total Registered Farms</div>
                    <div class="card-value"><?php echo e(number_format($totalFarms)); ?></div>
                    <div class="card-sub"><?php echo e(number_format($totalArea, 2)); ?> hectares total</div>
                </div>

                <div class="summary-card">
                    <div class="card-label">Total Pest &amp; Disease Cases</div>
                    <div class="card-value"><?php echo e(number_format($totalCases)); ?></div>
                    <div class="card-sub"><?php echo e(number_format($approvedCases)); ?> approved</div>
                </div>

                <div class="summary-card">
                    <div class="card-label">Critical Cases (High Severity)</div>
                    <div class="card-value" style="color:#dc2626;"><?php echo e(number_format($criticalCases)); ?></div>
                    <div class="card-sub">Approved, high severity</div>
                </div>

                <div class="summary-card">
                    <div class="card-label">Soil Analysis Tests</div>
                    <div class="card-value"><?php echo e(number_format($totalSoilTests)); ?></div>
                    <div class="card-sub">All-time tests conducted</div>
                </div>

                <div class="summary-card">
                    <div class="card-label">Average Soil pH Level</div>
                    <div class="card-value"><?php echo e($avgPh ?: '–'); ?></div>
                    <div class="card-sub">
                        <?php if($avgPh): ?>
                            <?php if($avgPh < 5.5): ?> Very Acidic
                            <?php elseif($avgPh < 6.0): ?> Acidic
                            <?php elseif($avgPh < 6.5): ?> Slightly Acidic
                            <?php elseif($avgPh < 7.0): ?> Neutral
                            <?php else: ?> Alkaline
                            <?php endif; ?>
                        <?php else: ?>
                            No data
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    
    <div class="two-col">

        <div class="section">
            <div class="section-title">2. Cases by Severity</div>
            <div class="section-body" style="padding: 0;">
                <?php
                    $low    = (int)($casesBySeverity['low']    ?? 0);
                    $medium = (int)($casesBySeverity['medium'] ?? 0);
                    $high   = (int)($casesBySeverity['high']   ?? 0);
                    $sevTotal = $low + $medium + $high;
                ?>
                <table>
                    <thead>
                        <tr>
                            <th>Severity</th>
                            <th class="text-right">Cases</th>
                            <th class="text-right">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge badge-low">Low</span></td>
                            <td class="text-right"><?php echo e(number_format($low)); ?></td>
                            <td class="text-right"><?php echo e($sevTotal > 0 ? round($low / $sevTotal * 100, 1) : 0); ?>%</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-medium">Medium</span></td>
                            <td class="text-right"><?php echo e(number_format($medium)); ?></td>
                            <td class="text-right"><?php echo e($sevTotal > 0 ? round($medium / $sevTotal * 100, 1) : 0); ?>%</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-high">High</span></td>
                            <td class="text-right"><?php echo e(number_format($high)); ?></td>
                            <td class="text-right"><?php echo e($sevTotal > 0 ? round($high / $sevTotal * 100, 1) : 0); ?>%</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Total</td>
                            <td class="text-right font-bold"><?php echo e(number_format($sevTotal)); ?></td>
                            <td class="text-right font-bold">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section">
            <div class="section-title">3. Validation Status Breakdown</div>
            <div class="section-body" style="padding: 0;">
                <?php
                    $pending  = (int)($validationStatus['pending']  ?? 0);
                    $approved = (int)($validationStatus['approved'] ?? 0);
                    $rejected = (int)($validationStatus['rejected'] ?? 0);
                    $valTotal = $pending + $approved + $rejected;
                ?>
                <table>
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th class="text-right">Cases</th>
                            <th class="text-right">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="badge badge-pending">Pending</span></td>
                            <td class="text-right"><?php echo e(number_format($pending)); ?></td>
                            <td class="text-right"><?php echo e($valTotal > 0 ? round($pending / $valTotal * 100, 1) : 0); ?>%</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-approved">Approved</span></td>
                            <td class="text-right"><?php echo e(number_format($approved)); ?></td>
                            <td class="text-right"><?php echo e($valTotal > 0 ? round($approved / $valTotal * 100, 1) : 0); ?>%</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-rejected">Rejected</span></td>
                            <td class="text-right"><?php echo e(number_format($rejected)); ?></td>
                            <td class="text-right"><?php echo e($valTotal > 0 ? round($rejected / $valTotal * 100, 1) : 0); ?>%</td>
                        </tr>
                        <tr>
                            <td class="font-bold">Total</td>
                            <td class="text-right font-bold"><?php echo e(number_format($valTotal)); ?></td>
                            <td class="text-right font-bold">100%</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    
    <div class="section">
        <div class="section-title">4. Top Pests &amp; Diseases Detected (Period)</div>
        <div class="section-body" style="padding: 0;">
            <?php $pdTotal = $topPests->sum('count'); ?>
            <table>
                <thead>
                    <tr>
                        <th class="text-center" style="width:40px;">#</th>
                        <th>Pest / Disease Name</th>
                        <th class="text-right" style="width:80px;">Cases</th>
                        <th class="text-right" style="width:60px;">%</th>
                        <th style="width:130px;">Distribution</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $topPests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $pct = $pdTotal > 0 ? round($item->count / $pdTotal * 100, 1) : 0;
                            $barW = max(2, (int)($pct));
                        ?>
                        <tr>
                            <td class="text-center"><?php echo e($i + 1); ?></td>
                            <td class="font-bold"><?php echo e($item->pest); ?></td>
                            <td class="text-right"><?php echo e(number_format($item->count)); ?></td>
                            <td class="text-right"><?php echo e($pct); ?>%</td>
                            <td>
                                <span class="bar-wrap">
                                    <span class="bar-fill" style="width:<?php echo e($barW); ?>%;"></span>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center" style="padding:12px;color:#9ca3af;">
                                No approved detections in the selected period.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="section">
        <div class="section-title">5. Monthly Detection Trend (Last 12 Months)</div>
        <div class="section-body" style="padding: 0;">
            <?php $maxMonthCount = $monthlyTrend->max('count') ?: 1; ?>
            <table>
                <thead>
                    <tr>
                        <th style="width:110px;">Month</th>
                        <th class="text-right" style="width:70px;">Cases</th>
                        <th>Trend</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $monthlyTrend; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $barPct = (int) round($row['count'] / $maxMonthCount * 100);
                        ?>
                        <tr>
                            <td><?php echo e($row['month']); ?></td>
                            <td class="text-right font-bold"><?php echo e($row['count']); ?></td>
                            <td>
                                <?php if($row['count'] > 0): ?>
                                    <span class="bar-wrap" style="width:180px;">
                                        <span class="bar-fill" style="width:<?php echo e($barPct); ?>%;"></span>
                                    </span>
                                <?php else: ?>
                                    <span style="color:#d1d5db;">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="two-col">

        
        <div class="section">
            <div class="section-title">6. Farms by Municipality</div>
            <div class="section-body" style="padding: 0;">
                <?php $farmTotal = $farmsByMunicipality->sum('count'); ?>
                <table>
                    <thead>
                        <tr>
                            <th>Municipality</th>
                            <th class="text-right">Farms</th>
                            <th class="text-right">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $farmsByMunicipality; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($row->municipality); ?></td>
                                <td class="text-right"><?php echo e(number_format($row->count)); ?></td>
                                <td class="text-right">
                                    <?php echo e($farmTotal > 0 ? round($row->count / $farmTotal * 100, 1) : 0); ?>%
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="text-center" style="padding:12px;color:#9ca3af;">No data.</td>
                            </tr>
                        <?php endif; ?>
                        <?php if($farmsByMunicipality->count()): ?>
                            <tr>
                                <td class="font-bold">Total (top 10)</td>
                                <td class="text-right font-bold"><?php echo e(number_format($farmTotal)); ?></td>
                                <td></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        
        <div class="section">
            <div class="section-title">7. Soil pH Distribution</div>
            <div class="section-body" style="padding: 0;">
                <?php $soilTotal = $soilPhDistribution->sum('count'); ?>
                <table>
                    <thead>
                        <tr>
                            <th>pH Range</th>
                            <th class="text-right">Samples</th>
                            <th class="text-right">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $phOrder = [
                                'Very Acidic (< 5.5)',
                                'Acidic (5.5–6.0)',
                                'Slightly Acidic (6.0–6.5)',
                                'Neutral (6.5–7.0)',
                                'Alkaline (> 7.0)',
                            ];
                            $phMap = $soilPhDistribution->pluck('count', 'ph_range');
                        ?>
                        <?php $__currentLoopData = $phOrder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $range): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $cnt = (int)($phMap[$range] ?? 0); ?>
                            <tr>
                                <td><?php echo e($range); ?></td>
                                <td class="text-right"><?php echo e(number_format($cnt)); ?></td>
                                <td class="text-right">
                                    <?php echo e($soilTotal > 0 ? round($cnt / $soilTotal * 100, 1) : 0); ?>%
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($soilTotal > 0): ?>
                            <tr>
                                <td class="font-bold">Total</td>
                                <td class="text-right font-bold"><?php echo e(number_format($soilTotal)); ?></td>
                                <td class="text-right font-bold">100%</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    
    <div class="report-footer">
        <strong>CofSys</strong> – Coffee Farm Management System with Smart Disease Detection and GeoAnalytics
        &nbsp;|&nbsp; Report generated on <?php echo e(now()->format('F j, Y \a\t h:i A')); ?>

        &nbsp;|&nbsp; For official use only.
    </div>

</div>
</body>
</html>
<?php /**PATH /var/www/html/CapstoneProject/resources/views/filament/pages/dashboard-print-report.blade.php ENDPATH**/ ?>