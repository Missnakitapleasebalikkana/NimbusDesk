<?php
// Provided variables: $ticket, $logs
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-dark fs-6"><?= htmlspecialchars($ticket['ticket_code']) ?></span>
            <h3 class="fw-bold text-dark mb-0"><?= htmlspecialchars($ticket['subject']) ?></h3>
        </div>
        <p class="text-muted small mb-0 mt-1">Submitted on <?= date('F d, Y \a\t h:i A', strtotime($ticket['created_at'])) ?></p>
    </div>
    <a href="<?= url('employee/index.php') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Tickets
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Main Details Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-circle-info text-primary me-2"></i>Incident Overview</span>
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
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-bold">Airport Location</small>
                        <div class="text-dark fw-semibold"><i class="fa-solid fa-location-dot text-danger me-1"></i><?= htmlspecialchars($ticket['location']) ?></div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-bold">Equipment Category</small>
                        <div class="text-dark fw-semibold"><i class="fa-solid <?= htmlspecialchars($ticket['category_icon']) ?> text-muted me-1"></i><?= htmlspecialchars($ticket['category_name']) ?></div>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block fw-bold">Urgency</small>
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
                        <small class="text-muted d-block fw-bold">Assigned Technician</small>
                        <div class="text-dark fw-semibold">
                            <?= $ticket['technician_name'] ? htmlspecialchars($ticket['technician_name']) : '<span class="text-warning fst-italic">Pending Assignment</span>' ?>
                        </div>
                    </div>
                </div>

                <div class="p-3 bg-light rounded border mb-3">
                    <h6 class="fw-bold text-dark mb-2">Detailed Issue Description:</h6>
                    <p class="mb-0 text-secondary" style="white-space: pre-wrap;"><?= htmlspecialchars($ticket['description']) ?></p>
                </div>

                <?php if (!empty($ticket['resolution_summary'])): ?>
                    <div class="p-3 bg-success bg-opacity-10 rounded border border-success mb-3">
                        <h6 class="fw-bold text-success mb-2"><i class="fa-solid fa-circle-check me-2"></i>Resolution Summary</h6>
                        <p class="mb-0 text-dark"><?= htmlspecialchars($ticket['resolution_summary']) ?></p>
                        <?php if ($ticket['resolved_at']): ?>
                            <small class="text-muted d-block mt-2">Resolved on <?= date('M d, Y h:i A', strtotime($ticket['resolved_at'])) ?></small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Activity Timeline Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <span class="fw-bold"><i class="fa-solid fa-timeline text-secondary me-2"></i>Status & Updates Log</span>
            </div>
            <div class="card-body">
                <?php if (empty($logs)): ?>
                    <p class="text-muted small text-center mb-0">No updates logged yet.</p>
                <?php else: ?>
                    <div class="timeline mt-2">
                        <?php foreach ($logs as $log): ?>
                            <div class="timeline-event">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small"><?= htmlspecialchars($log['actor_name']) ?> <span class="badge bg-light text-muted border"><?= htmlspecialchars($log['actor_role']) ?></span></span>
                                    <small class="text-muted"><?= date('M d, Y h:i A', strtotime($log['created_at'])) ?></small>
                                </div>
                                <div class="small text-secondary"><?= htmlspecialchars($log['notes']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Info -->
    <div class="col-lg-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <span class="fw-bold"><i class="fa-solid fa-headset text-primary me-2"></i>IT Help Desk Contact</span>
            </div>
            <div class="card-body">
                <p class="small text-muted">For immediate ground-operations emergency outages impacting active flight departure/arrival, dial the airport IT emergency hotwire directly.</p>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="fa-solid fa-phone text-danger"></i>
                    <span class="fw-bold text-dark">Airport IT Hotwire: Ext. 4499</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-tower-broadcast text-primary"></i>
                    <span class="text-muted small">VHF Channel 131.85 (Ops IT)</span>
                </div>
            </div>
        </div>
    </div>
</div>
