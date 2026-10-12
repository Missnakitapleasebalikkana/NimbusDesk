<?php
// Provided variables: $history, $categories, $categoryFilter, $search
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Aviation IT Incident History Archive</h3>
        <p class="text-muted small mb-0">Historical logs of resolved hardware, network, and software incidents for root cause audits.</p>
    </div>
    <a href="<?= url('admin/index.php') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Live Dashboard
    </a>
</div>

<!-- History Filter Bar -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-3">
        <form method="GET" action="<?= url('admin/index.php') ?>" class="row g-2 align-items-center">
            <input type="hidden" name="page" value="history">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Search by ticket code, location, or equipment keywords..." value="<?= htmlspecialchars($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select form-select-sm">
                    <option value="all">All Incident Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($categoryFilter == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-nimbus w-100">Search Logs</button>
            </div>
        </form>
    </div>
</div>

<!-- Archive Table -->
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold"><i class="fa-solid fa-database me-2 text-success"></i>Archived Resolution Records</span>
        <span class="badge bg-light text-dark border"><?= count($history) ?> records archived</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Failure / Location</th>
                    <th>Category</th>
                    <th>Reported By</th>
                    <th>Resolved By</th>
                    <th>Downtime</th>
                    <th>Resolution Summary</th>
                    <th class="text-end">Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-folder-closed fs-1 mb-2 d-block text-secondary"></i>
                            No historical records found for this query.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($history as $h): ?>
                        <tr>
                            <td><span class="badge bg-secondary fw-bold"><?= htmlspecialchars($h['ticket_code']) ?></span></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($h['subject']) ?></div>
                                <small class="text-muted"><i class="fa-solid fa-location-dot me-1 text-danger"></i><?= htmlspecialchars($h['location']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($h['category_name']) ?></td>
                            <td>
                                <small class="fw-bold text-dark d-block"><?= htmlspecialchars($h['requester_name']) ?></small>
                                <small class="text-muted"><?= htmlspecialchars($h['requester_dept']) ?></small>
                            </td>
                            <td>
                                <small class="fw-bold text-success d-block"><?= htmlspecialchars($h['resolved_by_name'] ?? 'IT Team') ?></small>
                                <small class="text-muted"><?= date('M d, Y', strtotime($h['resolved_at'])) ?></small>
                            </td>
                            <td>
                                <?php
                                $mins = intval($h['duration_minutes'] ?? 0);
                                if ($mins < 60) {
                                    $timeStr = "{$mins}m";
                                } else {
                                    $hours = floor($mins / 60);
                                    $rem = $mins % 60;
                                    $timeStr = "{$hours}h {$rem}m";
                                }
                                ?>
                                <span class="badge bg-light text-dark border"><?= $timeStr ?></span>
                            </td>
                            <td style="max-width: 250px;">
                                <div class="small text-truncate" title="<?= htmlspecialchars($h['resolution_summary'] ?? 'Resolved') ?>">
                                    <?= htmlspecialchars($h['resolution_summary'] ?? 'Standard maintenance completed') ?>
                                </div>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('admin/index.php?page=manage&id=' . $h['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye me-1"></i> Audit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
