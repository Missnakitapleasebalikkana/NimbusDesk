<!-- KPI Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 5px solid #dc3545 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Critical Incidents</h6>
                    <h2 class="fw-bold mb-0 text-danger"><?= (int)($stats['critical'] ?? 0) ?></h2>
                </div>
                <div class="p-3 rounded-circle bg-danger bg-opacity-10 text-danger fs-3">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 5px solid #ffc107 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Open Tickets</h6>
                    <h2 class="fw-bold mb-0 text-dark"><?= (int)($stats['open'] ?? 0) ?></h2>
                </div>
                <div class="p-3 rounded-circle bg-warning bg-opacity-10 text-warning fs-3">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 5px solid #0dcaf0 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">In Progress</h6>
                    <h2 class="fw-bold mb-0 text-dark"><?= (int)($stats['in_progress'] ?? 0) ?></h2>
                </div>
                <div class="p-3 rounded-circle bg-info bg-opacity-10 text-info fs-3">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 5px solid #198754 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Resolved Today</h6>
                    <h2 class="fw-bold mb-0 text-success"><?= (int)($stats['resolved_today'] ?? 0) ?></h2>
                </div>
                <div class="p-3 rounded-circle bg-success bg-opacity-10 text-success fs-3">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Toolbar -->
<div class="card p-3 shadow-sm border-0 mb-4 bg-white">
    <form action="/NimbusDesk/admin/index.php" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4 col-lg-3">
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Search ticket, station, user...">
            </div>
        </div>

        <div class="col-md-3 col-lg-2">
            <select class="form-select" name="status">
                <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active (Open & Progress)</option>
                <option value="" <?= ($filters['status'] ?? '') === '' ? 'selected' : '' ?>>All Statuses</option>
                <option value="Open" <?= ($filters['status'] ?? '') === 'Open' ? 'selected' : '' ?>>Open Only</option>
                <option value="In Progress" <?= ($filters['status'] ?? '') === 'In Progress' ? 'selected' : '' ?>>In Progress Only</option>
                <option value="Resolved" <?= ($filters['status'] ?? '') === 'Resolved' ? 'selected' : '' ?>>Resolved Only</option>
                <option value="Closed" <?= ($filters['status'] ?? '') === 'Closed' ? 'selected' : '' ?>>Closed Only</option>
            </select>
        </div>

        <div class="col-md-2 col-lg-2">
            <select class="form-select" name="urgency">
                <option value="">All Urgency</option>
                <option value="Critical" <?= ($filters['urgency'] ?? '') === 'Critical' ? 'selected' : '' ?>>Critical</option>
                <option value="High" <?= ($filters['urgency'] ?? '') === 'High' ? 'selected' : '' ?>>High</option>
                <option value="Medium" <?= ($filters['urgency'] ?? '') === 'Medium' ? 'selected' : '' ?>>Medium</option>
                <option value="Low" <?= ($filters['urgency'] ?? '') === 'Low' ? 'selected' : '' ?>>Low</option>
            </select>
        </div>

        <div class="col-md-3 col-lg-2">
            <select class="form-select" name="category">
                <option value="">All Categories</option>
                <option value="Hardware" <?= ($filters['category'] ?? '') === 'Hardware' ? 'selected' : '' ?>>Hardware</option>
                <option value="Software" <?= ($filters['category'] ?? '') === 'Software' ? 'selected' : '' ?>>Software</option>
                <option value="Network" <?= ($filters['category'] ?? '') === 'Network' ? 'selected' : '' ?>>Network</option>
                <option value="Facilities" <?= ($filters['category'] ?? '') === 'Facilities' ? 'selected' : '' ?>>Facilities</option>
                <option value="Other" <?= ($filters['category'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="col-md-3 col-lg-2">
            <select class="form-select" name="terminal">
                <option value="">All Terminals</option>
                <option value="Terminal 1" <?= ($filters['terminal'] ?? '') === 'Terminal 1' ? 'selected' : '' ?>>Terminal 1</option>
                <option value="Terminal 2" <?= ($filters['terminal'] ?? '') === 'Terminal 2' ? 'selected' : '' ?>>Terminal 2</option>
                <option value="Terminal 3" <?= ($filters['terminal'] ?? '') === 'Terminal 3' ? 'selected' : '' ?>>Terminal 3</option>
                <option value="Domestic Cargo" <?= ($filters['terminal'] ?? '') === 'Domestic Cargo' ? 'selected' : '' ?>>Domestic Cargo</option>
                <option value="Apron / Ramp" <?= ($filters['terminal'] ?? '') === 'Apron / Ramp' ? 'selected' : '' ?>>Apron / Ramp</option>
            </select>
        </div>

        <div class="col-md-12 col-lg-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-3">Filter</button>
            <a href="/NimbusDesk/admin/index.php" class="btn btn-outline-secondary">Reset</a>
            <a href="/NimbusDesk/admin/export_csv.php?status=<?= urlencode($filters['status']) ?>&urgency=<?= urlencode($filters['urgency']) ?>&category=<?= urlencode($filters['category']) ?>&terminal=<?= urlencode($filters['terminal'] ?? '') ?>&search=<?= urlencode($filters['search']) ?>" class="btn btn-outline-success ms-auto" title="Export current view to CSV">
                <i class="fa-solid fa-file-csv me-1"></i> CSV
            </a>
        </div>
    </form>
</div>

<!-- Incident Queue Table -->
<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Code</th>
                    <th>Incident Subject & Location</th>
                    <th>Requester</th>
                    <th>Category</th>
                    <th>Urgency</th>
                    <th>Status</th>
                    <th>Assigned Tech</th>
                    <th>Reported</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-clipboard-check fs-2 mb-2"></i>
                            <h6 class="fw-bold mt-2">No tickets found</h6>
                            <p class="small mb-0">No active airport incident reports match the current filters.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-nowrap">
                                <a href="/NimbusDesk/admin/ticket.php?id=<?= $t['id'] ?>" class="text-decoration-none" style="color: var(--nimbus-maroon);">
                                    <?= htmlspecialchars($t['ticket_code']) ?>
                                </a>
                            </td>
                            <td>
                                <a href="/NimbusDesk/admin/ticket.php?id=<?= $t['id'] ?>" class="text-decoration-none text-dark fw-semibold d-block">
                                    <?= htmlspecialchars($t['subject']) ?>
                                </a>
                                <small class="text-muted">
                                    <i class="fa-solid fa-location-dot me-1 text-danger"></i><?= htmlspecialchars($t['terminal']) ?> <?= !empty($t['location']) ? '• ' . htmlspecialchars($t['location']) : '' ?>
                                </small>
                            </td>
                            <td>
                                <div class="fw-semibold small"><?= htmlspecialchars($t['requester_name']) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($t['requester_dept'] ?? 'Staff') ?></small>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($t['category']) ?></span></td>
                            <td>
                                <span class="badge 
                                    <?= $t['urgency'] === 'Critical' ? 'bg-danger' : ($t['urgency'] === 'High' ? 'bg-warning text-dark' : 'bg-info text-dark') ?>">
                                    <?= htmlspecialchars($t['urgency']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($t['status'] === 'Open'): ?>
                                    <span class="badge ticket-status-open">Open</span>
                                <?php elseif ($t['status'] === 'In Progress'): ?>
                                    <span class="badge ticket-status-progress">In Progress</span>
                                <?php elseif ($t['status'] === 'Resolved'): ?>
                                    <span class="badge ticket-status-resolved">Resolved</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($t['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($t['technician_name'])): ?>
                                    <span class="small fw-semibold text-dark"><?= htmlspecialchars($t['technician_name']) ?></span>
                                <?php else: ?>
                                    <span class="small text-danger fst-italic"><i class="fa-solid fa-triangle-exclamation me-1"></i>Unassigned</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted text-nowrap"><?= date('M d, h:i A', strtotime($t['created_at'])) ?></td>
                            <td class="text-end pe-3">
                                <a href="/NimbusDesk/admin/ticket.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-wrench me-1"></i> Manage
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
