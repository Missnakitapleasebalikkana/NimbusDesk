<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 5px solid #ffc107 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Active Incidents</h6>
                    <h2 class="fw-bold mb-0 text-dark"><?= (int)($stats['active'] ?? 0) ?></h2>
                </div>
                <div class="p-3 rounded-circle bg-warning bg-opacity-10 text-warning fs-3">
                    <i class="fa-solid fa-spinner"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 5px solid #0d6efd !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Open (Pending IT)</h6>
                    <h2 class="fw-bold mb-0 text-dark"><?= (int)($stats['open'] ?? 0) ?></h2>
                </div>
                <div class="p-3 rounded-circle bg-primary bg-opacity-10 text-primary fs-3">
                    <i class="fa-solid fa-envelope-open-text"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 5px solid #198754 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Resolved Incidents</h6>
                    <h2 class="fw-bold mb-0 text-dark"><?= (int)($stats['resolved'] ?? 0) ?></h2>
                </div>
                <div class="p-3 rounded-circle bg-success bg-opacity-10 text-success fs-3">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0" style="color: var(--nimbus-maroon);">
        <i class="fa-solid fa-list-check me-2"></i>My Active Support Tickets
    </h5>
    <a href="/NimbusDesk/employee/create_ticket.php" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-plus me-1"></i> Report New Incident
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Ticket ID</th>
                    <th>Subject & Location</th>
                    <th>Category</th>
                    <th>Urgency</th>
                    <th>Status</th>
                    <th>Reported On</th>
                    <th>Technician</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($activeTickets)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fa-solid fa-champagne-glasses text-muted mb-2" style="font-size: 2.5rem;"></i>
                            <h6 class="fw-bold mt-2">All systems operational!</h6>
                            <p class="text-muted small mb-3">You currently have no active or pending IT incident reports.</p>
                            <a href="/NimbusDesk/employee/create_ticket.php" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-plus me-1"></i> Report an Issue
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($activeTickets as $t): ?>
                        <tr>
                            <td class="ps-3 fw-bold text-nowrap">
                                <a href="/NimbusDesk/employee/view_ticket.php?id=<?= $t['id'] ?>" class="text-decoration-none" style="color: var(--nimbus-maroon);">
                                    <?= htmlspecialchars($t['ticket_code']) ?>
                                </a>
                            </td>
                            <td>
                                <a href="/NimbusDesk/employee/view_ticket.php?id=<?= $t['id'] ?>" class="text-decoration-none text-dark fw-semibold d-block">
                                    <?= htmlspecialchars($t['subject']) ?>
                                </a>
                                <small class="text-muted">
                                    <i class="fa-solid fa-location-dot me-1 text-danger"></i><?= htmlspecialchars($t['terminal']) ?> <?= !empty($t['location']) ? '• ' . htmlspecialchars($t['location']) : '' ?>
                                </small>
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
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($t['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted text-nowrap"><?= date('M d, Y h:i A', strtotime($t['created_at'])) ?></td>
                            <td>
                                <?php if (!empty($t['technician_name'])): ?>
                                    <span class="small fw-semibold text-success"><i class="fa-solid fa-user-check me-1"></i><?= htmlspecialchars($t['technician_name']) ?></span>
                                <?php else: ?>
                                    <span class="small text-muted fst-italic">Pending Dispatch</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3">
                                <a href="/NimbusDesk/employee/view_ticket.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
