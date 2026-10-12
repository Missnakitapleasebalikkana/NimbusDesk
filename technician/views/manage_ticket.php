<?php
// Provided variables: $ticket, $logs
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-dark fs-6"><?= htmlspecialchars($ticket['ticket_code']) ?></span>
            <h3 class="fw-bold text-dark mb-0"><?= htmlspecialchars($ticket['subject']) ?></h3>
        </div>
        <p class="text-muted small mb-0 mt-1">Reported by <?= htmlspecialchars($ticket['requester_name']) ?> (<?= htmlspecialchars($ticket['requester_dept']) ?>) &bull; <?= date('M d, Y h:i A', strtotime($ticket['created_at'])) ?></p>
    </div>
    <a href="<?= url('technician/index.php') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Queue
    </a>
</div>

<div class="row g-4">
    <!-- Left Column: Incident Details and Troubleshooting Updates -->
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-circle-exclamation text-danger me-2"></i>Incident Overview</span>
                <?php
                $statusClass = match($ticket['status']) {
                    'Open' => 'badge-status-open',
                    'In Progress' => 'badge-status-progress',
                    'Resolved' => 'badge-status-resolved',
                    default => 'badge-status-closed'
                };
                ?>
                <span class="badge <?= $statusClass ?> fs-6"><?= htmlspecialchars($ticket['status']) ?></span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-bold">Airport Location</small>
                        <div class="text-dark fw-bold"><i class="fa-solid fa-location-dot text-danger me-1"></i><?= htmlspecialchars($ticket['location']) ?></div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-bold">Urgency Severity</small>
                        <div>
                            <?php
                            $urgencyClass = match($ticket['urgency']) {
                                'Critical' => 'badge-urgency-critical',
                                'High' => 'badge-urgency-high',
                                'Medium' => 'badge-urgency-medium',
                                default => 'badge-urgency-low'
                            };
                            ?>
                            <span class="badge <?= $urgencyClass ?>"><?= htmlspecialchars($ticket['urgency']) ?></span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-bold">Equipment Category</small>
                        <div><i class="fa-solid <?= htmlspecialchars($ticket['category_icon']) ?> me-1"></i><?= htmlspecialchars($ticket['category_name']) ?></div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-bold">Requester Email</small>
                        <div><a href="mailto:<?= htmlspecialchars($ticket['requester_email']) ?>"><?= htmlspecialchars($ticket['requester_email']) ?></a></div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded border mb-3">
                    <h6 class="fw-bold text-dark mb-2">Issue Description:</h6>
                    <p class="mb-0 text-secondary" style="white-space: pre-wrap;"><?= htmlspecialchars($ticket['description']) ?></p>
                </div>

                <?php if (!empty($ticket['resolution_summary'])): ?>
                    <div class="p-3 bg-success bg-opacity-10 rounded border border-success">
                        <h6 class="fw-bold text-success mb-2"><i class="fa-solid fa-check me-1"></i>Current Resolution Summary:</h6>
                        <p class="mb-0 text-dark"><?= htmlspecialchars($ticket['resolution_summary']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Troubleshooting Log -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <span class="fw-bold"><i class="fa-solid fa-timeline text-secondary me-2"></i>Investigation & Activity Log</span>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php if (empty($logs)): ?>
                        <p class="text-muted text-center small mb-0">No updates logged yet.</p>
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

                <hr class="my-3">

                <!-- Add troubleshooting note form -->
                <form id="techNoteForm">
                    <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-dark"><i class="fa-solid fa-comment-dots text-primary me-1"></i>Add Diagnostic Note / Dispatch Update</label>
                        <textarea name="note" class="form-control form-control-sm" rows="3" placeholder="e.g., Arrived at Gate 4B; tested scanner USB power line; replacing damaged transceiver..." required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-plus me-1"></i> Append Update to Log
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Technician Resolution & Status Form -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <span class="fw-bold"><i class="fa-solid fa-sliders text-primary me-2"></i>Resolution & Status Control</span>
            </div>
            <div class="card-body">
                <form id="techStatusForm">
                    <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">

                    <div class="mb-3">
                        <label for="status" class="form-label fw-bold small">Incident Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="Open" <?= ($ticket['status'] === 'Open') ? 'selected' : '' ?>>Open</option>
                            <option value="In Progress" <?= ($ticket['status'] === 'In Progress') ? 'selected' : '' ?>>In Progress (Working)</option>
                            <option value="Resolved" <?= ($ticket['status'] === 'Resolved') ? 'selected' : '' ?>>Resolved (Issue Fixed)</option>
                            <option value="Closed" <?= ($ticket['status'] === 'Closed') ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="resolution_summary" class="form-label fw-bold small">Resolution Summary (Required if Resolved)</label>
                        <textarea name="resolution_summary" id="resolution_summary" rows="4" class="form-control" placeholder="Describe root cause and corrective action (e.g., replaced burnt thermal roller unit; printed test baggage label successfully)."><?= htmlspecialchars($ticket['resolution_summary'] ?? '') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label fw-bold small">Status Change Note</label>
                        <input type="text" name="note" id="note" class="form-control form-control-sm" placeholder="Optional audit memo">
                    </div>

                    <button type="submit" class="btn btn-nimbus w-100 py-2">
                        <i class="fa-solid fa-floppy-disk me-2"></i> Save Status & Resolution
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
