<div class="card p-3 shadow-sm border-0 mb-4 bg-white">
    <form action="/NimbusDesk/technician/history.php" method="GET" class="row g-2 align-items-center">
        <div class="col-md-6 col-lg-5">
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Search ticket code, station, or issue...">
            </div>
        </div>

        <div class="col-md-3 col-lg-3">
            <select class="form-select" name="terminal">
                <option value="">All Terminals</option>
                <option value="Terminal 1" <?= ($terminal ?? '') === 'Terminal 1' ? 'selected' : '' ?>>Terminal 1</option>
                <option value="Terminal 2" <?= ($terminal ?? '') === 'Terminal 2' ? 'selected' : '' ?>>Terminal 2</option>
                <option value="Terminal 3" <?= ($terminal ?? '') === 'Terminal 3' ? 'selected' : '' ?>>Terminal 3</option>
                <option value="Domestic Cargo" <?= ($terminal ?? '') === 'Domestic Cargo' ? 'selected' : '' ?>>Domestic Cargo</option>
                <option value="Apron / Ramp" <?= ($terminal ?? '') === 'Apron / Ramp' ? 'selected' : '' ?>>Apron / Ramp</option>
            </select>
        </div>

        <div class="col-md-3 col-lg-2">
            <select class="form-select" name="category">
                <option value="">All Categories</option>
                <option value="Hardware" <?= ($category ?? '') === 'Hardware' ? 'selected' : '' ?>>Hardware</option>
                <option value="Software" <?= ($category ?? '') === 'Software' ? 'selected' : '' ?>>Software</option>
                <option value="Network" <?= ($category ?? '') === 'Network' ? 'selected' : '' ?>>Network</option>
                <option value="Facilities" <?= ($category ?? '') === 'Facilities' ? 'selected' : '' ?>>Facilities</option>
                <option value="Other" <?= ($category ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="col-md-12 col-lg-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary px-3">Filter</button>
            <?php if (!empty($search) || !empty($category) || !empty($terminal)): ?>
                <a href="/NimbusDesk/technician/history.php" class="btn btn-outline-secondary">Reset</a>
            <?php endif; ?>
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
                    <th>Requester</th>
                    <th>Category</th>
                    <th>Urgency</th>
                    <th>Status</th>
                    <th>Resolved Timestamp</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historyTickets)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-box-archive fs-2 mb-2"></i>
                            <h6 class="fw-bold mt-2">No resolved incidents found</h6>
                            <p class="small mb-0">Incidents you mark as Resolved will appear here for audit reference.</p>
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
