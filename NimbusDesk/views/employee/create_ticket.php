<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-plane-arrival me-2 fs-5" style="color: var(--nimbus-maroon);"></i>
                    <h5 class="fw-bold mb-0" style="color: var(--nimbus-maroon);">Aviation Incident Report Form</h5>
                </div>
                <small class="text-muted">Submit hardware, software, or network issues encountered in airline operations.</small>
            </div>
            
            <div class="card-body p-4">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mb-4">
                        <strong class="d-block mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> Please resolve the following errors:</strong>
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $field => $msg): ?>
                                <li><?= htmlspecialchars($msg) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="/NimbusDesk/employee/create_ticket.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= \NimbusDesk\Core\Auth::csrfToken() ?>">

                    <!-- Airport Location & Terminal -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="terminal" class="form-label fw-semibold">Airport Terminal / Zone <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-building text-muted"></i></span>
                                <select class="form-select <?= isset($errors['terminal']) ? 'is-invalid' : '' ?>" id="terminal" name="terminal" required>
                                    <option value="Terminal 1" <?= ($old['terminal'] ?? '') === 'Terminal 1' ? 'selected' : '' ?>>Terminal 1 (International)</option>
                                    <option value="Terminal 2" <?= ($old['terminal'] ?? '') === 'Terminal 2' ? 'selected' : '' ?>>Terminal 2 (Domestic & Regional)</option>
                                    <option value="Terminal 3" <?= ($old['terminal'] ?? '') === 'Terminal 3' ? 'selected' : '' ?>>Terminal 3 (Long-Haul & Transit)</option>
                                    <option value="Domestic Cargo" <?= ($old['terminal'] ?? '') === 'Domestic Cargo' ? 'selected' : '' ?>>Domestic Cargo Complex</option>
                                    <option value="General Aviation / Apron" <?= ($old['terminal'] ?? '') === 'General Aviation / Apron' ? 'selected' : '' ?>>General Aviation / Apron Ramp</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="location" class="form-label fw-semibold">Specific Location / Station <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="fa-solid fa-location-dot text-muted"></i></span>
                                <input type="text" class="form-control <?= isset($errors['location']) ? 'is-invalid' : '' ?>" id="location" name="location" value="<?= htmlspecialchars($old['location'] ?? '') ?>" placeholder="e.g. Gate 4, Counter 12, Carousel 3" required>
                            </div>
                            <div class="form-text small">Exact gate number, check-in counter, bay, or office room.</div>
                        </div>
                    </div>

                    <!-- Category & Urgency -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="category" class="form-label fw-semibold">Incident Category <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['category']) ? 'is-invalid' : '' ?>" id="category" name="category" required>
                                <option value="" disabled <?= empty($old['category']) ? 'selected' : '' ?>>-- Select Incident Type --</option>
                                <option value="Hardware" <?= ($old['category'] ?? '') === 'Hardware' ? 'selected' : '' ?>>Hardware (Scanners, Desktops, BHS Displays, Radios)</option>
                                <option value="Software" <?= ($old['category'] ?? '') === 'Software' ? 'selected' : '' ?>>Software (DCS, Radar Feeds, EFB, Boarding Spooler)</option>
                                <option value="Network" <?= ($old['category'] ?? '') === 'Network' ? 'selected' : '' ?>>Network (LAN Drop, Apron Wi-Fi, Telemetry Drop)</option>
                                <option value="Facilities" <?= ($old['category'] ?? '') === 'Facilities' ? 'selected' : '' ?>>Facilities (Power Outlets, Comm Rack AC, Turnstiles)</option>
                                <option value="Other" <?= ($old['category'] ?? '') === 'Other' ? 'selected' : '' ?>>Other Operational Equipment</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="urgency" class="form-label fw-semibold">Operational Urgency <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['urgency']) ? 'is-invalid' : '' ?>" id="urgency" name="urgency" required>
                                <option value="Low" <?= ($old['urgency'] ?? '') === 'Low' ? 'selected' : '' ?>>Low - Routine inquiry or scheduled maintenance</option>
                                <option value="Medium" <?= ($old['urgency'] ?? '') === 'Medium' ? 'selected' : '' ?>>Medium - Minor operational glitch, workaround available</option>
                                <option value="High" <?= ($old['urgency'] ?? '') === 'High' ? 'selected' : '' ?>>High - Impairs boarding, check-in, or flight dispatch</option>
                                <option value="Critical" <?= ($old['urgency'] ?? '') === 'Critical' ? 'selected' : '' ?>>Critical - Ground stop, total gate outage, immediate safety risk</option>
                            </select>
                        </div>
                    </div>

                    <!-- Incident Subject -->
                    <div class="mb-3">
                        <label for="subject" class="form-label fw-semibold">Subject / Brief Summary <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['subject']) ? 'is-invalid' : '' ?>" id="subject" name="subject" value="<?= htmlspecialchars($old['subject'] ?? '') ?>" placeholder="e.g. Optical scanner fails to read boarding QR codes at Gate 4" required>
                    </div>

                    <!-- Detailed Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold">Detailed Problem Description <span class="text-danger">*</span></label>
                        <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" id="description" name="description" rows="5" placeholder="Include error codes, equipment serial numbers, flight numbers affected, and steps already taken..." required><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
                    </div>

                    <!-- Attachment -->
                    <div class="mb-4">
                        <label for="attachment" class="form-label fw-semibold">Supporting File or Screenshot (Optional)</label>
                        <input class="form-control <?= isset($errors['attachment']) ? 'is-invalid' : '' ?>" type="file" id="attachment" name="attachment" accept=".png,.jpg,.jpeg,.gif,.webp,.pdf,.txt,.log,.docx">
                        <div class="form-text small">Max size: 8MB. Accepted formats: PNG, JPG, PDF, TXT, LOG.</div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="/NimbusDesk/employee/index.php" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 py-2">
                            <i class="fa-solid fa-paper-plane me-2"></i> Submit Incident Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
