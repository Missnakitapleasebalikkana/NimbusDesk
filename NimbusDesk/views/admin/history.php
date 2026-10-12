<div class="card p-3 shadow-sm border-0 mb-4 bg-white">
    <form action="/NimbusDesk/admin/history.php" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5 col-lg-4">
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Search ID, subject, station, staff...">
            </div>
        </div>

        <div class="col-md-3 col-lg-3">
            <select class="form-select" name="category">
                <option value="">All Categories</option>
                <option value="Hardware" <?= ($filters['category'] ?? '') === 'Hardware' ? 'selected' : '' ?>>Hardware</option>
                <option value="Software" <?= ($filters['category'] ?? '') === 'Software' ? 'selected' : '' ?>>Software</option>
                <option value="Network" <?= ($filters['category'] ?? '') === 'Network' ? 'selected' : '' ?>>Network</option>
                <option value="Facilities" <?= ($filters['category'] ?? '') === 'Facilities' ? 'selected' : '' ?>>Facilities</option>
                <option value="Other" <?= ($filters['category'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="col-md-2 col-lg-2">
            <select class="form-select" name="terminal">
                <option value="">All Terminals</option>
                <option value="Terminal 1" <?= ($filters['terminal'] ?? '') === 'Terminal 1' ? 'selected' : '' ?>>Terminal 1</option>
                <option value="Terminal 2" <?= ($filters['terminal'] ?? '') === 'Terminal 2' ? 'selected' : '' ?>>Terminal 2</option>
                <option value="Terminal 3" <?= ($filters['terminal'] ?? '') === 'Terminal 3' ? 'selected' : '' ?>>Terminal 3</option>
                <option value="Domestic Cargo" <?= ($filters['terminal'] ?? '') === 'Domestic Cargo' ? 'selected' : '' ?>>Domestic Cargo</option>
                <option value="Apron / Ramp" <?= ($filters['terminal'] ?? '') === 'Apron / Ramp' ? 'selected' : '' ?>>Apron / Ramp</option>
            </select>
        </div>

        <div class="col-md-2 col-lg-2">
            <select class="form-select" name="status">
                <option value="Resolved" <?= ($filters['status'] ?? '') === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                <option value="Closed" <?= ($filters['status'] ?? '') === 'Closed' ? 'selected' : '' ?>>Closed</option>
                <option value="" <?= ($filters['status'] ?? '') === '' ? 'selected' : '' ?>>All Statuses</option>
            </select>
        </div>

        <div class="col-md-12 col-lg-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-3">Filter</button>
            <a href="/NimbusDesk/admin/history.php" class="btn btn-outline-secondary">Reset</a>
            <a href="/NimbusDesk/admin/export_csv.php?status=<?= urlencode($filters['status']) ?>&category=<?= urlencode($filters['category']) ?>&terminal=<?= urlencode($filters['terminal'] ?? '') ?>&search=<?= urlencode($filters['search']) ?>" class="btn btn-outline-success ms-auto" title="Download spreadsheet of current audit records">
                <i class="fa-solid fa-file-csv me-1"></i> Export CSV
            </a>
        </div>
    </form>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Code</th>
                    <th>Incident Subject & Location</th>
                    <th>Reporter</th>
                    <th>Category</th>
                    <th>Urgency</th>
                    <th>Status</th>
                    <th>Resolved Date</th>
                    <th>Technician</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historyTickets)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-archive fs-2 mb-2"></i>
                            <h6 class="fw-bold mt-2">No historical incidents found</h6>
                            <p class="small mb-0">No past tickets match your filter parameters.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($historyTickets as $t): ?>
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
                                <small class="text-muted"><?= htmlspecialchars($t['requester_dept'] ?? '') ?></small>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($t['category']) ?></span></td>
                            <td>
                                <span class="badge 
                                    <?= $t['urgency'] === 'Critical' ? 'bg-danger' : ($t['urgency'] === 'High' ? 'bg-warning text-dark' : 'bg-info text-dark') ?>">
                                    <?= htmlspecialchars($t['urgency']) ?>
                                </span>
                            </td>
                            <td><span class="badge ticket-status-resolved"><?= htmlspecialchars($t['status']) ?></span></td>
                            <td class="small text-muted">
                                <?= !empty($t['resolved_at']) ? date('M d, Y h:i A', strtotime($t['resolved_at'])) : date('M d, Y', strtotime($t['updated_at'])) ?>
                            </td>
                            <td class="small">
                                <?= !empty($t['technician_name']) ? htmlspecialchars($t['technician_name']) : '<span class="text-muted fst-italic">IT Team</span>' ?>
                            </td>
                            <td class="text-end pe-3">
                                <a href="/NimbusDesk/admin/ticket.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye me-1"></i> Review
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
