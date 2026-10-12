<?php if (!empty($errors)): ?>
    <div class="alert alert-danger mb-4">
        <ul class="mb-0 ps-3">
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Pending Password Reset Alerts -->
<?php if (!empty($pendingResets)): ?>
    <div class="card border-warning mb-4 shadow-sm bg-warning bg-opacity-10">
        <div class="card-header bg-warning bg-opacity-25 py-2 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-bell me-2 text-danger"></i>Pending Password Reset Requests (<?= count($pendingResets) ?>)
            </h6>
            <span class="badge bg-danger">Requires Admin Action</span>
        </div>
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Requester</th>
                            <th>Role & Department</th>
                            <th>Requested Time</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingResets as $pr): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($pr['name']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($pr['email']) ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary text-capitalize"><?= htmlspecialchars($pr['role']) ?></span>
                                    <small class="text-muted ms-1"><?= htmlspecialchars($pr['department'] ?? '') ?></small>
                                </td>
                                <td class="small text-muted">
                                    <?= date('M d, Y h:i A', strtotime($pr['reset_requested_at'])) ?>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#resetModal<?= $pr['id'] ?>">
                                        <i class="fa-solid fa-key me-1"></i> Issue New Password
                                    </button>
                                    <form action="/NimbusDesk/admin/users.php" method="POST" class="d-inline ms-1">
                                        <input type="hidden" name="csrf_token" value="<?= \NimbusDesk\Core\Auth::csrfToken() ?>">
                                        <input type="hidden" name="action" value="clear_reset">
                                        <input type="hidden" name="user_id" value="<?= $pr['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Dismiss without resetting">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0" style="color: var(--nimbus-maroon);">
        <i class="fa-solid fa-users me-2"></i>Airport Staff & System Users
    </h5>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
        <i class="fa-solid fa-user-plus me-1"></i> Provision New Account
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Contact Phone</th>
                    <th>Account Status</th>
                    <th>Created</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td class="ps-3 fw-bold">
                            <?= htmlspecialchars($u['name']) ?>
                            <?php if ((int)$u['id'] === (int)$currentUser['id']): ?>
                                <span class="badge bg-success ms-1 small">You</span>
                            <?php endif; ?>
                            <?php if (!empty($u['reset_requested_at'])): ?>
                                <span class="badge bg-danger ms-1 small" title="Password reset requested"><i class="fa-solid fa-key"></i> Reset Pending</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td>
                            <?php if ($u['role'] === 'admin'): ?>
                                <span class="badge bg-dark">Administrator</span>
                            <?php elseif ($u['role'] === 'technician'): ?>
                                <span class="badge bg-info text-dark">Technician</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Employee</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($u['department'] ?? 'Aviation Staff') ?></td>
                        <td><small class="text-muted"><?= htmlspecialchars($u['phone'] ?? '—') ?></small></td>
                        <td>
                            <?php if (($u['status'] ?? 'active') === 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted"><?= date('M d, Y', strtotime($u['created_at'])) ?></td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#resetModal<?= $u['id'] ?>" title="Reset Password">
                                    <i class="fa-solid fa-key"></i>
                                </button>

                                <?php if ((int)$u['id'] !== (int)$currentUser['id']): ?>
                                    <!-- Status Toggle Form -->
                                    <form action="/NimbusDesk/admin/users.php" method="POST" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= \NimbusDesk\Core\Auth::csrfToken() ?>">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <input type="hidden" name="status" value="<?= ($u['status'] ?? 'active') === 'active' ? 'inactive' : 'active' ?>">
                                        <button type="submit" class="btn btn-outline-warning" title="<?= ($u['status'] ?? 'active') === 'active' ? 'Deactivate account' : 'Activate account' ?>">
                                            <i class="fa-solid <?= ($u['status'] ?? 'active') === 'active' ? 'fa-user-slash' : 'fa-user-check' ?>"></i>
                                        </button>
                                    </form>

                                    <!-- Safe Delete Button (Soft-deactivates if tickets exist) -->
                                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $u['id'] ?>" title="Remove User">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Password Reset & Delete Modals for User Accounts -->
<?php foreach ($users as $u): ?>
    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetModal<?= $u['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="/NimbusDesk/admin/users.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= \NimbusDesk\Core\Auth::csrfToken() ?>">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Reset Password for <?= htmlspecialchars($u['name']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted">
                            Issue a temporary or new password for <strong><?= htmlspecialchars($u['email']) ?></strong>.
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">New Password</label>
                            <input type="password" class="form-control" name="new_password" placeholder="Minimum 6 characters" required minlength="6">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if ((int)$u['id'] !== (int)$currentUser['id']): ?>
        <!-- Delete User Confirmation Modal -->
        <div class="modal fade" id="deleteModal<?= $u['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="/NimbusDesk/admin/users.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= \NimbusDesk\Core\Auth::csrfToken() ?>">
                        <input type="hidden" name="action" value="delete_user">
                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold text-danger">Remove User Account</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to remove <strong><?= htmlspecialchars($u['name']) ?></strong> (<?= htmlspecialchars($u['email']) ?>)?</p>
                            <div class="alert alert-info small mb-0">
                                <i class="fa-solid fa-shield-halved me-1"></i> <strong>Audit Compliance Safeguard:</strong> Any tickets previously submitted or resolved by this user will be preserved for aviation regulatory records.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger btn-sm">Confirm Remove / Deactivate</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/NimbusDesk/admin/users.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= \NimbusDesk\Core\Auth::csrfToken() ?>">
                <input type="hidden" name="action" value="create_user">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" style="color: var(--nimbus-maroon);">
                        <i class="fa-solid fa-user-plus me-2"></i>Provision Airport Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. Captain Juan Dela Cruz" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Work Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="email" placeholder="name@airline.com" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <label class="form-label fw-semibold small">System Role <span class="text-danger">*</span></label>
                            <select class="form-select" name="role" required>
                                <option value="employee" selected>Aviation Employee</option>
                                <option value="technician">IT Help Desk Technician</option>
                                <option value="admin">System Administrator</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Initial Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" placeholder="Min. 6 chars" required minlength="6">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Department / Division</label>
                        <input type="text" class="form-control" name="department" placeholder="e.g. Ground Operations, Baggage Handling, Flight Crew">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Contact Number</label>
                        <input type="tel" class="form-control" name="phone" placeholder="+63 (02) 8876-0000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm">Provision Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
