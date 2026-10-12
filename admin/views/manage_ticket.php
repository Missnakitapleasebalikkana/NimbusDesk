<?php
// Provided variables: $ticket, $logs, $technicians
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-dark fs-6"><?= htmlspecialchars($ticket['ticket_code']) ?></span>
            <h3 class="fw-bold text-dark mb-0"><?= htmlspecialchars($ticket['subject']) ?></h3>
        </div>
        <p class="text-muted small mb-0 mt-1">Submitted by <?= htmlspecialchars($ticket['requester_name']) ?> (Badge: <?= htmlspecialchars($ticket['requester_badge']) ?>) &bull; <?= date('M d, Y h:i A', strtotime($ticket['created_at'])) ?></p>
    </div>
    <a href="<?= url('admin/index.php') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to All Incidents
    </a>
</div>

<div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-plane-circle-exclamation text-primary me-2"></i>Flight Operation Incident Details</span>
                <span class="badge badge-status-<?= strtolower(str_replace(' ', '', $ticket['status'])) ?> fs-6"><?= htmlspecialchars($ticket['status']) ?></span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-bold">Location</small>
                        <div class="text-dark fw-bold"><i class="fa-solid fa-location-dot text-danger me-1"></i><?= htmlspecialchars($ticket['location']) ?></div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-bold">Category</small>
                        <div><i class="fa-solid <?= htmlspecialchars($ticket['category_icon']) ?> me-1"></i><?= htmlspecialchars($ticket['category_name']) ?></div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-bold">Requester Dept</small>
                        <div><?= htmlspecialchars($ticket['requester_dept']) ?> (<?= htmlspecialchars($ticket['requester_email']) ?>)</div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-bold">Urgency Level</small>
                        <div><span class="badge badge-urgency-<?= strtolower($ticket['urgency']) ?>"><?= htmlspecialchars($ticket['urgency']) ?></span></div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded border mb-3">
                    <h6 class="fw-bold text-dark mb-2">Requester Problem Statement:</h6>
                    <p class="mb-0 text-secondary" style="white-space: pre-wrap;"><?= htmlspecialchars($ticket['description']) ?></p>
                </div>

                <?php if (!empty($ticket['resolution_summary'])): ?>
                    <div class="p-3 bg-success bg-opacity-10 rounded border border-success">
                        <h6 class="fw-bold text-success mb-2"><i class="fa-solid fa-circle-check me-1"></i>Documented Resolution:</h6>
                        <p class="mb-0 text-dark"><?= htmlspecialchars($ticket['resolution_summary']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Activity Audit Log -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <span class="fw-bold"><i class="fa-solid fa-clock-rotate-left text-secondary me-2"></i>Chain of Custody & Activity Log</span>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php if (empty($logs)): ?>
                        <p class="text-muted text-center small mb-0">No activity logged.</p>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <div class="timeline-event">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small"><?= htmlspecialchars($log['actor_name']) ?> <span class="badge bg-light text-muted border"><?= htmlspecialchars($log['actor_role']) ?></span></span>
                                    <small class="text-muted"><?= date('M d, Y h:i A', strtotime($log['created_at'])) ?></small>
                                </div>
                                <div class="small text-secondary"><?= htmlspecialchars($log['notes']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Supervisor Control & Assignment Form -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <span class="fw-bold"><i class="fa-solid fa-user-gear text-primary me-2"></i>IT Supervisor Actions</span>
            </div>
            <div class="card-body">
                <form id="adminUpdateForm">
                    <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">

                    <div class="mb-3">
                        <label for="technician_id" class="form-label fw-bold small">Assign Technician</label>
                        <select name="technician_id" id="technician_id" class="form-select">
                            <option value="0">-- Unassigned --</option>
                            <?php foreach ($technicians as $tech): ?>
                                <option value="<?= $tech['id'] ?>" <?= ($ticket['assigned_to'] == $tech['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tech['name']) ?> (<?= htmlspecialchars($tech['department']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold small">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="Open" <?= ($ticket['status'] === 'Open') ? 'selected' : '' ?>>Open</option>
                            <option value="In Progress" <?= ($ticket['status'] === 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                            <option value="Resolved" <?= ($ticket['status'] === 'Resolved') ? 'selected' : '' ?>>Resolved</option>
                            <option value="Closed" <?= ($ticket['status'] === 'Closed') ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="urgency" class="form-label fw-bold small">Urgency Override</label>
                        <select name="urgency" id="urgency" class="form-select">
                            <option value="Low" <?= ($ticket['urgency'] === 'Low') ? 'selected' : '' ?>>Low</option>
                            <option value="Medium" <?= ($ticket['urgency'] === 'Medium') ? 'selected' : '' ?>>Medium</option>
                            <option value="High" <?= ($ticket['urgency'] === 'High') ? 'selected' : '' ?>>High</option>
                            <option value="Critical" <?= ($ticket['urgency'] === 'Critical') ? 'selected' : '' ?>>Critical</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="resolution_note" class="form-label fw-bold small">Resolution Summary / Supervisor Memo</label>
                        <textarea name="resolution_note" id="resolution_note" rows="3" class="form-control" placeholder="Optional notes or permanent resolution log..."><?= htmlspecialchars($ticket['resolution_summary'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-nimbus w-100 py-2">
                        <i class="fa-solid fa-check-double me-2"></i> Update Ticket & Dispatch
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
