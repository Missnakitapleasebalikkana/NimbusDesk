<!-- Chart.js Library (Offline Local) -->
<script src="/NimbusDesk/assets/vendor/chartjs/chart.umd.min.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <p class="text-muted small mb-0">
            Aviation IT Operations & Maintenance Audit Report • Generated <?= date('F d, Y h:i A') ?> (PST / UTC+8)
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="/NimbusDesk/admin/export_csv.php" class="btn btn-outline-success btn-sm">
            <i class="fa-solid fa-file-csv me-1"></i> Export Incident Log
        </a>
        <button onclick="window.print()" class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-print me-1"></i> Print / Save Report
        </button>
    </div>
</div>

<!-- Executive Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 5px solid var(--nimbus-maroon) !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Total Incidents</h6>
                    <h2 class="fw-bold mb-0" style="color: var(--nimbus-maroon);"><?= (int)$stats['total'] ?></h2>
                    <small class="text-muted">Recorded to date</small>
                </div>
                <div class="p-3 rounded-circle bg-danger bg-opacity-10 text-danger fs-3">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 5px solid #198754 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Resolution Rate</h6>
                    <h2 class="fw-bold mb-0 text-success"><?= $resolutionRate ?>%</h2>
                    <small class="text-muted"><?= (int)$stats['resolved'] ?> of <?= (int)$stats['total'] ?> closed/fixed</small>
                </div>
                <div class="p-3 rounded-circle bg-success bg-opacity-10 text-success fs-3">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 5px solid #0d6efd !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Mean Resolution Time</h6>
                    <h2 class="fw-bold mb-0 text-primary"><?= $mttr ?> hrs</h2>
                    <small class="text-muted">Average MTTR metric</small>
                </div>
                <div class="p-3 rounded-circle bg-primary bg-opacity-10 text-primary fs-3">
                    <i class="fa-solid fa-stopwatch"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 5px solid #dc3545 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Critical Outages</h6>
                    <h2 class="fw-bold mb-0 text-danger"><?= (int)$stats['critical'] ?></h2>
                    <small class="text-muted">Unresolved high-impact</small>
                </div>
                <div class="p-3 rounded-circle bg-danger bg-opacity-10 text-danger fs-3">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section 1 -->
<div class="row g-4 mb-4">
    <!-- Category Breakdown Chart -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4 h-100 bg-white">
            <h6 class="fw-bold mb-3" style="color: var(--nimbus-maroon);">
                <i class="fa-solid fa-chart-pie me-2"></i>Incident Frequency by Category
            </h6>
            <div style="position: relative; height: 260px;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Urgency Distribution Chart -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4 h-100 bg-white">
            <h6 class="fw-bold mb-3" style="color: var(--nimbus-maroon);">
                <i class="fa-solid fa-signal me-2"></i>Incident Breakdown by Operational Urgency
            </h6>
            <div style="position: relative; height: 260px;">
                <canvas id="urgencyChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section 2 -->
<div class="row g-4 mb-4">
    <!-- Terminal Distribution Chart -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4 h-100 bg-white">
            <h6 class="fw-bold mb-3" style="color: var(--nimbus-maroon);">
                <i class="fa-solid fa-building me-2"></i>Incident Distribution by Airport Terminal
            </h6>
            <div style="position: relative; height: 260px;">
                <canvas id="terminalChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Status Lifecycle Chart -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm p-4 h-100 bg-white">
            <h6 class="fw-bold mb-3" style="color: var(--nimbus-maroon);">
                <i class="fa-solid fa-arrows-spin me-2"></i>Incident Workflow Status Lifecycle
            </h6>
            <div style="position: relative; height: 260px;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Specific Objective #3: Recurring System Vulnerabilities Analysis -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0" style="color: var(--nimbus-maroon);">
            <i class="fa-solid fa-shield-virus me-2"></i>Recurring System Failures & Vulnerability Tracking (Objective #3)
        </h6>
        <small class="text-muted">Highlights airport infrastructure bottlenecks and equipment requiring proactive maintenance.</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Incident Category</th>
                    <th>Airport Terminal / Zone</th>
                    <th>Failure Frequency</th>
                    <th>High / Critical Impact</th>
                    <th>Vulnerability Level</th>
                    <th>Suggested Preventive Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recurring)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No recurrent failure patterns identified.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recurring as $item): ?>
                        <tr>
                            <td class="ps-3 fw-bold">
                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($item['category']) ?></span>
                            </td>
                            <td>
                                <i class="fa-solid fa-building text-muted me-1"></i>
                                <?= htmlspecialchars($item['terminal']) ?>
                            </td>
                            <td>
                                <strong><?= (int)$item['incident_count'] ?></strong> reports
                            </td>
                            <td>
                                <?php if ((int)$item['high_impact_count'] > 0): ?>
                                    <span class="badge bg-danger"><?= (int)$item['high_impact_count'] ?> Critical / High</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">0 Low impact</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int)$item['incident_count'] >= 2): ?>
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i>Recurring Hotspot</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark">Monitored</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted">
                                <?php if ($item['category'] === 'Hardware'): ?>
                                    Inspect optical laser scanners, check serial bus connections, and test power delivery.
                                <?php elseif ($item['category'] === 'Software'): ?>
                                    Verify print spoolers, audit background telemetry sync, and restart telemetry daemons.
                                <?php elseif ($item['category'] === 'Network'): ?>
                                    Audit PoE switches, test fiber backbone transceiver, and inspect Wi-Fi coverage channels.
                                <?php else: ?>
                                    Verify facility power and inspect environmental climate controls.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Chart initialization script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Category Chart
    const catData = <?= json_encode($categories) ?>;
    const catLabels = catData.map(d => d.category);
    const catCounts = catData.map(d => parseInt(d.count));
    
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: catLabels,
            datasets: [{
                data: catCounts,
                backgroundColor: ['#601a35', '#f191b5', '#0dcaf0', '#ffc107', '#6c757d'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' }
            }
        }
    });

    // 2. Urgency Chart
    const urgData = <?= json_encode($urgencies) ?>;
    const urgLabels = urgData.map(d => d.urgency);
    const urgCounts = urgData.map(d => parseInt(d.count));
    const urgColors = urgLabels.map(l => {
        if (l === 'Critical') return '#dc3545';
        if (l === 'High') return '#fd7e14';
        if (l === 'Medium') return '#ffc107';
        return '#0dcaf0';
    });

    new Chart(document.getElementById('urgencyChart'), {
        type: 'bar',
        data: {
            labels: urgLabels,
            datasets: [{
                label: 'Tickets',
                data: urgCounts,
                backgroundColor: urgColors,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    // 3. Terminal Chart
    const termData = <?= json_encode($terminals) ?>;
    const termLabels = termData.map(d => d.terminal);
    const termCounts = termData.map(d => parseInt(d.count));

    new Chart(document.getElementById('terminalChart'), {
        type: 'bar',
        data: {
            labels: termLabels,
            datasets: [{
                label: 'Incidents Reported',
                data: termCounts,
                backgroundColor: '#601a35',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    // 4. Status Chart
    const statusData = <?= json_encode($statuses) ?>;
    const statusLabels = statusData.map(d => d.status);
    const statusCounts = statusData.map(d => parseInt(d.count));

    new Chart(document.getElementById('statusChart'), {
        type: 'pie',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: ['#ffc107', '#0dcaf0', '#198754', '#212529'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' }
            }
        }
    });
});
</script>
