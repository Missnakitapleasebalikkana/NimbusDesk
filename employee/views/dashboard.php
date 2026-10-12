<?php
// Provided variables: $tickets, $stats, $statusFilter
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Aviation Staff IT Desk</h3>
        <p class="text-muted small mb-0">Report hardware, software, or network failures impacting flight and gate operations.</p>
    </div>
    <a href="<?= url('employee/index.php?page=create') ?>" class="btn btn-nimbus shadow-sm">
        <i class="fa-solid fa-paper-plane me-2"></i> Report Technical Issue
    </a>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold text-uppercase">Active Incidents</span>
                <h2 class="fw-bold mt-1 text-primary mb-0"><?= intval($stats['active_count'] ?? 0) ?></h2>
            </div>
            <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary fs-3">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold text-uppercase">Resolved Requests</span>
                <h2 class="fw-bold mt-1 text-success mb-0"><?= intval($stats['resolved_count'] ?? 0) ?></h2>
            </div>
            <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success fs-3">
                <i class="fa-solid fa-circle-check"></i>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small fw-bold text-uppercase">Total Submissions</span>
                <h2 class="fw-bold mt-1 text-dark mb-0"><?= intval($stats['total_count'] ?? 0) ?></h2>
            </div>
            <div class="rounded-circle bg-secondary bg-opacity-10 p-3 text-secondary fs-3">
                <i class="fa-solid fa-list-check"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tickets List Table -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-ticket text-muted"></i>
            <span class="fw-bold">My Reported Tickets</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <label for="employeeStatusFilter" class="small text-muted fw-bold mb-0">Status:</label>
            <select id="employeeStatusFilter" class="form-select form-select-sm" style="width: 160px;">
                <option value="all" <?= ($statusFilter === 'all') ? 'selected' : '' ?>>All Statuses</option>
                <option value="Open" <?= ($statusFilter === 'Open') ? 'selected' : '' ?>>Open</option>
                <option value="In Progress" <?= ($statusFilter === 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                <option value="Resolved" <?= ($statusFilter === 'Resolved') ? 'selected' : '' ?>>Resolved</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Ticket Code</th>
                    <th>Issue Subject & Location</th>
                    <th>Category</th>
                    <th>Urgency</th>
                    <th>Status</th>
                    <th>Assigned Tech</th>
                    <th>Reported On</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-circle-check fs-1 text-success mb-3 d-block"></i>
                            <div class="fw-bold">No tickets found for this filter.</div>
                            <small>Need technical help? Submit a new incident report above.</small>
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
                                    <?= $t['technician_name'] ? htmlspecialchars($t['technician_name']) : '<span class="text-muted fst-italic">Awaiting IT Tech</span>' ?>
                                </small>
                            </td>
                            <td>
                                <small class="text-muted"><?= date('M d, Y h:i A', strtotime($t['created_at'])) ?></small>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('employee/index.php?page=detail&id=' . $t['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye me-1"></i> Details
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
