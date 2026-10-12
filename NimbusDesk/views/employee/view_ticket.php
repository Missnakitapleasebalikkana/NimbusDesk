<div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <a href="/NimbusDesk/employee/index.php" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
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
    <!-- Main Ticket Details & Timeline -->
    <div class="col-lg-8">
        <!-- Ticket Information Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="fw-bold mb-0" style="color: var(--nimbus-maroon);">
                    <?= htmlspecialchars($ticket['subject']) ?>
                </h5>
                <div>
                    <?php if ($ticket['status'] === 'Open'): ?>
                        <span class="badge ticket-status-open px-3 py-2 fs-6">Open (Pending IT)</span>
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
                    <h6 class="fw-semibold text-muted text-uppercase small mb-2">Description</h6>
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
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Attachment
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="fw-bold mb-0" style="color: var(--nimbus-maroon);">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>Incident Activity Timeline
                </h5>
            </div>
            <div class="card-body p-4">
                <?php if (empty($notes)): ?>
                    <p class="text-muted text-center py-3">No activity logs recorded yet.</p>
                <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($notes as $item): ?>
                            <?php if (!empty($item['is_internal'])) continue; // Safety guard ?>
                            <div class="d-flex mb-4">
                                <div class="flex-shrink-0 me-3">
                                    <img src="<?= \NimbusDesk\Core\View::avatarUrl($item['author_name'], $item['author_role'] === 'employee' ? 'f191b5' : '601a35', $item['author_role'] === 'employee' ? '601a35' : 'ffffff') ?>" width="40" height="40" class="rounded-circle shadow-sm" alt="Author">
                                </div>
                                <div class="flex-grow-1 p-3 bg-light rounded-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1 flex-wrap">
                                        <div class="fw-bold">
                                            <?= htmlspecialchars($item['author_name']) ?>
                                            <span class="badge bg-secondary ms-1 small text-capitalize"><?= htmlspecialchars($item['author_role']) ?></span>
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

                <?php if ($ticket['status'] !== 'Closed'): ?>
                    <hr class="my-4">
                    <form action="/NimbusDesk/employee/view_ticket.php?id=<?= $ticket['id'] ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= \NimbusDesk\Core\Auth::csrfToken() ?>">
                        <label for="reply_note" class="form-label fw-semibold">Add Follow-up Message / Additional Details</label>
                        <div class="mb-3">
                            <textarea class="form-control" id="reply_note" name="reply_note" rows="3" placeholder="Provide more context, answer technician queries, or report current status..." required></textarea>
                        </div>
                        <div class="row align-items-center mb-3">
                            <div class="col-md-7">
                                <label for="reply_attachment" class="form-label small text-muted mb-1">Attach Supporting Screenshot / Log (Optional)</label>
                                <input type="file" class="form-control form-control-sm" id="reply_attachment" name="attachment" accept=".png,.jpg,.jpeg,.gif,.webp,.pdf,.txt,.log,.docx">
                            </div>
                            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                                <button type="submit" class="btn btn-primary px-3">
                                    <i class="fa-solid fa-comment-dots me-1"></i> Post Update
                                </button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-secondary text-center mb-0 mt-3">
                        This incident ticket is marked as Closed. Further replies are disabled.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Ticket Sidebar Metadata -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-circle-info me-2"></i>Incident Details</h6>
            </div>
            <div class="card-body p-3">
                <div class="mb-2 pb-2 border-bottom">
                    <small class="text-muted d-block">Airport Terminal / Zone</small>
                    <span class="fw-bold text-dark"><i class="fa-solid fa-building me-1 text-muted"></i><?= htmlspecialchars($ticket['terminal'] ?? 'Terminal 1') ?></span>
                </div>
                <div class="mb-2 pb-2 border-bottom">
                    <small class="text-muted d-block">Specific Location</small>
                    <span class="text-dark"><i class="fa-solid fa-location-dot me-1 text-danger"></i><?= htmlspecialchars($ticket['location'] ?? 'Not specified') ?></span>
                </div>
                <div class="mb-2 pb-2 border-bottom">
                    <small class="text-muted d-block">Assigned IT Technician</small>
                    <?php if (!empty($ticket['technician_name'])): ?>
                        <span class="fw-bold text-success"><i class="fa-solid fa-user-check me-1"></i><?= htmlspecialchars($ticket['technician_name']) ?></span>
                    <?php else: ?>
                        <span class="text-warning fst-italic"><i class="fa-solid fa-hourglass-half me-1"></i>Awaiting Dispatch</span>
                    <?php endif; ?>
                </div>
                <div class="mb-2 pb-2 border-bottom">
                    <small class="text-muted d-block">Reported Date & Time</small>
                    <span class="small"><?= date('M d, Y h:i A', strtotime($ticket['created_at'])) ?></span>
                </div>
                <div class="mb-2 pb-2 border-bottom">
                    <small class="text-muted d-block">Last Updated</small>
                    <span class="small"><?= date('M d, Y h:i A', strtotime($ticket['updated_at'])) ?></span>
                </div>
                <?php if (!empty($ticket['resolved_at'])): ?>
                    <div>
                        <small class="text-muted d-block">Resolution Timestamp</small>
                        <span class="text-success fw-semibold small"><?= date('M d, Y h:i A', strtotime($ticket['resolved_at'])) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
