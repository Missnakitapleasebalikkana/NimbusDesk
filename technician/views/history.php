<?php
// Provided variables: $tickets
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1">Resolved Technical Incidents</h3>
        <p class="text-muted small mb-0">Record of all issues resolved by the technical field team.</p>
    </div>
    <a href="<?= url('technician/index.php') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Active Queue
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="fw-bold text-dark"><i class="fa-solid fa-check-circle text-success me-2"></i>Completed Incident Log</div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Subject & Location</th>
                    <th>Requester</th>
                    <th>Category</th>
                    <th>Urgency</th>
                    <th>Resolved At</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No resolved incidents found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tickets as $t): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($t['ticket_code']) ?></span></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($t['subject']) ?></div>
                                <small class="text-muted"><i class="fa-solid fa-location-dot me-1 text-danger"></i><?= htmlspecialchars($t['location']) ?></small>
                            </td>
                            <td><?= htmlspecialchars($t['requester_name']) ?></td>
                            <td><?= htmlspecialchars($t['category_name']) ?></td>
                            <td><span class="badge badge-urgency-<?= strtolower($t['urgency']) ?>"><?= htmlspecialchars($t['urgency']) ?></span></td>
                            <td><small class="text-success fw-bold"><?= date('M d, Y', strtotime($t['created_at'])) ?></small></td>
                            <td class="text-end">
                                <a href="<?= url('technician/index.php?page=manage&id=' . $t['id']) ?>" class="btn btn-sm btn-outline-primary">
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
