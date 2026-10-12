<div class="card p-4 p-md-5 login-card my-4" style="width: 100%; max-width: 520px;">
    <div class="text-center mb-4">
        <img src="/NimbusDesk/assets/images/icon.png" alt="NimbusDesk Logo" width="75" height="75" class="rounded-circle shadow-sm mb-2 border border-2 border-white">
        <h3 class="fw-bold mt-2 mb-0" style="color: var(--nimbus-maroon); letter-spacing: 1px;">Create NimbusDesk Account</h3>
        <p class="text-muted small mt-1">Aviation Staff & IT Support Portal Registration</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger py-2 mb-3">
            <ul class="mb-0 ps-3 small">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/NimbusDesk/register.php" method="POST" autocomplete="on">
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold small text-dark">Full Name <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fa-solid fa-user text-muted"></i></span>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" placeholder="e.g. Capt. Jwayne Chester" required autofocus>
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold small text-dark">Work / Airline Email <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fa-solid fa-envelope text-muted"></i></span>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="name@airline.com" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="role" class="form-label fw-semibold small text-dark">Account Role <span class="text-danger">*</span></label>
                <select class="form-select" id="role" name="role" required>
                    <option value="employee" <?= ($old['role'] ?? 'employee') === 'employee' ? 'selected' : '' ?>>Aviation Employee (Ticket Creator)</option>
                    <option value="technician" <?= ($old['role'] ?? '') === 'technician' ? 'selected' : '' ?>>IT Technician (Support Staff)</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="department" class="form-label fw-semibold small text-dark">Department / Division <span class="text-danger">*</span></label>
                <select class="form-select" id="department" name="department" required>
                    <option value="Flight Operations" <?= ($old['department'] ?? '') === 'Flight Operations' ? 'selected' : '' ?>>Flight Operations</option>
                    <option value="Gate & Ground Services" <?= ($old['department'] ?? '') === 'Gate & Ground Services' ? 'selected' : '' ?>>Gate & Ground Services</option>
                    <option value="Ticketing & Check-in" <?= ($old['department'] ?? '') === 'Ticketing & Check-in' ? 'selected' : '' ?>>Ticketing & Check-in</option>
                    <option value="Baggage Handling" <?= ($old['department'] ?? '') === 'Baggage Handling' ? 'selected' : '' ?>>Baggage Handling</option>
                    <option value="IT Support & Network" <?= ($old['department'] ?? '') === 'IT Support & Network' ? 'selected' : '' ?>>IT Support & Network</option>
                    <option value="Airport Facilities" <?= ($old['department'] ?? '') === 'Airport Facilities' ? 'selected' : '' ?>>Airport Facilities</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label fw-semibold small text-dark">Contact Number (Optional)</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="fa-solid fa-phone text-muted"></i></span>
                <input type="tel" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" placeholder="+63 (02) 8876-0000">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="password" class="form-label fw-semibold small text-dark">Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="At least 6 chars" required minlength="6">
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="password_confirm" class="form-label fw-semibold small text-dark">Confirm Password <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="fa-solid fa-check-double text-muted"></i></span>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Re-type password" required minlength="6">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fs-6 shadow-sm mt-2">
            <i class="fa-solid fa-user-plus me-2"></i>Create Account
        </button>

        <div class="text-center mt-3 pt-2 border-top">
            <span class="small text-muted">Already have an account?</span>
            <a href="/NimbusDesk/index.php" class="small fw-bold ms-1" style="color: var(--nimbus-maroon);">Sign In here</a>
        </div>
    </form>
</div>
