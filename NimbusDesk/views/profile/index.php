<div class="row">
    <!-- Profile Info Card -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 text-center p-4">
            <div class="mb-3">
                <img src="<?= \NimbusDesk\Core\View::avatarUrl($currentUser['name'], 'f191b5', '601a35') ?>" alt="Avatar" width="100" height="100" class="rounded-circle shadow-sm">
            </div>
            <h5 class="fw-bold mb-1" style="color: var(--nimbus-maroon);"><?= htmlspecialchars($currentUser['name']) ?></h5>
            <p class="text-muted small mb-2"><?= htmlspecialchars($currentUser['email']) ?></p>
            <div>
                <span class="badge bg-warning text-dark text-uppercase px-3 py-2"><?= htmlspecialchars($currentUser['role']) ?></span>
                <span class="badge bg-success ms-1 px-3 py-2 text-capitalize"><?= htmlspecialchars($currentUser['status'] ?? 'active') ?></span>
            </div>
            <hr class="my-3">
            <div class="text-start small">
                <div class="mb-2">
                    <strong class="text-muted d-block">Department</strong>
                    <span><?= htmlspecialchars($currentUser['department'] ?? 'Aviation Staff') ?></span>
                </div>
                <div class="mb-2">
                    <strong class="text-muted d-block">Contact Phone</strong>
                    <span><?= htmlspecialchars($currentUser['phone'] ?? 'None registered') ?></span>
                </div>
                <div>
                    <strong class="text-muted d-block">Member Since</strong>
                    <span><?= date('M d, Y', strtotime($currentUser['created_at'])) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile & Change Password Tabs / Cards -->
    <div class="col-lg-8">
        <!-- Edit Profile Form -->
        <div class="card shadow-sm border-0 mb-4 p-4">
            <h5 class="fw-bold mb-3" style="color: var(--nimbus-maroon);">
                <i class="fa-solid fa-user-pen me-2"></i>Edit Profile Details
            </h5>

            <?php if (!empty($errors['name'])): ?>
                <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($errors['name']) ?></div>
            <?php endif; ?>

            <form action="/NimbusDesk/profile.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= \NimbusDesk\Core\Auth::csrfToken() ?>">
                <input type="hidden" name="action" value="update_profile">
                
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold small">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($currentUser['name']) ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email_disabled" class="form-label fw-semibold small">Work Email (Managed by Admin)</label>
                        <input type="email" class="form-control bg-light" id="email_disabled" value="<?= htmlspecialchars($currentUser['email']) ?>" readonly>
                        <div class="form-text small">Email cannot be changed directly for airport security audits.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label fw-semibold small">Contact Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>" placeholder="+63 (02) 8876-0000">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="department" class="form-label fw-semibold small">Department / Division</label>
                    <input type="text" class="form-control" id="department" name="department" value="<?= htmlspecialchars($currentUser['department'] ?? '') ?>" required>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="card shadow-sm border-0 mb-4 p-4">
            <h5 class="fw-bold mb-3" style="color: var(--nimbus-maroon);">
                <i class="fa-solid fa-key me-2"></i>Change Security Password
            </h5>

            <?php if (!empty($errors['current_password'])): ?>
                <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($errors['current_password']) ?></div>
            <?php endif; ?>
            <?php if (!empty($errors['new_password'])): ?>
                <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($errors['new_password']) ?></div>
            <?php endif; ?>
            <?php if (!empty($errors['confirm_password'])): ?>
                <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($errors['confirm_password']) ?></div>
            <?php endif; ?>

            <form action="/NimbusDesk/profile.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= \NimbusDesk\Core\Auth::csrfToken() ?>">
                <input type="hidden" name="action" value="change_password">

                <div class="mb-3">
                    <label for="current_password" class="form-label fw-semibold small">Current Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter current password" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="new_password" class="form-label fw-semibold small">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="At least 6 characters" required minlength="6">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="confirm_password" class="form-label fw-semibold small">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Re-type new password" required minlength="6">
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-outline-danger px-4">
                        <i class="fa-solid fa-lock me-1"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
