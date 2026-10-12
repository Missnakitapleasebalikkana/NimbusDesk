<?php
// Provided variables: $tickets
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">My Resolved Incident History</h3>
        <p class="text-muted small mb-0">Record of closed and resolved equipment tickets you previously reported.</p>
    </div>
    <a href="<?= url('employee/index.php') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Active Dashboard
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="fw-bold text-dark"><i class="fa-solid fa-clock-rotate-left text-success me-2"></i>Resolved IT Requests</div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Ticket Code</th>
                    <th>Subject</th>
                    <th>Category</th>
                    <th>Reported Date</th>
                    <th>Resolved Date</th>
                    <th>Assigned Tech</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $resolvedList = array_filter($tickets, fn($t) => $t['status'] === 'Resolved' || $t['status'] === 'Closed');
                ?>
                <?php if (empty($resolvedList)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-folder-open fs-1 mb-2 d-block text-secondary"></i>
                            No resolved history found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($resolvedList as $t): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($t['ticket_code']) ?></span></td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($t['subject']) ?></td>
                            <td>
                                <span class="badge bg-light text-secondary border">
                                    <i class="fa-solid <?= htmlspecialchars($t['category_icon']) ?> me-1"></i>
                                    <?= htmlspecialchars($t['category_name']) ?>
                                </span>
                            </td>
                            <td><small class="text-muted"><?= date('M d, Y', strtotime($t['created_at'])) ?></small></td>
                            <td><small class="text-success fw-semibold"><?= $t['resolved_at'] ? date('M d, Y', strtotime($t['resolved_at'])) : 'N/A' ?></small></td>
                            <td><small class="text-muted"><?= htmlspecialchars($t['technician_name'] ?? 'IT Team') ?></small></td>
                            <td class="text-end">
                                <a href="<?= url('employee/index.php?page=detail&id=' . $t['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-eye me-1"></i> View Log
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
