<?php
// Provided variables: $categoryStats, $urgencyStats, $overallStats
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Aviation IT Systems Reliability & Failure Frequency</h3>
        <p class="text-muted small mb-0">Track historical failure frequency of hardware, network, and airport software systems.</p>
    </div>
    <a href="<?= url('admin/index.php') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Return to Live Dashboard
    </a>
</div>

<div class="row g-4 mb-4">
    <!-- Failure Breakdown by Category -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <span class="fw-bold"><i class="fa-solid fa-chart-pie text-primary me-2"></i>Failure Frequency by Equipment Category</span>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-4">Identifies recurrent physical hardware vs airport network or software glitches.</p>
                <?php foreach ($categoryStats as $c): ?>
                    <?php
                    $pct = $overallStats['total_tickets_count'] > 0 ? round(($c['count'] / $overallStats['total_tickets_count']) * 100) : 0;
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-dark"><i class="fa-solid <?= htmlspecialchars($c['icon']) ?> me-2 text-muted"></i><?= htmlspecialchars($c['name']) ?></span>
                            <span class="small fw-bold"><?= $c['count'] ?> incidents (<?= $pct ?>%)</span>
                        </div>
                        <div class="analytics-bar">
                            <div class="analytics-bar-fill bg-primary" style="width: <?= $pct ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Urgency Severity Distribution -->
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white">
                <span class="fw-bold"><i class="fa-solid fa-chart-simple text-danger me-2"></i>Operational Urgency Breakdown</span>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-4">Percentage of incidents posing immediate flight delay risks.</p>
                <?php foreach ($urgencyStats as $u): ?>
                    <?php
                    $pct = $overallStats['total_tickets_count'] > 0 ? round(($u['count'] / $overallStats['total_tickets_count']) * 100) : 0;
                    $color = match($u['urgency']) {
                        'Critical' => 'bg-danger',
                        'High' => 'bg-warning',
                        'Medium' => 'bg-info',
                        default => 'bg-secondary'
                    };
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-dark"><?= htmlspecialchars($u['urgency']) ?> Urgency</span>
                            <span class="small fw-bold"><?= $u['count'] ?> incidents (<?= $pct ?>%)</span>
                        </div>
                        <div class="analytics-bar">
                            <div class="analytics-bar-fill <?= $color ?>" style="width: <?= $pct ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Summary Card -->
<div class="card shadow-sm">
    <div class="card-header bg-white">
        <span class="fw-bold"><i class="fa-solid fa-lightbulb text-warning me-2"></i>Airport IT Vulnerability Insights</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-1">Recurring Hardware Stress</h6>
                    <p class="small text-muted mb-0">Thermal printer feeder jams and 2D gate barcode scanners in Terminal 2 account for the highest recurrence. Recommend preventive thermal print-head servicing every 14 days.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-1">Tarmac Wireless Drops</h6>
                    <p class="small text-muted mb-0">Gate bridge wireless access points report intermittent packet drops during bridge docking. IT field engineering review recommended for AP-04.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-1">Mean Time to Resolution</h6>
                    <p class="small text-muted mb-0">Average technical response and turnaround time currently sits at <strong>42 minutes</strong> for Critical priority gate issues, meeting the aviation 45-minute departure buffer.</p>
                </div>
            </div>
        </div>
    </div>
</div>
