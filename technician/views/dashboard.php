<?php
// Provided variables: $tickets, $stats, $statusFilter
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Technician Support Queue</h3>
        <p class="text-muted small mb-0">Manage technical issues, diagnose hardware/network failures, and record resolution notes.</p>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold text-uppercase">My In-Progress</span>
                <h2 class="fw-bold mt-1 text-info mb-0"><?= intval($stats['in_progress_count'] ?? 0) ?></h2>
            </div>
            <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info fs-3">
                <i class="fa-solid fa-spinner"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold text-uppercase">Unassigned Open</span>
                <h2 class="fw-bold mt-1 text-warning mb-0"><?= intval($stats['unassigned_open_count'] ?? 0) ?></h2>
            </div>
            <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning fs-3">
                <i class="fa-solid fa-bell"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold text-uppercase">Resolved Today</span>
                <h2 class="fw-bold mt-1 text-success mb-0"><?= intval($stats['resolved_today_count'] ?? 0) ?></h2>
            </div>
            <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success fs-3">
                <i class="fa-solid fa-check-circle"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold text-uppercase">Total Resolved</span>
                <h2 class="fw-bold mt-1 text-dark mb-0"><?= intval($stats['total_resolved_count'] ?? 0) ?></h2>
            </div>
            <div class="rounded-circle bg-secondary bg-opacity-10 p-3 text-secondary fs-3">
                <i class="fa-solid fa-trophy"></i>
            </div>
        </div>
    </div>
</div>

<!-- Table of Tickets -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-list-check text-muted"></i>
            <span class="fw-bold">Active IT Incident Queue</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <label for="techStatusFilter" class="small text-muted fw-bold mb-0">Filter Status:</label>
            <select id="techStatusFilter" class="form-select form-select-sm" style="width: 170px;">
                <option value="all" <?= ($statusFilter === 'all') ? 'selected' : '' ?>>All Assigned & Open</option>
                <option value="Open" <?= ($statusFilter === 'Open') ? 'selected' : '' ?>>Open Only</option>
                <option value="In Progress" <?= ($statusFilter === 'In Progress') ? 'selected' : '' ?>>In Progress Only</option>
                <option value="Resolved" <?= ($statusFilter === 'Resolved') ? 'selected' : '' ?>>Resolved</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Incident & Location</th>
                    <th>Requester</th>
                    <th>Category</th>
                    <th>Urgency</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-circle-check fs-1 text-success mb-2 d-block"></i>
                            <div class="fw-bold">No active tickets waiting in queue.</div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border fw-bold"><?= htmlspecialchars($t['ticket_code']) ?></span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($t['subject']) ?></div>
                                <small class="text-muted"><i class="fa-solid fa-location-dot me-1 text-danger"></i><?= htmlspecialchars($t['location']) ?></small>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark"><?= htmlspecialchars($t['requester_name']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($t['requester_dept']) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border">
                                    <i class="fa-solid <?= htmlspecialchars($t['category_icon']) ?> me-1"></i>
                                    <?= htmlspecialchars($t['category_name']) ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $urgencyClass = match($t['urgency']) {
                                    'Critical' => 'badge-urgency-critical',
                                    'High' => 'badge-urgency-high',
                                    'Medium' => 'badge-urgency-medium',
                                    default => 'badge-urgency-low'
                                };
                                ?>
                                <span class="badge <?= $urgencyClass ?>"><?= htmlspecialchars($t['urgency']) ?></span>
                            </td>
                            <td>
                                <?php
                                $statusClass = match($t['status']) {
                                    'Open' => 'badge-status-open',
                                    'In Progress' => 'badge-status-progress',
                                    'Resolved' => 'badge-status-resolved',
                                    default => 'badge-status-closed'
                                };
                                ?>
                                <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($t['status']) ?></span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= $t['technician_name'] ? htmlspecialchars($t['technician_name']) : '<span class="badge bg-warning text-dark">Unassigned</span>' ?>
                                </small>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('technician/index.php?page=manage&id=' . $t['id']) ?>" class="btn btn-sm btn-nimbus">
                                    <i class="fa-solid fa-wrench me-1"></i> Diagnose & Update
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
