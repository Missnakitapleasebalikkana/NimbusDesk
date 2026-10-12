<?php
$backUrl = (($currentUser['role'] ?? '') === 'technician') ? '/NimbusDesk/technician/index.php' : '/NimbusDesk/admin/index.php';
?>
<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="<?= $backUrl ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Incident Queue
    </a>
    <div>
        <span class="badge bg-secondary me-1"><?= htmlspecialchars($ticket['ticket_code']) ?></span>
        <span class="badge 
            <?= $ticket['urgency'] === 'Critical' ? 'bg-danger' : ($ticket['urgency'] === 'High' ? 'bg-warning text-dark' : 'bg-info text-dark') ?>">
            <?= htmlspecialchars($ticket['urgency']) ?> Urgency
        </span>
        <span class="badge bg-light text-dark border ms-1"><?= htmlspecialchars($ticket['category']) ?></span>
    </div>
</div>

<div class="row">
    <!-- Main Incident Details & Timeline -->
    <div class="col-lg-8">
        <!-- Incident Information Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="fw-bold mb-0" style="color: var(--nimbus-maroon);">
                    <?= htmlspecialchars($ticket['subject']) ?>
                </h5>
                <div>
                    <?php if ($ticket['status'] === 'Open'): ?>
                        <span class="badge ticket-status-open px-3 py-2 fs-6">Open</span>
                    <?php elseif ($ticket['status'] === 'In Progress'): ?>
                        <span class="badge ticket-status-progress px-3 py-2 fs-6">In Progress</span>
                    <?php elseif ($ticket['status'] === 'Resolved'): ?>
                        <span class="badge ticket-status-resolved px-3 py-2 fs-6">Resolved</span>
                    <?php else: ?>
                        <span class="badge bg-dark px-3 py-2 fs-6"><?= htmlspecialchars($ticket['status']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-body p-4">
                <div class="p-3 bg-light rounded-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap">
                        <h6 class="fw-semibold text-muted text-uppercase small mb-0">Detailed Problem Description</h6>
                        <span class="small badge bg-white text-dark border">
                            <i class="fa-solid fa-location-dot me-1 text-danger"></i><?= htmlspecialchars($ticket['terminal'] ?? 'Terminal 1') ?> • <?= htmlspecialchars($ticket['location'] ?? 'Not specified') ?>
                        </span>
                    </div>
                    <p class="mb-0 text-dark" style="white-space: pre-line;"><?= htmlspecialchars($ticket['description']) ?></p>
                </div>

                <?php if (!empty($ticket['attachment'])): ?>
                    <div class="p-3 border rounded-3 bg-white d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fa-solid fa-paperclip text-muted fs-4 me-3"></i>
                            <div>
                                <div class="fw-semibold">Attached File / Evidence</div>
                                <small class="text-muted"><?= htmlspecialchars(basename($ticket['attachment'])) ?></small>
                            </div>
                        </div>
                        <a href="/NimbusDesk/<?= htmlspecialchars($ticket['attachment']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Attachment
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Activity & Resolution Log -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0" style="color: var(--nimbus-maroon);">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>Incident Activity & Audit Timeline
                </h5>
            </div>
            <div class="card-body p-4">
                <?php if (empty($notes)): ?>
                    <p class="text-muted text-center py-3">No activity logs recorded yet.</p>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($notes as $item): ?>
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0 me-3">
                                    <img src="<?= \NimbusDesk\Core\View::avatarUrl($item['author_name'], $item['author_role'] === 'employee' ? 'f191b5' : '601a35', $item['author_role'] === 'employee' ? '601a35' : 'ffffff') ?>" width="40" height="40" class="rounded-circle shadow-sm" alt="Author">
                                </div>
                                <div class="flex-grow-1 p-3 <?= !empty($item['is_internal']) ? 'border border-warning bg-warning bg-opacity-10' : 'bg-light' ?> rounded-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap">
                                        <div class="fw-bold">
                                            <?= htmlspecialchars($item['author_name']) ?>
                                            <span class="badge bg-secondary ms-1 small text-capitalize"><?= htmlspecialchars($item['author_role']) ?></span>
                                            <?php if (!empty($item['is_internal'])): ?>
                                                <span class="badge bg-warning text-dark ms-1"><i class="fa-solid fa-lock me-1"></i>Internal IT Note</span>
                                            <?php endif; ?>
                                            <?php if (!empty($item['status_change'])): ?>
                                                <span class="badge bg-info text-dark ms-1"><?= htmlspecialchars($item['status_change']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted"><?= date('M d, Y h:i A', strtotime($item['created_at'])) ?></small>
                                    </div>
                                    <p class="mb-0 text-secondary" style="white-space: pre-line;"><?= htmlspecialchars($item['note']) ?></p>
                                    <?php if (!empty($item['attachment'])): ?>
                                        <div class="mt-2">
                                            <a href="/NimbusDesk/<?= htmlspecialchars($item['attachment']) ?>" target="_blank" class="badge bg-white text-primary border text-decoration-none py-1 px-2">
                                                <i class="fa-solid fa-paperclip me-1"></i> <?= htmlspecialchars(basename($item['attachment'])) ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <hr class="my-4">

                <!-- Add Resolution / Troubleshooting Note -->
                <h6 class="fw-bold mb-3" style="color: var(--nimbus-maroon);">
                    <i class="fa-solid fa-pen-to-square me-2"></i>Add Troubleshooting / Resolution Note
                </h6>
                <form action="/NimbusDesk/admin/ticket.php?id=<?= $ticket['id'] ?>" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= \NimbusDesk\Core\Auth::csrfToken() ?>">
                    <input type="hidden" name="action" value="add_resolution_note">
                    <div class="mb-3">
                        <textarea class="form-control" name="note" rows="3" placeholder="Enter troubleshooting actions, diagnostics performed, or resolution steps..." required></textarea>
                    </div>

                    <div class="row align-items-center mb-3">
                        <div class="col-md-5 mb-2 mb-md-0">
                            <label class="form-label small fw-semibold text-muted mb-1">Update Status With Note:</label>
                            <select class="form-select form-select-sm" name="status_update">
                                <option value="" selected>Keep Current (<?= htmlspecialchars($ticket['status']) ?>)</option>
                                <option value="In Progress">Mark as In Progress</option>
                                <option value="Resolved">Mark as Resolved</option>
                                <option value="Closed">Mark as Closed</option>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label small fw-semibold text-muted mb-1">Attach Technical Evidence (Optional):</label>
                            <input type="file" class="form-control form-control-sm" name="note_attachment" accept=".png,.jpg,.jpeg,.gif,.webp,.pdf,.txt,.log,.docx">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_internal" value="1" id="internalCheck">
                            <label class="form-check-label small" for="internalCheck">
                                <i class="fa-solid fa-lock me-1 text-warning"></i> Internal IT note (strictly hidden from employee view)
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-plus me-1"></i> Post Note
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Sidebar: Management Controls & Reporter Info -->
    <div class="col-lg-4">
        <!-- Ticket Controls Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-sliders me-2"></i>Status & Assignment Control</h6>
            </div>
            <div class="card-body p-3">
                <form action="/NimbusDesk/admin/ticket.php?id=<?= $ticket['id'] ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= \NimbusDesk\Core\Auth::csrfToken() ?>">
                    <input type="hidden" name="action" value="update_assignment_status">
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Assigned Technician</label>
                        <select class="form-select" name="assigned_to">
                            <option value="">-- Unassigned --</option>
                            <?php foreach ($technicians as $tech): ?>
                                <option value="<?= $tech['id'] ?>" <?= ((int)$ticket['assigned_to'] === (int)$tech['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tech['name']) ?> (<?= htmlspecialchars($tech['department'] ?? 'IT') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Current Progress Status</label>
                        <select class="form-select" name="status">
                            <option value="Open" <?= $ticket['status'] === 'Open' ? 'selected' : '' ?>>Open</option>
                            <option value="In Progress" <?= $ticket['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="Resolved" <?= $ticket['status'] === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                            <option value="Closed" <?= $ticket['status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fw-semibold">
                        <i class="fa-solid fa-check me-1"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>

        <!-- Aviation Reporter Profile Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-id-card me-2"></i>Station & Reporter Information</h6>
            </div>
            <div class="card-body p-3">
                <div class="mb-2 pb-2 border-bottom">
                    <small class="text-muted d-block">Terminal & Station</small>
                    <span class="fw-bold text-dark"><i class="fa-solid fa-building me-1 text-muted"></i><?= htmlspecialchars($ticket['terminal'] ?? 'Terminal 1') ?></span>
                    <div class="small text-danger"><i class="fa-solid fa-location-dot me-1"></i><?= htmlspecialchars($ticket['location'] ?? 'Not specified') ?></div>
                </div>
                <div class="mb-2 pb-2 border-bottom">
                    <small class="text-muted d-block">Reporter Full Name</small>
                    <span class="fw-bold"><?= htmlspecialchars($ticket['requester_name']) ?></span>
                </div>
                <div class="mb-2 pb-2 border-bottom">
                    <small class="text-muted d-block">Department / Division</small>
                    <span><?= htmlspecialchars($ticket['requester_dept'] ?? 'Aviation Personnel') ?></span>
                </div>
                <div class="mb-2 pb-2 border-bottom">
                    <small class="text-muted d-block">Email</small>
                    <a href="mailto:<?= htmlspecialchars($ticket['requester_email']) ?>" class="text-decoration-none">
                        <?= htmlspecialchars($ticket['requester_email']) ?>
                    </a>
                </div>
                <?php if (!empty($ticket['requester_phone'])): ?>
                    <div class="mb-2 pb-2 border-bottom">
                        <small class="text-muted d-block">Contact Phone</small>
                        <span><?= htmlspecialchars($ticket['requester_phone']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="mb-2 pb-2 border-bottom">
                    <small class="text-muted d-block">Reported Timestamp</small>
                    <span class="small"><?= date('M d, Y h:i A', strtotime($ticket['created_at'])) ?></span>
                </div>
                <?php if (!empty($ticket['resolved_at'])): ?>
                    <div>
                        <small class="text-muted d-block">Resolved Timestamp</small>
                        <span class="text-success fw-semibold small"><?= date('M d, Y h:i A', strtotime($ticket['resolved_at'])) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
