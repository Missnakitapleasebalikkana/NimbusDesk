<?php
// Provided variables: $categories
?>

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Submit Incident Report</h3>
                <p class="text-muted small mb-0">Describe the equipment or software issue for prompt IT dispatch.</p>
            </div>
            <a href="<?= url('employee/index.php') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <div class="fw-bold text-dark"><i class="fa-solid fa-file-circle-plus text-primary me-2"></i>New IT Support Ticket Form</div>
            </div>
            <div class="card-body p-4">
                <form id="createTicketForm" enctype="multipart/form-data">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="subject" class="form-label fw-bold small">Incident Subject / Title <span class="text-danger">*</span></label>
                            <input type="text" name="subject" id="subject" class="form-control" placeholder="e.g., Gate 4 boarding pass scanner barcode lens unresponsive" required>
                            <div class="form-text small">Brief 1-line summary of what is broken or malfunctioning.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="urgency" class="form-label fw-bold small">Urgency Level <span class="text-danger">*</span></label>
                            <select name="urgency" id="urgency" class="form-select" required>
                                <option value="Low">Low (Workaround available)</option>
                                <option value="Medium" selected>Medium (Impairs standard duties)</option>
                                <option value="High">High (Direct flight delay risk)</option>
                                <option value="Critical">Critical (Operations halted / Safety)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label fw-bold small">Failure Category <span class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-select" required>
                                <option value="" disabled selected>Select category...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="location" class="form-label fw-bold small">Airport Location / Gate / Counter <span class="text-danger">*</span></label>
                            <input type="text" name="location" id="location" class="form-control" placeholder="e.g., Terminal 2 - Gate 4B, Ramp Ops Bay 12" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold small">Detailed Description <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" rows="5" class="form-control" placeholder="Provide complete troubleshooting notes: error codes displayed, symptoms, affected flight number, and device serial if visible..." required></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="attachment" class="form-label fw-bold small">Attachment / Screenshot (Optional)</label>
                        <input type="file" name="attachment" id="attachment" class="form-control" accept="image/*,.pdf,.log">
                        <div class="form-text small">Upload photo of device screen error or error log (Max 5MB).</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="<?= url('employee/index.php') ?>" class="btn btn-light border px-4">Cancel</a>
                        <button type="submit" class="btn btn-nimbus px-4">
                            <i class="fa-solid fa-paper-plane me-2"></i> Submit Technical Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
