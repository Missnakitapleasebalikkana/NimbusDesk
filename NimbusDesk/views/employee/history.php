<div class="card p-3 shadow-sm border-0 mb-4 bg-white">
    <form action="/NimbusDesk/employee/history.php" method="GET" class="row g-2 align-items-center">
        <div class="col-md-6 col-lg-5">
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Search ticket code, issue, or location...">
            </div>
        </div>

        <div class="col-md-4 col-lg-3">
            <select class="form-select" name="category">
                <option value="">All Categories</option>
                <option value="Hardware" <?= ($selectedCategory ?? '') === 'Hardware' ? 'selected' : '' ?>>Hardware</option>
                <option value="Software" <?= ($selectedCategory ?? '') === 'Software' ? 'selected' : '' ?>>Software</option>
                <option value="Network" <?= ($selectedCategory ?? '') === 'Network' ? 'selected' : '' ?>>Network</option>
                <option value="Facilities" <?= ($selectedCategory ?? '') === 'Facilities' ? 'selected' : '' ?>>Facilities</option>
                <option value="Other" <?= ($selectedCategory ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-3">Filter</button>
            <?php if (!empty($search) || !empty($selectedCategory)): ?>
                <a href="/NimbusDesk/employee/history.php" class="btn btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </div>
    </form>
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
                    <th>Resolved On</th>
                    <th>Handled By</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($resolvedTickets)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-clock-rotate-left fs-2 mb-2"></i>
                            <h6 class="fw-bold mt-2">No resolved incidents found</h6>
                            <p class="small mb-0">Tickets marked as resolved by IT support will be archived here.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($resolvedTickets as $t): ?>
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
                            <td><span class="badge ticket-status-resolved"><?= htmlspecialchars($t['status']) ?></span></td>
                            <td class="small text-muted text-nowrap">
                                <?= !empty($t['resolved_at']) ? date('M d, Y h:i A', strtotime($t['resolved_at'])) : date('M d, Y', strtotime($t['updated_at'])) ?>
                            </td>
                            <td>
                                <span class="small fw-semibold text-secondary">
                                    <?= !empty($t['technician_name']) ? htmlspecialchars($t['technician_name']) : 'IT Support Team' ?>
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <a href="/NimbusDesk/employee/view_ticket.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary">
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
