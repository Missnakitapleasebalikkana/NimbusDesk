<?php
// Provided variables: $tickets, $stats, $technicians, $categories, $filters
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Aviation IT Help Desk Command Dashboard</h3>
        <p class="text-muted small mb-0">Live triage, technician assignments, and technical escalation for airport operations.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('admin/index.php?page=history') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-clock-rotate-left me-1"></i> Incident History
        </a>
        <a href="<?= url('admin/index.php?page=reports') ?>" class="btn btn-nimbus btn-sm">
            <i class="fa-solid fa-chart-pie me-1"></i> Vulnerability Summary
        </a>
    </div>
</div>

<!-- KPI Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card kpi-card-critical d-flex align-items-center justify-content-between">
            <div>
                <span class="text-danger small fw-bold text-uppercase">Critical Urgency</span>
                <h2 class="fw-bold mt-1 text-danger mb-0"><?= intval($stats['critical_count'] ?? 0) ?></h2>
                <small class="text-muted">Direct flight delay risk</small>
            </div>
            <div class="rounded-circle bg-danger bg-opacity-10 p-3 text-danger fs-3">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card kpi-card-open d-flex align-items-center justify-content-between">
            <div>
                <span class="text-warning small fw-bold text-uppercase">Open / Unassigned</span>
                <h2 class="fw-bold mt-1 text-warning mb-0"><?= intval($stats['open_count'] ?? 0) ?></h2>
                <small class="text-muted">Awaiting IT action</small>
            </div>
            <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning fs-3">
                <i class="fa-solid fa-envelope-open"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card kpi-card-progress d-flex align-items-center justify-content-between">
            <div>
                <span class="text-info small fw-bold text-uppercase">In Progress</span>
                <h2 class="fw-bold mt-1 text-info mb-0"><?= intval($stats['in_progress_count'] ?? 0) ?></h2>
                <small class="text-muted">Technicians dispatched</small>
            </div>
            <div class="rounded-circle bg-info bg-opacity-10 p-3 text-info fs-3">
                <i class="fa-solid fa-wrench"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6">
        <div class="stat-card kpi-card-resolved d-flex align-items-center justify-content-between">
            <div>
                <span class="text-success small fw-bold text-uppercase">Resolved (Total)</span>
                <h2 class="fw-bold mt-1 text-success mb-0"><?= intval($stats['total_resolved_count'] ?? 0) ?></h2>
                <small class="text-muted"><?= intval($stats['resolved_today_count'] ?? 0) ?> closed today</small>
            </div>
            <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success fs-3">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form id="adminFilterForm" method="GET" action="<?= url('admin/index.php') ?>" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search ticket code, location, keyword..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="all">All Statuses</option>
                    <option value="Open" <?= ($filters['status'] === 'Open') ? 'selected' : '' ?>>Open</option>
                    <option value="In Progress" <?= ($filters['status'] === 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                    <option value="Resolved" <?= ($filters['status'] === 'Resolved') ? 'selected' : '' ?>>Resolved</option>
                    <option value="Closed" <?= ($filters['status'] === 'Closed') ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select form-select-sm">
                    <option value="all">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($filters['category'] == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="urgency" class="form-select form-select-sm">
                    <option value="all">All Urgencies</option>
                    <option value="Critical" <?= ($filters['urgency'] === 'Critical') ? 'selected' : '' ?>>Critical</option>
                    <option value="High" <?= ($filters['urgency'] === 'High') ? 'selected' : '' ?>>High</option>
                    <option value="Medium" <?= ($filters['urgency'] === 'Medium') ? 'selected' : '' ?>>Medium</option>
                    <option value="Low" <?= ($filters['urgency'] === 'Low') ? 'selected' : '' ?>>Low</option>
                </select>
            </div>
            <div class="col-md-1 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-nimbus w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Incidents Master Table -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="fa-solid fa-list-ul me-2 text-primary"></i>All Incident Reports</span>
        <span class="badge bg-light text-dark border"><?= count($tickets) ?> incidents found</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Issue & Location</th>
                    <th>Requester</th>
                    <th>Category</th>
                    <th>Urgency</th>
                    <th>Status</th>
                    <th>Assigned Tech</th>
                    <th>Created</th>
                    <th class="text-end">Manage</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-inbox fs-1 mb-2 d-block"></i>
                            No incidents matching the selected criteria.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td><span class="badge bg-dark fw-bold"><?= htmlspecialchars($t['ticket_code']) ?></span></td>
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
                                <?php if ($t['technician_name']): ?>
                                    <span class="small fw-semibold text-dark"><i class="fa-solid fa-user-check text-success me-1"></i><?= htmlspecialchars($t['technician_name']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-warning bg-opacity-25 text-dark border border-warning">Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= date('M d, H:i', strtotime($t['created_at'])) ?></small></td>
                            <td class="text-end">
                                <a href="<?= url('admin/index.php?page=manage&id=' . $t['id']) ?>" class="btn btn-sm btn-nimbus">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Manage
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
