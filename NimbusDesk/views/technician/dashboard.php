<!-- Technician KPI Counters -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 5px solid #0dcaf0 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">My Active Incidents</h6>
                    <h2 class="fw-bold mb-0 text-info"><?= (int)($techStats['my_active'] ?? 0) ?></h2>
                </div>
                <div class="p-3 rounded-circle bg-info bg-opacity-10 text-info fs-3">
                    <i class="fa-solid fa-list-check"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 5px solid #ffc107 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">In Troubleshooting</h6>
                    <h2 class="fw-bold mb-0 text-dark"><?= (int)($techStats['my_in_progress'] ?? 0) ?></h2>
                </div>
                <div class="p-3 rounded-circle bg-warning bg-opacity-10 text-warning fs-3">
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
                    <h2 class="fw-bold mb-0 text-success"><?= (int)($techStats['my_resolved_today'] ?? 0) ?></h2>
                </div>
                <div class="p-3 rounded-circle bg-success bg-opacity-10 text-success fs-3">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card p-3 shadow-sm border-0 bg-white" style="border-left: 5px solid var(--nimbus-maroon) !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted text-uppercase mb-1 small fw-bold">Total Lifetime Fixed</h6>
                    <h2 class="fw-bold mb-0" style="color: var(--nimbus-maroon);"><?= (int)($techStats['my_total_resolved'] ?? 0) ?></h2>
                </div>
                <div class="p-3 rounded-circle bg-danger bg-opacity-10 text-danger fs-3">
                    <i class="fa-solid fa-trophy"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 1: My Assigned Tasks -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0" style="color: var(--nimbus-maroon);">
            <i class="fa-solid fa-clipboard-user me-2"></i>My Currently Assigned Incidents
        </h5>
        <span class="badge bg-primary"><?= count($assignedTickets) ?> Active</span>
    </div>
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
                    <th>Reported</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($assignedTickets)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-mug-hot fs-3 mb-2 text-warning"></i>
                            <h6 class="fw-bold">No active incidents currently assigned to you!</h6>
                            <p class="small mb-0">Check the unassigned airport pool below to claim pending requests.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($assignedTickets as $t): ?>
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
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($t['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted text-nowrap"><?= date('M d, h:i A', strtotime($t['created_at'])) ?></td>
                            <td class="text-end pe-3">
                                <a href="/NimbusDesk/admin/ticket.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fa-solid fa-wrench me-1"></i> Troubleshoot
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Section 2: Unassigned Tickets Queue (Claimable) -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-warning">
            <i class="fa-solid fa-inbox me-2"></i>Unassigned Airport Incident Pool (Pending Dispatch)
        </h5>
        <span class="badge bg-warning text-dark"><?= count($unassignedTickets) ?> Open</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Code</th>
                    <th>Incident Subject & Location</th>
                    <th>Requester</th>
                    <th>Category</th>
                    <th>Urgency</th>
                    <th>Reported</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($unassignedTickets)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-circle-check text-success fs-3 mb-2"></i>
                            <h6 class="fw-bold">No unassigned tickets in queue!</h6>
                            <p class="small mb-0">All incoming airport support requests are currently assigned.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($unassignedTickets as $t): ?>
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
                            <td class="small text-muted text-nowrap"><?= date('M d, h:i A', strtotime($t['created_at'])) ?></td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex gap-1">
                                    <form action="/NimbusDesk/technician/index.php" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= \NimbusDesk\Core\Auth::csrfToken() ?>">
                                        <input type="hidden" name="action" value="claim_ticket">
                                        <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-success fw-semibold" title="Claim incident to your workbench">
                                            <i class="fa-solid fa-hand-holding-hand me-1"></i> Claim
                                        </button>
                                    </form>
                                    <a href="/NimbusDesk/admin/ticket.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
